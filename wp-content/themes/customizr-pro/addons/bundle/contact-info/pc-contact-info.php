<?php
/**
* Fires the plugin
* @package PC_pro_bundle
* @author Nicolas GUILLAUME - Rocco ALIBERTI
*/
if ( ! class_exists( 'PC_contact_info' ) ) :
class PC_contact_info {
    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;
    static $_is_plugin;

    function __construct() {

        self::$instance =& $this;

        //USEFUL CONSTANTS
        if ( ! defined( 'PC_CONTACT_INFO_DIR_NAME' ) ) { define( 'PC_CONTACT_INFO_DIR_NAME' , basename( dirname( __FILE__ ) ) ); }
        if ( ! defined( 'PC_CONTACT_INFO_BASE_URL' ) ) { define( 'PC_CONTACT_INFO_BASE_URL' , sprintf('%s/%s', TC_PRO_BUNDLE_BASE_URL, PC_CONTACT_INFO_DIR_NAME ) ); }

        self::$_is_plugin = !did_action( 'plugins_loaded' );


        //Defer the loading when theme classes are loaded so that we're sure the CZR_IS_MODERN_STYLE constant is defined.
        //but options are not cached yet
        add_action( 'czr_after_load', array( $this, 'load' ) );

    }//end of construct


    //@hook: czr_after_load
    public function load() {
        //this module exists only for the modern style
        if ( ! CZR_IS_MODERN_STYLE ) {

        }
        //following filters are defined in the Customizr theme
        //customizer
        add_filter( 'czr_add_section_map'                   , array( $this, 'pc_fn_add_contact_info_section' ) );

        //add controls to the map
        add_filter( 'czr_add_setting_control_map'           , array( $this, 'pc_fn_popul_contact_info_section_option_map' ), 20, 2 );


        //FRONT
        //add topbar contact info models to the header's children, which will register them!
        add_filter( 'czr_header_children_models'            , array( $this, 'pc_fn_add_topbar_contact_info_models_to_register' ) );


        //add file path prefix
        add_filter( 'pc_ci_prefix_file_path'                , array( $this, 'pc_ci_prefix_file_path' ) );

        //filter the template file path
        add_filter( 'czr_template_file_path'                , array( $this, 'pc_contact_info_filter_template_file_path' ), 10, 2 );

        //filter the model file path
        add_filter( 'czr_model_class_path'                  , array( $this, 'pc_contact_info_filter_model_file_path' ), 10, 2 );

    }



    //@hook : czr_header_children_model
    function pc_fn_add_topbar_contact_info_models_to_register( $children ) {
        if ( ! is_array( $children ) ) {
            $children = array();
        }

        $children = array_merge( $children, array(
            //before the WHOLE topbar row which contains: menu, tagline, socials, cart
            array(
              'id' => 'topbar_contact_info_before_navbar',
              'template' => 'header/parts/topbar_contact_info',
              'hook' => '__before_topbar_navbar_row',
              'controller' => array( $this, 'pc_fn_display_topbar_contact_info_before_topbar_navbar_row_view' )
            ),

            //in topbar navbar row, but before any other elements like: menu, tagline, socials, cart
            array(
              'id' => 'topbar_contact_info_in_navbar',
              'template' => 'header/parts/topbar_contact_info',
              'hook' => '__before_topbar_navbar_row_inner',
              'controller' => array( $this, 'pc_fn_display_topbar_contact_info_before_topbar_navbar_row_inner_view' )
            )
        ) );

        return $children;
    }






    //'topbar_contact_info_before_navbar' controller callback
    function pc_fn_display_topbar_contact_info_before_topbar_navbar_row_view() {
        //before the WHOLE topbar row which contains: menu, tagline, socials, cart
        //Allow only if the following exclude cases are not matched (the model will adjust the viewport visibility with CSS classes)
        //1. if topbar not displayed in desktop => don't allow
        if ( ! in_array( esc_attr( czr_fn_opt( 'tc_header_show_topbar' ) ), array( 'desktop', 'desktop_mobile' ) ) ) {
            return false;
        }
        //2. if contact info not displayed in desktop => don't allow
        if ( ! in_array( esc_attr( czr_fn_opt( 'tc_header_show_contact_info' ) ), array( 'desktop', 'desktop_mobile' ) ) ) {
            return false;
        }
        //3. if topbar_menu not possible we'll print the template inside the topbar navbar row (not before) => don't allow
        if ( ! czr_fn_is_registered_or_possible( 'topbar_menu' ) ) {
            return false;
        }

        //else => allow
        return true;
    }




    //'topbar_contact_info_in_navbar' controller callback
    function pc_fn_display_topbar_contact_info_before_topbar_navbar_row_inner_view() {
        //in topbar navbar row, but before any other elements like: menu, tagline, socials, cart
        //Allow only if the following exclude cases are not matched (the model will adjust the viewport visibility with CSS classes)
        $_show_topbar             = esc_attr( czr_fn_opt( 'tc_header_show_topbar' ) );
        //1. if topbar not displayed => don't allow
        if ( 'none' == $_show_topbar ) {
            return false;
        }

        $_show_topbar_ci          = esc_attr( czr_fn_opt( 'tc_header_show_contact_info' ) );

        //2. if topbar contact info not displayed
        if ( 'none' == $_show_topbar_ci ) {
            return false;
        }

        //3.if topbar menu is possible do not allow if
        //3.a. topbar not displayed in mobiles;
        //3.b. contact info not displayed in mobiles
        //( the possible contact info in topbar will be allowed before the topbar_navbar_row )
        $_show_topbar_menu        = czr_fn_is_registered_or_possible( 'topbar_menu' );
        $_show_topbar_ci          = esc_attr( czr_fn_opt( 'tc_header_show_contact_info' ) );
        if ( $_show_topbar_menu ) {
            if ( !in_array( $_show_topbar, array( 'mobile', 'desktop_mobile' ) ) )
                return false;
            if ( !in_array( $_show_topbar_ci, array( 'mobile', 'desktop_mobile' ) ) )
                return false;
        }

        //else => allow
        return true;
    }





    //@hook: czr_add_section_map
    public function pc_fn_add_contact_info_section( $_map ) {

        if ( !is_array( $_map ) ) {
            return $_map;
        }

        return array_merge( $_map, array(
            /*---------------------------------------------------------------------------------------------
            -> PANEL : HEADER
            ----------------------------------------------------------------------------------------------*/
            'contact_info_sec'     => array(
                                'title'     =>  __( 'Contact Information' , 'customizr-pro' ),
                                'priority'  =>  35,
                                'panel'     => 'tc-header-panel'
            )
        ) );
    }



    //@hook: czr_add_setting_control_map
    public function pc_fn_popul_contact_info_section_option_map( $_map, $get_default = null ) {
        if ( !is_array( $_map ) )
            return;

        $_new_map = array(
               'tc_header_show_contact_info'  =>  array(
                                  'default'       => 'none',
                                  'control'       => 'CZR_controls' ,
                                  'label'         => __( 'Display a block with your contact information in the topbar.' , 'customizr-pro' ),
                                  'section'       => 'contact_info_sec' ,
                                  'type'          => 'select' ,
                                  'choices'       => array(
                                      'none'           => __( 'Do not display', 'customizr-pro'),
                                      'desktop'        => __( 'In desktop devices', 'customizr-pro'),
                                      'mobile'         => __( 'In mobile devices', 'customizr-pro'),
                                      'desktop_mobile' => __( 'In desktop and mobile devices', 'customizr-pro')
                                  ),
                                  'priority'      => 1,
                                  'notice'    => sprintf( __('Make sure the topbar is displayed. You can control the visibility of the topbar in the %s.' , 'customizr-pro'),
                                      sprintf( '<a href="%1$s" title="%2$s">Header general design settings</a>',
                                          "javascript:wp.customize.control('tc_theme_options[tc_header_show_topbar]').focus();",
                                          __("jump to the topbar option" , 'customizr-pro')
                                      )
                                  )
                ),
                'tc_contact_info_phone'  =>  array(
                                  'default'       => '',
                                  'control'       => 'CZR_controls' ,
                                  'label'         => sprintf ( '<i class="fas fa-phone"></i> %1$s', __( 'Phone number' , 'customizr-pro' ) ),
                                  'section'       => 'contact_info_sec' ,
                                  'type'          => 'text' ,
                                  'priority'      => 5,
                ),
                'tc_contact_info_opening_hours'  =>  array(
                                  'default'       => '',
                                  'control'       => 'CZR_controls' ,
                                  'label'         => sprintf ( '<i class="fas fa-clock"></i> %1$s', __( 'Opening hours' , 'customizr-pro' ) ),
                                  'section'       => 'contact_info_sec' ,
                                  'type'          => 'text' ,
                                  'priority'      => 10,
                ),
                'tc_contact_info_email'  =>  array(
                                  'default'       => '',
                                  'control'       => 'CZR_controls' ,
                                  'label'         => sprintf ( '<i class="fas fa-envelope"></i> %1$s', __( 'E-mail' , 'customizr-pro' ) ),
                                  'section'       => 'contact_info_sec' ,
                                  'type'          => 'email' ,
                                  'sanitize_callback' => 'czr_fn_sanitize_email',
                                  'priority'      => 15,
                ),
        );

        return array_merge( $_map, $_new_map );
    }




    //This system doesn't allow child-theme override
    //@hook : czr_template_file_path
    function pc_contact_info_filter_template_file_path( $what, $template ) {
        $ci_templates = array(
          'header/parts/topbar_contact_info',
          'modules/common/contact_info'
        );

        if ( !empty( $template ) && in_array( $template, $ci_templates ) ) {
            return apply_filters( 'pc_ci_prefix_file_path',  'templates/' . $template . '.php' );
        }

        return $what;
    }



    //This system doesn't allow child-theme override
    //@hook : czr_model_class_path
    function pc_contact_info_filter_model_file_path( $what, $model_basename ) {
        $ci_models = array(
           'topbar_contact_info'  => 'header/parts/class-model-topbar_contact_info',
           'contact_info'         => 'modules/common/class-model-contact_info'
         );

        if ( !empty( $model_basename ) && array_key_exists( $model_basename, $ci_models ) ) {
            return apply_filters( 'pc_ci_prefix_file_path', 'core/front/models/' . $ci_models[ $model_basename ]  . '.php' );
        }

        return $what;
    }


    //hook: pc_ci_prefix_file_path
    function pc_ci_prefix_file_path( $file ) {
        return trailingslashit( dirname( __FILE__ ) ) . $file;
    }


} //end of class
endif;
