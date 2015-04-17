<?php $this->set('subnavcontent', $this->element('adminsubnav')); ?>

<?php
	// choosing the workshop
	if (!isset($this->params['named']['workshopid'])) :
?>
		<h2>Print Name Badges</h2>
		
		<?php echo $this->element('page-numbers'); ?>
		
		<table id="workshops">
			<?php
				$headers = array(
					'Workshop' => 'Detail.name',
					'City' => 'Workshop.city',
					'Date' => 'Workshop.date',
					'Category',
					'Print Badges'
				);
				
				echo $this->element('table-headers', array('headers' => $headers));
			?>
			
			<?php $i = 0; ?>
			<?php foreach ($workshops as $index => $workshop): ?>
			<tr
			<?php
				/* apply alt row colors on odd rows */
				if ($i % 2) echo " class='altrow'";
			?>>
				<td><?php echo $workshop['Detail']['name']; ?></td>
				<td><?php echo $workshop['Workshop']['city']; ?></td>
				<td><?php echo $workshop['Workshop']['date']; ?></td>
				<td><?php echo $workshop['Detail']['Category']['name']; ?></td>
				<td>
					<?php
						if ($workshop['Workshop']['attendee_count'] == 0)
							echo $html->div('button-link slim disabled full-width', 'No Attendees');
						else
							echo $html->link('Print Badges', array('workshopid' => $workshop['Workshop']['id']), array('class' => 'button-link slim full-width'));
					?>
				</td>
			</tr>
			<?php $i++; ?>
			<?php endforeach; ?>
		</table>
		
		<?php echo $this->element('page-numbers'); ?>
	
<?php
	// showing the pdf
	else :
		App::import('Vendor','xtcpdf');  
		$tcpdf = new XTCPDF('P', 'in', 'Letter'); 
		$tcpdf->setFontSubsetting(false);
		$textfont = 'freesans'; // looks better, finer, and more condensed than 'dejavusans' 
		
		$tcpdf->SetAutoPageBreak(false); 
		
		$tcpdf->SetTextColor(0, 0, 0); 
		$tcpdf->SetFont($textfont,'', 18);
		
		$tcpdf->AddPage();
		$tcpdf->SetTitle('Name Badges for Workshop ' . $workshop['Detail']['name']);
		$i = 0;
		
		// these values are for the avery 5263 -- modify them as needed.
		// 11-19-12: now for the avery 8395
		$badgeWidth = 3.38;
		$badgeHeight = 2.23;
		$hSpacing = 0.38;
		$nColumns = 2;
		$nRows = 4;
		$vSpacing = 0.19;
		$groupSpacing = 0.08;
		$leftMargin = 0.69;
		$topMargin = 0.75;
		
		$tcpdf->setLeftMargin($leftMargin, 0, -1, true);
		
		$count = 0;
		
		$tcpdf->SetX($leftMargin);
		$tcpdf->SetY($topMargin);
		
		// set the margins manually because tcpdf won't do it for us
		foreach ($attendees as $id => $attendee)
		{
			// determine the block height (first name + last name + newline + groupname
			$blockHeight = 0;
			$firstNameHeight = 0;
			$lastNameHeight = 0;
			$newLineHeight = 0;
			$affiliationHeight = 0;
			
			$lastX = $tcpdf->GetX();
			$lastY = $tcpdf->GetY();
			
			$tcpdf->SetFont($textfont, 'B', 40);
			$tcpdf->SetXY($lastX, $lastY + 0.35);
			$tcpdf->MultiCell($badgeWidth, $badgeHeight, "" . $attendee['User']['first_name'], 0, 'C', false, 0, '', '', true, 0, false, true, $badgeHeight, 'T', true);
			$tcpdf->SetXY($lastX, $lastY);
			
			$tcpdf->SetFont($textfont, '', 18);
			// kludge: add two newlines to make space for the first name
			$tcpdf->MultiCell($badgeWidth, $badgeHeight, "\n\n" . $attendee['User']['last_name'] . "\n" . $attendee['User']['affiliation'], 0, 'C', false, 0, '', '', true, 0, false, true, $badgeHeight, 'M', true);
			
			$count++;
			
			// move cursor to next column
			if ($count % $nColumns == 0)
			{
				$tcpdf->Ln();
				$tcpdf->SetY($tcpdf->GetY() + $vSpacing);
			}
			
			// fix stupid multicell bug
			if ($count % $nColumns == 1)
				$tcpdf->SetX($hSpacing + $tcpdf->GetX());
			
			if ($count % ($nColumns * $nRows) == 0)
			{
				$tcpdf->AddPage();
				$tcpdf->SetX($leftMargin);
			}
		}
		
		echo $tcpdf->Output($workshop['Detail']['name'] . ".pdf", 'S');
		
	endif;
?>
