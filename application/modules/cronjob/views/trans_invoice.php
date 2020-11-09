<?php
//require_once(APPPATH.'libraries/tcpdf/tcpdf.php');
		$obj_pdf = new My_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		
		$obj_pdf->SetCreator(PDF_CREATOR);  
		$obj_pdf->SetTitle("Invoice Data");  
		$obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);  
		$obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));  
		$obj_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
		$obj_pdf->SetDefaultMonospacedFont('helvetica');  
		$obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  
		$obj_pdf->SetMargins(PDF_MARGIN_LEFT, '5', PDF_MARGIN_RIGHT);  
		$obj_pdf->setPrintHeader(false);  
		$obj_pdf->setPrintFooter(true);  
		$obj_pdf->SetAutoPageBreak(TRUE, 10);  
		$obj_pdf->SetFont('helvetica', '', 9);
		
		$image = base_url().'assets/images/pride-diesel-logo.png';


		
			$content = '';

			
			//if($j != $after_divide-1){
				$content .= '<table border="0" nobr="true">
				<tr>
					<td width="70%" style="font-size: 9px;"><div style="margin-top: 10px;"></div><strong>COMMENTS:</strong> <br />Terms: Due Upon Receipt <br />Overdue balance will be charged interest at 26.8% per annum, compounded monthly.</td>
					<td width="30%">
					<table border="1" cellpadding="3" nobr="true"> 
						<tr><td>SUB-TOTAL</td><td></td></tr>
						<tr><td></td><td></td></tr>
						<tr><td></td><td></td></tr>
						<tr><td style="border-right: 1px solid transparent;">TOTAL</td><td style="border-left: 1px solid transparent;"></td></tr>
					</table>
					</td>
				</tr>	
				</table>
				<div style="margin-top: 20px;"></div>
				<table border="0" nobr="true">
				<tr>
					<td width="30%">Please remit payment to:<br /><strong>PRIDE DIESEL INC.</strong><br />6050 Dixie Rd<br />Missisauga ON L5T 1A6</td>
					<td width="40%"></td>
					<td width="30%" align="right"><strong>ACCOUNTS RECIEVABLE</strong><br />OFFICE 647-618-7184<br />x 244<br />FAX 866-867-8922<br />EMAIL info@pridediesel.com</td>
				</tr>	
				</table><h3 style="text-align: center;">Thank you for your business.</h3>';
			//}
			$obj_pdf->writeHTML($content);	
		
			


	ob_end_clean();

    $obj_pdf->Output('sample.pdf', 'I');