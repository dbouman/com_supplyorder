<?php 

/**
 * Supply Order Component for Joomla! 1.5
 * Form for placing new requests
 * @version 1.5.0
 * @author Howard County Library
 * @package com_supply_order
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 **/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<script type="text/javascript">
<!--
	jQuery(document).ready(function() {
	    jQuery('#unitPrice,#quantity').change(function() {
		    var total = parseFloat(jQuery('#unitPrice').val()) * parseInt(jQuery('#quantity').val());
	    	jQuery('#totalPrice').html("Total Price: $ "+total);
	    });

	    jQuery("#dateRequired").datepicker();

	 	// validate signup form on keyup and submit
		var validator = jQuery("#supplyOrderRequestForm").validate({
			rules: {
				vendor: "required",
				quantity: "required digits",
				unitPrice: "required number",
				unitMeasure: "required",
				description: "required",
				dateRequired: "required date",
				shipTo: "required",
				accountId: "required"
			},
			messages: {
				vendor: "Please enter a vendor.",
				quantity: "Please enter a quantity.",
				unitPrice: "Please enter valid amount like 10.89.",
				unitMeasure: "Please select a unit measure.",
				description: "Please enter a description.",
				dateRequired: "Please select the date by which you need this request filled.",
				shipTo: "Please select which location this request should be shipped to.",
				accountId: "Please select which account this request should be placed under."
			},
			errorClass: "error-required"
		});
		
		jQuery('vendor').focus(); 
	  });
//-->
</script>

<?php
if(isset($this->message)){
	$this->display('message');
}
?>

<form
	action="<?php echo JRoute::_( 'index.php?option=com_supply_order' ); ?>"
	method="post" id="supplyOrderRequestForm" name="supplyOrderRequestForm" >

	<?php if ( $this->params->def( 'show_page_title', 1 ) ) : ?>
	<div
		class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
		<?php echo $this->escape($this->params->get('page_title')); ?>
	</div>
	<?php endif; ?>

	<table cellpadding="0" cellspacing="0" border="0" width="100%"
		class="contentpane">
		<tr>
			<td><?php echo JText::_( 'Vendor Name' ); ?> <span style="color: red;">*</span>
			</td>
			<td><input type="text" name="vendor" id="vendor" class="inputbox required" /></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Item No.' ); ?></td>
			<td><input type="text" name="item_num" id="item_num" class="inputbox" /></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Color' ); ?></td>
			<td><input type="text" name="color" id="color" class="inputbox" /></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Quantity' ); ?><span style="color: red;">*</span></td>
			<td><input type="text" name="quantity" id="quantity" class="inputbox" /></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Unit Price' ); ?> <span style="color: red;">*</span>
			</td>
			<td>$<input type="text" name="unit_cost" id="unit_cost"
				class="inputbox" width="248px;" />	<span id="totalPrice"></span>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Unit Measure' ); ?> <span style="color: red;">*</span>
			</td>
			<td><select id="unitMeasure" name="unitMeasure">
					<option value="">Select One</option>
					<option value="Each">Each</option>
					<option value="Dozen">Dozen</option>
					<option value="Pack">Pack</option>
					<option value="Case">Case</option>
					<option value="Roll">Roll</option>
					<option value="Set">Set</option>
					<option value="Other">Other (Explain in Comments)</option>
			</select>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Description' ); ?> <span style="color: red;">*</span>
			</td>
			<td><textarea cols="50" id="item_desc" name="item_desc" rows="7"
					class="inputbox"></textarea>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><i><?php echo JText::_( 'Allow at least 2 weeks to process.' ); ?></i></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Need By' ); ?> <span style="color: red;">*</span>
			</td>
			<td>
				<input	type="text" name="date_required" id="date_required" class="inputbox" />
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Ship To' ); ?> <span style="color: red;">*</span>
			</td>
			<td><select id="ship_to" name="ship_to">
					<option value=""><?php echo JText::_( 'Select One' ); ?></option>
					<option value="Admin@CEN">Admin@CEN</option>
					<option value="Admin@ECO">Admin@ECO</option>
					<option value="CEN">Central Branch</option>
					<option value="ECO">East Columbia Branch</option>
					<option value="ELK">Elkridge Branch</option>
					<option value="GLE">Glenwood Branch</option>
					<option value="MIL">Miller Branch</option>
					<option value="SAV">Savage Branch</option>
			</select>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><i><?php echo JText::_( 'Please provide URL (Link for web-site)' ); ?></i></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'URL' ); ?></td>
			<td><input type="text" name="url" id="url" class="inputbox" /> <span>
			</span>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Account' ); ?> <span style="color: red;">*</span>
			</td>
			<td><select name="account_id" id="account_id">
					<option value=""><?php echo JText::_( 'Select Account' ); ?>:</option> 
					<?php 
					foreach ($accounts as $account) {
						?>
						<option value="<?php echo $account['id']; ?>">
							<?php echo	$account['number'] . " " . 
										$account['description'] . " " . 
										$account['owner']; 
							?>
						</option>
						<?php 	
					}
					?>
			</select>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Comments' ); ?></td>
			<td><textarea cols="50" id="comments" name="comments" rows="7"
					class="inputbox"></textarea>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" value="Save" name="saveOrder" /> <input
				type="reset" value="Cancel" name="cancel"
				onclick="jQuery('#supplyOrderRequestForm').data('validator').resetForm(); jQuery('#vendor').focus();"></input>
			</td>
		</tr>
	</table>
	<input type="hidden" name="task" value="request_save" /> 
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
