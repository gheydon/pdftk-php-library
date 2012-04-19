<?php
	include('../pdftk/pdftk.php');
	$path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'pdfs' . DIRECTORY_SEPARATOR;
	
	
	
	$pdftk = pdftk::factory('dump_data_fields');
	$pdftk	->setInputFile(array("filename"=>$path . 'example4.pdf'));

	echo '<pre>' . print_r($pdftk->getData(), 1) . '</pre>';
?>