<?php
/**
 * Holds general notices for user.
 *
 * Author:          Uriahs Victor
 * Created on:      11/05/2022 (d/m/y)
 *
 * @link    https://uriahsvictor.com
 * @since   1.5.4
 * @package Lpac/Notices
 */

namespace Lpac\Notices;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * General Notices Class.
 */
class General_Notices extends Notice {

	/**
	 * Class constructor
	 *
	 * @return void
	 */
	public function __construct() {
		$this->create_translators_needed_notice();
		$this->create_dps_released_notice();
		$this->createEcommerceCommunityNotice();
		$this->createPrintusReleaseNotice();
		$this->createShippingRefactoredNotice();
		$this->createWCBlocksIncompatibilityNotice();
	}

	/**
	 * Create notice requesting translation help.
	 *
	 * @return void
	 */
	private function create_translators_needed_notice() {

		$days_since_installed = $this->get_days_since_installed();

		// Show notice after 70 days
		if ( $days_since_installed < 70 ) {
			return;
		}

		// Show this notice only if the review notice has been dismissed
		if ( ! in_array( 'leave_review_notice_1', $this->get_dismissed_notices() ) ) {
			return;
		}

		$content = array(
			'title' => esc_html__( 'We Need Your Help', 'map-location-picker-at-checkout-for-woocommerce' ) . ' 🙏',
			'body'  => esc_html__( 'Do you speak a language beside English? If so, then please help translate Kikote to your native language; this will help other users who know your native language, but speak little to no English, better navigate and set up the plugin. Plus, you will get a cool "Translation Contributor" badge on your WordPress.org profile', 'map-location-picker-at-checkout-for-woocommerce' ) . ' 🚀',
			'cta'   => esc_html__( 'I can help', 'map-location-picker-at-checkout-for-woocommerce' ),
			'link'  => esc_attr( 'https://translate.wordpress.org/projects/wp-plugins/map-location-picker-at-checkout-for-woocommerce/' ),
		);

		$this->create_notice_markup( 'help_translate_lpac', $content );
	}

	/**
	 * Create a notice letting users know about our latest plugin.
	 *
	 * since 1.6.13
	 */
	private function create_dps_released_notice() {

		$days_since_installed = $this->get_days_since_installed();

		// Show notice after 24 days
		if ( $days_since_installed < 24 ) {
			return;
		}

		$content = array(
			'title' => esc_html__( 'Say hello to Chwazi - Delivery & Pickup Scheduling for WooCommerce!', 'map-location-picker-at-checkout-for-woocommerce' ) . ' 🚀',
			'body'  => esc_html__( 'Hey! We have a new plugin that helps you further optimize your store — by letting customers select the date and time they\'d like their Delivery or Pickup order. Give it a shot and let me know how it can be improved to better serve you!', 'map-location-picker-at-checkout-for-woocommerce' ),
			'link'  => esc_attr( 'https://chwazidatetime.com/' ),
		);

		$this->create_notice_markup( 'dps_released', $content );
	}

	/**
	 * Create notice informing uses of discord server.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	private function createEcommerceCommunityNotice() {

		$days_since_installed = $this->get_days_since_installed();

		// Show notice after 14 days
		if ( $days_since_installed < 14 ) {
			return;
		}

		$content = array(
			'title' => esc_html__( 'Join our E-Commerce Support Community', 'map-location-picker-at-checkout-for-woocommerce' ) . ' 🚀',
			'body'  => sprintf( esc_html__( 'Meet and chat with store owners just like yourself from around the world. Discuss on ways to help grow sales, plugin recommendations, tips and tricks and more. %1$1sGrow your store today.%2$2s', 'map-location-picker-at-checkout-for-woocommerce' ), '<strong>', '</strong>' ),
			'cta'   => esc_html__( 'Learn more', 'map-location-picker-at-checkout-for-woocommerce' ),
			'link'  => esc_attr( 'https://lpacwp.com/e-commerce-support-community/?utm_source=plugin-notice&utm_medium=wp-dashboard&utm_campaign=ecom-community' ),
		);

		$this->create_notice_markup( 'fb_group', $content );
	}

	/**
	 * Create Printus released notice.
	 *
	 * @return void
	 */
	private function createPrintusReleaseNotice() {

		$days_since_installed = $this->get_days_since_installed();

		// Show notice after 2 days
		if ( $days_since_installed < 2 ) {
			return;
		}

		$content = array(
			'title' => esc_html__( '[NEW] Printus - Cloud Printing Plugin for WooCommerce', 'map-location-picker-at-checkout-for-woocommerce' ) . ' 🚀',
			'body'  => sprintf( esc_html__( 'Print WooCommerce receipts, invoices or package labels to ANY printer as soon as a new order comes in.', 'map-location-picker-at-checkout-for-woocommerce' ), '📈', '<strong>', '</strong>', '%' ),
			'link'  => esc_attr( 'https://printus.cloud/?utm_source=banner&utm_medium=kikotenotice&utm_campaign=cross-sell' ),
		);

		$this->create_notice_markup( 'printus_launch_notice', $content );
	}

	/**
	 * Create Refactor notice for v1.8.0.
	 *
	 * @return void
	 */
	private function createShippingRefactoredNotice() {

		$installed_at = $this->get_installed_at_version();

		// Only show notice to older users.
		if ( version_compare( $installed_at, '1.8.0', '>=' ) ) {
			return;
		}

		// Only show notice for premium installs
		if ( ! LPAC_IS_PREMIUM_VERSION ) {
			return;
		}

		$content = array(
			'title' => esc_html__( '[NOTE] Kikote - Location Picker at Checkout for WooCommerce', 'map-location-picker-at-checkout-for-woocommerce' ),
			'body'  => sprintf( esc_html__( 'This update comes with major refactoring changes to the Kikote shipping workflows. If you\'re making use of Cost By: Region, Distance, or Store Location, then please double check that your shipping is working as expected. If you have any issues then please submit a support request.', 'map-location-picker-at-checkout-for-woocommerce' ), '📈', '<strong>', '</strong>', '%' ),
			'link'  => esc_attr( 'https://lpacwp.com/contact' ),
			'cta'   => esc_html__( 'Get Support', 'map-location-picker-at-checkout-for-woocommerce' ),
		);

		$this->create_notice_markup( 'v180_refactor_notice', $content );
	}

	/**
	 * Add notice informing users about WooCommerce Blocks Checkout incompatibility.
	 *
	 * @return void
	 * @since 1.9.1
	 */
	private function createWCBlocksIncompatibilityNotice(): void {

		$page_id = wc_get_page_id( 'checkout' );

		if ( has_block( 'woocommerce/checkout', $page_id ) === false ) {
			return;
		}

		$content = array(
			'title' => __( 'Kikote - WooCommerce Blocks Checkout Not Supported', 'map-location-picker-at-checkout-for-woocommerce' ),
			'body'  => __( 'Hey! It looks like you are making use of the WooCommerce Blocks Checkout. Unfortunately, its incompatible with Kikote. You need to switch to the classic checkout to use the plugin features.', 'map-location-picker-at-checkout-for-woocommerce' ),
			'cta'   => __( 'Show me how', 'map-location-picker-at-checkout-for-woocommerce' ),
			'link'  => 'https://lpacwp.com/docs/switching-to-classic-checkout/',
		);

		$this->create_notice_markup( 'wc_blocks_incompatible', $content );
	}
}
