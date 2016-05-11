<?php

/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-viper.php`
 *
 * @author    Mattia Migliorini <mattia@squeezyweb.com>
 * @license   GPL-2.0+
 *
 * @link      http://www.squeezyweb.com
 *
 * @copyright 2016 SqueezyWeb
 */
class Viper_Admin
{
    /**
     * Instance of this class.
     *
     * @var object
     *
     * @since    1.0.0
     */
    protected static $instance = null;

    /**
     * Slug of the plugin screen.
     *
     * @var string
     *
     * @since    1.0.0
     */
    protected $plugin_screen_hook_suffix = null;

    /**
     * Initialize the plugin by loading admin scripts & styles and adding a
     * settings page and menu.
     *
     * @since     1.0.0
     */
    private function __construct()
    {

        /*
         * @TODO :
         *
         * - Uncomment following lines if the admin class should only be available for super admins
         */
        /* if( ! is_super_admin() ) {
          return;
          }
             */

        $plugin = Viper::get_instance();
        $this->plugin_slug = $plugin->get_plugin_slug();
        $this->plugin_name = $plugin->get_plugin_name();
        $this->version = $plugin->get_plugin_version();
        $this->cpts = $plugin->get_cpts();

        // Load admin style in dashboard for the At glance widget
        add_action('admin_head-index.php', array($this, 'enqueue_admin_styles'));

        // Add the options page and menu item.
        // add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

        /*
         * Define custom functionality.
         *
         * Read more about actions and filters:
         * http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
         */
        add_action('@TODO', array($this, 'action_method_name'));
        add_filter('@TODO', array($this, 'filter_method_name'));
        /*
         * Debug mode
         */
        require_once plugin_dir_path(__FILE__).'includes/debug.php';
        $debug = new v_Debug();
        $debug->log(__('Plugin Loaded', $this->plugin_slug));

        /*
         * Load CPT_Columns
         *
         * Check the file for example
         */
        require_once plugin_dir_path(__FILE__).'includes/CPT_Columns.php';
        $post_columns = new CPT_columns('product');
        $post_columns->add_column('menu_order', array(
            'label' => __('Order'),
            'sortable' => true,
                'type' => 'menu_order',
            )
        );

        /*
         * All the extras functions
         */
        require_once plugin_dir_path(__FILE__).'includes/v_Extras.php';
    }

    /**
     * Return an instance of this class.
     *
     * @since     1.0.0
     *
     * @return object A single instance of this class.
     */
    public static function get_instance()
    {

        /*
         * @TODO :
         *
         * - Uncomment following lines if the admin class should only be available for super admins
         */
        /* if( ! is_super_admin() ) {
          return;
          }
             */

        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }
    /**
     * NOTE:     Actions are points in the execution of a page or process
     *           lifecycle that WordPress fires.
     *
     *           Actions:    http://codex.wordpress.org/Plugin_API#Actions
     *           Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
     *
     * @since    1.0.0
     */
    public function action_method_name()
    {
        // @TODO: Define your action hook callback here
    }

    /**
     * NOTE:     Filters are points of execution in which WordPress modifies data
     *           before saving it or sending it to the browser.
     *
     *           Filters: http://codex.wordpress.org/Plugin_API#Filters
     *           Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
     *
     * @since    1.0.0
     */
    public function filter_method_name()
    {
        // @TODO: Define your filter hook callback here
    }
}
