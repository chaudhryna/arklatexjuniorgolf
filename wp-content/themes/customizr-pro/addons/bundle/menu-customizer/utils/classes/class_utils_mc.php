<?php
/**
* Defines filters and actions used in several templates/classes
*
*
* @package      MC
* @subpackage   classes
* @since        3.0
* @author       Nicolas GUILLAUME <nicolas@themesandco.com>, Rocco ALIBERTI <rocco@themesandco.com>
* @copyright    Copyright (c) 2015, Nicolas GUILLAUME - Rocco ALIBERTI
* @link         http://www.themesandco.com/extension/grid-customizer/
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

class PC_utils_mc {
    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;
    public $default_options;
    public $options;//not used in customizer context only
    public $is_customizing;
    public $addon_opt_prefix;

    function __construct () {
        self::$instance =& $this;

        $this -> is_customizing     = PC_pro_bundle::$instance -> tc_is_customizing();
        $this -> addon_opt_prefix   = PC_pro_bundle::$instance -> addon_opt_prefix;

        add_filter ( 'tc_add_setting_control_map', array( $this ,  'mc_update_setting_control_map'), 50 );

        //For C4
        add_filter ( 'czr_add_setting_control_map', array( $this ,  'mc_update_setting_control_map'), 50 );
    }


    /**
    * Defines sections, settings and function of customizer and return and array
     */
    function mc_update_setting_control_map( $_map ) {

        //C4 default effect is slide on top. Older Customizr version had slide along
        $default = 'czr_add_setting_control_map' == current_filter() ? 'mc_slide_top' : 'mc_slide_along';

        $_new_settings = array(
            "tc_mc_effect"  =>  array(
                              'default'       => $default,
                              'control'       => 'CZR_controls' ,
                              'title'         => __( 'Side Menu Reveal Animation' , 'customizr-pro'),
                              'label'         => __( 'Select an animation to reveal the side menu' , 'customizr-pro' ),
                              'section'       => 'nav' ,
                              'type'          => 'select',
                              'choices'       => array(
                                      'mc_reveal'              => __( 'Reveal'            ,  'customizr-pro' ),
                                      'mc_slide_top'           => __( 'Slide on Top'      ,  'customizr-pro' ),
                                      'mc_push'                => __( 'Push'              ,  'customizr-pro' ),
                                      'mc_fall_down'           => __( 'Fall Down'         ,  'customizr-pro' ),
                                      'mc_slide_along'         => __( 'Slide Along'       ,  'customizr-pro' ),
                                      'mc_rev_slide_out'       => __( 'Reverse Slide Out' ,  'customizr-pro' ),
                                      'mc_persp_rotate_in'     => __( 'Rotate In'         ,  'customizr-pro' ),
                                      'mc_persp_rotate_out'    => __( 'Rotate Out'        ,  'customizr-pro' ),
                                      'mc_persp_scale_up'      => __( 'Scale Up'          ,  'customizr-pro' ),
                                      'mc_persp_rotate_delay'  => __( 'Delayed Rotate'    ,  'customizr-pro' ),
                              ),
                              'priority'      => 53,
                              //'notice'        =>  __( "Applies beautiful reveal effects to your side nav." , 'customizr-pro' ),
                              'transport'     => 'postMessage'
           ),
        );

        return array_merge($_map , $_new_settings );
    }
}//end of class