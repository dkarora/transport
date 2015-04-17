<?php
	class BbCodeHelper extends AppHelper
	{
		var $helpers = array('Html');
		
		function bb2html($bb)
		{
			// ensure there is a newline at the end to not confuse the parser
			$bb .= "\n";
			
			$search = array(
				'/\[img\](.*?)\[\/img\]/',
				'/\[url="?(.*?)"?\](.*?)\[\/url\]/',
				'/\[b\](.*?)\[\/b\]/',
				'/\[i\](.*?)\[\/i\]/',
				'/\[u\](.*?)\[\/u\]/',
				'/\[quote\](.*?)\[\/quote\]/',
				'/\*\s*(.*?)\n+/',
			);
			
			$replace = array(
				'<img src="$1" alt="$1" />',
				'<a href="$1">$2</a>',
				'<strong>$1</strong>',
				'<span class="italics">$1</span>',
				'<span class="underline">$1</span>',
				'<blockquote>$1</blockquote>',
				'<ul class="bb-generated"><li>$1</li></ul>',
			);
			
			return nl2br(preg_replace($search, $replace, $bb));
		}
		
		function stripbb($bb, $replacenl = true)
		{
			$search = array(
				'/\[img\](.*?)\[\/img\]/',
				'/\[url="?(.*?)"?\](.*?)\[\/url\]/',
				'/\[b\](.*?)\[\/b\]/',
				'/\[i\](.*?)\[\/i\]/',
				'/\[u\](.*?)\[\/u\]/',
				'/\[quote\](.*?)\[\/quote\]/',
			);
			
			$replace = array(
				'',
				'$1',
				'$1',
				'$1',
				'$1',
				'$1',
			);
			
			if ($replacenl)
				return nl2br(preg_replace($search, $replace, $bb));
			else
				return preg_replace($search, $replace, $bb);
		}
	}
?>
