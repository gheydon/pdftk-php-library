<?php
/**
 * @author Ben Squire <ben@hoasty.com>
 * @license Apache 2.0
 * 
 * @package PDFTK-PHP-Library
 * @version 0.1.0
 * 
 * @abstract This class allows you to integrate with pdftk command line from within
 * your PHP application (An application for PDF: merging, encrypting, rotating, watermarking, 
 * metadata viewer/editor, compressing etc etc). This library is currently limited 
 * to the concatenation functionality of the binary; additional functionality to
 * come over time.
 * 
 * This library is in no way connected with the author of PDFTK.
 * 
 * To be able to use this library a working version of the binary must be installed
 * and its path configured below.
 * 
 * @uses http://www.pdflabs.com/tools/pdftk-the-pdf-toolkit/
 * 
 * @see install.txt
 * 
 * @example examples/example1.php
 * @example examples/example2.php
 * @example examples/example3.php
*/

class pdftk {

	/* Configuration */
	protected $_bin = '/usr/local/bin/pdftk';
	/* End Configuration */
	
	
	protected $_input_files = null;
	protected $_output_file = null;
	protected $_verbose = false;
	protected $_askmode = false;
	protected $_owner_password = null;
	protected $_user_password = null;
	protected $_encryption = 128;
	protected $_flattenmode = FALSE;


	protected $input_data = null;	//We'll use this to store the key for the input file.
	
	function __construct($params = array()) {
		if (isset($params['owner_password'])) $this->setOwnerPassword($params['owner_password']);
		if (isset($params['user_password'])) $this->setUserPassword($params['user_password']);
		if (isset($params['encryption_level'])) $this->setEncryptionLevel($params['encryption_level']);
		if (isset($params['verbose_mode'])) $this->setVerboseMode($params['verbose_mode']);
		if (isset($params['ask_mode'])) $this->setAskMode($params['ask_mode']);
		if (isset($params['flatten_mode'])) $this->setFlatten($params['flatten_mode']);
	}


	
	public function factory($command, $params = array()) {
		$class_name = 'pdftk_command_' . $command;
		if (class_exists($class_name)) {
			return new $class_name($params);
		}
		
		return new pdftk($oarams);
	}
	
	/**
	 * Sets the level of encrpytion to be used (if owner/user password is specified).
	 * e.g. $foo->setEncrpytionLevel(128);
	 *
	 * @param int $var_encryption
	 * @return __pdftk
	 */
	public function setEncryptionLevel($var_encryption = 128) {
		if ((int)$var_encryption != 40 && (int)$var_encryption != 128) {
			throw new Exception('Encryption should either be 40 or 128 (bit)');
		} else {
			$this->_encryption = $var_encryption;
		}

		return $this;
	}


	
	/**
	 * Returns the level of encrpytion set.
	 * e.g. $level = $foo->getEncryptionLevel();
	 *
	 * @return int
	 */
	public function getEncryptionLevel() {
		return $this->_encryption;
	}
	


	/**
	 * Sets the users password for the ouput file
	 * $foo->setUserPassword("bar");
	 * @return __pdftk
	 */
	public function setUserPassword($var_password = null) {
		$this->_user_password = $var_password;

		return $this;
	}



	/**
	 * Retreives the user-password for the output file
	 * e.g: $foo->getUserPassword();
	 *
	 * @return string
	 */
	public function getUserPassword() {
		return $this->_user_password;
	}



	public function setFlattenMode($var_flattenmode) {
		if (!is_bool($var_flattenmode)) {
			throw new Exception('Flatten Mode should be either true or false');
		} else {
			$this->_flattenmode = $var_flattenmode;
		}
		
		return $this;
	}
	
	

	public function getFlattenMode() {
		return $this->_flattenmode;
	}
	
	
	/**
	 * Sets the owners password for the ouput file
	 * $foo->setOwnerPassword("bar");
	 *
	 * @param string $var_password
	 * @return __pdftk
	 */
	public function setOwnerPassword($var_password = null) {
		$this->_owner_password = $var_password;

		return $this;
	}



	/**
	 * Retreives the owner-password for the output file
	 * e.g: $foo->getOwnerPassword();
	 *
	 * @return string
	 */
	public function getOwnerPassword() {
		return $this->_owner_password;
	}



	/**
	 * Sets whether the cli will output verbose information
	 * e.g:	$foo->setVerboseMode(false);
	 *
	 * @param bool $var_verbose
	 * @return __pdftk
	 */
	public function setVerboseMode($var_verbose = false) {
		if (!is_bool($var_verbose)) {
			throw new Exception('Verbose mode should be either true or false');
		} else {
			$this->_verbose = $var_verbose;
		}

		return $this;
	}



	/**
	 * Returns whether the cli will output verbose information
	 * e.g:	$foo->getVerboseMode();
	 *
	 * @return boolean
	 */
	public function getVerboseMode() {
		return $this->_verbose;
	}



	/**
	 * Sets whether the cli will ask questons when needed
	 * e.g:	$foo->setAskMode(false);
	 *
	 * @param bool $var_askmode
	 * @return __pdftk
	 */
	public function setAskMode($var_askmode = false) {
		if (!is_bool($var_askmode)) {
			throw new Exception('Ask Mode should be either true or false');
		} else {
			$this->_askmode = $var_askmode;
		}

		return $this;
	}



	/**
	 * Returns whether the cli will output questions (when needed)
	 * e.g:	$foo->getAskMode();
	 *
	 * @return boolean
	 */
	public function getAskMode() {
		return $this->_askmode;
	}



	/**
	 * Setups the output file to be used
	 * e.g: $foo->setOutputFile("~/tmp/foo.pdf");
	 *
	 * @param string $var_file
	 * @return __pdftk
	 */
	public function setOutputFile($var_file) {
		$this->_output_file = $var_file;
		return $this;
	}



	/**
	 * Return the output pdfs file
	 * e.g: $foo->getOutputFile();
	 *
	 * @return string
	 */
	public function getOutputFile() {
		return $this->_output_file;
	}



	/**
	 * Setup an input file, as an object
	 * e.g. $foo->setInputFile(array("password"=>"foobar"));
	 *
	 * @param array $_params
	 * @return __pdftk
	 */
	public function setInputFile($_params) {
		if ($_params instanceof pdftk_inputfile) {
			$this->_input_files[] = $_params;
			
		} else {
			$class_name = 'pdftk_inputfile';
			
			$obj_type = get_class($this);
			if (preg_match('/_command_(.*?)$/i', $obj_type, $matches)) {
				if (class_exists($class_name . '_' . $matches[1])) {
					$class_name .= '_' . $matches[1];
				}
			}
			
			$this->_input_files[] = new $class_name($_params);
			
		}
		return $this;
	}



	/**
	 * Returns part of or all of the $this->_input_file array (when possible)
	 * e.g. $temp = $foo->getInputFile();
	 *
	 * @param <type> $var_index
	 * @return mixed __pdftk_inputfile|bool|array
	 */
	public function getInputFile($var_index = null) {
		if (isset($var_index) && isset($this->_input_files[$var_index])) {
			return $this->_input_files[$var_index];

		} elseif (isset($var_index) && !isset($this->_input_files[$var_index])) {
			return false;

		} else {
			return $this->_input_files;

		}
	}



	/**
	 * Returns command to be executed
	 *
	 * @return string
	 */
	protected function getPreCommand() {
		$command = $this->_bin . " ";

		//Assign each PDF a letter. (limited to 24 files atm)?
		foreach ($this->_input_files AS $key=>$file) {

			if ($file->getData() != null) {
				$command .= " - ";
				$this->input_data = $key;
				
			} else {
				$letter = chr(65 + $key);
				$command .= ' ' . $letter . '=' . escapeshellarg($file->getFilename());
				
			}
		}



		//Put read password in place for each file
		//input_pw A=foopass
		$passwords = array();
		foreach ($this->_input_files AS $key=>$file) {
			$letter = chr(65 + $key);
			if ($file->getPassword() !== null) {
				$passwords[] = $letter . '=' . $file->getPassword();
			}
		}

		$command .= ((count($passwords) > 0) ? ' input_pw ' . implode(' ', $passwords) : '');
		
		return $command;
	}

	public function getCommand() {}

	protected function getPostCommand() {
		//Output file paramters
		$command = ' output ';
		if(!empty($this->_output_file)) {
			$command .= escapeshellarg($this->_output_file);

		} else {
			$command .= '-';

		}


		//Check for PDF password...
		if ($this->_owner_password != null || $this->_user_password != null) {

			//Set Encryption Level
			$command .= ' encrypt_' . $this->_encryption . 'bit';


			//TODO: Sets permissions
			//pdftk mydoc.pdf output mydoc.128.pdf owner_pw foo user_pw baz allow printing


			//Setup owner password
			if ($this->_owner_password != null) {
				$command .= ' owner_pw ' . $this->_owner_password;
			}

			//Setup owner password
			if ($this->_user_password != null) {
				$command .= ' user_pw ' . $this->_user_password;
			}
			
		}


		// Flatten Mode
		$command .= (($this->_flattenmode) ? ' flatten' : '');

		//Verbose Mode
		$command .= (($this->_verbose) ? ' verbose' : '');

		//Ask Mode
		$command .= (($this->_askmode) ? ' do_ask' : ' dont_ask');
		
		return $command;
	}

	

	/**
	 * Render document as downloadable resource,
	 * e.g: $foo->downloadOutput();
	 *
	 * @param $return Should data be returned as well 'echoed'
	 * @return mixed void|string
	 */
	public function downloadOutput($return = false) {
		$filename = $this->_output_file;
		$this->_output_file = null;
		
		$pdfData = $this->_renderPdf();
		header('Content-type: application/pdf');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		echo $pdfData;

		if ($return === true) {
			return $pdfData;
		}
	}



	/**
	 * Render document as inline resource
	 * e.g: $foo->inlineOutput();
	 * 
	 * @return void
	 */
	public function inlineOutput($return=false) {
		$filename = $this->_output_file;
		$this->_output_file = null;
		
		$pdfData = $this->_renderPdf();
		$respObj->setHeader('Cache-Control', 'public, must-revalidate, max-age=0', true);
		$respObj->setHeader('Pragma', 'public', true);
		$respObj->setHeader('Expires', 'Sat, 26 Jul 1997 05:00:00 GMT', true);
		$respObj->setHeader('Last-Modified', gmdate('D, d m Y H:i:s') . " GMT", true);
		$respObj->setHeader('Content-Length', strlen($pdfData), true);
		$respObj->setHeader('Content-Disposition', 'inline; filename="' . preg_replace('/[^a-z0-9_\-.]+/i', '_', strtolower($filename)) . '.pdf' . '";', true);
		echo $pdfData;

		if ($return === true) {
			return $pdfData;
		}
	}

	
	
	/**
	 * Builds the final PDF
	 * e.g:	$foo->_renderPdf();
	 *
	 * @return string
	 */
	public function _renderPdf() {
		$command = $this->getCommand();
		$data = ((!is_null($this->input_data) ? $this->_input_files[$this->input_data]->getData() : null));
		$content = $this->_exec($command, $data);

		
		if ($content['stderr'] != '')
			throw new Exception('System error <pre>' . $content['stderr'] . '</pre>');

		//Error only if we expecting something from stdout and nothing was returned
		if (is_null($this->_output_file) && mb_strlen($content['stdout'], 'utf-8') === 0)
			throw new Exception('PDF-TK didnt return any data: ' . $this->getCommand() . ' ' . $this->_input_files[$this->input_data]->getData());

		if ((int) $content['return'] > 1)
			throw new Exception('Shell error, return code: ' . (int)$content['return']);

		return $content['stdout'];
	}



	/**
	 * Executes pdftk command
	 *
	 * @param string $cmd Command to execute
	 * @param string $input Other input (not arguments)??
	 * @return array
	 */
	protected function _exec($cmd, $input = null) {
		
		//TODO: Better handling of error codes
		//http://stackoverflow.com/questions/334879/how-do-i-get-the-application-exit-code-from-a-windows-command-line
		
		
		$result = array('stdout' => '', 'stderr' => '', 'return' => '');

		$descriptorspec = array(
			0 => array('pipe', 'r'),
			1 => array('pipe', 'w'),
			2 => array('pipe', 'w')
		);

		$proc = proc_open($cmd, $descriptorspec, $pipes);

		if (is_resource($proc)) {
			fwrite($pipes[0], $input);
			fclose($pipes[0]);

			$result['stdout'] = stream_get_contents($pipes[1]);
			fclose($pipes[1]);

			$result['stderr'] = stream_get_contents($pipes[2]);
			fclose($pipes[2]);

			$result['return'] = proc_close($proc);

		} else {
			throw new Exception('Unable to open command line resource');
		}

		return $result;
	}
	
	
	
	/**
	 * Returns the command to be executed
	 * 
	 * @return string
	 */
	public function  __toString() {
		return $this->getCommand();
	}
}






class pdftk_inputfile {

	protected $_filename = null;		//File to readin
	protected $_data = null;			//Direct Stream data
	protected $_password = null;		//Allow us to decode
	
	protected $_override = null;		//Incase the string is paticully complex


	function  __construct($_params = array()) {
		if (isset($_params["filename"]))	$this->setFilename($_params["filename"]);
		if (isset($_params["data"]))		$this->setData($_params["data"]);
		if (isset($_params["password"]))	$this->setPassword($_params["password"]);
	}



	public function factory($command, $params) {
		$class_name = 'pdftk_inputfile';
		if (class_exists($class_name . '_' . $command)) {
			$class_name .= '_' . $command;
		}
		
		return new $class_name($params);
	}
	
	
	
	/**
	 * Set the filename to be read from
	 *
	 * @param string $var_filename
	 * @return bool
	 */
	public function setFilename($var_filename) {
		if (file_exists($var_filename)) {
			$this->_filename = $var_filename;
			return true;

		} else {
			throw new Exception("File Doesn't exist: " . $var_filename);
		}
	}



	/**
	 * Return the filename of the input file
	 * e.g:	$foo->getFilename();
	 *
	 * @return string
	 */
	public function getFilename() {
		return $this->_filename;
	}



	/**
	 * Pass the input data in
	 *
	 * @param string $var_data
	 */
	public function setData($var_data = null) {
		$this->_data = $var_data;
	}



	/**
	 * Returns the 'string' version of the file.
	 *
	 * @return <type>
	 */
	public function getData() {
		return $this->_data;
	}



	/**
	 * Set the files read password
	 *
	 * @param string $var_password
	 */
	public function setPassword($var_password = null) {
		$this->_password = $var_password;
	}


	
	/**
	 * Returns the read password set for this input file
	 *
	 * @return string
	 */
	public function getPassword() {
		return $this->_password;
	}



	/**
	 * Allows the user to pass in a replacement command line string
	 * e.g: $foo->setOverride("5-25oddW");
	 *
	 * @param mixed $var_end_page
	 */
	public function setOverride($var_override) {
		$this->_override = $var_override;
	}
	
	
	
	public function getCommand() {}
}





class pdftk_command_cat extends pdftk {
	
	public function getCommand() {
		
		$command = $this->getPreCommand();
		
		$total_inputs = count($this->_input_files);
		
		// TODO: Do we have other commands between the input file elements??
		//	cat, shuffle, burst, generate_fdf, fill_form, background, multibackground,
		//	stamp, multistamp, dump_data, dump_data_utf8, dump_data_fields, dump_data_fields_utf8,
		//	update_info, update_info_utf8, attach_files, unpack_files
		$command .= ' cat';

		//Fetch command for each input file
		if ($total_inputs > 1) {
			foreach ($this->_input_files AS $key=>$file) {
				$letter = chr(65 + $key);
				$command .= ' ' . $letter . $file->getCommand();
			}
		}
		
		$command .= $this->getPostCommand();
		
		return $command;
	}
}

class pdftk_inputfile_cat extends pdftk_inputfile {
	
	protected $rotations = array(0=>"N", 90=>"E", 180=>"S", 270=>"W");

	protected $_start_page = null;		//numeric or end
	protected $_end_page = null;		//numeric or end
	protected $_alternate = null;		//odd or even
	protected $_rotation = null;		//N, E, S or W

	function  __construct($_params = array()) {
		parent::__construct($_params);
		
		if (isset($_params["start_page"]))	$this->setStartPage($_params["start_page"]);
		if (isset($_params["end_page"]))	$this->setEndPage($_params["end_page"]);
		if (isset($_params["alternate"]))	$this->setAlternate($_params["alternate"]);
		if (isset($_params["rotation"]))	$this->setRotation($_params["rotation"]);
	}
	
	/**
	 * Set the start page to read from
	 * e.g: $foo->setStartPage("end");
	 *
	 * @param mixed $var_start_page
	 */
	public function setStartPage($var_start_page) {
		$this->_start_page = $var_start_page;
	}



	/**
	 * Set the end page to read upto
	 * e.g: $foo->setEndPage(9);
	 *
	 * @param mixed $var_end_page
	 */
	public function setEndPage($var_end_page) {
		$this->_end_page = $var_end_page;
	}



	/**
	 * Sets the rotation of this document
	 * e.g: $foo->setRotation(90);
	 * 
	 * @param int $var_rotaton 
	 */
	public function setRotation($var_rotaton) {
		$this->_rotation = $var_rotaton;
	}


	/**
	 * Sets the rotation of the input file
	 * e.g: $foo->setAlternate("even");
	 *
	 * @return void
	 */
	public function setAlternate($var_alternate = null) {
		$this->_alternate = $var_alternate;
	}


	
	/**
	 * Returns command to be executed
	 * e.g:	$foo->_getCatCommand();
	 *
	 * @return string
	 */
	public function getCommand() {

		if ($this->_override != null) return $this->_override;

		$command = "";

		//Page Numbers and Qualifiers
		$command .= (($this->_start_page != null) ? $this->_start_page : "");
		$command .= (($this->_end_page != null) ? "-" . $this->_end_page : "");
		$command .= (($this->_alternate != null) ? $this->_alternate : "");

		//File rotation
		$command .= (($this->_rotation != null) ? $this->rotations[$this->_rotation] : "");

		return $command;
	}
}


class pdftk_command_data_dump extends pdftk {
	
	protected $utf8 = FALSE;
	
	function __construct($params) {
		parent::__construct($params);
		
		if (isset($params['utf8'])) $this->setUtf8($params['utf8']);
	}
	
	public function setUtf8($var_utf8) {
		if (!is_bool($var_utf8)) {
			throw new Exception('Ask Mode should be either true or false');
		} else {
			$this->_askmode = $var_utf8;
		}

		return $this;
	}
	
	public function getCommand() {
		
		$this->_input_files = array(0 => reset($this->_input_files));
		
		$command = $this->getPreCommand();
				
		// TODO: Do we have other commands between the input file elements??
		//	cat, shuffle, burst, generate_fdf, fill_form, background, multibackground,
		//	stamp, multistamp, dump_data, dump_data_utf8, dump_data_fields, dump_data_fields_utf8,
		//	update_info, update_info_utf8, attach_files, unpack_files
		
		$command .= ' data_dump' . ($this->utf8 ? '_utf8' : '');
		
		$command .= $this->getPostCommand();
		
		return $command;
	}
	
	
	
	public function getData() {
		$command = $this->getCommand();
		$data = ((!is_null($this->input_data) ? $this->_input_files[$this->input_data]->getData() : null));
		$content = $this->_exec($command, $data);
		
		if ($content['stderr'] != '') {
			throw new Exception('System error <pre>' . $content['stderr'] . '</pre>');
		}
		else {
			$vars = array();
			$output = explode("\n", $content['stdout']);
			$last_info_key = null;
			
			foreach ($output as $line) {
				if (preg_match('/^([a-z0-9]+): (.*)$/i', $line, $matches)) {
					switch ($matches[1]) {
						case 'InfoKey':
							$last_info_key = trim($matches[2]);
							break;
							
						case 'InfoValue':
							if ($last_info_key) {
								$vars['Info'][$last_info_key] = $matches[2];
								$last_info_key = null;
							}
							break;
							
						default:
							$vars[$matches[1]] = $matches[2];
					}
				}
			}
			
			return $vars;
		}
	}
}


class pdftk_command_dump_data_fields extends pdftk {
	
	protected $utf8 = FALSE;
	
	function __construct($params) {
		parent::__construct($params);
		
		if (isset($params['utf8'])) $this->setUtf8($params['utf8']);
	}
	
	public function setUtf8($var_utf8) {
		if (!is_bool($var_utf8)) {
			throw new Exception('Ask Mode should be either true or false');
		} else {
			$this->_askmode = $var_utf8;
		}

		return $this;
	}
	
	public function getCommand() {
		
		$this->_input_files = array(0 => reset($this->_input_files));
		
		$command = $this->getPreCommand();
				
		// TODO: Do we have other commands between the input file elements??
		//	cat, shuffle, burst, generate_fdf, fill_form, background, multibackground,
		//	stamp, multistamp, dump_data, dump_data_utf8, dump_data_fields, dump_data_fields_utf8,
		//	update_info, update_info_utf8, attach_files, unpack_files
		
		$command .= ' dump_data_fields' . ($this->utf8 ? '_utf8' : '');
		
		$command .= $this->getPostCommand();
		
		return $command;
	}
	
	
	
	public function getData() {
		$command = $this->getCommand();
		$data = ((!is_null($this->input_data) ? $this->_input_files[$this->input_data]->getData() : null));
		$content = $this->_exec($command, $data);
		
		if ($content['stderr'] != '') {
			throw new Exception('System error <pre>' . $content['stderr'] . '</pre>');
		}
		else {
			$fields = array();
			$output = explode("\n", $content['stdout']);
			$field = null;
			
			foreach ($output as $line) {				
				if ($line == '---') {					
					if ($field) {
						$fields[$field['name']] = $field;
					}
					$field = array();
				}
				elseif (preg_match('/^Field([a-z]+): (.*)$/i', $line, $matches)) {
					$field[strtolower($matches[1])] = $matches[2];
				}
			}
			if ($field) {
				$fields[$field['name']] = $field;
			}
			
			return $fields;
		}
	}
}

?>