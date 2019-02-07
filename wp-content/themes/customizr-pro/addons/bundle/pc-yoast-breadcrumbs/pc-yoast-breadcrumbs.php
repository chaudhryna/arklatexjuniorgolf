<?php
/**
* Fires the plugin
* @package      PC_pro_bundle
* @author Nicolas GUILLAUME - Rocco ALIBERTI
* @since 1.0
*/
if ( ! class_exists( 'PC_yoast_breadcrumbs' ) ) :
class PC_yoast_breadcrumbs {
  static $instance;

  function __construct() {
    self::$instance =& $this;

    //plugins compatibility
    add_action ('after_setup_theme'          , array( $this , 'pc_yoastbr_set_plugins_supported'), 20 );
    add_action ('after_setup_theme'          , array( $this , 'pc_yoastbr_plugins_compatibility'), 30 );

  }//end __construct



  /**
  * Set plugins supported ( before the plugin compat function is fired )
  * => allows to easily remove support by firing remove_theme_support() (with a priority < pc_yoastbr_plugins_compatibility ) on hook 'after_setup_theme'
  * hook : after_setup_theme
  *
  * @package PC_pro_bundle
  */
  function pc_yoastbr_set_plugins_supported() {
    // https://kb.yoast.com/kb/add-theme-support-for-yoast-seo-breadcrumbs/
    // add support for plugins (added in v3.1+)
    add_theme_support( 'yoast-seo-breadcrumbs' );
  }



  /**
  * This function handles the compatibility with the Yoast SEO breadcrumbs
  *
  * @package PC_pro_bundle
  */
  function pc_yoastbr_plugins_compatibility() {
    if ( current_theme_supports( 'yoast-seo-breadcrumbs' ) && CZR_plugins_compat::$instance -> czr_fn_is_plugin_active('wordpress-seo/wp-seo.php') )
      $this -> pc_yoastbr_set_yoast_seo_breadcrumbs_compat();
  }


  /**
  * Yoast SEO breadcrumbs compat hooks
  *
  * @package PC_pro_bundle
  */
  function pc_yoastbr_set_yoast_seo_breadcrumbs_compat() {
    // Returns a callback function needed by 'active_callback' to enable the options in the customizer
    add_filter( 'tc_yoast_breadcrumbs_option_enabled', 'pc_yoastbr_option_enabled_return_cb' );
    if ( ! function_exists( 'pc_yoastbr_option_enabled_return_cb' ) ) {
      function pc_yoastbr_option_enabled_return_cb() {
        return 'pc_yoastbr_option_enabled_cb';
      }
    }
    if ( ! function_exists( 'pc_yoastbr_option_enabled_cb' ) ) {
      function pc_yoastbr_option_enabled_cb() {
        return function_exists ( 'yoast_breadcrumb' );
      }
    }


    //replace theme breadcrumbs with yoast one
    add_filter( 'tc_breadcrumbs', 'pc_yoastbr_maybe_generate_classic_breadcrumbs' ); //backward compatibility
    if ( ! function_exists( 'pc_yoastbr_maybe_generate_classic_breadcrumbs' ) ) {
      function pc_yoastbr_maybe_generate_classic_breadcrumbs( $breadcrumbs ) {
        return CZR_utils::$inst->czr_fn_opt( 'tc_breadcrumb_yoast' ) && function_exists( 'yoast_breadcrumb' ) ? yoast_breadcrumb( $prefix = '', $suffix = '', $display = false ) : $breadcrumbs;
      }
    }

    //Breadcrumb for modern style
    add_filter( 'czr_breadcrumbs', 'pc_yoastbr_maybe_generate_breadcrumbs' );
    if ( ! function_exists( 'pc_yoastbr_maybe_generate_breadcrumbs' ) ) {
      function pc_yoastbr_maybe_generate_breadcrumbs( $breadcrumbs ) {
        return czr_fn_opt( 'tc_breadcrumb_yoast' ) && function_exists( 'yoast_breadcrumb' ) ? yoast_breadcrumb( $prefix = '<div class="col-12">', $suffix = '</div>', $display = false ) : $breadcrumbs;
      }
    }
  }


}//end class
endif;