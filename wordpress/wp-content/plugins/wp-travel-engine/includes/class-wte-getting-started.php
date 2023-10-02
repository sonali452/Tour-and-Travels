<?php
/**
 * Getting started handler class.
 *
 * @package WP_Travel_Engine
 */

class WTE_Getting_Started {

	/**
	 * Class constructor.
	 */
	public function __construct() {

		$this->version = WP_TRAVEL_ENGINE_VERSION;

		$this->init_hooks();

	}

	/**
	 * Load Admin Scripts.
	 *
	 * @return void
	 */
	public function load_scripts() {

		$current_screen = get_current_screen();

		// Admin Getting Started.
		if ( 'dashboard_page_class-wte-getting-started' === $current_screen->id ) {
			wp_enqueue_style( 'wte-getting-started', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'dist/admin/getting-started.css', array(), $this->version );
		}

	}

	/**
	 * Initilization hooks.
	 *
	 * @return void
	 */
	public function init_hooks() {

		add_action( 'admin_menu', array( $this, 'admin_menus' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ) );

		add_action( 'admin_init', array( $this, 'setup_redirection' ) );

	}

	/**
	 * Setup to getting started redirection.
	 *
	 * @return void
	 */
	public function setup_redirection() {
		$version       = str_replace( '.', '', WP_TRAVEL_ENGINE_VERSION );
		$has_changelog = apply_filters( "wte_show_changelog_for_{$version}", false );
		$shown         = get_transient( "wte_getting_started_page_shown_{$version}" );
		$redirect      = ! get_transient( "wte_redirected_{$version}" );
		if ( $has_changelog && $redirect ) {
			set_transient( "wte_redirected_{$version}", true );

			wp_safe_redirect( admin_url( 'index.php?page=class-wte-getting-started.php' ) );

			exit;
		}

	}

	/**
	 * Add admin menus/screens.
	 */
	public function admin_menus() {
		$version = str_replace( '.', '', WP_TRAVEL_ENGINE_VERSION );

		if ( ! get_transient( "wte_getting_started_page_shown_{$version}" ) || ( isset( $_GET['page'] ) && basename( __FILE__ ) === $_GET['page'] ) ) { // phpcs:ignore
			add_dashboard_page( __( 'WP Travel Engine - Welcome', 'wp-travel-engine' ), 'Getting Started', 'manage_options', basename( __FILE__ ), array( $this, 'getting_started_template' ) );
		}
	}

	/**
	 * Setup Wizard Header.
	 */
	public function setup_wizard_header() {

	}

	/**
	 * Wizard content.
	 *
	 * @return void
	 */
	public function setup_wizard_content() {
		$current_version_parts = explode( '.', WP_TRAVEL_ENGINE_VERSION );
		$major_version         = $current_version_parts[0];
		$major_version_int     = $major_version . '00';
		$assets_path_url       = plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . "admin/partials/plugin-updates/getting-started/{$major_version}00/assets/";
		ob_start();
		include_once plugin_dir_path( WP_TRAVEL_ENGINE_FILE_PATH ) . "admin/partials/plugin-updates/getting-started/{$major_version_int}/{$major_version_int}.php";
		return ob_get_clean();
	}

	/**
	 * Setup Wizard Footer.
	 */
	public function setup_wizard_footer() {

	}

	/**
	 * Template output.
	 *
	 * @return void
	 */
	public function getting_started_template() {

		if ( empty( $_GET['page'] ) || 'class-wte-getting-started.php' !== $_GET['page'] ) { // phpcs:ignore
			return;
		}
		$version = str_replace( '.', '', WP_TRAVEL_ENGINE_VERSION );

		set_transient( "wte_getting_started_page_shown_{$version}", true );

		$this->setup_wizard_header();

			echo $this->setup_wizard_content();

		$this->setup_wizard_footer();

	}

}
new WTE_Getting_Started();
