<?php

/**
 * The Templateberg theme hooks callback functionality of the plugin.
 *
 * @link       https://www.templateberg.com/
 * @since      1.0.0
 *
 * @package    Templateberg
 */

/**
 * The Templateberg theme hooks callback functionality of the plugin.
 *
 * Since Templateberg theme is hooks base theme, this file is main callback to add/remove/edit the functionality of the Templateberg Plugin
 *
 * @package    Templateberg
 * @author     Templateberg <info@templateberg.com>
 */
class Templateberg_Hooks {


	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
	}

	/**
	 * Main Templateberg_Hooks Instance
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @return object $instance Templateberg_Hooks Instance
	 */
	public static function instance() {

		// Store the instance locally to avoid private static replication
		static $instance = null;

		// Only run these methods if they haven't been ran previously
		if ( null === $instance ) {
			$instance              = new Templateberg_Hooks();
			$instance->plugin_name = TEMPLATEBERG_PLUGIN_NAME;
			$instance->version     = TEMPLATEBERG_VERSION;
		}

		// Always return the instance
		return $instance;
	}

	/**
	 * Callback functions for enqueue_block_editor_assets,
	 * Enqueue Gutenberg block assets for backend only.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @param null
	 * @return void
	 */
	public function block_editor_assets() { // phpcs:ignore

		$dependencies = array( 'jquery', 'lodash', 'wp-api', 'wp-i18n', 'wp-blocks', 'wp-components', 'wp-compose', 'wp-data', 'wp-element', 'wp-keycodes', 'wp-plugins', 'wp-rich-text', 'wp-viewport' );
		if ( templateberg_is_edit_page() ) {
			array_push( $dependencies, 'wp-editor', 'wp-edit-post' );
		}
		// Scripts.
		wp_enqueue_script(
			'templateberg-editor', // Handle.
			TEMPLATEBERG_URL . 'dist/blocks.build.js', // Block.build.js: We register the block here. Built with Webpack.
			$dependencies, // Dependencies, defined above.
			TEMPLATEBERG_VERSION, // Version: File modification time.
			true // Enqueue the script in the footer.
		);

		wp_set_script_translations( 'templateberg-editor', 'templateberg' );

		wp_localize_script(
			'templateberg-editor',
			'templateberg',
			array(
				'nonce'                => wp_create_nonce( 'templateberg_nonce' ),
				'gutentor'             => array(
					'active' => templateburg_is_gutentor_active(),
				),
				'user'                 => array(
					'id' => get_current_user_id(),
				),
				'connectUrl'           => esc_url( templateberg_connect()->get_remote_connect_url() ),
				'templatebergWhiteSvg' => TEMPLATEBERG_URL . 'assets/svg/templateberg-white-svg.svg',
				'templateLibrarySvg'   => TEMPLATEBERG_URL . 'assets/svg/template-library.svg',
				'templatebergLogo'     => esc_url( TEMPLATEBERG_URL . 'assets/img/logo-48x48.png' ),
				'itemCurrent'          => array(),
				'plugin_url'           => admin_url( 'plugins.php' ),
			)
		);

		// Styles.
		wp_enqueue_style(
			'templateberg-editor', // Handle.
			TEMPLATEBERG_URL . 'dist/blocks.editor.build.css',
			array( 'wp-edit-blocks' ), // Dependency to include the CSS after it.
			TEMPLATEBERG_VERSION // Version: File modification time.
		);
		wp_style_add_data( 'templateberg-editor', 'rtl', 'replace' );
	}

	/**
	 * Callback functions for admin_enqueue_scripts,
	 * Enqueue Admin page assets for backend only.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @param null
	 * @return void
	 */
	public function admin_scripts() { // phpcs:ignore
		if ( ! templateberg_connect()->is_current_screen() ) {
			return;
		}

		// Scripts.
		wp_enqueue_script(
			'templateberg-dashboard', // Handle.
			TEMPLATEBERG_URL . 'dist/admin.min.js', // Block.build.js: We register the block here. Built with Webpack.
			array( 'jquery', 'jquery-ui-dialog' ), // Dependencies, defined above.
			TEMPLATEBERG_VERSION, // Version: File modification time.
			true // Enqueue the script in the footer.
		);
		wp_localize_script(
			'templateberg-dashboard',
			'templateberg',
			array(
				'restNonce'      => wp_create_nonce( 'wp_rest' ),
				'restUrl'        => esc_url_raw( rest_url() ),
				'account'        => templateberg_connect()->get_account(),
				'advancedImport' => array(
					'active' => templateburg_is_advanced_import_active(),
				),
				'purchase_url'   => templateberg_connect()->get_purchase_url(),
				'admin_url'      => templateberg_connect()->get_admin_url(),
				'nonce'          => wp_create_nonce( 'templateberg_nonce' ),
				'msg'            => array(
					'not_connected' => __( 'Error! Not Connected to templateberg.com', 'templateberg' ),
					'failed'        => __( 'Error! Fail to sync.', 'templateberg' ),
				),
				'has_templates'  => array(
					'current_theme'         => templateberg_has_templates( 'current-theme' ),
					'current_theme_nothing' => templateberg_current_theme_is_nothing(),
					'available_themes'      => templateberg_has_templates( 'available-themes' ),
					'gutenberg_templates'   => templateberg_has_templates( 'gutenberg-templates' ),
				),
			)
		);

		// Styles.
		wp_enqueue_style(
			'templateberg-dashboard', // Handle.
			TEMPLATEBERG_URL . 'dist/admin.css',
			array( 'wp-jquery-ui-dialog' ), // Dependencies, defined above.
			TEMPLATEBERG_VERSION // Version: File modification time.
		);

		if ( templateburg_is_advanced_import_active() ) {
			advanced_import_admin()->enqueue_scripts( 'appearance_page_advanced-import' );
		}
	}

	/**
	 * Callback functions for block_categories,
	 * Adding Block Categories
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @param array $categories
	 * @return array
	 */
	public function add_block_categories( $categories ) {

		return array_merge(
			array(
				array(
					'slug'  => 'templateberg-modules',
					'title' => __( 'Templateberg', 'templateberg' ),
				),
			),
			$categories
		);
	}

	/**
	 * Register Favourites Templates User Meta
	 * templateberg-fav-gt : templateberg-favourites-gutenberg-templates
	 *
	 * Common callback function of rest_api_init and admin_init
	 * Schema: http://json-schema.org/draft-04/schema#
	 *
	 * @since 1.1.5
	 *
	 * @param null.
	 * @return void
	 */
	public function register_user_meta_favourites() {
		register_meta(
			'user',
			'templateberg-fav-gt',
			array(
				'single'       => true,
				'type'         => 'array',
				'show_in_rest' => array(
					'schema' => array(
						'type'  => 'array',
						'items' => array(
							'type' => 'integer',
						),
					),
				),
			)
		);
	}

	/**
	 * Add plugin menu items.
	 *
	 * @access public
	 *
	 * @since 1.0.0
	 * @param string[] $actions     An array of plugin action links. By default this can include
	 *                              'activate', 'deactivate', and 'delete'. With Multisite active
	 *                              this can also include 'network_active' and 'network_only' items.
	 * @param string   $plugin_file Path to the plugin file relative to the plugins directory.
	 * @param array    $plugin_data An array of plugin data. See get_plugin_data()
	 *                              and the {@see 'plugin_row_meta'} filter for the list
	 *                              of possible values.
	 * @param string   $context     The plugin context. By default this can include 'all',
	 *                              'active', 'inactive', 'recently_activated', 'upgrade',
	 *                              'mustuse', 'dropins', and 'search'.
	 * @return array settings schema for this plugin.
	 */
	public function add_plugin_links( $actions, $plugin_file, $plugin_data, $context ) {
		$actions[] = '<a href="' . esc_url( menu_page_url( 'templateberg', false ) ) . '">' . esc_html__( 'Getting Started', 'templateberg' ) . '</a>';
		return $actions;
	}
}

/**
 * Begins execution of the hooks.
 *
 * @since    1.0.0
 */
function templateberg_hooks() {
	return Templateberg_Hooks::instance();
}
