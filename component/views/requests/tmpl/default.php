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
	    });

	    jQuery("#date_required").datepicker();

	 	// validate signup form on keyup and submit
		var validator = jQuery("#save_request_form").validate({
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
	action="<?php echo JRoute::_( 'index.php?option=com_supplyorder' ); ?>"
	method="post" id="save_request_form" name="save_request_form" >

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
				class="inputbox" width="248px;" />	<span id="total_price"></span>
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
		<tr>
			<td><?php echo JText::_( 'Comments' ); ?></td>
			<td><textarea cols="50" id="comments" name="comments" rows="7"
					class="inputbox"></textarea>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" value="Save" name="save" /> <input
				type="reset" value="Cancel" name="cancel"
				onclick="jQuery('#save_request_form').data('validator').resetForm(); jQuery('#vendor').focus();"></input>
			</td>
		</tr>
	</table>
	<input type="hidden" name="task" value="save_request" /> 
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
<form id="fileupload" action="/components/com_supplyorder/helpers/fileupload.php" method="POST" enctype="multipart/form-data">
	<!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
	<div class="row fileupload-buttonbar">
		<div class="span7">
			<!-- The fileinput-button span is used to style the file input field as button -->
			<span class="btn btn-success fileinput-button"> <i
				class="icon-plus icon-white"></i> <span>Add files...</span> <input
				type="file" name="files[]" multiple>
			</span>
			<button type="submit" class="btn btn-primary start">
				<i class="icon-upload icon-white"></i> <span>Start upload</span>
			</button>
			<button type="reset" class="btn btn-warning cancel">
				<i class="icon-ban-circle icon-white"></i> <span>Cancel upload</span>
			</button>
			<button type="button" class="btn btn-danger delete">
				<i class="icon-trash icon-white"></i> <span>Delete</span>
			</button>
			<input type="checkbox" class="toggle">
		</div>
		<!-- The global progress information -->
		<div class="span5 fileupload-progress fade">
			<!-- The global progress bar -->
			<div class="progress progress-success progress-striped active"
				role="progressbar" aria-valuemin="0" aria-valuemax="100">
				<div class="bar" style="width: 0%;"></div>
			</div>
			<!-- The extended global progress information -->
			<div class="progress-extended">&nbsp;</div>
		</div>
	</div>
	<!-- The loading indicator is shown during file processing -->
	<div class="fileupload-loading"></div>
	<br>
	<!-- The table listing the files available for upload/download -->
	<table role="presentation" class="table table-striped">
		<tbody class="files" data-toggle="modal-gallery"
			data-target="#modal-gallery"></tbody>
	</table>
</form>
<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td class="preview"><span class="fade"></span></td>
        <td class="name"><span>{%=file.name%}</span></td>
        <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
        {% if (file.error) { %}
            <td class="error" colspan="2"><span class="label label-important">{%=locale.fileupload.error%}</span> {%=locale.fileupload.errors[file.error] || file.error%}</td>
        {% } else if (o.files.valid && !i) { %}
            <td>
                <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="bar" style="width:0%;"></div></div>
            </td>
            <td class="start">{% if (!o.options.autoUpload) { %}
                <button class="btn btn-primary">
                    <i class="icon-upload icon-white"></i>
                    <span>{%=locale.fileupload.start%}</span>
                </button>
            {% } %}</td>
        {% } else { %}
            <td colspan="2"></td>
        {% } %}
        <td class="cancel">{% if (!i) { %}
            <button class="btn btn-warning">
                <i class="icon-ban-circle icon-white"></i>
                <span>{%=locale.fileupload.cancel%}</span>
            </button>
        {% } %}</td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        {% if (file.error) { %}
            <td></td>
            <td class="name"><span>{%=file.name%}</span></td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td class="error" colspan="2"><span class="label label-important">{%=locale.fileupload.error%}</span> {%=locale.fileupload.errors[file.error] || file.error%}</td>
        {% } else { %}
            <td class="preview">{% if (file.thumbnail_url) { %}
                <a href="{%=file.url%}" title="{%=file.name%}" rel="gallery" download="{%=file.name%}"><img src="{%=file.thumbnail_url%}"></a>
            {% } %}</td>
            <td class="name">
                <a href="{%=file.url%}" title="{%=file.name%}" rel="{%=file.thumbnail_url&&'gallery'%}" download="{%=file.name%}">{%=file.name%}</a>
            </td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td colspan="2"></td>
        {% } %}
        <td class="delete">
            <button class="btn btn-danger" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}">
                <i class="icon-trash icon-white"></i>
                <span>{%=locale.fileupload.destroy%}</span>
            </button>
            <input type="checkbox" name="delete" value="1">
        </td>
    </tr>
{% } %}
</script>
