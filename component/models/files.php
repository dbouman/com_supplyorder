<?php 

/**
 * Supply Order Component for Joomla! 1.5
 * Files Model
 * @version 1.5.0
 * @author Howard County Library
 * @package com_supplyorder
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 **/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


jimport('joomla.application.component.model');

class SupplyOrderModelFiles extends JModel
{
	private $fiels = array();
	
	/**
	 * Construct
	 * 
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	
}