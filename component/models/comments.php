<?php 

/**
 * Supply Order Component for Joomla! 1.5
 * Comment Model
 * @version 1.5.0
 * @author Howard County Library
 * @package com_supplyorder
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 **/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');

class SupplyOrderModelComments extends JModel
{
	
	/**
	 * Constructor
	 * 
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Insert a comment
	 */
	function insertComment($comment, $request_id, $employee_id)
	{
		$db = JFactory::getDBO();
		
		$query = "INSERT INTO `#__so_comments` 
							(request_id, employee_id, comment_body, date_sent) 
							VALUES 
							($request_id, $employee_id, '$comment', NOW())";
		
		$db->setQuery($query);
		
		if (!$result = $db->query()) {
			JError::raiseError('', JText::_('SQL_ERROR'));
			return false;
		}
		
		return $db->insertid();
	}
	
	/**
	 * Get a list of all comments for a specific request
	 * 
	 * @param int $request_id
	 * @return false if no comments
	 */
	function getComments($request_id)
	{
		$db = JFactory::getDBO();
		
		$query = "SELECT * FROM `#__so_comments` WHERE request_id = $request_id";
		
		$db->setQuery($query);
		$result = $db->loadAssocList();
		
		// Get user model
		$userModel = JModel::getInstance('User', 'SupplyOrderModel');
		
		$comments = array();
		$i = 0;
		foreach ($result as $row) {
			$comments[$i]['comment_body'] = $row['comment_body'];
			$comments[$i]['date_sent'] = $row['date_sent'];
				
			$userInfo = $userModel->getUserInfo($row['employee_id']);
			$comments[$i]['employee_name'] = $userInfo['name'];
			$i++;
		}
		
		return $comments;
	}
	
	/**
	 * Delete comments 
	 * 
	 * @param int $request_id
	 */
	function deleteComments($request_id)
	{
		$db = JFactory::getDBO();
		
		$query = "Delete FROM `#__so_comments` WHERE request_id = $request_id";
		
		$db->setQuery($query);
		
		if (!$result = $db->query()) {
			JError::raiseError('', JText::_('SQL_ERROR'));
			return false;
		} 
		
		return true;
	}
}
