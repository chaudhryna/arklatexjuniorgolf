<?php
/**
* Fires the plugin
* @package      PC_pro_bundle
* @author Nicolas GUILLAUME - Rocco ALIBERTI
* @since 1.0
*/
if ( ! class_exists( 'TC_pro_slider' ) ) :
class TC_pro_slider {
  static $instance;

  function __construct() {
    self::$instance =& $this;

    $this -> load();

  }//end __construct


  private function load() {
    $plug_classes = array(
      'TC_utils_pro_slider'          => array('/utils/classes/class_utils_slider.php'),
      'TC_plugins_compat_pro_slider' => array('/utils/classes/class-fire-plugins_compat.php'),
      'TC_back_pro_slider'           => array('/back/classes/class_back_slider.php'),
      'TC_front_pro_slider'          => array('/front/classes/class_front_slider.php')
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

        new $name( $args );
    }//end for
  }//fn

}//end class
endif;
