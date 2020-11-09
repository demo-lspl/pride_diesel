<?php
class MY_tcpdf extends TCPDF {
public function Header() {
	$this->SetTopMargin(10);
    $headerData = $this->getHeaderData();
    $this->SetFont('helvetica', 'B', 10);
    $this->writeHTML($headerData['string']);
	$this->SetTopMargin(50);
	//$img_file = BASEPATH .'assets/images/pride-diesel-logo.png';
}

public function Footer() {
	$footertext = '';
	//$this->SetTopMargin(10);
    //$footerData = $this->getFooterData();
    $this->SetFont('helvetica', 'B', 10);
	//$this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    //$this->writeHTML($footerData['string']);
	$this->writeHTML($footertext, false, true, false, true);
	//$this->SetTopMargin(30);
}
}