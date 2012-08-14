<?php 

/**
 * Supply Order Component for Joomla! 1.5
 * Files Model
 * @version 1.5.0
 * @author Howard County Library
 * @package com_supplyorder
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 **/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


jimport('joomla.application.component.model');

class SupplyOrderModelFiles extends JModel
{
	
	/**
	 * Construct
	 * 
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Uploads a file and stores its location in the database
	 */
	function insertFile($file, $request_id, $employee_id) {

		$db = JFactory::getDBO();
		
		if(!class_exists('SupplyOrderFileUploads')) require('components'.DS.'com_supplyorder'.DS.'helpers'.DS.'fileuploads.php');
		
		// Upload actual file to correct location on server
		$error = SupplyOrderFileUploads::uploadFile($file, $request_id);
		if (!empty($error)) {
			JError::raiseError('', $error);
		}
		$file_name = SupplyOrderFileUploads::getCleanFilename($file, $request_id);
		$file_location = SupplyOrderFileUploads::getFileLocation($file,$request_id,true);

		$query = "INSERT INTO `#__so_files`
					(request_id, employee_id, file_location, file_name, date_posted)
					VALUES ($request_id, $employee_id, '$file_location', '$file_name', NOW())";

		$db->setQuery($query);

		if (!$result = $db->query()) {
			JError::raiseError('', JText::_('SQL_ERROR'));
			return false;
		}

		return $db->insertid();
	}
	
	/**
	 * Delete a specific file
	 * @param int $file_id
	 */
	function deleteFile ($file_id) {
		$db = JFactory::getDBO();
	
		if(!class_exists('SupplyOrderFileUploads')) require('components'.DS.'com_supplyorder'.DS.'helpers'.DS.'fileuploads.php');
	
		// Delete each physical file
		$file = $this->getFile($file_id);
		$success =SupplyOrderFileUploads::deleteFile(JPATH_SITE . $file['file_location']);
		if (!$success) {
			JError::raiseError('', JText::_('FILE_DELETE_FAILED'));
		}
	
		$query = "DELETE FROM `#__so_files` WHERE file_id = '$file_id'";
	
		$db->setQuery($query);
	
		if (!$result = $db->query()) {
			JError::raiseError('', JText::_('SQL_ERROR'));
			return false;
		}
	
		return true;
	}
	
	/**
	 * Delete all files associated with a specific request id
	 * @param int $request_id
	 */
	function deleteFiles ($request_id) {
		$db = JFactory::getDBO();
		
		if(!class_exists('SupplyOrderFileUploads')) require('components'.DS.'com_supplyorder'.DS.'helpers'.DS.'fileuploads.php');
		
		// Delete each physical file
		$files = $this->getFiles($request_id);
		foreach ($files as $file) {
			$success =SupplyOrderFileUploads::deleteFile(JPATH_SITE . $file['file_location']);
			if (!$success) {
				JError::raiseError('', JText::_('FILE_DELETE_FAILED'));
			}
		}
		
		$query = "DELETE FROM `#__so_files` WHERE request_id = '$request_id'";
		
		$db->setQuery($query);
		
		if (!$result = $db->query()) {
			JError::raiseError('', JText::_('SQL_ERROR'));
			return false;
		}
		
		return true;
	}
	
	/**
	 * Get a list of all files for a specific request
	 *
	 * @param int $request_id
	 * @return false if no files
	 */
	function getFiles($request_id)
	{
		$db = JFactory::getDBO();
	
		$query = "SELECT * FROM `#__so_files` WHERE request_id = $request_id";
	
		$db->setQuery($query);
		$result = $db->loadAssocList();
	
		return $result;
	}
	
	/**
	 * Get a specific file
	 *
	 * @param int $file_id
	 * @return details on a specific file
	 */
	function getFile($file_id)
	{
		$db = JFactory::getDBO();
	
		$query = "SELECT * FROM `#__so_files` WHERE file_id = $file_id";
	
		$db->setQuery($query);
		$result = $db->loadAssoc();
	
		return $result;
	}
	
}