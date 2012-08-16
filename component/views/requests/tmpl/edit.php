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
		jQuery('#unit_cost,#quantity').change(function() {
			calculateTotalCost();
	    });

	    jQuery("#date_required").datepicker();

	 	// validate signup form on keyup and submit
		var validator = jQuery("#edit_request").validate({
			rules: {
				vendor: "required",
				quantity: "required digits",
				unit_cost: "required number",
				unit_measure: "required",
				item_desc: "required",
				date_required: "required date",
				ship_to: "required",
				account_id: "required"
			},
			messages: {
				vendor: "Please enter a vendor.",
				quantity: "Please enter a quantity.",
				unit_cost: "Please enter valid amount like 10.89.",
				unit_measure: "Please select a unit measure.",
				item_desc: "Please enter a description.",
				date_required: "Please select the date by which you need this request filled.",
				ship_to: "Please select which location this request should be shipped to.",
				account_id: "Please select which account this request should be placed under."
			},
			errorClass: "error-required"
		});

	 	// Removes a file input field
		jQuery("#filesContainer").delegate(".removeFile","click", function() {
			jQuery(this).prev().remove();
			jQuery(this).next().remove();
			jQuery(this).remove();
		});

		// Auto-select values in dropdown menus if passed into form
		<?php if (!empty($this->request['unit_measure'])) { ?>
			jQuery('#unit_measure option[value=<?php echo $this->request['unit_measure']; ?>]').attr('selected',true);
		<?php } ?>

		<?php if (!empty($this->request['ship_to'])) { ?>
			jQuery('#ship_to option[value=<?php echo $this->request['ship_to']; ?>]').attr('selected',true);
		<?php } ?>

		<?php if (!empty($this->request['account_id'])) { ?>
			jQuery('#account_id option[value=<?php echo $this->request['account_id']; ?>]').attr('selected',true);
		<?php } ?>

		// Run calculate cost in case numbers already passed in, won't do anything if empty
		calculateTotalCost();
		
		jQuery('vendor').focus(); 
	});

	function calculateTotalCost () {
		var unit_cost = jQuery('#unit_cost').val();
	    var quantity = jQuery('#quantity').val();
	    if (unit_cost && quantity) { 
		    var total = parseFloat(jQuery('#unit_cost').val()) * parseInt(jQuery('#quantity').val());
		    total = parseFloat(Math.round(total * 100) / 100).toFixed(2);
	    	jQuery('#total_price').html("Total Price: $ "+total);
	    }
	    else {
	    	jQuery('#total_price').html("");
	    }
	}

	function addAnotherFile() {
		jQuery('#filesContainer').append(
			jQuery('<input/>').attr('type', 'file').attr('name', 'files[]'),
			" <a href='javascript:;' class='removeFile'>X</a> <br />"
		);
	}	

	function deleteFile(file_id) {
		var url = '<?php echo JRoute::_( 'index.php?option=com_supplyorder&task=delete_file' ); ?>';
		jQuery.get(  
	            url,  
	            {file_id: file_id},  
	            function(responseText){  
	            	jQuery('#file'+file_id).remove();
	            },  
	            "html"  
	        );  
	}
//-->
</script>
		
<?php
if(isset($this->message)){
	$this->display('message');
}
?>

<form
	action="<?php echo JRoute::_( 'index.php?option=com_supplyorder&view=list&layout=saved' . '&Itemid='.JRequest::getint( 'Itemid' ) ); ?>"
	method="post" id="edit_request" name="edit_request" enctype="multipart/form-data" >

	<?php if ( $this->params->def( 'show_page_title', 1 ) ) : ?>
	<div
		class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
		<?php echo $this->escape($this->params->get('page_title')); ?>
	</div>
	<?php endif; ?>

	<table cellpadding="0" cellspacing="2" border="0" width="100%"
		class="contentpane">
		<tr>
			<td><?php echo JText::_( 'Vendor Name' ); ?> <span style="color: red;">*</span>
			</td>
			<td><input type="text" name="vendor" id="vendor" class="inputbox required"  value="<?php if (!empty($this->request['vendor'])) { echo $this->request['vendor']; } ?>" /></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Item No.' ); ?></td>
			<td><input type="text" name="item_num" id="item_num" class="inputbox" value="<?php if (!empty($this->request['item_num'])) { echo $this->request['item_num']; } ?>" /></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Color' ); ?></td>
			<td><input type="text" name="color" id="color" class="inputbox" value="<?php if (!empty($this->request['color'])) { echo $this->request['color']; } ?>" /></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Quantity' ); ?><span style="color: red;">*</span></td>
			<td><input type="text" name="quantity" id="quantity" class="inputbox" value="<?php if (!empty($this->request['quantity'])) { echo $this->request['quantity']; } ?>" /></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Unit Price' ); ?> <span style="color: red;">*</span>
			</td>
			<td>$<input type="text" name="unit_cost" id="unit_cost"
				class="inputbox" width="248px;" value="<?php if (!empty($this->request['unit_cost'])) { echo $this->request['unit_cost']; } ?>" />	<span id="total_price"></span>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Unit Measure' ); ?> <span style="color: red;">*</span>
			</td>
			<td><select id="unit_measure" name="unit_measure">
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
					class="inputbox"><?php if (!empty($this->request['item_desc'])) { echo $this->request['item_desc']; } ?></textarea>
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
				<input	type="text" name="date_required" id="date_required" value="<?php if (!empty($this->request['date_required'])) { echo JHTML::_('date', $this->request['date_required'], JText::_( 'DATE_FORMAT' )); } ?>" class="inputbox" />
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Ship To' ); ?> <span style="color: red;">*</span>
			</td>
			<td><select id="ship_to" name="ship_to">
					<option value=""><?php echo JText::_( 'Select One' ); ?></option>
					<option value="Admin at CEN">Admin at CEN</option>
					<option value="Admin at ECO">Admin at ECO</option>
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
			<td><input type="text" name="url" id="url" class="inputbox" value="<?php if (!empty($this->request['url'])) { echo $this->request['url']; } ?>" /> <span>
			</span>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Account' ); ?> <span style="color: red;">*</span>
			</td>
			<td><select name="account_id" id="account_id">
					<option value=""><?php echo JText::_( 'Select Account' ); ?>:</option> 
					<?php 
					foreach ($this->accounts as $account) {
						?>
						<option value="<?php echo $account['id']; ?>">
							<?php echo	$account['number'] . " " . 
										$account['name'] . " " . 
										$account['owner']; 
							?>
						</option>
						<?php 	
					}
					?>
			</select>
			</td>
		</tr>
		<?php
		if (!empty($this->files)) {
		?>
		<tr>
			<td>Files</td>
			<td>
				<div id="attached_files">
					<?php
					foreach ($this->files as $file) {
					?>
					<div id="file<?php echo $file['file_id']; ?>" class="so_file_row">
						<span><a href="<?php echo $file['file_location']; ?>" target="_blank"><?php echo $file['file_name']; ?></a></span>
						<span><?php echo JHTML::_('date', $file['date_posted'], JText::_( 'DATETIME_FORMAT' )); ?></span>
						<span><a href="javascript: deleteFile(<?php echo $file['file_id']; ?>);" title="<?php echo JText::_( 'Delete File' ); ?>">X</a></span>
					</div>
					<?php
					} 
					?>
				</div>
			</td>
		</tr>
		<?php 
		}
		?>
		<tr>
			<td><?php echo JText::_( 'Attach New Files' ); ?></td>
			<td><input type="file" name="files[]"> <a href="javascript: addAnotherFile();">Attach another file</a>
				<div id="filesContainer"></div>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Comments' ); ?></td>
			<td><textarea cols="50" id="comments" name="comments" rows="7"
					class="inputbox"><?php if (!empty($this->request['comments'])) { echo $this->request['comments']; } ?></textarea>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" value="Save Changes" name="saveButton" /> <input
				type="button" value="Cancel" name="cancelButton"
				onclick="window.history.back()"></input>
			</td>
		</tr>
	</table>
	<input type="hidden" name="request_id" value="<?php echo $this->request['request_id']; ?>" /> 
	<input type="hidden" name="task" value="edit_request" /> 
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
