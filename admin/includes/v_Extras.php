<?php

/**
 * This class contain all the snippet or extra that improve the experience on the backend
 *
 * @package   Plugin_name
 * @author    Mattia Migliorini <mattia@squeezyweb.com>
 * @license   GPL-2.0+
 * @link      http://www.squeezyweb.com
 * @copyright 2016 SqueezyWeb
 */
class v_Extras {

    /**
     * Initialize the snippet
     */
    function __construct() {
        $plugin = Viper::get_instance();
        $this->plugin_slug = $plugin->get_plugin_slug();
        $this->cpts = $plugin->get_cpts();

        // At Glance Dashboard widget for your cpts
        add_filter( 'dashboard_glance_items', array( $this, 'cpt_glance_dashboard_support' ), 10, 1 );
        // Activity Dashboard widget for your cpts
        add_filter( 'dashboard_recent_posts_query_args', array( $this, 'cpt_activity_dashboard_support' ), 10, 1 );
    }

    /**
     * Add the counter of your CPTs in At Glance widget in the dashboard<br>
     * NOTE: add in $post_types your cpts, remember to edit the css style (admin/assets/css/admin.css) for change the dashicon<br>
     *
     *        Reference:  http://wpsnipp.com/index.php/functions-php/wordpress-post-types-dashboard-at-glance-widget/
     *
     * @since    1.0.0
     * @return array
     */
    public function cpt_glance_dashboard_support( $items = array() ) {
        $post_types = $this->cpts;
        foreach ( $post_types as $type ) {
            if ( !post_type_exists( $type ) ) {
                continue;
            }
            $num_posts = wp_count_posts( $type );
            if ( $num_posts ) {
                $published = intval( $num_posts->publish );
                $post_type = get_post_type_object( $type );
                $text = _n( '%s ' . $post_type->labels->singular_name, '%s ' . $post_type->labels->name, $published, $this->plugin_slug );
                $text = sprintf( $text, number_format_i18n( $published ) );
                if ( current_user_can( $post_type->cap->edit_posts ) ) {
                    $items[] = '<a class="' . $post_type->name . '-count" href="edit.php?post_type=' . $post_type->name . '">' . sprintf( '%2$s', $type, $text ) . "</a>\n";
                } else {
                    $items[] = sprintf( '%2$s', $type, $text ) . "\n";
                }
            }
        }
        return $items;
    }

    /**
     * Add the recents post type in the activity widget<br>
     * NOTE: add in $post_types your cpts
     *
     * @since    1.0.0
     * @return array
     */
    function cpt_activity_dashboard_support( $query_args ) {
        if ( !is_array( $query_args[ 'post_type' ] ) ) {
            // Set default post type
            $query_args[ 'post_type' ] = array( 'page' );
        }
        $query_args[ 'post_type' ] = array_merge( $query_args[ 'post_type' ], $this->cpts );
        return $query_args;
    }

}

new v_Extras();
