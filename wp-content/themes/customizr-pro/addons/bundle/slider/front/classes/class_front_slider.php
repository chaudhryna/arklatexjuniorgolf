<?php
/**
* This class is instantiated both in front end
*
*
* @package      Pro Slider
* @subpackage   classes
* @since        3.0
* @author       Nicolas GUILLAUME <nicolas@themesandco.com>, Rocco ALIBERTI <rocco@themesandco.com>
* @copyright    Copyright (c) 2015, Nicolas GUILLAUME - Rocco ALIBERTI
* @link         http://presscustomizr.com
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_front_pro_slider' ) ) :
  class TC_front_pro_slider {
    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;

    function __construct () {
      self::$instance =& $this;
      add_action( 'after_setup_theme'                  , array( $this , 'tc_set_pro_slider_hooks'), 500 );
    }



    /***************************************
    * HOOKS SETTINGS ***********************
    ****************************************/
    /**
    * hook : after_setup_theme
    */
    function tc_set_pro_slider_hooks() {
      //backward compatibility
      // filter the pre_posts_slider_args to render the current post/page slider of posts
      add_filter( 'tc_get_pre_posts_slides_args'         , array( $this, 'tc_pro_slider_force_post_slider_options') );
      //alter the posts slider query
      add_filter( 'tc_query_posts_slider_args'           , array( TC_utils_pro_slider::$instance, 'tc_pro_slider_filter_posts_by_cat'), 10, 2 );


      //CZR4
      // filter the pre_posts_slider_args to render the current post/page slider of posts
      add_filter( 'czr_get_pre_posts_slides_args'        , array( $this, 'tc_pro_slider_force_post_slider_options') );

      add_filter( 'czr_query_posts_slider_args'          , array( TC_utils_pro_slider::$instance, 'tc_pro_slider_filter_posts_by_cat'), 10, 2 );

    }




    /**
    * Filter the pre_posts_slider_args to render the current post/page slider of posts
    *
    * @param args array of post slider params
    * @return update array of post slider params
    *
    * hook: tc_get_pre_posts_slides_args
    */
    function tc_pro_slider_force_post_slider_options( $args ) {

      if ( czr_fn_is_home() )
        return $args;

      $args = array_merge( $args, TC_utils_pro_slider::$instance -> tc_pro_slider_get_post_slider_options() );

      if ( isset( $args[ 'limit' ] ) ) {
        $args[ 'posts_per_page' ] = $args[ 'limit' ];
        unset( $args['limit'] );
      }

      return $args;
    }


  }//end of class
endif;
