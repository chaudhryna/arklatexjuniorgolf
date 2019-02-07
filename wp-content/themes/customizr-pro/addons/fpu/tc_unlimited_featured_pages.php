<?php
/**
 * Plugin Name: Featured Pages Unlimited
 * Plugin URI: https://presscustomizr.com/extension/featured-pages-unlimited/
 * Description: Engage your visitors with beautifully featured content on home. Design live from the WordPress customizer.
 * Version: 2.1.7
 * Author: Press Customizr
 * Author URI: https://www.presscustomizr.com
 * License: GPLv2 or later
 * Text Domain: tc_unlimited_fp
 * Domain Path: /lang
 */


/**
* The tc__f() function is an extension of WP built-in apply_filters() where the $value param becomes optional.
* It is shorter than the original apply_filters() and only used on already defined filters.
* Can pass up to five variables to the filter callback.
*
* @since TCF 1.0
*/
if( ! function_exists( 'tc__f' )) :
    function tc__f ( $tag , $value = null , $arg_one = null , $arg_two = null , $arg_three = null , $arg_four = null , $arg_five = null) {
      return apply_filters( $tag , $value , $arg_one , $arg_two , $arg_three , $arg_four , $arg_five );
    }
endif;



/**
* Fires the plugin
* @package      FPU
* @author Nicolas GUILLAUME
* @since 1.0
*/
if ( ! class_exists( 'TC_fpu' ) ) :
class TC_fpu {
    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;
    public $plug_name;
    public $plug_file;
    public $plug_version;
    public $plug_prefix;

    public static $theme_version;
    public static $theme_name;
    public $plug_option_prefix;
    public $fpc_ids;
    public $fpc_size;
    public $is_customizing;

    function __construct() {

        self::$instance =& $this;

        /* LICENSE AND UPDATES */
        // the name of your product. This should match the download name in EDD exactly
        $this -> plug_name    = 'Featured Pages Unlimited';
        $this -> plug_file    = __FILE__; //main plugin root file.
        $this -> plug_prefix  = 'unlimited_fp';
        $this -> plug_version = '2.1.7';

        //gets the theme name (or parent if child)
        $tc_theme                       = wp_get_theme();
        self::$theme_name               = $this -> tc_get_theme_name( $tc_theme );


        //check if theme is customizr-pro and plugin mode (did_action not triggered yet)
        if ( in_array( self::$theme_name, array( 'customizr-pro' ) ) && ! did_action('plugins_loaded') ) {
          add_action( 'admin_notices', array( $this , 'presscustomizr_pro_admin_notice' ) );
          $this->tc_deactivate_plugin();
          return;
        }

        /* die if addon mode and previewing a different theme */
        if ( ( ! in_array( self::$theme_name, array( 'customizr-pro' ) ) ) && did_action('plugin_loaded') ) {
          return;
        }

        //USEFUL CONSTANTS
        if ( ! defined( 'TC_FPU_DIR_NAME' ) ) { define( 'TC_FPU_DIR_NAME' , basename( dirname( __FILE__ ) ) ); }
        if ( ! ( defined( 'TC_BASE_URL' ) || defined( 'HU_BASE_URL' ) ) ) {
          //plugin context
           if ( ! defined( 'TC_FPU_BASE_URL' ) ) { define( 'TC_FPU_BASE_URL' , plugins_url( TC_FPU_DIR_NAME ) ); }
        } else {
          $_theme_base_url = defined( 'TC_BASE_URL' ) ? TC_BASE_URL : HU_BASE_URL;
          //addon context
          if ( ! defined( 'TC_FPU_BASE_URL' ) ) { define( 'TC_FPU_BASE_URL' , sprintf('%s/%s' , $_theme_base_url . 'addons' , basename( dirname( __FILE__ ) ) ) ); }
        }


        //gets the version of the theme or parent if using a child theme
        self::$theme_version            = $this -> tc_get_theme_version( $tc_theme );

        //define the plug option key
        $this -> plug_option_prefix     = did_action('plugins_loaded') ? 'tc_pro_fpu' : 'tc_unlimited_featured_pages';

        //Default featured pages ids
        $this -> fpc_ids                = array( 'one' , 'two' , 'three' );
        //Default images sizes
        $this -> fpc_size               = array('width' => 270 , 'height' => 250, 'crop' => true );

        $_activation_classes = array(
          'TC_activation_key'         => array('/back/classes/activation-key/activation/class_activation_key.php', array(  $this -> plug_name , $this -> plug_prefix , $this -> plug_version )),
          'TC_plug_updater'           => array('/back/classes/activation-key/updates/class_plug_updater.php'),
          'TC_check_updates'          => array('/back/classes/activation-key/updates/class_check_updates.php', array(  $this -> plug_name , $this -> plug_prefix , $this -> plug_version, $this -> plug_file ))
        );

        $_standalone_classes = array(
          'TC_back_system_info'       => array('/back/classes/class_back_system_info.php'),
        );

        $_plug_core_classes = array(
          'TC_utils_fpu'              => array('/utils/classes/class_utils_fpu.php'),
          'TC_utils_thumb'            => array('/utils/classes/class_utils_thumbnails.php'),
          'TC_back_fpu'               => array('/back/classes/class_back_fpu.php'),
          'TC_front_fpu'              => array('/front/classes/class_front_fpu.php')
        );//end of plug_classes array


        $plug_classes       =  did_action('plugins_loaded') ? $_plug_core_classes : array_merge($_activation_classes , $_standalone_classes, $_plug_core_classes);

        //checks if is customizing : two context, admin and front (preview frame)
        $this -> is_customizing = $this -> tc_is_customizing();

        //loads and instanciates the plugin classes
        foreach ( $plug_classes as $name => $params ) {
            //don't load admin classes if not admin && not customizing
            if ( is_admin() && ! $this -> is_customizing ) {
                if ( false != strpos($params[0], 'front') )
                    continue;
            }

            if ( ! is_admin() && ! $this -> is_customizing ) {
                if ( false != strpos($params[0], 'back') )
                    continue;
            }

            if( ! class_exists( $name ) )
                require_once ( dirname( __FILE__ ) . $params[0] );

            $args = isset( $params[1] ) ? $params[1] : null;
            if ( $name !=  'TC_plug_updater' )
                new $name( $args );
        }

        //adds setup on init
        add_action( 'plugins_loaded'                    , array( $this , 'tc_setup' ) );
        //add various plugins compatibilty (Qtranslate-X, Polylang)
        add_action ( 'plugins_loaded'                   , array( $this , 'tc_fpu_plugins_compatibility'), 20 );

        ////disable front end rendering for czr4 if theme = Customizr or Customizr Pro
        add_filter( 'czr_show_fp', '__return_false' );

        //disable front end rendering if theme = Customizr or Customizr Pro old version
        add_action ( 'wp'                               , array( $this , 'tc_disable_fp_rendering'), 20 );

        //unset options if theme = Customizr or Customizr Pro
        //This has to be fired after : tc_generates_featured_pages | 10 (priority)
        add_filter ( 'tc_front_page_option_map'         , array( $this , 'tc_delete_fp_options' ), 20 );
        //Since Customizr 3.4.24 the function prefix has changed from tc to czr_fn
        add_filter ( 'czr_fn_front_page_option_map'     , array( $this , 'tc_delete_fp_options' ), 20 );

        //add / register the following actions only in plugin context
        if ( ! did_action('plugins_loaded') ) {
          //reset option on theme switch
          add_action ( 'after_switch_theme'               , array( $this , 'tc_reset_fp_options' ));

          //copy options from previous plugin version if needed
          register_activation_hook( __FILE__              , array( __CLASS__ , 'tc_move_previous_options' ) );
          //delete the hook's transient and default options
          register_activation_hook( __FILE__              , array( __CLASS__ , 'tc_clean_plugin_settings' ) );
          //writes versions
          register_activation_hook( __FILE__              , array( __CLASS__ , 'tc_write_versions' ) );
          //deactivation : delete the hook's transient and default options
          register_deactivation_hook( __FILE__            , array( __CLASS__ , 'tc_clean_plugin_settings' ) );
        } else {
          //adds setup and plugin comp when as addon in customizr-pro
          add_action( 'after_setup_theme'                 , array( $this , 'tc_setup' ), 20 );

          add_action( 'after_setup_theme'                 , array( $this , 'tc_fpu_plugins_compatibility'), 35 );
        }

    }//end of construct



    /**
    * Returns a boolean on the customizer's state
    *
    */
    function tc_is_customizing() {
          //checks if is customizing : two contexts, admin and front (preview frame)
          global $pagenow;
          $is_customizing = false;
          if ( is_admin() && isset( $pagenow ) && 'customize.php' == $pagenow ) {
            $is_customizing = true;
          } else if ( is_customize_preview() || ( ! is_admin() && isset($_REQUEST['customize_messenger_channel']) ) ) {
            $is_customizing = true;
          }
          return $is_customizing;
    }



    function tc_setup() {
        //Add image size
        $fpc_size       = apply_filters( 'fpc_size' , $this -> fpc_size );
        add_image_size( 'fpc-size' , $fpc_size['width'] , $fpc_size['height'], $fpc_size['crop'] );
        //set current theme name
        self::$theme_name = $this -> tc_get_theme_name();

        //declares the plugin translation domain
        if ( current_filter() == 'plugins_loaded' ) {
           load_plugin_textdomain( 'tc_unlimited_fp' , false, basename( dirname( __FILE__ ) ) . '/lang' );
        }
    }

    /**
    * This function handles the following plugins compatibility : Qtranslate-X, Polylang
    *
    * @package FPC
    *
    * @since FPC 1.4
    */
    function tc_fpu_plugins_compatibility() {
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        /*
        * QTranslateX
        */
        if ( is_plugin_active('qtranslate-x/qtranslate.php') ) {

        function tc_fpu_url_lang($url) {
          return ( function_exists( 'qtrans_convertURL' ) ) ? qtrans_convertURL($url) : $url;
        }
        function tc_fpu_apply_qtranslate ($text) {
          return call_user_func(  '__' , $text );
        }
        function tc_fpu_change_transport( $value , $set ) {
          return ('transport' == $set) ? 'refresh' : $value;
        }

        //outputs correct urls for current language : fp
        add_filter( 'fpc_link_url' ,    'tc_fpu_url_lang');
        //outputs the qtranslate translation for featured pages
        add_filter( 'fpc_text' ,        'tc_fpu_apply_qtranslate' );
        add_filter( 'fpc_button_text' , 'tc_fpu_apply_qtranslate' );
        add_filter( 'fpc_title'       , 'tc_fpu_apply_qtranslate' );

        /* The following is pretty useless at the moment since we should inhibit preview js code */
        //modify the customizer transport from post message to null for some options
  //      add_filter( 'tc_fp_button_text_customizer_set' ,      'tc_fpu_change_transport', 20, 2);
  //      add_filter( 'tc_featured_text_one_customizer_set' ,   'tc_fpu_change_transport', 20, 2);
  //      add_filter( 'tc_featured_text_two_customizer_set' ,   'tc_fpu_change_transport', 20, 2);
  //      add_filter( 'tc_featured_text_three_customizer_set' , 'tc_fpu_change_transport', 20, 2);
      }// end Qtranslate-X



      /*
       * Polylang
       */
      if ( is_plugin_active('polylang/polylang.php') || is_plugin_active('polylang-pro/polylang.php') ) {

        // If Polylang is active, hook function on the admin pages
        if ( function_exists( 'pll_register_string' ) )
          add_action( 'admin_init', 'pll_fpc_strings_setup' );

        function pll_fpc_strings_setup() {

          $polylang_group      = TC_fpu::$instance -> plug_name;
          $plug_option_prefix  = TC_fpu::$instance -> plug_option_prefix;

          // grab plugin options
          $pll_tc_fpu_options  = array_merge( TC_utils_fpu::$instance -> default_options, (array) get_option( $plug_option_prefix ) );

          $pll_tc_fpu_areas    = TC_utils_fpu::$instance -> tc_get_custom_fp_nb();

          // grab settings map, useful for some options labels
          $tc_fpu_settings_map = TC_utils_fpu::$instance -> tc_customizer_map( $get_default = true );
          $tc_fpu_controls_map = $tc_fpu_settings_map['add_setting_control'];

          // Add featured pages button text to Polylang's string translation panel
          if ( isset( $pll_tc_fpu_options[ 'tc_fp_button_text'] ) )
            pll_register_string( $tc_fpu_controls_map["{$plug_option_prefix}[tc_fp_button_text]"]["label"], esc_attr($pll_tc_fpu_options[ 'tc_fp_button_text']), $polylang_group );

            // Add featured pages excerpt text to Polylang's string translation panel
            foreach ( $pll_tc_fpu_areas as $area )
              if ( isset( $pll_tc_fpu_options["tc_featured_text_$area"] ) )
                pll_register_string( $tc_fpu_controls_map["{$plug_option_prefix}[tc_featured_text_$area]"]["label"], esc_attr($pll_tc_fpu_options['tc_featured_text_'.$area]), $polylang_group );

        }// end pll_fpc_strings_setup

        // Front
        // If Polylang is active, translate/swap featured page buttons/text/link and slider
        if ( function_exists( 'pll_get_post' ) && function_exists( 'pll__' ) && ! is_admin() ) {

          // Substitute any page id with the equivalent page in current language (if found)
          add_filter( 'fpc_id', 'pll_tc_page_id' );
          function pll_tc_page_id( $fp_page_id ){
            return is_int( pll_get_post( $fp_page_id ) ) ? pll_get_post( $fp_page_id ) : $fp_page_id;
          }
          // Substitute the featured page button text with the current language button text
          add_filter( 'fpc_button_text', 'pll__' );

          // Substitute the featured page text with the translated featured page text
          add_filter( 'fpc_text', 'pll__' );

        }//end Front

      }// end Polylang


      /*
       * WPML
       */
      if ( is_plugin_active('sitepress-multilingual-cms/sitepress.php') ) {
        // Since wpml looks for strings in a specific context, when we switch from FPU plugin to Customizr-Pro the new strings have to be set again ( as the FP have too )
        // Also must be considered that old FPU strings are still available in the WPML string translation panel, they have to be manually deleted.
        // Though this might be considered a goood point since users can copy the old translations and paste them in the new entries.
        //define the CONSTANT wpml context
        if ( ! defined( 'FPU_WPML_CONTEXT' ) )
          define( 'FPU_WPML_CONTEXT' ,  'customizr-pro' == TC_fpu::$theme_name && defined( 'TC_WPML_CONTEXT' ) ? TC_WPML_CONTEXT : TC_fpu::$instance -> plug_name );

        //array which binds the options to the wpml name of the translation ( see icl_register_string used in fpu_wpml_admin_setup )
        function fpu_wpml_get_options_names_config() {
          $_wp_cache_key     = 'fpu_wpml_get_options_names_config';
          $option_name_assoc = wp_cache_get( $_wp_cache_key );

          if ( false === $option_name_assoc ) {
            $option_name_assoc['tc_fp_button_text'] = 'Featured page button text';

            $wpml_tc_fpu_areas    = TC_utils_fpu::$instance -> tc_get_custom_fp_nb();
            foreach ( $wpml_tc_fpu_areas as $fpu_area )
              $option_name_assoc['tc_featured_text_' . $fpu_area] = 'Featured page text ' . $fpu_area;
            $option_name_assoc = apply_filters( 'fpu_wpml_options_names_config_pre_cache', $option_name_assoc );
            //cache this 'cause is used several times in filter callbacks
            wp_cache_set( $_wp_cache_key, $option_name_assoc );
          }
          return apply_filters( 'fpu_wpml_get_options_names_config', $option_name_assoc );
        }
        // to transpose the featured page/post id in another language we (wpml) need(s) to know the exact post type (in the future we'll have to do a similar thing for the taxonomies when we'll be able to use them as FP)
        function fpu_wpml_fpc_id( $id ) {
          if ( ! $id ) return;
          global $wpdb;
          //get_post_type retrieves the post object (will be cached), we might consider to speed up this process, if needed, with a custom query (I do not think it's needed).
          $post_type   = get_post_type( $id);
          return apply_filters( 'wpml_object_id', $id, $post_type, true );
        }

        add_action( 'admin_init', 'fpu_wpml_admin_setup' );

        function fpu_wpml_admin_setup() {
          $plug_option_prefix  = TC_fpu::$instance -> plug_option_prefix;

          // If wpml-string-translation is active perform admin pages translation
          if ( function_exists( 'icl_register_string' ) ) {
            $fpu_wpml_option_name = fpu_wpml_get_options_names_config();
            $fpu_wpml_options     = array_keys($fpu_wpml_option_name);

            // grab theme options
            $fpu_options = array_merge( TC_utils_fpu::$instance -> default_options, (array) get_option( $plug_option_prefix ) );

            // build array of options to translate

            foreach ( $fpu_wpml_options as $fpu_wpml_option )
              if ( isset( $fpu_options[$fpu_wpml_option] ) )
                icl_register_string( FPU_WPML_CONTEXT,
                  //md5 and html are stripped in wpml string table rendering, we add it for a better key
                  $fpu_wpml_option_name[$fpu_wpml_option] . ' - ' . md5($fpu_wpml_option), //name
                  esc_attr($fpu_options[$fpu_wpml_option]) //value
               );
          }//end of string based admin translation
          if ( defined( 'ICL_LANGUAGE_CODE') ) {
          //Transposing the Featured Pages available and chosed in the customizer
          //join
            add_filter( 'fpu_control_get_posts_join', 'fpu_wpml_control_get_posts_join' );
          //where
            add_filter( 'fpu_control_get_posts_where', 'fpu_wpml_control_get_posts_where', 10, 2 );
          }
          if ( TC_fpu::$instance -> is_customizing )
            // Transpose selected featured page in the Customize
            add_filter( 'option_'. $plug_option_prefix, 'fpu_wpml_customizer_options_transpose' );

          function fpu_wpml_control_get_posts_join( $join ) {
            global $wpdb;
            $join .= "INNER JOIN {$wpdb->prefix}icl_translations AS wpml_tr
                ON wpml_tr.element_id = posts.ID";

            return $join;
          }

          function fpu_wpml_control_get_posts_where( $where, $post_types ) {
            //which kind of post types we're looking for?
            $wpml_post_types = array();
            foreach ( $post_types as $post_type )
              $wpml_post_types[] = 'post_' . $post_type;
            //add wpml where clause
            $where .= sprintf(" AND wpml_tr.language_code='%s' AND wpml_tr.element_type IN ('%s')",
                ICL_LANGUAGE_CODE,
                implode("', '", array_map( 'strval', $wpml_post_types) )
            );
            return $where;
          }

          function fpu_wpml_customizer_options_transpose( $options ) {
            //cache this, might be called several times as we don't cache fpu options gets!
            $_wp_cache_key      = 'fpu_wpml_customizer_options_transpose';
            $transposed_options = wp_cache_get( $_wp_cache_key );
            if ( ! $transposed_options ) {
              // cannot use this here, infinite loop? ->             $wpml_tc_fpu_areas    = TC_utils_fpu::$instance -> tc_get_custom_fp_nb();
              foreach ( $options as $key => $value )
                if ( strstr( $key, 'tc_featured_page_') )
                  $options[$key] = fpu_wpml_fpc_id( $options[$key] );

              $transposed_options = $options;
              //set cache
              wp_cache_set( $_wp_cache_key, $transposed_options );
            }
            return $transposed_options;
          }
        }//end fpu_wpml_admin_setup
        //Front
        if ( ! is_admin() ) {
          // String transaltion binders : requires wpml icl_t function
          if ( function_exists( 'icl_t') ) {
            /*** FPU - WPML bind, wrap WPML string translator function into convenient tc functions ***/
            //define our icl_t wrapper for options filtered with tc_opt_{$option}
            if ( ! function_exists( 'fpu_wpml_t_opt' ) ) {
              function fpu_wpml_t_opt( $string ) {
                return fpu_wpml_t( $string, str_replace('tc_fpc_get_opt_', '', current_filter() ) );
              }
            }
            //define our icl_t wrapper
            if ( ! function_exists( 'fpu_wpml_t' ) ) {
              function fpu_wpml_t( $string, $opt ) {
                $tc_wpml_options_names = fpu_wpml_get_options_names_config();
                return icl_t( FPU_WPML_CONTEXT, $tc_wpml_options_names[$opt] . ' - ' . md5( $opt ), $string );
              }
            }
            /*** End FPU - WPML bind ***/
            //get the options to translate
            $fpu_wpml_options = array_keys( fpu_wpml_get_options_names_config() );

            //strings translation
            foreach ( $fpu_wpml_options as $fpu_wpml_option )
              add_filter("tc_fpc_get_opt_$fpu_wpml_option", 'fpu_wpml_t_opt', 20 );
          }

          // Featured pages ids "translation"
          add_filter( 'fpc_id', 'fpu_wpml_fpc_id', 20 );

        }//end Front
      }//end of WPML

      /*
       * Super Socializer
       * fixes #61: https://github.com/presscustomizr/tc-unlimited-featured-pages/issues/61
       * Unhook the share bar rendering on the fp the_excerpt, re-hook after
       */
      if ( is_plugin_active('super-socializer/super_socializer.php') ) {
        if ( function_exists( 'the_champ_render_sharing' ) && has_filter( 'the_excerpt', 'the_champ_render_sharing' ) ) {
          add_action( '__before_fp', 'fpu_remove_champ_render_sharing');
          add_action( '__after_fp', 'fpu_readd_champ_render_sharing' );

          if ( ! function_exists( 'fpu_remove_champ_render_sharing' ) ) {
            function fpu_remove_champ_render_sharing() {
              remove_filter( 'the_excerpt', 'the_champ_render_sharing' );
            }
          }

          if ( ! function_exists( 'fpu_readd_champ_render_sharing' ) ) {
            function fpu_readd_champ_render_sharing() {
              add_filter( 'the_excerpt', 'the_champ_render_sharing' );
            }
          }
        }
      }//end of Super Socializer

    }//end of plugin compatibility function



    /**
    * Unset Customizr theme default options for featured pages
    * Triggered if active theme is customizr or customizr-pro
    * hook : tc_front_page_option_map
    * @return void
    *
    */
    function tc_delete_fp_options( $front_page_option_map ) {
        //we can use the cached theme name 'cause we're sure we're using a customizr* theme as this method is hooked to a specific customizr* hook
        if ( ( 'customizr' != self::$theme_name && 'customizr-pro' != self::$theme_name ) || true !== apply_filters('fpc_disable_customizr_fp' , true ) )
            return $front_page_option_map;

        $to_delete = array(
            'tc_show_featured_pages',
            'tc_show_featured_pages_img',
            'tc_featured_page_button_text',
            'tc_featured_page_one',
            'tc_featured_page_two',
            'tc_featured_page_three',
            'tc_featured_text_one',
            'tc_featured_text_two',
            'tc_featured_text_three'
        );
        foreach ($front_page_option_map as $key => $value) {
            if ( in_array( $key, $to_delete) ) {
                unset($front_page_option_map[$key]);
            }
        }
        return $front_page_option_map;
    }//end of callback


    /**
    * Trigger actions if active theme is customizr or customizr-pro
    * @return void
    *
    */
    function tc_disable_fp_rendering() {
        $theme_name = $this -> tc_get_theme_name();
        if ( ( 'customizr' != $theme_name && 'customizr-pro' != $theme_name ) || true !== apply_filters('fpc_disable_customizr_fp' , true ) )
            return;

        /* is old customizr? */
        $_is_old_customizr             = $this -> tc_is_customizr_before_prefix_changes();

        if ( $_is_old_customizr ) {
          if ( class_exists('TC_featured_pages') && method_exists(TC_featured_pages::$instance , 'tc_fp_block_display') && has_action( '__before_main_container', array( TC_featured_pages::$instance , 'tc_fp_block_display') ) )
            remove_action  ( '__before_main_container'     , array( TC_featured_pages::$instance , 'tc_fp_block_display'), 10 );
        }
        else {
          if ( class_exists('CZR_featured_pages') && method_exists( CZR_featured_pages::$instance , 'czr_fn_fp_block_display') && has_action( '__before_main_container', array( CZR_featured_pages::$instance , 'czr_fn_fp_block_display') ) )
            remove_action  ( '__before_main_container'     , array( CZR_featured_pages::$instance , 'czr_fn_fp_block_display'), 10 );
        }
    }//end of callback


    function tc_is_customizr_before_prefix_changes() {
      /* is old customizr? */
      $theme_name               = TC_fpu::$instance -> tc_get_theme_name();

      if ( 'customizr' == $theme_name )
        return version_compare( CUSTOMIZR_VER, '3.4.30', '<' );
      else
        return false;

    }
    /**
    * Checks if we use a child theme.
    * @return boolean
    *
    */
    function tc_is_child( $tc_theme = null ) {
      $tc_theme       = $tc_theme ? $tc_theme : wp_get_theme();
      return ( $tc_theme -> parent() ) ? true : false;
    }



    function tc_get_theme_name( $tc_theme = null ) {
      if ( ! $tc_theme  ) {
        // $_REQUEST['theme'] is set both in live preview and when we're customizing a non active theme
        $stylesheet   = $this -> is_customizing && isset($_REQUEST['theme']) ? $_REQUEST['theme'] : '';
        $tc_theme     = wp_get_theme( $stylesheet );
      }

      $theme_name     = $this -> tc_is_child( $tc_theme ) ? $tc_theme -> parent() -> Name : $tc_theme -> Name;

      return sanitize_file_name( strtolower($theme_name) );
    }


    function tc_get_theme_version( $tc_theme = null ) {
      $tc_theme       = $tc_theme ? $tc_theme : wp_get_theme();
      //gets the version of the theme or parent if using a child theme
      return ( $tc_theme -> parent() ) ? $tc_theme -> parent() -> Version : $tc_theme -> Version;
    }

    //reset some theme related options on theme switch
    function tc_reset_fp_options() {
        $prefix = TC_fpu::$instance -> plug_option_prefix;

        $fpc_options = get_option($prefix);

        $to_reset = array(
            'tc_fp_position' ,
            'tc_fp_background',
            'tc_fp_text_color'
        );
        foreach ($to_reset as $opt) {
            if ( isset($fpc_options[$opt]) ) {
                unset($fpc_options[$opt]);
            }
        }
        update_option ( TC_fpu::$instance -> plug_option_prefix , $fpc_options );
        delete_option ( "{$prefix}_default" );
    }


    //clean default and hook transient on plugin activation/desactivation
    public static function tc_clean_plugin_settings() {
        $prefix             = TC_fpu::$instance -> plug_option_prefix;
        delete_option("{$prefix}_default");
        delete_transient( 'tc_fpu_config' );
    }



    //write current and previous version => used for system infos
    public static function tc_write_versions(){
        //Gets options
        $plug_options = get_option(TC_fpu::$instance -> plug_option_prefix);
        //Adds Upgraded From Option
        if ( isset($plug_options['tc_plugin_version']) ) {
            $plug_options['tc_upgraded_from'] = $plug_options['tc_plugin_version'];
        }
        //Sets new version
        $plug_options['tc_plugin_version'] = TC_fpu::$instance -> plug_version;
        //Updates
        update_option( TC_fpu::$instance -> plug_option_prefix , $plug_options );
    }



    //move previous options if
    //1) theme is customizr
    //2) has_copy boolean is not set or false in options
    public static function tc_move_previous_options() {
        if ( 'customizr' != self::$theme_name )
            return;

        $plug_options = get_option(TC_fpu::$instance -> plug_option_prefix);

        if ( isset($plug_options['has_moved_options']) && $plug_options['has_moved_options'] )
            return;

        $customizr_options = get_option('tc_theme_options');

        $options_key_mapping = array(//Customizr => plugin
            'tc_show_featured_pages'        => 'tc_show_fp',
            'tc_show_featured_pages_img'    => 'tc_show_fp_img',
        );

        //parse current customizr options and copy the FP options into the plug_options
        foreach ($customizr_options as $key => $value) {
            if ( false !== strpos( $key, 'tc_featured_text_') || false !== strpos( $key, 'tc_featured_page_') ) {
                $plug_options[$key] = $value;
            }//endif
            if ( isset($options_key_mapping[$key]) ) {
                //we define the new key or we keep the customizr one if not mapped
                $plug_key = isset($options_key_mapping[$key]) ? $options_key_mapping[$key] : $key;
                $plug_options[$plug_key] = $value;
            }
        }//end for each

        //add the has_moved_options boolean
        $plug_options['has_moved_options'] = true;

        //update options in db
        update_option( TC_fpu::$instance -> plug_option_prefix , $plug_options );
    }


    function tc_deactivate_plugin() {
        //In front: requires a page refresh or whatever navigation to occurr to actually see the disabling effect
        if ( ! function_exists( 'deactivate_plugins' ) ) {
          include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
          deactivate_plugins( plugin_basename( __FILE__ ) );
        }
    }


    function presscustomizr_pro_admin_notice() {
        ?>
        <div class="error">
            <p>
              <?php
              printf( __( 'The <strong>%s</strong> plugin has been disabled since it is included in this theme.' , 'customizr-pro' ),
                $this -> plug_name
              );
              ?>
            </p>
        </div>
        <?php
    }

} //end of class

//Creates a new instance of front and admin
new TC_fpu;

endif;
