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


jimport('joomla.application.component.helper');
require_once(JPATH_COMPONENT.DS.'controller.php');
// Create the controller
$controller = new SupplyOrderController();
// Perform the Request task
$controller->execute(JRequest::getVar('task', null, 'default', 'cmd'));
// Redirect if set by the controller
$controller->redirect();