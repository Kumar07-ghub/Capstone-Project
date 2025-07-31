<?php

ini_set('display_errors', 1);

ini_set('display_startup_errors', 1);

error_reporting(E_ALL);



// FPDF - PDF Invoice Generator

require_once(__DIR__ . '/pdfg.php');



// PHPMailer for sending emails

use PHPMailer\PHPMailer\PHPMailer;

use PHPMailer\PHPMailer\Exception;



require_once(__DIR__ . '/PHPMailer-master/src/PHPMailer.php');

require_once(__DIR__ . '/PHPMailer-master/src/SMTP.php');

require_once(__DIR__ . '/PHPMailer-master/src/Exception.php');



function sendInvoice($order_id, $conn) {

    // 1. Fetch order details

    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");

    $stmt->bind_param("i", $order_id);

    $stmt->execute();

    $order = $stmt->get_result()->fetch_assoc();

    if (!$order) return;



    // Build full customer name

    $customer_name = trim($order['first_name'] . ' ' . $order['last_name']);



    // 2. Fetch order items

    $stmt = $conn->prepare("SELECT p.name, oi.quantity, oi.price 

                            FROM order_items oi 

                            JOIN products p ON oi.product_id = p.id 

                            WHERE oi.order_id = ?");

    $stmt->bind_param("i", $order_id);

    $stmt->execute();

    $items_result = $stmt->get_result();



    // 3. Generate PDF invoice

    $pdf = new PDF(); // Your extended class from pdfg.php

    $pdf->AddPage();

    $pdf->SetFont('Arial','B',14);

    $pdf->Cell(0,10,"Invoice - Order #{$order['id']}",0,1,'C');

    $pdf->SetFont('Arial','',12);

    $pdf->Cell(0,10,"Customer: {$customer_name}",0,1);

    $pdf->Cell(0,10,"Email: {$order['email']}",0,1);

    $pdf->Cell(0,10,"Date: " . date('Y-m-d'), 0,1);

    $pdf->Ln(10);



    // Table headers

    $pdf->SetFont('Arial','B',12);

    $pdf->Cell(80,10,"Product",1);

    $pdf->Cell(30,10,"Qty",1);

    $pdf->Cell(40,10,"Price",1);

    $pdf->Cell(40,10,"Subtotal",1);

    $pdf->Ln();



    // Table data

    $pdf->SetFont('Arial','',12);

    $total = 0;

    while ($item = $items_result->fetch_assoc()) {

        $subtotal = $item['quantity'] * $item['price'];

        $total += $subtotal;

        $pdf->Cell(80,10,$item['name'],1);

        $pdf->Cell(30,10,$item['quantity'],1);

        $pdf->Cell(40,10,"$" . number_format($item['price'], 2),1);

        $pdf->Cell(40,10,"$" . number_format($subtotal, 2),1);

        $pdf->Ln();

    }



    // Totals

    $tax = $total * 0.13;

    $grand_total = $total + $tax;



    $pdf->Ln(5);

    $pdf->Cell(150,10,"Subtotal:",0,0,'R');

    $pdf->Cell(40,10,"$" . number_format($total, 2),0,1,'R');



    $pdf->Cell(150,10,"Tax (13%):",0,0,'R');

    $pdf->Cell(40,10,"$" . number_format($tax, 2),0,1,'R');



    $pdf->SetFont('Arial','B',12);

    $pdf->Cell(150,10,"Grand Total:",0,0,'R');

    $pdf->Cell(40,10,"$" . number_format($grand_total, 2),0,1,'R');



    // 4. Save PDF to file

    $invoices_dir = __DIR__ . '/invoices';

    if (!is_dir($invoices_dir)) {

        mkdir($invoices_dir, 0777, true);

    }



    $filename = "$invoices_dir/invoice_{$order_id}.pdf";

    $pdf->Output('F', $filename); // Save to file



    try {

        $mail = new PHPMailer(true);

        $mail->isSMTP();

        $mail->Host = 'smtp.sendgrid.net';

        $mail->SMTPAuth = true;

        $mail->Username = 'apikey';

        $mail->Password = 'YOUR_SENDGRID_API_KEY';

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        $mail->Port = 587;



        $mail->setFrom('tarunkumarabburi99@gmail.com', 'Your Store');

        $mail->addAddress($order['email'], $customer_name);



        $mail->isHTML(true);

        $mail->Subject = "🧾 Invoice for Order #{$order['id']}";

        $mail->Body    = "

            <p>Hello <strong>{$customer_name}</strong>,</p>

            <p>Thank you for your order <strong>#{$order['id']}</strong>. Please find your invoice attached.</p>

            <p>Best regards,<br>Your Store Team</p>

        ";



        $mail->addAttachment($filename);

        $mail->send();



    } catch (Exception $e) {

        error_log("Email sending failed: " . $mail->ErrorInfo);

    }

}