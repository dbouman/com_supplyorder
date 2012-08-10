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

if(isset($this->message)){
	$this->display('message');
}
?>
<div
	class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
	Confirm action - #<?php echo $this->request['request_id']; ?>
</div>

<form
	action="<?php echo JRoute::_( 'index.php?option=com_supplyorder' ); ?>"
	method="post" id="action_confirm_form" name="action_confirm_form" enctype="multipart/form-data" >

	<div id="divToPrint">
		<table cellpadding="0" cellspacing="0" border="0" width="100%"
			class="so_table">
			<tr>
				<td>&nbsp;</td>
				<td><?php echo JText::_( 'Admin Comments' ); ?> <span
					style="color: red;">*</span></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><textarea cols="50" id="comments" name="comments" rows="7"
						class="inputbox"></textarea>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="submit" value="Save" name="save" /> <input
					type="reset" value="Cancel" name="cancel"
					onclick="$.fancybox.close();" />
				</td>
			</tr>
		</table>
	</div>
	<input type="hidden" name="request_id" value="<?php echo $this->request['request_id']; ?>" />
	<input type="hidden" name="task" value="save_request" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>