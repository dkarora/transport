<?php
	App::import('Vendor','xtcpdf');  
	$tcpdf = new XTCPDF('L', 'in', 'LETTER');
	
	$tcpdf->SetAutoPageBreak( false, 1 );
	
	$titleWidth = 7.95;
	$titleHeight = 1.2;
	$BSRLogoWidth = 1.3;
	$BSRLogoSpacing = 0.3;
	
	$lrMargins = 1;
	$tbMargins = 0.75;
	
	// text
	$headerText = 'Baystate Roads Program';
	$line2Text = 'Certifies that';
	$line3Text = 'has achieved the rank of';
	$logosSpacing = 0.5;
	$signaturesSpacing = 0;
	$signatureLineLength = 3;
	
	$tcpdf->SetTextColor(0, 0, 0); 
	$tcpdf->SetMargins($lrMargins, $tbMargins);
	
	$headerWidth = $titleWidth + $BSRLogoWidth + $BSRLogoSpacing;
	
	foreach ($scholars as $scholar)
	{
		$tcpdf->AddPage();
		$tcpdf->SetLineWidth(0.025);
		
		// center the header
		$tcpdf->SetX($tcpdf->getPageWidth() / 2 - $headerWidth / 2);
		$tcpdf->SetFont('Times','', 40);
		$tcpdf->Cell($titleWidth, $titleHeight, $headerText, 'TB', 0, 'C');
		
		$tcpdf->Image('img/baystate-logo-black.png', $tcpdf->GetX() + $BSRLogoSpacing, $tcpdf->GetY(), $BSRLogoWidth);
		$tcpdf->Ln(1.4);
		
		$tcpdf->SetFont('Times','', 28);
		$tcpdf->Write(1, $line2Text, '', 0, 'C', false);
		$tcpdf->Ln(1);
		
		$name = $scholar['User']['full_name'];
		
		$tcpdf->SetFont('Freesans','B', 36);
		$tcpdf->Write(1, $name, '', 0, 'C', false);
		$tcpdf->Ln(1);
		
		$tcpdf->SetFont('Times','', 28);
		$tcpdf->Write(1, $line3Text, '', 0, 'C', false);
		$tcpdf->Ln(1);
		
		$tcpdf->SetFont('Freesans', 'B', 36);
		$tcpdf->Write(1, 'Baystate Roads Scholar', '', 0, 'C', false);
		$tcpdf->Ln(0.6);
		
		$tcpdf->Write(1, date('Y'), '', 0, 'C');
		$tcpdf->Ln(1.6);
		
		$logoStartX = 2;
		$logoStartY = 7;
		
		$tcpdf->SetX($logoStartX);
		$tcpdf->SetY($logoStartY);
		
		$tcpdf->Image('img/massdot-logo-black.png', $tcpdf->GetX(), $tcpdf->GetY() + 0.25, 1.5, 0, '', '', 'T');
		$tcpdf->SetX($tcpdf->GetX() + $logosSpacing);
		$tcpdf->Image('img/usa-dot-logo-black.png', $tcpdf->GetX(), $tcpdf->GetY() - 0.25, 1, 0, '', '', 'M');
		$underlineY = $tcpdf->GetY();
		$tcpdf->SetX($tcpdf->GetX() + $logosSpacing);
		$tcpdf->Image('img/umass-logo-black.png', $tcpdf->GetX(), $tcpdf->GetY(), 1, 0, '', '', 'T');
		$tcpdf->SetX($tcpdf->GetX() + $logosSpacing);
		
		$underlineX = $tcpdf->GetX();
		$tcpdf->SetLineWidth(0.015);
		$tcpdf->Image('img/cja-sig-black.png', $tcpdf->GetX() + 0.25, $tcpdf->GetY(), 2.5, 0, '', '', 'B');
		$tcpdf->Line($underlineX, $underlineY + 0.55, $underlineX + $signatureLineLength, $underlineY + 0.525);
		
		$tcpdf->SetY($tcpdf->GetY() - 0.5);
		$tcpdf->SetX($underlineX + 0.65);
		$tcpdf->SetFont('Freesans', '', 14);
		$tcpdf->Write(1, 'Program Manager', '', 0, 'L');
	}
	
	echo $tcpdf->Output("Road Scholar Certificates" . ".pdf", 'S');
?>
