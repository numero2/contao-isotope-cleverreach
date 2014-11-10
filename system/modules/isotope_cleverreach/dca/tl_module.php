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


/**
 * Change palettes
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'iso_enableCleverReachSignup';

$cleverPalette = ',iso_order_conditions_position,iso_enableCleverReachSignup';

$GLOBALS['TL_DCA']['tl_module']['palettes']['iso_checkout'] = str_replace(',iso_order_conditions_position', $cleverPalette, $GLOBALS['TL_DCA']['tl_module']['palettes']['iso_checkout']);
$GLOBALS['TL_DCA']['tl_module']['palettes']['iso_checkoutmember'] = str_replace(',iso_order_conditions_position', $cleverPalette, $GLOBALS['TL_DCA']['tl_module']['palettes']['iso_checkoutmember']);
$GLOBALS['TL_DCA']['tl_module']['palettes']['iso_checkoutguest'] = str_replace(',iso_order_conditions_position', $cleverPalette, $GLOBALS['TL_DCA']['tl_module']['palettes']['iso_checkoutguest']);
$GLOBALS['TL_DCA']['tl_module']['palettes']['iso_checkoutboth'] = str_replace(',iso_order_conditions_position', $cleverPalette, $GLOBALS['TL_DCA']['tl_module']['palettes']['iso_checkoutboth']);


/**
 * Add subpalettes
 */
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['iso_enableCleverReachSignup'] = 'iso_cleverReachNLCheck, iso_cleverReachAPIKey, iso_cleverReachGroup';


/**
 * Add fields
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['iso_enableCleverReachSignup'] = array
(
    'label'                     => &$GLOBALS['TL_LANG']['tl_module']['iso_enableCleverReachSignup'],
    'exclude'                   => false,
    'inputType'                 => 'checkbox',
    'eval'                      => array('submitOnChange'=>true, 'tl_class'=>'clr'),
    'sql'                       => "char(1) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_module']['fields']['iso_cleverReachNLCheck'] = array
(
    'label'                     => &$GLOBALS['TL_LANG']['tl_module']['iso_cleverReachNLCheck'],
    'exclude'                   => false,
    'inputType'                 => 'select',
    'options_callback'			=> array( 'tl_module_iso_clever_reach', 'getFormCheckbox' ),
    'eval'                      => array('mandatory'=>true, 'tl_class'=>'w50'),
    'sql'                       => "int(10) unsigned NOT NULL default '0'",
);

$GLOBALS['TL_DCA']['tl_module']['fields']['iso_cleverReachAPIKey'] = array
(
    'label'                     => &$GLOBALS['TL_LANG']['tl_module']['iso_cleverReachAPIKey'],
    'exclude'                   => false,
    'inputType'                 => 'text',
    'eval'                      => array('mandatory'=>true, 'submitOnChange'=>true, 'tl_class'=>'w50'),
    'sql'                       => "varchar(255) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_module']['fields']['iso_cleverReachGroup'] = array
(
    'label'                     => &$GLOBALS['TL_LANG']['tl_module']['iso_cleverReachGroup'],
    'exclude'                   => false,
    'inputType'                 => 'select',
    'options_callback'			=> array( 'tl_module_iso_clever_reach', 'getReceiverGroups' ),
    'eval'                      => array('includeBlankOption'=>true, 'mandatory'=>true, 'tl_class'=>'w50'),
    'sql'                       => "int(10) unsigned NOT NULL default '0'",
);


class tl_module_iso_clever_reach {


	public function getFormCheckbox( \DataContainer $dc ){

		$fields = NULL;
		$fields = \Database::getInstance()->prepare(" SELECT id, label, name FROM tl_form_field WHERE type = 'checkbox' AND pid = ? ")->execute( $dc->activeRecord->iso_order_conditions );

		$aFields = array();

		while( $fields->next() ) {
			$aFields[ $fields->id ] = $fields->name;
		}

		return $aFields;
	}


	public function getReceiverGroups( \DataContainer $dc ) {

		if( empty($dc->activeRecord->iso_cleverReachAPIKey) )
			return NULL;

		$groups = NULL;
		$groups = \Isotope\CleverReachAPI::getReceiverGroups( $dc->activeRecord->iso_cleverReachAPIKey );

		return $groups;
	}
}