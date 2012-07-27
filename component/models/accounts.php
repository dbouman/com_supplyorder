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
	 * set $accounts array
	 * 
	 * @var string, string
	 */
	function setAccount($field, $value)
	{
		$this->accounts["$field"] = $value;	
	}
	
	
	/**
	 * New account
	 * Create new account 
	 * 
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
		
		$newAccountSql = "Insert Into `#__so_accounts` $columns Values $columnValue";
		
		$db->setQuery($newAccountSql);
		
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
	 * Update Account
	 * 
	 */
	function updateAccount($accountId)
	{
		$db = JFactory::getDBO();
		
		$fieldValuePair = "";
		
		foreach ($this->accounts as $field => $value)
		{
			$fieldValuePair .= $field." = ".$value.", ";
		}
		
		$fieldValuePair = substr($fieldValuePair, 0, -1);
		
		$updateAccountSql = "Update `#__so_accounts` set $fieldValuePair where `account_id` = $accountId";

		$db->setQuery($updateAccountSql);
		
		try {
			$db->query();
			return $db->LoadResults();
		} catch (Exception $e) {
		}
	}

	/**
	 * Delete Account
	 * 
	 */
	function deleteAccount($accountId)
	{
		$db = JFactory::getDBO();
		
		$deleteAccount = "Delete from `#__so_accounts` where `account_id` = $accountId";
		$db->setQuery($deleteAccount);
		
		try {
			$db->query();
			return true;
		} catch (Exception $e) {
			return $e;
		}
	}
	
	/**
	 * list of Accounts
	 * List of the all the accounts from DB for dropdown.
	 * 
	 */
	function listAccount()
	{
		$db = &JFactory::getDBO();
		
		$listAllAccounts = "select account_id, employee_id, account_num, account_name 
							from `#__so_accounts`";
		
		$db->setQuery($listAllAccounts);
		
		// Check for database error
		if (!$result = $db->loadAssocList())
		{
			$this->setError(JText::_('DATABASE_ERROR'));
			return false;
		}

		// Get user model
		$userModel = JModel::getInstance(‘User’, ‘SupplyOrderModel’);
		
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

