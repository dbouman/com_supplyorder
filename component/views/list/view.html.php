<?php 

/**
 * Supply Order Component for Joomla! 1.5
 * This view is responsible for handling all of the forms accessible to every user. Handles
 * placing requests, viewing saved orders, updating an order and viewing requested orders.
 * @version 1.5.0
 * @author Howard County Library
 * @package com_supplyorder
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 **/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.view');
/**
 * HTML View class for the com_supplyorder component
 * @package com_supplyorder
 */
class SupplyOrderViewList extends JView
{
	function display( $tpl = null)
	{
		global $mainframe;
		
		$layoutName = $this->getLayout();
		
		$document =& JFactory::getDocument();
		$document->addScript(JURI::base(true).'/components/com_supplyorder/js/jquery-1.7.2.min.js');
		$document->addScriptDeclaration ( 'jQuery.noConflict();');
		$document->addScript(JURI::base(true).'/components/com_supplyorder/js/jquery.fancybox.pack.js');
		$document->addScript(JURI::base(true).'/components/com_supplyorder/js/so.table.js');
		
		$document->addStyleSheet(JURI::base(true).'/components/com_supplyorder/css/jquery.fancybox.css');
		$document->addStyleSheet(JURI::base(true).'/components/com_supplyorder/css/default.css');
		if (JRequest::getCmd('tmpl') != 'component') 
			$document->addStyleSheet(JURI::base(true).'/components/com_supplyorder/css/default.print.css','text/css','print');
		
		// Get the page/component configuration
		$params = &$mainframe->getParams();
		$menus = &JSite::getMenu();
		$menu = $menus->getActive();
		// because the application sets a default page title, we need to get it
		// right from the menu item itself
		if (is_object( $menu )) {
			$menu_params = new JParameter( $menu->params );
			if (!$menu_params->get( 'page_title')) {
				$params->set('page_title', $this->getDefaultTitle ($layoutName));
			}
		} else {
			$params->set('page_title', $this->getDefaultTitle ($layoutName) );
		}
		$document->setTitle( $params->get( 'page_title' ) );

		$user =& JFactory::getUser();
		$employee_id = $user->id;
		
		// Get required models
		$requestsModel =& $this->getModel('requests');
		$userModel =& $this->getModel('user');
		$commentsModel =& $this->getModel('comments');
		$filesModel =& $this->getModel('files');
		
		$userInfo = $userModel->getUserInfo($employee_id);
		$role_id = $userInfo['role_id'];
		
		//Column Sorting
		$this->assignRef('sortDirection', $requestsModel->getState('filter_order_dir'));
		$this->assignRef('sortColumn', $requestsModel->getState('filter_order'));
		
		if ($layoutName == 'details') {
			$request_id = JRequest::getVar('request_id');
			$request = $requestsModel->getRequestCompleteDetail($request_id);
			$this->assignRef('request',$request);
			
			$this->assignRef('comments',$commentsModel->getComments($request['request_id']));
			$this->assignRef('files',$filesModel->getFiles($request['request_id']));
		}else if ($layoutName == 'confirm') {//Confirm popup to approve and unapprove a request
			$request_id = JRequest::getVar('request_id');
			$request = $requestsModel->getRequestCompleteDetail($request_id);
			$this->assignRef('request',$request);
			
			$this->assignRef('comments',$commentsModel->getComments($request['request_id']));
			$this->assignRef('files',$filesModel->getFiles($request['request_id']));
		}
		else {
			if ($layoutName == 'saved') {
				$status_ids = array(1);
				$requests = $requestsModel->listRequestByOwner($employee_id, $status_ids);
			}
			else if ($layoutName == 'requested') {
				$status_ids = array(2,3,4,5,6); 
				$requests = $requestsModel->listRequestByOwner($employee_id, $status_ids);
			}
			else if($layoutName == 'received') {
				$status_ids = array(7);
				$requests = $requestsModel->listRequestByOwner($employee_id, $status_ids);
			}
			else if($layoutName == 'pending') {
				if ($role_id == 4) { // CEO using page
					$status_ids = array(4);
					$requests = $requestsModel->listRequestByStatus($status_ids);
				}
				else {
					$requests = $requestsModel->listRequestByApprover($employee_id);
				}
			}
			else if($layoutName == 'approved') {
				if ($role_id == 4) { // CEO using page
					$status_ids = array(5,6,7);
					$approval_level_required = 3;
					$requests = $requestsModel->listRequestByStatus($status_ids,$approval_level_required);
				}
				else if ($role_id == 3) {
					$status_ids = array(4,5,6,7);
					$requests = $requestsModel->listRequestByApprover($employee_id, $status_ids);
				}
				else if ($role_id == 2) {
					$status_ids = array(3,4,5,6,7);
					$requests = $requestsModel->listRequestByApprover($employee_id, $status_ids);
				}
			}
			else if($layoutName == 'pending_accouting') {
				$status_ids = array(2,3,4,5,6);
				$requests = $requestsModel->listRequestByOwner($employee_id, $status_ids);// @TODO list pending orders by Acc Admin 
			}
			else if($layoutName == 'ordered'){
				$status_ids = array(2,3,4,5,6);
				$requests = $requestsModel->listRequestByStatus($status_ids);// @NOTE Admin List only
			}
			
			$this->assignRef('requests',$requests);
		}
		
		// Get pagination data from model
		$pagination =& $this->get('Pagination');
		$this->assignRef('pagination', $pagination);
		
		// Assign params
		$this->assignRef('params', $params);
		
		parent::display($tpl);
	}
	
	/**
	 * Get default page title based on layout
	 * @param string $layout
	 */
	function getDefaultTitle ($layoutName) {
		$page_title = "";
		if ($layoutName == 'default') {
			$page_title = JText::_( 'SUPPLYORDER' ); // NO Default page actually
		}
		else if ($layoutName == 'saved') {
			$page_title = JText::_( 'Saved Requests' );
		}
		else if ($layoutName == 'requested') {
			$page_title = JText::_( 'Requested Requests' );
		}
		else if ($layoutName == 'received') {
			$page_title = JText::_( 'Received Requests' );
		}
		else if ($layoutName == 'pending') {
			$page_title = JText::_( 'Pending Requests' );
		}
		else if ($layoutName == 'approved') {
			$page_title = JText::_( 'Approved Requests' );
		}
		else if ($layoutName == 'pending_accouting') {
			$page_title = JText::_( 'PendingAccounting Requests' );
		}
		else if ($layoutName == 'ordered') {
			$page_title = JText::_( 'Ordered Requests' );
		}
	
		return $page_title;
	}
	
}
?>