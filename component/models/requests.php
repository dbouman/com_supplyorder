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
		$db = JFactory::getDBO();
		
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
		
		$insertSql = "insert into `#__so_request` $columns values $columnValue";
		
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
		$db = JFactory::getDBO();
		
		$fieldValuePair = "";
		foreach ($this->request as $field => $value)
		{
			$fieldValuePair .= $field. "= \"".$value."\",";
		}
		
		$fieldValuePair = substr($fieldValuePair, 0, -1);
		$fieldValuePair = "update `#__so_request` set $fieldValuePair where request_id = $id";
		
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
		$db = JFactory::getDBO();
		
		$deleteQuery = "delete from `#__so_request` where request_id = $id";
	
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
		$db = JFactory::getDBO();
		
		$statusIdQuery = "select  from r `#__so_request`, rs `#__so_request_status` 
							where (r.request_id = $id) AND (r.request_status_id = rs.request_status_id)";
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
		$db = JFactory::getDBO();
		
		$updateStatusQuery = "Update `#__so_request` set `request_status_id` = $statusId where request_id = $id";
		$db->setQuery($updateStatusQuery);
		try
		{
			return $db->loadResult();//return true;
		}
		catch (Exception $e)
		{
			return $e;
		}
	}
	
	/**
	 * List request by Owner
	 * 
	 * @var int employee id
	 * @access public
	 * @return array
	 *  
	 */
	function listRequestByOwner($employeId)
	{
		$db = JFactory::getDBO();
		
		$listRequestByOwnerQuery = "Select * from `#__so_request` where `employee_id` = $employeId";
		$db->setQuery($listRequestByOwnerQuery);
		
		try {
			return $db->loadAssocList();
		} catch (Exception $e) {
			return $e;
		}
	}
	
	/**
	 * List request by Approver
	 * 
	 * @var int approver id
	 * @access public
	 * @return array 
	 */
	function listRequestByApprover($approverId)
	{
		$db = JFactory::getDBO();
		
		$listRequestByApproverQuery = "Select * from `#__so_request` where `account_id` = $approverId";
		
		$db->setQuery($listRequestByApproverQuery);
		
		try {
			return $db->loadAssocList();
		} catch (Exception $e) {
			return $e;
		}
	}
	
	/**
	 * List request by Status
	 * 
	 * @var int status id
	 * @access public
	 * @return array
	 */
	function listRequestByStatus($statusId)
	{
		$db = JFactory::getDBO();
		
		$listRequestByStatusQuery = "Select * from `#__so_request` where `request_status_id` = $statusId";
		
		$db->setQuery($listRequestByStatusQuery);
		
		try {
			return $db->loadAssocList();
		} catch (Exception $e) {
			return $e;
		}
	}
	
	/**
	 * Request received update 
	 * 
	 * @var boolean, int
	 * @access public
	 * @return boolean
	 */
	function requestReceived($newStatusId, $requestId)
	{
		$db = JFactory::getDBO();
		
		$requestReceivedQuery = "Update `#__so_request` set `request_status_id` = $newStatusId where `request_id` = $requestId";
		$db->setQuery($requestReceivedQuery);
		
		try {
			$db->query();
			return true;
		} catch (Exception $e) {
			return $e;
		}
	}
	
	/**
	 * getter Request Breif Details
	 * 
	 * @var int
	 * @return array
	 */
	function getRequestBriefDetail($requestId)
	{
		$db = JFactory::getDBO();
		
		//Select Query 
		$requestBriefDetailQuery = "Select o.order_id, o.vendor, o.item_desc, o.ship_to, o.quantity, o.order_total, 
										o.date_required, os.status_name, a.account_num, e.first_name, e.last_name, 
										e.email 
										from o order, os order_status, e employee, a accounts 
										where 
										o.order_status_id = os.order_status_id
										AND o.employee_id = o.employee_id
										AND o.account_id = a.account_id";
		
		$db->setQuery($requestBriefDetailQuery);
		
		try {
			$db->query();
			return $db->loadAssoc();
		} catch (Exception $e) {
			return $e;
		}
		
	}
	
	/**
	 * Request Detail View
	 * This detail view is for complete information.
	 *
	 * @var int
	 * @return array
	 */
	function getRequestDetail($requestId)
	{
		$db = JFactory::getDBO();
		
		//Select query
		$requestDetailQuery = "Select o.order_id, o.vendor, o.item_desc, o.ship_to, o.quantity, o.order_total,
		o.date_required, os.status_name, a.account_num, e.first_name, e.last_name,
		e.email
		from o order, os order_status, e employee, a accounts
		where
		o.order_status_id = os.order_status_id
		AND o.employee_id = o.employee_id
		AND o.account_id = a.account_id";
	
		$db->setQuery($requestDetailQuery);
	
		try {
			$db->query();
			return $db->loadAssoc();
		} catch (Exception $e) {
			return $e;
		}
	
	}
	
}



