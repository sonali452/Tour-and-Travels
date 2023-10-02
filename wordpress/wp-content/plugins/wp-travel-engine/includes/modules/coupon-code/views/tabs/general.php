<?php
use WPTravelEngine\Modules\CouponCode;
/**
 * Coupons General Tab Contents
 *
 * @package WP Travel Engine Coupons
 */
global $post;

// General Tab Data.
$coupon_metas = get_post_meta( $post->ID, 'wp_travel_engine_coupon_metas', true );
$general_tab  = isset( $coupon_metas['general'] ) ? $coupon_metas['general'] : array();
$coupon_code  = get_post_meta( $post->ID, 'wp_travel_engine_coupon_code', true );

// Field Values.
$coupon_active = isset( $general_tab['coupon_active'] ) ? $general_tab['coupon_active'] : 'yes';
$coupon_code   = ! empty( $coupon_code ) ? $coupon_code : '';
$coupon_type   = isset( $general_tab['coupon_type'] ) ? $general_tab['coupon_type'] : 'fixed';
$coupon_value  = isset( $general_tab['coupon_value'] ) ? $general_tab['coupon_value'] : '';

$date_format = get_option( 'date_format' );

$coupon_expiry_date = isset( $general_tab['coupon_expiry_date'] ) ? $general_tab['coupon_expiry_date'] : '';

try {
	$coupon_expiry_date = ! empty( $general_tab['coupon_expiry_date'] ) ? ( new \DateTime( $general_tab['coupon_expiry_date'] ) )->format( 'Y-m-d' ) : '';
} catch ( \Exception $e ) {
	$coupon_expiry_date = '';
}
try {
	$coupon_start_date = isset( $general_tab['coupon_start_date'] ) ? ( new \DateTime( $general_tab['coupon_start_date'] ) )->format( 'Y-m-d' ) : gmdate( 'Y-m-d' );
} catch ( \Exception $e ) {
	$coupon_start_date = gmdate( 'Y-m-d' );
}

// $coupon    = new WP_Travel_engine_Coupon();
$coupon_id = CouponCode::coupon_id_by_code( $coupon_code );

$wp_travel_engine_settings = get_option( 'wp_travel_engine_settings', true );

$code = ! empty( $wp_travel_engine_settings['currency_code'] ) ? $wp_travel_engine_settings['currency_code'] : 'USD';

$obj      = new \Wp_Travel_Engine_Functions();
$currency = $obj->wp_travel_engine_currencies_symbol( $code );
?>
<div class="wpte-form-block">
	<?php if ( $coupon_id ) : ?>
	<div class="wpte-field wpte-floated departure-dates-options">
		<label
			class="wpte-field-label"
			for="currency"
		>
			<?php esc_html_e( 'Coupon Status ', 'wp-travel-engine' ); ?>
		</label>
		<?php
		$coupon_status = CouponCode::get_coupon_status( $coupon_id );
		if ( 'active' === $coupon_status ) {
			?>
		<span class="wp-travel-engine-info-msg">
			<?php echo esc_html( 'Active', 'wp-travel-engine-coupons' ); ?>
		</span>
			<?php
		} else {
			?>
		<span class="wp-travel-engine-error-msg">
			<?php echo esc_html( 'Inactive', 'wp-travel-engine-coupons' ); ?>
		</span>
			<?php
		}
		?>
		<span
			class="wpte-tooltip"><?php _e( 'Either the coupon is enabled in site or not.', 'wp-travel-engine' ); ?></span>
	</div>
	<?php endif; ?>
	<div class="wpte-field wpte-floated departure-dates-options">
		<label
			class="wpte-field-label"
			for="coupon-code"
		><?php esc_html_e( 'Coupon Code', 'wp-travel-engine' ); ?></label>
		<input
			required="required"
			type="text"
			id="coupon-code"
			name="wp_travel_engine_coupon_code"
			placeholder="<?php echo esc_attr__( 'WP-TRAVEL-ENGINE-SALE', 'wp-travel-engine' ); ?>"
			value="<?php echo esc_attr( $coupon_code ); ?>"
		>
		<input
			id="wp-travel-coupon-id"
			type="hidden"
			value="<?php echo esc_attr( $coupon_id ); ?>"
		>
		<span
			class="wpte-tooltip"><?php esc_html_e( 'Unique Identifier for the coupon.', 'wp-travel-engine' ); ?></span>
		<span
			class="wpte-tooltip wp-travel-coupon_code-error wp-travel-error"
			style="display:none;"
		><strong><?php echo esc_html( 'Warning :', 'wp-travel-engine-coupons' ); ?></strong><?php esc_html_e( ' Coupon Code already in use. Multiple coupouns with same code results to only latest coupon settings being applied.', 'wp-travel-engine' ); ?></span>
	</div>

	<div class="wpte-field wpte-floated wpte-select departure-dates-options">
		<label
			class="wpte-field-label"
			for="coupon-type"
		><?php esc_html_e( 'Discount Type', 'wp-travel-engine' ); ?></label>
		<select
			class="wpte-enhanced-select wte-coupon-code-type"
			id="coupon-type"
			name="wp_travel_engine_coupon[general][coupon_type]"
		>
			<option
				value="fixed"
				<?php selected( $coupon_type, 'fixed' ); ?>
			><?php esc_html_e( 'Fixed Discount', 'wp-travel-engine' ); ?></option>
			<option
				value="percentage"
				<?php selected( $coupon_type, 'percentage' ); ?>
			><?php esc_html_e( 'Percentage Discount', 'wp-travel-engine' ); ?></option>
		</select>
		<span
			class="wpte-tooltip"><?php esc_html_e( 'Coupon Type: Fixed Discount Amount or Percentage discount( Applies to cart total price ).', 'wp-travel-engine' ); ?></span>

	</div>

	<div class="wpte-field wpte-number wpte-floated departure-dates-options">
		<label
			class="wpte-field-label"
			for="coupon-code"
		><?php esc_html_e( 'Discount Value', 'wp-travel-engine' ); ?></label>
		<div class="wpte-floated">
			<input
				required="required"
				type="number"
				min="1"
				<?php echo 'percentage' === $coupon_type ? 'max="100"' : ''; ?>
				step="0.01"
				id="coupon-value"
				name="wp_travel_engine_coupon[general][coupon_value]"
				placeholder="<?php echo esc_attr__( 'Discount Value', 'wp-travel-engine' ); ?>"
				value="<?php echo esc_attr( $coupon_value ); ?>"
			>
			<span
				<?php echo 'percentage' === $coupon_type ? 'style="display:none;"' : ''; ?>
				id="coupon-currency-symbol"
				class="wpte-sublabel"
			>
				<?php echo esc_html( $currency ); ?>
			</span>
			<span
				<?php echo 'fixed' === $coupon_type ? 'style="display:none;"' : ''; ?>
				id="coupon-percentage-symbol"
				class="wpte-sublabel"
			>
				<?php echo '%'; ?>
			</span>
		</div>
		<span
			class="wpte-tooltip"><?php esc_html_e( 'Coupon value amount/percentage.', 'wp-travel-engine' ); ?></span>
	</div>
	<div class="wpte-field wpte-floated departure-dates-options">
		<label
			class="wpte-field-label"
			for="coupon-start-date"
		><?php esc_html_e( 'Coupon Start Date', 'wp-travel-engine' ); ?></label>
		<input
			type="text"
			class="wte-datepicker"
			id="coupon-start-date"
			name="wp_travel_engine_coupon[general][coupon_start_date]"
			value="<?php echo esc_attr( $coupon_start_date ); ?>"
		>
		<span
			class="wpte-tooltip"><?php esc_html_e( 'Coupon start date. Defaults to coupon creation date.', 'wp-travel-engine' ); ?></span>
	</div>
	<div class="wpte-field wpte-floated departure-dates-options">
		<label
			class="wpte-field-label"
			for="coupon-expiry-date"
		><?php esc_html_e( 'Coupon Expiry Date', 'wp-travel-engine' ); ?></label>
		<input
			type="text"
			class="wte-datepicker"
			id="coupon-expiry-date"
			name="wp_travel_engine_coupon[general][coupon_expiry_date]"
			value="<?php echo esc_attr( $coupon_expiry_date ); ?>"
		>
		<span
			class="wpte-tooltip"><?php esc_html_e( 'Coupon expiration date. Leave blank to disable expiration.', 'wp-travel-engine' ); ?></span>
	</div>
</div>
<?php // Leave it here now, is here for a reason. ?>
<script>
;(function() {
	var couponTypeField = document.getElementById('coupon-type')
	var toggleSign = function(showPercentage = true) {
		var currency = document.getElementById('coupon-currency-symbol')
		var percentage = document.getElementById('coupon-percentage-symbol')
		var coupon = document.getElementById('coupon-value')
		if (showPercentage) {
			currency.style.display = 'none'
			percentage.style.removeProperty('display')
			coupon.setAttribute('max', '100')
		} else {
			percentage.style.display = 'none'
			currency.style.removeProperty('display')
			coupon.removeAttribute('max')
		}
	}
	couponTypeField && jQuery(couponTypeField).on('change', function(e) {
		toggleSign(this.value === 'percentage')
	})

	var couponCode = document.getElementById('coupon-code')

	var checkCouponCode = function(_data) {
		var formData = new FormData()
		Object.entries(_data).forEach(function([name, value]) {
			formData.append(name, value)
		})
		return fetch(ajaxurl, {
			method: 'POST',
			body: formData
		})
	}
	function showError(showError = true) {
		var error = document.getElementById('#wp-travel-coupon_code-error')
		if(error && showError) error.style.removeProperty('display')
		if(error && ! showError) error.style.display = 'none'
	}
	couponCode && couponCode.addEventListener('blur', (couponValue => function(){
		if(couponValue === this.value) return
		checkCouponCode({
			coupon_code: this.value,
			coupon_id: document.getElementById('wp-travel-coupon-id').value,
			action: 'wp_travel_engine_check_coupon_code'
		})
		.then(function(response) {
			return response.json()
		})
		.then(function(data){
			showError(! data.success)
		})
		couponValue = this.value
	})(couponCode.value))

	jQuery(function($) {
		$('#coupon-start-date').datepicker({
			minDate: new Date(),
			onSelect: function onSelect() {
				//- get date from another datepicker without language dependencies
				$('#coupon-start-date').datepicker('option', 'dateFormat', 'yy-mm-dd');
				var minDate = $('#coupon-start-date').datepicker('getDate');
				$("#coupon-expiry-date").datepicker("change", {
					minDate: minDate
				});
			}
		})
		$("#coupon-expiry-date").datepicker({
			minDate: new Date(),
			onSelect: function onSelect() {
				$(this).datepicker('option', 'dateFormat', 'yy-mm-dd');
			}
		}); //setup before functions
		// var typingTimer; //timer identifier

		// var doneTypingInterval = 5000; //time in ms, 5 second for example

		// var $input = $('#coupon-code'); //on keyup, start the countdown

		// $input.on('keyup', function() {
		// 	clearTimeout(typingTimer);
		// 	typingTimer = setTimeout(doneTyping, doneTypingInterval);
		// }); //on keydown, clear the countdown 

		// $input.on('keydown', function() {
		// 	clearTimeout(typingTimer);
		// }); //user is "finished typing," do something

		// function doneTyping() {
		// 	var value = $input.val();
		// 	var couponId = jQuery('#wp-travel-coupon-id').val();
		// 	coupon_fields = {};
		// 	coupon_fields['coupon_code'] = value;
		// 	coupon_fields['coupon_id'] = couponId;
		// 	coupon_fields['action'] = 'wp_travel_engine_check_coupon_code';
		// 	jQuery.ajax({
		// 		type: "POST",
		// 		url: ajaxurl,
		// 		data: coupon_fields,
		// 		beforeSend: function beforeSend() {},
		// 		success: function success(data) {
		// 			if (!data.success) {
		// 				jQuery('#wp-travel-coupon_code-error').show();
		// 			} else {
		// 				jQuery('#wp-travel-coupon_code-error').hide();
		// 			}
		// 		}
		// 	});
		// }

		$('.wte-coupon-tabs').tabs();
		$('.wte-coupon-code-select').select2();
	});
})();
</script>
