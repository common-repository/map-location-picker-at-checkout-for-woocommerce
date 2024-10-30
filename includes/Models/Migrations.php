<?php
/**
 * Handles migrations.
 *
 * When old settings should be moved to new formats to renamed.
 *
 * Author:          Uriahs Victor
 * Created on:      06/08/2022 (d/m/y)
 *
 * @link    https://uriahsvictor.com
 * @since   1.6.2
 * @package Lpac/Models
 */
namespace Lpac\Models;

if ( ! defined( 'WPINC' ) ) {
	die;
}

use Lpac\Models\Plugin_Settings\Store_Locations;

/**
 * Migrations Class.
 */
class Migrations {

	/**
	 * Plugin current version.
	 *
	 * @var string
	 */
	private $plugin_version = LPAC_VERSION;

	/**
	 * Version at which the plugin was installed.
	 *
	 * @var string
	 */
	private $installed_at = '';

	/**
	 * Constructor method
	 *
	 * @return void
	 */
	public function __construct() {
		$this->installed_at = get_option( 'lpac_installed_at_version', '1.0.0' );
	}

	/**
	 * Add new address field to store locations array.
	 *
	 * Remove function when v1.6 usage is depleted.
	 *
	 * @since 1.6.2
	 * @return void
	 */
	public function add_address_field_to_store_locations() : void {

		if ( version_compare( $this->installed_at, '1.6.2', '>=' ) ) {
			return;
		}

		$migrated = get_option( 'lpac_migrated__add_address_to_store_locations' );
		if ( $migrated ) {
			return;
		}

		$store_locations = get_option( 'lpac_store_locations', array() );
		if ( empty( $store_locations ) ) {
			return;
		}

		foreach ( $store_locations as $key => &$store ) {
			$store = array_merge(
				array_slice( $store, 0, 3, true ),
				array( 'store_address_text' => '' ),
				array_slice( $store, 3, null, true )
			);
		}
		unset( $store );

		update_option( 'lpac_store_locations', $store_locations );
		update_option( 'lpac_migrated__add_address_to_store_locations', true );
	}

	/**
	 * Add "Calculate per <unit>" checkbox option to distance range repeater.
	 *
	 * Remove function when v1.6 usage is depleted.
	 *
	 * @return void
	 * @since 1.6.9
	 */
	public function add_should_calculate_per_distance_unit_field() : void {

		if ( version_compare( $this->installed_at, '1.6.9', '>=' ) ) {
			return;
		}

		$migrated = get_option( 'lpac_migrated__add_should_calculate_per_distance_unit_field' );
		if ( $migrated ) {
			return;
		}

		$ranges = get_option( 'lpac_cost_by_distance_range_rangelist', array() );
		if ( empty( $ranges ) ) {
			return;
		}

		foreach ( $ranges as $key => &$range ) {
			$range = array_merge(
				array_slice( $range, 0, 3, true ),
				array( 'should_calculate_per_distance_unit_checkbox' => '' ),
				array_slice( $range, 3, null, true )
			);
		}
		unset( $range );

		update_option( 'lpac_cost_by_distance_range_rangelist', $ranges );
		update_option( 'lpac_migrated__add_should_calculate_per_distance_unit_field', true );
	}

	/**
	 * Enable Cost by Distance Standard if previously only cost by distance feature was enabled which is the same thing.
	 *
	 * @return void
	 * @since 1.8.0
	 */
	public function activateCostByDistanceStandard() {

		if ( version_compare( $this->installed_at, '1.8.0', '>=' ) ) {
			return;
		}

		// Only show notice for premium installs
		if ( ! LPAC_IS_PREMIUM_VERSION ) {
			return;
		}

		$migrated = get_option( 'lpac_migrated__cost_by_distance_standard' );
		if ( $migrated ) {
			return;
		}

		$cost_by_distance = get_option( 'lpac_enable_shipping_cost_by_distance_feature' );
		$cost_by_distance = filter_var( $cost_by_distance, FILTER_VALIDATE_BOOLEAN );

		$cost_by_distance_range = get_option( 'lpac_enable_cost_by_distance_range' );
		$cost_by_distance_range = filter_var( $cost_by_distance_range, FILTER_VALIDATE_BOOLEAN );

		$cost_by_store_distance = get_option( 'lpac_enable_cost_by_store_distance' );
		$cost_by_store_distance = filter_var( $cost_by_store_distance, FILTER_VALIDATE_BOOLEAN );

		if ( $cost_by_distance === true && ( $cost_by_distance_range === false && $cost_by_store_distance === false ) ) {
			update_option( 'lpac_enable_cost_by_distance_standard', 'yes' );
		}
		update_option( 'lpac_migrated__cost_by_distance_standard', true );

	}

	/**
	 * Set the shipping restriction option
	 *
	 * @return void
	 * @since 1.8.2
	 */
	public function setShippingRestrictionSettings() {

		// Only do this for premium installs
		if ( ! LPAC_IS_PREMIUM_VERSION ) {
			return;
		}

		// Dont run this if plugin was installed on or after v1.8.2
		if ( version_compare( $this->installed_at, '1.8.2', '>=' ) ) {
			return;
		}

		$migrated = get_option( 'lpac_migrated__enabled_shipping_restrictions_option' );
		if ( $migrated ) {
			return;
		}

		$regions_order_total_restrictions = get_option( 'lpac_regions_min_max_order_total' );

		if ( ! empty( $regions_order_total_restrictions ) ) {
			update_option( 'lpac_enable_shipping_restrictions', 'yes' );
		} else {
			update_option( 'lpac_enable_shipping_restrictions', 'no' );
		}
		update_option( 'lpac_migrated__enabled_shipping_restrictions_option', true );
	}

	/**
	 * Add the new shipping method key to saved store locations option so users can see it in the admin area.
	 *
	 * @return void
	 * @since 1.8.9
	 */
	public function addNewStoreLocationsShippingMethodField() {

		// Dont run this if plugin was installed on or after v1.8.9
		if ( version_compare( $this->installed_at, '1.8.9', '>=' ) ) {
			return;
		}

		$migrated = get_option( 'kikote_migrated__store_locations_shipping_method' );
		if ( $migrated ) {
			return;
		}

		$current_store_locations = Store_Locations::getStoreLocations();

		foreach ( $current_store_locations as $key => &$store_location_details ) {
			$store_location_details['store_locations_shipping_method_select'] = array();
		}

		unset( $store_location_details );

		update_option( 'lpac_store_locations', $current_store_locations );
		update_option( 'kikote_migrated__store_locations_shipping_method', true );
	}

}
