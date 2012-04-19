<?php
	include('../pdftk/pdftk.php');
	$path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'pdfs' . DIRECTORY_SEPARATOR;
	
	
	
	$pdftk = pdftk::factory('fill_form');
	$pdftk	->setInputFile(array("filename"=>$path . 'example4.pdf'))
			->setFieldData(array('firstname' => 'Gordon', 'lastname' => 'Heydon'))
			->setFlattenMode(true);

	//echo '<pre>' . print_r($pdftk->getCommand(), 1) . '</pre>';
	header('Content-type: application/pdf');
	header('Content-Disposition: attachment; filename="temp.pdf"');
	echo $pdftk->_renderPdf();
	
?>