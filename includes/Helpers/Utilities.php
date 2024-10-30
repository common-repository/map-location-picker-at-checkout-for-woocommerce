<?php
/**
 * File responsible for creating utility functions.
 *
 * Author:          Uriahs Victor
 * Created on:      06/05/2023 (d/m/y)
 *
 * @link    https://uriahsvictor.com
 * @since   1.7.5
 * @package Helpers
 */

namespace Lpac\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Lpac\Models\Plugin_Settings\Store_Locations;

/**
 * Class responsible for creating utility methods.
 *
 * @package Lpac\Helpers
 * @since 1.7.5
 */
class Utilities {

	/**
	 * Convert Meters to Kilometers
	 *
	 * @param int $meters
	 * @return float
	 */
	public static function getKilometers( int $meters ) {
		return (float) $meters / 1000;
	}

	/**
	 * Convert Kilometers to miles
	 *
	 * @param float $kilometers
	 * @return float
	 * @since 1.5.2
	 * @since 1.6.3 Cast param to float instead of integer.
	 */
	public static function getMilesFromKilometers( float $km ) : float {
		$miles_conversion = apply_filters( 'lpac_distance_matrix_miles_conversion_rate', 0.621371 );
		return (float) $km * $miles_conversion;
	}

	/**
	 * Get saved store details based on the passed store ID.
	 *
	 * @param string $store_id
	 * @return array $store_details The details of the store.
	 * @since 1.8.2
	 */
	public static function getStoreDetailsFromID( string $store_id ) {

		$store_locations = Store_Locations::getStoreLocations();
		$store_details   = array();

		foreach ( $store_locations as $store_location ) {
			if ( $store_id === $store_location['store_location_id'] ) {
				$store_details = $store_location;
				break;
			}
		}

		return $store_details;
	}
}
