<?php
use WPTravelEngine\Plugin;
use WPTravelEngine\Packages;
/**
 * @package wte/neo
 *
 * @since wte-neo-version
 */
function wte_default_sanitize_callback( $value ) {
	return $value;
}

add_action(
	'init',
	function() {
		if ( isset( $_POST['enable_legacy_trip'] ) ) {
			update_option( 'enable_legacy_trip', $_POST['enable_legacy_trip'], true );
		}

		if ( get_option( 'enable_legacy_trip' ) === 'yes' ) {
			! defined( 'USE_WTE_LEGACY_VERSION' ) && define( 'USE_WTE_LEGACY_VERSION', true );
		}
	},
	9
);

add_action(
	'init',
	function() {
		// Register Post Type.
		// TODO: Move to separate function.
		$post_type = 'trip-packages';
		register_post_type(
			$post_type,
			array(
				'label'        => __( 'Trip Packages', 'wp-travel-engine' ),
				'public'       => ! 0,
				'show_in_rest' => ! 0,
				'rest_base'    => 'packages',
				'supports'     => array( 'title', 'editor', 'custom-fields' ),
				'show_in_menu' => false,
			)
		);

		register_taxonomy(
			'trip-packages-categories',
			$post_type,
			array(
				'public'       => ! 0,
				'show_in_rest' => ! 0,
				'rest_base'    => 'package-categories',
				'hierarchical' => ! 0,
				'show_in_menu' => true,
			)
		);

		register_term_meta(
			'trip-packages-categories',
			'is_primary_pricing_catgory',
			array(
				'type'         => 'boolean',
				'description'  => __( 'If the term is set as primary category for pricing.', 'wp-travel-engine' ),
				'single'       => true,
				'show_in_rest' => array(
					'schema' => array(
						'type' => 'boolean',
					),
				),
			)
		);

		register_term_meta(
			'trip-packages-categories',
			'age_group',
			array(
				'type'         => 'string',
				'description'  => __( 'The age group/range for the category.', 'wp-travel-engine' ),
				'single'       => true,
				'show_in_rest' => array(
					'schema' => array(
						'type' => 'string',
					),
				),
			)
		);

		foreach ( array(
			// 'dates'      => array(
			// 'type'       => 'object',
			// 'properties' => array(
			// 'dtstart' => array(
			// 'type'  => 'array',
			// 'items' => array(
			// 'type' => 'string',
			// ),
			// ),
			// ),
			// ),
			'min_pax'    => array(
				'type'     => 'number',
				'callback' => 'absint',
				'default'  => 1,
				'single'   => ! 0,
			),
			'max_pax'    => array(
				'type'     => 'number',
				'callback' => 'absint',
				'default'  => -1,
				'single'   => ! 0,
			),
			'order'      => array(
				'type'     => 'number',
				'callback' => 'absint',
				'default'  => 1,
				'single'   => ! 0,
			),
			'categories' => array(
				'type' => array(),
			),
		) as $meta_key => $args ) {
			$_args = array(
				'object_subtype'    => $post_type,
				'type'              => wte_array_get( $args, 'type', 'string' ),
				'sanitize_callback' => wte_array_get( $args, 'callback', 'wte_default_sanitize_callback' ),
				'single'            => wte_array_get( $args, 'type', ! 0 ),
				'show_in_rest'      => wte_array_get( $args, 'show_in_rest', ! 0 ),
			);
			if ( isset( $args['default'] ) ) {
				$_args['default'] = $args['default'];
			}
			register_meta(
				'post',
				$meta_key,
				$_args
			);
		}

		// TODO: Move to separate function maybe.
		add_filter(
			'wp_travel_engine_admin_trip_meta_tabs',
			function( $trip_edit_tabs ) {
				global $post;

				$status = $post->post_status;

				if ( ! defined( 'USE_WTE_LEGACY_VERSION' ) && ( get_post_meta( $post->ID, 'trip_version', true ) === '2.0.0' ) || 'draft' === $post->post_status ) {
					wp_enqueue_style( 'magnific-popup' );
					wp_enqueue_script( 'wte-rxjs' );
					wp_enqueue_script( 'wte-redux' );
					unset( $trip_edit_tabs['wpte-availability'] );
					$trip_edit_tabs['wpte-pricing']['tab_label']    = __( 'Pricings and Dates', 'wp-travel-engine' );
					$trip_edit_tabs['wpte-pricing']['content_path'] = plugin_dir_path( WP_TRAVEL_ENGINE_FILE_PATH ) . 'neo/admin/trip/edit/tab-pricing/tab-pricing.php';
				}
				return $trip_edit_tabs;
			}
		);

		add_filter(
			'wte_admin_localize_data',
			function( $data ) {
				$screen = get_current_screen();
				if ( $screen && $screen->id !== 'trip' ) {
					return $data;
				}
				global $post;

				if ( ! isset( $post->ID ) ) {
					return $data;
				}

				$trip_version = get_post_meta( $post->ID, 'trip_version', true );

				$data['wteEdit'] = array(
					'handle' => 'wp-travel-engine',
					'l10n'   => array(
						'tripID'                        => $post->ID,
						'tripMigratedToMultiplePricing' => version_compare( $trip_version, '2.0.0', '>=' ) && ( ! defined( 'USE_WTE_LEGACY_VERSION' ) || ! USE_WTE_LEGACY_VERSION ),
						'tripVersion'                   => $trip_version,
					),
				);

				return $data;
			}
		);

		if ( get_option( 'wte_migrated_to_multiple_pricing', false ) !== 'done' ) {
			if ( ! function_exists( 'WTE\Upgrade500\wte_process_migration' ) ) {
				include_once sprintf( '%s/upgrade/500.php', WP_TRAVEL_ENGINE_BASE_PATH );
				WTE\Upgrade500\wte_process_migration();
			}
		}
	}
);

add_action(
	'admin_menu',
	function() {
		global $submenu;

		$submenu['edit.php?post_type=trip'][20] = array(
			__( 'Pricing Categories', 'wp-travel-engine' ),
			'manage_categories',
			'edit-tags.php?taxonomy=trip-packages-categories&amp;post_type=trip',
		);
		return $submenu;

	}
);


add_action(
	'rest_api_init',
	function() {
		$trip_meta = null;
		function wte_default_get_callback( $prepared, $field ) {
			return get_post_meta( $prepared['id'], $field, true );
		}

		// Posttype: trip-packages.
		$packages_post_type = 'trip-packages';

		$trip_packages_rest_field = apply_filters(
			"wte_rest_fields__{$packages_post_type}",
			array(
				'package-dates'            => array(
					'type'         => 'array',
					'schema'       => array(
						'items' => array(
							'type'       => 'object',
							'properties' => array(
								'dtstart'           => array(
									'type' => 'string',
								),
								'dtend'             => array(
									'type' => 'string',
								),
								'seats'             => array(
									'type' => 'string',
								),
								'availability_type' => array(
									'type' => 'string',
								),
								'rrule'             => array(
									'type'       => 'object',
									'properties' => array(
										'enable'      => array(
											'type' => 'boolean',
										),
										'r_dtstart'   => array(
											'type' => 'string',
										),
										'r_frequency' => array(
											'type' => 'string',
										),
										'r_weekdays'  => array(
											'type'   => 'array',
											'schema' => array(
												'items' => array(
													'type' => 'string',
												),
											),
										),
										'r_until'     => array(
											'type' => 'string',
										),
										'r_months'    => array(
											'type'   => 'array',
											'schema' => array(
												'items' => array(
													'type' => 'number',
												),
											),
										),
										'r_count'     => array(
											'type' => 'number',
										),
									),
								),
							),
						),
					),
					'get_callback' => function( $prepared, $field ) {
						return array();
					},
				),
				'package-categories'       => array(
					'type'         => 'array',
					'schema'       => array(
						'type' => 'array',
					),
					'get_callback' => function( $prepared, $field ) {
						global $wpdb;

						$categories = Packages\get_packages_pricing_categories();

						$package_categories = get_post_meta( $prepared['id'], $field, ! 0 );
						$new_categories = array();
						foreach ( $categories as $category ) {
							if ( ! isset( $package_categories['c_ids'][ $category->term_id ] ) ) {
								$new_categories[ $category->term_id ] = array(
									'label'                => $category->name,
									'price'                => '',
									'enabledSale'          => ! 1,
									'salePrice'            => 0,
									'priceType'            => 'per-person',
									'minPax'               => 0,
									'maxPax'               => '',
									'groupPricing'         => array(),
									'enabledGroupDiscount' => ! 1,
								);
								continue;
							}
							foreach ( array(
								'labels'                 => array( 'label', '' ),
								'prices'                 => array( 'price', '' ),
								'enabled_sale'           => array( 'enabledSale', ! 1 ),
								'sale_prices'            => array( 'salePrice', 0 ),
								'min_paxes'              => array( 'minPax', 0 ),
								'max_paxes'              => array( 'maxPax', '' ),
								'group-pricings'         => array( 'groupPricing', array() ),
								'enabled_group_discount' => array( 'enabledGroupDiscount', ! 1 ),
								'pricing_types'          => array( 'pricingType', 'per-person' ),
							) as $key => $args ) {
								if ( isset( $package_categories[ $key ][ $category->term_id ] ) ) {
									$value = $package_categories[ $key ][ $category->term_id ];
									if ( in_array( $key, array( 'prices', 'sale_prices', 'min_paxes', 'max_paxes' ), true ) ) {
										$value = '' === $value ? '' : (float) $value;
									} elseif ( 'enabled_group_discount' === $key ) {
										$value = 1 === (int) $value;
									} elseif ( 'labels' === $key ) {
										$value = $category->name;
									}
									$new_categories[ $category->term_id ][ $args[0] ] = $value;
								} else {
									$new_categories[ $category->term_id ][ $args[0] ] = $args[1];
								}
							}
						}

						return $new_categories;
					},
				),
				'group-pricing'            => array(
					'type'         => 'array',
					'schema'       => array(
						'type' => 'array',
					),
					'get_callback' => function( $prepared, $field ) {
						return new StdClass();
					},
				),
				'primary_pricing_category' => array(
					'type'         => 'array',
					'schema'       => array(
						'type' => 'array',
					),
					'get_callback' => function( $prepared, $field ) {
						return (int) get_post_meta( $prepared['id'], $field, ! 0 );
					},
				),
				'trip_ID'                  => array(
					'type'         => 'number',
					'get_callback' => function( $prepared, $field ) {
						return (int) get_post_meta( $prepared['id'], $field, ! 0 );
					},
					'schema'       => array(
						'type' => 'number',
					),
				),
			)
		);

		foreach ( $trip_packages_rest_field as $attribute => $args ) {

			register_rest_field(
				$packages_post_type,
				$attribute,
				array(
					'get_callback'    => wte_array_get( $args, 'get_callback', 'wte_default_get_callback' ),
					'update_callback' => function() {

					},
					'schema'          => $args['schema'],
				)
			);
		}

		// Posttype: trip.
		$fields = apply_filters(
			'wte_rest_field__' . WP_TRAVEL_ENGINE_POST_TYPE,
			array(
				'packages_ids' => array(
					'schema' => array(
						'type'   => 'array',
						'schema' => array( 'items' => 'number' ),
					),
				),
				'trip_extras'  => array(
					'schema'       => array(
						'type'   => 'array',
						'schema' => array( 'items' => 'number' ),
					),
					'get_callback' => function( $object, $field_name, $default ) {
						return array();
					},
				),
				'cut_off_time' => array(
					'schema'       => array(
						'type'   => 'array',
						'schema' => array( 'items' => 'number' ),
					),
					'get_callback' => function( $object, $field_name, $default ) {
						$trip_settings = get_post_meta( $object['id'], 'wp_travel_engine_setting', ! 0 );
						return array(
							'enabled'       => (bool) wte_array_get( $trip_settings, 'trip_cutoff_enable', ! 1 ),
							'duration'      => (int) wte_array_get( $trip_settings, 'trip_cut_off_time', 0 ),
							'duration_unit' => wte_array_get( $trip_settings, 'trip_cut_off_unit', 'days' ),
						);
					},
				),
				'booked-seats' => array(
					'type'         => 'array',
					'get_callback' => function( $prepared, $field ) {
						$booked_seats = get_post_meta( $prepared['id'], 'wte_fsd_booked_seats', ! 0 );
						return ! empty( $booked_seats ) ? $booked_seats : array();
					},
					'schema'       => array(
						'type' => 'array',
					),
				),
			)
		);

		foreach ( $fields as $attribute => $args ) {
			register_rest_field(
				WP_TRAVEL_ENGINE_POST_TYPE,
				$attribute,
				array(
					'get_callback'    => wte_array_get( $args, 'get_callback', 'wte_default_get_callback' ),
					'update_callback' => function() {

					},
					'schema'          => $args['schema'],
				)
			);
		}

		// Taxonomy: Pricing Categories
		register_rest_field(
			'trip-packages-categories',
			'is-primary',
			array(
				'get_callback' => function( $object, $field_name, $default ) {
					return get_option( 'primary_pricing_category', 0 ) == $object['id'];
				},
			)
		);

	}
);

add_action( 'wpte_save_and_continue_additional_meta_data', 'wte_update_trip_packages', 10, 2 );
add_action(
	'save_post_' . WP_TRAVEL_ENGINE_POST_TYPE,
	function( $post_ID, $post, $update = false ) {
		if ( ! $update ) {
			$trip_version = get_post_meta( $post_ID, 'trip_version', true );
			if ( empty( $trip_version ) ) {
				update_post_meta( $post_ID, 'trip_version', '2.0.0' );
			}
		}
		if ( WP_TRAVEL_ENGINE_POST_TYPE === $post->post_type ) {
			wte_update_trip_packages( $post_ID, $_POST );
		}
	},
	10,
	3
);

function wte_update_trip_packages( $post_ID, $posted_data ) {
	$pricing_ids = null;
	$categories  = null;

	if ( ! isset( $posted_data['trip-edit-tab__dates-pricings'] ) ) {
		return;
	}

	foreach ( array( 'packages_ids', 'categories' ) as $meta_key ) {
		if ( ! isset( $posted_data[ $meta_key ] ) ) {
			continue;
		}
		$meta_input[ $meta_key ] = wp_unslash( $posted_data[ $meta_key ] );
	}

	$package_post_type = 'trip-packages';

	$packages_ids               = isset( $posted_data['packages_ids'] ) ? $posted_data['packages_ids'] : array();
	$packages_titles            = isset( $posted_data['packages_titles'] ) ? wp_unslash( $posted_data['packages_titles'] ) : array();
	$categories                 = isset( $posted_data['categories'] ) ? $posted_data['categories'] : array();
	$primary_pricing_categories = isset( $posted_data['packages_primary_category'] ) ? (array) $posted_data['packages_primary_category'] : array();

	$meta_packages_ids        = array();
	$primary_pricing_category = get_option( 'primary_pricing_category', 0 );
	$lowest_price             = 0;

	foreach ( $packages_ids as $index => $package_id ) {

		if ( empty( trim( $package_id ) ) ) {
			continue;
		}

		$meta_input = array();

		// Update Categories.
		if ( isset( $categories[ $package_id ] ) ) {
			$package_categories = $categories[ $package_id ];

			$meta_input['package-categories'] = $package_categories;

			if ( $primary_pricing_category && isset( $package_categories['c_ids'][ $primary_pricing_category ] ) ) {
				if ( isset( $package_categories['enabled_sale'][ $primary_pricing_category ] ) && '1' === $package_categories['enabled_sale'][ $primary_pricing_category ] ) {
					$lowest_price = ! empty( $package_categories['sale_prices'][ $primary_pricing_category ] ) && ( 0 === $lowest_price || (float) $package_categories['sale_prices'][ $primary_pricing_category ] < $lowest_price ) ? (float) $package_categories['sale_prices'][ $primary_pricing_category ] : $lowest_price;
				} else {
					$lowest_price = ! empty( $package_categories['prices'][ $primary_pricing_category ] ) && ( 0 === $lowest_price || (float) $package_categories['prices'][ $primary_pricing_category ] < $lowest_price ) ? (float) $package_categories['prices'][ $primary_pricing_category ] : $lowest_price;
				}
			}

			update_post_meta( +$package_id, 'package-categories', $package_categories );
		}

		// Update Primary Pricing Category.
		if ( isset( $primary_pricing_categories[ $package_id ] ) ) {
			$primary_pricing_category = $primary_pricing_categories[ $package_id ];

			$meta_input['primary_pricing_category'] = $primary_pricing_category;

			update_post_meta( +$package_id, 'primary_pricing_category', $primary_pricing_category );
		}

		$postarr             = new stdClass();
		$postarr->ID         = $package_id;
		$postarr->meta_input = $meta_input;
		if ( isset( $packages_titles[ $package_id ] ) ) {
			$postarr->post_title = $packages_titles[ $package_id ];
		}

		$new_package_id = wp_update_post( $postarr );
		if ( $package_id ) {
			$meta_packages_ids[ $index ] = $new_package_id;
			update_post_meta( $package_id, 'trip_ID', $post_ID );
		}
		do_action( 'save_trip_package', $new_package_id, $posted_data, $post_ID );
	}
	update_post_meta( $post_ID, 'packages_ids', array_values( $meta_packages_ids ) );
	\Wp_Travel_Engine_Admin::update_search_params_meta( get_post( $post_ID ) );
}

// New Booking Process on Trip Page.
add_action(
	'wte_after_single_trip',
	function() {
		wp_enqueue_script( 'wte-redux' );
		wte_get_template( 'script-templates/booking-process/wte-booking.php' );
	}
);

add_action(
	'trip-packages-categories_edit_form_fields',
	function( $tag, $taxonomy ) {
		$meta_value = get_option( 'primary_pricing_category', '' );
		$age_group  = get_term_meta( $tag->term_id, 'age_group', true );
		?>
		<tr class="form-field">
			<th class="row">
				<label for="package-primary-catgory"><?php esc_html_e( 'Set as Primary Pricing Catgeory', 'wp-travel-engine' ); ?></label>
			</th>
			<td>
				<input type="checkbox" <?php checked( $tag->term_id, $meta_value ); ?> name="is_primary_pricing_catgory" value="1" id="package-primary-catgory">
				<p><?php esc_html_e( 'If checked, this category will be treated as primary pricing category in packages and trip price will be the price of this category.', 'wp-travel-engine' ); ?></p>
			</td>
		</tr>
		<tr class="form-field">
			<th class="row">
				<label for="category-age-group"><?php esc_html_e( 'Age Group', 'wp-travel-engine' ); ?></label>
			</th>
			<td>
				<input type="text" placeholder="16-30" name="age_group" value="<?php echo esc_attr( $age_group ); ?>" id="category-age-group">
				<p><?php esc_html_e( 'Age Group of the category.', 'wp-travel-engine' ); ?></p>
			</td>
		</tr>
		<?php
	},
	999999,
	2
);

add_action(
	'trip-packages-categories_add_form_fields',
	function ( $taxonomy ) {
		?>
		<div class="form-field">
			<label for="package-primary-catgory"><?php esc_html_e( 'Set as Primary Pricing Catgeory', 'wp-travel-engine' ); ?></label>
			<input type="checkbox" name="is_primary_pricing_catgory" value="1" id="package-primary-catgory">
			<p><?php esc_html_e( 'If checked, this category will be treated as primary pricing category in packages and trip price will be the price of this category.', 'wp-travel-engine' ); ?></p>
		</div>
		<div class="form-field">
			<label for="category-age-group"><?php esc_html_e( 'Age Group', 'wp-travel-engine' ); ?></label>
			<input type="text" placeholder="16-30" name="age_group" value="" id="category-age-group">
			<p><?php esc_html_e( 'Age Group of the category.', 'wp-travel-engine' ); ?></p>
		</div>
		<?php
	},
	11,
	2
);


function wte_save_trip_package_categories_meta( $term_id ) {
	$value = '';
	if ( isset( $_REQUEST['is_primary_pricing_catgory'] ) ) {
		$value = $_REQUEST['is_primary_pricing_catgory'];
		update_option( 'primary_pricing_category', $term_id );
	}
	if ( isset( $_REQUEST['age_group'] ) ) {
		$value = $_REQUEST['age_group'];
		update_term_meta( $term_id, 'age_group', $value );
	}
	update_term_meta( $term_id, 'is_primary_pricing_catgory', (bool) $value );
}

add_action( 'saved_trip-packages-categories', 'wte_save_trip_package_categories_meta' );
add_action( 'update_trip-packages-categories', 'wte_save_trip_package_categories_meta' );

add_action(
	'admin_notices',
	function() {
		$admin_notices = apply_filters(
			'wte_admin_notices',
			array(
				'notice'  => array(),
				'warning' => array(),
				'error'   => array(),
			)
		);
		foreach ( $admin_notices  as $type => $messages ) {
			if ( $messages && count( $messages ) > 0 ) {
				echo '<div class="notice notice-' . esc_attr( $type ) . '">';
				foreach ( $messages as $message ) {
					echo '<p>' . wp_kses(
						$message,
						array(
							'code' => array(),
							'a'    => array(
								'href'   => array(),
								'class'  => array(),
								'id'     => array(),
								'target' => array(),
							),
						)
					) . '</p>';
				}
				echo '</div>';
			}
		}
	}
);
