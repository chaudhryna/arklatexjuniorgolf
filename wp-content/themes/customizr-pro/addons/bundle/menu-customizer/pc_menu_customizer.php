<?php
/**
 * Plugin Name: Menu Customizer
 * Description: Add beautiful side menu to the Customizr theme.
 * Version: 1.0.1
 * Author: PressCustomizr
 * Author URI: http://presscustomizr.com
 * License: GPLv2 or later
 */


/**
* Fires the plugin
* @package      MC
* @author Nicolas GUILLAUME - Rocco ALIBERTI
* @since 1.0
*/
if ( ! class_exists( 'PC_mc' ) ) :
class PC_mc {
    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;

    function __construct() {

        self::$instance =& $this;

        //USEFUL CONSTANTS
        if ( ! defined( 'PC_MC_DIR_NAME' ) ) { define( 'PC_MC_DIR_NAME' , basename( dirname( __FILE__ ) ) ); }

        if ( ! defined( 'PC_MC_BASE_URL' ) ) { define( 'PC_MC_BASE_URL' ,  sprintf('%s/%s', TC_PRO_BUNDLE_BASE_URL, PC_MC_DIR_NAME ) ); }

        $this -> load();
    }//end of construct


    private function load() {
      $plug_classes = array(
        'PC_utils_mc'              => array('/utils/classes/class_utils_mc.php'),
        'PC_back_mc'               => array('/back/classes/class_back_mc.php'),
        'PC_front_mc'              => array('/front/classes/class_front_mc.php')
      );//end of plug_classes array

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
          if ( $name !=  'PC_plug_updater' )
            new $name( $args );
      }
    }//fn

}//class
endif;
