<?php
/**
 * Place order form for personal details.
 *
 * @package    Wp_Travel_Engine
 * @subpackage Wp_Travel_Engine/includes
 * @author
 */
class Wp_Travel_Engine_Order_Confirmation {

	/**
	 * Initialize the final confirmation form shortcode.
	 *
	 * @since 1.0
	 */
	function init() {
		add_shortcode( 'WP_TRAVEL_ENGINE_BOOK_CONFIRMATION', array( $this, 'wp_travel_engine_confirmation_shortcodes_callback' ) );
	}

	/**
	 * Final confirmation form shortcode callback function.
	 *
	 * @since 1.0
	 */
	function wp_travel_engine_confirmation_shortcodes_callback() {
		if ( is_admin() ) {
			return;
		}

		if ( defined( 'WTE_USE_OLD_BOOKING_PROCESS' ) && WTE_USE_OLD_BOOKING_PROCESS ) {
			wp_die( new \WP_Error( 'WTE_ERROR', __( 'WP Travel Engine no more supports old booking process.', 'wp-travel-engine' ) ) );
			return;
		}

		ob_start();
			wte_get_template( 'traveller-information/template-traveler-info.php' );
		return ob_get_clean();

	}

	/**
	 * Insert new customer.
	 *
	 * @since 1.0
	 */
	function insert_customer( &$order_metas ) {
		 global $wpdb;
		global $wte_cart;
		if ( ! is_admin() ) {
			require_once ABSPATH . 'wp-admin/includes/post.php';
		}
		$ids  = $wte_cart->get_cart_trip_ids();
		$post = $ids['0'];
		$post = get_post( $post );
		if ( wp_travel_engine_use_old_booking_process() ) {
			$post = get_post( $_SESSION['trip-id'] );
		}
		$slug = $post->post_title;
		if ( post_exists( $order_metas['place_order']['booking']['email'], '', '' ) == 0 ) {
			$new_post = array(
				'post_status' => 'publish',
				'post_type'   => 'customer',
				'post_title'  => 'customer',
			);
			$post_id  = wp_insert_post( $new_post );

			foreach ( $order_metas['place_order'] as $key => $value ) {
				$arr[ $key ][1] = $value;
			}
			unset( $arr['booking'] );

			$booked_id[] = $order_metas[0];

			if ( ! isset( $booked_id ) && ! is_array( $booked_id ) ) {
				$booked_id = array();
			}

			update_post_meta( $post_id, 'wp_travel_engine_bookings', $booked_id );
			update_post_meta( $post_id, 'wp_travel_engine_booking_setting', $order_metas );
			update_post_meta( $post_id, 'wp_travel_engine_booked_trip_setting', $arr );

			$customer_post = array(
				'ID'         => $post_id,
				'post_title' => esc_attr( $order_metas['place_order']['booking']['email'] ),
			);
			// Update the post into the database
			$updated = wp_update_post( $customer_post );

			if ( false === $updated ) {
				_e( 'There was an error on update.', 'wp-travel-engine' );
			}
		} else {

			$pid      = get_page_by_title( $order_metas['place_order']['booking']['email'], OBJECT, 'customer' );
			$my_array = get_post_meta( $pid->ID, 'wp_travel_engine_booked_trip_setting', true );

			if ( isset( $my_array ) && $my_array != '' ) {
				$size = sizeof( $my_array['traveler'] );
			} else {
				$my_array[] = '';
				$size       = 0;
			}
			$size++;
			foreach ( $order_metas['place_order'] as $key => $value ) {
				$arr[ $key ][ $size ] = $value;
			}
			unset( $arr['booking'] );
			$a           = array_merge_recursive( $my_array, $arr );
			$my_bookings = get_post_meta( $pid->ID, 'wp_travel_engine_bookings', true );
			$booked_id[] = $order_metas[0];
			if ( ! isset( $booked_id ) && ! is_array( $booked_id ) ) {
				$booked_id = array();
			}
			if ( isset( $my_bookings ) && $my_bookings != '' ) {
				$my_bookings = array_merge_recursive( $my_bookings, $booked_id );
			} else {
				$my_bookings[] = '';
			}
			update_post_meta( $pid->ID, 'wp_travel_engine_booked_trip_setting', $a );
			update_post_meta( $pid->ID, 'wp_travel_engine_bookings', $my_bookings );
		}
		if ( is_user_logged_in() ) {
				$user              = wp_get_current_user();
				$saved_booking_ids = get_user_meta( $user->ID, 'wp_travel_engine_user_bookings', true );
			if ( ! $saved_booking_ids ) {
				$saved_booking_ids = array();
			}
			array_push( $saved_booking_ids, $order_metas[0] );
			update_user_meta( $user->ID, 'wp_travel_engine_user_bookings', $saved_booking_ids );
		}
	}
}
