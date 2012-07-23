<?php 

/**
 * Supply Order Component for Joomla! 1.5
 * This model handles all queries to the database with regards to requests
 * @version 1.5.0
 * @author Howard County Library
 * @package com_supplyorder
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 **/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


jimport('joomla.application.component.model');

class SupplyOrderModelRequests extends JModel
{
	private $request = array();
	
	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Array setter 
	 * 
	 * @var int, string, float
	 */
	function setRequest($field, $value)
	{
		$this->request["$field"] = $value;
	}

	/**
	 * New Request insert Query
	 * 
	 * @var array
	 * @access public
	 */
	function requestInsertSql() 
	{
		$columns = "(";
		$columnValue = "(";
		foreach ($this->request as $field => $value)
		{
			$columns .= $field."'";
			$columnValue .= "\"".$value."\",";
		}
		$columns = substr($columns, 0, -1);
		$columnValue = substr($columnValue, 0, -1);
		
		$columns .= ")";
		$columnValue .= ")";
		
		$insertSql = "insert into `#__so_order` $columns values $columnValue";
		
		$db->setQuery($insertSql);
		
		try
		{
			$db->query();
			$requestId = $db->insertid();
			return $requestId;//return true;
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	
	/**
	 * Request update query
	 * 
	 * @var int record id
	 * @access public
	 * @return boolean 
	 * 
	 */
	function updateRequest($id)
	{
		$fieldValuePair = "";
		foreach ($this->request as $field => $value)
		{
			$fieldValuePair .= $field. "= \"".$value."\",";
		}
		
		$fieldValuePair = substr($fieldValuePair, 0, -1);
		$fieldValuePair = "update `#__so_order` set $fieldValuePair where order_id = $id";
		
		$db->setQuery($fieldValuePair);
		try
		{
			$db->query();
			$requestId = $db->insertid();
			return $requestId;//return true;
		}
		catch (Exception $e)
		{
			return false;
		}
		
	}
	
	/**
	 * Request Delete query
	 * 
	 * @var int record id
	 * @access public
	 * @return boolean
	 *
	 */
	function deleteRequest($id)
	{
		$deleteQuery = "delete from `#__so_order` where order_id = $id";
	
		$db->setQuery($deleteQuery);
		try
		{
			$db->query();
			return $db->loadResult();//return true;
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	
	/**
	 * Get Status Id for the request
	 * 
	 * @var int request id
	 * @access public
	 * @return string
	 */
	function getStatusId($id)
	{
		$statusIdQuery = "select `order_status_id` from `#__so_order` where order_id = $id";
		$db->setQuery($statusIdQuery);
		try
		{
			return $db->loadResult();//return true;
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	
	/**
	 * Update Status Id for the request
	 *
	 * @var int list or single request id
	 * @access public
	 * @return string
	 */
	function updateStatus($id, $statusId)
	{
		
	}
	
	/**
	 * List request by owner
	 * 
	 * @var string email
	 * @access public
	 * @return array
	 *  
	 */
	function listRequestByOwner($employeId)
	{
		
	}
	
}