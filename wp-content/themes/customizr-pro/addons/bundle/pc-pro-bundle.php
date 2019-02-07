<?php
//
//
//@todo : setup gruntfile for each addons
//
//
/**
 * Plugin Name: PC Pro Bundle
 * Plugin URI: http://presscustomizr.com
 * Description: Various Customizr-Pro addons
 * Version: 1.2.0
 * Author: Press Customizr
 * Author URI: http://presscustomizr.com
 * License: GPLv2 or later
 */


/**
* Fires the plugin
* @package      PC_pro_bundle
* @author Nicolas GUILLAUME - Rocco ALIBERTI
* @since 1.0
*/
if ( ! class_exists( 'PC_pro_bundle' ) ) :
class PC_pro_bundle {
    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;

    public $version;
    public $plug_name;
    public $plug_file;
    public $plug_version;
    public $plug_prefix;

    public static $theme;
    public static $theme_name;
    public $addon_opt_prefix;
    public $is_customizing;

    private $bundle_fire_plugin_active_notice;

    function __construct() {

        self::$instance =& $this;

        /* LICENSE AND UPDATES */
        // the name of your product. This should match the download name in EDD exactly
        $this -> plug_name    = 'Customizr Pro Bundle';
        $this -> plug_file    = __FILE__; //main plugin root file.
        $this -> plug_prefix  = 'tc_pro_bundle';
        $this -> plug_version = '1.2.0';

        //checks if is customizing : two context, admin and front (preview frame)
        $this -> is_customizing = $this -> tc_is_customizing();

        self::$theme          = $this -> tc_get_theme();
        self::$theme_name     = $this -> tc_get_theme_name();

        // check if plugin active
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        $pro_bundle_plugin_active = is_plugin_active('pro-bundle/pc-pro-bundle.php');
        //check if theme is customizr pro , plugin included
        /* REMOVE THIS IF NOT INCLUDED IN PRO YET */
        if ( 'customizr-pro' == self::$theme_name && $pro_bundle_plugin_active ) {
            $this -> bundle_fire_plugin_active_notice = true;
            add_action( 'admin_notices', array( $this , 'tc_pro_bundle_admin_notice' ) );
            return;
        }

        //check if theme is customizr* and plugin mode (did_action not triggered yet)
        //stop execution if not customizr*
        if ( false === strpos( self::$theme_name, 'customizr' ) && ! did_action('plugins_loaded') ) {
          add_action( 'admin_notices', array( $this , 'tc_pro_bundle_admin_notice' ) );
          return;
        }


        /* die if addon mode and previewing a different theme */
        if ('customizr-pro' != self::$theme_name && did_action('plugin_loaded') ) {
          return;
        }

        //USEFUL CONSTANTS
        if ( ! defined( 'TC_PRO_BUNDLE_DIR_NAME' ) ) { define( 'TC_PRO_BUNDLE_DIR_NAME' , basename( dirname( __FILE__ ) ) ); }
        if ( ! defined( 'TC_BASE_URL' ) ) {
          //plugin context
           if ( ! defined( 'TC_PRO_BUNDLE_BASE_URL' ) ) { define( 'TC_PRO_BUNDLE_BASE_URL' , plugins_url( TC_PRO_BUNDLE_DIR_NAME ) ); }
           if ( ! defined( 'TC_PRO_BUNDLE_REL_DIR' ) ) { define( 'TC_PRO_BUNDLE_REL_DIR', TC_PRO_BUNDLE_DIR_NAME ); }
        } else {
          //addon context
          if ( ! defined( 'TC_PRO_BUNDLE_BASE_URL' ) ) { define( 'TC_PRO_BUNDLE_BASE_URL' , sprintf('%s/%s' , TC_BASE_URL . 'addons' , basename( dirname( __FILE__ ) ) ) ); }
          if ( ! defined( 'TC_PRO_BUNDLE_REL_DIR' ) ) { define( 'TC_PRO_BUNDLE_REL_DIR', 'addons/'. TC_PRO_BUNDLE_DIR_NAME ); }
        }


        //gets the version of the theme or parent if using a child theme
        $this -> version               = ( self::$theme -> parent() ) ? self::$theme -> parent() -> Version : self::$theme -> Version;
        //define the plug option key
        $this -> addon_opt_prefix      = 'tc_theme_options';

        //plug-in version == theme version if as add-on
        $this -> plug_version          = did_action('plugins_loaded') ? $this -> version : $this -> plug_version;

        $plug_classes = array(
          'PC_contact_info'      => array('/contact-info/pc-contact-info.php'),
          'PC_mc'                => array('/menu-customizer/pc_menu_customizer.php'),
          'TC_gc'                => array('/grid-customizer/tc_grid_customizer.php'),
          'TC_pro_slider'        => array('/slider/tc_pro_slider.php'),
          'TC_fc'                => array('/footer-customizer/footer-customizer.php'),
          'PC_yoast_breadcrumbs' => array('/pc-yoast-breadcrumbs/pc-yoast-breadcrumbs.php'),
          'PC_init_infinite'     => array('/infinite/init-pro-infinite.php'),
          'PC_gallery_carousel'  => array('/gallery-carousel/pc-gallery-carousel.php'),
          'PC_custom_scripts'    => array('/custom-scripts/pc-custom-scripts.php')
        );//end of plug_classes array

        //loads and instanciates the plugin classes
        foreach ( $plug_classes as $name => $params ) {
            if( ! class_exists( $name ) )
              require_once ( dirname( __FILE__ ) . $params[0] );

            $args = isset( $params[1] ) ? $params[1] : null;
            new $name( $args );
        }

        //add / register the following actions only in plugin context
        if ( ! did_action('plugins_loaded') ) {
          //writes versions
          register_activation_hook( __FILE__              , array( __CLASS__ , 'tc_write_versions' ) );
        }
    }//end of construct


    /**
    * @uses  wp_get_theme() the optional stylesheet parameter value takes into account the possible preview of a theme different than the one activated
    *
    * @return  the (parent) theme object
    */
    function tc_get_theme(){
      // Return the already set theme
      if ( self::$theme )
        return self::$theme;
      // $_REQUEST['theme'] is set both in live preview and when we're customizing a non active theme
      $stylesheet = $this -> is_customizing && isset($_REQUEST['theme']) ? $_REQUEST['theme'] : '';

      //gets the theme (or parent if child)
      $tc_theme               = wp_get_theme($stylesheet);

      return $tc_theme -> parent() ? $tc_theme -> parent() : $tc_theme;

    }

    /**
    *
    * @return  the theme name
    *
    */
    function tc_get_theme_name(){
      $tc_theme = $this -> tc_get_theme();

      return sanitize_file_name( strtolower( $tc_theme -> Name ) );
    }



    //write current and previous version => used for system infos
    public static function tc_write_versions(){
      //Gets options
      $plug_options = get_option( PC_pro_bundle::$instance -> addon_opt_prefix );
      //Adds Upgraded From Option
      if ( isset($plug_options['ver']) ) {
        $plug_options['tc_upgraded_from'] = $plug_options['ver'];
      }
      //Sets new version
      $plug_options['ver'] = PC_pro_bundle::$instance -> plug_version;
      //Updates
      update_option( PC_pro_bundle::$instance -> addon_opt_prefix , $plug_options );
    }



    function tc_pro_bundle_admin_notice() {
        $what = $this -> bundle_fire_plugin_active_notice ?
            __( 'must be disabled since it is included in this theme' , 'customizr-pro' ) :
            __( 'works only with the Customizr or Customizr Pro themes', 'customizr-pro' );

        $where = '';
        global $pagenow;
        if ( ! ( is_admin() && isset( $pagenow ) && 'plugins.php' == $pagenow ) && $this -> bundle_fire_plugin_active_notice )
            $where = sprintf(__(' Open the <a href="%s">plugins page</a> to deactivate it.', 'customizr-pro'),
                         admin_url('plugins.php')
                     );

       ?>
        <div class="error">
            <p>
              <?php
              printf( __( 'The <strong>%1$s</strong> plugin %2$s.%3$s' , 'customizr-pro' ),
                $this -> plug_name,
                $what,
                $where
              );
              ?>
            </p>
        </div>
        <?php
    }


    /**
    * Returns a boolean on the customizer's state
    * @since  3.2.9
    */
    function tc_is_customizing() {
      //checks if is customizing : two contexts, admin and front (preview frame)
      global $pagenow;
      $bool = false;
      if ( is_admin() && isset( $pagenow ) && 'customize.php' == $pagenow )
        $bool = true;
      if ( is_customize_preview() || ( ! is_admin() && isset($_REQUEST['customize_messenger_channel']) ) )
        $bool = true;
      if ( $this -> tc_doing_customizer_ajax() )
        $bool = true;
      return $bool;
    }

    /**
    * Returns a boolean
    * @since  3.3.2
    */
    function tc_doing_customizer_ajax() {
      return isset( $_POST['customized'] ) && ( defined( 'DOING_AJAX' ) && DOING_AJAX );
    }

} //end of class

//Creates a new instance of front and admin
new PC_pro_bundle;

endif;
