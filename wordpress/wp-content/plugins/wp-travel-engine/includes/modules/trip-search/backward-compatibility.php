<?php
use WPTravelEngine\Modules\TripSearch;
/**
 * To provide backward comaptibility.
 */

/**
 * Sort tax parent child hierarchy
 *
 * @param Array   $cats
 * @param Array   $into
 * @param integer $parentId
 * @return void
 */
function wte_advanced_search_sort_terms_hierarchicaly( array &$cats, array &$into, $parentId = 0 ) {
	foreach ( $cats as $i => $cat ) {
		if ( $cat->parent == $parentId ) {
			$into[ $cat->term_id ] = $cat;
			unset( $cats[ $i ] );
		}
	}

	foreach ( $into as $topCat ) {

		$topCat->children = array();

		wte_advanced_search_sort_terms_hierarchicaly( $cats, $topCat->children, $topCat->term_id );
	}
}
/**
 * Get search layout.
 *
 * @param [type]  $taxonomy
 * @param boolean $has_children
 * @return void
 */
function wte_advanced_search_taxonomy_render( $taxonomy_array, $has_children = false, $name = 'cat' ) {
	$name_val = isset( $_GET[ $name ] ) && ! empty( $_GET[ $name ] ) ? $_GET[ $name ] : false;
	?>
		<ul class="<?php echo $has_children ? esc_attr( 'children' ) : 'wte-terms-list'; ?>">
			<?php
			foreach ( $taxonomy_array as $ky => $tax ) :
				$term_slug    = $tax->slug;
				$term_name    = $tax->name;
				$has_children = isset( $tax->children ) && ! empty( $tax->children ) ? true : false;
				$tax_count    = $tax->category_count;
				?>
				<li class="<?php echo $has_children ? esc_attr( 'has-children' ) : ''; ?>">
					<label>
						<input <?php echo isset( $_GET[ $name ] ) && ( $name_val && $term_slug === $_GET[ $name ] ) ? 'checked' : ''; ?> type='checkbox' name='<?php esc_attr_e( $name, 'wp-travel-engine' ); ?>' class='<?php esc_attr_e( $name, 'wp-travel-engine' ); ?> wte-filter-item' value='<?php esc_attr_e( $term_slug, 'wp-travel-engine' ); ?>'>
						<span><?php esc_html_e( $term_name, 'wp-travel-engine' ); ?></span>
					</label>
					<?php
						$show_tax_count = apply_filters( 'wte_advanced_search_filters_show_tax_count', true );
					if ( $show_tax_count ) :
						?>
							<span class='count'>(<?php esc_html_e( $tax_count, 'wp-travel-engine' ); ?>)</span>
						<?php
						endif;
					if ( $has_children ) :
						wte_advanced_search_taxonomy_render( $tax->children, $has_children, $name );
						endif;
					?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php
}

/**
 * Get max/min costs and duration values for trip
 *
 * @param      string $attr   attribute cost | duration
 * @param      string $type   type min | max
 *
 * @return     int
 */
function wte_advanced_search_cost_and_duration( $attr, $type ) {

	if ( $attr === 'cost' ) {
		$range = (object) TripSearch::get_price_range();
		if ( isset( $range->{"$type}_price"} ) ) {
			return $range->{"$type}_price"};
		}
	}
	if ( $attr === 'duration' ) {
		$range = (object) TripSearch::get_price_range();
		if ( isset( $range->{"$type}_duration"} ) ) {
			return $range->{"$type}_duration"};
		}
	}
}

/**
 * Check if search fields should be hidden in Search Page - FILTER BY Section
 */
function wte_advanced_search_hide_filters_in_search_page( $search_field ) {

	$options = get_option( 'wp_travel_engine_settings', array() );
	if ( isset( $options['trip_search']['apply_in_search_page'] ) && '1' === $options['trip_search']['apply_in_search_page'] && isset( $options['trip_search'][ '' . $search_field . '' ] ) && '1' === $options['trip_search'][ '' . $search_field . '' ] ) {
		return true;
	}
	return false;
}

/**
 * check if is search page.
 *
 * @return void
 */
function wte_advanced_search_is_search_page() {
	return TripSearch::is_search_page();
}

/**
 * apply the top header filters in search page.
 *
 * @return void
 */
function wte_advanced_search_get_order_args( $sortby_val ) {
	$sort_args = array();
	switch ( $sortby_val ) {
		case 'latest':
			$sort_args['order']   = 'DESC';
			$sort_args['orderby'] = 'date';
			break;
		case 'rating':
			$sort_args['order']   = 'DESC';
			$sort_args['orderby'] = 'comment_count';
			break;
		case 'price':
			$sort_args['meta_key'] = 'wp_travel_engine_setting_trip_actual_price';
			$sort_args['order']    = 'ASC';
			$sort_args['orderby']  = 'meta_value_num';
			break;
		case 'price-desc':
			$sort_args['meta_key'] = 'wp_travel_engine_setting_trip_actual_price';
			$sort_args['order']    = 'DESC';
			$sort_args['orderby']  = 'meta_value_num';
			break;
		case 'days':
			$sort_args['meta_key'] = 'wp_travel_engine_setting_trip_duration';
			$sort_args['order']    = 'ASC';
			$sort_args['orderby']  = 'meta_value_num';
			break;
		case 'days-desc':
			$sort_args['meta_key'] = 'wp_travel_engine_setting_trip_duration';
			$sort_args['order']    = 'DESC';
			$sort_args['orderby']  = 'meta_value_num';
			break;
		case 'name':
			$sort_args['order']   = 'ASC';
			$sort_args['orderby'] = 'title';
			break;
		case 'name-desc':
			$sort_args['order']   = 'DESC';
			$sort_args['orderby'] = 'title';
			break;
	}

	return $sort_args;
}

/**
 * For Backward Compatibility.
 */
class Wte_Advanced_Search {}
