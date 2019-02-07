<?php
/**
* Defines filters and actions used in several templates/classes
*
*
* @package      Pro Slider
* @subpackage   classes
* @since        3.0
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>, Rocco ALIBERTI <rocco@presscustomizr.com>
* @copyright    Copyright (c) 2015, Nicolas GUILLAUME - Rocco ALIBERTI
* @link         http://presscustomizr.com
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_utils_pro_slider' ) ) :
  class TC_utils_pro_slider {
    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;
    public $addon_opt_prefix;

    function __construct () {
      self::$instance =& $this;

      $this -> addon_opt_prefix   = PC_pro_bundle::$instance -> addon_opt_prefix;//=>tc_theme_options since only used as an addon for now

      //add new settings
      add_filter( 'czr_fn_front_page_option_map'  , array( $this ,  'tc_pro_slider_update_setting_control_map'), 50 );

      //allow array type settings
      add_filter( 'tc_get_ctx_excluded_options', array( $this,   'tc_pro_slider_allow_array_settings') );

    }


    /**
    * Defines sections, settings and function of customizer and return and array
    * hook : tc_add_setting_control_map
    */
    function tc_pro_slider_update_setting_control_map( $_map ) {
      $addon_opt_prefix     = $this -> addon_opt_prefix;
      $_new_settings = array(
        //page for posts
        'tc_posts_slider_restrict_by_cat'  => array(
                'default'     => array(),
                'label'       =>  __( 'Apply a category filter to your posts slider' , 'customizr-pro'  ),
                'section'     => 'frontpage_sec',
                'control'     => 'CZR_Customize_Multipicker_Categories_Control',
                'type'        => 'czr_multiple_picker',
                'priority'    => 23,
                'notice'      => sprintf( '%1$s <a href="%2$s" target="_blank">%3$s<span style="font-size: 17px;" class="dashicons dashicons-external"></span></a>' ,
                                __( "Click inside the above field and pick post categories you want to display. No filter will be applied if empty.", 'customizr-pro'),
                                esc_url('codex.wordpress.org/Posts_Categories_SubPanel'),
                                __('Learn more about post categories in WordPress' , 'customizr-pro')
                              )
        )
      );
      return array_merge($_map , $_new_settings );
    }


    function tc_pro_slider_allow_array_settings( $settings ) {
      $settings[] = 'tc_posts_slider_restrict_by_cat';
      return $settings;
    }


    /*
    * Get the current context slider option when in posts/pages
    *
    * used on front to filter the pre post slides args ( in a callback of tc_get_pre_posts_slides_args )
    *
    * @return array of options
    */
    function tc_pro_slider_get_post_slider_options( $post_id = null ) {
      //backward compatibility
      if ( czr_fn_is_home() ) {
        return array();
      }

      if ( is_null( $post_id ) )
        $post_id = get_queried_object_id() ? get_queried_object_id() : get_the_ID() ;

      if ( ! $post_id )
        return array();

      $saved_meta  = get_post_meta( $post_id, 'post_slider_posts_key', true );

      if ( empty( $saved_meta ) ) return array();

      return $saved_meta;
    }



    /**
    * Filter the posts slider args passet to get_posts
    * @param args , params passed throughout the caller, it might contain info on the slider to show
    *
    */
    function tc_pro_slider_filter_posts_by_cat( $args ) {
      $cats = $this -> tc_get_slider_categories( $args );

      if ( is_array( $cats ) && ! empty( $cats ) ) {
        $args = array_merge( $args, array(
            'category' => $cats
          )
        );
      }

      return $args;
    }

    /*
    * Helper
    * Get the categories which will filter the slider of posts
    *
    * @param args    : the array which stores the slider's configuration
    * @return array : the array of categories
    *
    *
    */
    function tc_get_slider_categories( $args ) {
      $_cats = ( ! empty( $args ) && isset( $args['categories'] ) ) ? $args['categories'] : array();

      if ( czr_fn_is_home() ) {

        $cats =  czr_fn_opt( 'tc_posts_slider_restrict_by_cat');

      }
      else {

        $cats = $_cats;

      }

      $cats = (array)apply_filters( 'tc_posts_slider_cat_filter', $cats);

      $cats = array_filter( $cats, 'czr_fn_category_id_exists' );


      return $cats;
    }

  }//end of class
endif;
