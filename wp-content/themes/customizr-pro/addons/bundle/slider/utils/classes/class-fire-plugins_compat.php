<?php
/**
* Handles various plugins compatibilty ( Polylang, WPML, WP Ultimate Recipe )
*
* @package      Pro Slider
* @subpackage   classes
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>, Rocco Aliberti <rocco@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME - Rocco Aliberti
* @link         http://presscustomizr.com/
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_plugins_compat_pro_slider' ) ) :
  class TC_plugins_compat_pro_slider {
    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;
    //credits @Srdjan
    public $default_language, $current_language;

    function __construct () {

      self::$instance =& $this;
      // Call this after the main theme has set up its supported plugins so we can check it
      //kept commented in case we need it in the future
      //add_action ('after_setup_theme'          , array( $this , 'tc_set_plugins_supported'), 22 );
      // Call this before the main theme has executed the actually plugin compatibility code, so we can hook to its action/filter hooks
      add_action ('after_setup_theme'          , array( $this , 'tc_plugins_compatibility'), 25 );
    }//end of constructor




    /**
    * This function handles the following plugins compatibility : Polylang, WPML, WP Ultimate Recipe
    *
    * @package Pro Slider
    */
    function tc_plugins_compatibility() {
      /* Lang compatiblity codes use functions which are defined in the core theme with priority 30 */
      /* I think we need some hook in the core code like __before_core_plugins_compatibility_code __after_core_plugins_compatibility_code*/
      add_action ('after_setup_theme'          , array( $this , 'tc_lang_plugins_compatibility'), 30 );
    }





    /**
    * This function handles the following plugins compatibility : Polylang, WPML
    *
    * @package Pro Slider
    */
    function tc_lang_plugins_compatibility() {
      /*
      * tc_posts_slider_cat_filter filter hook defined in TC_utils_pro_slider::tc_get_slider_categories()
      */
      /*
      * Front Plugin Compatibility
      */
      /* Callbacks defined in the Customizr theme */
      /* Polylang */
      //Translate category ids for the filtered slider of posts by cat
      if ( function_exists( 'czr_fn_pll_translate_tax' ) )
        add_filter( 'tc_posts_slider_cat_filter', 'czr_fn_pll_translate_tax' );
      /* WPML */
      //Translate category ids for the filtered slider of posts by cat
      // in this case we remove the polylang posts filter and translate only the category
      if ( function_exists( 'czr_fn_wpml_translate_cat' ) )
        add_filter( 'tc_posts_slider_cat_filter', 'czr_fn_wpml_translate_cat' );


    }
  }//end of class
endif;
