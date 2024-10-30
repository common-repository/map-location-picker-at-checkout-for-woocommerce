<?php
/**
 * Checkout validation related methods.
 *
 * Author:          Uriahs Victor
 * Created on:      15/11/2022 (d/m/y)
 *
 * @link    https://uriahsvictor.com
 * @since   1.6.10
 * @package Controllers
 * @subpackage Controllers/Checkout_Page
 */
namespace Lpac\Controllers\Checkout_Page;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Lpac\Models\Plugin_Settings\General_Settings;
use Lpac\Models\Plugin_Settings\Store_Locations;

/**
 * Class responsible for checkout page validation.
 *
 * @package Lpac\Controllers\Checkout_Page
 * @since 1.0.0
 */
class Validate {

	/**
	 * Check both $_POST and $fields for a form field.
	 *
	 * A field may sometimes not be in $_POST but be programmatically passed in $field such as in our DCash for WooCommerce plugin.
	 *
	 * @param string $field
	 * @param array  $fields
	 * @return null|string
	 * @since 1.8.7
	 */
	private function findFieldValue( string $field, array $fields ) {

		$post_value = wp_unslash( ( $_POST[ $field ] ?? '' ) );
		if ( is_array( $post_value ) ) {
			$post_value = array_map( 'sanitize_text_field', $post_value );
		} else {
			$post_value = sanitize_text_field( $post_value );
		}

		$field_value = wp_unslash( ( $fields[ $field ] ?? '' ) );
		if ( is_array( $field_value ) ) {
			$field_value = array_map( 'sanitize_text_field', $field_value );
		} else {
			$field_value = sanitize_text_field( $field_value );
		}

		if ( ! empty( $post_value ) ) {
			return $post_value;
		}
		if ( ! empty( $field_value ) ) {
			return $field_value;
		}

		return null;
	}

	/**
	 * Check whether to override our validation logic when a local pickup method is selected at checkout.
	 *
	 * @param array  $fields
	 * @param object $errors
	 * @return bool
	 * @since 1.8.7
	 */
	private function localPickupOverride( array $fields, object $errors ): bool {

		$override_local_pickup = apply_filters( 'lpac_local_pickup_override_map_validation', true, $fields, $errors );

		if ( $override_local_pickup ) {
			if ( function_exists( 'WC' ) ) {
				$chosen_shipping_method = WC()->session->get( 'chosen_shipping_methods' );
				$chosen_shipping_method = $chosen_shipping_method[0] ?? '';

				if ( strpos( $chosen_shipping_method, 'local_pickup' ) !== false ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Check if the latitude or longitude inputs are filled in.
	 *
	 * @since    1.1.0
	 * @param array  $fields The fields array.
	 * @param object $errors The errors object.
	 *
	 * @return void
	 */
	public function validate_location_fields( $fields, $errors ) : void {

		if ( ! is_array( $fields ) && ! is_object( $errors ) ) {
			return;
		}

		if ( General_Settings::forceUseOfMapEnabled() === false ) {
			return;
		}

		/**
		 * The map visibility might be changed via JS or other conditions
		 * So we need to check if its actually shown before trying to validate
		 */
		if ( empty( $this->findFieldValue( 'lpac_is_map_shown', $fields ) ) ) {
			return;
		}

		// Don't validate the location fields when local pickup option is used.
		if ( $this->localPickupOverride( $fields, $errors ) ) {
			return;
		}

		/**
		 * Allow users to override this setting
		 */
		$custom_override = apply_filters( 'lpac_override_map_validation', false, $fields, $errors );

		if ( $custom_override === true ) {
			return;
		}

		$error_msg = '<strong>' . __( 'Please select your location using the Map.', 'map-location-picker-at-checkout-for-woocommerce' ) . '</strong>';

		$error_msg = apply_filters( 'lpac_checkout_empty_cords_error_msg', $error_msg );

		$latitude  = $this->findFieldValue( 'lpac_latitude', $fields );
		$longitude = $this->findFieldValue( 'lpac_longitude', $fields );

		if ( empty( $latitude ) || empty( $longitude ) ) {
			$errors->add( 'map-used-validation', $error_msg );
		}
	}

	/**
	 * Check if places autocomplete feature was used at any point during checkout.
	 *
	 * @since    1.6.13
	 * @param array  $fields The fields array.
	 * @param object $errors The errors object.
	 *
	 * @return void
	 */
	public function validate_places_autocomplete( array $fields, object $errors ) : void {

		if ( General_Settings::isPlacesAutoCompleteEnabled() === false ) {
			return;
		}

		$shipping_methods = $this->findFieldValue( 'shipping_method', $fields );

		if ( is_array( $shipping_methods ) ) {
			$shipping_methods = array_values( $shipping_methods );

			// Don't validate this when local pickup is selected as shipping method.
			foreach ( $shipping_methods as $shipping_method ) {
				if ( strpos( $shipping_method, 'local_pickup' ) !== false ) {
					return;
				}
			}
		}

		$places_autocomplete_used = $this->findFieldValue( 'lpac_places_autocomplete', $fields );

		if ( ! empty( $places_autocomplete_used ) ) {
			return;
		}

		$force_use = General_Settings::get_force_use_places_autocomplete_setting();

		if ( $force_use === false ) {
			return;
		}

		$text = General_Settings::get_force_places_autocomplete_notice_text();
		$errors->add( 'places-autocomplete-validation', "<strong>$text</strong>" );
	}

	/**
	 * Check if the origin store dropdown has a selected value for Store location selector feature in Shipping Locations settings.
	 *
	 * @since    1.6.0
	 * @param array  $fields The fields array.
	 * @param object $errors The errors object.
	 *
	 * @return void
	 */
	public function validateStoreLocationSelectorDropdown( array $fields, object $errors ) :void {

		/**
		 * The store dropdown visibility might be changed via JS or other conditions
		 * So we need to check if its actually shown before trying to validate
		 *
		 * see changeMapVisibility() in checkout-page-map.js
		 */
		// TODO Maybe add an actual field that defines this rather than using the map is shown...there are chances the map is shown and the field is not.
		if ( empty( $this->findFieldValue( 'lpac_is_map_shown', $fields ) ) ) {
			return;
		}

		$enable_store_location_selector = Store_Locations::showStoreSelectorInCheckoutEnabled();

		if ( empty( $enable_store_location_selector ) ) {
			return;
		}

		if ( $this->localPickupOverride( $fields, $errors ) ) {
			return;
		}

		$latitude  = $this->findFieldValue( 'lpac_latitude', $fields );
		$longitude = $this->findFieldValue( 'lpac_longitude', $fields );
		if ( empty( $latitude ) || empty( $longitude ) ) { // Don't validate until customer has selected location on map.
			return;
		}

		$origin_store = $this->findFieldValue( 'lpac_order__origin_store', $fields );
		if ( empty( $origin_store ) ) {
			$error_msg = '<strong>' . __( 'Please select the store location you would like to order from.', 'map-location-picker-at-checkout-for-woocommerce' ) . '</strong>';
			$error_msg = apply_filters( 'lpac_checkout_empty_origin_store_msg', $error_msg );
			$errors->add( 'store-dropdown-validation', $error_msg );
		}

	}

}
