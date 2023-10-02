<?php
namespace WPTravelEngine;

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @since      1.0.0
 *
 * @package    Wp_Travel_Engine
 * @subpackage Wp_Travel_Engine/includes
 */
defined( 'ABSPATH' ) || exit;

/**
 * Main Class.
 */
final class Plugin {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wp_Travel_Engine_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Singleton instance.
	 *
	 * @since 5.0.0
	 * @access protected
	 * @var null|WP_Travel_Engine
	 */
	protected static $instance = null;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'wp-travel-engine';
		$this->version     = WP_TRAVEL_ENGINE_VERSION;
		$this->define_constants();
		$this->load_dependencies();

		$this->loader = new \Wp_Travel_Engine_Loader();

		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->init_hooks();
		$this->init_shortcodes();
		add_filter( 'widget_text', 'do_shortcode' );
		add_filter( 'meta_content', 'wptexturize' );
		add_filter( 'meta_content', 'convert_smilies' );
		add_filter( 'meta_content', 'convert_chars' );
		add_filter( 'meta_content', 'shortcode_unautop' );
		add_filter( 'meta_content', 'prepend_attachment' );
		add_filter( 'meta_content', 'do_shortcode' );
		add_filter( 'term_description', 'wpautop' );

		$this->run();
	}

	/**
	 * Define constants.
	 *
	 * @since 5.0.0
	 * @return void
	 */
	private function define_constants() {
		foreach ( array(
			'WP_TRAVEL_ENGINE_BASE_PATH'           => dirname( WP_TRAVEL_ENGINE_FILE_PATH ),
			'WP_TRAVEL_ENGINE_ABSPATH'             => dirname( WP_TRAVEL_ENGINE_FILE_PATH ) . '/',
			'WP_TRAVEL_ENGINE_IMG_PATH'            => dirname( WP_TRAVEL_ENGINE_FILE_PATH ) . '/admin/css/icons',
			'WP_TRAVEL_ENGINE_TEMPLATE_PATH'       => dirname( WP_TRAVEL_ENGINE_FILE_PATH ) . '/includes/templates',
			'WP_TRAVEL_ENGINE_FILE_URL'            => plugins_url( '', WP_TRAVEL_ENGINE_FILE_PATH ),
			'WP_TRAVEL_ENGINE_POST_TYPE'           => 'trip',
			'WP_TRAVEL_ENGINE_TRIP_VERSION'        => '2.0.0',
			'WP_TRAVEL_ENGINE_URL'                 => rtrim( plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ), '/' ),
			'WP_TRAVEL_ENGINE_IMG_URL'             => rtrim( plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ), '/' ),
			'WP_TRAVEL_ENGINE_STORE_URL'           => 'https://wptravelengine.com/',
			'WP_TRAVEL_ENGINE_PLUGIN_LICENSE_PAGE' => 'wp_travel_engine_license_page',
			'WP_TRAVEL_ENGINE_PAYMENT_DEBUG'       => call_user_func(
				function() {
					$option = get_option( 'wp_travel_engine_settings', array() );
					return isset( $option['payment_debug'] ) && 'yes' === $option['payment_debug'];
				}
			),
		) as $name => $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}
	}

	private function check_version() {
		if ( ! defined( 'IFRAME_REQUEST' ) ) {
			if ( version_compare( get_option( 'wptravelengine_version', '0.0.0' ), WP_TRAVEL_ENGINE_VERSION, '<' ) ) {
				update_option( 'wptravelengine_version', WP_TRAVEL_ENGINE_VERSION );
			}
			if ( version_compare( get_option( 'wptravelengine_trip_version', '0.0.0' ), WP_TRAVEL_ENGINE_TRIP_VERSION, '<' ) ) {
				update_option( 'wptravelengine_trip_version', WP_TRAVEL_ENGINE_TRIP_VERSION );
			}
			if ( ! get_option( 'wptravelengine_since', false ) ) {
				update_option( 'wptravelengine_since', WP_TRAVEL_ENGINE_VERSION );
			}
		}
	}

	/**
	 * Returns instance of the Class.
	 *
	 * @return void
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function init_hooks() {
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ), 20 );
		add_action( 'init', array( $this, 'init' ) );

		register_activation_hook(
			WP_TRAVEL_ENGINE_FILE_PATH,
			function() {
				require_once plugin_dir_path( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/class-wp-travel-engine-activator.php';
				\Wp_Travel_Engine_Activator::activate();

				if ( version_compare( WP_TRAVEL_ENGINE_VERSION, '4.2.1', '>=' ) ) {
					include_once sprintf( '%s/upgrade/500.php', WP_TRAVEL_ENGINE_BASE_PATH );
					\WTE\Upgrade500\wte_process_migration();
				}
			}
		);

		register_deactivation_hook(
			WP_TRAVEL_ENGINE_FILE_PATH,
			function() {
				require_once plugin_dir_path( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/class-wp-travel-engine-deactivator.php';
				\Wp_Travel_Engine_Deactivator::deactivate();
			}
		);

		add_action(
			'activated_plugin',
			function() {
				$path    = str_replace( WP_CONTENT_DIR . '/plugins/', '', WP_TRAVEL_ENGINE_FILE_PATH );
				$plugins = get_option( 'active_plugins', array() );
				if ( ! empty( $plugins ) ) {
					$key = array_search( $path, $plugins, true );
					if ( ! empty( $key ) ) {
						array_splice( $plugins, $key, 1 );
						array_unshift( $plugins, $path );
						update_option( 'active_plugins', $plugins );
					}
				}
			}
		);

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action(
			'admin_init',
			function() {
				// Check version.
				$this->check_version();
			}
		);

		add_filter(
			'term_name',
			function( $name, $tag ) {
				if ( isset( $tag->{'taxonomy'} ) && 'trip-packages-categories' === $tag->{'taxonomy'} ) {
					$primary_category = get_option( 'primary_pricing_category', 0 );
					if ( $primary_category == $tag->term_id ) {
						$name .= ' — &#128974;';
					}
				}

				return $name;
			},
			10,
			2
		);

		add_action(
			'init',
			function() {

				if ( wte_array_get( $_REQUEST, '_action', '' ) == 'email-template-preview' ) {
					if ( ! isset( $_REQUEST['pid'] ) ) {
						return;
					}

					// Mail class.
					require_once plugin_dir_path( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/class-wp-travel-engine-emails.php';

					\WTE_Booking_Emails::template_preview( $_REQUEST['pid'], wte_array_get( $_REQUEST, 'template_type', 'order' ), wte_array_get( $_REQUEST, 'to', 'customer' ) );
				}

				if ( wte_array_get( $_REQUEST, '_action', '' ) == 'wte-email-template-update' ) {
					if ( ! isset( $_REQUEST['field'] ) ) {
						return;
					}
					switch ( $_REQUEST['field'] ) {
						case 'email.sales_wpeditor':
							$settings                            = get_option( 'wp_travel_engine_settings', array() );
							$settings['email']['sales_wpeditor'] = '';
							update_option( 'wp_travel_engine_settings', $settings );
							update_option( 'payment_notification_admin_version', '2.0.0' );
							break;
						case 'email.purchase_wpeditor':
							$settings                               = get_option( 'wp_travel_engine_settings', array() );
							$settings['email']['purchase_wpeditor'] = '';
							update_option( 'wp_travel_engine_settings', $settings );
							update_option( 'payment_notification_customer_version', '2.0.0' );
							break;
					}
				}
			}
		);

		// @TODO: Move to form Editor
		add_filter(
			'wte_booking_mail_tags',
			function( $mail_tags, $payment_id ) {
				$booking_id = get_post_meta( $payment_id, 'booking_id', ! 0 );
				$booking    = get_post( $booking_id );

				if ( is_null( $booking ) || 'booking' !== $booking->post_type ) {
					return $mail_tags;
				}

				$additional_fields = wte_array_get( get_post_meta( $booking->ID, 'additional_fields', ! 0 ), null, array() );

				foreach ( $additional_fields as $field_name => $field_value ) {
					$mail_tags[ '{' . $field_name . '}' ] = is_array( $field_value ) ? implode( ',', $field_value ) : $field_value;
				}

				// Move to Discount Coupon.
				// Discount Tags.
				$mail_tags['{discount_name}']   = '';
				$mail_tags['{discount_amount}'] = '';
				$mail_tags['{discount_sign}']   = '';
				$mail_tags['{discount_value}']  = '';

				if ( isset( $booking->cart_info['discounts'] ) ) {
					$discounts = $booking->cart_info['discounts'];
					if ( ! is_array( $discounts ) || empty( $discounts ) ) {
						return $mail_tags;
					}
					$discount  = (object) array_shift( $discounts );
					$cart_info = $booking->cart_info;

					$mail_tags['{discount_name}']   = $discount->name;
					$mail_tags['{discount_amount}'] = 'percentage' === $discount->type ? wte_get_formated_price( ( +$cart_info['subtotal'] * ( +$discount->value ) / 100 ), $cart_info['currency'] ) : wte_get_formated_price( $discount->value, $cart_info['currency'] );
					$mail_tags['{discount_sign}']   = 'percentage' === $discount->type ? '%' : $cart_info['currency'];
					$mail_tags['{discount_value}']  = 'percentage' === $discount->type ? $discount->value : wte_get_formated_price( $discount->value, $cart_info['currency'] );
				}

				return $mail_tags;
			},
			11,
			2
		);

		add_action( 'plugins_loaded', array( __CLASS__, 'init_freemus' ) );

		add_filter( 'extra_theme_headers', array( $this, 'plugin_headers' ) );
		add_filter( 'extra_plugin_headers', array( $this, 'plugin_headers' ) );

		// Show changelog for 5.0.
		add_filter( 'wte_show_changelog_for_500', '__return_true' );

		add_filter(
			'display_post_states',
			function( $states, $post ) {
				if ( ! in_array( $post->post_type, array( 'page', WP_TRAVEL_ENGINE_POST_TYPE ) ) ) {
					return $states;
				}
				$pages  = wte_array_get( get_option( 'wp_travel_engine_settings', array() ), 'pages', array() );
				$pages  = is_array( $pages ) ? array_flip( $pages ) : array();
				$labels = array(
					'wp_travel_engine_place_order'       => __( 'WTE Checkout', 'wp-travel-engine' ),
					'wp_travel_engine_terms_and_conditions' => __( 'WTE Terms and Conditions', 'wp-travel-engine' ),
					'wp_travel_engine_thank_you'         => __( 'WTE Thank You', 'wp-travel-engine' ),
					'wp_travel_engine_confirmation_page' => __( 'WTE Travelers Information', 'wp-travel-engine' ),
					'wp_travel_engine_dashboard_page'    => __( 'WTE Dashboard', 'wp-travel-engine' ),
					'enquiry'                            => __( 'WTE Enquiry Thank You', 'wp-travel-engine' ),
					'search'                             => __( 'WTE Search Results', 'wp-travel-engine' ),
				);

				if ( ! empty( $post->trip_version ) ) {
					$version_parts       = explode( '.', $post->trip_version );
					$states[ $post->ID ] = $version_parts[0] . '.' . $version_parts[1];
				}

				if ( isset( $pages[ $post->ID ] ) ) {
					$states[ $pages[ $post->ID ] ] = $labels[ $pages[ $post->ID ] ];
				}
				return $states;
			},
			11,
			2
		);

	}

	public function init() {}

	public function plugins_loaded() {

		if ( is_admin() && ! empty( $_REQUEST['action'] ) && 'activate' === $_REQUEST['action'] && isset( $_REQUEST['plugin'] ) ) {
			$plugin = wp_unslash( $_REQUEST['plugin'] );
			if ( strpos( $plugin, 'wte-advanced-search.php' ) > -1 ) {
				if ( headers_sent() ) {
					echo "<meta http-equiv='refresh' content='" . esc_attr( '0;url=plugins.php?deactivate=true&plugin_status=all&paged=1' ) . "' />";
				} else {
					wp_redirect( self_admin_url( 'plugins.php?deactivate=true&plugin_status=all&paged=1' ) );
				}
				exit;
			}
		}

		// Deactivate core integrated Plugin.
		foreach ( array(
			'WTE_TRIP_CODE_FILE_PATH'              => __( 'Trip Code', 'wp-travel-engine' ),
			'WP_TRAVEL_ENGINE_COUPONS_PLUGIN_FILE' => __( 'Coupon Code', 'wp-travel-engine' ),
			'WTE_ADVANCED_SEARCH_FILE_PATH'        => __( 'Advanced Search', 'wp-travel-engine' ),
		) as $constant_name => $plugin_name ) {
			if ( defined( $constant_name ) ) {
				$plugin = \constant( $constant_name );
				deactivate_plugins( $plugin );

				add_action(
					'admin_notices',
					function() use ( $plugin_name ) {
						printf(
							'<div id="message" class="notice notice-info is-dismissible"><p>%1$s</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">%2$s</span></button></div>',
							sprintf( __( '%1$s has been automatically deactivated, the feature providing by the plugin is now available in the WP Travel Engine Core.', 'wp-travel-engine' ), $plugin_name ),
							__( 'Dismiss this notice.', 'wp-travel-engine' )
						);
					}
				);
			}
		}

		if ( ! class_exists( 'Wte_Advanced_Search' ) ) {
			include_once dirname( __FILE__ ) . '/modules/trip-search/backward-compatibility.php';
		}
	}

	/**
	 *
	 * @param [type] $headers
	 * @since 4.3.8
	 * @return void
	 */
	public function plugin_headers( $headers ) {
		// WTE requires at least.
		$headers[] = 'WTE requires at least';
		// WTE Tested up to.
		$headers[] = 'WTE tested up to';
		// WTE.
		$headers[] = 'WTE';

		return $headers;
	}

	public function admin_enqueue_scripts() {
		// Admin Scripts.
		$screens        = array( 'trip', 'enquiry', 'booking', 'customer', 'wte-coupon', 'downloadfile' );
		$current_screen = get_current_screen();

		if ( ! $current_screen ) {
			return;
		}

		$screen_ids = array( 'trip_page_class-wp-travel-engine-admin' );

		$development = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;

		$suffix = $development ? '' : '';

		wp_enqueue_style( 'wte-plugins-php', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . "dist/admin/plugins-php{$suffix}.css", array(), $this->version );

		$dependencies = array(
			'jquery',
			'jquery-ui-core',
			'jquery-ui-datepicker',
			'jquery-ui-tabs',
			'jquery-ui-sortable',
			'toastr',
			'parsley',
			'wte-select2',
			'magnific-popup',
			'wte-fontawesome-all',
			'v4-shims',
			'wte-fpickr',
			'moment',
		);

		if ( $current_screen && WP_TRAVEL_ENGINE_POST_TYPE === $current_screen->id ) {
			$dependencies[] = 'wte-redux';
			$dependencies[] = 'wte-rxjs';
		}

		$to_be_localized = array(
			'WTE_UI' => array(
				'handle' => "{$this->plugin_name}",
				'l10n'   => array(
					'suretodel'        => __( 'Sure to delete? This action cannot be reverted.', 'wp-travel-engine' ),
					'validation_error' => __( 'Validation Error. Settings could not be saved.', 'wp-travel-engine' ),
					'copied'           => __( 'Text copied to clipboard.', 'wp-travel-engine' ),
					'novid'            => __( 'No video URL supplied.', 'wp-travel-engine' ),
					'invalid_url'      => __( 'Invalid URL supplied. Please make sure to add valid YouTube or Vimeo video URL', 'wp-travel-engine' ),
				),
			),
		);

		wp_register_script( $this->plugin_name, plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . "dist/admin/wte-admin{$suffix}.js", $dependencies, $this->version, true );

		foreach ( apply_filters( 'wte_admin_localize_data', $to_be_localized ) as $object_name => $load ) {
			wp_localize_script( $load['handle'], $object_name, $load['l10n'] );
		}
		wp_register_style( $this->plugin_name . '_core_ui', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . "dist/admin/wte-admin{$suffix}.css", array( 'wte-select2', 'wte-global', 'datepicker-style', 'animate', 'toastr' ), $this->version );

		if ( in_array( $current_screen->post_type, $screens, ! 0 ) || ( isset( $_GET['page'] ) && $_GET['page'] == 'class-wp-travel-engine-admin.php' ) || in_array( $current_screen->id, $screen_ids, ! 0 ) ) {
			wp_enqueue_editor();
			wp_enqueue_media();
			wp_enqueue_script( 'wte-global' );
			wp_enqueue_style( 'wte-fpickr' );
			wp_enqueue_script( $this->plugin_name );

			// Styles.
			wp_enqueue_style( $this->plugin_name . '_core_ui' );
		}

		wp_register_style( 'wte-plugins-php', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'dist/admin/plugins-php.css', array(), $this->version );
	}

	public function enqueue_scripts() {

		// Library Scripts Registration.
		$settings = get_option( 'wp_travel_engine_settings', array() );

		$development = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;

		$suffix = $development ? '' : '';

		// Owl Carousel.
		wp_register_script( 'owl-carousel', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/vendors/owl-carousel-2.3.4/owl.carousel.js', array( 'jquery' ), '2.3.4', true );
		wp_register_style( 'owl-carousel', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/vendors/owl-carousel-2.3.4/owl.carousel.css', array(), '2.3.4' );

		wp_register_script( 'slick', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/vendors/slick-min-js.js', array( 'jquery' ), '2.3.4', true );
		wp_register_script( 'parsley', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/vendors/parsley-min.js', array( 'jquery' ), '2.9.2', true );

		// Magnific popup.
		wp_register_script( 'magnific-popup', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/vendors/magnific-popup-1.1.0/magnific-popup.min.js', array( 'jquery' ), '2.9.2', true );
		wp_register_style( 'magnific-popup', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/vendors/magnific-popup-1.1.0/magnific-popup.min.css', array(), '2.9.2' );

		wp_register_script( 'jquery-steps', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/vendors/jquery-steps.min.js', array( 'jquery', 'jquery-ui-core' ), $this->version, true );
		wp_register_script( 'jquery-validate', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/vendors/jquery.validate.min.js', array( 'jquery' ), '1.19.1', true );
		wp_register_script( 'wte-fontawesome-all', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/vendors/fontawesome/all.min.js', array(), '5.6.3', true );
		wp_register_script( 'v4-shims', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/vendors/fontawesome/v4-shims.min.js', array(), '5.6.3', true );
		wp_register_script( 'jquery-sticky-kit', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/vendors/jquery.sticky-kit.js', array( 'jquery' ), null, true );

		// Toastr.
		wp_register_script( 'toastr', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/vendors/toastr/toastr.min.js', array( 'jquery' ), null, true );
		wp_register_style( 'toastr', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/vendors/toastr/toastr.min.css', array(), $this->version );

		// Select2.
		wp_register_script( 'wte-select2', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/vendors/select2-4.0.13/select2.js', array( 'jquery' ), '4.0.13', true );
		wp_register_style( 'wte-select2', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/vendors/select2-4.0.13/select2.css', array(), '4.0.13' );

		wp_register_script( 'wte-rrule', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/vendors/rrule.min.js', array( 'jquery' ), '3.3.2', true );

		// Flatpickr.
		$locale = explode( '_', get_locale() );
		if ( isset( $locale[0] ) && 'en' !== $locale[0] ) {
			wp_enqueue_script( 'wte-fpickr-l10n', 'https://npmcdn.com/flatpickr@4.6.9/dist/l10n/' . $locale[0] . '.js', array( 'wte-fpickr' ), '4.6.9' );
		}
		wp_register_script( 'wte-fpickr', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/vendors/flatpickr-4.6.9/fpickr.js', array(), '4.6.9', true );
		wp_register_style( 'wte-fpickr', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/vendors/flatpickr-4.6.9/fpickr.css', array(), '4.6.9' );

		// wp_register_script( 'wte-video-popup-trigger', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'js/video-popup-trigger.js', array( 'jquery', 'magnific-popup' ), $this->version, true );
		// wp_register_script( 'wte-video-slider-trigger', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'js/video-slider-trigger.js', array( 'jquery' ), $this->version, true );

		// Highlight JS.
		wp_register_script( 'wte-highlightjs', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/vendors/highlightjs-10.5.0/highlight.pack.js', array(), '10.5.0', true );
		wp_register_style( 'wte-highlightjs', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/vendors/highlightjs-10.5.0/highlight.pack.css', array(), '10.5.0' );

		// Redux.
		wp_register_script( 'wte-redux', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/vendors/redux.min.js', array( 'wp-redux-routine' ), '4.0.5', true );
		wp_register_script( 'wte-rxjs', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/vendors/rxjs.umd.js', array(), '6.6.6', ! 0 );

		// Moment.
		wp_register_script( 'wte-moment-tz', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/vendors/moment/moment-tz.js', array( 'moment' ), '0.5.33', true );

		// Regenerator Runtime
		wp_register_script( 'regenerator-runtime', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . "dist/global/regenerator-runtime{$suffix}.js", array(), '0.13.7', true );

		// Global Script Registration.
		wp_register_script( 'wte-global', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . "dist/global/wte-global{$suffix}.js", array( 'lodash', 'regenerator-runtime' ), $development ? filemtime( plugin_dir_path( WP_TRAVEL_ENGINE_FILE_PATH ) . "dist/global/wte-global{$suffix}.js" ) : $this->version, true );
		wp_register_style( 'wte-global', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . "dist/global/wte-global{$suffix}.css", array(), $development ? filemtime( plugin_dir_path( WP_TRAVEL_ENGINE_FILE_PATH ) . "dist/global/wte-global{$suffix}.css" ) : $this->version );

		// DatePicker.
		wp_register_style( 'datepicker-style', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/vendors/datepicker/datepicker-style.css', array(), '1.11.4' );

		// Animate CSS.
		wp_register_style( 'animate', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/vendors/animate.css', array(), '3.5.2' );

		// jQuery UI CSS.
		wp_register_style( 'jquery-ui', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/vendors/jquery-ui.css', array(), '1.12.1', 'all' );

		// Nice Select.
		wp_register_script( 'wte-custom-niceselect', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/vendors/nice-select/jquery.nice-select.min.js', array( 'jquery' ), '1.0', true );
		wp_register_style( 'wte-custom-niceselect', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/vendors/nice-select/nice-select.css', array(), '1.0' );

		// Custom Scrollbar
		wp_register_style( 'wte-custom-scrollbar', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/vendors/custom-scrollbar/jquery.mCustomScrollbar.min.css', array(), '3.1.13' );
		wp_register_script( 'wte-custom-scrollbar', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/vendors/custom-scrollbar/jquery.mCustomScrollbar.concat.min.js', array( 'jquery' ), '3.1.13', true );

		// WTE Fonts
		wp_register_style( 'wte-icons', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'includes/vendors/wte-icons/style.css', array(), '1.0.0' );

		$base_currency = wte_array_get( $settings, 'currency_code', 'USD' );
		$currency      = apply_filters( 'wp_travel_engine_currency_code', $base_currency, ! 1 );

		$extensions = array();
		foreach ( array(
			'wte-extra-services'            => 'WTE_EXTRA_SERVICES_VERSION',
			'wte-trip-reviews'              => 'WTE_TRIP_REVIEW_VERSION',
			'wte-trip-fixed-starting-dates' => 'WTE_FIXED_DEPARTURE_VERSION',
			'wte-currency-converter'        => 'WTE_CURRENCY_CONVERTER_VERSION',
		) as $slug => $constant_name ) {
			if ( defined( $constant_name ) ) {
				$extensions[ $slug ] = \constant( $constant_name );
			}
		}
		$l10n = array(
			'version'            => $this->version,
			'baseCurrency'       => $base_currency,
			'baseCurrencySymbol' => \Wp_Travel_Engine_Functions::currency_symbol_by_code( $base_currency ),
			'currency'           => $currency,
			'currencySymbol'     => \Wp_Travel_Engine_Functions::currency_symbol_by_code( $currency ),
			'_nonces'            => array(
				'addtocart' => wp_create_nonce( 'wte_add_to_cart' ),
			),
			'wpapi'              => array(
				'root'          => esc_url_raw( rest_url() ),
				'nonce'         => wp_create_nonce( 'wp_rest' ),
				'versionString' => 'wp/v2/',
			),
			'wpxhr'              => array(
				'root'  => esc_url_raw( admin_url( 'admin-ajax.php' ) ),
				'nonce' => wp_create_nonce( 'wp_xhr' ),
			),
			'format'             => array(
				'number'   => array(
					'decimal'           => wte_array_get( $settings, 'decimal_digits', 2 ),
					'decimalSeparator'  => wte_array_get( $settings, 'decimal_separator', '.' ),
					'thousandSeparator' => wte_array_get( $settings, 'thousands_separator', ',' ),
				),
				'price'    => wte_array_get( $settings, 'amount_display_format', '%CURRENCY_SYMBOL%%FORMATED_AMOUNT%' ),
				'date'     => get_option( 'date_format', 'Y-m-d' ),
				'time'     => get_option( 'time_format', 'g:i a' ),
				'datetime' => array(
					'date'      => get_option( 'date_format', 'Y-m-d' ),
					'time'      => get_option( 'time_format', 'g:i a' ),
					'GMTOffset' => wte_get_timezone_info(),
					'timezone'  => get_option( 'timezone_string', '' ),
				),
			),
			'extensions'         => apply_filters( 'wte_active_extensions', $extensions ),
			'locale'             => get_locale(),
			'l10n'               => array(
				'invalidCartTraveler'  => __( 'No. of Travellers\' should be at least %s', 'wp-travel-engine' ),
				'availableSeatsExceed' => __( 'The number of pax can not exceed more than %s', 'wp-travel-engine' ),
			),
		);

		global $post;
		global $wtetrip;
		if ( $post instanceof \WP_Post && ( \WP_TRAVEL_ENGINE_POST_TYPE === $post->post_type ) && $wtetrip ) {
			$trip_version = get_post_meta( $post->ID, 'trip_version', true );
			if ( empty( $trip_version ) ) {
				$trip_version = '0.0.0';
			}
			$l10n['tripID']      = (int) $post->ID;
			$l10n['tripVersion'] = $trip_version;
			$l10n['legacy']      = $wtetrip->use_legacy_trip;
		}
		wp_localize_script(
			'wte-global',
			'wteL10n',
			apply_filters( 'wtel10n', $l10n )
		);

		// Admin Scripts.
		if ( is_admin() ) {
			$this->admin_enqueue_scripts();
			return;
		}

		global $post;

		$post_id = 0;
		if ( isset( $_GET['action'] ) && $_GET['action'] == 'partial-payment' && isset( $_GET['booking_id'] ) && ! empty( $_GET['booking_id'] ) ) :
			$post_id = sanitize_text_field( $_GET['booking_id'] );
		elseif ( is_object( $post ) && ! is_404() ) :
			$post_id = $post->ID;
		endif;

		// Public Scripts.
		wp_register_script(
			$this->plugin_name,
			plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . "dist/public/wte-public{$suffix}.js",
			array( 'wte-global', 'wte-fpickr', 'jquery', 'wte-custom-niceselect', 'jquery-ui-core', 'jquery-ui-datepicker', 'jquery-steps', 'jquery-validate', 'wte-fontawesome-all', 'v4-shims', 'toastr', 'jquery-sticky-kit', 'parsley', 'magnific-popup', 'owl-carousel', 'wte-rrule', 'moment', 'wte-moment-tz' ),
			$this->version,
			! 0
		);

		global $wte_cart;

		$currency_code_JS = apply_filters( 'wpte_cc_allow_payment_with_switcher', ! 0 ) ? wp_travel_engine_get_currency_code() : wte_currency_code_in_db();

		$totals = $wte_cart->get_total();

		$is_sale_price_enabled = wp_travel_engine_is_trip_on_sale( $post_id );
		$sale_price            = wp_travel_engine_get_sale_price( $post_id );
		$regular_price         = wp_travel_engine_get_prev_price( $post_id );
		$price                 = wp_travel_engine_get_actual_trip_price( $post_id );
		$labels                = wte_multi_pricing_labels( $post_id );

		$to_be_localized = array(
			'wte_currency_vars' => array(
				'handle' => $this->plugin_name,
				'l10n'   => array(
					'code_or_symbol' => wte_array_get( $settings, 'currency_option', 'symbol' ),
				),
			),
			'WTEAjaxData'       => array(
				'handle' => $this->plugin_name,
				'l10n'   => array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( 'wp_rest' ),
				),
			),
			'wte_strings'       => array(
				'handle' => $this->plugin_name,
				'l10n'   => array(
					'bookNow'         => wte_get_book_now_text(),
					'pax_validation'  => __( 'Number of pax (%1$s) is not eligible for booking. Please choose travellers number between %2$s and %3$s for this trip.', 'wp-travel-engine' ),
					'bookingContinue' => _x( 'Continue', 'Booking continue button Label', 'wp-travel-engine' ),
				),
			),
			'wte'               => array(
				'handle' => $this->plugin_name,
				'l10n'   => array(
					'personFormat'    => wte_get_person_format(),
					'bookNow'         => wte_get_book_now_text(),
					'totaltxt'        => wte_get_total_text(),
					'currency'        => array(
						'code'   => apply_filters( 'wpte_cc_allow_payment_with_switcher', true ) ? wp_travel_engine_get_currency_code() : wte_currency_code_in_db(),
						'symbol' => wp_travel_engine_get_currency_symbol( $currency_code_JS ),
					),
					'trip'            => array(
						'id'                 => $post_id,
						'salePrice'          => wp_travel_engine_get_sale_price( $post_id ),
						'regularPrice'       => wp_travel_engine_get_prev_price( $post_id ),
						'isSalePriceEnabled' => wp_travel_engine_is_trip_on_sale( $post_id ),
						'price'              => wp_travel_engine_get_actual_trip_price( $post_id ),
						'travellersCost'     => wp_travel_engine_get_actual_trip_price( $post_id ),
						'extraServicesCost'  => 0.0,
					),
					'payments'        => array(
						'locale'        => get_locale(),
						'total'         => $totals['total'],
						'total_partial' => $totals['total_partial'],
					),
					'single_showtabs' => apply_filters( 'wte_single_trip_show_all_tabs', ! 1 ),
					'pax_labels'      => wte_multi_pricing_labels( $post_id ),
					'booking_cutoff'  => wpte_get_booking_cutoff( $post_id ),
				),
			),
		);

		$to_be_localized['wte_cart'] = array(
			'handle' => $this->plugin_name,
			'l10n'   => $wte_cart->getItems(),
		);

		$to_be_localized['rtl'] = array(
			'handle' => $this->plugin_name,
			'l10n'   => array( 'enable' => is_rtl() ? '1' : '0' ),
		);

		$to_be_localized['Url'] = array(
			'handle' => $this->plugin_name,
			'l10n'   => array(
				'paypalurl' => defined( 'WP_TRAVEL_ENGINE_PAYMENT_DEBUG' ) && WP_TRAVEL_ENGINE_PAYMENT_DEBUG ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr',
				'normalurl' => esc_url( wte_array_get( $settings, 'pages.wp_travel_engine_confirmation_page', '' ) ),
			),
		);

		$to_be_localized['wp_travel_engine'] = array(
			'handle' => $this->plugin_name,
			'l10n'   => array(
				'ajaxurl'     => admin_url( 'admin-ajax.php' ),
				'cartURL'     => '',
				'CheckoutURL' => add_query_arg( 'wte_id', time(), wp_travel_engine_get_checkout_url() ),
			),
		);

		// Localization.
		foreach (
			$to_be_localized as $object_name => $load
			) {
				wp_localize_script(
					$load['handle'],
					$object_name,
					$load['l10n']
				);
		}

		wp_register_style( $this->plugin_name, plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . "dist/public/wte-public{$suffix}.css", array( 'animate', 'jquery-ui', 'owl-carousel' ), $this->version );

		global $wte_cart;
		$wptravelengine_cart = '';
		if ( wp_travel_engine_is_checkout_page() && isset( $wte_cart ) ) {
			$cart_data           = array(
				'cart_items'  => $wte_cart->getItems(),
				'cart_totals' => $wte_cart->get_total(),
			);
			$wptravelengine_cart = "window['wptravelengineCart'] = %s;";
			wp_add_inline_script( $this->plugin_name, sprintf( $wptravelengine_cart, wp_json_encode( $cart_data ) ) );
		}
		wp_enqueue_script( $this->plugin_name );
		wp_add_inline_script( $this->plugin_name, 'var WPTE_Price_Separator = "' . wte_array_get( $settings, 'thousands_separator', '' ) . '"; // Backward compatibility.' );
		wp_enqueue_style( $this->plugin_name );
	}

	/**
	 * Freemus Setup.
	 *
	 * @since 5.0.0
	 *
	 * @return void
	 */
	public static function init_freemus() {
		global $wte_fs;

		if ( ! $wte_fs ) {
			// Include Freemius SDK.
			require_once dirname( WP_TRAVEL_ENGINE_FILE_PATH ) . '/includes/lib/freemius/start.php';

			$wp_travel_engine_first_time_activation_flag = get_option( 'wp_travel_engine_first_time_activation_flag', false );

			if ( $wp_travel_engine_first_time_activation_flag == false ) {
				$slug = 'wp-travel-engine-onboard';
			} else {
				$slug = 'class-wp-travel-engine-admin.php';
			}
			$arg_array = array(
				'id'             => '5392',
				'slug'           => 'wp-travel-engine',
				'type'           => 'plugin',
				'public_key'     => 'pk_d9913f744dc4867caeec5b60fc76d',
				'is_premium'     => false,
				'has_addons'     => false,
				'has_paid_plans' => false,
				'menu'           => array(
					'slug'    => $slug, // Default: class-wp-travel-engine-admin.php.
					'account' => false,
					'contact' => false,
					'support' => false,
					'parent'  => array(
						'slug' => 'edit.php?post_type=booking',
					),
				),
			);
			$wte_fs    = fs_dynamic_init( $arg_array );
		}

		$wte_fs->add_action( 'after_uninstall', function() {} );
		do_action( 'wte_fs_loaded' );
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wp_Travel_Engine_Loader. Orchestrates the hooks of the plugin.
	 * - Wp_Travel_Engine_i18n. Defines internationalization functionality.
	 * - Wp_Travel_Engine_Admin. Defines all hooks for the admin area.
	 * - Wp_Travel_Engine_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * WTE Helper and utility functions .
		 *
		 * @since 4.3.0
		 */
		include WP_TRAVEL_ENGINE_BASE_PATH . '/includes/lib/jwt/loader.php';

		include WP_TRAVEL_ENGINE_BASE_PATH . '/includes/helpers/functions.php';

		include WP_TRAVEL_ENGINE_BASE_PATH . '/includes/class-wte.php';

		include WP_TRAVEL_ENGINE_BASE_PATH . '/includes/class-wte-trip.php';

		// Plugin Updater.
		include WP_TRAVEL_ENGINE_BASE_PATH . '/admin/plugin-updates/plugin-updater.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-travel-engine-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-travel-engine-i18n.php';

		/**
		 * Helpers
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/wp-travel-engine-helpers.php';

		/**
		 * Default form fields
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wte-default-form-fields.php';

		/**
		 *
		 * @since
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'neo/class-wte-field-builder.php';

		/**
		 * Form Fields
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/wp-travel-engine-form-fields.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-travel-engine-admin.php';

		/**
		 * The class responsible for the admin settings.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-travel-engine-permalinks.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wp-travel-engine-public.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-travel-engine-messages-list.php';

		/**
		 * The class responsible for building tabs in post type.
		 * side of the site.
		 */
		require WP_TRAVEL_ENGINE_BASE_PATH . '/includes/class-wp-travel-engine-meta-tabs.php';

		require WP_TRAVEL_ENGINE_BASE_PATH . '/includes/class-wp-travel-engine-onboard.php';

		/**
		 * The class responsible for activation setup page.
		 */
		// require WP_TRAVEL_ENGINE_BASE_PATH . '/includes/class-wp-travel-engine-activate-setup.php';

		/**
		 * The class responsible for defining tabs in custom post type.
		 */
		require WP_TRAVEL_ENGINE_BASE_PATH . '/admin/class-wp-travel-engine-tabs.php';

		/**
		 * The class responsible for defining functions for backend.
		 */
		require WP_TRAVEL_ENGINE_BASE_PATH . '/includes/class-wp-travel-engine-functions.php';

		/**
		 * The class responsible for defining templates.
		 */
		require WP_TRAVEL_ENGINE_BASE_PATH . '/includes/frontend/class-wp-travel-engine-templates.php';

		/**
		 * The class responsible for placing order.
		 */
		require WP_TRAVEL_ENGINE_BASE_PATH . '/includes/class-wp-travel-engine-place-order.php';

		/**
		 * The class responsible for thank you.
		 */
		require WP_TRAVEL_ENGINE_BASE_PATH . '/includes/class-wp-travel-engine-thank-you.php';
		/**
		 * The class responsible for final confirmation.
		 */
		require WP_TRAVEL_ENGINE_BASE_PATH . '/includes/class-wp-travel-engine-confirmation.php';

		/**
		 * The class responsible for creating metas for order form.
		 */
		require WP_TRAVEL_ENGINE_BASE_PATH . '/includes/class-wp-travel-engine-order-meta.php';

		/**
		 * The class responsible for creating meta tags for single trip.
		 */
		require WP_TRAVEL_ENGINE_BASE_PATH . '/includes/frontend/trip-meta/class-wp-travel-engine-meta-tags.php';

		/**
		 * The class responsible for creating hoks for archive.
		 */
		require WP_TRAVEL_ENGINE_BASE_PATH . '/includes/class-wp-travel-engine-archive-hooks.php';

		/**
		 * The class responsible for creating widget area.
		 */
		require WP_TRAVEL_ENGINE_BASE_PATH . '/includes/class-wte-widget-area-admin.php';

		/**
		 * The class responsible for showing widgets from widget area.
		 */
		require WP_TRAVEL_ENGINE_BASE_PATH . '/includes/class-wte-widget-area-main.php';

		/**
		 * The class responsible for showing image field in taxonomies.
		 */
		require WP_TRAVEL_ENGINE_BASE_PATH . '/includes/class-wp-travel-engine-taxonomy-thumb.php';

		/**
		 * Including the mail class.
		 */
		include WP_TRAVEL_ENGINE_BASE_PATH . '/includes/class-wp-travel-engine-mail.php';

		/**
		 * Including the trip facts shortcode.
		 */
		include WP_TRAVEL_ENGINE_BASE_PATH . '/includes/frontend/trip-meta/trip-meta-parts/trip-facts-shortcode.php';

		/**
		 * Including the trip facts shortcode.
		 */
		include WP_TRAVEL_ENGINE_BASE_PATH . '/includes/class-wp-travel-engine-enquiry-form-shortcodes.php';

		/**
		 * The class responsible for compatibility check.
		 */
		require WP_TRAVEL_ENGINE_BASE_PATH . '/includes/class-wp-travel-engine-compatibility-check.php';

		/**
		 * Including the trip facts shortcode.
		 */
		include WP_TRAVEL_ENGINE_BASE_PATH . '/includes/privacy-functions.php';

		include WP_TRAVEL_ENGINE_BASE_PATH . '/includes/class-wp-travel-engine-reorder-trips.php';
		include WP_TRAVEL_ENGINE_BASE_PATH . '/includes/class-wp-travel-engine-custom-shortcodes.php';

		include WP_TRAVEL_ENGINE_BASE_PATH . '/includes/class-wp-travel-engine-seo.php';

		include WP_TRAVEL_ENGINE_BASE_PATH . '/includes/cart/class-wte-cart.php';

		include WP_TRAVEL_ENGINE_BASE_PATH . '/includes/class-wte-ajax.php';

		// include WP_TRAVEL_ENGINE_BASE_PATH . '/includes/class-wte-process-booking-core.php';
		include WP_TRAVEL_ENGINE_BASE_PATH . '/includes/class-wp-travel-engine-remaining-partial-payment.php';

		include_once WP_TRAVEL_ENGINE_BASE_PATH . '/includes/payment-gateways/standard-paypal/paypal-functions.php';

		include_once WP_TRAVEL_ENGINE_BASE_PATH . '/includes/payment-gateways/standard-paypal/class-wp-travel-engine-paypal-request.php';

		include_once WP_TRAVEL_ENGINE_BASE_PATH . '/public/class-wp-travel-engine-template-hooks.php';

		/** Admin Ui New Changes indicator Pointer */
		include_once WP_TRAVEL_ENGINE_BASE_PATH . '/includes/class-wp-travel-engine-ui-pointers.php';
		include_once WP_TRAVEL_ENGINE_BASE_PATH . '/includes/class-wte-getting-started.php';

		/**
		 * Featured Trips widget
		 */
		require_once WP_TRAVEL_ENGINE_BASE_PATH . '/includes/widgets/widget-featured-trip.php';

		// load user modules.
		/**
		 * Include Query Classes.
		 *
		 * @since 1.2.6
		 */
		include sprintf( '%s/includes/dashboard/class-wp-travel-engine-query.php', WP_TRAVEL_ENGINE_ABSPATH );

		// User Modules.
		include sprintf( '%s/includes/dashboard/wp-travel-engine-user-functions.php', WP_TRAVEL_ENGINE_ABSPATH );
		include sprintf( '%s/includes/dashboard/class-wp-travel-engine-user-account.php', WP_TRAVEL_ENGINE_ABSPATH );
		include sprintf( '%s/includes/dashboard/class-wp-travel-engine-form-handler.php', WP_TRAVEL_ENGINE_ABSPATH );

		// WP Travel Engine Neo.
		if ( ! defined( 'USE_WTE_LEGACY_VERSION' ) || ! USE_WTE_LEGACY_VERSION ) {
			require_once sprintf( '%s/neo/wte-neo.php', WP_TRAVEL_ENGINE_ABSPATH );
			require_once sprintf( '%s/includes/helpers/helpers-packages.php', WP_TRAVEL_ENGINE_ABSPATH );
		}

		/**
		 * New Booking Process.
		 *
		 * @since 4.3.0
		 */
		require_once sprintf( '%s/includes/bookings/class-wte-payment.php', WP_TRAVEL_ENGINE_ABSPATH );
		require_once sprintf( '%s/includes/class-wp-travel-engine-emails.php', WP_TRAVEL_ENGINE_ABSPATH );
		require_once sprintf( '%s/includes/bookings/class-wte-process-booking-core.php', WP_TRAVEL_ENGINE_ABSPATH );

		/**
		 * Modules integrated on later version.
		 */
		include_once sprintf( '%s/includes/modules/class-trip-code.php', WP_TRAVEL_ENGINE_ABSPATH );
		include_once sprintf( '%s/includes/modules/coupon-code/class-coupon-code.php', WP_TRAVEL_ENGINE_ABSPATH );
		include_once sprintf( '%s/includes/modules/trip-search/class-trip-search.php', WP_TRAVEL_ENGINE_ABSPATH );
		include_once sprintf( '%s/includes/modules/custom-filters/class-custom-filters.php', WP_TRAVEL_ENGINE_ABSPATH );
		include_once sprintf( '%s/includes/blocks/class-blocks.php', WP_TRAVEL_ENGINE_ABSPATH );

		/**
		 * Rest API.
		 */
		include_once sprintf( '%s/includes/rest-api/index.php', WP_TRAVEL_ENGINE_ABSPATH );

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wp_Travel_Engine_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new \Wp_Travel_Engine_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new \Wp_Travel_Engine_Admin( $this->get_plugin_name(), $this->get_version() );

		// $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		// $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_admin, 'wp_travel_engine_register_trip' );
		$this->loader->add_action( 'init', $plugin_admin, 'wp_travel_engine_register_booking' );
		$this->loader->add_action( 'init', $plugin_admin, 'wp_travel_engine_register_customer' );
		$this->loader->add_action( 'init', $plugin_admin, 'wp_travel_engine_register_enquiry' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'wp_travel_engine_register_settings' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'wte_update_actual_prices_for_filter' );
		$this->loader->add_action( 'admin_head', $plugin_admin, 'wp_travel_engine_tabs_template', 0 );
		$this->loader->add_filter( 'manage_enquiry_posts_columns', $plugin_admin, 'wp_travel_engine_enquiry_cpt_columns' );
		$this->loader->add_filter( 'post_row_actions', $plugin_admin, 'enquiry_remove_row_actions', 10, 1 );
		$this->loader->add_action( 'wp_ajax_wte_get_enquiry_preview', $plugin_admin, 'wte_get_enquiry_preview_action' );
		$this->loader->add_action( 'manage_posts_custom_column', $plugin_admin, 'wp_travel_engine_enquiry_custom_columns', 10, 2 );
		$this->loader->add_filter( 'manage_booking_posts_columns', $plugin_admin, 'wp_travel_engine_booking_cpt_columns' );
		$this->loader->add_action( 'manage_posts_custom_column', $plugin_admin, 'wp_travel_engine_booking_custom_columns', 10, 2 );
		$this->loader->add_filter( 'manage_customer_posts_columns', $plugin_admin, 'wp_travel_engine_customer_cpt_columns' );
		$this->loader->add_action( 'manage_posts_custom_column', $plugin_admin, 'wp_travel_engine_customer_custom_columns', 10, 2 );
		$this->loader->add_filter( 'manage_edit-trip_types_columns', $plugin_admin, 'wp_travel_engine_trip_types_columns', 10, 2 );
		$this->loader->add_action( 'manage_trip_types_custom_column', $plugin_admin, 'wp_travel_engine_trip_types_custom_columns', 10, 3 );
		$this->loader->add_filter( 'manage_edit-destination_columns', $plugin_admin, 'wp_travel_engine_trip_types_columns', 10, 2 );
		$this->loader->add_action( 'manage_destination_custom_column', $plugin_admin, 'wp_travel_engine_trip_types_custom_columns', 10, 3 );
		$this->loader->add_filter( 'manage_edit-activities_columns', $plugin_admin, 'wp_travel_engine_trip_types_columns', 10, 2 );
		/*
		* ADMIN COLUMN - HEADERS
		*/
		$this->loader->add_filter( 'manage_edit-trip_columns', $plugin_admin, 'wp_travel_engine_trips_columns' );
		$this->loader->add_action( 'wp_ajax_wp_travel_engine_featured_trip', $plugin_admin, 'wp_travel_engine_featured_trip_admin_ajax' );
		$this->loader->add_action( 'wp_ajax_wp_travel_engine_featured_term', $plugin_admin, 'wp_travel_engine_featured_term_admin_ajax' );
		$this->loader->add_action( 'manage_activities_custom_column', $plugin_admin, 'wp_travel_engine_trip_types_custom_columns', 10, 3 );
		$this->loader->add_action( 'admin_head-post.php', $plugin_admin, 'hide_publishing_actions', 10, 2 );
		$this->loader->add_action( 'init', $plugin_admin, 'wp_travel_engine_create_destination_taxonomies' );
		$this->loader->add_action( 'init', $plugin_admin, 'wp_travel_engine_create_activities_taxonomies' );
		$this->loader->add_action( 'init', $plugin_admin, 'wp_travel_engine_create_trip_types_taxonomies' );

		$this->loader->add_action( 'admin_footer', $plugin_admin, 'wp_travel_engine_get_icon_list', 20 );

		if ( isset( $_GET['page'] ) && $_GET['page'] == 'class-wp-travel-engine-admin.php' ) {
			$this->loader->add_action( 'admin_footer', $plugin_admin, 'trip_facts_template', 20 );
		}

		$this->loader->add_action( 'admin_footer', $plugin_admin, 'wpte_add_itinerary_template', 20 );
		$this->loader->add_action( 'admin_footer', $plugin_admin, 'wpte_add_faq_template', 20 );
		$this->loader->add_action( 'wp_ajax_wp_add_trip_info', $plugin_admin, 'wp_add_trip_info' );
		$this->loader->add_action( 'wp_ajax_nopriv_wp_add_trip_info', $plugin_admin, 'wp_add_trip_info' );
		$this->loader->add_action( 'wp_loaded', $plugin_admin, 'wpte_add_destination_templates' );
		$this->loader->add_action( 'rest_api_init', $plugin_admin, 'wpte_add_destination_templates' );
		$this->loader->add_action( 'wte_paypal_form', $plugin_admin, 'wte_paypal_form' );
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'wpte_trip_pay_add_meta_boxes' );
		// $this->loader->add_action( 'save_post', $plugin_admin, 'wp_travel_engine_trip_pay_meta_box_data' );
		$this->loader->add_filter( 'tiny_mce_before_init', $plugin_admin, 'wte_tinymce_config' );
		$this->loader->add_filter( 'manage_trip_posts_columns', $plugin_admin, 'wp_travel_engine_trip_cpt_columns' );
		$this->loader->add_action( 'manage_posts_custom_column', $plugin_admin, 'wp_travel_engine_trip_custom_columns', 10, 2 );

		$this->loader->add_action( 'admin_notices', $plugin_admin, 'admin_notices' );
		$this->loader->add_action( 'in_plugin_update_message-wp-travel-engine/wp-travel-engine.php', $plugin_admin, 'in_plugin_update_message', 10, 2 );
		$this->loader->add_action( 'wp_travel_engine_trip_itinerary_setting', $plugin_admin, 'wte_itinerary_setting' );

		// Add bulk actions to migrate customers.
		$this->loader->add_filter( 'bulk_actions-edit-customer', $plugin_admin, 'wte_add_customer_bulk_actions' );
		// Handle bulk action migrate users to customer.
		$this->loader->add_filter( 'handle_bulk_actions-edit-customer', $plugin_admin, 'wte_add_customer_bulk_action_handler', 10, 3 );

		$this->loader->add_action( 'admin_notices', $plugin_admin, 'customer_bulk_action_notices' );
		/*
		* ADMIN COLUMN - Featured CONTENT
		*/
		$this->loader->add_action( 'manage_trip_posts_custom_column', $plugin_admin, 'wte_itineraries_manage_columns', 10, 2 );

		/**
		 * Admin menu
		 */
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'wp_travel_engine_dashboard_menu' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'wp_travel_engine_extensions_page' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'wp_travel_engine_themes_page' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'wp_travel_engine_settings_page' );

		// Display message feature only if the user has enabled it.
		if ( '1' === get_option( 'wte_messages_enabled' ) || ( isset( $_GET['wte-message-enabled'] ) && '1' === $_GET['wte-message-enabled'] ) ) {
			$this->loader->add_action( 'admin_menu', $plugin_admin, 'messages_page' );
		}

		// lOAD TAB CONTENT AJAX
		$this->loader->add_action( 'wp_ajax_wpte_admin_load_tab_content', $plugin_admin, 'wpte_admin_load_tab_content_callback' );

		// Save tab and continue button ajax.
		$this->loader->add_action( 'wp_ajax_wpte_tab_trip_save_and_continue', $plugin_admin, 'wpte_tab_trip_save_and_continue_callback' );

		// Trip Code section.
		// $this->loader->add_action( 'wp_travel_engine_trip_code_display', $plugin_admin, 'wpte_display_trip_code_section' );

		// Pricing Tab upsell notes section.
		$this->loader->add_action( 'wte_after_pricing_upsell_notes', $plugin_admin, 'wpte_display_extension_upsell_notes' );

		// Load Global Tabs AJAX
		// lOAD TAB CONTENT AJAX
		$this->loader->add_action( 'wp_ajax_wpte_global_settings_load_tab_content', $plugin_admin, 'wpte_global_settings_load_tab_content_callback' );

		// Save global tabs data.
		$this->loader->add_action( 'wp_ajax_wpte_global_tabs_save_data', $plugin_admin, 'wpte_global_tabs_save_data_callback' );
		$this->loader->add_filter( 'admin_body_class', $plugin_admin, 'wpte_body_class_before_header_callback' );
		$this->loader->add_action( 'wp_travel_engine_trip_custom_info', $plugin_admin, 'wp_travel_engine_trip_custom_info' );

		$this->loader->add_action( 'post_submitbox_misc_actions', $plugin_admin, 'wte_publish_metabox' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new \Wp_Travel_Engine_Public( $this->get_plugin_name(), $this->get_version() );

		// $process_booking_core      = new WTE_Process_Booking_Core();
		// $process_remaining_payment = new \WTE_Process_Remaing_Payment();

		// Add new booking process handler to public init hook.
		// Since - WP Travel Engine - V.2.2.9
		// $this->loader->add_action( 'init', $process_booking_core, 'process_booking', 99 );

		// $this->loader->add_action( 'init', $process_remaining_payment, 'process_remaining_payment', 99 );

		// $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		// $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_public, 'wpte_start_session', 1 );
		$this->loader->add_action( 'wte_cart_trips', $plugin_public, 'wte_cart_trips' );
		$this->loader->add_action( 'wp_ajax_wp_add_trip_cart', $plugin_public, 'wp_add_trip_cart' );
		$this->loader->add_action( 'wp_ajax_nopriv_wp_add_trip_cart', $plugin_public, 'wp_add_trip_cart' );
		$this->loader->add_action( 'wte_update_cart', $plugin_public, 'wte_update_cart' );
		$this->loader->add_action( 'wte_cart_form_wrapper', $plugin_public, 'wte_cart_form_wrapper' );
		$this->loader->add_action( 'wte_cart_form_close', $plugin_public, 'wte_cart_form_close' );
		$this->loader->add_action( 'wp_ajax_wte_remove_order', $plugin_public, 'wte_remove_from_cart' );
		$this->loader->add_action( 'wp_ajax_nopriv_wte_remove_order', $plugin_public, 'wte_remove_from_cart' );
		$this->loader->add_action( 'wp_ajax_wte_update_cart', $plugin_public, 'wte_ajax_update_cart' );
		$this->loader->add_action( 'wp_ajax_nopriv_wte_update_cart', $plugin_public, 'wte_ajax_update_cart' );
		$this->loader->add_action( 'wte_payment_gateways_dropdown', $plugin_public, 'wte_payment_gateways_dropdown' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'wpte_be_load_more_js' );
		$this->loader->add_action( 'wp_ajax_wpte_ajax_load_more', $plugin_public, 'wpte_ajax_load_more' );
		$this->loader->add_action( 'wp_ajax_nopriv_wpte_ajax_load_more', $plugin_public, 'wpte_ajax_load_more' );
		$this->loader->add_action( 'wp_ajax_wpte_ajax_load_more_destination', $plugin_public, 'wpte_ajax_load_more_destination' );
		$this->loader->add_action( 'wp_ajax_nopriv_wpte_ajax_load_more_destination', $plugin_public, 'wpte_ajax_load_more_destination' );
		$this->loader->add_action( 'wp_ajax_wpte_ajax_load_more', $plugin_public, 'wpte_be_load_more_js' );
		$this->loader->add_action( 'wp_ajax_nopriv_wpte_ajax_load_more', $plugin_public, 'wpte_be_load_more_js' );
		$this->loader->add_action( 'init', $plugin_public, 'do_output_buffer' );
		$wp_travel_engine_settings = get_option( 'wp_travel_engine_settings', true );
		if ( isset( $wp_travel_engine_settings['paypal_payment'] ) ) {
			$this->loader->add_filter( 'wte_payment_gateways_dropdown_options', $plugin_public, 'wte_paypal_add_option' );
		}
		if ( isset( $wp_travel_engine_settings['test_payment'] ) ) {
			$this->loader->add_filter( 'wte_payment_gateways_dropdown_options', $plugin_public, 'wte_test_add_option' );
		}
		$this->loader->add_action( 'wp_ajax_wte_payment_gateway', $plugin_public, 'wte_payment_gateway' );
		$this->loader->add_action( 'wp_ajax_nopriv_wte_payment_gateway', $plugin_public, 'wte_payment_gateway' );
		$this->loader->add_action( 'wp_footer', $plugin_public, 'wpte_calendar_custom_code' );

		// Form dynamic hook - Booking form
		$this->loader->add_action( 'wp_travel_engine_order_form_before_form_field', $plugin_public, 'wpte_order_form_before_fields' );
		$this->loader->add_action( 'wp_travel_engine_order_form_after_form_field', $plugin_public, 'wpte_order_form_after_fields' );

		// Before submit button - Booking form
		$this->loader->add_action( 'wp_travel_engine_order_form_before_submit_button', $plugin_public, 'wpte_order_form_before_submit_button' );
		$this->loader->add_action( 'wp_travel_engine_order_form_after_submit_button', $plugin_public, 'wpte_order_form_after_submit_button' );

		$this->loader->add_action( 'wte_enquiry_contact_form_after_submit_button', $plugin_public, 'wte_enquiry_contact_form_after_submit_button' );

		// Tinymce Filters.
		$this->loader->add_filter( 'mce_buttons_2', $plugin_public, 'register_tinymce_buttons', 999, 2 );
		$this->loader->add_filter( 'mce_external_plugins', $plugin_public, 'register_tinymce_plugin', 999 );

		// $this->loader->add_action( 'wp_travel_engine_before_trip_add_to_cart', $plugin_public, 'check_min_max_pax', 9, 6 );
		$this->loader->add_action( 'wte_before_add_to_cart', $plugin_public, 'check_min_max_pax', 9, 2 );

		add_filter(
			'wp_travel_engine_available_payment_gateways',
			function( $gateways_list ) {
				if ( array_key_exists( 'direct_bank_transfer', $gateways_list ) ) {
					$settings = get_option( 'wp_travel_engine_settings', array() );
					$method   = isset( $settings['bank_transfer'] ) ? $settings['bank_transfer'] : array();
					if ( ! empty( $method['title'] ) ) {
						$gateways_list['direct_bank_transfer']['label'] = $method['title'];
					}
					if ( ! empty( $method['description'] ) ) {
						$gateways_list['direct_bank_transfer']['info_text'] = $method['description'];
					}
				}
				if ( array_key_exists( 'check_payments', $gateways_list ) ) {
					$settings = get_option( 'wp_travel_engine_settings', array() );
					$method   = isset( $settings['check_payment'] ) ? $settings['check_payment'] : array();
					if ( ! empty( $method['title'] ) ) {
						$gateways_list['check_payments']['label'] = $method['title'];
					}
					if ( ! empty( $method['description'] ) ) {
						$gateways_list['check_payments']['info_text'] = $method['description'];
					}
				}
				return $gateways_list;
			}
		);
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Wp_Travel_Engine_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Init shortcodes.
	 *
	 * @since    1.0.0
	 */
	public function init_shortcodes() {

		$plugin_shortcode = new \Wp_Travel_Engine_Place_Order();
		$plugin_shortcode->init();
		$plugin_shortcode = new \Wp_Travel_Engine_Thank_You();
		$plugin_shortcode->init();
		$plugin_shortcode = new \Wp_Travel_Engine_Order_Confirmation();
		$plugin_shortcode->init();
	}
}

/**
 * Backward Compatibility before namespacing - Wp_Travel_Engine Class may be used
 *
 * @since 5.0.0
 */
\class_alias( 'WPTravelEngine\Plugin', 'Wp_Travel_Engine' );
