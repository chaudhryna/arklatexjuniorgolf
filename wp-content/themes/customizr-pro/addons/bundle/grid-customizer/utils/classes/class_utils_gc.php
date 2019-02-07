<?php
/**
* Defines filters and actions used in several templates/classes
*
*
* @package      GC
* @subpackage   classes
* @since        3.0
* @author       Nicolas GUILLAUME <nicolas@themesandco.com>, Rocco ALIBERTI <rocco@themesandco.com>
* @copyright    Copyright (c) 2015, Nicolas GUILLAUME - Rocco ALIBERTI
* @link         http://www.themesandco.com/extension/grid-customizer/
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

class TC_utils_gc {
    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;

    function __construct () {
        self::$instance =& $this;

        add_filter ( 'tc_add_setting_control_map', array( $this ,  'gc_update_setting_control_map'), 50 );
        add_filter ( 'czr_add_setting_control_map', array( $this ,  'gc_update_setting_control_map'), 50 );
    }


    function tc_get_effects_list() {
        return apply_filters( 'gc_effect' ,
            array(
                'effect-1' =>  __( 'Effect #1' , 'customizr-pro' ), //Apollo
                'effect-2' =>  __( 'Effect #2' , 'customizr-pro' ), //Goliath
                'effect-3' =>  __( 'Effect #3' , 'customizr-pro' ), //Selena
                'effect-4' =>  __( 'Effect #4' , 'customizr-pro' ), //Julia
                'effect-5' =>  __( 'Effect #5' , 'customizr-pro' ), //Steve
                'effect-6' =>  __( 'Effect #6' , 'customizr-pro' ), //Jazz
                'effect-7' =>  __( 'Effect #7' , 'customizr-pro' ), //Ming
                'effect-8' =>  __( 'Effect #8' , 'customizr-pro' ), //Lexy
                'effect-9' =>  __( 'Effect #9' , 'customizr-pro' ), //Duke
                'effect-10' =>  __( 'Effect #10' , 'customizr-pro' ), //Lily
                'effect-11' =>  __( 'Effect #11' , 'customizr-pro' ), //Sadie
                'effect-12' =>  __( 'Effect #12' , 'customizr-pro' ), //Layla
                'effect-13' =>  __( 'Effect #13' , 'customizr-pro' ), //Oscar
                'effect-14' =>  __( 'Effect #14' , 'customizr-pro' ), //Marley
                'effect-15' =>  __( 'Effect #15' , 'customizr-pro' ), //Ruby
                'effect-16' =>  __( 'Effect #16' , 'customizr-pro' ), //Milo
                'effect-17' =>  __( 'Effect #17' , 'customizr-pro' ), //Dexter
                'effect-18' =>  __( 'Effect #18' , 'customizr-pro' ), //Sarah
                'effect-19' =>  __( 'Effect #19' , 'customizr-pro' ), //Roxy
                'effect-20' =>  __( 'Effect #20' , 'customizr-pro' ), //Bubba
                'effect-21' =>  __( 'Effect #21' , 'customizr-pro' ), //Romeo
            )
        );
    }



    /**
    * Defines sections, settings and function of customizer and return and array
    * hook : tc_add_setting_control_map
     */
    function gc_update_setting_control_map( $_map ) {
        $_new_settings = array(
          "tc_gc_limit_excerpt_length"  =>  array(
                            'default'       => 1,
                            'label'         => __( 'Limit the excerpt length when the grid customizer is enabled' , 'customizr-pro' ),
                            'section'       => 'post_lists_sec',
                            'control'       => 'CZR_controls' ,
                            'type'          => 'checkbox',
                            'priority'      => 25,
                            'notice'        =>  __( "Note : bear in mind that some grid customizer effects look better when the excerpt's length is limited to only a few words." , 'customizr-pro' ),
              ),
          "tc_gc_enabled"  =>  array(
                            'default'       => 1,
                            'control'       => 'CZR_controls' ,
                            'title'         => __( 'Grid Customizer' , 'customizr-pro' ),
                            'label'         => __( 'Enable the Grid Customizer' , 'customizr-pro' ),
                            'section'       => 'post_lists_sec',
                            'type'          => 'select',
                            'choices'       => array(
                                    1   => __( 'Enable' ,  'customizr-pro' ),
                                    0    => __( 'Disable' ,  'customizr-pro' ),
                            ),
                            'priority'      => 48,
                            'notice'        =>  __( "Applies beautiful reveal effects to your posts. <strong>Note :</strong> the Grid Customizer limits the excerpt's length to ensure an optimal rendering." , 'customizr-pro' ),
           ),
           "tc_gc_effect" =>  array(
                            'default'       => 'effect-1',
                            'label'         => __( 'Select the hover effect' , 'customizr-pro' ),
                            'section'       => 'post_lists_sec',
                            'type'          => 'select' ,
                            'choices'       => $this -> tc_get_effects_list(),
                            'priority'      => 50,
                            'transport'     => 'postMessage',
                            'notice'        =>  __( "Depending on the choosen effect, you might want to adjust the title and / or the excerpt length with the options above." , 'customizr-pro' ),
            ),
            "tc_gc_random" =>  array(
                            'default'       => 'no-random',
                            'label'         => __( 'Randomize the effects' , 'customizr-pro' ),
                            'section'       => 'post_lists_sec',
                            'type'          => 'select',
                            'control'       => 'CZR_controls',
                            'choices'       => array(
                                'no-random'   => __( 'Random effect disabled' , 'customizr-pro' ),
                                'rand-global' => __( 'Same random effect to all posts' , 'customizr-pro' ),
                                'rand-each'   => __( 'Different random effects to each posts' , 'customizr-pro' )
                            ),
                            'priority'      => 51
            ),
           "tc_gc_transp_bg" =>  array(
                            'default'       => 'title-dark-bg',
                            'label'         => __( 'Background' , 'customizr-pro' ),
                            'section'       => 'post_lists_sec',
                            'control'       => 'CZR_controls' ,
                            'type'          => 'select',
                            'choices'       => array(
                                'title-dark-bg'   => __( 'Dark transparent background on titles only' , 'customizr-pro' ),
                                'title-light-bg'  => __( 'Light transparent background on titles only' , 'customizr-pro' ),
                                'dark-bg'   => __( 'Dark transparent background' , 'customizr-pro' ),
                                'light-bg'  => __( 'Light transparent background' , 'customizr-pro' ),
                                'no-bg'     => __( 'No background' , 'customizr-pro' ),
                            ),
                            'priority'      => 52,
                            'transport'     => 'postMessage'
            ),
            "tc_gc_title_location" =>  array(
                            'default'       => 'over',
                            'label'         => __( 'Title location' , 'customizr-pro' ),
                            'section'       => 'post_lists_sec',
                            'control'       => 'CZR_controls' ,
                            'type'          => 'select',
                            'choices'       => array(
                                'over'   => __( 'Over the post' , 'customizr-pro' ),
                                'below'  => __( 'Below the post' , 'customizr-pro' ),
                            ),
                            'priority'      => 53
            ),
            "tc_gc_title_color" =>  array(
                            'default'       => 'white',
                            'label'         => __( 'Title color' , 'customizr-pro' ),
                            'section'       => 'post_lists_sec',
                            'control'       => 'CZR_controls',
                            'type'          => 'select',
                            'choices'       => array(
                                'white'     => __( 'White' , 'customizr-pro' ),
                                'skin'      => __( 'Skin main color' , 'customizr-pro' ),
                                'custom'  => __( 'Custom Color' , 'customizr-pro' )
                            ),
                            'priority'      => 54,
                            'transport'     => 'postMessage'
            ),
            "tc_gc_title_custom_color" => array(
                                'default'     => method_exists( 'CZR_utils', 'czr_fn_get_skin_color' ) ? CZR_utils::$inst -> czr_fn_get_skin_color() : czr_fn_opt( 'tc_skin_color' ),
                                'control'     => 'WP_Customize_Color_Control',
                                'label'       => __( 'Title custom color' , 'customizr-pro' ),
                                'section'     => 'post_lists_sec',
                                'type'        =>  'color' ,
                                'priority'    => 55,
                                'sanitize_callback'    => array( $this, 'tc_sanitize_hex_color' ),
                                'sanitize_js_callback' => 'maybe_hash_hex_color',
                                'transport'   => 'postMessage'
            ),
            "tc_gc_title_caps" =>  array(
                            'default'       => 0,
                            'label'         => __( 'Post titles in big caps' , 'customizr-pro' ),
                            'section'       => 'post_lists_sec',
                            'control'       => 'CZR_controls' ,
                            'type'          => 'checkbox',
                            'priority'      => 56,
                            'transport'     => 'postMessage'
            ),
        );
        return array_merge($_map , $_new_settings );
    }


    /**
    * adds sanitization callback funtion : colors
    * @package Customizr
    * @since Customizr 1.1.4
    */
    function tc_sanitize_hex_color( $color ) {
      if ( $unhashed = sanitize_hex_color_no_hash( $color ) )
        return '#' . $unhashed;

      return $color;
    }
}//end of class
