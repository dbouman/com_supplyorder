<html>
<body>
<style type="text/css">
#so_comment_footer {
	font-size: 10px;
	font-style:italic
}

#so_left_col {
	float: left;
	width: 50%;
}

#so_right_col {
	float: right;
	width: 40%;
	margin-left: 10px; 
	margin-right: 15px;
}
.clear_both {
	clear: both;
}
</style>

<div id="so_left_col">
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="so_table">
		<tr>
			<th colspan="2"><?php echo JText::_( 'Request' ); ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'ID' ); ?></td>
			<td><?php echo $this->request['request_id']; ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Status' ); ?></td>
			<td><?php echo SupplyOrderController::get_status_with_date($this->request); ?></td>
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
			<td><?php echo JText::_( 'Item Number' ); ?></td>
			<td><?php echo $this->request['item_num']; ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Color' ); ?></td>
			<td><?php echo $this->request['color']; ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'URL' ); ?></td>
			<td><a href="<?php echo $this->request['url']; ?>" target="_blank"><?php echo $this->request['url']; ?></a></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Quantity' ); ?></td>
			<td><?php echo $this->request['quantity']; ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Total Price' ); ?></td>
			<td>$<?php echo $this->request['request_cost']; ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Needed By' ); ?></td>
			<td><?php echo JHTML::_('date', $this->request['date_required'], JText::_( 'DATE_FORMAT' )); ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Ship To' ); ?></td>
			<td><?php echo $this->request['ship_to']; ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Date Created' ); ?></td>
			<td><?php echo JHTML::_('date', $this->request['date_submitted'], JText::_( 'DATETIME_FORMAT' )); ?></td>
		</tr>
	</table>
	<?php if (!empty($this->request['order_id'])) { ?>
	<br /><br />
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="so_table">
		<tr>
			<th colspan="2"><?php echo JText::_( 'Order Information' ); ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Shipping Cost' ); ?></td>
			<td><?php echo $this->request['shipping_cost']; ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Order Total' ); ?></td>
			<td><?php echo $this->request['order_total']; ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Date Ordered' ); ?></td>
			<td><?php echo JHTML::_('date', $this->request['date_ordered'], JText::_( 'DATETIME_FORMAT' )); ?></td>
		</tr>
	</table>
	<?php } ?>
</div>
<div id="so_right_col">
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="so_table">
		<tr>
			<th colspan="2"><?php echo JText::_( 'Employee Information' ); ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Name' ); ?></td>
			<td><?php echo $this->request['employee_name']; ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Email' ); ?></td>
			<td><?php echo $this->request['employee_email']; ?></td>
		</tr>
	</table>
	<br /><br />
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="so_table">
		<tr>
			<th colspan="2"><?php echo JText::_( 'Account Information' ); ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Number' ); ?></td>
			<td><?php echo $this->request['account_num']; ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Name' ); ?></td>
			<td><?php echo $this->request['account_name']; ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Owner' ); ?></td>
			<td><?php echo $this->request['acct_owner_name']; ?></td>
		</tr>
	</table>
	<br /><br />
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="so_table">
		<tr>
			<th><?php echo JText::_( 'Comments' ); ?></td>
		</tr>
		<?php
		if (!empty($this->comments)) { 
			foreach ($this->comments as $comment) {
			?>
				<tr>
					<td><?php echo $comment['comment_body']; ?><br />
						<div id="comment_footer">
							<?php echo JText::_( 'Posted by' ) . " "; ?>
							<?php echo $comment['employee_name'] . " "; ?> 
							<?php echo JText::_( 'on' ) . " "; ?> 
							<?php echo JHTML::_('date', $comment['date_sent'], JText::_( 'DATETIME_FORMAT' )); ?>
						</div>
					</td>
				</tr>
			<?php
			} 
			?>
		<?php 
		}
		else {
		?>
			<tr>
				<td colspan="2"><?php echo JText::_( 'No comments.' ); ?></td>
			</tr>
		<?php 
		}
		?>
	</table>
	<br /><br />
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="so_table">
		<tr>
			<th colspan="2"><?php echo JText::_( 'Files' ); ?></td>
		</tr>
		<?php
		if (!empty($this->files)) { 
			foreach ($this->files as $file) {
			?>
			<tr>
				<td><a href="<?php echo JURI::base() . ltrim($file['file_location'],'/'); ?>" target="_blank"><?php echo $file['file_name']; ?></a></td>
				<td><?php echo $file['date_posted']; ?></td>
			</tr>
			<?php
			} 
			?>
		<?php 
		}
		else {
		?>
			<tr>
				<td colspan="2"><?php echo JText::_( 'No files.' ); ?></td>
			</tr>
		<?php 
		}
		?>
	</table>
</div>
<div class="clear_both"></div>
</body>
</html>