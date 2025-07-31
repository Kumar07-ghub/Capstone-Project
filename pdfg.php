<?php

require_once(__DIR__ . '/fpdf184/fpdf.php');



class PDF extends FPDF {

    // Page header

    function Header() {

        $this->SetFont('Arial', 'B', 15);

        $this->Cell(0, 10, 'Invoice', 0, 1, 'C');

        $this->Ln(10);

    }



    // Page footer

    function Footer() {

        $this->SetY(-15);

        $this->SetFont('Arial', 'I', 8);

        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');

    }

}

