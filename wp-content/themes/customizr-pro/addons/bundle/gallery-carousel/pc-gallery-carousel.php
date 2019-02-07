<?php
/**
* Fires the plugin
* @package PC_pro_bundle
* @author Nicolas GUILLAUME - Rocco ALIBERTI
*/
if ( ! class_exists( 'PC_gallery_carousel' ) ) :
class PC_gallery_carousel {
    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;
    static $_is_plugin;

    function __construct() {

        self::$instance =& $this;

        //USEFUL CONSTANTS
        if ( ! defined( 'PC_GALLERY_DIR_NAME' ) ) { define( 'PC_GALLERY_DIR_NAME' , basename( dirname( __FILE__ ) ) ); }
        if ( ! defined( 'PC_GALLERY_BASE_URL' ) ) { define( 'PC_GALLERY_BASE_URL' , sprintf('%s/%s', TC_PRO_BUNDLE_BASE_URL, PC_GALLERY_DIR_NAME ) ); }

        self::$_is_plugin = !did_action( 'plugins_loaded' );


        //Defer the loading when theme classes are loaded so that we're sure the CZR_IS_MODERN_STYLE constant is defined.
        //but options are not cached yet
        add_action( 'czr_after_load', array( $this, 'load' ) );

    }//end of construct

    public function load() {

        //Only for modern style
        if ( CZR_IS_MODERN_STYLE ) {

            //extend the gallery option map adding new specific option
            add_filter( 'czr_fn_gallery_option_map'                 , array( $this, 'pc_fn_new_galleries_option_map' ), 50, 2 );

            //in front allow the gallery carousel in post lists
            //Note: only specific post lists are able to display the carousels: Alternate and Masonry.
            //Here we just allow them to do it.
            //This filter acts on the 'czr_allow_gallery_carousel_in_post_lists' hook which will allow the carousel gallery model/template
            //to be loaded in
            //theme:core/front/models/content/common/class-model-media.php::czr_fn__setup_media()
            //This hook can be also found in:
            //theme:core/front/models/content/post-lists/class-model-post_list_alternate.php::czr_fn_is_full_image()
            //but in this case it will only allow the gallery post format to be displayed as full-image (which is a pre-condition for the gallery carousel)
            add_filter( 'czr_allow_gallery_carousel_in_post_lists'  , array( $this, 'pc_fn_maybe_allow_gallery_carousel_in_post_lists' ), 100 );

        }

    }


    //hook: czr_allow_gallery_carousel_in_post_lists
    public function pc_fn_new_galleries_option_map( $map ) {

        $_gallery_map = array(
            'tc_gallery_carousel_post_list'  =>  array(
                  'default'   => czr_fn_user_started_before_version( '4.0.10', '2.0.13' ) ? 0 : 1,
                  'title'     => __( 'Gallery Post Format', 'customizr-pro' ),
                  'control'   => 'CZR_controls' ,
                  'label'     => __( 'Display the gallery post formats as a carousel' , 'customizr-pro' ),
                  'section'   => 'galleries_sec' ,
                  'type'      => 'checkbox',
                  'notice'    =>  __( 'Note : When this option is checked, the first gallery embedded in a gallery post format is displayed as a carousel in all post lists ( blog, archives, author page, search pages, ... )', 'customizr-pro' ),
                  'priority'      => 40,
                  // 'active_callback' => 'czr_fn_is_list_of_posts',
                  'ubq_section'   => array(
                      'section' => 'post_lists_sec',
                      'priority' => '39'
                  )
            ),
        );


        return array_merge( $map, $_gallery_map );
    }




    //hook: czr_allow_gallery_carousel_in_post_lists
    public function pc_fn_maybe_allow_gallery_carousel_in_post_lists( $bool ) {
        return esc_attr( czr_fn_opt( 'tc_gallery_carousel_post_list' ) );
    }



} //end of class
endif;
