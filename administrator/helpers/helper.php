<?php 

/**
 * Joomla! 1.5 component supplyorder
 * Code generated by : Danny's Joomla! 1.5 MVC Component Code Generator
 * http://www.joomlafreak.be
 * date generated:  
 * @version 0.8
 * @author Danny Buytaert 
 * @package com_supplyorder
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 **/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


  /**
  * @package supplyorder
  */
  class SupplyorderHelper
{
function checkCategories() {
  global $mainframe ;
  $db =& JFactory::getDBO();
  $db->setQuery( "SELECT * FROM #__categories WHERE `section`='com_supplyorder'" ) ;
  $rows = $db->loadAssocList() ;
  if(empty($rows)) {
 $mainframe->enqueueMessage( JText::_( 'You have no categories configured yet' ), 'notice' ) ;
  return false ;
  }
  return true ;
  }
  }