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
		
		$newAccountSql = "Insert Into #__so_accounts $columns Values $columnValue";
		
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
		$fieldValuePair = "";
		
		foreach ($this->accounts as $field => $value)
		{
			$fieldValuePair .= $field." = ".$value.", ";
		}
		
		$fieldValuePair = substr($fieldValuePair, 0, -1);
		
		$updateAccountSql = "Update #__so_accounts set $fieldValuePair where account_id = $accountId";

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
		$deleteAccount = "Delete from #__so_accounts where account_id = $accountId";
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
	 * 
	 */
	function listAccount()
	{
		$listAllAccounts = "Select * from #__so_accounts";
		$db->setQuery($listAllAccounts);
		try {
			return $db->loadAssocList();
		} catch (Exception $e) {
			return $e;
		}
	}

}

