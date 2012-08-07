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
		
		$requestsModel =& $this->getModel('requests');
		
		//Column Sorting
		$this->assignRef('sortDirection', $requestsModel->getState('filter_order_dir'));
		$this->assignRef('sortColumn', $requestsModel->getState('filter_order'));
		
		if ($layoutName == 'details') {
			$request_id = JRequest::getVar('request_id');
			$request = $requestsModel->getRequestCompleteDetail($request_id); // @NOTE change to complete details
			$this->assignRef('request',$request);
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
			else if($layoutName == 'received'){
				$status_ids = array(2,3,4,5,6);
				$requests = $requestsModel->listRequestByStatus($status_ids);//@NOTE Admin List only
			}
			else if($layoutName == 'pending'){
				$status_ids = array(2,3,4,5,6);
				$requests = $requestsModel->listRequestByOwner($employee_id, $status_ids);
			}
			else if($layoutName == 'approved'){
				$status_ids = array(2,3,4,5,6);
				$requests = $requestsModel->listRequestByApprover($employee_id, $status_ids);
			}
			else if($layoutName == 'pendingAccouting'){
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
		else if ($layoutName == 'pendingAccouting') {
			$page_title = JText::_( 'PendingAccounting Requests' );
		}
		else if ($layoutName == 'ordered') {
			$page_title = JText::_( 'Ordered Requests' );
		}
	
		return $page_title;
	}
}
?>