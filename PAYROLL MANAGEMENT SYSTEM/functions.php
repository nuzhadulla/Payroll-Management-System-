<?php
	function printArray($str){
		print "<pre>";
			print_r($str);
		print "</pre>";
	}
	function redirect($url){
		header("location:".$url);
		exit;
	}
	
?>