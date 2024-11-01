<?php
    class VXSDKAdminConfig {

        private $plugin_name;
        private $version;

        public function __construct( $plugin_name, $version )
        {

            $this->plugin_name = $plugin_name;
            $this->version = $version;

            add_action( 'admin_menu', 'vxsdkplugin_add_settings_page' );
            
            add_action( 'admin_init', 'vxsdk_plugin_register_setting_page' );

            add_action( 'init',  'register_venixs_adminsdk' );

            function register_venixs_adminsdk()
            {
                
                $labels = array(
                    'name' => _x('Payment Forms', 'Venixs_configuration_form'),
                    'singular_name' => _x('Venixs Form', 'Venixs_configuration_form'),
                    'add_new' => _x('Add New', 'Venixs_configuration_form'),
                    'add_new_item' => _x('Add Venixs Form', 'venixs_configuration_form'),
                    'edit_item' => _x('Edit Venixs Form', 'venixs_configuration_form'),
                    'new_item' => _x('Venixs Form', 'venixs_configuration_form'),
                    'view_item' => _x('View Venixs Form', 'venixs_configuration_form'),
                    'all_items' => _x('All Forms', 'venixs_configuration_form'),
                    'search_items' => _x('Search Venixs Forms', 'venixs_configuration_form'),
                    'not_found' => _x('No Venixs Forms found', 'venixs_configuration_form'),
                    'not_found_in_trash' => _x('No Venixs Forms found in Trash', 'venixs_configuration_form'),
                    'parent_item_colon' => _x('Parent Venixs Form:', 'venixs_configuration_form'),
                    'menu_name' => _x('Venixs Forms', 'venixs_configuration_form'),
                );

                $args = array(
                    'labels' => $labels,
                    'hierarchical' => true,
                    'description' => 'Venixs Forms filterable by genre',
                    'supports' => array('title', 'editor'),
                    'public' => true,
                    'show_ui' => true,
                    'show_in_menu' => true,
                    'menu_position' => 5,
                    'menu_icon' => plugins_url('../../resources/src_images/logo.png', __FILE__),
                    'show_in_nav_menus' => true,
                    'publicly_queryable' => true,
                    'exclude_from_search' => false,
                    'has_archive' => false,
                    'query_var' => true,
                    'can_export' => true,
                    'rewrite' => false,
                    'comments' => false,
                    'capability_type' => 'post'
                );
    
                register_post_type( 'vvenixs_configuration_form', $args );
            }

            function venixs_c_add_view_payments($actions, $post)
            {
                if ( get_post_type() === 'vvenixs_configuration_form' ) {
                    unset($actions['view']);
                    unset($actions['quick edit']);
                    $url = add_query_arg(
                        array(
                            'post_id' => $post->ID,
                            'action' => 'submissions',
                        )
                    );
                    $actions['export'] = '<a href="' . admin_url('admin.php?page=submissions&form=' . $post->ID) . '" >View Payments</a>';
                }
                return $actions;
            }

            function vxsdkplugin_add_settings_page()
            {
                add_menu_page(
                    __( 'Venixs', 'venixs_checkout' ),
                    __( 'Venixs', 'venixs_checkout' ),
                    'manage_options',
                    'venixs_checkout', // what is displayed as the name on the url
                    'vxsdk_wpplugin_settings_page_markup',
                    plugins_url('../../resources/src_images/logo.png', __FILE__),
                    5
                );
                
                add_submenu_page('venixs_checkout', __( 'Configuration', 'venixs_checkout' ), __( 'Configuration', 'venixs_checkout' ), 'manage_options', 'edit.php?post_type=venixs_config', 'vxsdk_show_admin_settings_screen');
            }

            function vxsdk_wpplugin_settings_page_markup()
            {
                if(!current_user_can('manage_options')) {
                    return;
                }
                

                echo '<p><h1>Dashboard display overview coming soon!</h1></p>';
            }

            function vkyc_mode_check($name, $txncharge)
            {
                if ($name == $txncharge) {
                    $result = "selected";
                } else {
                    $result = "";
                }
                return $result;
            }

            function vxsdk_show_admin_settings_screen()
            {
    
    ?>
                <div class="wrap venixs_x_ai">
                    <h1>Venixs SDK Configuration</h1>
                    <h2>SDK Keys Settings</h2>
                    <div>Don't have your Chat API Keys? <br>Get it here: <a href="https://console.venixs.com/Configurations" target="_blank">here</a> </div><br><br>
                    <form method="post" action="options.php">
                        <?php settings_fields('venplugin-settings-pallet');
                        do_settings_sections('venplugin-settings-pallet'); ?>
                        <table class="form-table setting_page">
                            <tr valign="top">
                                <div class="input-group">
                                    <input class="form-control" type="text" value="<?php echo esc_attr(get_option('venixs_pub_key')); ?>" name="venixs_pub_key" required="required" placeholder="Chat Public Key">
                                    <label for="venixs_pub_key">Chat Public Key</label>
                                    <div class="padlock-mark">&#128274;</div>
                                </div>
                            </tr>
    
                        </table>
    
                        <hr>
    
                        <?php submit_button(); ?>
                    </form>
                </div>
            <?php
            }
            
            function vxsdk_plugin_register_setting_page()
            {
                register_setting('venplugin-settings-pallet', 'venixs_pub_key');
            }
        }

        public function add_custom_action_links( $links )
        {
            $settings_link = array(
                '<a href="' . admin_url('admin.php?page=edit.php?post_type=venixs_config') . '">' . __('Configuration', 'Venixs SDK') . '</a>',
            );
            return array_merge($settings_link, $links);
        }
    }

    if ( !class_exists('WP_List_Table') ) {
        include_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
    }
