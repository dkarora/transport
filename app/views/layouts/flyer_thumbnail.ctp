<?php
	Configure::write('debug', 0);
	header("Content-type: image/png"); 
	echo $content_for_layout; 
?>