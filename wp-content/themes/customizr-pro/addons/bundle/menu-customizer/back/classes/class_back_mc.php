<?php
/**
* Customizer actions and filters
*
*
* @package      MC
* @subpackage   classes
* @since        1.0
* @author       Nicolas GUILLAUME <nicolas@themesandco.com>, Rocco ALIBERTI <rocco@themesandco.com>
* @copyright    Copyright (c) 2015, Nicolas GUILLAUME, Rocco ALIBERTI
*/

class PC_back_mc {

    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;
    public $addon_opt_prefix;

    function __construct () {

      self::$instance =& $this;
      $this -> addon_opt_prefix = PC_pro_bundle::$instance -> addon_opt_prefix;
      add_action ( 'customize_preview_init'             , array( $this , 'pc_customize_preview_js' ));
    }



  function pc_customize_preview_js() {
    //Temporary for the theme transition
    //depends on which Customizr version are we running
    $_preview_path_pattern      = defined( 'CZR_IS_MODERN_STYLE' ) && CZR_IS_MODERN_STYLE ? '%1$s/assets/czr/js/mc-customizer-preview%2$s.js' : '%1$s/back/assets/js/mc-customizer-preview%2$s.js';

    $_is_debug_mode             = ( defined('WP_DEBUG') && true === WP_DEBUG );

    wp_enqueue_script(
      'pc-mc-preview' ,
      sprintf( $_preview_path_pattern, PC_MC_BASE_URL, $_is_debug_mode ? '' : '.min'),
      array( 'customize-preview', 'underscore' ),
      $_is_debug_mode ? PC_pro_bundle::$instance -> plug_version : PC_pro_bundle::$instance -> plug_version . time(),
      true
    );

    //localizes
    wp_localize_script(
      'pc-mc-preview',
      'PCMCPreviewParams',
      apply_filters('pc_mc_js_preview_params' ,
        array(
          'OptionPrefix'  => PC_pro_bundle::$instance -> addon_opt_prefix,
        )
      )
    );
  }

}//end of class
