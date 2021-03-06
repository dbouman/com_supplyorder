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

// Set the table directory
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_supplyorder'.DS.'tables');

class SupplyOrderModelUser extends JModel
{
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
	 * If id is empty, get information on currently logged in user.
	 * If id is not empty, get information on the user based on user id
	 * @param int $id
	 * @return array of user information
	 */
	function getUserInfo($id = 0)
	{
		if ($id == 0) // No id passed in, get current user info
			$user =& JFactory::getUser();
		else // Get user info of id passed in
			$user =& JFactory::getUser($id);
		
		// Check if user exists
		if ($user->id == 0) {
			$this->setError(JText::_('USER_ERROR'));
		}
		
		$user_info = array();
		
		if (!$user->guest) {
			$user_id = $user->id;
			$user_info['id'] = $user_id;
			$user_info['username'] = $user->username;
			$user_info['name'] = $user->name;
			$user_info['email'] = $user->email;
			
			// Get user position from associated contact details
			$row =& JTable::getInstance('contact', 'Table');
			$row->load( $user_id );
			$title = $row->con_position;
			
			$user_info['role_id'] = $this->getRoleId($title);
		}

		return $user_info;
	}
	
	function getUserEmail($id = 0) {
		$user_info = $this->getUserInfo($id);
		
		return $user_info['email'];
	}

	/**
	 * Map a title to a role id. If no match is found, assume that the role id is 1 (lowest). 
	 * @param string $title
	 * @return role id, returns 1 if no title match found
	 */
	function getRoleId ($title) 
	{
		$db = &JFactory::getDBO();
		
		$query = "SELECT role_id FROM `#__so_title_roles`
					WHERE title = '" . $title . "'";
		
		$db->setQuery($query);
		
		if (!$role_id = $db->loadResult())
		{
			// Title not found, assume lowest role id
			$role_id = 1;
		}
		
		return $role_id;
	}

}

