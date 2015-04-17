<?php 
App::import('Vendor','tcpdf/tcpdf'); 

class XTCPDF  extends TCPDF 
{
	// define these so no horizontal line shows up on
	// the resulting pdf
	function Header() { }
	
	function Footer() { }
} 
?>