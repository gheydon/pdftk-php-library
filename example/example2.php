<?php
	include('../pdftk/pdftk.php');
	$path = dirname(__FILE__) . DIRECTORY_SEPARATOR. 'pdfs' . DIRECTORY_SEPARATOR;
	
	
	
	$pdftk = pdftk::factory('cat');
	$pdftk	->setInputFile(array("filename"=>$path . 'example.pdf', 'start_page'=>1, "end_page"=>2))
			->setInputFile(array("filename"=>$path . 'example.pdf', 'rotation'=>90))
			->setUserPassword("userpassword")
			->setOwnerPassword("ownerpassword")
			->setEncryptionLevel(40)					//Weak Encryption, 128 is default
			->setOutputFile($path . 'generated.pdf');				
	
	
	//echo $pdftk->getCommand();
	$pdftk->_renderPdf();
?>