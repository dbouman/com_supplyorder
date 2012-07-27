<?php 

/**
 * Supply Order Component for Joomla! 1.5
 * Accounts Model
 * @version 1.5.0
 * @author Howard County Library
 * @package com_supplyorder
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 **/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


jimport('joomla.application.component.model');

class SupplyOrderModelAccounts extends JModel
{
	private $accounts = array();
	
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
	 * Add field/value pairs to the $accounts array
	 * @param string $field
	 * @param string $value
	 */
	function setAccount($field, $value)
	{
		$this->accounts["$field"] = $value;	
	}
	
	
	/**
	 * Insert an account 
	 */
	function newAccount()
	{
		$db = JFactory::getDBO();
		
		$columns = "(";
		$columnValue = "(";
		foreach($this->accounts as $field => $value){
			$columns .= $field .",";
			$columnValue .= $value .","; 
		}
		
		$columns = substr($columns, 0, -1);
		$columnValue = substr($columnValue, 0, -1);
		
		$columns .= ")";
		$columnValue .= ")";
		
		$newAccountSql = "INSERT INTO `#__so_accounts` $columns Values $columnValue";
		
		$db->setQuery($newAccountSql);
		
		if (!$result = $db->query()) {
			JError::raiseError('', JText::_('SQL_ERROR'));
			return false;
		}
		
		return $db->insertid();
	}
	
	/**
	 * Update an account by account id
	 * @param int $accountId
	 */
	function updateAccount($accountId)
	{
		$db = JFactory::getDBO();
		
		$updateAccountSql = "UPDATE `#__so_accounts` SET ";
		
		foreach ($this->accounts as $field => $value)
		{
			$updateAccountSql .= $field." = ".$value.", ";
		}
		
		$updateAccountSql = substr($updateAccountSql, 0, -2);
		
		$updateAccountSql .= " WHERE `account_id` = $accountId";

		$db->setQuery($updateAccountSql);
		
		if (!$result = $db->query()) {
			JError::raiseError('', JText::_('SQL_ERROR'));
			return false;
		} 
		
		return true;
	}

	/**
	 * Delete an account by account id
	 * @param int $accountId
	 */
	function deleteAccount($accountId)
	{
		$db = JFactory::getDBO();
		
		$deleteAccount = "DELETE FROM `#__so_accounts` WHERE `account_id` = $accountId";
		$db->setQuery($deleteAccount);
		
		if (!$result = $db->query()) {
			JError::raiseError('', JText::_('SQL_ERROR'));
			return false;
		} 
		
		return true;
	}
	
	/**
	 * List of all the accounts
	 */
	function listAccounts()
	{
		$db = &JFactory::getDBO();
		
		$listAllAccounts = "SELECT account_id, employee_id, account_num, account_name 
							FROM `#__so_accounts`";
		
		$db->setQuery($listAllAccounts);
		
		// Check for empty accounts table
		if (!$result = $db->loadAssocList())
		{
			JError::raiseNotice('', JText::_('ACCOUNTS_EMPTY'));
			return false;
		}

		// Get user model
		$userModel = JModel::getInstance('User', 'SupplyOrderModel');
		
		$accounts = array();
		$i = 0;
		foreach ($result as $row) {
			$accounts[$i]['id'] = $row['account_id'];
			$accounts[$i]['name'] = $row['account_name'];
			$accounts[$i]['number'] = $row['account_num'];
			
			$userInfo = $userModel->getUserInfo($row['employee_id']);
			$accounts[$i]['owner'] = $userInfo['name'];
			$i++; 
		}
		
		return $accounts;
	}

}

