<?php
	App::import('Vendor','tcpdf');

	class BaystateInvoice extends TCPDF
	{
		public function TableRow($cells, $border = '', $height = 0)
		{
			$pageWidth = $this->getPageWidth() - $this->original_lMargin - $this->original_rMargin;
			
			foreach ($cells as $cell)
			{
				$text = '';
				$align = 'L';
				
				if (!empty($cell['text']))
					$text = $cell['text'];
				if (!empty($cell['align']))
					$align = $cell['align'];
				
				$this->Cell($cell['width'] * $pageWidth, $height, $text, $border, 0, $align);
			}
			$this->Ln();
		}
		
		protected function InvoiceTable()
		{
			// text is any string
			// width [0..1] as percentage of page width
			// everything else is to tcpdf spec
			// and yes, that is a scary thought
			$headers = array(array('text' => 'Quantity', 'width' => 0.15), array('text' => 'Description', 'width' => 0.5), array('text' => 'Unit Price', 'width' => 0.15, 'align' => 'R'), array('text' => 'Total', 'width' => 0.2, 'align' => 'R'));
			$border = array(
				'B' => array('width' => 0.05, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)),
				'LTR' => array('width' => 0.01, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0))
			);
			$this->SetFont('Times', 'B', '12');
			$this->TableRow($headers, $border, 0.3);
		}
		
		public function __construct()
		{
			parent::__construct('P', 'in', 'LETTER', true, 'UTF-8', false);
		}
		
		// proof that tcpdf is worse than useless
		public function AutoCell($text)
		{
			$this->MultiCell(0, 0, $text, 0, 'L');
		}
		
		public function Body()
		{
			$this->Meta();
			$this->InvoiceTable();
			$this->PaymentInfo();
		}
		
		protected function Meta()
		{
			$this->SetFont('Times', '', '12');
			
			$date = date('m/d/Y');
			$this->AutoCell("INVOICE:\n$date");
			$this->Ln();
		}
		
		public function Header()
		{
			// only print the header on the first page
			if ($this->page > 1)
				return;
			
			$lineColor = array(19, 10, 188);
			$thickLineStretch = ($this->w - $this->original_lMargin - $this->original_rMargin) * 0.4;
			$thinLineStretch = ($this->w - $this->original_lMargin - $this->original_rMargin) - 0.5;
			
			$headerfont = $this->getHeaderFont();
			$headerdata = $this->getHeaderData();
			$this->y = $this->header_margin;
			
			// draw the thin lines first
			$this->SetLineStyle(array('width' => 1 / $this->k, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $lineColor));
			$this->Cell($thinLineStretch, 0, '', 'T', 0, 'C');
			$this->x = $this->original_lMargin;
			$this->y = $this->header_margin + 1.3;
			$this->Cell($thinLineStretch, 0, '', 'T', 0, 'C');
			
			// draw the thick lines next
			$this->x = $this->original_lMargin + 0.05;
			$this->y = $this->header_margin + 0.1;
			$this->SetLineStyle(array('width' => 8 / $this->k, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $lineColor));
			$this->Cell($thickLineStretch, 0, '', 'T', 0, 'C');
			
			$this->x = $this->original_lMargin + 0.05;
			$this->y = $this->header_margin + 1.2;
			$this->SetLineStyle(array('width' => 8 / $this->k, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $lineColor));
			$this->Cell($thickLineStretch, 0, '', 'T', 0, 'C');
			
			// logo
			$this->x = $this->w - $this->original_rMargin - $headerdata['logo_width'];
			$this->y = $this->header_margin - 0.01;
			$this->Image($headerdata['logo'], '', '', $headerdata['logo_width']);
			
			// text
			$this->SetTextColor(0, 0, 0);
			$this->x = $this->original_lMargin;
			$this->y = $this->header_margin + 0.2;
			$this->SetFont('HelveticaB', 'B', 12);
			$this->Cell(0, 0, 'BAYSTATE ROADS PROGRAM', '', 1, 'L');
			$this->SetFont('Times', '', 7);
			$this->Cell(0, 0, 'University of Massachusetts Transportation Center', '', 1);
			$this->Cell(0, 0, '214 Marston Hall', '', 1);
			$this->Cell(0, 0, '130 Natural Resources Road', '', 1);
			$this->Cell(0, 0, 'Amherst, MA 01003', '', 1);
			$this->Cell(0, 0, '(413) 545-5403    FAX: (413) 545-6471', '', 1);
			$this->Cell(0, 0, 'cindy@baystateroads.org   http://www.mass.gov/baystateroads', '', 1);
		}
		
		protected function PaymentInfo()
		{
			$this->SetFont('Times', 'B', 12);
			$this->MultiCell(0, 0, '*Please reference attendee names or include a copy of this invoice for reference when sending payment.');
			$this->Ln();
			
			$this->Cell(0, 0, 'Checks should be made payable to: University of Massachusetts', '', 1);
			$this->SetFont('Times', '', 12);
			$this->Cell(0, 0, 'Please remit payment to:', '', 1);
			//$this->Cell(0, 0, 'Christopher Ahmadjian, Director', '', 1);
			$this->Cell(0, 0, 'Baystate Roads Program', '', 1);
			$this->Cell(0, 0, '214 Marston Hall UMass', '', 1);
			$this->Cell(0, 0, '130 Natural Resources Rd', '', 1);
			$this->Cell(0, 0, 'Amherst, MA 01003', '', 1);
			
			$this->Ln();
			$this->SetFont('Times', '', 7);
			$this->Cell(0, 0, 'Federal ID# 043-167-352', '', 1);
		}
	}
?>
