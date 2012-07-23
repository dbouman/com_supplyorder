<?php 

/**
 * Supply Order Component for Joomla! 1.5
 * Install information
 * @version 1.5.0
 * @author Howard County Library
 * @package com_supplyorder
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 **/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


function com_install()
{
	?>
<p>
	<strong>Supply Order component has been successfully installed.</strong>
</p>
<p>
	The supply order component allows for staff members to request items to
	be approved and purchased.<br /> Every request will go through a
	different approval process depending on the approval level needed by
	the order and the level of the employee requesting the order.<br />
	Conditions: <br /> 1. Less then $1000 then the account owner or 1st
	approver will approve the request and the request will move to
	Accounting Dept.<br /> 2. More then $1000 and less then $3000, the
	request will go to 1st approver (in this account owner) then will move
	to 2nd approver then after the 2nd approval this request will go to
	Accounting Dept.<br />
</p>
<?php
  }
?>