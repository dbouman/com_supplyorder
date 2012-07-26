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
	private $comments = array();
	
	/**
	 * Constructor
	 * 
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * NOT USED
	 * Comments setter
	 * 
	 */
	function setComments($field, $value)
	{
		$this->comments["$field"] = $value;	
	}
	
	/**
	 * New Comments
	 * 
	 */
	function newComments($comment, $request_id, $employee_id)
	{
		$db = JFactory::getDBO();
		
		$newCommentSql = "Insert into `#__so_comemnts` 
							(request_id, employee_id, comment_body) 
							values
							($request_id, $employee_id, $comment)";
		
		$db->setQuery($newCommentSql);
		
		try {
			$db->query;
			return $db->insertid();
		} catch (Exception $e) {
			return $e;
		}
	}
	
	/**
	 * Comment Details
	 * 
	 * Assuming everytime multiple entries of comments are selected on one request.
	 * 
	 */
	function commentDetails($request_id)
	{
		$db = JFactory::getDBO();
		
		$commentsDetailSql = "Select * from `#__so_comments` where request_id = $request_id";
		
		$db->setQuery($commentsDetailSql);
		
		try {
			$db->query();
			return $db->loadAssoc();
		} catch (Exception $e) {
			return $e;
		}
		
	}
}
