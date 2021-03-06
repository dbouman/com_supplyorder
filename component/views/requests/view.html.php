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
class SupplyOrderViewRequests extends JView
{
	function display( $tpl = null)
	{
		global $mainframe;
		
		$layoutName = $this->getLayout();
		
		$document =& JFactory::getDocument();
		$document->addScript(JURI::base(true).'/components/com_supplyorder/js/jquery-1.7.2.min.js');
		$document->addScriptDeclaration ( 'jQuery.noConflict();');
		$document->addScript(JURI::base(true).'/components/com_supplyorder/js/jquery.validate.min.js');
		$document->addScript(JURI::base(true).'/components/com_supplyorder/js/jquery-ui-1.8.22.custom.min.js');
		$document->addStyleSheet(JURI::base(true).'/components/com_supplyorder/css/smoothness/jquery-ui-1.8.22.custom.css');
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
		
		// Get models
		$requestsModel =& $this->getModel('requests');
		$commentsModel =& $this->getModel('comments');
		$filesModel =& $this->getModel('files');
		$accountsModel =& $this->getModel('accounts');
		
		// Get list of accounts
		$accounts = $accountsModel->listAccounts();
		$this->assignRef('accounts',$accounts);
		
		// Assign params
		$this->assignRef('params', $params);
		
		// Get any existing form data to be displayed, possibly if an error occurred
		$request = JFactory::getApplication()->getUserState('com_supplyorder.edit.request.data', array());
		if(empty($request)) {
			if ($layoutName == "edit") {
				$request_id = JRequest::getVar('request_id');
				$request = $requestsModel->getRequestCompleteDetail($request_id);	
			}
		}
		if (!empty($request['request_id'])) {
			$this->assignRef('comments',$commentsModel->getComments($request['request_id']));
			$this->assignRef('files',$filesModel->getFiles($request['request_id']));
		}
		$this->assignRef('request', $request);
		// Clear saved session data after loaded once
		JFactory::getApplication()->setUserState('com_supplyorder.edit.request.data', '');
		
		
		parent::display($tpl);
	}
	
	/**
	 * Get default page title based on layout
	 * @param string $layout
	 */
	function getDefaultTitle ($layoutName) {
		$page_title = "";
		if ($layoutName == 'default') {
			$page_title = JText::_( 'New Request' );
		}
		
		return $page_title;
	}
}
?>