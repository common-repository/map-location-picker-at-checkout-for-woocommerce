<?php
/**
 * Model file used to get the store locations settings of the plugin.
 *
 * Author:          Uriahs Victor
 * Created on:      18/02/2023 (d/m/y)
 *
 * @link    https://uriahsvictor.com
 * @since   1.7.0
 * @package Models
 */

namespace Lpac\Models\Plugin_Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class for retrieving Store Locations settings.
 *
 * @package Lpac\Models\Plugin_Settings
 * @since 1.7.0
 */
class Store_Locations {

	/**
	 * Get saved Store Locations.
	 *
	 * @return array
	 * @since 1.7.0
	 */
	public static function getStoreLocations(): array {
		return get_option( 'lpac_store_locations', array() );
	}

	/**
	 * Get the "Display Store Selector on Checkout Page" option.
	 *
	 * @return bool
	 * @since 1.8.2
	 */
	public static function showStoreSelectorInCheckoutEnabled(): bool {
		$value = get_option( 'lpac_enable_store_location_selector', false );
		return filter_var( $value, FILTER_VALIDATE_BOOLEAN );
	}

	/**
	 * Get the "Allow Searching of Store Locations" option.
	 *
	 * @return bool
	 * @since 1.10.0
	 */
	public static function enableSearchInStoreLocations(): bool {
		$value = get_option( 'lpac_enable_search_in_store_locations', false );
		return filter_var( $value, FILTER_VALIDATE_BOOLEAN );
	}
}
