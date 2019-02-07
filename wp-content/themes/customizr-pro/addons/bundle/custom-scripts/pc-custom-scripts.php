<?php
/**
* Fires the plugin
* @package PC_pro_bundle
* @author Nicolas GUILLAUME - Rocco ALIBERTI
*/
if ( ! class_exists( 'PC_custom_scripts' ) ) :
class PC_custom_scripts {
    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;
    static $_is_plugin;

    function __construct() {

        self::$instance =& $this;

        //USEFUL CONSTANTS
        if ( ! defined( 'PC_CUSTOM_SCRIPTS_DIR_NAME' ) ) { define( 'PC_CUSTOM_SCRIPTS_DIR_NAME' , basename( dirname( __FILE__ ) ) ); }
        if ( ! defined( 'PC_CUSTOM_SCRIPTS_BASE_URL' ) ) { define( 'PC_CUSTOM_SCRIPTS_BASE_URL' , sprintf('%s/%s', TC_PRO_BUNDLE_BASE_URL, PC_CUSTOM_SCRIPTS_DIR_NAME ) ); }

        self::$_is_plugin = !did_action( 'plugins_loaded' );


        //Defer the loading when theme classes are loaded so that we're sure the CZR_IS_MODERN_STYLE constant is defined.
        //but options are not cached yet
        add_action( 'czr_after_load', array( $this, 'load' ) );

    }//end of construct


    //@hook: czr_after_load
    public function load() {

        //customizer
        //modern
        add_filter( 'czr_add_section_map'         , array( $this, 'pc_fn_add_custom_scripts_section' ) );
        //classical
        add_filter( 'tc_add_section_map'          , array( $this, 'pc_fn_add_custom_scripts_section' ) );

        //add controls to the map
        //modern
        add_filter( 'czr_add_setting_control_map' , array( $this, 'pc_fn_popul_custom_scripts_section_option_map' ), 20, 2 );
        //classical
        add_filter( 'tc_add_setting_control_map'  , array( $this, 'pc_fn_popul_custom_scripts_section_option_map' ) );

        //front print scripts
        add_action( 'wp'                          , array( $this, 'pc_fn_front_hook_setup' ), 20 );
    }


    //@hook: czr_add_section_map
    public function pc_fn_add_custom_scripts_section( $map ) {

        if ( !is_array( $map ) ) {
            return $map;
        }

        return array_merge( $map, array(
            /*---------------------------------------------------------------------------------------------
            -> PANEL : ADVANCED
            ----------------------------------------------------------------------------------------------*/
            'custom_scripts_sec'     => array(
                                'title'     =>  __( 'Additional scripts' , 'customizr-pro' ),
                                'priority'  =>  15,
                                'panel'     => 'tc-advanced-panel'
            )
        ) );
    }



    //@hook: czr_add_setting_control_map
    public function pc_fn_popul_custom_scripts_section_option_map( $_map, $get_default = null ) {
        if ( !is_array( $_map ) )
            return;

        $_refresh_notice = sprintf( '%1$s%2$s',
                __( '<strong>Note:</strong>You need to click on the refresh button below to see the code applied to your site live preview.', 'customizr-pro' ),
                sprintf( '<input type="button" style="cursor:pointer; display:block" onclick="wp.customize.previewer.refresh()" title="%1$s" value="%1$s" />',
                    __( 'Refresh', 'customizr-pro' )
                )
            );

        $_new_map = array(
                'tc_custom_head_script' =>  array(
                                'control'   => 'CZR_Customize_Code_Editor_Control',
                                'label'     => __( 'Add your custom scripts to the <head> of your site' , 'customizr-pro' ),
                                'section'   => 'custom_scripts_sec' ,
                                'code_type' => 'text/html',
                                'transport' => 'postMessage', //<- to avoid the refresh while typing, also we cannot really apply this live even debouncing the refresh because, if the user didn't finish to write the code, incomplete (see unbalanced tags) scripts might break the page layout, which is always scaring for users.
                                'notice'    => sprintf( '%1$s<br/>%2$s',
                                      __( 'Any code you place here will appear in the head section of every page of your site. This is particularly useful if you need to input a tracking pixel for a state counter such as <a href="https://developers.google.com/analytics/devguides/collection/analyticsjs/" title="google-analytics" target="_blank">Google Analytics</a>', 'customizr-pro'),
                                      $_refresh_notice
                                )
                ),
                'tc_custom_footer_script' =>  array(
                                'control'   => 'CZR_Customize_Code_Editor_Control',
                                'label'     => __( 'Add your custom scripts before the </body> of your site' , 'customizr-pro' ),
                                'section'   => 'custom_scripts_sec' ,
                                'input_attrs' => array(
                                  'aria-describedby' => 'editor-keyboard-trap-help-1 editor-keyboard-trap-help-2 editor-keyboard-trap-help-3 editor-keyboard-trap-help-4',
                                ),
                                'code_type' => 'text/html',
                                //'type'      => 'textarea' ,
                                'transport' => 'postMessage', //<- to avoid the refresh while typing, also we cannot really apply this live even debouncing the refresh because, if the user didn't finish to write the code, incomplete (see unbalanced tags) scripts might break the page layout, which is always scaring for users.
                                'notice'    => sprintf( '%1$s<br/>%2$s',
                                      __( 'Any code you place here will appear at the very bottom of every page of your site, just before the closing &lt;/body&gt; tag.', 'customizr-pro'),
                                      $_refresh_notice
                                )
                ),
        );

        //Fall back on the standard textarea control if the CZR_Customize_Code_Editor_Control doesn't exists
        //e.g. wp version < 4.9
        if ( ! class_exists( 'CZR_Customize_Code_Editor_Control' ) ) {
            foreach ( $_new_map as $key => &$params ) {
                unset( $params[ 'input_attrs' ], $params[ 'code_type' ] );
                $params[ 'type' ]        = 'textarea';
                $params[ 'control' ]     = 'CZR_Controls';
                //in our base control we don't escape the html from the label
                //while the wp built-in label is escaped
                $params[ 'label' ]       = esc_html( $params['label'] );
            }
        }

        $_new_map = array_merge( $_map, $_new_map );

        return $_new_map;
    }




    //@hook: wp
    public function pc_fn_front_hook_setup() {

        add_action( 'wp_head'  , array( $this, 'pc_fn_maybe_print_custom_head_script' ), apply_filters( 'pc_custom_head_script_priority', 12 ) );
        add_action( 'wp_footer', array( $this, 'pc_fn_maybe_print_custom_footer_script' ), apply_filters( 'pc_custom_footer_script_priority', 12 ) );
    }



    //@hook: wp_head
    public function pc_fn_maybe_print_custom_head_script() {
        $custom_head_script = trim( czr_fn_opt( 'tc_custom_head_script' ) );
        if ( ! empty( $custom_head_script ) ) {
            echo $custom_head_script;
        }
    }


    //@hook: wp_footer
    public function pc_fn_maybe_print_custom_footer_script() {
        $custom_footer_script = trim( czr_fn_opt( 'tc_custom_footer_script' ) );
        if ( ! empty( $custom_footer_script ) ) {
            echo $custom_footer_script;
        }
    }


} //end of class
endif;
