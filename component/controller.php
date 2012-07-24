<?php 

/**
 * Supply Order Component for Joomla! 1.5
 * Component Controller
 * @version 1.5.0
 * @author Howard County Library
 * @package com_supplyorder
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 **/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


jimport( 'joomla.application.component.controller' );

/**
 * Supplyorder Component Controller
 *
 * @package com_supplyorder
 */

class SupplyOrderController extends JController
{
	/**
	 * Method to show a supplyorder view
	 */
	function display() {
		// Set a default view if none exists
		if ( ! JRequest::getCmd( 'view' ) ) {
			JRequest::setVar('view', 'requests' );
		}
		
		parent::display();
	}
	
	/**
	 * Save a request
	 */
	function request_save() {
		$model =& $this->getModel('requests');
		
		$model->setRequest("order_status_id", '0');
		$model->setRequest("account_id", $_POST['account_id']);
		//$model->setRequest("order_name", $_POST['']);
		//$model->setRequest("order_desc", $_POST['']);
		$model->setRequest("vendor", $_POST['vendor']);
		//$model->setRequest("item_name", $_POST['']);
		$model->setRequest("item_num", $_POST['item_num']);
		$model->setRequest("item_desc", $_POST['item_desc']);
		$model->setRequest("color", $_POST['color']);
		$model->setRequest("url", $_POST['url']);
		//$model->setRequest("requester_name", $_POST['']);
		//$model->setRequest("requester_email", $_POST['']);
		//$model->setRequest("requester_dept", $_POST['']);
		$model->setRequest("ship_to", $_POST['ship_to']);
		$model->setRequest("quantity", $_POST['quantity']);
		$model->setRequest("unit_cost", $_POST['unit_cost']);
		$order_cost = $_POST['quantity'] * $_POST['unit_cost'];
		$model->setRequest("order_cost", $order_cost);
		//$model->setRequest("shipping_cost", $_POST['']);
		//$model->setRequest("order_total", $_POST['']);
		$model->setRequest("date_ordered", date('Y-m-d H:i:s',strtotime('now')));
		$model->setRequest("date_required", $_POST['date_required']);
		
		$model->setRequest("approval_level_required", get_approval_level($order_cost));
		
		if ($model->requestInsertSql()) {
			$msg	= JText::_( 'Your request has been saved. Your request has not yet been ordered, please visit your saved orders to submit the order for purchasing.' );
		} else {
			//$msg	= JText::_( 'Error saving your settings.' );
			$msg	= $model->getError();
		}
		
		// get the redirect
		$return = JURI::base();
		
		$this->setRedirect( $return, $msg );
	}
	
	private function get_approval_level ($order_cost) {
		$approval_level = 1;
		
		if ($order_cost > 3000) {
			$approval_level = 3;
		}
		else if ($order_cost > 1000) {
			$approval_level = 2;
		}
		
		return $approval_level;
	}
}