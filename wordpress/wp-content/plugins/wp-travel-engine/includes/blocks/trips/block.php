<?php
/**
 * Content for Trips Block.
 */

$results = array();
if ( ! empty( $attributes['filters']['tripsToDisplay'] ) ) {
	$results = get_posts(
		array(
			'post_type'      => WP_TRAVEL_ENGINE_POST_TYPE,
			'post__in'       => $attributes['filters']['tripsToDisplay'],
			'posts_per_page' => 100,
		)
	);
	if ( ! is_array( $results ) ) {
		return;
	}
}

$results = array_combine( array_column( $results, 'ID' ), $results );

$layout   = wte_array_get( $attributes, 'layout', 'grid' );
$column   = wte_array_get( $attributes, 'tripsCountPerRow', 3 );
$settings = get_option( 'wp_travel_engine_settings', array() );

$dates_layout = ! empty( $settings['fsd_dates_layout'] ) ? $settings['fsd_dates_layout'] : 'dates_list';
$show_heading = wte_array_get( $attributes, 'showSectionHeading', false );

$show_section_description = wte_array_get( $attributes, 'showSectionDescription', false );

$viewMoreLink = wte_array_get( $attributes, 'viewAllLink', '' ) != '' ? trailingslashit( $attributes['viewAllLink'] ) : trailingslashit( get_post_type_archive_link( WP_TRAVEL_ENGINE_POST_TYPE ) );

echo '<div class="wp-block-wptravelengine-trips wpte-gblock-wrapper">';
if ( $results && is_array( $results ) ) :
	if ( $show_heading || $show_section_description ) {
		echo '<div class="wpte-gblock-title-wrap">';
		if ( $show_heading ) {
			$heading_level = isset( $attributes['sectionHeadingLevel'] ) && $attributes['sectionHeadingLevel'] ? $attributes['sectionHeadingLevel'] : 0;
			$heading       = $heading_level ? "<h{$heading_level} class=\"wpte-gblock-title\">%s</h{$heading_level}>" : '<p>%s</p>';
			printf( $heading, wte_array_get( $attributes, 'sectionHeading', '' ) );
		}
		if ( $show_section_description ) {
			printf( '<p>%s</p>', wte_array_get( $attributes, 'sectionDescription', '' ) );
		}
		echo '</div>';
	}
	echo "<div class=\"category-{$layout} wte-d-flex wte-col-{$column} wpte-trip-list-wrapper columns-{$column}\">";
	$position = 1;
	foreach ( $attributes['filters']['tripsToDisplay'] as $trip_id ) :
		if ( ! isset( $results[ $trip_id ] ) ) {
			continue;
		}
		$trip        = $results[ $trip_id ];
		$is_featured = wte_is_trip_featured( $trip->ID );
		$meta        = \wte_trip_get_trip_rest_metadata( $trip->ID );
		$args        = array( $attributes, $trip, $results );
		include __DIR__ . '/layouts/layout-' . $attributes['cardlayout'] . '.php';

		$position++;
	endforeach;
	echo '</div>';
endif;
if ( wte_array_get( $attributes, 'layoutFilters.showViewAll', false ) ) : ?>
	<div class="wte-block-btn-wrapper">
		<a href="<?php echo esc_url( trailingslashit( $viewMoreLink ) ); ?>" class="wte-view-all-trips-btn"><?php echo wte_array_get( $attributes, 'viewAllButtonText', __( 'View All', 'wp-travel-engine' ) ); ?></a>
	</div>
	<?php
endif;
echo '</div>';
