<?php
	// time out after 10 minutes
	set_time_limit(600);
	
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
	$line2Text = 'Recognizes the participation of';
	$line3Text = 'At the workshop on';
	$line4Text = 'Held at:';
	$line5Text = 'on';
	//$line6Text= 'CEU';
	$logosSpacing = 0.125;
	$signaturesSpacing = 0.5;
	$signatureLineLength = 2.25;
	
	$tcpdf->SetTextColor(0, 0, 0);
	$tcpdf->setFontSubsetting(false);
	$tcpdf->SetMargins($lrMargins, $tbMargins);
	
	$headerWidth = $titleWidth + $BSRLogoWidth + $BSRLogoSpacing;
	
	foreach ($attendees as $attendee)
	{
		$tcpdf->AddPage();
		$tcpdf->SetLineWidth(0.025);
		
		// center the header
		$tcpdf->SetX($tcpdf->getPageWidth() / 2 - $headerWidth / 2);
		$tcpdf->SetFont('Times','', 40);
		$tcpdf->Cell($titleWidth, $titleHeight, $headerText, 'TB', 0, 'C');
		
		$tcpdf->Image('img/baystate-logo-black.png', $tcpdf->GetX() + $BSRLogoSpacing, $tcpdf->GetY(), $BSRLogoWidth);
		$tcpdf->Ln(1.5);
		
		$tcpdf->SetFont('Times','', 22);
		$tcpdf->Write(1, $line2Text, '', 0, 'C', true);
		
		$name = $attendee['User']['full_name'];
		$tcpdf->SetFont('Freesans','B', 32);
		$tcpdf->Write(1, $name, '', 0, 'C', true);
		$tcpdf->Ln(0.5);
		
		$tcpdf->SetFont('Times','', 16);
		$tcpdf->Write(1, $line3Text, '', 0, 'L');
		$tcpdf->SetX($tcpdf->GetX() + 0.2);
		
		$tcpdf->SetFont('Freesans', 'B', 14);
		$tcpdf->Write(1, $workshop['Detail']['name']);
		$tcpdf->Ln(0.5);
		
		$tcpdf->SetFont('Times','', 16);
		$tcpdf->Write(1, $line4Text, '', 0, 'L');
		$tcpdf->SetX($tcpdf->GetX() + 0.2);
		
		$tcpdf->SetFont('Freesans', 'B', 14);
		$tcpdf->Write(1, $workshop['Workshop']['city']);
		
		$tcpdf->SetX($tcpdf->getPageWidth() / 2);
		$tcpdf->SetFont('Times','', 16);
		$tcpdf->Write(1, $line5Text, '', 0, 'L');
		$tcpdf->SetX($tcpdf->GetX() + 0.2);
		
		$tcpdf->SetFont('Freesans', 'B', 14);
		$tcpdf->Write(1, $timeFormatter->writtenWord($workshop['Workshop']['date']));
		$tcpdf->Ln(1.5);
		
		$tcpdf->Image('img/massdot-logo-black.png', $tcpdf->GetX(), $tcpdf->GetY() + 0.25, 1.5, 0, '', '', 'T');
		$tcpdf->SetX($tcpdf->GetX() + $logosSpacing);
		$tcpdf->Image('img/usa-dot-logo-black.png', $tcpdf->GetX(), $tcpdf->GetY() - 0.25, 1, 0, '', '', 'M');
		$underlineY = $tcpdf->GetY();
		$tcpdf->SetX($tcpdf->GetX() + $logosSpacing);
		$tcpdf->Image('img/umass-logo-black.png', $tcpdf->GetX(), $tcpdf->GetY(), 1, 0, '', '', 'T');
		$tcpdf->SetX($tcpdf->GetX() + $logosSpacing * 2);
		
		$underlineX = $tcpdf->GetX();
		$tcpdf->SetLineWidth(0.015);
		$tcpdf->Image('img/cja-sig-black.png', $tcpdf->GetX() + 0.015, $tcpdf->GetY(), 2, 0, '', '', 'B');
		$tcpdf->Line($underlineX, $underlineY + 0.525, $underlineX + 2.5, $underlineY + 0.525);
		$tcpdf->Line($underlineX + $signatureLineLength + $signaturesSpacing, $underlineY + 0.525, $underlineX + $signatureLineLength * 2 + $signaturesSpacing, $underlineY + 0.525);
	}
	
	echo $tcpdf->Output("Road Scholar Certificates" . ".pdf", 'S');
?>