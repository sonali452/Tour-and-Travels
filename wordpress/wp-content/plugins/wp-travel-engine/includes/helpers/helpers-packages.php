<?php
/**
 * Helper functions for trip packages.
 *
 * @since 5.0.0
 */

namespace WPTravelEngine\Packages;

use WPTravelEngine\Posttype\Trip;

function get_packages_by_trip_id( $trip_id ) {
	// Get Trip package Ids.
	global $wtetrip;

	if ( $trip_id ) {
		$wtetrip = \wte_get_trip( $trip_id );
	}
	$packages = array();

	if ( $wtetrip instanceof \WPTravelEngine\Posttype\Trip ) {
		$packages = $wtetrip->packages;
	} else {
		$package_ids = \get_post_meta( $trip_id, 'packages_ids', ! 0 );

		if ( ! is_array( $package_ids ) ) {
			$package_ids = array();
		}

		$packages = \get_posts(
			array(
				'post_type' => 'trip-packages',
				'include'   => $package_ids,
			)
		);

		$_packages = array();
		foreach ( $packages as $package ) {
			$_packages[ $package->ID ] = $package;
		}

		$packages = $_packages;

	}

	return $packages;
}

function get_package_by_id( $package_id ) {
	return get_post( $package_id );
}

function get_package_dates_by_package_id( $package ) {

	if ( $package instanceof \WP_Post ) {
		$_package = $package;
	} else {
		$_package = WP_Post::get_instance( $package );
	}

	// @TODO: Create package date class.
	$package_dates = $_package->{'package-dates'};

	return is_array( $package_dates ) ? $package_dates : array();

}

function get_booked_seats_number_by_date( $trip_id, $date = null ) {
	$fsd_booked_seats = get_post_meta( $trip_id, 'wte_fsd_booked_seats', true );
	if ( is_null( $date ) ) {
		return is_array( $fsd_booked_seats ) ? $fsd_booked_seats : array();
	}

	return isset( $fsd_booked_seats[ $date ] ) ? $fsd_booked_seats[ $date ] : array(
		'booked'  => 0,
		'datestr' => $date,
	);
}

function get_trip_lowest_price_package( $trip_id = null ) {
	global $wtetrip;
	$_wtetrip = $wtetrip;

	if ( $trip_id ) {
		$_wtetrip = Trip::instance( $trip_id );
	}

	return $_wtetrip->{'default_package'};
}

function get_trip_lowest_price( $trip_id ) {
	$lowest_cost_package = get_trip_lowest_price_package( $trip_id );

	if ( is_null( $lowest_cost_package ) ) {
		return 0;
	}
	$package_categories = (object) $lowest_cost_package->{'package-categories'};

	$primary_pricing_category = get_option( 'primary_pricing_category', 0 );

	$category_price = $package_categories->prices[ $primary_pricing_category ];
	if ( isset( $package_categories->enabled_sale[ $primary_pricing_category ] ) && '1' === $package_categories->enabled_sale[ $primary_pricing_category ] ) {
		$category_price = $package_categories->sale_prices[ $primary_pricing_category ];
	}

	return (float) $category_price;
}

function get_trip_lowest_price_by_package_id( $package_id ) {

	$lowest_cost_package = get_post( $package_id );

	if ( ! $lowest_cost_package || 'trip-packages' !== $lowest_cost_package->post_type ) {
		return 0;
	}
	$package_categories = (object) $lowest_cost_package->{'package-categories'};

	$primary_pricing_category = get_option( 'primary_pricing_category', 0 );

	$category_price = isset( $package_categories->prices[ $primary_pricing_category ] ) ? $package_categories->prices[ $primary_pricing_category ] : 0;
	if ( isset( $package_categories->enabled_sale[ $primary_pricing_category ] ) && '1' === $package_categories->enabled_sale[ $primary_pricing_category ] ) {
		$category_price = $package_categories->sale_prices[ $primary_pricing_category ];
	}

	return (float) $category_price;
}

function get_packages_pricing_categories() {
	global $wpdb;

	$pricing_taxonomy = 'trip-packages-categories';

	$results = wp_cache_get( 'trip_package_categories', 'wptravelengine' );
	if ( ! $results ) {
		$results = $wpdb->get_results( "SELECT {$wpdb->terms}.term_id, {$wpdb->terms}.name FROM {$wpdb->term_taxonomy} INNER JOIN {$wpdb->terms} ON {$wpdb->term_taxonomy}.term_id = {$wpdb->terms}.term_id WHERE taxonomy = 'trip-packages-categories'" );

		if ( empty( $results ) && is_array( $results ) && function_exists( 'wp_insert_term' ) ) {
			$term = wp_insert_term( 'Adult', $pricing_taxonomy, array( 'slug' => 'adult' ) );
			if ( ! \is_wp_error( $term ) ) {
				update_option( 'primary_pricing_category', $term['term_id'] );
			}
			$results = $wpdb->get_results( $query );
		}
		wp_cache_add( 'trip_package_categories', $results, 'wptravelengine' );
	}

	return $results;
}
