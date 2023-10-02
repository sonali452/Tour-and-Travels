<?php
/**
 *
 * @package    wp-travel-engine
 * @subpackage wp-travel-engine/moduels
 */
$wp_travel_engine_settings = get_option( 'wp_travel_engine_settings', true );
?>
<div class="wpte-field wpte-checkbox advance-checkbox">
	<label class="wpte-field-label" for="wp_travel_engine_settings[trip_search][destination]"><?php _e( 'Hide Destination', 'wp-travel-engine' ); ?></label>
	<div class="wpte-checkbox-wrap">
		<input type="hidden" name="wp_travel_engine_settings[trip_search][destination]" value="0">
		<input type="checkbox" id="wp_travel_engine_settings[trip_search][destination]" name="wp_travel_engine_settings[trip_search][destination]" value="1"
		<?php
		if ( isset( $wp_travel_engine_settings['trip_search']['destination'] ) ) {
			checked( $wp_travel_engine_settings['trip_search']['destination'], 1 );}
		?>
		>
		<label for="wp_travel_engine_settings[trip_search][destination]"></label>
	</div>
	<span class="wpte-tooltip"><?php esc_html_e( 'Check the above checkbox to hide Destination field in the Trip Search Form.', 'wp-travel-engine' ); ?></span>
</div>

<div class="wpte-field wpte-checkbox advance-checkbox">
	<label class="wpte-field-label" for="wp_travel_engine_settings[trip_search][activities]"><?php _e( 'Hide Activities', 'wp-travel-engine' ); ?></label>
	<div class="wpte-checkbox-wrap">
		<input type="hidden" name="wp_travel_engine_settings[trip_search][activities]" value="0">
		<input type="checkbox" id="wp_travel_engine_settings[trip_search][activities]" name="wp_travel_engine_settings[trip_search][activities]" value="1"
		<?php
		if ( isset( $wp_travel_engine_settings['trip_search']['activities'] ) ) {
			checked( $wp_travel_engine_settings['trip_search']['activities'], 1 );}
		?>
		>
		<label for="wp_travel_engine_settings[trip_search][activities]"></label>
	</div>
	<span class="wpte-tooltip"><?php esc_html_e( 'Check the above checkbox to hide Activities field in the Trip Search Form.', 'wp-travel-engine' ); ?></span>
</div>

<div class="wpte-field wpte-checkbox advance-checkbox">
	<label class="wpte-field-label" for="wp_travel_engine_settings[trip_search][duration]"><?php _e( 'Hide Duration', 'wp-travel-engine' ); ?></label>
	<div class="wpte-checkbox-wrap">
		<input type="hidden" name="wp_travel_engine_settings[trip_search][duration]" value="0">
		<input type="checkbox" id="wp_travel_engine_settings[trip_search][duration]" name="wp_travel_engine_settings[trip_search][duration]" value="1"
		<?php
		if ( isset( $wp_travel_engine_settings['trip_search']['duration'] ) ) {
			checked( $wp_travel_engine_settings['trip_search']['duration'], 1 );}
		?>
		>
		<label for="wp_travel_engine_settings[trip_search][duration]"></label>
	</div>
	<span class="wpte-tooltip"><?php esc_html_e( 'Check the above checkbox to hide Duration field in the Trip Search Form.', 'wp-travel-engine' ); ?></span>
</div>

<div class="wpte-field wpte-checkbox advance-checkbox">
	<label class="wpte-field-label" for="wp_travel_engine_settings[trip_search][budget]"><?php _e( 'Hide Budget', 'wp-travel-engine' ); ?></label>
	<div class="wpte-checkbox-wrap">
		<input type="hidden" name="wp_travel_engine_settings[trip_search][budget]" value="0">
		<input type="checkbox" id="wp_travel_engine_settings[trip_search][budget]" name="wp_travel_engine_settings[trip_search][budget]" value="1"
		<?php
		if ( isset( $wp_travel_engine_settings['trip_search']['budget'] ) ) {
			checked( $wp_travel_engine_settings['trip_search']['budget'], 1 );}
		?>
		>
		<label for="wp_travel_engine_settings[trip_search][budget]"></label>
	</div>
	<span class="wpte-tooltip"><?php esc_html_e( 'Check the above checkbox to hide Budget field in the Trip Search Form.', 'wp-travel-engine' ); ?></span>
</div>

<?php do_action( 'wp_travel_engine_starting_dates_form' ); ?>

<div class="wpte-field wpte-checkbox advance-checkbox">
	<label class="wpte-field-label" for="wp_travel_engine_settings[trip_search][apply_in_search_page]"><?php _e( 'Hide in Search Page - FILTER BY Section', 'wp-travel-engine' ); ?></label>
	<div class="wpte-checkbox-wrap">
		<input type="hidden" name="wp_travel_engine_settings[trip_search][apply_in_search_page]" value="0">
		<input type="checkbox" id="wp_travel_engine_settings[trip_search][apply_in_search_page]" name="wp_travel_engine_settings[trip_search][apply_in_search_page]" value="1"
		<?php
		if ( isset( $wp_travel_engine_settings['trip_search']['apply_in_search_page'] ) ) {
			checked( $wp_travel_engine_settings['trip_search']['apply_in_search_page'], 1 );}
		?>
		>
		<label for="wp_travel_engine_settings[trip_search][apply_in_search_page]"></label>
	</div>
	<span class="wpte-tooltip"><?php esc_html_e( 'Check the above checkbox to apply the above settings in Search Page - FITER BY Section as well.', 'wp-travel-engine' ); ?></span>
</div>
