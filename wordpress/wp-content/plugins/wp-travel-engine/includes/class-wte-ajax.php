<?php
/**
 * WP Travel Engine AJAX
 *
 * @package WP_Travel_Engine
 *
 * @since 2.2.6
 */
class WTE_Ajax {

	public function __construct() {

		// Cart Ajax handlers.
		add_action( 'wp_ajax_wte_add_trip_to_cart', array( $this, 'wte_add_trip_to_cart' ) );
		add_action( 'wp_ajax_nopriv_wte_add_trip_to_cart', array( $this, 'wte_add_trip_to_cart' ) );

		/**
		 * Clone Existing Trips
		 *
		 * @since 2.2.6
		 */
		add_action( 'wp_ajax_wte_fxn_clone_trip_data', array( $this, 'wte_fxn_clone_trip_data' ) );
		add_action( 'wp_ajax_nopriv_wte_fxn_clone_trip_data', array( $this, 'wte_fxn_clone_trip_data' ) );
	}

	/**
	 * Ajax callback function to clone trip data.
	 *
	 * @since 2.2.6
	 */
	function wte_fxn_clone_trip_data() {

		// Nonce checks.
		check_ajax_referer( 'wte_clone_post_nonce', 'security' );

		if ( ! isset( $_POST['post_id'] ) ) {
			return;
		}

		$post_id   = $_POST['post_id'];
		$post_type = get_post_type( $post_id );

		if ( 'trip' !== $post_type ) {
			return;
		}
		$post = get_post( $post_id );

		$post_array = array(
			'post_title'   => $post->post_title,
			'post_content' => $post->post_content,
			'post_status'  => 'draft',
			'post_type'    => 'trip',
		);

		// Cloning old trip.
		$new_post_id = wp_insert_post( $post_array );

		// Cloning old trip meta.
		$all_old_meta = get_post_meta( $post_id );

		if ( is_array( $all_old_meta ) && count( $all_old_meta ) > 0 ) {
			foreach ( $all_old_meta as $meta_key => $meta_value_array ) {
				$meta_value = isset( $meta_value_array[0] ) ? $meta_value_array[0] : '';

				if ( '' !== $meta_value ) {
					$meta_value = maybe_unserialize( $meta_value );
				}
				update_post_meta( $new_post_id, $meta_key, $meta_value );
			}
		}

		// Cloning taxonomies
		$trip_taxonomies = array( 'destination', 'activities', 'trip_types' );
		foreach ( $trip_taxonomies as $taxonomy ) {
			$trip_terms      = get_the_terms( $post_id, $taxonomy );
			$trip_term_names = array();
			if ( is_array( $trip_terms ) && count( $trip_terms ) > 0 ) {
				foreach ( $trip_terms as $post_terms ) {
					$trip_term_names[] = $post_terms->name;
				}
			}
			wp_set_object_terms( $new_post_id, $trip_term_names, $taxonomy );
		}
		wp_send_json( array( 'true' ) );
	}

	/**
	 * Add to cart.
	 *
	 * @since 4.0.0-neo
	 * @return void
	 */
	public static function add_to_cart() {
		// Cart Data sent as JSON body.
		$raw_data = wte_get_request_raw_data();

		// Decoded JSON data.
		$cart_data = json_decode( $raw_data, ! 0 );

		if ( ! $cart_data ) {
			wp_send_json_error( new WP_Error( 'ADD_TO_CART_ERROR', __( 'Invalid data structure.', 'wp-travel-engine' ) ) );
			exit;
		}

		$cart_data = (object) $cart_data;

		if ( empty( $cart_data->nonce ) || ! wp_verify_nonce( $cart_data->nonce, 'wte_add_to_cart' ) ) {
			wp_send_json_error( new WP_Error( 'ADD_TO_CART_ERROR', __( 'Nonce verification failed.', 'wp-travel-engine' ) ) );
			exit;
		}

		if ( empty( $cart_data->{'tripID'} ) ) {
			wp_send_json_error( new WP_Error( 'ADD_TO_CART_ERROR', __( 'Invalid Trip ID.', 'wp-travel-engine' ) ) );
			exit;
		}

		global $wte_cart;

		if ( ! apply_filters( 'wp_travel_engine_allow_multiple_cart_items', false ) ) {
			$wte_cart->clear();
		}

		// Mapped Data.
		$trip_id   = $cart_data->{'tripID'};
		$trip_date = $cart_data->{'tripDate'};
		$trip_time = $cart_data->{'tripTime'};
		$travelers = $cart_data->{'travelers'};

		$cart_total = $cart_data->{'cartTotal'};

		$pax           = array();
		$pax_cost      = array();
		$category_info = array(); // This contains pricing category information.

		$only_trip_price = 0;

		if ( isset( $cart_data->{'pricingOptions'} ) && is_array( $cart_data->{'pricingOptions'} ) ) {
			foreach ( $cart_data->{'pricingOptions'} as $cid => $info ) {
				if ( (int) $info['pax'] < 1 ) {
					continue;
				}
				$category_total_cost   = isset( $info['categoryInfo']['pricingType'] ) && 'per-person' === $info['categoryInfo']['pricingType'] ? (float) $info['pax'] * $info['cost'] : (float) $info['cost'];
				$pax[ $cid ]           = $info['pax'];
				$pax_cost[ $cid ]      = $category_total_cost;
				$category_info[ $cid ] = $info['categoryInfo'];
				$only_trip_price      += $category_total_cost;
			}
		}

		$pax_labels = array();
		$attrs      = apply_filters(
			'wp_travel_engine_cart_attributes',
			array(
				'trip_date'          => $cart_data->{'tripDate'},
				'trip_time'          => $cart_data->{'tripTime'},
				'price_key'          => $cart_data->{'packageID'},
				'pricing_options'    => $cart_data->{'pricingOptions'},
				'pax'                => $pax,
				'pax_labels'         => $pax_labels,
				'category_info'      => $category_info,
				'pax_cost'           => $pax_cost,
				'multi_pricing_used' => ! 0,
				'price_key'          => $cart_data->{'packageID'},
				'trip_extras'        => ! empty( $cart_data->{'extraServices'} ) ? (array) $cart_data->{'extraServices'} : array(),
			)
		);

		$trip_price         = $only_trip_price;
		$trip_price_partial = 0;

		$partial_payment_data = wp_travel_engine_get_trip_partial_payment_data( $trip_id );

		if ( ! empty( $partial_payment_data ) ) :

			if ( 'amount' === $partial_payment_data['type'] ) :

				$trip_price_partial = $partial_payment_data['value'];

			elseif ( 'percentage' === $partial_payment_data['type'] ) :

				$partial            = 100 - $partial_payment_data['value'];
				$trip_price_partial = ( $trip_price ) - ( $partial / 100 ) * $trip_price;

			endif;

		endif;

		// combine additional parameters to attributes insted more params.
		$attrs['trip_price']         = $trip_price;
		$attrs['trip_price_partial'] = $trip_price_partial;

		$price_key = $cart_data->{'packageID'};
		/**
		 * Action with data.
		 */
		do_action_deprecated( 'wp_travel_engine_before_trip_add_to_cart', array( $trip_id, $trip_price, $trip_price_partial, $pax, $price_key, $attrs ), '4.3.0', 'wte_before_add_to_cart', __( 'deprecated because of more params.', 'wp-travel-engine' ) );
		do_action( 'wte_before_add_to_cart', $trip_id, $attrs );

		// Get any errors/ notices added.
		$wte_errors = WTE()->notices->get( 'error' );

		// If any errors found bail.Ftrip-cost
		if ( $wte_errors ) :
			wp_send_json_error( $wte_errors );
			exit;
		endif;

		// Add to cart.
		$wte_cart->add( $trip_id, $attrs );

		wp_send_json_success(
			array(
				'code'    => 'ADD_TO_CART_SUCCESS',
				'message' => __( 'Trip added to cart successfully.', 'wp-travel-engine' ),
				'items'   => $wte_cart->getItems(),
			)
		);
		exit;
	}

	/**
	 * Add trip to cart.
	 *
	 * @return void
	 */
	function wte_add_trip_to_cart() {

		if ( ! empty( $_REQUEST['cart_version'] ) ) {
			self::add_to_cart();
		}

		if ( ! isset( $_POST['trip-id'] ) || is_null( get_post( $_POST['trip-id'] ) ) ) {
			wp_send_json_error( new WP_Error( 'ADD_TO_CART_ERROR', __( 'Invalid trip ID.', 'wp-travel-engine' ) ) );
			die;
		}

		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp_travel_engine_booking_nonce' ) ) {
			wp_send_json_error( new WP_Error( 'ADD_TO_CART_ERROR', __( 'Nonce verification failed.', 'wp-travel-engine' ) ) );
			die;
		}

		global $wte_cart;

		$allow_multiple_cart_items = apply_filters( 'wp_travel_engine_allow_multiple_cart_items', false );

		if ( ! $allow_multiple_cart_items ) {
			$wte_cart->clear();
		}

		$trip_id            = $_POST['trip-id'];
		$trip_date          = isset( $_POST['trip-date'] ) ? $_POST['trip-date'] : '';
		$trip_time          = isset( $_POST['trip-time'] ) ? $_POST['trip-time'] : '';
		$travelers          = isset( $_POST['travelers'] ) ? $_POST['travelers'] : 1;
		$travelers_cost     = isset( $_POST['travelers-cost'] ) ? $_POST['travelers-cost'] : 0;
		$child_travelers    = isset( $_POST['child-travelers'] ) ? $_POST['child-travelers'] : 0;
		$child_cost         = isset( $_POST['child-travelers-cost'] ) ? $_POST['child-travelers-cost'] : 0;
		$trip_extras        = isset( $_POST['extra_service'] ) ? $_POST['extra_service'] : array();
		$trip_price         = isset( $_POST['trip-cost'] ) ? $_POST['trip-cost'] : 0;
		$price_key          = '';
		$trip_price_partial = 0;

		// Additional cart params.
		$attrs['trip_date']   = $trip_date;
		$attrs['trip_time']   = $trip_time;
		$attrs['trip_extras'] = $trip_extras;

		$pax      = array();
		$pax_cost = array();

		if ( ! empty( $_POST['pricing_options'] ) ) :

			foreach ( $_POST['pricing_options'] as $key => $option ) :

				$pax[ $key ]      = $option['pax'];
				$pax_cost[ $key ] = $option['cost'];

			endforeach;

			// Multi-pricing flag
			$attrs['multi_pricing_used'] = true;

		else :

			$pax = array(
				'adult' => $travelers,
				'child' => $child_travelers,
			);

			$pax_cost = array(
				'adult' => $travelers_cost,
				'child' => $child_cost,
			);

		endif;

		$attrs['pax']      = $pax;
		$attrs['pax_cost'] = $pax_cost;

		$attrs = apply_filters( 'wp_travel_engine_cart_attributes', $attrs );

		$partial_payment_data = wp_travel_engine_get_trip_partial_payment_data( $trip_id );

		if ( ! empty( $partial_payment_data ) ) :

			if ( 'amount' === $partial_payment_data['type'] ) :

				$trip_price_partial = $partial_payment_data['value'];

			elseif ( 'percentage' === $partial_payment_data['type'] ) :

				$partial            = 100 - (float) $partial_payment_data['value'];
				$trip_price_partial = ( $trip_price ) - ( $partial / 100 ) * $trip_price;

			endif;

		endif;

		// combine additional parameters to attributes insted more params.
		$attrs['trip_price']         = $trip_price;
		$attrs['trip_price_partial'] = $trip_price_partial;
		$attrs['pax']                = $pax;
		$attrs['price_key']          = $price_key;

		/**
		 * Action with data.
		 */
		do_action_deprecated( 'wp_travel_engine_before_trip_add_to_cart', array( $trip_id, $trip_price, $trip_price_partial, $pax, $price_key, $attrs ), '4.3.0', 'wte_before_add_to_cart', __( 'deprecated because of more params.', 'wp-travel-engine' ) );
		do_action( 'wte_before_add_to_cart', $trip_id, $attrs );

		// Get any errors/ notices added.
		$wte_errors = WTE()->notices->get( 'error' );

			// If any errors found bail.Ftrip-cost
		if ( $wte_errors ) :
			wp_send_json_error( $wte_errors );
		endif;

		// Add to cart.
		$wte_cart->add( $trip_id, $attrs );

		/**
		 * @since 3.0.7
		 */
		do_action_deprecated( 'wp_travel_engine_after_trip_add_to_cart', array( $trip_id, $trip_price, $trip_price_partial, $pax, $price_key, $attrs ), '4.3.0', 'wte_after_add_to_cart', __( 'deprecated because of more params.', 'wp-travel-engine' ) );

		do_action( 'wte_after_add_to_cart', $trip_id, $attrs );

		// send success notification.
		wp_send_json_success( array( 'message' => __( 'Trip added to cart successfully', 'wp-travel-engine' ) ) );

		die;

	}

}
new WTE_Ajax();
