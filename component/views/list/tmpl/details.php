<?php 

/**
 * Supply Order Component for Joomla! 1.5
 * Request details for a single request
 * @version 1.5.0
 * @author Howard County Library
 * @package com_supply_order
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 **/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<?php
if(isset($this->message)){
	$this->display('message');
}
?>

<div
	class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
	Request Details - <?php echo $request['request_id']; ?>
</div>


<table cellpadding="0" cellspacing="0" border="0" width="100%"	class="so_table">
	<tr>
		<td><?php echo JText::_( 'ID' ); ?></td>
		<td><?php echo $this->request['request_id']; ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_( 'Vendor' ); ?></td>
		<td><?php echo $this->request['vendor']; ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_( 'Description' ); ?></td>
		<td><?php echo $this->request['item_desc']; ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_( 'Quantity' ); ?></td>
		<td><?php echo $this->request['quantity']; ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_( 'Total Price' ); ?></td>
		<td><?php echo $this->request['request_cost']; ?></td>
	</tr>
</table>
