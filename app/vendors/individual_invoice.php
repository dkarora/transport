<?php
	// don't use App::import here. tends to smash the memory limit!
	require_once('baystate_invoice.php');
	
	class IndividualInvoice extends BaystateInvoice
	{
		var $attendee = array();
		var $workshop = array();
		
		public function __construct($attendee, $workshop)
		{
			parent::__construct();
			$this->attendee = $attendee;
			$this->workshop = $workshop;
		}
		
		protected function Meta()
		{
			parent::Meta();
			
			$isPrivate = (strtolower($this->attendee['User']['affiliation']) == 'private');
			
			$this->SetFont('Times', 'B', '12');
			$this->AutoCell('Sold to:');
			$this->SetFont('Times', '', '12');
			$this->AutoCell($isPrivate ? $this->attendee['User']['full_name'] : $this->attendee['User']['affiliation']);
			$this->Ln();
			
			$this->SetFont('Times', 'B', '12');
			$this->AutoCell('For:');
			$this->SetFont('Times', '', '12');
			$wsName = $this->workshop['Detail']['name'];
			$this->AutoCell("Workshop Registration Fees - $wsName");
			$this->Ln();
		}
		
		protected function InvoiceTable()
		{
			parent::InvoiceTable();
			
			$this->SetFont('Times', '', '12');
			$name = sprintf('%s, %s', $this->attendee['User']['last_name'], $this->attendee['User']['first_name']);
			$isPrivate = (strtolower($this->attendee['User']['affiliation']) == 'private');
			$cost = ($isPrivate ? $this->workshop['Workshop']['private_cost'] : $this->workshop['Workshop']['public_cost']);
			$unitPrice = '$' . number_format($cost, 2);
			$total = $unitPrice;
			
			$cells = array(
				array('text' => '1', 'width' => 0.15),
				array('text' => $name, 'width' => 0.5),
				array('text' => $unitPrice, 'width' => 0.15, 'align' => 'R'),
				array('text' => $total, 'width' => 0.2, 'align' => 'R')
			);
			$border = array('LRB' => array('width' => 0.01, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
			$this->TableRow($cells, $border, 0.3);
			
			$this->SetFont('Times', 'B', '16');
			$cells = array(
				array('text' => 'Grand Total', 'width' => 0.8, 'align' => 'R'),
				array('text' => $total, 'width' => 0.2, 'align' => 'R'),
			);
			$border = array('LRB' => array('width' => 0.01, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
			$this->TableRow($cells, $border, 0.4);
			$this->Ln();
		}
		
		static public function Generate($attendee, $workshop, $output = 'I')
		{
			App::import('Helper', 'TimeFormatter');
			$timeFormatter = new TimeFormatterHelper();
			$pdf = new IndividualInvoice($attendee, $workshop);
			
			// set document information
			$pdf->SetCreator('http://www.mass.gov/baystateroads');
			$pdf->SetAuthor('Baystate Roads Program');
			$pdf->SetSubject($attendee['User']['full_name']);
			$pdf->SetTitle(sprintf('Invoice for %s (%s)', $workshop['Detail']['name'], $timeFormatter->commonDateTime($workshop['Workshop']['date'])));
			
			// header stuff
			$pdf->SetHeaderData(WWW_ROOT . 'img' . DS . 'baystate-logo.png', 1.405, $workshop['Detail']['name'], $timeFormatter->commonDateTime($workshop['Workshop']['date']));
			$pdf->setHeaderFont(Array('Freesans', 'B', 24));
			$pdf->setFooterFont(Array('Freesans', '', 10));
			
			// margin as inches
			$pdf->SetMargins(0.75, 0.75, 1);
			$pdf->SetHeaderMargin(1);
			$pdf->SetFooterMargin(1);
			
			$pdf->SetAutoPageBreak(true, 1);
			$pdf->SetFont('Freesans', '', 12);
			$pdf->AddPage();
			
			// shift down after the header
			$pdf->SetY(2.75);
			$pdf->Body();
			
			// make a temporary file
			$folder = TMP;
			$filename = Inflector::slug($workshop['Detail']['name'], '-').'-'.Inflector::slug($attendee['User']['full_name'], '-').'-invoice.pdf';
			
			switch ($output)
			{
				case 'F':
				case 'FI':
				case 'FD':
					$path = $folder . $filename;
					$pdf->Output($path, $output);
					return $path;
				
				default:
					return $pdf->Output($filename, $output);
			}
		}
	}
?>