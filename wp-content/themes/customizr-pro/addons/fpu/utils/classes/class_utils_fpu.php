<?php
/**
* Defines filters and actions used in several templates/classes
*
*
* @package      FPU
* @subpackage   classes
* @since        3.0
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    copyright (c) 2013-2015 Nicolas GUILLAUME
* @link         http://presscustomizr.com/extension/featured-pages-unlimited/
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

class TC_utils_fpu {
    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;
    public $default_options;
    public $options;//not used in customizer context only
    public $is_customizing;

    function __construct () {
        self::$instance =& $this;


        //Filter the default fp rendering
        add_filter( 'fpc_featured_pages_ids'                , array( $this , 'tc_get_custom_fp_nb' ) );
        add_filter( 'fpc_per_line'                          , array( $this , 'tc_get_custom_fp_nb_per_line' ) );

        //get single option
        add_filter  ( '__get_fpc_option'                    , array( $this , 'tc_fpc_get_option' ), 10, 2 );

        //some useful filters
        add_filter  ( '__ID'                                , array( $this , 'tc_get_the_ID' ));
        add_filter  ( '__is_home'                           , array( $this , 'tc_is_home' ) );
        add_filter  ( '__is_home_empty'                     , array( $this , 'tc_is_home_empty' ) );

        //default options
        $this -> is_customizing   = TC_fpu::$instance -> is_customizing;
        $this -> default_options  = $this -> tc_get_default_options();
    }




    /**
    * Return the default options array from a customizer map + add slider option
    *
    * @package FPU
    * @since FPU 1.4
    */
    function tc_get_default_options() {
        $prefix             = TC_fpu::$instance -> plug_option_prefix;
        $def_options        = get_option( "{$prefix}_default" );
        //Always update the default option when (OR) :
        // 1) they are not defined
        // 2) customzing => takes into account if user has set a filter or added a new customizer setting
        // 3) theme version not defined
        // 4) versions are different
        if ( ! $def_options || $this -> is_customizing || ! isset($def_options['ver']) || 0 != version_compare( $def_options['ver'] , TC_fpu::$instance -> plug_version ) ) {
            $def_options          = $this -> tc_generate_default_options( $this -> tc_customizer_map( $get_default_option = 'true' ) , $prefix );
            //Adds the version
            $def_options['ver']   =  TC_fpu::$instance -> plug_version;
            update_option( "{$prefix}_default" , $def_options );
        }
        return apply_filters( "{$prefix}_default", $def_options );
    }



    /**
    *
    * @package FPU
    * @since FPU 1.4.3
    */
    function tc_generate_default_options( $map, $option_group = null ) {
        //do we have to look in a specific group of option (plugin?)
        $option_group   = is_null($option_group) ? TC_fpu::$instance -> plug_option_prefix : $option_group;
        foreach ($map['add_setting_control'] as $key => $options) {
            //check it is a customizr option
            if( false !== strpos( $key  , $option_group ) ) {
                //isolate the option name between brackets [ ]
                $option_name = '';
                $option = preg_match_all( '/\[(.*?)\]/' , $key , $match );
                if ( isset( $match[1][0] ) ) {
                      $option_name = $match[1][0];
                }
                //write default option in array
                if(isset($options['default'])) {
                  $defaults[$option_name] = $options['default'];
                }
                else {
                  $defaults[$option_name] = null;
                }
            }//end if
        }//end foreach
      return $defaults;
    }



    /**
    * Returns an option from the options array of the theme.
    *
    * @package FPU
    * @since FPU 1.4
    */
    function tc_fpc_get_option( $option_name , $option_group = null ) {

        //do we have to look in a specific group of option (plugin?)
        $option_group       = is_null($option_group) ? TC_fpu::$instance -> plug_option_prefix : $option_group;
        $saved              = (array) get_option( $option_group );
        $defaults           = $this -> is_customizing ? $this -> tc_get_default_options() : $this -> default_options;
        $__options          = wp_parse_args( $saved, $defaults );
        $returned_option    = isset($__options[$option_name]) ? $__options[$option_name] : false;
        $returned_option    = apply_filters( "tc_fpc_get_opt" , $returned_option, $option_name, $option_group );
      return apply_filters( "tc_fpc_get_opt_{$option_name}" , $returned_option , $option_name , $option_group );
    }




    function tc_get_theme_config( $what = 'hook_list' ) {
        $theme_name         = TC_fpu::$instance -> tc_get_theme_name();
        $prefix             = TC_fpu::$instance -> plug_option_prefix;
        $def_options        = get_option( "{$prefix}_default" );
        //Always update the transient when (OR) :
        // 1) it is not defined
        // 2) the plugin version has changed or is not defined

        //checks if transient exists or has expired
        if ( false == get_transient( 'tc_fpu_config' ) || ! isset($def_options['ver']) || 0 != version_compare( $def_options['ver'] , TC_fpu::$instance -> plug_version ) ) {
            $config_raw          = @file_get_contents( dirname( dirname(__FILE__) ) ."/assets/config/config.json" );
            if ( $config_raw === false ) {
                  $config_raw = wp_remote_fopen( dirname( dirname(__FILE__) ) ."/assets/config/config.json" );
            }
            $config_raw     = json_decode( $config_raw , true );
            set_transient( 'tc_fpu_config' , $config_raw , 60*60*24*10 );//10 days
        } else {
            $config_raw = get_transient( 'tc_fpu_config' );
        }


        $translations =  array(
            'before_header'         => __('Before header'   , 'customizr-pro' ),
            'after_menu'            => __('After main menu'    , 'customizr-pro' ),
            'after_header'          => __('After header'    , 'customizr-pro' ),
            'before_featured'       => __('Before featured posts'   , 'customizr-pro' ),
            'after_featured'        => __('After featured posts'   , 'customizr-pro' ),
            'before_main_wrapper'   => __('Before main wrapper'   , 'customizr-pro' ),
            'after_main_wrapper'    => __('After main wrapper'   , 'customizr-pro' ),
            'before_content'        => __('Before content'  , 'customizr-pro' ),
            'before_footer'         => __('Before footer'   , 'customizr-pro' ),
            'custom_hook'           => __('Custom location' , 'customizr-pro' )
        );

        $config                 = isset($config_raw[$theme_name]) ? $config_raw[$theme_name] : false ;

        //generates the config array for the current theme or fallbacks to the default values
        $default_hook       = 'loop_start';
        $default_bgcolor    = '#fff';
        $default_textcolor  = 'inherit';
        $theme_hooks    = array(
            'loop_start'        =>      isset($translations['before_content']) ? $translations['before_content'] : 'before_content'
        );
        $menu_location  = 'primary';

        if ( $config ) {
            foreach ( $config as $setting => $data ) {
                //sets default bgcolor if exists
                switch ($setting) {
                    case 'menu' :
                        $menu_location = $data;
                    break;

                    case 'bgcolor':
                        $default_bgcolor = $data;
                    break;

                    case 'textcolor':
                        $default_textcolor = $data;
                    break;

                    case 'hooks' :
                        foreach ( $data as $hook => $position ) {
                            if ( false !== strpos($hook, '[default]') ) {
                                $hook           = str_replace('[default]', '', $hook);
                                $default_hook   = $hook;
                            }
                            $theme_hooks[$hook] = isset($translations[$position]) ? $translations[$position] : $position;
                        }//end foreach
                    break;
                }
            }//end foreach
        }//end if isset


        //add a user's defined hook option
        $theme_hooks['custom_hook'] =  isset($translations['custom_hook']) ? $translations['custom_hook'] : 'custom_hook';

        switch ($what) {
            case 'default_bgcolor':
                return apply_filters( 'fpc_default_bgcolor', $default_bgcolor );
            break;

            case 'default_textcolor':
                return apply_filters( 'fpc_default_textcolor', $default_textcolor );
            break;

            case 'default_hook':
                return apply_filters( 'fpc_default_hook', $default_hook );
            break;

            case 'hook_list':
                return apply_filters( 'fpc_theme_hooks', $theme_hooks );
            break;

            case 'menu' :
                return apply_filters( 'fpc_theme_menu', $menu_location );
            break;
        }
    }



     function tc_get_custom_fp_nb() {
        $tc_options                             = get_option( TC_fpu::$instance -> plug_option_prefix );
        $fp_nb                                  = apply_filters( 'fpc_number' , isset( $tc_options['tc_fp_number'] ) ? $tc_options['tc_fp_number'] : 3 );
        $default                                = array( 'one' , 'two' , 'three' );
        $custom_fp = array();
        for ($i = 0; $i < $fp_nb ; $i++) {
            $custom_fp[] = isset($default[$i]) ? $default[$i] : $i + 1;
        }

        return apply_filters( 'fpc_custom_number' , $custom_fp );
    }


    function tc_get_custom_fp_nb_per_line() {
        $saved      = esc_attr( tc__f( '__get_fpc_option' , 'tc_fp_number_per_line' ) );
        $fp_per_line = array(
            'one'       => 1,
            'two'       => 2,
            'three'     => 3,
            'four'      => 4,
        );
        return ( !isset($saved) || is_null($saved) ) ? 3 : $fp_per_line[$saved];
    }



    /**
    * Returns the "real" queried post ID or if !isset, get_the_ID()
    * Checks some contextual booleans
    *
    * @package FPU
    * @since FPU 1.4
    */
    function tc_get_the_ID()  {
        $queried_object   = get_queried_object();
        $tc_id            = get_post() ? get_the_ID() : null;
        $tc_id            = ( isset ($queried_object -> ID) ) ? $queried_object -> ID : $tc_id;
        return ( is_404() || is_search() || is_archive() ) ? null : $tc_id;
    }




    /**
    * Check if we are displaying posts lists or front page
    *
    * @since FPU 1.4.6
    *
    */
    function tc_is_home() {
      //get info whether the front page is a list of last posts or a page
      return ( (is_home() && ( 'posts' == get_option( 'show_on_front' ) || 'nothing' == get_option( 'show_on_front' ) ) ) || is_front_page() ) ? true : false;
    }





    /**
    * Check if we show posts or page content on home page
    *
    * @since FPU 1.4.6
    *
    */
    function tc_is_home_empty() {
      //check if the users has choosen the "no posts or page" option for home page
      return ( (is_home() || is_front_page() ) && 'nothing' == get_option( 'show_on_front' ) ) ? true : false;
    }



    /**
     * Generates the featured pages options
     *
     */
    function tc_generates_featured_pages() {
        $plug_option_prefix     = TC_fpu::$instance -> plug_option_prefix;

        $default = array(
            'dropdown'  =>  array(
                        'one'   => __( 'Home featured page one' , 'customizr-pro' ),
                        'two'   => __( 'Home featured page two' , 'customizr-pro' ),
                        'three' => __( 'Home featured page three' , 'customizr-pro' )
            ),
            'text'      => array(
                        'one'   => __( 'Featured text one (200 char. max)' , 'customizr-pro' ),
                        'two'   => __( 'Featured text two (200 char. max)' , 'customizr-pro' ),
                        'three' => __( 'Featured text three (200 char. max)' , 'customizr-pro' )
            )
        );

        //declares some loop's vars and the settings array
        $priority           = 50;
        $incr               = 0;
        $fp_setting_control = array();

        //gets the featured pages id from init
        $fp_ids             = apply_filters( 'fpc_featured_pages_ids' , TC_fpu::$instance -> fpc_ids);

        //dropdown field generator
        foreach ( $fp_ids as $id ) {
            $priority   = $priority + $incr;
            $fpc_opt    = "{$plug_option_prefix}[tc_featured_page_{$id}]";
            $fp_setting_control[$fpc_opt]       =  array(
                                        'default'       => 0,
                                        'control'       => 'TC_controls_fpu' ,
                                        'label'         => isset($default['dropdown'][$id]) ? $default['dropdown'][$id] :  sprintf( __('Custom featured page %1$s' , 'customizr-pro' ) , $id ),
                                        'section'       => 'tc_fpu' ,
                                        'type'          => 'dropdown-posts-pages' ,
                                        'priority'      => $priority
                                    );
            $incr += 10;
        }

        //text field generator
        $incr               = 10;
        foreach ( $fp_ids as $id ) {
            $priority   = $priority + $incr;
            $fpc_opt    = "{$plug_option_prefix}[tc_featured_text_{$id}]";
            $fp_setting_control[$fpc_opt]   = array(
                                        'sanitize_callback' => array( $this , 'tc_sanitize_textarea' ),
                                        'transport'     => 'postMessage',
                                        'control'       => 'TC_controls_fpu' ,
                                        'label'         => isset($default['text'][$id]) ? $default['text'][$id] : sprintf( __('Featured text %1$s (200 char. max)' , 'customizr-pro' ) , $id ),
                                        'section'       => 'tc_fpu' ,
                                        'type'          => 'textarea' ,
                                        'notice'        => __( 'You need to select a page/post first. Leave this field empty if you want to use the excerpt. You can include HTML tags.' , 'customizr-pro' ),
                                        'priority'      => $priority,
                                    );
            $incr += 10;
        }
        return $fp_setting_control;
    }

    /*
    * Since 2.0.18
    */
    function tc_generates_responsive_image_settings() {
      $plug_option_prefix     = TC_fpu::$instance -> plug_option_prefix;
      return array(
           "{$plug_option_prefix}[tc_resp_fp_img]"  =>  array(
                'default'     => 0,
                'control'     => 'TC_controls_fpu' ,
                'label'       => __( "Enable the WordPress responsive image feature for the Featured Pages images" , 'customizr-pro' ),
                'notice'      => __( 'This feature has been introduced in WordPress v4.4+ (dec-2015), and might have minor sides effects on some of your existings images. Check / uncheck this option to safely verify that your images are displayed nicely.', 'customizr-pro' ),
                'section'     => 'tc_fpu' ,
                'type'        => 'checkbox' ,
                'priority'    => 20
          )
      );
    }

    function tc_get_button_color_list() {
        $colors = array(
                'none'    =>  __( ' &#45; Select &#45; ' ,  'customizr-pro' ),
                'blue'    =>  __( 'Blue' , 'customizr-pro' ),
                'green'   =>  __( 'Green' , 'customizr-pro' ),
                'yellow'  =>  __( 'Yellow' , 'customizr-pro' ),
                'orange'  =>  __( 'Orange' , 'customizr-pro' ),
                'red'     =>  __( 'Red' , 'customizr-pro' ),
                'purple'  =>  __( 'Purple' , 'customizr-pro' ),
                'grey'    =>  __( 'Grey' , 'customizr-pro' ),
                'original' =>  __( 'Light grey' , 'customizr-pro' ),
                'black'   =>  __( 'Black' , 'customizr-pro' )
        );

        //if theme is customizr or customizr-pro add the skin "color" option
        if ( class_exists( 'TC___' ) || class_exists( 'CZR___' ) )
          $colors = array_merge( $colors, array( 'skin' => __('Theme Skin', 'customizr-pro' ) ) );

        return apply_filters( 'fpc_button_colors' , $colors );
    }



    /**
    * Defines sections, settings and function of customizer and return and array
    * Also used to get the default options array, in this case $get_default_option = true and we DISABLE the __get_option (=>infinite loop)
    */
    function tc_customizer_map( $get_default_option = false ) {
        $plug_option_prefix     = TC_fpu::$instance -> plug_option_prefix;
        //customizer option array
        $remove_section = array(
                        /*'remove_section'           =>   array(
                                                'background_image' ,
                                                'static_front_page' ,
                                                'colors'
                        )*/
        );//end of remove_sections array
        $remove_section = apply_filters( 'fpc_remove_section_map', $remove_section );

        $add_section = array(
                        'add_section'           =>   array(
                                        'tc_fpu'                            => array(
                                                                            'title'         =>  __( 'Featured Pages' , 'customizr-pro' ),
                                                                            'priority'      =>  0,
                                                                            'description'   =>  __( 'Customize your featured pages' , 'customizr-pro' )
                                        ),
                        )
        );//end of add_sections array

        //add the panel parameter if theme is customizr-pro
        $theme_name = TC_fpu::$instance ->tc_get_theme_name();
        if ( 'customizr-pro' == $theme_name ) {
          $add_section['add_section']['tc_fpu']['panel'] = 'tc-content-panel';
        }elseif ( 'hueman-pro' == $theme_name ) {
          $add_section['add_section']['tc_fpu']['panel'] = 'hu-content-panel';
        }

        $add_section = apply_filters( 'fpc_add_section_map', $add_section );

        //specifies the transport for some options
        $get_setting        = array(
                        /*'get_setting'         =>   array(
                                        'blogname' ,
                                        'blogdescription'
                        )*/
        );//end of get_setting array
        $get_setting = apply_filters( 'tc_fpc_get_setting_map', $get_setting );

        $default_bg_color = defined( 'CZR_IS_MODERN_STYLE' ) && CZR_IS_MODERN_STYLE && ! $get_default_option ? '#fff' : $this -> tc_get_theme_config( 'default_bgcolor');
        /*-----------------------------------------------------------------------------------------------------
                                                   FEATURED PAGES SETTINGS
        ------------------------------------------------------------------------------------------------------*/
        $fpc_option_map = array(

                        //Front page widget area
                        "{$plug_option_prefix}[tc_show_fp]" => array(
                                'default'       => 1,
                                'control'       => 'TC_controls_fpu' ,
                                //'title'           => __( 'Featured pages options' , 'tc_unlimited_fp' ),
                                'label'         => __( 'Display home featured pages area' , 'customizr-pro' ),
                                'section'       => 'tc_fpu' ,
                                'type'          => 'select' ,
                                'choices'       => array(
                                                1 => __( 'Enable' , 'customizr-pro' ),
                                                0 => __( 'Disable' , 'customizr-pro' ),
                                ),
                                'priority'      => 10,
                        ),

                        //hook select
                        "{$plug_option_prefix}[tc_fp_position]"         => array(
                                'default'       =>  $this -> tc_get_theme_config( 'default_hook'),
                                'control'       => 'TC_controls_fpu' ,
                                'label'         =>  __( 'Select a location' , 'customizr-pro' ),
                                'section'       =>  'tc_fpu' ,
                                'type'          =>  'select' ,
                                'choices'       =>  $get_default_option ? '' : $this -> tc_get_theme_config( 'hook_list'),
                                'priority'      => 11,
                        ),

                        //Custom hook
                        "{$plug_option_prefix}[tc_fp_custom_position]"         => array(
                                'default'       =>  '',
                                'control'       => 'TC_controls_fpu' ,
                                'label'         =>  __( 'Define a custom hook to display your featured pages' , 'customizr-pro' ),
                                'section'       =>  'tc_fpu' ,
                                'type'          =>  'text' ,
                                'notice'        => __( 'Add your custom hook in this field whithout any quotes, space or special characters (it will be stripped out) . Ex : my_custom_hook. Then in your WordPress template, simply add <?php do_action("my_custom_hook") ?> where you want to display your featured pages.' , 'customizr-pro' ),
                                'priority'      => 12,
                        ),

                        //number of featured pages
                        "{$plug_option_prefix}[tc_fp_number]" => array(
                                'default'       => 3,
                                'sanitize_callback' => array( $this , 'tc_sanitize_number' ),
                                'control'       => 'TC_controls_fpu' ,
                                'label'         => __( 'Featured pages number' , 'customizr-pro' ),
                                'section'       => 'tc_fpu' ,
                                'type'          => 'number' ,
                                'step'          => 1,
                                'min'           => 1,
                                'priority'      => 13,
                                'notice'        => __( '1) Set a number of featured pages. 2) Save and refresh the page 3) Edit your featured pages' , 'customizr-pro' ),
                                ),

                        //featured pages by line
                        "{$plug_option_prefix}[tc_fp_number_per_line]" => array(
                                'default'       => 'three',
                                'control'       => 'TC_controls_fpu' ,
                                'label'         => __( 'Featured pages layout' , 'customizr-pro' ),
                                'section'       => 'tc_fpu' ,
                                'type'          => 'select' ,
                                'choices'       => array(
                                            'one'       => __( '1 block by line' , 'customizr-pro'),
                                            'two'       => __( '2 blocks by line' , 'customizr-pro'),
                                            'three'     => __( '3 blocks by line' , 'customizr-pro'  ),
                                            'four'      => __( '4 blocks by line' , 'customizr-pro'  ),
                                ),
                                'priority'      => 14,
                        ),

                        //background color
                        "{$plug_option_prefix}[tc_fp_background]" => array(
                                'default'       => $default_bg_color,
                                'transport'     => 'postMessage' ,
                                'sanitize_callback'    => array( $this , 'tc_sanitize_hex_color' ),
                                'sanitize_js_callback' => 'maybe_hash_hex_color' ,
                                'control'       => 'TC_Color_Control' ,
                                'label'         => __( 'Background color', 'customizr-pro'),
                                'section'       => 'tc_fpu',
                                'priority'      =>  15,
                        ),

                        //enable/disable random colors
                        "{$plug_option_prefix}[tc_random_colors]" => array(
                                'default'       => 0,
                                'control'       => 'TC_controls_fpu' ,
                                'label'         => __( 'Enable random colors' , 'customizr-pro' ),
                                'section'       => 'tc_fpu' ,
                                'type'          => 'checkbox' ,
                                'notice'        => __( 'This option will apply a beautiful flat design look to your front page with random colors' , 'customizr-pro' ),
                                'priority'      => 16,
                        ),

                        //display featured page images
                        "{$plug_option_prefix}[tc_show_fp_img]" => array(
                                'default'       => 1,
                                'transport'     =>  'postMessage',
                                'control'       => 'TC_controls_fpu' ,
                                'label'         => __( 'Display thumbnails' , 'customizr-pro' ),
                                'section'       => 'tc_fpu' ,
                                'type'          => 'checkbox' ,
                                'notice'        => __( 'The images are set with the "featured image" of each pages or posts. Uncheck the option above to disable the featured images.' , 'customizr-pro' ),
                                'priority'      => 17,
                        ),

                        "{$plug_option_prefix}[tc_show_fp_img_override]" => array(
                                'default'       => 1,
                                //'transport'     =>  'postMessage',
                                'control'       => 'TC_controls_fpu' ,
                                'label'         => __( 'Override random colors' , 'customizr-pro' ),
                                'section'       => 'tc_fpu' ,
                                'type'          => 'checkbox' ,
                                'notice'        => __( 'If this option is checked and you have enable the random color option, your page/post thumbnail will be displayed instead of the random color.' , 'customizr-pro' ),
                                'priority'      => 18,
                        ),
                        "{$plug_option_prefix}[tc_thumb_shape]"  =>  array(
                                'default'       => 'rounded',
                                'control'       => 'TC_controls_fpu' ,
                                'label'         => __( "Thumbnails shape" , "customizr-pro" ),
                                'section'       => 'tc_fpu' ,
                                'type'          =>  'select' ,
                                'choices'       => array(
                                        'rounded'                   => __( 'Rounded, expand on hover' , 'customizr-pro'),
                                        'fp-rounded-expanded'       => __( 'Rounded, no expansion' , 'customizr-pro'),
                                        'fp-squared'                => __( 'Squared, expand on hover' , 'customizr-pro'),
                                        'fp-squared-expanded'       => __( 'Squared, no expansion' , 'customizr-pro')
                                ),
                                'priority'      => 19,
                                'transport'     =>  'postMessage'
                        ),
                        "{$plug_option_prefix}[tc_center_fp_img]" => array(
                                'default'       => 1,
                                //'transport'     =>  'postMessage',
                                'control'       => 'TC_controls_fpu' ,
                                'label'         => __( 'Dynamic thumbnails centering on any devices' , 'customizr-pro' ),
                                'section'       => 'tc_fpu' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 20,
                                'notice'        => __( 'This option dynamically centers your images on any devices, vertically or horizontally according to their initial aspect ratio.' , 'customizr-pro' ),
                        ),

                        //enable/disable fp titles
                        "{$plug_option_prefix}[tc_show_fp_title]" => array(
                                'default'       => 1,
                                'transport'     =>  'postMessage',
                                'control'       => 'TC_controls_fpu' ,
                                'label'         => __( 'Display titles' , 'customizr-pro' ),
                                'section'       => 'tc_fpu' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 21,
                        ),

                         //enable/disable fp text
                        "{$plug_option_prefix}[tc_show_fp_text]" => array(
                                'default'       => 1,
                                'transport'     =>  'postMessage',
                                'control'       => 'TC_controls_fpu' ,
                                'label'         => __( 'Display excerpts' , 'customizr-pro' ),
                                'section'       => 'tc_fpu' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 22,
                        ),

                        //text color
                        "{$plug_option_prefix}[tc_fp_text_color]" => array(
                                'default'       => $this -> tc_get_theme_config( 'default_textcolor'),
                                'transport'     => 'postMessage' ,
                                'sanitize_callback'    => array( $this , 'tc_sanitize_hex_color' ),
                                'sanitize_js_callback' => 'maybe_hash_hex_color' ,
                                'control'       => 'TC_Color_Control' ,
                                'label'         => __( 'Title/excerpt color', 'customizr-pro'),
                                'section'       => 'tc_fpu',
                                'priority'      =>  24,
                        ),

                         //text color
                        "{$plug_option_prefix}[tc_fp_text_color_override]" => array(
                                'default'       => 1,
                                //'transport'     =>  'postMessage',
                                'control'       => 'TC_controls_fpu' ,
                                'label'         => __( 'Override random colors' , 'customizr-pro' ),
                                'section'       => 'tc_fpu' ,
                                'type'          => 'checkbox' ,
                                'notice'        => __( 'If enabled, your custom color will override the random color.' , 'customizr-pro' ),
                                'priority'      => 26,
                        ),

                        //text color
                        "{$plug_option_prefix}[tc_fp_text_limit]" => array(
                                'default'       => 1,
                                //'transport'     =>  'postMessage',
                                'control'       => 'TC_controls_fpu' ,
                                'label'         => __( 'Limit excerpt to 200 chars.' , 'customizr-pro' ),
                                'section'       => 'tc_fpu' ,
                                'type'          => 'checkbox' ,
                                'notice'        => __( 'Uncheck this option if you want to disable the default limit of the excerpt.' , 'customizr-pro' ),
                                'priority'      => 27,
                        ),

                        //enable/disable link button
                        "{$plug_option_prefix}[tc_show_fp_button]" => array(
                                'default'       => 1,
                                'transport'     =>  'postMessage',
                                'control'       => 'TC_controls_fpu' ,
                                'label'         => __( 'Display buttons' , 'customizr-pro' ),
                                'section'       => 'tc_fpu' ,
                                'type'          => 'checkbox' ,
                                'priority'      => 28,
                        ),
                         //button text
                        "{$plug_option_prefix}[tc_fp_button_text]" => array(
                                'default'       => __( 'Read more &raquo;' , 'customizr-pro' ),
                                'transport'     =>  'postMessage',
                                'label'         => __( 'Button text' , 'customizr-pro' ),
                                'section'       => 'tc_fpu' ,
                                'type'          => 'text' ,
                                'priority'      => 30,
                        ),

                        //button color
                        "{$plug_option_prefix}[tc_fp_button_color]" => array(
                                'default'       =>  'none',
                                'transport'     =>  'postMessage',
                                'control'       => 'TC_controls_fpu' ,
                                'label'         =>  __( 'Select a button style' , 'customizr-pro' ),
                                'section'       =>  'tc_fpu' ,
                                'type'          =>  'select' ,
                                'priority'      =>  32,
                                'choices'       =>  $get_default_option ? '' : $this-> tc_get_button_color_list()
                        ),


                        //text color
                        "{$plug_option_prefix}[tc_fp_button_color_override]" => array(
                                'default'       => 1,
                                //'transport'     =>  'postMessage',
                                'control'       => 'TC_controls_fpu' ,
                                'label'         => __( 'Override random colors' , 'customizr-pro' ),
                                'section'       => 'tc_fpu' ,
                                'type'          => 'checkbox' ,
                                'notice'        => __( 'If enabled, your custom button style will override the random color.' , 'customizr-pro' ),
                                'priority'      => 34,
                        ),

                        //button text color
                        "{$plug_option_prefix}[tc_fp_button_text_color]" => array(
                                'default'       => '#fff',
                                'transport'     => 'postMessage' ,
                                'sanitize_callback'    => array( $this , 'tc_sanitize_hex_color' ),
                                'sanitize_js_callback' => 'maybe_hash_hex_color' ,
                                'control'       => 'TC_Color_Control' ,
                                'label'         => __( 'Button text color', 'customizr-pro'),
                                'section'       => 'tc_fpu',
                                'priority'      =>  36,
                        ),

        );//end of $featured_pages_option_map

        //add responsive image settings for wp >= 4.4
        if ( version_compare( $GLOBALS['wp_version'], '4.4', '>=' ) )
          $fpc_option_map = array_merge( $fpc_option_map,  $this -> tc_generates_responsive_image_settings() );

        $fpc_option_map = array_merge( $fpc_option_map , $this -> tc_generates_featured_pages() );
        $fpc_option_map = apply_filters( 'fpc_option_map', $fpc_option_map , $get_default_option );
        $add_setting_control = array(
                        'add_setting_control'   =>   $fpc_option_map
        );
        $add_setting_control = apply_filters( 'fpc_add_setting_control_map', $add_setting_control );
        //merges all customizer arrays
        $customizer_map = array_merge( $remove_section , $add_section , $get_setting , $add_setting_control );
        return apply_filters( 'fpc_customizer_map', $customizer_map );
    }//end of tc_customizer_map function



    /**
     * adds sanitization callback funtion : textarea
     */
    function tc_sanitize_textarea( $value) {
        $value = esc_html( $value);
        return $value;
    }



    /**
     * adds sanitization callback funtion : number
     */
    function tc_sanitize_number( $value) {
        $value = esc_attr( $value); // clean input
        $value = (int) $value; // Force the value into integer type.
        return ( 0 < $value ) ? $value : null;
    }



    /**
     * adds sanitization callback funtion : url
     */
    function tc_sanitize_url( $value) {
        $value = esc_url( $value);
        return $value;
    }


    /**
     * adds sanitization callback funtion : colors
     */
    function tc_sanitize_hex_color( $color ) {
        if ( $unhashed = sanitize_hex_color_no_hash( $color ) )
            return '#' . $unhashed;

        return $color;
    }

    /*** HELPERS ***/
    /**
    * Returns the url of the customizer with the current url arguments + an optional customizer section args
    *
    * @param $autofocus(optional) is an array indicating the elements to focus on ( control,section,panel).
    * Ex : array( 'control' => 'tc_front_slider', 'section' => 'frontpage_sec').
    * Wordpress will cycle among autofocus keys focusing the existing element - See wp-admin/customize.php.
    * The actual focused element depends on its type according to this priority scale: control, section, panel.
    * In this sense when specifying a control, additional section and panel could be considered as fall-back.
    *
    * @param $control_wrapper(optional) is a string indicating the wrapper to apply to the passed control. By default is "tc_theme_options".
    * Ex: passing $aufocus = array('control' => 'tc_front_slider') will produce the query arg 'autofocus'=>array('control' => 'tc_theme_options[tc_front_slider]'
    *
    * @return url string
    * @since Customizr 3.4+
    */
    static function tc_get_customizer_url( $autofocus = null, $control_wrapper = null ) {
      $_current_url       = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
      $_customize_url     = add_query_arg( 'url', urlencode( $_current_url ), wp_customize_url() );
      $autofocus  = ( ! is_array($autofocus) || empty($autofocus) ) ? null : $autofocus;
      if ( is_null($autofocus) )
        return $_customize_url;
      // $autofocus must contain at least one key among (control,section,panel)
      if ( ! count( array_intersect( array_keys($autofocus), array( 'control', 'section', 'panel') ) ) )
        return $_customize_url;

      // wrap the control in the $control_wrapper if neded
      if ( array_key_exists( 'control', $autofocus ) && ! empty( $autofocus['control'] ) ) {
        $control_wrapper = $control_wrapper ? $control_wrapper : TC_fpu::$instance -> plug_option_prefix;
        $autofocus['control'] = $control_wrapper . '[' . $autofocus['control'] . ']';
      }
      // We don't really have to care for not existent autofocus keys, wordpress will stash them when passing the values to the customize js
      return add_query_arg( array( 'autofocus' => $autofocus ), $_customize_url );
    }
}//end of class
