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

<script type="text/javascript">
<!--
	jQuery(document).ready(function() {
		
		// Alternate table row color
		jQuery('.so_table tr:even').addClass('alt'); 
		
		// Handles highlighting row when box is checked
		jQuery(".so_table").delegate('input[id^="request"]','click', function(e) {
			jQuery(this).parents("tr").toggleClass("selected_color");
		});
	
		jQuery("a.popup").fancybox({
			fitToView	: false,
			width		: 830,
			height		: 600,
			autoSize	: false,
			closeClick	: false,
			scrolling	: 'no',
			openEffect	: 'elastic',
			closeEffect	: 'elastic',
			openSpeed	: 'normal', 
			closeSpeed	: 'normal'
		});
		
	  });

	function tableOrdering( order, dir, task )
	{
		var form = document.adminForm;
	 
		form.filter_order.value = order;
		form.filter_order_Dir.value = dir;
		document.adminForm.submit( task );
	}
//-->
</script>

<?php
if(isset($this->message)){
	$this->display('message');
}
?>

<form
	action="<?php echo JRoute::_( 'index.php?option=com_supplyorder' ); ?>"
	method="post" id="saved_requests" name="saved_requests" >

	<?php if ( $this->params->def( 'show_page_title', 1 ) ) : ?>
	<div
		class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
		<?php echo $this->escape($this->params->get('page_title')); ?>
	</div>
	<?php endif; ?>

	<table cellpadding="0" cellspacing="0" border="0" width="100%"	class="so_table">
		<tr>
			<th><?php echo JText::_( 'Received' ); ?></th>
			<th><?php echo JHTML::_( 'grid.sort', 'ID', 'DbNameColumn', $this->sortDirection, $this->sortColumn); ?></th>
			<th><?php echo JHTML::_( 'grid.sort', 'Vendor', 'DbDescriptionColumn', $this->sortDirection, $this->sortColumn); ?></th>
			<th><?php echo JText::_( 'Description' ); ?></th>
			<th><?php echo JText::_( 'Quantity' ); ?></th>
			<th><?php echo JText::_( 'Total Price' ); ?></th>
			<th><?php echo JText::_( 'Details' ); ?></th>
			<th><?php echo JText::_( 'Delete' ); ?></th>
		</tr>
		<?php
		if (!empty($this->requests)) {
			foreach ($this->requests as $request) 
			{ 
		?>
			<tr>
				<td><input type="checkbox" value="<?php echo $request['request_id']; ?>" name="requests[]" id="request<?php echo $request['request_id']; ?>" /></td>
				<td><?php echo $request['request_id']; ?></td>
				<td><?php echo $request['vendor']; ?></td>
				<td><?php echo $request['item_desc']; ?></td>
				<td><?php echo $request['quantity']; ?></td>
				<td><?php echo $request['request_cost']; ?></td>
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
	<div style="float: right; text-align: right;">
		<input type="submit" value="Submit" name="submitButton" />
		<input type="button" value="Cancel" name="cancelButton" onclick="window.history.back();"></input>
	</div>
	<?php
	}
	else {
	?>
		<tr>
			<td colspan="9">
				<?php echo JText::_( 'No saved requests.' ); ?>
			</td>
		</tr>
	<?php
	}
	?>
	</table>
	<input type="hidden" name="filter_order" value="<?php echo $this->request_id; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->desc; ?>" />
	
	<input type="hidden" name="view" value="list" />
	<input type="hidden" name="layout" value="requested" />
	<input type="hidden" name="task" value="received_submit" /> 
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
