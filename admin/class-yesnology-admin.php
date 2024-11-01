<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://yesnology.com/
 * @since      1.0.0
 *
 * @package    Yesnology
 * @subpackage Yesnology/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two hooks to
 * enqueue the admin-facing stylesheet and JavaScript.
 * As you add hooks and methods, update this description.
 *
 * @package    Yesnology
 * @subpackage Yesnology/admin
 * @author     yesnology <zavaroni@yesnology.com>
 */
class Yesnology_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The unique prefix of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_prefix    The string used to uniquely prefix technical functions of this plugin.
	 */
	private $plugin_prefix;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
   * A reference the class responsible for rendering the submenu page.
     *
     * @var    Submenu_Page
     * @access private
     */
	private $submenu_page;        
    
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $plugin_prefix    The unique prefix of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $plugin_prefix, $version, $submenu_page ) {

		$this->plugin_name   = $plugin_name;
		$this->plugin_prefix = $plugin_prefix;
		$this->version = $version;
		$this->submenu_page = $submenu_page;
		$this->baseUrl = 'https://isp.yesnology.com';
		$this->baseUrlApi = 'https://api.yesnology.com';
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_styles( $hook_suffix ) {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/yesnology-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_scripts( $hook_suffix ) {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/yesnology-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, $this->plugin_name . 'object', array('yesnologyShortcodeOk' => __('Here we are! Here is the shortcode to copy and paste where you want it to appear in a new form or in an existing one!', 'YesNology'), 'yesnologyCopyButton' => __('Copy', 'YesNology')) );
	}


	public function add_menu() {
    	add_menu_page(
        	'yesnology',
        	'YesNology',
        	'manage_options',
			'yesnology',
			array( $this->submenu_page, 'render' ),
        	plugins_url( 'yesnology/admin/logo-compatto.png' ),
			20
    	);
		add_submenu_page( 'yesnology', 'base', 'Setup',
			'manage_options', 'yesnology', array( $this->submenu_page, 'render' ));
		add_submenu_page( 'yesnology', 'design', 'Design',
			'manage_options', 'yesnology_display', array( $this->submenu_page, 'renderConfirmPage' ));
	}
	
	public function yesnology_setting_init() {
		 $response = wp_remote_get( $this->baseUrl . "/connect/token",
		 	array(
				'method'      => 'POST',
				'headers'     => Array('Content-Type' => 'application/x-www-form-urlencoded'),
				'body'		  => "client_id=yesnology.plugin.client&client_secret="  . wp_kses(get_option( 'yesnology_secretkey' ), 'strip') . "&grant_type=password&username=" . wp_kses(get_option( 'yesnology_username' ), 'strip') . "&password=" . wp_kses(get_option( 'yesnology_password' ), 'strip') . "&scope=api_plugins_access"
		 	));
		if (!is_wp_error($response)) $token = json_decode($response['body'])->access_token;
		 $response = wp_remote_get( $this->baseUrlApi . "/api/plugins/usercompanies",
		 	array(
				'method'      => 'GET',
				'headers'     => Array('Accept' => 'application/json', 'x-TenantId' => wp_kses(get_option( 'yesnology_tenatId' ), 'strip'), 'Authorization' => 'Bearer ' . $token),
			));
		if (!is_wp_error($response)) $company = json_decode($response['body']);
		 $response = wp_remote_get( $this->baseUrlApi . "/api/plugins/companies/" . wp_kses(get_option( 'yesnology_companyId' ), 'strip') . "/binders",
		 	array(
				'method'      => 'GET',
				'headers'     => Array('Accept' => 'application/json', 'x-TenantId' => wp_kses(get_option( 'yesnology_tenatId' ), 'strip'), 'Authorization' => 'Bearer ' . $token),
			));
		if (!is_wp_error($response)) $binders = json_decode($response['body']);

		add_settings_section(
			'yesnology_page_user_section',
			__( 'Registration data', 'YesNology' ),
			null,
			'yesnology'
		);
		add_settings_field(
			'yesnology_username',
			__( 'Username', 'YesNology' ),
			array( $this->submenu_page, 'username_markup' ),
			'yesnology',
			'yesnology_page_user_section'
		 );    
		add_settings_field(
			'yesnology_password',
			__( 'Password', 'YesNology' ),
			array( $this->submenu_page, 'password_markup' ),
			'yesnology',
			'yesnology_page_user_section'
		 );
		add_settings_field(
			'yesnology_clientId',
			__( 'ClientId', 'YesNology' ),
			array( $this->submenu_page, 'clientId_markup' ),
			'yesnology',
			'yesnology_page_user_section',
			['class' => 'hidden']
		 );
		add_settings_field(
			'yesnology_secretKey',
			__( 'SecretKey', 'YesNology' ),
			array( $this->submenu_page, 'secretKey_markup' ),
			'yesnology',
			'yesnology_page_user_section'
		 );
		add_settings_section(
			'yesnology_page_company_section',
			__( 'Company data', 'YesNology' ),
			null,
			'yesnology_company'
			
		 );
		add_settings_field(
			'yesnology_companyId',
			__( null, 'yesnology_company' ),
			array( $this->submenu_page, 'companyId_markup' ),
			'yesnology_company',
			'yesnology_page_company_section',
			['class' => 'hidden']
		 );
		add_settings_field(
			'yesnology_tenatId',
			__( null, 'yesnology_company' ),
			array( $this->submenu_page, 'tenatId_markup' ),
			'yesnology_company',
			'yesnology_page_company_section',
			['class' => 'hidden']
		 );
		add_settings_field('yesnology_company', 
		 __( 'Company', 'YesNology' ),
		 array( $this->submenu_page, 'companySelect_markup' ),
		 'yesnology_company',
		 'yesnology_page_company_section',
		 array(
			 'option' => $company
		 )
	  	 );  
		add_settings_section(
			'yesnology_page_binder_section',
			__( 'Setup shortcode', 'YesNology' ),
			null,
			'yesnology_binder'
			
		);
		add_settings_field("yesnology_binder", 
			__( 'Binder', 'YesNology' ),
			array( $this->submenu_page, 'binder_markup' ),
			'yesnology_binder',
			'yesnology_page_binder_section',
			array(
				'option' => $binders
			)
	 	);  
		add_settings_section(
			'yesnology_page_submit_design',
			__( 'Thank you text', 'YesNology' ),
			null,
			'yesnology_design_submit'
		);
		add_settings_field(
			'yesnology_submit_confirm',
			__( 'Confirm page', 'YesNology' ),
			array( $this->submenu_page, 'confirmPage_markup' ),
			'yesnology_design_submit',
			'yesnology_page_submit_design'
		 );
		add_settings_field(
			'yesnology_bodyColor',
			__( 'Body color form', 'YesNology' ),
			array( $this->submenu_page, 'bodyColor_markup' ),
			'yesnology_design_submit',
			'yesnology_page_submit_design'
		 );
		add_settings_field(
			'yesnology_textColor',
			__( 'Text color form', 'YesNology' ),
			array( $this->submenu_page, 'textColor_markup' ),
			'yesnology_design_submit',
			'yesnology_page_submit_design'
		 );
		add_settings_field(
			'yesnology_buttonPosition',
			__( 'Submit position', 'YesNology' ),
			array( $this->submenu_page, 'buttonPosition_markup' ),
			'yesnology_design_submit',
			'yesnology_page_submit_design'
		 );
		register_setting( 'yesnology', 'yesnology_username' );
		register_setting( 'yesnology', 'yesnology_password', array('type' => 'string', 'description' => null, ) );
		register_setting( 'yesnology', 'yesnology_clientId' );
		register_setting( 'yesnology', 'yesnology_secretKey' );
		register_setting( 'yesnology_company', 'yesnology_companyId' );
		register_setting( 'yesnology_company', 'yesnology_tenatId' );
		register_setting( 'yesnology_company', 'yesnology_companySelect' );
		register_setting( 'yesnology_design_submit', 'yesnology_bodyColor' );
		register_setting( 'yesnology_design_submit', 'yesnology_textColor' );
		register_setting( 'yesnology_design_submit', 'yesnology_buttonPosition' );
		register_setting( 'yesnology_design_submit', 'yesnology_submit_confirm' );
	}
}
