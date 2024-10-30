<?php
/**
 * Handles saving of location details to the database.
 *
 * Author:          Uriahs Victor
 * Created on:      16/10/2021 (d/m/y)
 *
 * @link    https://uriahsvictor.com
 * @since   ..
 * @package Lpac/Models
 */

namespace Lpac\Models;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Lpac\Controllers\Map_Visibility_Controller;
use Lpac\Helpers\Functions;
use WC_Order;

/**
 * Location_Details class.
 *
 * Handles saving of latitude and longitude.
 */
class Location_Details {

	/**
	 * Validate the Map's visibility setting to prevent manipulations via the DOM.
	 *
	 * @param int   $order_id
	 * @param array $data
	 * @return void
	 */
	public function saveOrderMeta( int $order_id, array $data ) {

		$show      = Map_Visibility_Controller::lpac_show_map( 'checkout' );
		$post_data = $_POST;
		$map_shown = $post_data['lpac_is_map_shown'] ?? '';

		if ( $show === false ) {
			return;
		}

		// If we're hiding the map using the places autocomplete feature then we need to ALLOW the coordinates to be saved.
		$places_autocomplete_hidemap = get_option( 'lpac_places_autocomplete_hide_map' );

		if ( empty( $map_shown ) && $places_autocomplete_hidemap !== 'yes' ) {
			return;
		}

		$lat = $post_data['lpac_latitude'] ?? 0.0;
		$lat = Functions::normalize_coordinates( $lat );

		$long = $post_data['lpac_longitude'] ?? 0.0;
		$long = Functions::normalize_coordinates( $long );

		$order = wc_get_order( $order_id );
		$order = $this->save_order_meta_cords( $order, $lat, $long );
		$order = $this->save_order_fulfillment_origin( $order, $post_data );
		$order = $this->save_places_autocomplete( $order, $post_data );
		$order->save();
	}

	/**
	 * Save the coordinates to the database.
	 *
	 * @since    1.0.0
	 * @param int $order_id The order id.
	 */
	/**
	 *
	 * @param WC_Order $order
	 * @param float    $lat
	 * @param float    $long
	 * @return WC_Order
	 */
	public function save_order_meta_cords( WC_Order $order, float $lat, float $long ) : WC_Order {

		if ( empty( $lat ) || empty( $long ) ) {
			return $order;
		}

		$order->add_meta_data( 'lpac_latitude', sanitize_text_field( $lat ) );
		$order->add_meta_data( 'lpac_longitude', sanitize_text_field( $long ) );
		return $order;
	}

	/**
	 * Save whether the Places Autocomplete feature was used.
	 *
	 * The value saved is a 1 or 0. 1 meaning yes and 0 meaning no.
	 *
	 * @param WC_Order $order_id
	 * @param array    $data
	 * @return WC_Order
	 */
	private function save_places_autocomplete( WC_Order $order, array $data ) : WC_Order {
		$places_autocomplete_used = $_POST['lpac_places_autocomplete'] ?? '';
		$order->add_meta_data( '_lpac_places_autocomplete', sanitize_text_field( $places_autocomplete_used ) );
		return $order;
	}

	/**
	 * Save the order delivery origin to the DB.
	 *
	 * @param WC_Order $order
	 * @param array    $post_data
	 * @return WC_Order
	 */
	private function save_order_fulfillment_origin( WC_Order $order, array $post_data ) : WC_Order {
		$store_origin_id = $post_data['lpac_order__origin_store'] ?? '';

		if ( empty( $store_origin_id ) ) {
			return $order;
		}

		$store_locations = get_option( 'lpac_store_locations', array() );
		if ( empty( $store_locations ) ) {
			return $order;
		}

		$store_location_ids = array_column( $store_locations, 'store_location_id' );

		$key                  = array_search( $store_origin_id, $store_location_ids, true );
		$store_origin_id      = $store_locations[ $key ]['store_location_id'] ?? '';
		$store_origin_name    = $store_locations[ $key ]['store_name_text'] ?? '';
		$store_origin_address = $store_locations[ $key ]['store_address_text'] ?? '';

		$order->add_meta_data( '_lpac_order__origin_store_id', sanitize_text_field( $store_origin_id ) );
		$order->add_meta_data( '_lpac_order__origin_store_name', sanitize_text_field( $store_origin_name ) );
		$order->add_meta_data( '_kikote_order__origin_store_address', sanitize_text_field( $store_origin_address ) );

		return $order;
	}

}
