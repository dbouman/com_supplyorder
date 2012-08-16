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
		
		// Get ordering variables for sorting
		$filter_order = JRequest::getCmd('filter_order');
		$filter_order_dir = JRequest::getCmd('filter_order_dir');
		
		$this->setState('filter_order', $filter_order);
		$this->setState('filter_order_dir', $filter_order_dir);
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
	 * Get status details for specific request
	 * 
	 * @param int request id
	 * @return array of status details
	 */
	function getStatusDetails($request_id)
	{
		$db = JFactory::getDBO();
		
		$query = "SELECT r.request_status_id, r.employee_id, r.approval_level_required, rs.approval_level, 
					a.employee_id as account_owner_id, d.employee_id as dept_head_id
					FROM #__so_requests r
					INNER JOIN #__so_request_status rs ON r.request_status_id = rs.request_status_id
					INNER JOIN #__so_accounts a	ON r.account_id = a.account_id
					INNER JOIN #__so_department_head d ON a.dept_head_id = d.dept_head_id
					WHERE r.request_id = $request_id";
		
		$db->setQuery($query);
		
		return $db->loadAssoc();
	}
	
	/**
	 * Update status id for specific request
	 *
	 * @param int request id
	 * @param int request status id
	 * @param string date column to update
	 * @return true if successfully updated
	 */
	function updateStatus($request_id, $request_status_id, $date_column)
	{
		$db = JFactory::getDBO();
		
		$query = "UPDATE `#__so_requests` 
					SET `request_status_id` = $request_status_id,
					`$date_column` = NOW()
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
		
		//Order by
		$order_by = $db->getEscaped($this->getState('filter_order'));
		//Asc or desc
		$asc_by = $db->getEscaped($this->getState('filter_order_dir'));
		if (empty($order_by)) {
			$order_by = 'request_id';
			$asc_by = 'ASC';
		}
		
		$query = "SELECT SQL_CALC_FOUND_ROWS r.request_id, r.vendor, r.item_num, r.item_desc, r.quantity, r.unit_cost, 
					r.request_cost, r.date_approved, r.date_required, r.date_submitted, r.po_number, r.date_received, 
					o.date_ordered, rs.request_status_id, rs.status_name, rs.status_desc
					FROM `#__so_requests` r
					INNER JOIN `#__so_request_status` rs ON r.request_status_id = rs.request_status_id
					LEFT JOIN `#__so_orders` o ON r.order_id = o.order_id
					WHERE r.employee_id = $employee_id
					AND r.request_status_id IN ($status_ids)
					ORDER BY $order_by $asc_by";
		
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
	function listRequestByApprover($approver_id, $status_ids='')
	{
		$db = JFactory::getDBO();
		
		// Turn status ids into comma separated list
		if (!empty($status_ids)) {
			$status_ids = implode(',', $status_ids);
			$status_ids = rtrim($status_ids, ',');
		}
		
		//Order by
		$order_by = $db->getEscaped($this->getState('filter_order'));
		$asc_by = $db->getEscaped($this->getState('filter_order_dir'));
		if (empty($order_by)) {
			$order_by = 'r.request_id';
			$asc_by = 'ASC';
		}		
		
		$query = "SELECT SQL_CALC_FOUND_ROWS r.request_id, r.vendor, r.item_num, r.item_desc, r.quantity, r.unit_cost, 
					r.request_cost, r.date_approved, r.date_required, r.date_submitted, r.po_number, r.date_received, 
					o.date_ordered, rs.request_status_id, rs.status_name, rs.status_desc
					FROM `#__so_requests` r
					INNER JOIN `#__so_request_status` rs ON r.request_id = rs.request_status_id 
					INNER JOIN #__so_accounts a	ON r.account_id = a.account_id
					INNER JOIN #__so_department_head d ON a.dept_head_id = d.dept_head_id
					LEFT JOIN `#__so_orders` o ON r.order_id = o.order_id";
		
		// If status ids is empty, assume it wants only pending requests
		if (empty($status_ids)) {
			$query .= "WHERE (a.`employee_id` = $approver_id AND r.`request_status_id` = 2)
						OR (d.`employee_id` = $approver_id AND r.`request_status_id` = 3)";
		}
		else {
			$query .= "WHERE (a.`employee_id` = $approver_id OR (d.`employee_id` = $approver_id AND r.approval_level_required > 1))
						AND r.`request_status_id` IN ($status_ids)";
		}
		
		$query .= "ORDER BY $order_by $asc_by";
		
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
		
		//Order by
		$order_by = $db->getEscaped($this->getState('filter_order'));
		//Asc or desc
		$asc_by = $db->getEscaped($this->getState('filter_order_dir'));
		if (empty($order_by)) {
			$order_by = 'r.request_id';
			$asc_by = 'ASC';
		}
		
		$query = "SELECT SQL_CALC_FOUND_ROWS r.request_id, r.vendor, r.item_num, r.item_desc, r.quantity, r.unit_cost, 
					r.request_cost, r.date_approved, r.date_required, r.date_submitted, r.po_number, r.date_received, 
					o.date_ordered, rs.request_status_id, rs.status_name, rs.status_desc
					FROM `#__so_requests` r
					INNER JOIN `#__so_request_status` rs ON r.request_status_id = rs.request_status_id
					LEFT JOIN `#__so_orders` o ON r.order_id = o.order_id
					WHERE r.`request_status_id` IN ($status_ids)
					ORDER BY $order_by $asc_by";
				
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
		
		$query = "SELECT r.request_id, r.vendor, r.item_desc, r.ship_to, r.quantity, r.request_cost, r.request_status_id,
					r.date_required, r.employee_id, os.status_name, os.status_desc, a.account_id, a.account_num, 
					a.account_name, a.employee_id as account_owner_id
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
	 * @FIXME Needs to be FIX the query. SQL error 1064
	 * Get complete details about a request
	 * 
	 * @param int request id
	 * @return indexed array of associated arrays
	 */
	function getRequestCompleteDetail($request_id)
	{
		$db = JFactory::getDBO();
		
		$query = "SELECT r.request_id, r.request_status_id, r.employee_id, r.account_id, r.approval_level_required, r.vendor, r.item_num, r.item_desc, r.color, 
						r.url, r.ship_to, r.quantity, r.unit_cost, r.unit_measure, r.request_cost, r.date_approved, r.date_required, 
						r.date_submitted, r.date_received, o.order_name, o.order_desc, o.shipping_cost, o.order_total, o.date_ordered, 
						rs.status_name,	rs.status_desc, rs.approval_level, a.account_num, a.account_name, a.employee_id as account_owner_id, d.employee_id as dept_head_id
					FROM #__so_requests r
					INNER JOIN #__so_request_status rs ON r.request_status_id = rs.request_status_id
					INNER JOIN #__so_accounts a	ON r.account_id = a.account_id
					INNER JOIN #__so_department_head d ON a.dept_head_id = d.dept_head_id
					LEFT JOIN #__so_orders o	ON r.order_id = o.order_id
					WHERE r.request_id = $request_id";
		
		$db->setQuery($query);
		
		//Acc array for the Complete Details
		$completeDetails = $db->loadAssoc();
		
		//User Model instance
		$userModel = JModel::getInstance('User', 'SupplyOrderModel');
		
		//Employee Info
		$userInfo = $userModel->getUserInfo($completeDetails["employee_id"]);
		$completeDetails["employee_name"] = $userInfo['name'];
		$completeDetails["employee_email"] = $userInfo['email'];
		
		//Account Owner Info
		$userInfo = $userModel->getUserInfo($completeDetails["account_owner_id"]);
		$completeDetails["acct_owner_name"] = $userInfo['name'];
		$completeDetails["acct_owner_email"] = $userInfo['email'];
		
		return $completeDetails;
	}
	
}



