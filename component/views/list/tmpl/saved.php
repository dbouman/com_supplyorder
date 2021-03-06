<?php 

/**
 * Supply Order Component for Joomla! 1.5
 * List saved requests
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

<form
	action="<?php echo JURI::getInstance()->toString(); ?>"
	method="post" id="saved_requests" name="saved_requests" >

	<?php if ( $this->params->def( 'show_page_title', 1 ) ) : ?>
	<div
		class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
		<?php echo $this->escape($this->params->get('page_title')); ?>
	</div>
	<?php endif; ?>

	<table cellpadding="0" cellspacing="0" border="0" width="100%"	class="so_table">
		<tr>
			<th><?php echo JText::_( 'Select' ); ?></th>
			<th><?php echo JText::_( 'ID' ); ?></th>
			<th><?php echo JText::_( 'Vendor' ); ?></th>
			<th><?php echo JText::_( 'Description' ); ?></th>
			<th><?php echo JText::_( 'Quantity' ); ?></th>
			<th><?php echo JText::_( 'Total Price' ); ?></th>
			<th><?php echo JText::_( 'Details' ); ?></th>
			<th><?php echo JText::_( 'Edit' ); ?></th>
			<th><?php echo JText::_( 'Delete' ); ?></th>
		</tr>
		<?php
		if (!empty($this->requests)) {
			foreach ($this->requests as $request) 
			{ 
		?>
			<tr>
				<td style="text-align: center;"><input type="checkbox" value="<?php echo $request['request_id']; ?>" name="requests[]" id="request<?php echo $request['request_id']; ?>" /></td>
				<td><?php echo $request['request_id']; ?></td>
				<td><?php echo $request['vendor']; ?></td>
				<td><?php echo $request['item_desc']; ?></td>
				<td><?php echo $request['quantity']; ?></td>
				<td><?php echo $request['request_cost']; ?></td>
				<td><a class="popup" href="<?php echo JRoute::_( 'index.php?option=com_supplyorder&view=list&layout=details&tmpl=component&request_id=' . $request['request_id'] ); ?>">Details</a></td>
				<td><a href="<?php echo JRoute::_( 'index.php?option=com_supplyorder&view=requests&layout=edit&request_id=' . $request['request_id'] . '&Itemid='.JRequest::getint( 'Itemid' ) ); ?>">Edit</a></td>
				<td><a href="<?php echo JRoute::_( 'index.php?option=com_supplyorder&view=list&layout=saved&task=delete_request&request_id=' . $request['request_id'] . '&Itemid='.JRequest::getint( 'Itemid' ) ); ?>">Delete</a></td>
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
		<div id="so_button_container">
			<input type="submit" value="Submit Selected Requests" name="submitButton" /> 
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
		</table>
		<?php 
		}
		?>
	<input type="hidden" name="view" value="list" />
	<input type="hidden" name="layout" value="saved" />
	<input type="hidden" name="task" value="submit_request" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
