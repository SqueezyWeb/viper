<?php

/**
 * viper.
 *
 * @author    Mattia Migliorini <mattia@squeezyweb.com>
 * @license   GPL-2.0+
 *
 * @link      http://www.squeezyweb.com
 *
 * @copyright 2016 SqueezyWeb
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-viper-admin.php`
 *
 * @author  Mattia Migliorini <mattia@squeezyweb.com>
 */
class Viper
{
    /**
   * Plugin version, used for cache-busting of style and script file references.
   *
   * @since   1.0.0
   *
   * @var     string
   */
  const VERSION = '1.0.0-beta3';

  /**
   * Unique identifier for your plugin.
   *
   *
   * The variable name is used as the text domain when internationalizing strings
   * of text. Its value should match the Text Domain file header in the main
   * plugin file.
   *
   * @var      string
   *
   * @since    1.0.0
   */
  protected static $plugin_slug = 'viper';

  /**
   * Unique identifier for your plugin.
   *
   * @var      string
   *
   * @since    1.0.0
   */
  protected static $plugin_name = 'viper';

  /**
   * Instance of this class.
   *
   * @var      object
   *
   * @since    1.0.0
   */
  protected static $instance = null;

  /**
   * Array of cpts of the plugin.
   *
   * @var      array
   *
   * @since    1.0.0
   */
  protected $cpts = array('product');

  /**
   * Initialize the plugin by setting localization and loading public scripts
   * and styles.
   *
   * @since     1.0.0
   */
  private function __construct()
  {
      // Create Custom Post Type https://github.com/jtsternberg/CPT_Core/blob/master/README.md
    register_via_cpt_core(
      array(__('Product', $this->get_plugin_slug()), __('Products', $this->get_plugin_slug()), 'product'),
      array(
        'taxonomies' => array('product-category'),
        'menu_icon' => 'dashicons-cart',
        'has_archive' => false,
        'capability_type' => 'post',
        'supports' => array(
          'title',
          'editor',
          'thumbnail',
          'excerpt',
          'page-attributes',
        ),
      )
    );

      add_filter('pre_get_posts', array($this, 'filter_search'));

    // Create Custom Taxonomy https://github.com/jtsternberg/Taxonomy_Core/blob/master/README.md
    register_via_taxonomy_core(
      array(
        __('Category', $this->get_plugin_slug()),
        __('Categories', $this->get_plugin_slug()),
        'product-category',
      ),
      array(
        'hierarchical' => true,
        'show_tagcloud' => false,
        'rewrite' => array(
          'slug' => '',
          'with_front' => false,
          'hierarchical' => true,
        ),
      ),
      array('product')
    );

      add_filter('body_class', array($this, 'add_v_class'), 10, 3);

    // Override the template hierarchy for load /templates/content-product.php
    add_filter('template_include', array($this, 'load_content_product'));

    // Load public-facing style sheet and JavaScript.
    add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));

    // Enable Divi Builder in product Post Type.
    add_filter('et_builder_post_types', array($this, 'enable_divi_builder'));

      add_shortcode('products', array($this, 'products_shortcode'));
  }

  /**
   * Return the plugin slug.
   *
   * @since    1.0.0
   *
   * @return    Plugin slug variable.
   */
  public function get_plugin_slug()
  {
      return self::$plugin_slug;
  }

  /**
   * Return the plugin name.
   *
   * @since    1.0.0
   *
   * @return    Plugin name variable.
   */
  public function get_plugin_name()
  {
      return self::$plugin_name;
  }

  /**
   * Return the version.
   *
   * @since    1.0.0
   *
   * @return    Version const.
   */
  public function get_plugin_version()
  {
      return self::VERSION;
  }
  /**
   * Return the cpts.
   *
   * @since    1.0.0
   *
   * @return    Cpts array
   */
  public function get_cpts()
  {
      return $this->cpts;
  }

  /**
   * Return an instance of this class.
   *
   * @since     1.0.0
   *
   * @return    object    A single instance of this class.
   */
  public static function get_instance()
  {
      // If the single instance hasn't been set, set it now.
    if (null == self::$instance) {
        self::$instance = new self();
    }

      return self::$instance;
  }

  /**
   * Retrieve product categories associated with a product.
   *
   * If in The Loop, the $post parameter can be omitted and the current post
   * will be used.
   *
   * @since 1.0.0
   * @static
   *
   * @param int|object $post Optional. Post ID or object. Can be null in The
   * Loop and the current post will be used.
   *
   * @ return array|false|WP_Error Array of term objects on success, false if
   * there are no terms or the post does not exist, WP_Error on failure.
   */
  public static function get_the_categories($post = null)
  {
      if (is_null($post)) {
          global $post;
      }

      return get_the_terms($post, 'product-category');
  }

  /**
   * Retrieve a post's product categories as a list with specified format.
   *
   * @since 1.0.0
   * @static
   *
   * @param int|object $post Optional. Post ID or object. Can be null in The
   * Loop where the current post will be used. Default null.
   * @param string $before Optional. Before list.
   * @param string $sep Optional. Separate items using this.
   * @param string $after Optional. After list.
   *
   * @return string|false|WP_Error A list of terms on success, false if there
   * are no terms, WP_Error on failure.
   */
  public static function get_the_categories_list($post = null, $before = '', $sep = '', $after = '')
  {
      if (is_null($post)) {
          global $post;
      }

    // If $post is null again, it has been left blank on function call, but
    // outside of The Loop.
    if (is_null($post)) {
        return new WP_Error(
        'bad_call',
        sprintf(__('<code>%s</code> has been called without passing the <code>$post</code> parameter outside of The Loop.', 'viper'), __METHOD__)
      );
    }

    // Apply default separator.
    if (empty($sep)) {
        $sep = ', ';
    }

      $id = is_int($post) ? $post : $post->ID;

      return get_the_term_list($id, 'product-category', $before, $sep, $after);
  }

  /**
   * Output a post's product categories list as a list with specified format.
   *
   * @since 1.0.0
   * @static
   *
   * @param int|object $post Optional. Post ID or object. Can be null in The
   * Loop where the current post will be used. Default null.
   * @param string $before Optional. Before list.
   * @param string $sep Optional. Separate items using this.
   * @param string $after Optional. After list.
   */
  public static function the_categories_list($post = null, $before = '', $sep = '', $after = '')
  {
      $list = self::get_the_categories_list($post, $before, $sep, $after);
      if (is_wp_error($list)) {
          trigger_error($list->get_error_message(), E_USER_WARNING);
      } elseif ($list) {
          echo $list;
      }
  }

  /**
   * Add support for custom CPT on the search box.
   *
   * @since    1.0.0
   *
   * @param    object    $query
   *
   * @return object
   */
  public function filter_search($query)
  {
      if ($query->is_search) {
          // Mantain support for post
      $this->cpts[] = 'post';
          $query->set('post_type', $this->cpts);
      }

      return $query;
  }

  /**
   * Register and enqueue public-facing style sheet.
   *
   * @since    1.0.0
   */
  public function enqueue_styles()
  {
      wp_enqueue_style($this->get_plugin_slug().'-plugin-styles', plugins_url('assets/css/public.css', __FILE__), array(), self::VERSION);
  }

  /**
   * Add class in the body on the frontend.
   *
   * @since    1.0.0
   *
   * @param array $classes THe array with all the classes of the page.
   *
   * @return array
   */
  public function add_v_class($classes)
  {
      $classes[] = $this->get_plugin_slug();

      return $classes;
  }

  /**
   * Example for override the template system on the frontend.
   *
   * @since    1.0.0
   *
   * @param string $original_template The original templace HTML.
   *
   * @return string
   */
  public function load_content_product($original_template)
  {
      if (is_singular('product') && in_the_loop()) {
          return v_get_template_part('content', 'product', false);
      } else {
          return $original_template;
      }
  }

  /**
   * Enable Divi Builder for Product Post Type.
   *
   * Only enables the Divi Builder if the CPT supports the editor.
   *
   * @since 1.0.0
   *
   * @param array $post_types Divi Builder enabled post types.
   *
   * @return array Filtered Divi Builder enabled post types.
   */
  public function enable_divi_builder($post_types)
  {
      if (post_type_supports('product', 'editor')) {
          $post_types[] = 'product';
      }

      return $post_types;
  }

  /**
   * Shortcode to display a set of products by category.
   *
   * @since 1.0.0
   *
   * @param array $atts Shortcode attributes.
   *
   * @return string HTML code for the list of products.
   */
  public function products_shortcode($atts)
  {
      $atts = shortcode_atts(
      array(
        'category' => '',
      ),
      $atts,
      'products'
    );

      $products = get_posts(array(
      'posts_per_page' => -1,
      'product-category' => $atts['category'],
      'post_type' => 'product',
      'orderby' => 'title',
      'order' => 'ASC',
    ));

    // Initialize classes for template.

    /*
     * Products shortcode container classes.
     *
     * @since 1.0.0
     *
     * @param array $classes Array of classes for the container.
     */
    $container_classes = apply_filters('viper_shortcode_products_container_classes', array('viper-contaner', 'products-container'));

    /*
     * Product thumbnail size.
     *
     * @since 1.0.0
     *
     * @param string|array $size Product thumbnail size.
     */
    $thumbnail_size = apply_filters('viper_shortcode_products_thumbnail_size', 'medium');

    // TODO: include template to display products loop.
    ob_start();
      include v_get_template_part('shortcode', 'products', false);

      return ob_get_clean();
  }
}
