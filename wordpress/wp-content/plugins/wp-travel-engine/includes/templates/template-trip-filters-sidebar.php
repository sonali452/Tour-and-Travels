<?php
use WPTravelEngine\Modules\TripSearch;
/**
 * Archive Filters Sidebar.
 *
 * @since __addonmigration__
 */
$filters = TripSearch::get_filters_sections();

$nonce = wp_create_nonce( 'wte-trip-archive-filters' );
?>
<div class='advanced-search-wrapper' id="wte__trip-search-filters">
	<div class="sidebar">
		<div class="advanced-search-header">
			<h2><?php echo apply_filters( 'wte_advanced_filterby_title', __( 'Criteria', 'wp-travel-engine' ) ); ?></h2>
			<button class="clear-search-criteria" id="reset-trip-search-criteria"><?php esc_html_e( 'Clear all', 'wp-travel-engine' ); ?></button>
		</div>
		<?php
		if ( is_array( $filters ) ) {
			foreach ( $filters as $filter ) {
				if ( isset( $filter['show'] ) && $filter['show'] && isset( $filter['render'] ) && is_callable( $filter['render'] ) ) {
					call_user_func( $filter['render'], $filter );
				}
			}
		}

		$hide_dates = wte_advanced_search_hide_filters_in_search_page( 'dates' );
		if ( ! $hide_dates ) {
			do_action( 'wte_departure_date_dropdown', true );
		}
		?>
	</div>
</div>
