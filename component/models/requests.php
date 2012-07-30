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
	 * Items total
	 * @var integer
	 */
	var $_total = null;
	
	/**
	 * Pagination object
	 * @var object
	 */
	var $_pagination = null;
	
	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	function __construct()
	{
	 	parent::__construct();
	 
		$mainframe = JFactory::getApplication();
	 
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
	 
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
	 
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}
	
	function getPagination()
	{
		// Load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->_total, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
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
	 * List requests by owner and status ids
	 * 
	 * @param int employee id
	 * @param array of status ids
	 * @return indexed array of associated arrays
	 */
	function listRequestByOwner($employee_id, $status_ids)
	{
		$db = JFactory::getDBO();
		
		// Turn status ids into comma separated list
		$status_ids = implode(',', $status_ids);
		$status_ids = rtrim($status_ids, ',');
		
		$query = "SELECT SQL_CALC_FOUND_ROWS * FROM `#__so_requests` 
					WHERE `employee_id` = $employee_id
					AND `request_status_id` IN ($status_ids)";
		$db->setQuery($query, $this->getState('limitstart'), $this->getState('limit'));
		$requests = $db->loadAssocList();
		
		// Get total for pagination
		$db->setQuery('SELECT FOUND_ROWS();');
		$this->_total = $db->loadResult();
		
		return $requests;
	}
	
	/**
	 * List requests by approver and status ids
	 * 
	 * @param int approver id
	 * @param array status ids
	 * @return indexed array of associated arrays
	 */
	function listRequestByApprover($approver_id, $status_ids)
	{
		$db = JFactory::getDBO();
		
		// Turn status ids into comma separated list
		$status_ids = implode(',', $status_ids);
		$status_ids = rtrim($status_ids, ',');
		
		$query = "SELECT SQL_CALC_FOUND_ROWS * FROM `#__so_requests` 
					WHERE `account_id` = $approver_id
					AND `request_status_id` IN ($status_ids)";
		$db->setQuery($query, $this->getState('limitstart'), $this->getState('limit'));
		$requests = $db->loadAssocList();

		// Get total for pagination
		$db->setQuery('SELECT FOUND_ROWS();');
		$this->_total = $db->loadResult();
		
		return $requests;
	}
	
	/**
	 * List requests by status ids
	 * 
	 * @param array of status ids
	 * @return indexed array of associated arrays
	 */
	function listRequestByStatus($status_ids)
	{
		$db = JFactory::getDBO();
		
		// Turn status ids into comma separated list
		$status_ids = implode(',', $status_ids);
		$status_ids = rtrim($status_ids, ',');
		
		$query = "SELECT SQL_CALC_FOUND_ROWS * FROM `#__so_requests` WHERE `request_status_id` IN ($status_ids)";
		$db->setQuery($query, $this->getState('limitstart'), $this->getState('limit'));
		$requests = $db->loadAssocList();
		
		// Get total for pagination
		$db->setQuery('SELECT FOUND_ROWS();');
		$this->_total = $db->loadResult();
		
		return $requests;
	}
	
	
	/**
	 * Brief Details 
	 * 
	 * @param int request id
	 * @return indexed array of associated arrays
	 */
	function getRequestBriefDetail($request_id)
	{
		$db = JFactory::getDBO();
		
		$userModel = JModel::getInstance('User', 'SupplyOrderModel');
		
		$query = "SELECT r.request_id, r.vendor, r.item_desc, r.ship_to, r.quantity, r.request_cost, 
					r.date_required, r.employee_id, os.status_name, os.status_desc, a.account_num, a.account_name
					FROM `#__so_requests` r, `#__so_request_status` os, `#__so_accounts` a 
					WHERE r.request_id = $request_id 
					AND r.request_status_id = os.request_status_id
					AND r.account_id = a.account_id" ;
		$db->setQuery($query);
		
		//Acc array for the Brief Details
		$briefDetails = $db->loadAssoc();
		
		//Employee info
		$userInfo = $userModel->getUserInfo($briefDetails['employee_id']);
		
		//Add employee value
		$briefDetails["employee_name"] = $userInfo['name'];
		$briefDetails["employee_email"] = $userInfo['email'];
		
		return $briefDetails;
	}
	
	/**
	 * @FIXME Needs to be changed for JOIN query
	 * Get complete details about a request
	 * 
	 * @param int request id
	 * @return indexed array of associated arrays
	 */
	function getRequestCompleteDetail($request_id)
	{
		$db = JFactory::getDBO();
		
		$userModel = JModel::getInstance('User', 'SuppyOrderModel');
		
		$query = "SELECT r.request_id, r.vendor, r.item_desc, r.ship_to, r.quantity, r.request_cost, 
					r.date_required, r.employee_id, os.status_name, os.status_desc, a.account_num, a.account_name
					FROM r requests, os order_status, a accounts 
					WHERE r.request_id = $request_id 
					AND r.request_status_id = os.request_status_id
					AND r.account_id = a.account_id" ;
		$db->setQuery($query);
	
		//Acc array for the Complete Details
		$completeDetails = $db->loadAssoc();
		
		//Employee info
		$userInfo = $userModel->getUserInfo($completeDetails['employee_id']);
		
		//Add employee value
		$completeDetails["employee_name"] = $userInfo['name'];
		$completeDetails["employee_email"] = $userInfo['email'];
		
		return $completeDetails;
	}
	
}



