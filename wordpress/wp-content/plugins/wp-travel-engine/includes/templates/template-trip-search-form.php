<?php
/**
 * Trip Search Form Template.
 * Content for Wte_Advanced_Search_Form Shortcode.
 */
$options = get_option( 'wp_travel_engine_settings', array() );
if ( ! empty( $options['pages']['search'] ) ) {
	ob_start();
	$pid   = $options['pages']['search'];
	$nonce = wp_create_nonce( 'search-nonce' );

	// $max_duration = wte_advanced_search_cost_and_duration( 'duration', 'max' );
	// $max_cost     = wte_advanced_search_cost_and_duration( 'cost', 'max' );

	$duration_range = \WPTravelEngine\Modules\TripSearch::get_duration_range();
	$max_duration   = $duration_range->max_duration;

	$price_range  = \WPTravelEngine\Modules\TripSearch::get_price_range();
	$max_cost     = $price_range->max_price;
	$max_cost_val = apply_filters( 'wte_price_cost_reverse', $max_cost );


	// Search label filters
	$destination_lbl = apply_filters( 'wte_search_destination_label', __( 'Destination', 'wp-travel-engine' ) );
	$activities_lbl  = apply_filters( 'wte_search_activities_label', __( 'Activities', 'wp-travel-engine' ) );
	$duration_lbl    = apply_filters( 'wte_search_duration_label', __( 'Duration', 'wp-travel-engine' ) );
	$budget_lbl      = apply_filters( 'wte_search_budget_label', __( 'Budget', 'wp-travel-engine' ) );

	?>
			<h3>
		<?php
		$search_title = __( 'Search for a trip', 'wp-travel-engine' );
		echo apply_filters( 'wte_advanced_search_title', $search_title );
		?>
			</h3>
			<form method="get" action='<?php echo esc_url( get_permalink( $pid ) ); ?>' id="wte-advanced-search-form-shortcode">
				<div class="class-wte-advanced-search-wrapper wte-advanced-search-wrapper-nice-select">
					<input type="hidden" name="search-nonce" value="<?php echo $nonce; ?>">
					<?php
					$categories = get_categories( 'taxonomy=destination' );
					$taxonomy   = 'destination';
					/** Get only parent taxonomy terms */
					$terms = get_terms(
						$taxonomy,
						array(
							'hide_empty' => 1,
							'parent'     => 0,
						)
					);

					$select  = "<div class='advanced-search-field trip-destination'><h3>" . $destination_lbl . "</h3><div class='custom-select'><select name='destination' id='destination' class='postform'>";
					$select .= "<option value='-1'>" . $destination_lbl . '</option>';
					if ( sizeof( $categories ) > 0 ) {
						foreach ( $terms as $term ) {
							$select .= '<option value="' . $term->slug . '">' . $term->name . '</option>';

							/** Check if term has children */
							$childterms = get_terms(
								$taxonomy,
								array(
									'hide_empty' => 1,
									'orderby'    => 'slug',
									'parent'     => $term->term_id,
								)
							);
							if ( $childterms ) :
								foreach ( $childterms as $childterm ) {
									$select .= "<option value='" . $childterm->slug . "'>&nbsp;&nbsp;&nbsp;" . $childterm->name . '</option>';

									/** Check if childterm has grand children */
									$grandterms = get_terms(
										$taxonomy,
										array(
											'hide_empty' => 1,
											'orderby'    => 'slug',
											'parent'     => $childterm->term_id,
										)
									);
									if ( $grandterms ) :
										foreach ( $grandterms as $grandterm ) {
											$select .= "<option value='" . $grandterm->slug . "'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $grandterm->name . '</option>';

											/** Check if grand child term has great grand children */
											$greatterms = get_terms(
												$taxonomy,
												array(
													'hide_empty' => 1,
													'orderby' => 'slug',
													'parent'  => $grandterm->term_id,
												)
											);
											if ( $greatterms ) :
												foreach ( $greatterms as $greatterm ) {
																	$select .= "<option value='" . $greatterm->slug . "'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $greatterm->name . '</option>';
												}
													endif;
										}
							endif;
								}
					endif;
						}
					}
					$select .= '</select></div></div>';

					if ( ! isset( $options['trip_search']['destination'] ) || '0' === $options['trip_search']['destination'] ) {
						echo $select;
					}
					$categories = get_categories( 'taxonomy=activities' );
					$taxonomy   = 'activities';
					/** Get only parent taxonomy terms */
					$terms   = get_terms(
						$taxonomy,
						array(
							'hide_empty' => 1,
							'parent'     => 0,
						)
					);
					$select  = "<div class='advanced-search-field trip-activities'><h3>" . $activities_lbl . "</h3><div class='custom-select'><select name='activities' id='activities' class='postform'>";
					$select .= "<option value='-1'>" . $activities_lbl . '</option>';

					if ( sizeof( $categories ) > 0 ) {
						foreach ( $terms as $term ) {
							$select .= '<option value="' . $term->slug . '">' . $term->name . '</option>';

							/** Check if term has children */
							$childterms = get_terms(
								$taxonomy,
								array(
									'hide_empty' => 1,
									'orderby'    => 'slug',
									'parent'     => $term->term_id,
								)
							);
							if ( $childterms ) :
								foreach ( $childterms as $childterm ) {
									$select .= "<option value='" . $childterm->slug . "'>&nbsp;&nbsp;&nbsp;" . $childterm->name . '</option>';

									/** Check if childterm has grand children */
									$grandterms = get_terms(
										$taxonomy,
										array(
											'hide_empty' => 1,
											'orderby'    => 'slug',
											'parent'     => $childterm->term_id,
										)
									);
									if ( $grandterms ) :
										foreach ( $grandterms as $grandterm ) {
													$select .= "<option value='" . $grandterm->slug . "'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $grandterm->name . '</option>';

													/** Check if grand child term has great grand children */
													$greatterms = get_terms(
														$taxonomy,
														array(
															'hide_empty' => 1,
															'orderby'    => 'slug',
															'parent'     => $grandterm->term_id,
														)
													);
											if ( $greatterms ) :
												foreach ( $greatterms as $greatterm ) {
																			$select .= "<option value='" . $greatterm->slug . "'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $greatterm->name . '</option>';
												}
													endif;
										}
									endif;
								}
							endif;
						}
					}

					$select .= '</select></div></div>';
					if ( ! isset( $options['trip_search']['activities'] ) || '0' === $options['trip_search']['activities'] ) {
						echo $select;
					}

					$select  = "<div class='advanced-search-field trip-duration'>";
					$select .= '<h3>' . $duration_lbl . '</h3>';
					$select .= '<strong>' . $duration_lbl . '</strong>';
					$select .= '<div class="advanced-search-field search-dur"><div id="sform-duration-slider-range"></div><div class="duration-slider-value"><span id="min-duration" class="min-duration" name="min-duration">0</span><span class="max-duration" id="max-duration" name="max-duration">' . $max_duration . '</span></div></div>';
					$select .= '<input type="hidden" id="min-duration-value" value="0" name="min-duration"><input type="hidden" id="max-duration-value" name="max-duration" value="' . $max_duration . '">';

					$select .= '</div>';
					if ( ! isset( $options['trip_search']['duration'] ) || '0' === $options['trip_search']['duration'] ) {
						echo $select;
					}

					$select  = '<div class="advanced-search-field trip-cost"><h3>' . $budget_lbl . '</h3>';
					$select .= '<strong>' . $budget_lbl . '</strong>';
					$select .= '<div class="advanced-search-field search-price"><div id="sform-cost-slider-range"></div><div class="cost-slider-value"><span class="min-cost" id="min-cost" name="min-cost">0</span><span class="max-cost" id="max-cost" name="max-cost">' . $max_cost . '</span></div></div></div>';
					$select .= '<input type="hidden" id="min-cost-value" name="min-cost" value="0"><input type="hidden" id="max-cost-value" name="max-cost" value="' . $max_cost_val . '">';

					if ( ! isset( $options['trip_search']['budget'] ) || '0' === $options['trip_search']['budget'] ) {
						echo $select;
					}
					if ( ! isset( $options['trip_search']['dates'] ) || '0' === $options['trip_search']['dates'] ) {
						do_action( 'wte_departure_date_dropdown' );
					}
					printf(
						'<div class="advanced-search-field-submit"><input type="submit" name="search" value="%1$s"/></div>',
						apply_filters( 'wte_search_label', __( 'Search', 'wp-travel-engine' ) )
					);
					?>
				</div>
			</form>
			<?php
			$output = ob_get_contents();
			ob_end_clean();
			echo $output;
}
