<?php
	/**
	 * WP Travel Engine coupons restrictions tab.
	 *
	 * @package WP Travel Engine Coupons
	 */
	global $post;
	// Get post ID.
	if ( ! is_object( $post ) && defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		$post_id  = $_POST['post_id'];
		$next_tab = $_POST['next_tab']; 
	} else {
		$post_id = $post->ID;
	}
	// Get Restrictions Tab Data.
	$coupon_metas     = get_post_meta( $post_id, 'wp_travel_engine_coupon_metas', true );
	$restrictions_tab = isset( $coupon_metas['restriction'] ) ? $coupon_metas['restriction'] : array();

	// Field Values.
	$restricted_trips    = isset( $restrictions_tab['restricted_trips'] ) ? $restrictions_tab['restricted_trips'] : array();
	$coupon_limit_number = isset( $restrictions_tab['coupon_limit_number'] ) ? $restrictions_tab['coupon_limit_number'] : '';
	?>
<div class="wpte-form-block">
	<div class="wpte-field wpte-floated wpte-select">
		<label
			class="wpte-field-label"
			for="wp_travel_engine_coupon[restriction][restricted_trips][]"
		><?php echo esc_html( 'Allow Coupon Use For', 'wp-travel-coupon-pro' ); ?></label>
		<?php 
				$trips = wp_travel_engine_get_trips_array(); 
				$count_options_data   = count( $restricted_trips );
				$count_trips    = count( $trips );
				$multiple_checked_all = '';
				
				if ( $count_options_data == $count_trips ) {
					$multiple_checked_all = 'checked=checked';
				}

				$multiple_checked_text = __( 'Select multiple', 'wp-travel-engine' );
				if ( $count_trips > 0 ) {
					$multiple_checked_text = $count_options_data . __( ' item selected', 'wp-travel-engine' );
				}
			//	echo esc_html( $multiple_checked_text ); 
			?>
			<select
				multiple
				class="wp-travel-engine-multi-inner wpte-enhanced-select"
				name="wp_travel_engine_coupon[restriction][restricted_trips][]"
			>
				<?php
					foreach ( $trips as $key => $iti ) {
						$checked            = '';
						$selecte_list_class = '';
						if ( in_array( $key, $restricted_trips ) ) {

							$checked            = 'selected=selected';
							$selecte_list_class = 'selected';
						}
						?>
				<option
					value="<?php echo esc_attr( $key ); ?>"
					<?php echo esc_attr( $checked ); ?>
				><?php echo esc_html( $iti ); ?></option>
				<?php } ?>
			</select>
			<span
				class="wpte-tooltip"><?php esc_html_e( 'Choose to apply coupons to certain trips only. Select none to apply to all trips', 'wp-travel-engine' ); ?></span>

	</div>
	<div class="wpte-field wpte-floated departure-dates-options">
		<label
			class="wpte-field-label"
			for="coupon-limit"
		><?php esc_html_e( 'Coupon Usage Limit', 'wp-travel-engine' ); ?></label>
		<input
			type="number"
			step="1"
			min="0"
			id="coupon-limit"
			name="wp_travel_engine_coupon[restriction][coupon_limit_number]"
			value="<?php echo esc_attr( $coupon_limit_number ); ?>"
		>
		<span
			class="wpte-tooltip"><?php echo esc_attr( 'No. of times coupon can be used before being obsolute.', 'wp-travel-engine-coupons' ); ?></span>
	</div>
</div>
