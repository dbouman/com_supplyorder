<?php 

/**
 * Supply Order Component for Joomla! 1.5
 * List requested requests
 * @version 1.5.0
 * @author Howard County Library
 * @package com_supply_order
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 **/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Get Brief details display in list
// Select  ID    Vendor    Description     Quantity     Total Price    Details    Edit   Delete  
// Check boxes to select orders to submit (use javascript to highly row when selected)
// Links to edit/delete order (use image icons)
//
?>

<?php
if(isset($this->message)){
	$this->display('message');
}
?>

<form
	action="<?php echo JRoute::_( 'index.php?option=com_supplyorder&view=list&layout=ordered' ); ?>"
	method="post" id="ordered_requests" name="ordered_requests" >

	<?php if ( $this->params->def( 'show_page_title', 1 ) ) : ?>
	<div
		class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
		<?php echo $this->escape($this->params->get('page_title')); ?>
	</div>
	<?php endif; ?>

	<table cellpadding="0" cellspacing="0" border="0" width="100%"	class="so_table">
		<tr>
			<th><?php echo JHTML::_( 'grid.sort', 'ID', 'request_id', $this->sortDirection, $this->sortColumn); ?></th>
			<th><?php echo JHTML::_( 'grid.sort', 'Vendor', 'vendor', $this->sortDirection, $this->sortColumn); ?></th>
			<th><?php echo JHTML::_( 'grid.sort', 'Description', 'item_desc', $this->sortDirection, $this->sortColumn); ?></th>
			<th><?php echo JText::_( 'Quantity' ); ?></th>
			<th><?php echo JText::_( 'Total Price' ); ?></th>
			<th><?php echo JText::_( 'Status' ); ?></th>
			<th><?php echo JText::_( 'Details' ); ?></th>
			<th><?php echo JText::_( 'Delete' ); ?></th>
		</tr>
		<?php
		if (!empty($this->requests)) {
			foreach ($this->requests as $request) 
			{ 
		?>
			<tr>
				<td><?php echo $request['request_id']; ?></td>
				<td><?php echo $request['vendor']; ?></td>
				<td><?php echo $request['item_desc']; ?></td>
				<td><?php echo $request['quantity']; ?></td>
				<td><?php echo $request['request_cost']; ?></td>
				<td><?php echo $request['status_name']; ?></td>
				<td><a class="popup fancybox.iframe" href="<?php echo JRoute::_( 'index.php?option=com_supplyorder&view=list&layout=details&tmpl=component&request_id=' . $request['request_id'] ); ?>">Details</a></td>
				<td><a href="<?php echo JRoute::_( 'index.php?option=com_supply_order&view=list&layout=saved&task=delete_request&request_id=' . $request['request_id'] ); ?>">Delete</a></td>
			</tr>
		<?php
			} 
		?>
		<tfoot>
		    <tr>
		      <td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td>
		    </tr>
		</tfoot>
	</table>
	<?php
	}
	else {
	?>
		<tr>
			<td colspan="9">
				<?php echo JText::_( 'No received requests.' ); ?>
			</td>
		</tr>
	<?php
	}
	?>
	</table>
	<input type="hidden" name="filter_order" value="<?php echo $this->sortColumn; ?>" />
	<input type="hidden" name="filter_order_dir" value="<?php echo $this->sortDirection; ?>" />
	
	<input type="hidden" name="view" value="list" />
	<input type="hidden" name="layout" value="ordered" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
