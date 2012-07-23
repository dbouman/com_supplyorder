<?php 

/**
 * Supply Order Component for Joomla! 1.5
 * @version 1.5.0
 * @author Howard County Library
 * @package com_supplyorder
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 **/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );



// Require the base controller
require_once (JPATH_COMPONENT.DS.'controller.php');

$controller = new SupplyOrderController( );

// Perform the Request task
$controller->execute( JRequest::getCmd('task'));
$controller->redirect();
