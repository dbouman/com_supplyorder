<?php 

/**
 * Supply Order Component for Joomla! 1.5
 * Helper function for handling all email notifications
 * @version 1.5.0
 * @author Howard County Library
 * @package com_supplyorder
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 **/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class SupplyOrderNotifications
{
	
	/**
	 * Handles all emails that need to send out request details to various people
	 * 
	 * @param string $to_email
	 * @param string $subject
	 * @param array $request includes all request details for display in email message
	 * @param array $comments includes all comments for display in email message
	 * @param array $files includes all files for display in email message
	 * @return true on success or JError object on failure
	 */
	static public function emailRequestDetails ($to_email, $subject, $request, $comments, $files) {
		$mailer =& JFactory::getMailer();
		
		// Set sender
		$config =& JFactory::getConfig();
		$sender = array(
				$config->getValue( 'config.mailfrom' ),
				$config->getValue( 'config.fromname' ) );
		
		$mailer->setSender($sender);
		
		// Set recipient
		$mailer->addRecipient($to_email);
		
		// Set subject
		$mailer->setSubject("Request #". $request['request_id'] ." - $subject");
		
		// Set body
		$body   = self::getTemplate('/components/com_supplyorder/email_templates/request_details.php');
		$mailer->isHTML(true);
		$mailer->Encoding = 'base64';
		$mailer->setBody($body);
		
		$send =& $mailer->Send();
		
		return $send;
	}

	/**
	 * Renders specific email template
	 * @param string $file location of email template
	 * @return string template as rendered variable
	 */
	static private function getTemplate($file) {
	
		ob_start(); // start output buffer
	
		include $file;
		$template = ob_get_contents(); // get contents of buffer
		ob_end_clean();
		return $template;
	
	
	}
	
}
?>