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
		if ( !JRequest::getCmd( 'view' ) ) {
			JRequest::setVar('view', 'requests' );
		}
		
		$document =& JFactory::getDocument();
		
		$viewType	= $document->getType();
		$viewName	= JRequest::getCmd( 'view', $this->getName() );
		$viewLayout	= JRequest::getCmd( 'layout', 'default' );
		
		$view =& $this->getView( $viewName, $viewType, '', array( 'base_path'=>$this->_basePath));
		
		// Set the default model as requests
		$requestsModel =& $this->getModel ( 'requests' ); // get first model
	    $view->setModel( $requestsModel, true );  // true is for the default model  
	    
	    // Set accounts model
	    $accountsModel =& $this->getModel ( 'accounts' ); // get second model     
	    $view->setModel( $accountsModel );  
	    
	    // Set user model
	    $userModel =& $this->getModel ( 'user' ); // get second model
	    $view->setModel( $userModel );

	    // Set the layout
	    $view->setLayout($viewLayout);
	    
	    $view->display();
	}
	
	/**
	 * Save a request
	 */
	function save_request() {
		$model =& $this->getModel('requests');
		$userModel =& $this->getModel ( 'user' );
		
		$userInfo = $userModel->getUserInfo($row['employee_id']);
		$employee_id = $userInfo['id'];
		
		$model->setRequest("request_status_id", '1');
		$model->setRequest("employee_id", '1'); // FIX ME
		//$model->setRequest("employee_id", $employee_id);
		$model->setRequest("account_id", JRequest::getVar('account_id'));
		$model->setRequest("vendor", JRequest::getVar('vendor'));
		$model->setRequest("item_num", JRequest::getVar('item_num'));
		$model->setRequest("item_desc", JRequest::getVar('item_desc'));
		$model->setRequest("color", JRequest::getVar('color'));
		$model->setRequest("url", JRequest::getVar('url'));
		$model->setRequest("ship_to", JRequest::getVar('ship_to'));
		$model->setRequest("quantity", JRequest::getVar('quantity'));
		$model->setRequest("unit_cost", JRequest::getVar('unit_cost'));
		$model->setRequest("unit_measure", JRequest::getVar('unit_measure'));
		
		$order_cost = JRequest::getVar('quantity') * JRequest::getVar('unit_cost');
		$model->setRequest("request_cost", $order_cost);
		
		$model->setRequest("date_required", JRequest::getVar('date_required'));
		$model->setRequest("date_submitted", date('Y-m-d H:i:s',strtotime('now')));
		
		$model->setRequest("approval_level_required", $this->get_approval_level($order_cost));
		
		if ($model->insertRequest()) {
			$msg	= JText::_( 'Your request has been saved. Your request has not yet been ordered, please visit your saved orders to submit the order for purchasing.' );
		} else {
			$msg	= JText::_( 'Error saving your request.' );
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