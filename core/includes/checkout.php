<?php

class Vxsdk_pnix {
    
    private $plugin_name;
	private $version;
    
    protected $loader;

    public $idx_admin;

    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        
        /**
         * Load all the custom libraries
         */

        $this->load_libraries();
        $this->define_admin();
        
        if(empty($version) || $version == null) { $this->version = '1.0'; } else { $this->version = $version; }
        
        add_action ( 'init', 'wp_idx_init' );
    }

    public function wp_idx_init() {

        wp_enqueue_script(
            'venixs-script-handle',
            plugin_dir_url(__FILE__) . '../../resources/js/script.js',
            array(),
            'version',
            true
        );

        $venixs_pub_key = esc_attr(get_option('venixs_pub_key'));
        $venixs_config_scope = array(
            'signature' => !empty($venixs_pub_key) ? $venixs_pub_key : '',
        );
        
        // wp_enqueue_script( 'functionality-scripts', plugin_dir_url( __FILE__ ) . '../../core/script.js', array('jquery') );

        wp_localize_script(
            'venixs-script-handle',
            'venixsConfigScope',
            $venixs_config_scope
        );
        
        wp_enqueue_style( 'style-handler', plugin_dir_url( __FILE__ ) . '../../resources/css/styles.css', array(), $this->version, 'all' );


        // Check if the logged in WordPress User can edit Posts or Pages
        // If not, don't register our TinyMCE plugin
        if (! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {
            return;
        }

        // // Check if the logged in WordPress User has the Visual Editor enabled
        // // If not, don't register our TinyMCE plugin
        if (get_user_option('rich_editing') !== 'true' ) {
            return;
        }
        
        add_filter('mce_buttons', array( &$this, 'add_tinymce_toolbar_button' ));

    }

    private function load_libraries()
    {
        include_once plugin_dir_path( __FILE__ ) . '../../core/admin/venixs-plugin-admin.php';

        require_once plugin_dir_path( __FILE__ ) . '../../core/includes/venixs_loader.php';

        $this->loader = new VXSDK_Loader();
    }


    function add_tinymce_toolbar_button( $buttons ) 
    {

        array_push($buttons, 'custom_class');
        return $buttons;

    }

    public function run() {
        $this->loader->run();
        $this->wp_idx_init();
    }

    public function define_admin()
    {

        $idx_admin = new VXSDKAdminConfig($this->plugin_name, $this->version);
        // $idx_admin = new VXSDKAdminConfig( $plugin_name, $version );

        // Add settings link to plugin
        $this->loader->add_filter(
            'plugin_action_links_' . VXSDK_PLUGIN_BASENAME,
            $idx_admin,
            'add_custom_action_links'
        );

    }
 }


