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

JLoader::import('joomla.application.component.model');

class SupplyOrderNotifications
{
	// Models
	private $requestsModel;
	private $commentsModel;
	private $filesModel;
	
	// Variables
	private $request;
	private $comments;
	private $files;
	
	public function __construct ( ) {
		if(!class_exists('SupplyOrderModelRequests')) {
			JLoader::import( 'requests', 'components' . DS . 'com_supplyorder' . DS . 'models' );
		}
		
		if(!class_exists('SupplyOrderModelComments')) {
			JLoader::import( 'comments', 'components' . DS . 'com_supplyorder' . DS . 'models' );
		}
		
		if(!class_exists('SupplyOrderModelFiles')) {
			JLoader::import( 'files', 'components' . DS . 'com_supplyorder' . DS . 'models' );
		}
		
		$this->requestsModel =& JModel::getInstance( 'Requests', 'SupplyOrderModel' );
		$this->commentsModel =& JModel::getInstance( 'Comments', 'SupplyOrderModel' );
		$this->filesModel =& JModel::getInstance( 'Files', 'SupplyOrderModel' );
	}
	
	
	/**
	 * Handles all emails that need to send out request details to various people
	 * 
	 * @param string $to_email
	 * @param string $subject
	 * @param int $request_id 
	 * @return true on success or JError object on failure
	 */
	public function emailRequestDetails ($to_email, $subject, $request_id) {
		$this->request = $this->requestsModel->getRequestCompleteDetail($request_id);
		$this->comments = $this->commentsModel->getComments($request_id);
		$this->files = $this->filesModel->getFiles($request_id);
		
		$mailer =& JFactory::getMailer();
		
		// Set sender
		$config =& JFactory::getConfig();
		$sender = array(
				$config->getValue( 'config.mailfrom' ),
				$config->getValue( 'config.fromname' ) );
		
		$mailer->setSender($sender);
		
		// Set recipient
		$mailer->addRecipient($to_email);
		$mailer->addCC('danny.bouman@hclibrary.org'); // Testing purposes
		
		// Set subject
		$mailer->setSubject("Request #". $request_id ." - $subject");
		
		// Set body
		$body   = self::getTemplate(JPATH_SITE . DS.'components'.DS.'com_supplyorder'.DS.'email_templates'.DS.'request_details.php');
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
	private function getTemplate($file) {
	
		ob_start(); // start output buffer
	
		include $file;
		$template = ob_get_contents(); // get contents of buffer
		ob_end_clean();
		return $template;
	
	
	}
	
}
?>