<?php
	include('../pdftk/pdftk.php');
	$path = dirname(__FILE__) . DIRECTORY_SEPARATOR. 'pdfs' . DIRECTORY_SEPARATOR;
	
	
	
	$pdftk = pdftk::factory('cat');
	
	$tmp = pdftk_inputfile::factory('cat', array("filename"=>$path . 'example.pdf', 'start_page'=>2));

	$pdftk	->setInputFile($tmp)
			->setInputFile(array("filename"=>$path . 'example2.pdf', 'rotation'=>90))
			->setInputFile(array("filename"=>$path . 'example2.pdf', 'password'=>'password', 'alternate'=>'odd'))
			->setOutputFile("test.pdf");
	
	
	$pdftk->downloadOutput();
	//echo $pdftk;
	
?>