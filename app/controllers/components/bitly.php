<?php
	class BitlyComponent extends Object
	{
		function shorten($url)
		{
			$login = 'baystateroads';
			$format = 'txt';
			$domain = 'j.mp';
			$apiKey = 'R_74adbc98ab88bcc84dff6deaf70c5da1';
			
			$request = sprintf('http://api.bit.ly/v3/shorten?login=%s&format=%s&domain=%s&apiKey=%s&longUrl=%s', $login, $format, $domain, $apiKey, urlencode($url));
			$response = file_get_contents($request);
			
			return $response;
		}
	}
?>