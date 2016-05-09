<?php

/**
 * The WordPress Plugin Boilerplate.
 *
 * A foundation off of which to build well-documented WordPress plugins that
 * also follow WordPress Coding Standards and PHP best practices.
 *
 * @package   Viper
 * @author    Mattia Migliorini <mattia@squeezyweb.com>
 * @license   GPL-2.0+
 * @link      http://www.squeezyweb.com
 * @copyright 2016 2016 SqueezyWeb
 *
 * Plugin Name:       Products Showcase for WP
 * Plugin URI:        @TODO
 * Description:       @TODO
 * Version:           1.0.0
 * Author:            Mattia Migliorini
 * Author URI:        http://www.squeezyweb.com
 * Text Domain:       viper
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * WordPress-Plugin-Boilerplate-Powered: v1.2.0
 */

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
	die;
}

/*
 * ------------------------------------------------------------------------------
 * Public-Facing Functionality
 * ------------------------------------------------------------------------------
 */
require_once( plugin_dir_path( __FILE__ ) . 'includes/load_textdomain.php' );

/*
 * Load library for simple and fast creation of Taxonomy and Custom Post Type
 */

require_once( plugin_dir_path( __FILE__ ) . 'includes/Taxonomy_Core/Taxonomy_Core.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/CPT_Core/CPT_Core.php' );

/*
 * Load template system
 */

require_once( plugin_dir_path( __FILE__ ) . 'includes/template.php' );

/**
 * Create a helper function for easy SDK access.
 *
 * @global type $v_fs
 * @return object
 */
function v_fs() {
    global $v_fs;

    if ( ! isset( $v_fs ) ) {
        // Include Freemius SDK.
        require_once dirname(__FILE__) . '/includes/freemius/start.php';

        $v_fs = fs_dynamic_init( array(
            'id'                => '283',
            'slug'              => 'viper',
            'public_key'        => 'pk_ac561c8b47cd71214289dc4094f90',
            'is_live'           => false,
            'is_premium'        => false,
            'has_addons'        => false,
            'has_paid_plans'    => false,
            'menu'              => array(
                'slug'       => 'viper',
                'account'    => false,
                'support'    => false
            ),
        ) );
    }

    return $v_fs;
}

// Init Freemius.
// v_fs();

/*
 * Load Language wrapper function for WPML/Ceceppa Multilingua/Polylang
 */

require_once( plugin_dir_path( __FILE__ ) . 'includes/language.php' );
require_once( plugin_dir_path( __FILE__ ) . 'public/class-viper.php' );
require_once( plugin_dir_path( __FILE__ ) . 'public/includes/v_ActDeact.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 */

register_activation_hook( __FILE__, array( 'v_ActDeact', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'v_ActDeact', 'deactivate' ) );
add_action( 'plugins_loaded', array( 'Viper', 'get_instance' ), 9999 );

/*
 * -----------------------------------------------------------------------------
 * Dashboard and Administrative Functionality
 * -----------------------------------------------------------------------------
*/

/*
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 */

if ( is_admin() && (!defined( 'DOING_AJAX' ) || !DOING_AJAX ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-viper-admin.php' );
	add_action( 'plugins_loaded', array( 'Viper_Admin', 'get_instance' ) );
}
