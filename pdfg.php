<?php
require_once(__DIR__ . '/fpdf184/fpdf.php');

class PDF extends FPDF {
    function Header() {
        // Company logo
        $logo_path = __DIR__ . '/img/indian-supermarket-logo.jpg';
        if (file_exists($logo_path)) {
            // Get the image size to maintain aspect ratio
            list($img_actual_width, $img_actual_height) = getimagesize($logo_path);

            // Set the desired width for the image
            $img_width = 30; 

            // Calculate the height while maintaining the aspect ratio
            $img_height = ($img_actual_height / $img_actual_width) * $img_width;

            // Position the image (10mm from the left, 6mm from the top)
            $this->Image($logo_path, 10, 6, $img_width, $img_height);
        }
        
        // Company name and info
        $this->SetFont('Arial','B',16);
        $this->Cell(0, 6, 'The Indian Supermarket', 0, 1, 'R');
        $this->SetFont('Arial','',10);
        $this->Cell(0, 5, '108 University Avenue, Waterloo, On, Canada', 0, 1, 'R');
        $this->Cell(0, 5, 'Phone: (123) 456‑7890 | Email: info@theindiansupermarket.com', 0, 1, 'R');
        $this->Ln(5);
        
        // Horizontal line
        $y = $this->GetY() + 2;  // move line 2mm down
        $this->Line(10, $y, $this->GetPageWidth() - 10, $y);
        $this->SetDrawColor(0, 0, 0);
        $this->Ln(5);
    }

    function Footer() {
        // Position at 40mm from the bottom
        $this->SetY(-40);
        $this->SetFont('Arial','',9);
        $this->Cell(0,6,'Thank you for your business!',0,1,'L');
        $this->Cell(0,6,'This is a computer-generated invoice and does not require a signature.',0,1,'L');
        $this->Cell(0,6,'Page ' . $this->PageNo() . '/{nb}',0,0,'C');
    }
}
?>
