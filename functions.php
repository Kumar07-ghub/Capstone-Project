<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

require_once(__DIR__ . '/pdfg.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once(__DIR__ . '/PHPMailer-master/src/PHPMailer.php');
require_once(__DIR__ . '/PHPMailer-master/src/SMTP.php');
require_once(__DIR__ . '/PHPMailer-master/src/Exception.php');

function sendInvoice($order_id, $conn) {
    // Fetch order
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->bind_param("i",$order_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    if (!$order) return;

    $customer_name = trim($order['first_name'].' '.$order['last_name']);

    // Fetch items
    $stmt = $conn->prepare(
      "SELECT p.name, oi.quantity, oi.price
       FROM order_items oi
       JOIN products p ON oi.product_id = p.id
       WHERE oi.order_id = ?");
    $stmt->bind_param("i",$order_id);
    $stmt->execute();
    $items = $stmt->get_result();

    // Build PDF
    $pdf = new PDF('P','mm','A4');
    $pdf->AliasNbPages();
    $pdf->AddPage();

    // Customer details
    $pdf->SetFont('Arial','',12);
    $pdf->Cell(0,6,"Invoice #: {$order_id}",0,1,'L');
    $pdf->Cell(0,6,"Date: ".date('Y-m-d'),0,1,'L');
    $pdf->Cell(0,6,"Bill To: {$customer_name} ({$order['email']})",0,1,'L');
    $pdf->Ln(5);

    // Table header
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(80,8,'Product',1,0,'L');
    $pdf->Cell(25,8,'Qty',1,0,'C');
    $pdf->Cell(35,8,'Unit Price',1,0,'R');
    $pdf->Cell(35,8,'Subtotal',1,1,'R');

    // Table rows
    $pdf->SetFont('Arial','',11);
    $fill = false; $total = 0;
    while ($item = $items->fetch_assoc()) {
        $sub = $item['quantity'] * $item['price'];
        $total += $sub;
        $pdf->SetFillColor($fill ? 240 : 255);
        $pdf->Cell(80,6,$item['name'],1,0,'L',$fill);
        $pdf->Cell(25,6,$item['quantity'],1,0,'C',$fill);
        $pdf->Cell(35,6,'$'.number_format($item['price'],2),1,0,'R',$fill);
        $pdf->Cell(35,6,'$'.number_format($sub,2),1,1,'R',$fill);
        $fill = !$fill;
    }

    // Totals
    $tax = $total * 0.13;
    $grand = $total + $tax;
    $pdf->Ln(4);
    $pdf->Cell(140,6,'Subtotal',0,0,'R');
    $pdf->Cell(35,6,'$'.number_format($total,2),0,1,'R');
    $pdf->Cell(140,6,'Tax (13%)',0,0,'R');
    $pdf->Cell(35,6,'$'.number_format($tax,2),0,1,'R');
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(140,8,'Grand Total',0,0,'R');
    $pdf->Cell(35,8,'$'.number_format($grand,2),0,1,'R');
    $pdf->Ln(5);

    // Optional text
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(0,6,'Amount in words: '.number_to_words($grand).' only.',0,1,'L');

    // Save PDF
    $dir = __DIR__ . '/invoices';
    if (!is_dir($dir)) mkdir($dir,0777,true);
    $filename = "$dir/invoice_{$order_id}.pdf";
    $pdf->Output('F',$filename);

    // Email
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.sendgrid.net';
        $mail->SMTPAuth = true;
        $mail->Username = 'apikey';
        $mail->Password = 'YOUR_SENDGRID_API_KEY';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->setFrom('info@yourcompany.com','Your Store');
        $mail->addAddress($order['email'],$customer_name);
        $mail->isHTML(true);
        $mail->Subject = "Invoice for Order #{$order_id}";
        $mail->Body    = "<p>Hi <strong>{$customer_name}</strong>,</p>
            <p>Thank you for your order (#{$order_id}). Please find the invoice attached.</p>
            <p>Regards,<br>Your Store Team</p>";
        $mail->addAttachment($filename);
        $mail->send();
    } catch (Exception $e) {
        error_log("Email failed: ".$mail->ErrorInfo);
    }
}

// Simple number-to-words helper (English, whole dollars)
function number_to_words($num) {
    $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
    return ucfirst($f->format(round($num)));
}
