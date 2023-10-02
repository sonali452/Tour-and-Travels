<?php
/**
 * Booking Notifications Page
 */
$wp_travel_engine_settings    = get_option( 'wp_travel_engine_settings', array() );
$subject_book                 = WTE_Booking_Emails::get_subject( 'order_confirmation', 'admin' );
$disable_admin_notification   = isset( $wp_travel_engine_settings['email']['disable_notif'] ) ? esc_attr( $wp_travel_engine_settings['email']['disable_notif'] ) : '0';
$enable_customer_notification = isset( $wp_travel_engine_settings['email']['cust_notif'] ) ? esc_attr( $wp_travel_engine_settings['email']['cust_notif'] ) : '0';
?>
<div class="wpte-field wpte-textarea wpte-floated">
	<label class="wpte-field-label" for="wp_travel_engine_settings[email][emails]"><?php esc_html_e( 'Sale Notification Emails', 'wp-travel-engine' ); ?></label>
	<input type="text" name="wp_travel_engine_settings[email][emails]" id="wp_travel_engine_settings[email][emails]" value="<?php echo esc_attr( wte_array_get( $wp_travel_engine_settings, 'email.emails', get_option( 'admin_email' ) ) ); ?>" />
	<span class="wpte-tooltip"><?php esc_html_e( 'Enter the email address(es) that should receive a notification anytime a sale is made, separated by comma (,) and no spaces.', 'wp-travel-engine' ); ?></span>
</div>

<div class="wpte-field wpte-checkbox advance-checkbox">
	<label class="wpte-field-label" for="disable-admin-notification"><?php esc_html_e( 'Disable Admin Notification', 'wp-travel-engine' ); ?></label>
	<div class="wpte-checkbox-wrap">
		<input type="checkbox"  name="wp_travel_engine_settings[email][disable_notif]"  value="1" <?php checked( $disable_admin_notification, '1' ); ?> id="disable-admin-notification">
		<label for="disable-admin-notification"></label>
	</div>
	<span class="wpte-tooltip"><?php esc_html_e( 'Turn this on if you do not want to receive sales notification emails.', 'wp-travel-engine' ); ?></span>
</div>

<div class="wpte-field wpte-checkbox advance-checkbox">
	<label class="wpte-field-label" for="enable-customer-enquiry-notification"><?php esc_attr_e( 'Enable Customer Enquiry Notification', 'wp-travel-engine' ); ?></label>
	<div class="wpte-checkbox-wrap">
		<input type="checkbox" value="1" <?php checked( $enable_customer_notification, '1' ); ?> name="wp_travel_engine_settings[email][cust_notif]" id="enable-customer-enquiry-notification">
		<label for="enable-customer-enquiry-notification"></label>
	</div>
	<span class="wpte-tooltip"><?php esc_html_e( 'Turn this on if you want to send enquiry notification emails to customer as well.', 'wp-travel-engine' ); ?></span>
</div>
<?php
$booking_notification_subject = WTE_Booking_Emails::get_subject( 'order', 'admin' );
?>
<div class="wpte-field wpte-text wpte-floated">
	<label class="wpte-field-label" data-wte-update="wte_new_430" for="wp_travel_engine_settings[email][booking_notification_subject_admin]"><?php _e( 'Booking Notification Subject', 'wp-travel-engine' ); ?></label>
	<input type="text" name="wp_travel_engine_settings[email][booking_notification_subject_admin]" id="wp_travel_engine_settings[email][booking_notification_subject_admin]" value="<?php echo esc_attr( $booking_notification_subject ); ?>">
	<span class="wpte-tooltip"><?php esc_html_e( 'Enter the booking subject for the booking notification email.', 'wp-travel-engine' ); ?></span>
</div>

<div class="wpte-field wpte-textarea wpte-floated">
	<label class="wpte-field-label" data-wte-update="wte_new_430" for="booking_notification_template_admin"><?php _e( 'Booking Notification', 'wp-travel-engine' ); ?></label>
	<?php
	$value_wysiwyg = WTE_Booking_Emails::get_template_content( 'order', 'emails/booking/notification.php', 'admin' ); // $email_class->get_email_template( 'booking', 'admin', true );

	$editor_id = 'booking_notification_template_admin';
	$settings  = array(
		'media_buttons' => true,
		'textarea_name' => 'wp_travel_engine_settings[email][' . $editor_id . ']',
	);
	?>
	<div class="wpte-field wpte-textarea wpte-floated wpte-rich-textarea delay">
		<!-- <div class="wte-editor-notice">
			<?php _e( 'Click to initialize RichEditor', 'wp-travel-engine' ); ?>
		</div> -->
		<textarea
			placeholder="<?php _e( 'Email Message', 'wp-travel-engine' ); ?>"
			name="wp_travel_engine_settings[email][<?php echo esc_attr( $editor_id ); ?>]"
			class="wte-editor-area wp-editor-area" id="<?php echo esc_attr( $editor_id ); ?>"><?php echo wp_kses_post( $value_wysiwyg ); ?></textarea>
	</div>
	<span class="wpte-tooltip"><?php esc_html_e( 'This template will be used when a booking request has been received.', 'wp-travel-engine' ); ?> <a class="wte-email-template-preview-link" href="<?php echo home_url( '/' ); ?>?_action=email-template-preview&template_type=order&pid=0&to=admin"><?php esc_html_e( 'Preview Template', 'wp-travel-engine' ); ?></a></span>
</div>

<div class="wpte-field wpte-text wpte-floated">
	<label class="wpte-field-label" for="wp_travel_engine_settings[email][sale_subject]"><?php _e( 'Payment Notification Subject', 'wp-travel-engine' ); ?></label>
	<input type="text" name="wp_travel_engine_settings[email][sale_subject]" id="wp_travel_engine_settings[email][sale_subject]" value="<?php echo esc_attr( $subject_book ); ?>">
	<span class="wpte-tooltip"><?php esc_html_e( 'Enter the booking subject for the purchase receipt email.', 'wp-travel-engine' ); ?></span>
</div>

<div class="wpte-field wpte-textarea wpte-floated">
	<label class="wpte-field-label" data-wte-update="wte_updated_430" for="sales_wpeditor"><?php _e( 'Payment Notification', 'wp-travel-engine' ); ?></label>
	<?php
	$value_wysiwyg = WTE_Booking_Emails::get_template_content( 'order_confirmation', 'emails/booking/confirmation.php', 'admin' ); // $email_class->get_email_template( 'booking', 'admin', true );

	$editor_id = 'sales_wpeditor';
	$settings  = array(
		'media_buttons' => true,
		'textarea_name' => 'wp_travel_engine_settings[email][' . $editor_id . ']',
	);
	?>
	<div class="wpte-field wpte-textarea wpte-floated wpte-rich-textarea delay">
		<textarea
			placeholder="<?php _e( 'Email Message', 'wp-travel-engine' ); ?>"
			name="wp_travel_engine_settings[email][<?php echo esc_attr( $editor_id ); ?>]"
			class="wte-editor-area wp-editor-area" id="<?php echo esc_attr( $editor_id ); ?>"><?php echo wp_kses_post( $value_wysiwyg ); ?></textarea>
	</div>
	<span class="wpte-tooltip"><?php esc_html_e( 'This email template will be used when ever a payment received.', 'wp-travel-engine' ); ?> <a class="wte-email-template-preview-link" href="<?php echo home_url( '/' ); ?>?_action=email-template-preview&template_type=order_confirmation&pid=0&to=admin"><?php esc_html_e( 'Preview Template', 'wp-travel-engine' ); ?></a></span>
	<?php if ( version_compare( get_option( 'payment_notification_admin_version', '1.0.0' ), '2.0.0', '<' ) ) : ?>
	<div style="margin-left: 145px;" class="wpte-info-block _wte_update_notice_430">
		<form action="" method="POST" id="wte-payment-notification-admin">
			<input type="hidden" name="_action" value="wte-email-template-update"/>
			<input type="hidden" name="field" value="email.sales_wpeditor"/>
		</form>
		<b><?php esc_html_e( 'Note:', 'wp-travel-engine' ); ?></b>
		<p>
		<?php
		echo wp_kses(
			sprintf( __( 'This is the default template from previous WP Travel Engine versions. The template has been updated since %1$sv4.3.0%2$s. %3$sClick here%4$s to update', 'wp-travel-engine' ), '<code>', '</code>', '<a href="#" data-target="wte-payment-notification-admin" class="wte-email-template-updater">', '</a>' ),
			array(
				'code' => array(),
				'a'    => array(
					'href'  => array(),
					'class' => array(),
					'data-target' => []
				),
			)
		);
		?>
		</p>
	</div>
	<?php endif; ?>

	<script>
	(function() {
		jQuery('.wte-email-template-preview-link').magnificPopup({
			type:'iframe'
		})
	})()
	</script>
</div>

<div class="wpte-field wpte-tags">
	<p><?php _e( 'Enter the text that is sent as sale notification email after completion of a purchase. HTML is accepted.', 'wp-travel-engine' ); ?></p>
	<p><b><?php _e( 'Available Template Tags', 'wp-travel-engine' ); ?>-</b></p>
	<ul class="wpte-list">
		<li>
			<b>{trip_url}</b>
			<span><?php _e( 'The trip URL for each booked trip', 'wp-travel-engine' ); ?></span>
		</li>
		<li>
			<b>{name}</b>
			<span><?php _e( 'The buyer\'s first name', 'wp-travel-engine' ); ?></span>
		</li>
		<li>
			<b>{fullname}</b>
			<span><?php _e( 'The buyer\'s full name, first and last', 'wp-travel-engine' ); ?></span>
		</li>
		<li>
			<b>{user_email}</b>
			<span><?php _e( 'The buyer\'s email address', 'wp-travel-engine' ); ?></span>
		</li>
		<li>
			<b>{billing_address}</b>
			<span><?php _e( 'The buyer\'s billing address', 'wp-travel-engine' ); ?></span>
		</li>
		<li>
			<b>{city}</b>
			<span><?php _e( 'The buyer\'s city', 'wp-travel-engine' ); ?></span>
		</li>
		<li>
			<b>{country}</b>
			<span><?php _e( 'The buyer\'s country', 'wp-travel-engine' ); ?></span>
		</li>
		<li>
			<b>{tdate}</b>
			<span><?php _e( 'The starting date of the trip', 'wp-travel-engine' ); ?></span>
		</li>
		<li>
			<b>{date}</b>
			<span><?php _e( 'The trip booking date', 'wp-travel-engine' ); ?></span>
		</li>
		<li>
			<b>{traveler}</b>
			<span><?php _e( 'The total number of traveler(s)', 'wp-travel-engine' ); ?></span>
		</li>
		<li>
			<b>{child-traveler}</b>
			<span><?php _e( 'The total number of child traveler(s)', 'wp-travel-engine' ); ?></span>
		</li>
		<li>
			<b>{tprice}</b>
			<span><?php _e( 'The trip price', 'wp-travel-engine' ); ?></span>
		</li>
		<li>
			<b>{price}</b>
			<span><?php _e( 'The total payment made of the booking', 'wp-travel-engine' ); ?></span>
		</li>
		<li>
			<b>{total_cost}</b>
			<span><?php _e( 'The total price of the booking', 'wp-travel-engine' ); ?></span>
		</li>
		<li>
			<b>{due}</b>
			<span><?php _e( 'The due balance', 'wp-travel-engine' ); ?></span>
		</li>
		<li>
			<b>{sitename}</b>
			<span><?php _e( 'Your site name', 'wp-travel-engine' ); ?></span>
		</li>
		<li>
			<b>{booking_url}</b>
			<span><?php _e( 'The trip booking link', 'wp-travel-engine' ); ?></span>
		</li>
		<li>
			<b>{ip_address}</b>
			<span><?php _e( 'The buyer\'s IP Address', 'wp-travel-engine' ); ?></span>
		</li>
		<li>
			<b>{booking_id}</b>
			<span><?php _e( 'The booking order ID', 'wp-travel-engine' ); ?></span>
		</li>
		<li>
			<b>{booking_details}</b>
			<span><?php _e( 'The booking details: Booked trips, Extra Services, Traveler details etc', 'wp-travel-engine' ); ?></span>
		</li>
	</ul>
	<?php
		/**
		 * Hook to add additional e-mail tags by addons.
		 */
		do_action( 'wte_additional_booking_email_tags' );
	?>
</div>
<?php
