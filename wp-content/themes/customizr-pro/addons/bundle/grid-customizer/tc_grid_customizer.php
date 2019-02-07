<?php
/**
 * Plugin Name: Grid Customizr
 * Plugin URI: http://www.themesandco.com/extension/grid-customizer/
 * Description: Add beautiful effects to your blog post grid.
 * Version: 1.0.0
 * Author: ThemesandCo
 * Author URI: http://www.themesandco.com
 * License: GPLv2 or later
 */


/**
* Fires the plugin
* @package      GC
* @author Nicolas GUILLAUME - Rocco ALIBERTI
* @since 1.0
*/
if ( ! class_exists( 'TC_gc' ) ) :
class TC_gc {
    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;
    static $_is_plugin;

    public $tc_grid_masonry_size;
    public $tc_grid_masonry_fi_size;

    function __construct() {

        self::$instance =& $this;

        //USEFUL CONSTANTS
        if ( ! defined( 'TC_GC_DIR_NAME' ) ) { define( 'TC_GC_DIR_NAME' , basename( dirname( __FILE__ ) ) ); }
        if ( ! defined( 'TC_GC_BASE_URL' ) ) { define( 'TC_GC_BASE_URL' , sprintf('%s/%s', TC_PRO_BUNDLE_BASE_URL, TC_GC_DIR_NAME ) ); }

        self::$_is_plugin = !did_action( 'plugins_loaded' );


        //Defer the loading when theme classes are loaded so that we're sure the CZR_IS_MODERN_STYLE constant is defined.
        //but options are not cached yet
        add_action( 'czr_after_load', array( $this, 'load' ) );

    }//end of construct

    public function load() {

      $plug_classes = array(
        'TC_utils_gc'              => array('/utils/classes/class_utils_gc.php'),
        'TC_back_gc'               => array('/back/classes/class_back_gc.php'),
        'TC_front_gc'              => array('/front/classes/class_front_gc.php')
      );//end of plug_classes array

      //do not load front class in modern style
      if ( defined( 'CZR_IS_MODERN_STYLE' ) && CZR_IS_MODERN_STYLE )
        unset( $plug_classes[ 'TC_front_gc' ] );


      //loads and instanciates the plugin classes
      foreach ( $plug_classes as $name => $params ) {
          //don't load admin classes if not admin && not customizing
          if ( is_admin() && ! PC_pro_bundle::$instance -> is_customizing ) {
              if ( false != strpos($params[0], 'front') )
                  continue;
          }

          if ( ! is_admin() && ! PC_pro_bundle::$instance -> is_customizing ) {
              if ( false != strpos($params[0], 'back') )
                  continue;
          }

          if( ! class_exists( $name ) )
              require_once ( dirname( __FILE__ ) . $params[0] );

          $args = isset( $params[1] ) ? $params[1] : null;
          if ( $name !=  'TC_plug_updater' )
              new $name( $args );
      }

      //Only for modern style
      if ( CZR_IS_MODERN_STYLE ) {

        add_filter( 'czr_fn_post_list_option_map'   , array( $this, 'tc_new_grid_option_map' ), 50, 2 );

        //maybe register pro grids models. For this purpose we use the filter 'czr_model_map'
        //located in customizr/core/init.php CZR___::czr_fn_get_model_map(), which is fired at 'wp'

        // this is mostly needed to register models that need to do something very early,
        // e.g. add inline style like the masonry
        add_filter( 'czr_model_map'                 , array( $this, 'tc_gc_get_model_map') );

        // replace the classical grid wrapper with the grid customizer one
        // the fallback will be done ( if gc not enabled ) in the model class file
        add_filter( 'czr_prepare_model'             , array( $this, 'tc_maybe_replace_customizr_grid' ) );


        //add file path prefix
        add_filter( 'tc_gc_prefix_file_path'        , array( $this, 'tc_gc_prefix_file_path' ) );


        //filter the main content loop item so that we can add masonry and plain_excerpt templates/models
        add_filter( 'czr_main_content_loop_item'    , array( $this, 'tc_gc_set_main_content_loop_item' ) );


        //filter the template file path
        add_filter( 'czr_template_file_path'        , array( $this, 'tc_gc_filter_template_file_path' ), 10, 2 );

        //filter the model file path
        add_filter( 'czr_model_class_path'          , array( $this, 'tc_gc_filter_model_file_path' ), 10, 2 );


        //Default images sizes

        //masonry img size
        $this -> tc_grid_masonry_size       = array( 'width' => 570 , 'height' => 99999, 'crop' => false ); //size name : tc-masonry-thumb-size

        $tc_grid_masonry_size = apply_filters( 'tc_grid_masonry_size' , $this -> tc_grid_masonry_size );
        add_image_size( 'tc-masonry-thumb-size' , $tc_grid_masonry_size['width'] , $tc_grid_masonry_size['height'], $tc_grid_masonry_size['crop'] );

        //masonry for the big images (images and gallery post formats) sizes
        //10by15
        $this -> tc_grid_masonry_bi_size   = array( 'width' => 570 , 'height' => 869, 'crop' => true ); //size name : tc-masonry-bi-thumb-size

        //masonry for the big images (images and gallery post formats) sizes
        $tc_grid_masonry_bi_size           = apply_filters( 'tc_grid_masonry_big_size' , $this -> tc_grid_masonry_bi_size );
        add_image_size( 'tc-masonry-bi-thumb-size' , $tc_grid_masonry_bi_size['width'] , $tc_grid_masonry_bi_size['height'], $tc_grid_masonry_bi_size['crop'] );
      }

    }

    //hook : czr_model_map
    function tc_gc_get_model_map( $map ) {
        if ( ! is_array( $map ) )
            return $map;

        return array_merge( $map, array(

            // the masonry grid needs to be registered early as it has to access the user option style
            array(
              'id'          => 'post_list_masonry',
              'model_class' => 'content/post-lists/post_list_masonry',
            )

        ) );
    }


    // replace the classical grid wrapper with the grid customizer one
    // the fallback will be done ( if gc not enabled ) in the model class file
    // hook : czr_prepare_model
    function tc_maybe_replace_customizr_grid( $model ) {
      //replace the customizr grid with the PRO one
      if ( ( array_key_exists('model_class', $model) && 'modules/grid/grid_wrapper' == $model['model_class'] )
        || ( array_key_exists('template', $model) && 'modules/grid/grid_wrapper' == $model['template'] ) ) {
        $model[ 'template' ] = 'modules/grid/grid_wrapper';
        $model['model_class'] = array(
          'parent' => 'modules/grid/grid_wrapper',
          'name' => 'modules/grid/gc_grid_wrapper'
        );
      }
      return $model;
    }


    // filter the main content loop item so that we can add masonry and plain_excerpt templates/models
    // hook: czr_main_content_loop_item
    function tc_gc_set_main_content_loop_item( $_main_content_loop_item ) {

      if ( czr_fn_is_list_of_posts() ) {

        if ( czr_fn_is_registered_or_possible('post_list_masonry') ) {
          $_main_content_loop_item = array(
            'loop_item_tmpl'  => 'content/post-lists/post_list_masonry',
            'loop_item_model' => array()
          );
        }
        elseif ( czr_fn_is_registered_or_possible('post_list_plain_excerpt') ) {
          $_main_content_loop_item = array(
            'loop_item_tmpl'  => 'content/post-lists/post_list_plain_excerpt',
            'loop_item_model' => array(
                'model_class' => 'content/post-lists/post_list_plain',
                'model_args' => array(
                  'show_full_content' => 0
                )
            )
          );
        }

      }

      return $_main_content_loop_item;

    }


    //This system doesn't allow child-theme override
    //hook : czr_template_file_path
    function tc_gc_filter_template_file_path( $what, $template ) {
        $gc_templates = array(
          'content/post-lists/post_list_plain_excerpt',
          'content/post-lists/post_list_masonry'
        );

        if ( !empty( $template ) && in_array( $template, $gc_templates ) ) {
          return apply_filters( 'tc_gc_prefix_file_path', 'templates/' . $template . '.php' );
        }

        return $what;
    }

    //This system doesn't allow child-theme override
    //hook : czr_model_class_path
    function tc_gc_filter_model_file_path( $what, $model_basename ) {
        $gc_models = array(
          'post_list_plain_excerpt' => 'content/post-lists/class-model-post_list_plain_excerpt',
          'post_list_masonry'       => 'content/post-lists/class-model-post_list_masonry',
          'gc_grid_wrapper'         => 'modules/grid/class-model-gc_grid_wrapper'
        );
        if ( !empty( $model_basename ) && array_key_exists( $model_basename, $gc_models ) ) {
          return apply_filters( 'tc_gc_prefix_file_path', 'core/front/models/' . $gc_models[ $model_basename ] . '.php' );
        }

        return $what;
    }



    function tc_new_grid_option_map( $map ) {
      if ( array_key_exists( 'tc_post_list_grid', $map )
        && is_array( $map[ 'tc_post_list_grid' ] )
        && array_key_exists( 'choices', $map[ 'tc_post_list_grid' ]) ) {

          $map[ 'tc_post_list_grid' ][ 'choices' ] = is_array( $map[ 'tc_post_list_grid' ][ 'choices' ]  ) ? $map[ 'tc_post_list_grid' ][ 'choices' ] : array();

          $map[ 'tc_post_list_grid' ][ 'choices' ] = array_merge( $map[ 'tc_post_list_grid' ][ 'choices' ], array(
            'masonry'         => __( 'Masonry grid layout' , 'customizr-pro'), //pro
            'plain_excerpt'   => __( 'Plain excerpt layout' , 'customizr-pro'),//pro
          ) );
      }

      $_masonry_map = array(

        'tc_masonry_columns'  =>  array(
              'default'   => '3',
              'control'   => 'CZR_controls' ,
              'label'     => __( 'Masonry max number of columns' , 'customizr-pro' ),
              'section'   => 'post_lists_sec' ,
              'type'      => 'select' ,
              'choices'   => array(
                    '2'      => __( '2' , 'customizr-pro' ),
                    '3'      => __( '3' , 'customizr-pro' ),
                    '4'      => __( '4' , 'customizr-pro' )
              ),
              'notice'    => __( 'Note : columns are limited to 3 for single sidebar layouts and to 2 for double sidebar layouts.', 'customizr-pro' ),
              'priority'      => 46,
        ),

      );

      return array_merge( $map, $_masonry_map );
    }


    //hook: tc_gc_prefix_file_path
    function tc_gc_prefix_file_path( $file ) {
      return trailingslashit( dirname( __FILE__ ) ) . $file;
    }

} //end of class
endif;
