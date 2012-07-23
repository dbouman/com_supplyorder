<?php 

/**
 * Supply Order Component for Joomla! 1.5
 * Administrator Controller
 * @version 1.5.0
 * @author Howard County Library
 * @package com_supplyorder
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 **/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


jimport( 'joomla.application.component.controller' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'helper.php' );
/**
 * Supplyorder Controller
 *
 * @package Supplyorder
 */
class SupplyOrderController extends JController
{
	function __construct()
	{
		parent::__construct();
		// Register Extra tasks
		$this->registerTask( 'add', 'display' );
		$this->registerTask( 'edit', 'display' );
	}
	function display( )
	{
		switch($this->getTask())
		{
			case 'add' :
				{
					JRequest::setVar( 'hidemainmenu', 1 );
					JRequest::setVar( 'layout', 'form' );
					JRequest::setVar( 'view' , 'item');
					JRequest::setVar( 'edit', false );
					// Checkout the item
					$model = $this->getModel('item');
					$model->checkout();
				} break;
			case 'edit' :
				{
					JRequest::setVar( 'hidemainmenu', 1 );
					JRequest::setVar( 'layout', 'form' );
					JRequest::setVar( 'view' , 'item');
					JRequest::setVar( 'edit', true );
					// Checkout the item
					$model = $this->getModel('item');
					$model->checkout();
				} break;
		}
		parent::display();
	}
	function store()
	{
		$post = JRequest::get('post');
		$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$post['id'] = (int)$cid[0];
		$this->id = $post['id'] ;
		//$post['text'] = JRequest::getVar('text', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$model = $this->getModel('item');
		if ($model->store($post)) {
			$this->msg = JText::_( 'Item Saved' );
		} else {
			$this->msg = JText::_( 'Error Saving Item' );
		}
		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();
	}
	function save()
	{
		$this->store() ;
		$link = 'index.php?option=com_supplyorder';
		$this->setRedirect( $link, $this->msg);
	}
	function apply()
	{
		$this->store() ;
		$link = 'index.php?option=com_supplyorder&view=item&task=edit&cid[]=' . $this->id ;
		$this->setRedirect($link, $this->msg);
	}
	function remove()
	{
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to delete' ) );
		}
		$model = $this->getModel('item');
		if(!$model->delete($cid)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>
			";
		}
		$this->setRedirect( 'index.php?option=com_supplyorder' );
	}

	function publish()
	{
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to publish' ) );
		}
		$model = $this->getModel('item');
		if(!$model->publish($cid, 1)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>
			";
		}
		$this->setRedirect( 'index.php?option=com_supplyorder' );
	}

	function unpublish()
	{
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to unpublish' ) );
		}
		$model = $this->getModel('item');
		if(!$model->publish($cid, 0)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>
			";
		}
		$this->setRedirect( 'index.php?option=com_supplyorder' );
	}
	function cancel()
	{
		// Checkin the item
		$model = $this->getModel('item');
		$model->checkin();
		$this->setRedirect( 'index.php?option=com_supplyorder' );
	}

	function orderup()
	{
		$model = $this->getModel('item');
		$model->move(-1);
		$this->setRedirect( 'index.php?option=com_supplyorder');
	}
	function orderdown()
	{
		$model = $this->getModel('item');
		$model->move(1);
		$this->setRedirect( 'index.php?option=com_supplyorder');
	}
	function saveorder()
	{
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		$order = JRequest::getVar( 'order', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		JArrayHelper::toInteger($order);
		$model = $this->getModel('item');
		$model->saveorder($cid, $order);
		$msg = 'New ordering saved';
		$this->setRedirect( 'index.php?option=com_supplyorder', $msg );
	}
}
