<?php
/**
 * Genesis Framework.
 *
 * WARNING: This file is part of the core Genesis Framework. DO NOT edit this file under any circumstances.
 * Please do all modifications in the form of a child theme.
 *
 * @package Genesis\Admin
 * @author  StudioPress
 * @license GPL-2.0-or-later
 * @link    https://my.studiopress.com/themes/genesis/
 */

/**
 * Abstract base class to create menus and settings pages (with or without sortable meta boxes).
 *
 * This class is extended by subclasses that define specific types of admin pages.
 *
 * @since 1.8.0
 *
 * @package Genesis\Admin
 */
abstract class Genesis_Admin {

	/**
	 * Name of the page hook when the menu is registered.
	 *
	 * @since 1.8.0
	 *
	 * @var string Page hook
	 */
	public $pagehook;

	/**
	 * ID of the admin menu and settings page.
	 *
	 * @since 1.8.0
	 *
	 * @var string
	 */
	public $page_id;

	/**
	 * The page to redirect to when menu page is accessed.
	 *
	 * @since 2.10.0
	 *
	 * @var string
	 */
	public $redirect_to;

	/**
	 * The query flag to check for to bypass the redirect setting.
	 *
	 * @since 2.10.0
	 *
	 * @var string
	 */
	public $redirect_bypass;

	/**
	 * Name of the settings field in the options table.
	 *
	 * @since 1.8.0
	 *
	 * @var string
	 */
	public $settings_field;

	/**
	 * Associative array (field name => values) for the default settings on this
	 * admin page.
	 *
	 * @since 1.8.0
	 *
	 * @var array
	 */
	public $default_settings;

	/**
	 * Associative array of configuration options for the admin menu(s).
	 *
	 * @since 1.8.0
	 *
	 * @var array
	 */
	public $menu_ops;

	/**
	 * Associative array of configuration options for the settings page.
	 *
	 * @since 1.8.0
	 *
	 * @var array
	 */
	public $page_ops;

	/**
	 * Help view file base.
	 *
	 * @since 2.5.0
	 *
	 * @var string
	 */
	protected $help_base;

	/**
	 * Views path base.
	 *
	 * @since 2.5.0
	 *
	 * @var string
	 */
	protected $views_base;

	/**
	 * Call this method in a subclass constructor to create an admin menu and settings page.
	 *
	 * @since 1.8.0
	 *
	 * @param string $page_id          ID of the admin menu and settings page.
	 * @param array  $menu_ops         Optional. Config options for admin menu(s). Default is empty array.
	 * @param array  $page_ops         Optional. Config options for settings page. Default is empty array.
	 * @param string $settings_field   Optional. Name of the settings field. Default is an empty string.
	 * @param array  $default_settings Optional. Field name => values for default settings. Default is empty array.
	 *
	 * @return void Return early if page ID is not set.
	 */
	public function create( $page_id = '', array $menu_ops = [], array $page_ops = [], $settings_field = '', array $default_settings = [] ) {

		$this->page_id = $this->page_id ?: $page_id;

		if ( ! $this->page_id ) {
			return;
		}

		$this->menu_ops         = $this->menu_ops ?: $menu_ops;
		$this->page_ops         = $this->page_ops ?: $page_ops;
		$this->settings_field   = $this->settings_field ?: $settings_field;
		$this->default_settings = $this->default_settings ?: $default_settings;
		$this->help_base        = $this->help_base ?: GENESIS_VIEWS_DIR . '/help/' . $page_id . '-';
		$this->views_base       = $this->views_base ?: GENESIS_VIEWS_DIR;

		$this->page_ops = wp_parse_args(
			$this->page_ops,
			[
				'save_button_text'  => __( 'Save Changes', 'genesis' ),
				'reset_button_text' => __( 'Reset Settings', 'genesis' ),
				'saved_notice_text' => __( 'Settings saved.', 'genesis' ),
				'reset_notice_text' => __( 'Settings reset.', 'genesis' ),
				'error_notice_text' => __( 'Error saving settings.', 'genesis' ),
			]
		);

		// Check to make sure there we are only creating one menu per subclass.
		if ( isset( $this->menu_ops['submenu'] ) && ( isset( $this->menu_ops['main_menu'] ) || isset( $this->menu_ops['first_submenu'] ) ) ) {
			/* translators: %s: Genesis_Admin class name. */
			wp_die(
				sprintf(
					/* translators: %s: Genesis_Admin class name. */
					esc_html__( 'You cannot use %s to create two menus in the same subclass. Please use separate subclasses for each menu.', 'genesis' ),
					'Genesis_Admin'
				)
			);
		}

		// Create the menu(s). Conditional logic happens within the separate methods.
		add_action( 'admin_menu', [ $this, 'maybe_add_main_menu' ], 5 );
		add_action( 'admin_menu', [ $this, 'maybe_add_first_submenu' ], 5 );
		add_action( 'admin_menu', [ $this, 'maybe_add_submenu' ] );

		// Redirect to location on access, if specified.
		add_action( 'admin_init', [ $this, 'maybe_redirect' ], 1000 );

		// Set up settings and notices.
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'admin_notices', [ $this, 'notices' ] );

		// Load the page content (meta boxes or custom form).
		add_action( 'admin_init', [ $this, 'settings_init' ] );

		// Load help tab.
		add_action( 'admin_init', [ $this, 'load_help' ] );

		// Load contextual assets (registered admin page).
		add_action( 'admin_init', [ $this, 'load_assets' ] );

		// Add a sanitizer/validator.
		add_filter( 'pre_update_option_' . $this->settings_field, [ $this, 'save' ], 10, 2 );

	}

	/**
	 * Possibly create a new top level admin menu.
	 *
	 * @since 1.8.0
	 */
	public function maybe_add_main_menu() {

		// Maybe add a menu separator.
		if ( isset( $this->menu_ops['main_menu']['sep'] ) ) {
			$sep = wp_parse_args(
				$this->menu_ops['main_menu']['sep'],
				[
					'sep_position'   => '',
					'sep_capability' => '',
				]
			);

			if ( $sep['sep_position'] && $sep['sep_capability'] ) {
				$GLOBALS['menu'][ $sep['sep_position'] ] = [ '', $sep['sep_capability'], 'separator', '', 'genesis-separator wp-menu-separator' ]; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Intentionally overriding the global here.
			}
		}

		// Maybe add main menu.
		if ( isset( $this->menu_ops['main_menu'] ) && is_array( $this->menu_ops['main_menu'] ) ) {
			$menu = wp_parse_args(
				$this->menu_ops['main_menu'],
				[
					'page_title' => '',
					'menu_title' => '',
					'capability' => 'edit_theme_options',
					'icon_url'   => '',
					'position'   => '',
				]
			);

			$this->pagehook = add_menu_page( $menu['page_title'], $menu['menu_title'], $menu['capability'], $this->page_id, [ $this, 'admin' ], $menu['icon_url'], $menu['position'] );
		}

	}

	/**
	 * Possibly create the first submenu item.
	 *
	 * Because the main menu and first submenu item are usually linked, if you
	 * don't create them at the same time, something can sneak in between the
	 * two, specifically custom post type menu items that are assigned to the
	 * custom top-level menu.
	 *
	 * Plus, maybe_add_first_submenu takes the guesswork out of creating a
	 * submenu of the top-level menu you just created. It's a shortcut of sorts.
	 *
	 * @since 1.8.0
	 */
	public function maybe_add_first_submenu() {

		// Maybe add first submenu.
		if ( isset( $this->menu_ops['first_submenu'] ) && is_array( $this->menu_ops['first_submenu'] ) ) {
			$menu = wp_parse_args(
				$this->menu_ops['first_submenu'],
				[
					'page_title' => '',
					'menu_title' => '',
					'capability' => 'edit_theme_options',
				]
			);

			$this->pagehook = add_submenu_page( $this->page_id, $menu['page_title'], $menu['menu_title'], $menu['capability'], $this->page_id, [ $this, 'admin' ] );
		}

	}

	/**
	 * Possibly create a submenu item.
	 *
	 * @since 1.8.0
	 */
	public function maybe_add_submenu() {

		// Maybe add submenu.
		if ( isset( $this->menu_ops['submenu'] ) && is_array( $this->menu_ops['submenu'] ) ) {
			$menu = wp_parse_args(
				$this->menu_ops['submenu'],
				[
					'parent_slug' => '',
					'page_title'  => '',
					'menu_title'  => '',
					'capability'  => 'edit_theme_options',
				]
			);

			$this->pagehook = add_submenu_page( $menu['parent_slug'], $menu['page_title'], $menu['menu_title'], $menu['capability'], $this->page_id, [ $this, 'admin' ] );
		}

	}

	/**
	 * If specified, redirect when accessing this page's menu URL.
	 *
	 * @since 2.10.0
	 *
	 * @return void Return early if no redirect destination is set, or if a special query flag is set, or if we're not on this menu page URL.
	 */
	public function maybe_redirect() {

		if ( ! $this->redirect_to ) {
			return;
		}

		// Allow users to access the page if a special query flag is set.
		if ( $this->redirect_bypass && isset( $_REQUEST[ $this->redirect_bypass ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- We don't need nonce verification here
			return;
		}

		if ( ! genesis_is_menu_page( $this->page_id ) ) {
			return;
		}

		wp_safe_redirect( esc_url_raw( $this->redirect_to ) );
		exit;

	}

	/**
	 * Register the database settings for storage.
	 *
	 * @since 1.8.0
	 *
	 * @return void Return early if admin page doesn't store settings, or user is not on the correct admin page.
	 */
	public function register_settings() {

		// If this page doesn't store settings, no need to register them.
		if ( ! $this->settings_field ) {
			return;
		}

		register_setting(
			$this->settings_field,
			$this->settings_field,
			[
				'default' => $this->default_settings,
			]
		);

		if ( ! genesis_get_option( 'theme_version' ) ) {
			update_option( $this->settings_field, $this->default_settings );
		}

		if ( ! genesis_is_menu_page( $this->page_id ) ) {
			return;
		}

		if ( genesis_get_option( 'reset', $this->settings_field ) ) {
			if ( update_option( $this->settings_field, $this->default_settings ) ) {
				genesis_admin_redirect(
					$this->page_id,
					[
						'reset' => 'true',
					]
				);
			} else {
				genesis_admin_redirect(
					$this->page_id,
					[
						'error' => 'true',
					]
				);
			}
			exit;
		}

	}

	/**
	 * Display notices on the save or reset of settings.
	 *
	 * @since 1.8.0
	 *
	 * @return void Return early if not on the correct admin page.
	 */
	public function notices() {

		if ( ! genesis_is_menu_page( $this->page_id ) ) {
			return;
		}

		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- We don't need nonce verification here
		if ( isset( $_REQUEST['settings-updated'] ) && 'true' === $_REQUEST['settings-updated'] ) {
			printf( '<div id="message" class="updated"><p><strong>%s</strong></p></div>', esc_html( $this->page_ops['saved_notice_text'] ) );
		} elseif ( isset( $_REQUEST['reset'] ) && 'true' === $_REQUEST['reset'] ) {
			printf( '<div id="message" class="updated"><p><strong>%s</strong></p></div>', esc_html( $this->page_ops['reset_notice_text'] ) );
		} elseif ( isset( $_REQUEST['error'] ) && 'true' === $_REQUEST['error'] ) {
			printf( '<div id="message" class="updated"><p><strong>%s</strong></p></div>', esc_html( $this->page_ops['error_notice_text'] ) );
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

	}

	/**
	 * Save method.
	 *
	 * Override this method to modify form data (for validation, sanitization, etc.) before it gets saved.
	 *
	 * @since 1.8.0
	 *
	 * @param mixed $newvalue New value to save.
	 * @param mixed $oldvalue Old value.
	 * @return mixed Value to save.
	 */
	public function save( $newvalue, $oldvalue ) {

		return $newvalue;

	}

	/**
	 * Initialize the settings page.
	 *
	 * This method must be re-defined in the extended classes, to hook in the
	 * required components for the page.
	 *
	 * @since 1.8.0
	 */
	abstract public function settings_init();

	/**
	 * Load the optional help method, if one exists.
	 *
	 * @since 2.1.0
	 */
	public function load_help() {

		if ( method_exists( $this, 'help' ) ) {
			add_action( "load-{$this->pagehook}", [ $this, 'help' ] );
		}

	}

	/**
	 * Add help tab.
	 *
	 * @since 2.5.0
	 *
	 * @param string $id    Help tab id.
	 * @param string $title Help tab title.
	 */
	public function add_help_tab( $id, $title ) {

		$current_screen = get_current_screen();

		if ( null === $current_screen ) {
			return;
		}

		$current_screen->add_help_tab(
			[
				'id'       => $this->pagehook . '-' . $id,
				'title'    => $title,
				'content'  => '',
				'callback' => [ $this, 'help_content' ],
			]
		);

	}

	/**
	 * Display a help view file if it exists.
	 *
	 * @since 2.5.0
	 *
	 * @param object $screen Current WP_Screen.
	 * @param array  $tab    Help tab.
	 */
	public function help_content( $screen, $tab ) {

		$hook_len = $this->pagehook ? strlen( $this->pagehook ) + 1 : 0;
		$view     = $this->help_base . substr( $tab['id'], $hook_len ) . '.php';

		if ( is_file( $view ) ) {
			include $view;
		}

	}

	/**
	 * Set help sidebar for Genesis screens.
	 *
	 * @since 2.5.0
	 */
	public function set_help_sidebar() {

		$current_screen = get_current_screen();

		if ( null === $current_screen ) {
			return;
		}

		$screen_reader = '<span class="screen-reader-text">. ' . esc_html__( 'Link opens in a new window.', 'genesis' ) . '</span>';
		$current_screen->set_help_sidebar(
			'<p><strong>' . esc_html__( 'For more information:', 'genesis' ) . '</strong></p>' .
			'<p><a href="http://my.studiopress.com/help/" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Get Support', 'genesis' ) . $screen_reader . '</a></p>' .
			'<p><a href="http://my.studiopress.com/snippets/" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Genesis Snippets', 'genesis' ) . $screen_reader . '</a></p>' .
			'<p><a href="http://my.studiopress.com/tutorials/" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Genesis Tutorials', 'genesis' ) . $screen_reader . '</a></p>'
		);

	}

	/**
	 * Load script and stylesheet assets via scripts() and styles() methods, if they exist.
	 *
	 * @since 2.1.0
	 */
	public function load_assets() {

		// Hook scripts method.
		if ( method_exists( $this, 'scripts' ) ) {
			add_action( "load-{$this->pagehook}", [ $this, 'scripts' ] );
		}

		// Hook styles method.
		if ( method_exists( $this, 'styles' ) ) {
			add_action( "load-{$this->pagehook}", [ $this, 'styles' ] );
		}

	}

	/**
	 * Output the main admin page.
	 *
	 * This method must be re-defined in the extended class, to output the main
	 * admin page content.
	 *
	 * @since 1.8.0
	 */
	abstract public function admin();

	/**
	 * Helper function that constructs name attributes for use in form fields.
	 *
	 * Within Genesis pages, the id attributes of form fields are the same as
	 * the name attribute, as since HTML5, [ and ] characters are valid, so this
	 * function is also used to construct the id attribute value too.
	 *
	 * Other page implementation classes may wish to construct and use a
	 * get_field_id() method, if the naming format needs to be different.
	 *
	 * @since 1.8.0
	 *
	 * @param string $name Field name base.
	 * @return string Full field name.
	 */
	protected function get_field_name( $name ) {

		return sprintf( '%s[%s]', $this->settings_field, $name );

	}

	/**
	 * Echo constructed name attributes in form fields.
	 *
	 * @since 2.1.0
	 *
	 * @param string $name Field name base.
	 */
	protected function field_name( $name ) {

		echo $this->get_field_name( $name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- We escape later.

	}

	/**
	 * Helper function that constructs id attributes for use in form fields.
	 *
	 * @since 1.8.0
	 *
	 * @param string $id Field id base.
	 * @return string Full field id.
	 */
	protected function get_field_id( $id ) {

		return sprintf( '%s[%s]', $this->settings_field, $id );

	}

	/**
	 * Echo constructed id attributes in form fields.
	 *
	 * @since 2.1.0
	 *
	 * @param string $id Field id base.
	 */
	protected function field_id( $id ) {

		echo $this->get_field_id( $id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- We escape later.

	}

	/**
	 * Helper function that returns a setting value from this form's settings
	 * field for use in form fields.
	 *
	 * @since 1.8.0
	 *
	 * @param string $key Field key.
	 * @return string Field value.
	 */
	protected function get_field_value( $key ) {

		return genesis_get_option( $key, $this->settings_field );

	}

	/**
	 * Echo a setting value from this form's settings field for use in form fields.
	 *
	 * @since 2.1.0
	 *
	 * @param string $key Field key.
	 */
	protected function field_value( $key ) {

		echo $this->get_field_value( $key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- We escape later.

	}

}
