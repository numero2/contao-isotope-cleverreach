<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package   Isotope Clever Reach
 * @author    Benny Born <benny.born@numero2.de>
 * @license   LGPL
 * @copyright 2014 numero2 - Agentur für Internetdienstleistungen
 */

namespace Isotope;

use Isotope\Model\Config;
use Isotope\Model\ProductCollection\Order;


class CleverReachAPI extends \Controller {


	private static $wsdlURL = "http://api.cleverreach.com/soap/interface_v5.1.php?wsdl";


	/*
	 * Returns a list of all available receiver groups
	 *
	 * @param apiKey
	 * @returns array
	 */
	public static function getReceiverGroups( $apiKey ) {

		$api = NULL;
		$api = new \SoapClient( self::$wsdlURL );

		$result = $api->groupGetList( $apiKey );

		if( !empty($result) && $result->status == "SUCCESS" ) {

			$list = array();

			foreach( $result->data as $group ) {
				$list[ $group->id ] = $group->name;
			}

			return $list;
		}

		return array();
	}


	/*
	 * Check if customer should be subscribed to newsletter
	 *
	 * @param objOrder
	 * @param arrTokens
	 */
	public function checkSubscription( Order $objOrder, $arrTokens ) {

		// find checkout module for current page
		global $objPage;

		$module = NULL;
		$module = \Database::getInstance()->prepare("SELECT module.iso_cleverReachNLCheck, module.iso_cleverReachAPIKey, module.iso_cleverReachGroup, field.name AS form_field_name FROM tl_module AS module JOIN tl_layout layout ON (layout.pid = module.pid) JOIN tl_form_field field ON (field.id = module.iso_cleverReachNLCheck) WHERE module.type = 'iso_checkout' AND module.iso_enableCleverReachSignup = 1 AND module.iso_cleverReachNLCheck != '' AND module.iso_cleverReachAPIKey != '' AND module.iso_cleverReachGroup != '' AND layout.id = ?")->limit(1)->execute( $objPage->layout );

		// check if checkbox was selected
		if( !empty($arrTokens['form_'.$module->form_field_name]) ) {

			// subscribe user to specified goup
			$api = NULL;
			$api = new \SoapClient( self::$wsdlURL );

			$email = $arrTokens['billing_email'];

			$recipient = array(
     			"email" => $email
 			,	"registered" => time()
 			,	"activated" => time()
 			,	"source" => "Contao Isotope Onlineshop"
			);
 
			$result = $api->receiverAdd( $module->iso_cleverReachAPIKey, $module->iso_cleverReachGroup, $recipient );

			if( $result->status == "SUCCESS" ) {

				$this->log('Customer '.$email.' successfully subscribed to newsletter', __METHOD__, TL_GENERAL);

			} else {

				$this->log('Customer '.$email.' could not be subscribed to newsletter', __METHOD__, TL_ERROR);
			}
		}
	}
}
