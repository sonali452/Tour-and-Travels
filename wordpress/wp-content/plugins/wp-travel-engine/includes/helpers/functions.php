<?php
use \Firebase\JWT\JWT;
use WPTravelEngine\Core\Functions;
/**
 * WTE Helper functions.
 */


/**
 * Gets value of provided index.
 *
 * @param array  $array Array to pick value from.
 * @param string $index Index.
 * @param any    $default Default Values.
 * @return mixed
 */
function wte_array_get( $array, $index = null, $default = null ) {
	if ( ! is_array( $array ) ) {
		return $default;
	}
	if ( is_null( $index ) ) {
		return $array;
	}
	$multi_label_indices = explode( '.', $index );
	$value               = $array;
	foreach ( $multi_label_indices as $key ) {
		if ( ! isset( $value[ $key ] ) ) {
			$value = $default;
			break;
		}
		$value = $value[ $key ];
	}
	return $value;
}

/**
 * Generate Random Integer.
 */
function wte_get_random_integer( $min, $max ) {
		$range = ( $max - $min );

	if ( $range < 0 ) {
		// Not so random...
		return $min;
	}

	$log = log( $range, 2 );

	// Length in bytes.
	$bytes = (int) ( $log / 8 ) + 1;

	// Length in bits.
	$bits = (int) $log + 1;

	// Set all lower bits to 1.
	$filter = (int) ( 1 << $bits ) - 1;

	do {
		$rnd = hexdec( bin2hex( openssl_random_pseudo_bytes( $bytes ) ) );

		// Discard irrelevant bits.
		$rnd = $rnd & $filter;

	} while ( $rnd >= $range );

	return ( $min + $rnd );
}

/**
 * Generates uniq ID.
 *
 * @return void
 */
function wte_uniqid( $length = 5 ) {
	if ( ! isset( $length ) || intval( $length ) < 5 ) {
		$length = 5;
	}
	$token      = '';
	$characters = implode( range( 'a', 'z' ) ) . implode( range( 'A', 'Z' ) );
	for ( $i = 0; $i < $length; $i++ ) {
		$random_key = wte_get_random_integer( 0, strlen( $characters ) );
		$token     .= $characters[ $random_key ];
	}

	return $token;

	// if ( function_exists( 'random_bytes' ) ) {
	// return bin2hex( random_bytes( $length ) );
	// }
	// if ( function_exists( 'mcrypt_create_iv' ) ) {
	// return bin2hex( mcrypt_create_iv( $length, MCRYPT_DEV_URANDOM ) );
	// }
	// if ( function_exists( 'openssl_random_pseudo_bytes' ) ) {
	// return bin2hex( openssl_random_pseudo_bytes( $length ) );
	// }
	// return uniqid();
}

/**
 * Generate JWT.
 *
 * @return void
 */
function wte_jwt( array $payload, string $key ) {
	return JWT::encode( $payload, $key );
}

/**
 * Decode JWT.
 */
function wte_jwt_decode( string $jwt, string $key ) {
	return JWT::decode( $jwt, $key, array( 'HS256' ) );
}

/**
 * WTE Log data in json format.
 *
 * @param mixed $data
 * @return void
 */
function wte_log( $data, $name = 'data', $dump = false ) {
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		$data = wp_json_encode( array( $name => $data ), JSON_PRETTY_PRINT );
		error_log( $data, 3, WP_CONTENT_DIR .'/wte.log' ); // phpcs:ignore
		if ( $dump ) {
			var_dump( $data );
		} else {
			return $data;
		}
	}
};

/**
 * Returns Booking Email instance.
 *
 * @return WTE_Booking_Emails
 */
function wte_booking_email() {
	// Mail class.
	require_once plugin_dir_path( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/class-wp-travel-engine-emails.php';
	return new WTE_Booking_Emails();
}

/**
 * Undocumented function
 *
 * @since 4.3.8
 * @return void
 */
function wte_form_fields( array $fields, $echo = ! 0 ) {
	ob_start();
	( new WTE_Field_Builder_Admin( $fields ) )->render();
	$html = ob_get_clean();

	if ( $echo ) {
		echo $html;
	} else {
		return $html;
	}
}

/**
 * Availability Options.
 */
function wte_get_availability_options( $key = ! 1 ) {
	$options = apply_filters(
		'wte_date_availability_options',
		array(
			'guaranteed' => __( 'Guaranteed', 'wp-travel-engine' ),
			'available'  => __( 'Available', 'wp-travel-engine' ),
			'limited'    => __( 'Limited', 'wp-travel-engine' ),
		)
	);
	if ( $key && isset( $options[ $key ] ) ) {
		return $options[ $key ];
	} else {
		return $options;
	}
}

/**
 * Get Requested Raw Data.
 *
 * @return void
 */
function wte_get_request_raw_data() {
	// phpcs:disable PHPCompatibility.Variables.RemovedPredefinedGlobalVariables.http_raw_post_dataDeprecatedRemoved
	global $HTTP_RAW_POST_DATA;

	// $HTTP_RAW_POST_DATA was deprecated in PHP 5.6 and removed in PHP 7.0.
	if ( ! isset( $HTTP_RAW_POST_DATA ) ) {
		$HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
	}

	return $HTTP_RAW_POST_DATA;
	// phpcs:enable
}

/**
 * Timezone info.
 *
 * @return void
 */
function wte_get_timezone_info() {
	$tz_string     = get_option( 'timezone_string' );
	$timezone_info = array();

	if ( $tz_string ) {
		try {
			$tz = new DateTimezone( $tz_string );
		} catch ( Exception $e ) {
			$tz = '';
		}

		if ( $tz ) {
			$now                  = new DateTime( 'now', $tz );
			$formatted_gmt_offset = wte_format_gmt_offset( $tz->getOffset( $now ) / 3600 );
			$tz_name              = str_replace( '_', ' ', $tz->getName() );
		}
	} else {
		$formatted_gmt_offset = wte_format_gmt_offset( (float) get_option( 'gmt_offset', 0 ) );

		$timezone_info['description'] = sprintf(
			/* translators: 1: UTC abbreviation and offset, 2: UTC offset. */
			__( 'Your timezone is set to %1$s (Coordinated Universal Time %2$s).', 'wp-travel-engine' ),
			'<abbr>UTC</abbr>' . $formatted_gmt_offset,
			$formatted_gmt_offset
		);
	}

	return $formatted_gmt_offset;
}

/**
 *
 */
function wte_format_gmt_offset( $offset ) {
	$offset = number_format( $offset, 2 );

	if ( 0 <= (float) $offset ) {
		$formatted_offset = '+' . (string) $offset;
	} else {
		$formatted_offset = (string) $offset;
	}

	preg_match( '/(\+|\-)?(\d+\.\d+)/', $formatted_offset, $matches );

	if ( isset( $matches[2] ) ) {
		$formatted_offset = substr( '0000' . $matches[2], -5 );
	}

	$formatted_offset = $matches[1] . $formatted_offset;

	$formatted_offset = str_replace(
		array( '.25', '.50', '.75', '.00' ),
		array( ':15', ':30', ':45', ':00' ),
		$formatted_offset
	);
	return $formatted_offset;
}

function wte_get_trip( $trip = null ) {
	if ( empty( $trip ) && isset( $GLOBALS['wtetrip'] ) ) {
		$trip = $GLOBALS['wtetrip'];
	}

	if ( $trip instanceof Posttype\Trip ) {
		$_trip = $trip;
	} else {
		$_trip = WPTravelEngine\Posttype\Trip::instance( $trip );
	}

	if ( ! $_trip ) {
		return null;
	}

	return $_trip;
}

function wte_get_engine_extensions() {
	$plugins = get_plugins();

	$matches = array();
	foreach ( $plugins as $file => $plugin ) {
		if ( 'WordPress Travel Booking Plugin - WP Travel Engine' !== $plugin['Name'] && ( stristr( $plugin['Name'], 'wp travel engine' ) || stristr( $plugin['Description'], 'wp travel engine' ) ) ) {
			$matches[ $file ] = $plugin;
		}
	}

	return $matches;
}

function wte_get_extensions_ids( $key = null ) {
	$ids = apply_filters(
		'wp_travel_engine_addons_id',
		array(
			'wte_group_discount'           => 146,
			'wte_currency_converter'       => 30074,
			'wte_fixed_starting_dates'     => 79,
			'wte_midtrans'                 => 31412,
			'wte_hbl_payment'              => 20311,
			'wte_partial_payment'          => 1750,
			'wte_payfast'                  => 1744,
			'wte_paypal_express'           => 7093,
			'wte_payu'                     => 1055,
			'wte_advanced_itinerary'       => 31567,
			'wte_advance_search'           => 1757,
			'wte_authorize_net'            => 577,
			'wte_extra_services'           => 20573,
			'wte_form_editor'              => 33247,
			'wte_payhere_payment'          => 30754,
			'wte_payu_money_bolt_checkout' => 30752,
			'wte_stripe_gateway'           => 557,
			'wte_trip_code'                => 40085,
			'wte_coupons'                  => 42678,
		)
	);
	if ( $key && ! isset( $ids[ $key ] ) ) {
		return false;
	}

	return $key ? $ids[ $key ] : $ids;
}

function wte_functions() {
	return new Functions();
}

function wte_readonly( $value, $check_against, $echo = true ) {
	if ( ( is_array( $check_against ) && in_array( $value, $check_against ) )
		|| ( ! is_array( $check_against ) && $value === $check_against )
		) {
		if ( $echo ) {
			echo 'readonly=\"readonly\"';
		}
		return true;
	}
}

/**
 * Gets Trip Reviews.
 */
function wte_get_trip_reviews( $trip_id ) {
	global $wpdb;

	// SELECT c.comment_content, JSON_OBJECTAGG(wp_commentmeta.`meta_key`, wp_commentmeta.meta_value)  FROM wp_comments as c INNER JOIN wp_commentmeta WHERE c.comment_post_ID = 22 AND c.comment_ID = wp_commentmeta.comment_id GROUP BY wp_commentmeta.comment_id
	$where = "c.comment_ID = cm.comment_id AND c.comment_post_ID = {$trip_id}";
	$query = "SELECT c.comment_ID, c.comment_content, JSON_OBJECTAGG(cm.meta_key, cm.meta_value) as reviews_meta FROM {$wpdb->comments} as c INNER JOIN {$wpdb->commentmeta} as cm WHERE {$where} GROUP BY cm.comment_id";

	$results = $wpdb->get_results( $query );

	$_result = array();
	if ( $results && is_array( $results ) ) {
		$reviews_meta = array(
			'phone'           => '',
			'title'           => '',
			'stars'           => 0,
			'experience_date' => '',
		);
		$i            = 0;
		foreach ( $results as $result ) {
			$_result[ $i ]['ID']      = (int) $result->comment_ID;
			$_result[ $i ]['content'] = $result->comment_content;

			if ( isset( $result->reviews_meta ) && json_decode( $result->reviews_meta ) ) {
				$_metas = json_decode( $result->reviews_meta );
				foreach ( $reviews_meta as $key => $value ) {
					if ( isset( $_metas->$key ) ) {
						$_result[ $i ][ $key ] = 'stars' === $key ? (int) $_metas->{$key} : $_metas->{$key};
					} else {
						$_result[ $i ][ $key ] = $value;
					}
				}
			}
			$i++;
		}
	}

	$stars = array_column( $_result, 'stars' );

	return array(
		'reviews' => $_result,
		'average' => count( $stars ) > 0 ? array_sum( $stars ) / count( $stars ) : 0,
		'count'   => count( $stars ),
	);
}

/**
 * Use it as a templating function inside loop.
 */
function wte_get_the_trip_reviews( $trip_id = null ) {
	if ( ! defined( 'WTE_TRIP_REVIEW_VERSION' ) ) {
		return '';
	}
	if ( is_null( $trip_id ) ) {
		$trip_id = get_the_ID();
	}

	$trip_reviews = (object) wte_get_trip_reviews( $trip_id );
	ob_start();
	?>
	<div class="wpte-trip-review-stars">
		<div class="stars-group-wrapper">
			<div class="stars-placeholder-group">
				<?php
				echo implode(
					'',
					array_map(
						function() {
							return '<svg width="15" height="15" viewBox="0 0 15 15" fill="none"><path d="M6.41362 0.718948C6.77878 -0.0301371 7.84622 -0.0301371 8.21138 0.718948L9.68869 3.74946C9.83326 4.04602 10.1148 4.25219 10.4412 4.3005L13.7669 4.79272C14.5829 4.91349 14.91 5.91468 14.3227 6.49393L11.902 8.88136C11.6696 9.1105 11.5637 9.4386 11.6182 9.76034L12.1871 13.1191C12.3258 13.9378 11.464 14.559 10.7311 14.1688L7.78252 12.5986C7.4887 12.4421 7.1363 12.4421 6.84248 12.5986L3.89386 14.1688C3.16097 14.559 2.29922 13.9378 2.43789 13.1191L3.0068 9.76034C3.06129 9.4386 2.95537 9.1105 2.72303 8.88136L0.302324 6.49393C-0.285 5.91468 0.0420871 4.91349 0.85811 4.79272L4.18383 4.3005C4.5102 4.25219 4.79174 4.04602 4.93631 3.74946L6.41362 0.718948Z" fill="#EBAD34"></path></svg>';
						},
						range( 0, 4 )
					)
				);
				?>
			</div>
			<div
				class="stars-rated-group"
				style="width: <?php echo esc_attr( $trip_reviews->average * 20 ); ?>%"
			>
			<?php
				echo implode(
					'',
					array_map(
						function() {
							return '<svg width="15" height="15" viewBox="0 0 15 15" fill="none"><path d="M6.41362 0.718948C6.77878 -0.0301371 7.84622 -0.0301371 8.21138 0.718948L9.68869 3.74946C9.83326 4.04602 10.1148 4.25219 10.4412 4.3005L13.7669 4.79272C14.5829 4.91349 14.91 5.91468 14.3227 6.49393L11.902 8.88136C11.6696 9.1105 11.5637 9.4386 11.6182 9.76034L12.1871 13.1191C12.3258 13.9378 11.464 14.559 10.7311 14.1688L7.78252 12.5986C7.4887 12.4421 7.1363 12.4421 6.84248 12.5986L3.89386 14.1688C3.16097 14.559 2.29922 13.9378 2.43789 13.1191L3.0068 9.76034C3.06129 9.4386 2.95537 9.1105 2.72303 8.88136L0.302324 6.49393C-0.285 5.91468 0.0420871 4.91349 0.85811 4.79272L4.18383 4.3005C4.5102 4.25219 4.79174 4.04602 4.93631 3.74946L6.41362 0.718948Z" fill="#EBAD34"></path></svg>';
						},
						range( 0, 4 )
					)
				);
			?>
			</div>
		</div>
		<span class="wpte-trip-review-count"><?php printf( _n( '%d Review', '%d Reviews', $trip_reviews->count, 'wp-travel-engine' ), $trip_reviews->count ); ?></span>
	</div>
	<?php

	return ob_get_clean();
}

function wte_the_trip_reviews() {
	echo wte_get_the_trip_reviews( get_the_ID() );
}

function wte_get_the_excerpt( $trip_id = null, $words = 25 ) {
	if ( is_null( $trip_id ) ) {
		$trip_id = get_the_ID();
	}

	return wp_trim_words( get_the_excerpt( $trip_id ), $words, '...' );
}

function wte_list( $array, $vars ) {
	$_array = array();
	if ( is_array( $array ) && is_array( $vars ) ) {
		foreach ( $vars as $index => $key ) {
			$_array[ $index ] = isset( $array[ $key ] ) ? $array[ $key ] : null;
		}
	}

	return $_array;
}

function wte_get_media_details( $media_id ) {
	$media_details = \wp_get_attachment_metadata( $media_id );

	// Ensure empty details is an empty object.
	if ( empty( $media_details ) ) {
		$media_details = new \stdClass();
	} elseif ( ! empty( $media_details['sizes'] ) ) {

		foreach ( $media_details['sizes'] as $size => &$size_data ) {

			if ( isset( $size_data['mime-type'] ) ) {
				$size_data['mime_type'] = $size_data['mime-type'];
				unset( $size_data['mime-type'] );
			}

			// Use the same method image_downsize() does.
			$image_src = wp_get_attachment_image_src( $media_id, $size );
			if ( ! $image_src ) {
				continue;
			}

			$size_data['source_url'] = $image_src[0];
		}

		$full_src = wp_get_attachment_image_src( $media_id, 'full' );

		if ( ! empty( $full_src ) ) {
			$media_details['sizes']['full'] = array(
				'file'       => wp_basename( $full_src[0] ),
				'width'      => $full_src[1],
				'height'     => $full_src[2],
				// 'mime_type'  => $post->post_mime_type,
				'source_url' => $full_src[0],
			);
		}
	} else {
		$media_details['sizes'] = new \stdClass();
	}

	unset( $media_details->{'image_meta'} );

	return $media_details;
}

/**
 * Checks if trip has group discount.
 */
function wte_has_trip_group_discount( $trip_id ) {
	return \apply_filters( 'has_packages_group_discounts', false, $trip_id );
}

function wte_get_terms_by_id( $taxonomy, $args = array() ) {
	$terms        = get_terms( $taxonomy, $args );
	$terms_by_ids = array();

	if ( is_array( $terms ) ) {
		foreach ( $terms as $term_object ) {
			$term_object->children  = array();
			$term_object->link      = get_term_link( $term_object->term_id );
			$term_object->thumbnail = (int) get_term_meta( $term_object->term_id, 'category-image-id', true );
			if ( isset( $terms_by_ids[ $term_object->term_id ] ) ) {
				foreach ( (array) $terms_by_ids[ $term_object->term_id ] as $prop_name => $prop_value ) {
					$term_object->{$prop_name} = $prop_value;
				}
			}
			if ( $term_object->parent ) {
				if ( ! isset( $terms_by_ids[ $term_object->parent ] ) ) {
					$terms_by_ids[ $term_object->parent ] = new \stdClass();
				}
				$terms_by_ids[ $term_object->parent ]->children[] = $term_object->term_id;
			}

			$terms_by_ids[ $term_object->term_id ] = $term_object;
		}
	}

	return $terms_by_ids;
}

// wte_trip_get_trip_rest_metadata
function wte_trip_get_trip_rest_metadata( $trip_id ) {

	$post = get_post( $trip_id );

	$trip_details = \wte_get_trip_details( $trip_id );

	$data = new \stdClass();

	$featured_media = get_post_thumbnail_id( $trip_id );

	foreach ( array(
		'code'             => array(
			'key'  => 'trip_settings.trip_code',
			'type' => 'string',
		),
		'price'            => array(
			'key'  => 'display_price',
			'type' => 'number',
		),
		'has_sale'         => array(
			'key'  => 'on_sale',
			'type' => 'boolean',
		),
		'sale_price'       => array(
			'key'  => 'sale_price',
			'type' => 'number',
		),
		'discount_percent' => array(
			'key'     => 'discount_percent',
			'type'    => 'number',
			'decimal' => 0,
		),
		'currency'         => array(
			'type'  => 'array',
			'items' => array(
				'code'   => array(
					'key'  => 'code',
					'type' => 'string',
				),
				'symbol' => array(
					'key'  => 'currency',
					'type' => 'string',
				),
			),
		),
		'duration'         => array(
			'type'  => 'array',
			'items' => array(
				'days'   => array(
					'key'  => 'trip_duration',
					'type' => 'number',
				),
				'nights' => array(
					'key'  => 'trip_duration_nights',
					'type' => 'number',
				),
			),
		),
	) as $property_name => $args ) {
		$value = isset( $args['key'] ) ? wte_array_get( $trip_details, $args['key'], '' ) : '';

		if ( 'array' === $args['type'] && isset( $args['items'] ) ) {
			$value = array();
			$items = $args['items'];
			foreach ( $items as $sub_property_name => $item ) {
				if ( isset( $trip_details[ $item['key'] ] ) ) {
					if ( 'number' === $item['type'] ) {
						$decimal                     = isset( $item['decimal'] ) ? (int) $item['decimal'] : 0;
						$value[ $sub_property_name ] = round( (float) $trip_details[ $item['key'] ], $decimal );
					} else {
						$value[ $sub_property_name ] = $trip_details[ $item['key'] ];
					}
				}
			}
			$data->{$property_name} = $value;
			continue;
		}
		$data->{$property_name} = 'number' === $args['type'] ? round( (float) $value, 2 ) : $value;
	}

	// $wte_trip = \wte_get_trip( $trip_id );

	$lowest_package = WPTravelEngine\Packages\get_trip_lowest_price_package( $trip_id );

	$primary_category_id = get_option( 'primary_pricing_category', 0 );
	$primary_category    = new \stdClass();
	if ( isset( $lowest_package->{'package-categories'} ) && $primary_category_id ) {
		$package_categories = $lowest_package->{'package-categories'};

		foreach ( array(
			'prices'        => 'price',
			'labels'        => 'label',
			'pricing_types' => 'pricing_type',
			'enabled_sale'  => 'has_sale',
			'sale_prices'   => 'sale_price',
			'min_paxes'     => 'min_pax',
			'max_paxes'     => 'max_pax',
		) as $source => $key ) {
			if ( isset( $package_categories[ $source ][ $primary_category_id ] ) ) {
				$value = in_array( $key, array( 'price', 'sale_price', 'has_sale' ) ) ? (float) $package_categories[ $source ][ $primary_category_id ] : $package_categories[ $source ][ $primary_category_id ];
			} else {
				$value = in_array( $key, array( 'price', 'sale_price', 'has_sale' ) ) ? 0 : '';
			}
			$primary_category->{$key} = $value;
		}
	}

	$data->price            = isset( $primary_category->price ) && $primary_category->price != '' ? (int) $primary_category->price : '';
	$data->has_sale         = isset( $primary_category->has_sale ) ? $primary_category->has_sale : false;
	$data->sale_price       = isset( $primary_category->sale_price ) && $primary_category->sale_price != '' ? (int) $primary_category->sale_price : '';
	$data->primary_category = $primary_category_id;
	$data->available_times  = array(
		'type'  => 'default',
		'items' => array_map(
			function( $month ) {
				return "2021-{$month}-01";
			},
			range( 1, 12 )
		),
	);

	$trip_settings = get_post_meta( $trip_id, 'wp_travel_engine_setting', true );
	// Group Size.
	$data->min_pax = '';
	if ( ! empty( $trip_settings['trip_minimum_pax'] ) ) {
		$data->min_pax = (int) $trip_settings['trip_minimum_pax'];
	}
	$data->max_pax = '';
	if ( ! empty( $trip_settings['trip_maximum_pax'] ) ) {
		$data->max_pax = (int) $trip_settings['trip_maximum_pax'];
	}

	if ( isset( $trip_settings['trip_facts'][2][2] ) ) {
		$data->group_size = $trip_settings['trip_facts'][2][2];
	}

	$data->is_featured = \wte_is_trip_featured( $trip_id );

	if ( defined( 'WTE_TRIP_REVIEW_VERSION' ) ) {
		$data->{'trip_reviews'} = \wte_get_trip_reviews( $trip_id );
	}

	$media_details = \wte_get_media_details( $featured_media );

	$data->featured_image = $media_details;

	return $data;
}

/**
 * Retrive currency code.
 *
 * @since 5.2.0
 */
function wte_currency_code() {
	$code = wte_array_get( get_option( 'wp_travel_engine_settings', array() ), 'currency_code', 'USD' );

	return wte_functions()->wp_travel_engine_currencies_symbol( $code );
}
