<?php
/**
 * File responsible for methods that handle adding inline scripts.
 * Author:          Uriahs Victor
 * Created on:      08/06/2024 (d/m/y)
 *
 * @link    https://uriahsvictor.com
 * @package \InlineScriptsController
 * @since   1.0.0
 */

namespace Lpac\Controllers\SiteWide;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Lpac\Models\Plugin_Settings\Store_Locations;

/**
 * Class which defines methods that handle adding inline scripts.
 *
 * @package \Lpac\Controllers\Sitewide\InlineScriptsController
 * @since   1.0.0
 * TODO add all applicable calls to wp_add_inline_script to this class or create similiar classes for the applicable use such as Checkout_Page.
 */
class InlineScriptsController {

	/**
	 * InlineScriptsController constructor.
	 */
	public function __construct() {
		$this->addStoreSelectorShortcodeInlineScripts();
		$this->addPluginVersionInlineScripts();
	}

	/**
	 * Add inline scripts that apply to store selector shortcode.
	 *
	 * @return void
	 * @since 1.10.0
	 */
	public function addStoreSelectorShortcodeInlineScripts() {
		$config = array(
			'enableSearch' => Store_Locations::enableSearchInStoreLocations(),
		);

		$config = wp_json_encode( $config );
		wp_add_inline_script(
			LPAC_PLUGIN_NAME,
			"
			var storeSelectorShortcodeConfig = $config
			"
		);

		// This is needed to fix the dropdown and stop it from duplicating.
		wp_enqueue_style( 'select2' );
	}

	/**
	 * Echo plugin version in console.
	 *
	 * @return void
	 * @since 1.10.0
	 */
	public function addPluginVersionInlineScripts() {

		$plugin_type   = ( LPAC_IS_PREMIUM_VERSION ) ? 'PRO' : 'Free';
		$pluginVersion = LPAC_VERSION;
		wp_add_inline_script(
			LPAC_PLUGIN_NAME,
			"console.log('Kikote - Location Picker at Checkout for WooCommerce {$plugin_type}: v{$pluginVersion}');"
		);
	}

}
