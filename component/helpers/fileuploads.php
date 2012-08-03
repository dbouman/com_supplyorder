<?php 

/**
 * Supply Order Component for Joomla! 1.5
 * Helper function for handling all file uploads
 * @version 1.5.0
 * @author Howard County Library
 * @package com_supplyorder
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 **/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//import joomlas filesystem functions, we will do all the filewriting with joomlas functions,
//so if the ftp layer is on, joomla will write with that, not the apache user, which might
//not have the correct permissions
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class SupplyOrderFileUploads
{
	
	static private $VALID_FILE_EXTS = array('jpeg','jpg','gif','png','pdf','odt','xls','doc','docx','ods');
	static private $MAX_FILE_SIZE = 2000000;
	static private $MAX_FILE_SIZE_MB = '2MB'; 
	
	/**
	 * By default the $_FILES passed in from form data are grouped by key instead of 
	 * by file. This function swaps that so they are grouped by file and it is easier to handle 
	 * multiple files
	 * @param $_FILES $files_arr 
	 */
	static public function initFilesArray ($files_arr) {
		for ($i=0;$i<count($files_arr['name']);$i++) {
			$files[$i]['name'] = $files_arr['name'][$i];
			$files[$i]['tmp_name'] = $files_arr['tmp_name'][$i];
			$files[$i]['type'] = $files_arr['type'][$i];
			$files[$i]['error'] = $files_arr['error'][$i];
			$files[$i]['size'] = $files_arr['size'][$i];
		}
		
		return $files;
	}
	
	static public function uploadFile ($file, $request_id) {
		$error = "";
		
		//the name of the file in PHP's temp directory that we are going to move to our folder
		$fileTemp = $file['tmp_name'];
		
		$uploadPath = self::getFileLocation ($file, $request_id);
		
		if(!JFile::upload($fileTemp, $uploadPath))
		{
			$error = JText::_( 'ERROR MOVING FILE' );
		}
		
		return $error;
	}
	
	static public function getFileLocation ($file, $request_id, $relative=false) {
		
		$filename = $file['name'];
		//lose any special characters in the filename
		$filename = preg_replace("/[^A-Za-z0-9]/i", "-", $filename);
		$filename = $request_id . "-" . $filename;
		
		//always use constants when making file paths, to avoid the possibilty of remote file inclusion
		$uploadPath = "";
		if (!$relative)
			$uploadPath .= JPATH_SITE;
		
		$uploadPath .= DS.'media'.DS.'com_supplyorder'.DS.'uploads'.DS.$filename;
		
		return $uploadPath;
	}
	
	static public function deleteFile ($file_location) {
		return unlink($file_location);
	}
	
	static public function checkFileForError($file) {
		$filename = $file['name'];
		$filesize = $file['size'];
		$errorCode = $file['error'];
		$error = "";
		
		$error = self::checkErrorCode($errorCode);
		if (!empty($error)) {
			return $error;
		}
		
		$error = self::checkFileSize($filesize);
		if (!empty($error)) {
			return $error;
		}
		
		$error = self::checkValidExtension($filename);
		if (!empty($error)) {
			return $error;
		}
		
		
	}
		
	static private function checkErrorCode ($errorCode) {
		$error = "";
		
		if ($errorCode > 0)
		{
			switch ($errorCode)
			{
				case 1:
					$error = JText::_( 'FILE TO LARGE THAN PHP INI ALLOWS' );
					break;
				case 2:
					$error = JText::_( 'FILE TO LARGE THAN HTML FORM ALLOWS' );
					break;
				case 3:
					$error = JText::_( 'ERROR PARTIAL UPLOAD' );
					break;
				case 4:
					$error = JText::_( 'ERROR NO FILE' );
					break;
			}
		}
		
		return $error;
	}
	
	static private function checkFileSize  ($filesize) {
		$error = "";
		
		if($fileSize > self::$MAX_FILE_SIZE)
		{
			$error = JText::_( 'FILE BIGGER THAN ' . self::$MAX_FILE_SIZE_MB );
		}
		
		return $error;
	}
	
	static private function checkValidExtension ($filename) {
		$error = "";
		$uploadedFileNameParts = explode('.',$filename);
		$uploadedFileExtension = array_pop($uploadedFileNameParts);
		
		//assume the extension is false until we know its ok
		$extOk = false;
		
		//go through every ok extension, if the ok extension matches the file extension (case insensitive)
		//then the file extension is ok
		foreach(self::$VALID_FILE_EXTS as $validExt)
		{
			if( preg_match("/$validExt/i", $uploadedFileExtension ) )
			{
				$extOk = true;
			}
		}
		
		if ($extOk == false)
		{
			$error = JText::_( 'INVALID EXTENSION' );
		}
		
		return $error;
	}
		
}



?>