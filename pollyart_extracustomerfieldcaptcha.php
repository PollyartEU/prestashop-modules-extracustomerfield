<?php
/**
 * 2007-2018 Frédéric BENOIST
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 *  @author    Damian Gęsicki
 *  @copyright 2021-2024 Damian Gęsicki
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

if ( ! defined( '_PS_VERSION_' ) ) {
	exit;
}

class Pollyart_ExtraCustomerFieldCaptcha extends Module {
	public function __construct() {
		$this->name = 'pollyart_extracustomerfieldcaptcha';
		$this->author = 'Damian Gęsicki';
		$this->version = '1.0.0';
		$this->need_instance = 0;
		$this->bootstrap = true;
		$this->tab = 'others';
		parent::__construct();

		$this->displayName = $this->l( 'Custom captcha field on register page' );
		$this->ps_versions_compliancy = array(
			'min' => '1.7',
			'max' => _PS_VERSION_
		);
		$this->description = $this->l( 'Adds a captcha field to prevent bots from registering accounts' );
	}

	/**
	 * Install module
	 *
	 * @return bool true if success
	 */
	public function install() {
		if ( Shop::isFeatureActive() ) {
			Shop::setContext( Shop::CONTEXT_ALL );
		}

		if ( ! parent::install()
			|| ! $this->registerHook( 'additionalCustomerFormFields' )
			|| ! $this->registerHook( 'validateCustomerFormFields' )
		) {
			return false;
		}
		return true;
	}


	/**
	 * Add fields in Customer Form
	 *
	 * @param array $params parameters (@see CustomerFormatter->getFormat())
	 *
	 * @return array of extra FormField
	 */
	public function hookAdditionalCustomerFormFields( $params ) {

		$extra_fields = array();
		$extra_fields['custom-captcha'] = ( new FormField )
			->setName( 'custom-captcha' )
			->setType( 'custom-captcha' )
			->setAvailableValues( [ 'comment' => $this->l( 'This field is used to block bots from creating accounts. Leave unchecked' ) ] )
			->setValue( 'custom-captcha' );


		return $extra_fields;
	}

	/**
	 * Validate fields in Customer form
	 *
	 * @param array $params hook call parameters (@see CustomerForm->validateByModules())
	 *
	 * @return array of extra FormField
	 */
	public function hookvalidateCustomerFormFields( $params ) {
		$module_fields = $params['fields'];
		if ( 'custom-captcha' == $module_fields[0]->getValue() ) {
			$module_fields[0]->addError(
				$this->l( 'Suspicious activity detected' )
			);
		}
		return array(
			$module_fields
		);
	}

}
