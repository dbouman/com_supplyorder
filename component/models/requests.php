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
	 * Add field/value pairs to the $request array
	 * @param string $field
	 * @param string $value
	 */
	function setRequest($field, $value)
	{
		$this->request["$field"] = $value;
	}

	/**
	 * Insert a request
	 * 
	 * @return int request id
	 */
	function insertRequest() 
	{
		$db = JFactory::getDBO();
		
		$columns = "(";
		$column_values = "(";
		foreach ($this->request as $field => $value)
		{
			$columns .= $field.",";
			$column_values .= "\"".$value."\",";
		}
		$columns = substr($columns, 0, -1);
		$column_values = substr($column_values, 0, -1);
		
		$columns .= ")";
		$column_values .= ")";
		
		$query = "INSERT INTO `#__so_requests` $columns VALUE $column_values";
		
		$db->setQuery($query);
		
		if (!$result = $db->query()) {
			JError::raiseError('', JText::_('SQL_ERROR'));
			return false;
		}
		
		return $db->insertid();
	}
	
	/**
	 * Update a request by request id
	 * 
	 * @param int request id
	 * @return true if request was successfully updated 
	 */
	function updateRequest($request_id)
	{
		$db = JFactory::getDBO();
		
		$query = "UPDATE `#__so_requests` SET ";
		
		foreach ($this->request as $field => $value)
		{
			$query .= $field. "= \"".$value."\", ";
		}
		
		$query = substr($query, 0, -2);
		$query .= " WHERE request_id = $request_id";
		
		$db->setQuery($query);
		
		if (!$result = $db->query()) {
			JError::raiseError('', JText::_('SQL_ERROR'));
			return false;
		}
		
		return true;
	}
	
	/**
	 * Delete a request by request id
	 * 
	 * @param int request id
	 * @return true if request was successfully deleted 
	 */
	function deleteRequest($request_id)
	{
		$db = JFactory::getDBO();
		
		$query = "DELETE FROM `#__so_requests` WHERE request_id = $request_id";
	
		$db->setQuery($query);
		
		if (!$result = $db->query()) {
			JError::raiseError('', JText::_('SQL_ERROR'));
			return false;
		} 
		
		return true;
	}
	
	/**
	 * Get details of a specific status
	 * 
	 * @param int request status id
	 * @return array of status details
	 */
	function getStatus($request_status_id)
	{
		$db = JFactory::getDBO();
		
		$query = "SELECT *
					FROM `#__so_request_status` 
					WHERE request_status_id = $request_status_id)";
		
		$db->setQuery($query);
		
		return $db->loadResult();
	}
	
	/**
	 * Update status id for specific request
	 *
	 * @param int request id
	 * @param int request status id
	 * @return true if successfully updated
	 */
	function updateStatus($request_id, $request_status_id)
	{
		$db = JFactory::getDBO();
		
		$query = "UPDATE `#__so_requests` 
					SET `request_status_id` = $request_status_id 
					WHERE request_id = $request_id";
		
		$db->setQuery($query);
		
		if (!$result = $db->query()) {
			JError::raiseError('', JText::_('SQL_ERROR'));
			return false;
		}
		
		return true;
	}
	
	/**
	 * List requests by owner
	 * 
	 * @param int employee id
	 * @return indexed array of associated arrays
	 */
	function listRequestByOwner($employee_id)
	{
		$db = JFactory::getDBO();
		
		$query = "SELECT * FROM `#__so_requests` WHERE `employee_id` = $employee_id";
		$db->setQuery($query);
		
		return $db->loadAssocList();
	}
	
	/**
	 * List requests by approver
	 * 
	 * @param int approver id
	 * @return indexed array of associated arrays
	 */
	function listRequestByApprover($approver_id)
	{
		$db = JFactory::getDBO();
		
		$query = "SELECT * FROM `#__so_requests` WHERE `account_id` = $approver_id";
		
		$db->setQuery($query);
		
		return $db->loadAssocList();
		
	}
	
	/**
	 * List requests by status id
	 * 
	 * @param int status id
	 * @return indexed array of associated arrays
	 */
	function listRequestByStatus($status_id)
	{
		$db = JFactory::getDBO();
		
		$query = "SELECT * FROM `#__so_requests` WHERE `request_status_id` = $status_id";
		
		$db->setQuery($query);
		
		return $db->loadAssocList();
	}
	
	/**
	 * @TODO Why do we need this, when we have a way to update status already? - Danny
	 * Update a request when it has been received 
	 * 
	 * @param int new status id
	 * @param int request id
	 * @return true if request successfully updated
	 */
	function requestReceived($new_status_id, $request_id)
	{
		$db = JFactory::getDBO();
		
		$query = "UPDATE `#__so_requests` 
					SET `request_status_id` = $new_status_id 
					WHERE `request_id` = $request_id";
		
		$db->setQuery($query);
		
		if (!$result = $db->query()) {
			JError::raiseError('', JText::_('SQL_ERROR'));
			return false;
		} 
		
		return true;
	}
	
	/**
	 * @FIXME Needs to be updated for new database schema, and it needs to use request_id
	 * Get brief details about a request
	 * 
	 * @param int request id
	 * @return indexed array of associated arrays
	 */
	function getRequestBriefDetail($request_id)
	{
		$db = JFactory::getDBO();
		
		$query = "SELECT o.order_id, o.vendor, o.item_desc, o.ship_to, o.quantity, o.order_total, 
					o.date_required, os.status_name, a.account_num, e.first_name, e.last_name, e.email 
					FROM o order, os order_status, e employee, a accounts 
					WHERE o.order_status_id = os.order_status_id
					AND o.employee_id = o.employee_id
					AND o.account_id = a.account_id";
		
		$db->setQuery($query);
		
		return $db->loadAssoc();
	}
	
	/**
	 * @FIXME Needs to be updated for new database schema, and it needs to use request_id
	 * Get complete details about a request
	 * 
	 * @param int request id
	 * @return indexed array of associated arrays
	 */
	function getRequestDetail($request_id)
	{
		$db = JFactory::getDBO();
		
		$query = "SELECT o.order_id, o.vendor, o.item_desc, o.ship_to, o.quantity, o.order_total,
					o.date_required, os.status_name, a.account_num, e.first_name, e.last_name, e.email
					FROM o order, os order_status, e employee, a accounts
					WHERE o.order_status_id = os.order_status_id
					AND o.employee_id = o.employee_id
					AND o.account_id = a.account_id";
	
		$db->setQuery($query);
	
		return $db->loadAssoc();
	}
	
}



