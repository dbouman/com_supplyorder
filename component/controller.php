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
		
		// Check if user is logged in
		$this->check_user_logged_in();
		
		$document =& JFactory::getDocument();
		
		$viewType	= $document->getType();
		$viewName	= JRequest::getCmd( 'view', $this->getName() );
		$viewLayout	= JRequest::getCmd( 'layout', 'default' );
		
		$view =& $this->getView( $viewName, $viewType, '', array( 'base_path'=>$this->_basePath));
		
		// Set the default model as requests
		$requestsModel =& $this->getModel ( 'requests' ); 
	    $view->setModel( $requestsModel, true );  // true is for the default model  
	    
	    // Set accounts model
	    $accountsModel =& $this->getModel ( 'accounts' );   
	    $view->setModel( $accountsModel );  
	    
	    // Set user model
	    $userModel =& $this->getModel ( 'user' );
	    $view->setModel( $userModel );
	    
	    // Set comments model
	    $commentsModel =& $this->getModel ( 'comments' ); 
	    $view->setModel( $commentsModel );
	    
	    // Set files model
	    $filesModel =& $this->getModel ( 'files' ); 
	    $view->setModel( $filesModel );

	    // Set the layout
	    $view->setLayout($viewLayout);
	    
	    $view->display();
	}
	
	/**
	 * Save a request
	 */
	function save_request() {
		$mainframe =& JFactory::getApplication();
		$model =& $this->getModel('requests');
		$userModel =& $this->getModel ( 'user' );
		$commentsModel =& $this->getModel ( 'comments' );
		$filesModel =& $this->getModel ( 'files' );
		
		// Clean all POST variables
		JRequest::_cleanArray( $_POST );
		
		$user =& JFactory::getUser();
		$employee_id = $user->id;
		
		$model->setRequest("request_status_id", '1');
		$model->setRequest("employee_id", $employee_id);
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
		
		$model->setRequest("date_required", date('Y-m-d H:i:s',strtotime(JRequest::getVar('date_required'))));
		$model->setRequest("date_submitted", date('Y-m-d H:i:s',strtotime('now')));
		
		$model->setRequest("approval_level_required", $this->get_approval_level($order_cost));
		
		if ($request_id = $model->insertRequest()) {
			$msg	= JText::_( 'Your request has been saved. Your request has not yet been ordered, please visit your saved orders to submit the order for purchasing.' );
		} else {
			$msg	= JText::_( 'Error saving your request.' );
		}
		
		// get the redirect, current page including query string
		$uri = JURI::getInstance();
		
		// Add files if they exist
		$files = JRequest::getVar('files', null, 'files', 'array');
		if (!empty($files)) {
			if(!class_exists('SupplyOrderFileUploads')) require('components'.DS.'com_supplyorder'.DS.'helpers'.DS.'fileuploads.php');
			
			$files = SupplyOrderFileUploads::initFilesArray($files);
			
			foreach ($files as $file) {
				$error = SupplyOrderFileUploads::checkFileForError($file);
				if ($error == JText::_( 'ERROR NO FILE' )) {
					// skip file not found errors
					continue;
				}
				else if (!empty($error)) {
					// Get all form data and store in session
					$mainframe->setUserState('com_supplyorder.edit.request.data', JRequest::get($_POST));
					
					// Delete previously entered request and files since there was an error with current file
					$filesModel->deleteFiles($request_id);
					$model->deleteRequest($request_id);
					
					$this->setRedirect( $uri->toString(), $error, 'error' );
					return;
				}
				
				$filesModel->insertFile($file, $request_id, $employee_id);
			}
		}
		
		// Add comments if they exist
		$comments = JRequest::getVar('comments');
		if (!empty($comments)) {
			$commentsModel->insertComment($comments, $request_id, $employee_id);
		}
		
		$this->setRedirect( $uri->toString(), $msg );
	}
	
	// Similar to approve_request function, but only handles submitting saved requests
	public function submit_request ( ) {
		$mainframe =& JFactory::getApplication();
		$model =& $this->getModel('requests');
		$userModel =& $this->getModel ( 'user' );
		
		// Clean all POST variables
		JRequest::_cleanArray( $_POST );
		
		// Get helper for emails
		if(!class_exists('SupplyOrderNotifications')) require('components'.DS.'com_supplyorder'.DS.'helpers'.DS.'notifications.php');
		
		$requests_id_list = JRequest::getVar('requests');
		foreach ($requests_id_list as $request_id) {
			$status_details = $model->getStatusDetails($request_id);
			
			$to_email = $userModel->getUserEmail($status_details['account_owner_id']);
			$new_status_id = 2;
			$subject =  JText::_( 'Request has been submitted and is awaiting approval' );
			
			// Update status in database
			$model->updateStatus($request_id,$new_status_id,'date_submitted');
				
			// Send email
			$notifications = new SupplyOrderNotifications();
			$notifications->emailRequestDetails($to_email, $subject, $request_id);
		}
		
		// get the redirect, current page including query string
		$uri = JURI::getInstance();
		if (count($requests_id_list) > 1)
			$msg	= JText::_( 'Your requests have been submitted and are awaiting approval.' );
		else if (count($requests_id_list) == 1)
			$msg	= JText::_( 'Your request has been submitted and is awaiting approval.' );
		$this->setRedirect( $uri->toString(), $msg );
	}
	
	// Updates request status on approval
	public function approve_request ( ) {
		$mainframe =& JFactory::getApplication();
		$model =& $this->getModel('requests');
		$userModel =& $this->getModel ( 'user' );
		
		// Get parameters
		$params = &JComponentHelper::getParams( 'com_supplyorder' );
		$accounting_email = $params->get('accounting_email');
		$ceo_email = $params->get('ceo_email');
		
		// Clean all POST variables
		JRequest::_cleanArray( $_POST );
		
		// Get helper for emails
		if(!class_exists('SupplyOrderNotifications')) require('components'.DS.'com_supplyorder'.DS.'helpers'.DS.'notifications.php');
		
		$requests_id_list = JRequest::getVar('requests');
		foreach ($requests_id_list as $request_id){
			$status_details = $model->getStatusDetails($request_id); 
			$curr_status_id = $status_details['request_status_id'];
			$approval_level_required = $status_details['approval_level_required'];
			$approval_level = $status_details['approval_level'];
			
			if ($curr_status_id == 2) { // Tier 1 request approved 
				if ($this->is_approved($approval_level, $approval_level_required)) {
					// Approval level met, go straight to accounting
					$to_email = $accounting_email; 
					$new_status_id = 5;
					$subject =  JText::_( 'Request has been approved and is awaiting purchase' );
				}
				else {
					// Needs next level of approval
					$to_email = $userModel->getUserEmail($status_details['dept_head_id']);
					$new_status_id = 3;
					$subject =  JText::_( 'A new request is awaiting your approval' );
				}
			}
			else if ($curr_status_id == 3) { // Tier 2 request approved
				if ($this->is_approved($approval_level, $approval_level_required)) {
					// Approval level met, go straight to accounting
					$to_email = $accounting_email;
					$new_status_id = 5;
					$subject =  JText::_( 'Request has been approved and is awaiting purchase' );
				}
				else {
					// Needs next level of approval
					$to_email = $ceo_email;
					$new_status_id = 4;
					$subject =  JText::_( 'A new request is awaiting your approval' );
				}
			}
			else if ($curr_status_id == 4) { // Tier 3 request approved
				$to_email = $accounting_email;
				$new_status_id = 5;
				$subject =  JText::_( 'Request has been approved and is awaiting purchase' );
			}
			
			// Update status in database
			$model->updateStatus($request_id,$new_status_id,'date_approved');
			
			// Send email
			$notifications = new SupplyOrderNotifications();
			$notifications->emailRequestDetails($to_email, $subject, $request_id);	
		}
		
		// get the redirect, current page including query string
		$uri = JURI::getInstance();
		if (count($requests_id_list) > 1)
			$msg	= JText::_( 'Selected requests have been approved.' );
		else if (count($requests_id_list) == 1)
			$msg	= JText::_( 'Selected request has been approved.' );
		$this->setRedirect( $uri->toString(), $msg );
		
	}
	
	//Delete the reqeust item
	public function delete_request(){
		$model =& $this->getModel('requests');
		// Clean all POST variables
		JRequest::_cleanArray( $_POST );
		
		$requests_id_list = JRequest::getVar('requests');
		
		$model->deleteRequest($request_id);
	}
	
	/*
	 * Edit Request
	 * 
	 */	
	
	public function get_status_with_date ($request) {
		$status_id = $request['request_status_id'];
		$status_desc = $request['status_desc'];
		$date_submitted = $request['date_submitted'];
		$date_approved = $request['date_approved'];
		$date_ordered = @$request['date_ordered'];
		$date_received = $request['date_received'];
		
		if ($status_id == 7) { // Received
			$status_desc = $status_desc . " (" . $date_received . ")";
		}
		else if ($status_id == 6) { // Ordered
			$status_desc = $status_desc . " (" . $date_ordered . ")";
		}
		else if ($status_id > 2) { // Ordered
			$status_desc = $status_desc . " (" . $date_approved . ")";
		}
		
		return $status_desc;
	}
	
	private function is_approved ($approval_level, $approval_level_required) {
		return (($approval_level+1) >= $approval_level_required);
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
	
	private function check_user_logged_in () {
		$user =& JFactory::getUser();
		
		if ($user->guest) {
			$redirectUrl = JURI::getInstance()->toString();
			$redirectUrl = urlencode(base64_encode($redirectUrl));
			$redirectUrl = '&return='.$redirectUrl;
			
			$joomlaLoginUrl = 'index.php?option=com_user&view=login';
			$return = $joomlaLoginUrl . $redirectUrl;
			
			$this->setRedirect( $return);
		}
	}
	
	
}