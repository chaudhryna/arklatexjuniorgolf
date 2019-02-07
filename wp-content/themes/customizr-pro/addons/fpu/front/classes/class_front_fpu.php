<?php
/**
* FRONT END CLASS
* @package  FPU
* @author Nicolas GUILLAUME
* @since 1.0
*/
class TC_front_fpu {

    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;

    public $fp_block;

    function __construct () {
        self::$instance     =& $this;

        add_action( 'template_redirect'         , array( $this , 'tc_fp_setup' ), 10 );
        add_action( 'fp_front_ready'            , array( $this , 'tc_set_fp_hook'), 10 );
        add_action( 'fp_front_ready'            , array( $this , 'tc_fp_setup_fp_block'), 10 );
        add_action( 'fp_front_ready'            , array( $this , 'tc_fp_on_ready_hook_resources'), 20 );

    }//end of construct

    //hook: template_redirect
    function tc_fp_setup() {
        if ( ! $this -> tc_show_fp() )
            return;

        /*
        * Emit the front fpu is ready
        * All the actions to set-up the fpu front are hooked to the fp_front_ready action hook
        * This makes also easier the scripts enqueing controlling
        */
        do_action( 'fp_front_ready' );
    }


    function tc_fp_on_ready_hook_resources() {
        add_action( 'wp_head'                   , array( $this , 'tc_set_colors'), 10 );
        //before Customizr-Pro theme resources
        add_action( 'wp_enqueue_scripts'        , array( $this , 'tc_enqueue_plug_resources'), 9);
    }



    function tc_set_fp_hook() {
        $hook               = apply_filters( 'tc_fp_location' , esc_attr( tc__f( '__get_fpc_option' , 'tc_fp_position' ) ) );

        switch ( $hook ) {
          case 'wp_nav_menu':
              add_filter ('wp_nav_menu'         , array($this , 'tc_fp_after_menu') , 100 , 2 );
              add_filter ('wp_page_menu'        , array($this , 'tc_fp_after_menu') , 100 , 2 );
          break;

          default:
              if ('custom_hook' == $hook ) {
                $custom_hook = esc_attr( tc__f( '__get_fpc_option' , 'tc_fp_custom_position' ) );
                $hook = !empty($custom_hook) ? $custom_hook : $hook;
              }

              add_action ( $hook           , array($this , 'tc_fp_block_display'), 10, 0 );
          break;
        }//end switch

        //Set thumb shape with customizer options (since V1.17)
        add_filter ( 'fpc_row_classes'     , array( $this , 'tc_set_thumb_shape'), 10 , 2);

        //Allow smartload when in Customizr or Customizr Pro and smartload enabled
        if ( $this -> fpc_is_img_smart_load_enabled() ) {
          add_filter ( 'tc_img_smart_load_options', array( $this, 'fpc_allow_img_smartload' ) );

          /* is old customizr? */
          $_parse_img_callback           = false;

          if ( method_exists( 'TC_utils', 'tc_parse_imgs' ) ) //old customizr before prefix changes
            $_parse_img_callback         = array( TC_utils::$instance, 'tc_parse_imgs' );

          elseif ( method_exists( 'CZR_utils', 'czr_fn_parse_imgs' ) ) //old customizr after prefix changes
            $_parse_img_callback         = array( CZR_utils::$instance, 'czr_fn_parse_imgs' );

          elseif ( function_exists( 'czr_fn_parse_imgs' ) )
            $_parse_img_callback         = 'czr_fn_parse_imgs';

          if ( !empty( $_parse_img_callback ) )
            add_filter ( 'fpu_thumb_html', $_parse_img_callback );
        }

        //Allow deeplink of the help notice to the customizer
        add_filter ( 'tc_fp_notice_customizer_url' , array( $this, 'tc_fp_notice_customizer_url' ) );

        //Add the FPU center option as localized params to Customizr
        add_filter( 'tc_customizr_script_params', array( $this, 'tc_add_center_img_param' ) );
    }


    //hook : tc_customizr_script_params
    function tc_add_center_img_param( $params = array() ) {
        $params = ! is_array( $params ) ? array() : $params;
        $params['FPUImgCentered'] = esc_attr( tc__f( '__get_fpc_option' , 'tc_center_fp_img' ) );
        return $params;
    }




    function tc_fp_setup_fp_block() {
      //in czr4 we use a different "template"
      if ( defined( 'CZR_IS_MODERN_STYLE' ) && CZR_IS_MODERN_STYLE ) {
        $this->fp_block = $this -> tc_fp_generate_block_modern();
      }
      else {
        $this->fp_block = $this -> tc_fp_generate_block();
      }
    }


    function tc_fp_block_display( $echo = true, $fp_block = '' ) {
        $fp_block           = ! $fp_block ? $this -> fp_block : $fp_block;

        if ( empty( $fp_block['html'] ) )
          return;

        $hook               = esc_attr( tc__f( '__get_fpc_option' , 'tc_fp_position' ) );

        //if the hook is loop start, we don't want to display fp in all queries.
        if ( 'loop_start' == $hook && (! is_main_query() || ! in_the_loop() ) )
            return;

        if ( !tc__f( '__is_home_empty') && !( defined( 'CZR_IS_MODERN_STYLE' ) && CZR_IS_MODERN_STYLE ) ) //no divider in the modern style
          $fp_block['html'] .= apply_filters( 'fpc_after_fp_separator', '<hr class="featurette-divider '.current_filter().'">' );

        //Return or echo
        if ( $echo )
          echo apply_filters( 'fpc_block_display' , $fp_block['html'], array_key_exists('args', $fp_block) ? $fp_block['args'] : array() );
        else
          return apply_filters( 'fpc_block_display' , $fp_block['html'], array_key_exists('args', $fp_block) ? $fp_block['args'] : array() );
    }


    /*
    * @hook fpc_row_classes
    * @since v1.17
    */
    function tc_set_thumb_shape( $classes ) {
      $_shape = esc_attr( tc__f( '__get_fpc_option' , 'tc_thumb_shape') );
      if ( false == $_shape )
        return $classes;
      return array_merge( $classes , array($_shape) );
    }




    function tc_set_colors() {
        $bg_color               = esc_attr( tc__f( '__get_fpc_option' , 'tc_fp_background' ) );
        $text_color             = esc_attr( tc__f( '__get_fpc_option' , 'tc_fp_text_color' ) );

        printf('<style id="fpc-colors" type="text/css">%1$s%2$s%3$s%4$s%5$s</style>',
            "\n\n",

            ( isset( $bg_color) && ! empty( $bg_color) ) ? sprintf( '.fpc-widget-front .round-div, .fpc-widget-front .czr-link-mask {%1$s: %2$s!important%3$s}%3$s',
                                        "\nborder-color",
                                        $bg_color,
                                        "\n"
                                    ) : '',

            ( isset( $bg_color) && ! empty( $bg_color) ) ? sprintf( '.fpc-container {%1$s: %2$s!important%3$s}%3$s',
                                        "\nbackground-color",
                                        $bg_color,
                                        "\n"
                                    ) : '',

            ( isset( $text_color) && ! empty( $text_color) ) ? sprintf( '.fpc-marketing .fpc-widget-front .fp-title, .fpc-widget-front > [class*=fp-text] {%1$s: %2$s!important%3$s}%3$s',
                                        "\ncolor",
                                        $text_color,
                                        "\n"
                                    ) : '',
            //C4 add
            ( isset( $text_color) && ! empty( $text_color) ) ? sprintf( '.fpc-marketing .fpc-widget-front .fp-title::after{%1$s: %2$s!important%3$s}%3$s',
                                        "\nbackground-color",
                                        $text_color,
                                        "\n"
                                    ) : ''
        );//end of printf
    }



    function tc_fp_after_menu( $nav_menu , $args ) {
        //enable the filter only if menu location is primary (for natives wordpress themes, can filtered for other themes )
        $args     = (array)$args;
        $location = '';
        if ( isset($args['theme_location']) ) {
          $location = $args['theme_location'];
        }
        if ( TC_utils_fpu::$instance -> tc_get_theme_config('menu') == $location )
          return $nav_menu.$this->tc_fp_block_display( $_echo = false);
        else
          return $nav_menu;
    }



    function tc_get_layout( $what = null) {
      $fp_per_row                     = apply_filters( 'fpc_per_line', 3 );
      //defines the span class
      $span_array = array(
        1 => 12,
        2 => 6,
        3 => 4,
        4 => 3,
        5 => 2,
        6 => 2,
        7 => 2
      );
      //default 4
      $span_value = ( $fp_per_row > 7) ? 1 : 4;
      $span_value = isset( $span_array[$fp_per_row] ) ? $span_array[$fp_per_row] :  $span_value;
      return ('span' == $what ) ? $span_value : array( $span_value, $fp_per_row );
    }


    /**
    * The template displaying the OLD front page featured page block.
    *
    *
    * @package FPU
    * @since FPU 1.4
    */
    function tc_fp_generate_block() {

        //gets the featured pages array and sets the fp layout
        $fp_ids                         = apply_filters( 'fpc_featured_pages_ids' , TC_fpu::$instance -> fpc_ids);
        $fp_nb                          = count($fp_ids);
        $_fp_row_classes                = implode(" " , apply_filters('fpc_row_classes' , array('fpc-row-fluid' ,'fpc-widget-area') ) );
        list($span_value, $fp_per_row)  = $this -> tc_get_layout();

        //save $args for filter
        $args                           = array($fp_ids, $fp_nb, $fp_per_row, $span_value);

        ob_start(); ?>

          <div class="fpc-container fpc-marketing">
            <?php
              do_action ('__before_fp') ;

              $row = 1;

              for ($i = 1; $i <= $fp_nb ; $i++ ) {
                    $j = ( $fp_per_row > 1 ) ? $i % $fp_per_row : $i;
                    printf('%1$s<div class="fpc-span%2$s fp-%3$s">%4$s</div>%5$s',
                        ( 1 == $j ) ? "<div class='{$_fp_row_classes}' role='complementary'>" : "",
                        apply_filters( 'fpc_span_value', $span_value, $fp_ids[ $i - 1 ]),
                        $fp_ids[$i - 1],
                        $this -> tc_fp_single_display( $fp_ids[$i - 1]),
                        ( $j == 0 || $i == $fp_nb ) ? "</div><!--/fpc-row-{$row}-->" : ''
                    );
                    if ( 0 == $j || $i == $fp_nb ) {
                        do_action( "__after_row_{$row}" );
                        $row++;
                    }
              }

              do_action ('__after_fp') ;

              //display edit link for logged in users with edit theme options
              if ( apply_filters('tc_show_fp_edit_link' , is_user_logged_in() && current_user_can( 'edit_theme_options' ) ) && ! TC_fpu::$instance->is_customizing ) {
                $_customize_url     = TC_utils_fpu::tc_get_customizer_url( array( 'section' => 'tc_fpu') );

                printf('<a class="fpc-edit-link fpc-btn fpc-btn-inverse" href="%1$s" title="%2$s" target="_blank">%2$s</a>',
                  $_customize_url,
                  esc_attr__( 'Edit The Featured Pages' , 'customizr-pro' )
                );
              }//end edit attachment condition
            ?>
          </div><!-- .fpc-container -->

       <?php
        $html = ob_get_contents();
        if ($html) ob_end_clean();

        return compact( 'html', 'args');
      }


      /**
      * The template displaying the front page featured page block.
      *
      *
      * @package FPU
      * @since FPU 1.5
      */
      function tc_fp_generate_block_modern() {

            $hook                           = esc_attr( tc__f( '__get_fpc_option' , 'tc_fp_position' ) );

            //gets the featured pages array and sets the fp layout
            $fp_ids                         = apply_filters( 'fpc_featured_pages_ids' , TC_fpu::$instance -> fpc_ids);
            $fp_nb                          = count($fp_ids);
            $_fp_row_classes                = implode(" " , apply_filters('fpc_row_classes'       , array('fpc-row-fluid' ,'fpc-widget-area') ) );
            $_fp_container_classes          = implode(" " , apply_filters('fpc_container_classes' , array_filter(
                  array(
                        1 == esc_attr( tc__f( '__get_fpc_option' , 'tc_center_fp_img' ) ) ? 'js-center-images-enabled' : 'js-center-images-disabled',
                        'fpc-container',
                        'fpc-marketing',
                        '__before_main_container' == $hook ? 'container' : ''

                  )
            )) );
            $bg_color                       = esc_attr( tc__f( '__get_fpc_option' , 'tc_fp_background' ) );

            list($span_value, $fp_per_row)  = $this -> tc_get_layout();


            //save $args for filter
            $args                           = array($fp_ids, $fp_nb, $fp_per_row, $span_value);

            ob_start(); ?>

               <div class="<?php echo $_fp_container_classes ?>" data-bgcolor="<?php echo esc_attr( strtolower($bg_color) ) ?>">
                 <?php
                   do_action ('__before_fp') ;
                  ?>
                  <div class="<?php echo $_fp_row_classes ?>" role="complementary">
                  <?php
                   $row = 1;

                   for ($i = 1; $i <= $fp_nb ; $i++ ) {
                         $j = ( $fp_per_row > 1 ) ? $i % $fp_per_row : $i;

                         printf('<div class="featured-page fpc-col-12 fpc-col-md-%1$s fp-%2$s">%3$s</div>',
                             apply_filters( 'fpc_span_value', $span_value, $fp_ids[ $i - 1 ]),
                             $fp_ids[$i - 1],
                             $this -> tc_fp_single_display_modern( $fp_ids[$i - 1])
                         );

                         if ( 0 == $j || $i == $fp_nb ) {
                             do_action( "__after_row_{$row}" ); //kept for compatibility reasons
                             $row++;
                         }
                   }

                   ?>
                   </div><!-- .fpc-row -->
                   <?php
                   do_action ('__after_fp') ;


                   //display edit link for logged in users with edit theme options
                   if ( apply_filters('tc_show_fp_edit_link' , is_user_logged_in() && current_user_can( 'edit_theme_options' ) ) && ! TC_fpu::$instance->is_customizing ) {
                     $_customize_url     = TC_utils_fpu::tc_get_customizer_url( array( 'section' => 'tc_fpu') );

                     printf('<a class="fpc-edit-link fpc-btn fpc-btn-inverse" href="%1$s" title="%2$s" target="_blank">%2$s</a>',
                       $_customize_url,
                       esc_attr__( 'Edit The Featured Pages' , 'customizr-pro' )
                     );
                   }//end edit condition
                   ?>
               </div><!-- .fpc-container -->

            <?php
            $html = ob_get_contents();
            if ($html) ob_end_clean();

            return compact( 'html', 'args');
      }

      /**
      * The template displaying one single featured page
      *
      * @package FPU
      * @since FPU 1.4
      * @param area are defined in featured-pages templates,show_img is a customizer option
      * @todo better area definition : dynamic
      */
      function tc_fp_single_display( $fp_single_id ) {
        //holder declaration
        $fp_holder_img          = apply_filters ('fp_holder_img' , '<img class="tc-holder-img" data-src="holder.js/270x250" alt="Holder Thumbnail" style="width:270px;height:250px;"/>' );
        $fp_img                 = $fp_holder_img;

        //gets boolean and general options
        //$prefix                 = TC_fpu::$instance -> plug_option_prefix;
        $tc_random_colors             = esc_attr( tc__f( '__get_fpc_option' , 'tc_random_colors') );
        $tc_show_fp_img               = esc_attr( tc__f( '__get_fpc_option' , 'tc_show_fp_img') );
        $tc_show_fp_img_override      = esc_attr( tc__f( '__get_fpc_option' , 'tc_show_fp_img_override') );
        $tc_show_fp_button            = esc_attr( tc__f( '__get_fpc_option' , 'tc_show_fp_button') );
        $tc_show_fp_title             = esc_attr( tc__f( '__get_fpc_option' , 'tc_show_fp_title') );
        $tc_show_fp_text              = esc_attr( tc__f( '__get_fpc_option' , 'tc_show_fp_text') );
        $tc_fp_text_limit             = esc_attr( tc__f( '__get_fpc_option' , 'tc_fp_text_limit') );
        $tc_fp_text_color_override    = esc_attr( tc__f( '__get_fpc_option' , 'tc_fp_text_color_override') );
        $tc_fp_button_color           = esc_attr( tc__f( '__get_fpc_option' , 'tc_fp_button_color') );
        $tc_button_text_color         = esc_attr( tc__f( '__get_fpc_option' , 'tc_fp_button_text_color') );
        $tc_fp_button_color_override  = esc_attr( tc__f( '__get_fpc_option' , 'tc_fp_button_color_override') );
        $tc_button_text               = esc_attr( tc__f( '__get_fpc_option' , 'tc_fp_button_text') );

        //random colors
        $rand_color_key         = '';
        if ( false != $tc_random_colors ) {
            $colors             = apply_filters( 'fpc_random_color_list' , array("#510300" , "#4D2A33", "#2B3F38", "#03A678" ,"#7A5945" , "#807D77" ,"#073233", "#B3858A","#F57B3D", "#449BB5", "#043D5D", "#EB5055", "#68C39F", "#1A4A72", "#4B77BE", "#5C97BF", "#F5AE30", "#EDA737", "#C8C8C8", "#13181C", "#248F79", "#D95448", "#26B89A" , "#EC6766", "#E74C3C") );
            $rand_color_key     = array_rand($colors, 1);
            $fp_img             = '<div style="background:' . $colors[$rand_color_key] . ';height: 100%;opacity: 0.7"></div>';
        }

        //if fps are not set
        if ( ! $this -> tc_fp_is_eligible($fp_single_id) ) {

            //admin link if user logged in
            $featured_page_link             = '';
            $admin_link                     = '';
            if ( ! TC_fpu::$instance->is_customizing && is_user_logged_in() && current_user_can('edit_theme_options') ) {
              $admin_link                   = sprintf( '<a href="%1$s" title="%2$s">%3$s</a>',
                TC_utils_fpu::tc_get_customizer_url( array( 'control' => 'tc_featured_text_'.$fp_single_id ) ),
                esc_attr__( 'Customizer screen' , 'customizr-pro' ),
                __( 'here' , 'customizr-pro' )
              );
              $featured_page_link           = apply_filters( 'fpc_link_url', TC_utils_fpu::tc_get_customizer_url( array( 'control' => 'tc_featured_page_'.$fp_single_id) ) );
            }

            //rendering
            $featured_page_id               =  null;
            $featured_page_title            =  apply_filters( 'fpc_title', esc_attr__( 'Featured page' , 'customizr-pro' ) );
            $text                           =  apply_filters(
                                                'fpc_text',
                                                sprintf( __( 'Featured page description text : use the page or post excerpt or set your own custom text in the WordPress customizer screen %s.' , 'customizr-pro' ),
                                                  $admin_link
                                                ),
                                                $fp_single_id,
                                                $featured_page_id
                                              );
            $fp_img                         =  apply_filters ('fpc_img_src' , $fp_img );
        }

        else {
            $featured_page_id               = apply_filters( 'fpc_id', esc_attr( tc__f( '__get_fpc_option' , 'tc_featured_page_'.$fp_single_id) ), $fp_single_id );

            //get the page/post object
            $_post                          = get_post( $featured_page_id );

            $featured_page_link             = apply_filters( 'fpc_link_url', get_permalink( $featured_page_id ), $fp_single_id );
            $featured_page_title            = apply_filters( 'fpc_title', isset( $_post->post_title ) ? strip_tags( apply_filters( 'the_title', $_post->post_title, $featured_page_id ) ) : '', $fp_single_id, $featured_page_id );

            $edit_enabled                   = false;
            //when are we displaying the edit link?
            //never display when customizing
            if ( ! TC_fpu::$instance->is_customizing ) {
              $edit_enabled                 = ( (is_user_logged_in()) && current_user_can('edit_pages') && is_page( $featured_page_id ) ) ? true : $edit_enabled;
              $edit_enabled                 = ( (is_user_logged_in()) && current_user_can('edit_post' , $featured_page_id ) && ! is_page( $featured_page_id ) ) ? true : $edit_enabled;
            }
            $edit_enabled                   = apply_filters( 'tc_edit_in_fp_title', $edit_enabled );

            $featured_text                  = apply_filters( 'fpc_text', tc__f( '__get_fpc_option' , 'tc_featured_text_'.$fp_single_id ), $fp_single_id, $featured_page_id );
            $featured_text                  = apply_filters( 'fpc_text_sanitize', html_entity_decode( $featured_text ) , $fp_single_id, $featured_page_id );

            //set page/post excerpt as default text if no $featured_text
            $text                           = ( empty($featured_text) && !post_password_required($featured_page_id) ) ? strip_tags(apply_filters( 'the_excerpt' , $_post->post_excerpt )) : $featured_text ;
            $text                           = ( empty($text) && !post_password_required($featured_page_id) ) ? strip_tags(apply_filters( 'the_content' , $_post->post_content )) : $text ;

            //limit text to 200 car
            $default_fp_text_length         = $tc_fp_text_limit ? apply_filters( 'fpc_text_length', 200 ) : 9999;
            $tc_fp_text_length = strlen($text);
            if ( $tc_fp_text_length > $default_fp_text_length ){
                /* strpos returns FALSE if the needle was not found this coudl mess up substr*/
                $end_substr = strpos( $text, ' ' , $default_fp_text_length);
                $end_substr = ( $end_substr !== FALSE ) ? $end_substr : $tc_fp_text_length;
                $text       = substr( $text , 0 , $end_substr );
                $text       = ( $end_substr == $tc_fp_text_length ) ? $text : $text . ' ...';
            }

            //set the image : uses thumbnail if any then >> the first attached image then >> a holder script
            $fp_img_size                    = apply_filters( 'fpc_img_size' , 'fpc-size' );
            $fp_img_id                      = apply_filters( 'fpc_img_id', false , $fp_single_id , $featured_page_id );

            //When do we look for images?
            //1) when random colors not enabled
            //2) when random colors enabled AND overidde boolean is true
            if ( ! $tc_random_colors || $tc_show_fp_img_override ) {
              //try to get "tc_thumb" , "tc_thumb_height" , "tc_thumb_width"
              //tc_get_thumbnail_model( $requested_size = null, $_post_id = null , $_thumb_id = null )
              $_fp_img_model = TC_utils_thumb::$instance -> tc_get_thumbnail_model( $fp_img_size, $featured_page_id, $fp_img_id );

              //finally we define a default holder if no thumbnail found or page is protected
              if ( isset( $_fp_img_model["tc_thumb"]) && ! empty( $_fp_img_model["tc_thumb"] ) && ! post_password_required( $featured_page_id ) )
                $fp_img = $_fp_img_model["tc_thumb"];
              else
                $fp_img = $fp_holder_img;
            }

            //finally we define a default holder if no thumbnail found or page is protected
            $fp_img                 = apply_filters ('fp_img_src' , $fp_img , $fp_single_id , $featured_page_id );
          }//end if

          $random_color_enabled_class = $tc_random_colors ? 'tc-random-colors-enabled' : '';

          //require holder js
          if ( $fp_holder_img == $fp_img )
            add_filter( 'tc_fp_holder_js_required', '__return_true' );

          //Let's render this
          ob_start();
          ?>

          <div class="fpc-widget-front <?php echo $random_color_enabled_class ?>">
            <?php
              $tc_fp_img_block = sprintf('<div class="thumb-wrapper %1$s %2$s">%3$s%4$s</div>',
                   ( $fp_img == $fp_holder_img ) ? 'tc-holder' : '',
                   $tc_show_fp_img ? '' : 'fpc-hide',
                   apply_filters('fpc_round_div' , sprintf('<a class="round-div" href="%1$s" title="%2$s"></a>',
                                                    $featured_page_link,
                                                    esc_attr( $featured_page_title )
                                                  ) ,
                                $fp_single_id ),
                   $fp_img
              );
              echo apply_filters( 'fpc_img_block' , $tc_fp_img_block , $fp_single_id );


              //When do we show the random colors?
              //1) if random is set
              //2) no override
              $apply_random_to_text = isset($colors[$rand_color_key]) ? true : false;
              $apply_random_to_text = $tc_fp_text_color_override ? false : $apply_random_to_text;

              //title block
              $tc_fp_title_block  = sprintf('<%1$s %2$s class="fp-title %3$s %4$s">%5$s %6$s</%1$s>',
                                    apply_filters( 'fpc_title_tag' , 'h2' ),
                                    $apply_random_to_text ? 'style="color:'.$colors[$rand_color_key].'!important"' : '',
                                    $tc_show_fp_title ? '' : 'fpc-hide',
                                    $tc_fp_text_color_override ? 'text-random-override' : '',
                                    $featured_page_title,
                                    ( isset($edit_enabled) && $edit_enabled )? sprintf('<span class="edit-link fpc-btn fpc-btn-inverse btn btn-inverse btn-mini"><a class="post-edit-link" href="%1$s" title="%2$s" target="_blank">%2$s</a></span>',
                                              get_edit_post_link( $featured_page_id ),
                                              esc_attr__( 'Edit' , 'customizr-pro' )
                                              ) : ''
              );
              echo apply_filters( 'fpc_title_block' , $tc_fp_title_block , $featured_page_title );

              //text block
              $tc_fp_text_block   = sprintf('<p class="fp-excerpt fp-text-%1$s %2$s %3$s" %4$s>%5$s</p>',
                                    $fp_single_id,
                                    $tc_show_fp_text ? '' : 'fpc-hide',
                                    $tc_fp_text_color_override ? 'text-random-override' : '',
                                    $apply_random_to_text ? 'style="color:'.$colors[$rand_color_key].'!important"' : '',
                                    $text
              );
              echo apply_filters( 'fpc_text_block' , $tc_fp_text_block , $fp_single_id , $text);

              //BUTTON BLOCK
              //When do we show the random colors?
              //1) if random is set and no button style
              //2) random + button style + no override
              $apply_random_to_btn = isset($colors[$rand_color_key]) ? true : false;
              //$apply_random_to_btn = ('none' == $tc_fp_button_color ) ? $apply_random_to_btn : false;
              $apply_random_to_btn = ('none' != $tc_fp_button_color && $tc_fp_button_color_override ) ? false : $apply_random_to_btn;


              $fpc_button_color = ! $apply_random_to_btn ? $tc_fp_button_color : '';
              $fpc_button_class = 'fpc-btn fpc-btn-primary';

              if ( 'skin' == $fpc_button_color ) {
                if ( class_exists( 'TC___' ) || class_exists( 'CZR___' ) )
                  $fpc_button_class    = 'btn btn-primary';
                else
                  $fpc_button_color    = 'none';
              }

              if ($apply_random_to_btn) {
                  $btn_style = sprintf('style="background-color:%1$s;border-color:%2$s;color:%3$s"',
                          $colors[$rand_color_key],
                          $colors[$rand_color_key],
                          $tc_button_text_color
                  );
              } else {
                  $btn_style = ('none' != $fpc_button_color && 'original' != $fpc_button_color && '#fff' != $tc_button_text_color) ? sprintf('style="color:%1$s!important"',
                          $tc_button_text_color
                  ) : '';
              }

              $fpc_button_text = apply_filters( 'fpc_button_text' , $tc_button_text , $fp_single_id );

              $tc_show_fp_button = $tc_show_fp_button && $fpc_button_text ? true : false;
              $tc_fp_button_block = sprintf('<a class="%1$s %2$s" href="%3$s" title="%4$s" data-color="%5$s" %6$s>%7$s</a>',

                                    apply_filters( 'fpc_button_class' ,
                                                sprintf('%1$s fp-button %2$s %3$s %4$s',
                                                    $fpc_button_class,
                                                    $fpc_button_color,
                                                    isset($colors[$rand_color_key]) ? 'btn-random-colors' : '',
                                                    $tc_fp_button_color_override ? 'btn-random-override' : ''
                                                ),
                                                $fp_single_id
                                    ),//end filter

                                    $tc_show_fp_button ? '' : 'fpc-hide',
                                    $featured_page_link,
                                    esc_attr( $featured_page_title ),
                                    $fpc_button_color,
                                    $btn_style,
                                    $fpc_button_text
              );
              echo apply_filters( 'fpc_button_block' , $tc_fp_button_block , $featured_page_link , $featured_page_title , $fp_single_id );

            ?>

          </div><!-- /.fpc-widget-front -->

          <?php
          $html = ob_get_contents();
          if ($html) ob_end_clean();
          return apply_filters( 'fpc_single_display' , $html, $fp_single_id, $fp_img, $featured_page_link, $featured_page_title, $text );
    }//end of function


      /**
      * The template displaying one single featured page
      *
      * @package FPU
      * @since FPU 1.5
      * @param area are defined in featured-pages templates,show_img is a customizer option
      * @todo better area definition : dynamic
      */
      function tc_fp_single_display_modern( $fp_single_id ) {
        //holder declaration
        $fp_holder_img          = apply_filters ('fp_holder_img' , '<img class="tc-holder-img" data-src="holder.js/270x250" style="width:270px;height:250px;" alt="Holder Thumbnail" data-no-retina/>' );
        $fp_img                 = $fp_holder_img;

        //gets boolean and general options
        //$prefix                 = TC_fpu::$instance -> plug_option_prefix;
        $tc_random_colors             = esc_attr( tc__f( '__get_fpc_option' , 'tc_random_colors') );
        $tc_show_fp_img               = esc_attr( tc__f( '__get_fpc_option' , 'tc_show_fp_img') );
        $tc_show_fp_img_override      = esc_attr( tc__f( '__get_fpc_option' , 'tc_show_fp_img_override') );
        $tc_show_fp_button            = esc_attr( tc__f( '__get_fpc_option' , 'tc_show_fp_button') );
        $tc_show_fp_title             = esc_attr( tc__f( '__get_fpc_option' , 'tc_show_fp_title') );
        $tc_show_fp_text              = esc_attr( tc__f( '__get_fpc_option' , 'tc_show_fp_text') );
        $tc_fp_text_limit             = esc_attr( tc__f( '__get_fpc_option' , 'tc_fp_text_limit') );
        $tc_fp_text_color_override    = esc_attr( tc__f( '__get_fpc_option' , 'tc_fp_text_color_override') );
        $tc_fp_button_color           = esc_attr( tc__f( '__get_fpc_option' , 'tc_fp_button_color') );
        $tc_button_text_color         = esc_attr( tc__f( '__get_fpc_option' , 'tc_fp_button_text_color') );
        $tc_fp_button_color_override  = esc_attr( tc__f( '__get_fpc_option' , 'tc_fp_button_color_override') );
        $tc_button_text               = esc_attr( tc__f( '__get_fpc_option' , 'tc_fp_button_text') );

        //random colors
        $rand_color_key         = '';
        if ( false != $tc_random_colors ) {
            $colors             = apply_filters( 'fpc_random_color_list' , array("#510300" , "#4D2A33", "#2B3F38", "#03A678" ,"#7A5945" , "#807D77" ,"#073233", "#B3858A","#F57B3D", "#449BB5", "#043D5D", "#EB5055", "#68C39F", "#1A4A72", "#4B77BE", "#5C97BF", "#F5AE30", "#EDA737", "#C8C8C8", "#13181C", "#248F79", "#D95448", "#26B89A" , "#EC6766", "#E74C3C") );
            $rand_color_key     = array_rand($colors, 1);
            $fp_img             = '<div style="background:' . $colors[$rand_color_key] . ';opacity: 0.7;position: absolute;top: 2px;right: 2px; left:2px; bottom: 2px"></div>';
        }

        //if fps are not set
        if ( ! $this -> tc_fp_is_eligible($fp_single_id) ) {

            //admin link if user logged in
            $featured_page_link             = '';
            $admin_link                     = '';
            if ( ! TC_fpu::$instance->is_customizing && is_user_logged_in() && current_user_can('edit_theme_options') ) {
              $admin_link                   = sprintf( '<a href="%1$s" title="%2$s">%3$s</a>',
                TC_utils_fpu::tc_get_customizer_url( array( 'control' => 'tc_featured_text_'.$fp_single_id ) ),
                esc_attr__( 'Customizer screen' , 'customizr-pro' ),
                __( 'here' , 'customizr-pro' )
              );
              $featured_page_link           = apply_filters( 'fpc_link_url', TC_utils_fpu::tc_get_customizer_url( array( 'control' => 'tc_featured_page_'.$fp_single_id) ) );
            }

            //rendering
            $featured_page_id               =  null;
            $featured_page_title            =  apply_filters( 'fpc_title', esc_attr__( 'Featured page' , 'customizr-pro' ) );
            $text                           =  apply_filters(
                                                'fpc_text',
                                                sprintf( __( 'Featured page description text : use the page or post excerpt or set your own custom text in the WordPress customizer screen %s.' , 'customizr-pro' ),
                                                  $admin_link
                                                ),
                                                $fp_single_id,
                                                $featured_page_id
                                              );
            $fp_img                         =  apply_filters ('fpc_img_src' , $fp_img );
        }

        else {
            $featured_page_id               = apply_filters( 'fpc_id', esc_attr( tc__f( '__get_fpc_option' , 'tc_featured_page_'.$fp_single_id) ), $fp_single_id );

            //get the page/post object
            $_post                          = get_post( $featured_page_id );

            $featured_page_link             = apply_filters( 'fpc_link_url', get_permalink( $featured_page_id ), $fp_single_id );
            $featured_page_title            = apply_filters( 'fpc_title', isset( $_post->post_title ) ? strip_tags( apply_filters( 'the_title', $_post->post_title, $featured_page_id ) ) : '', $fp_single_id, $featured_page_id );

            $edit_enabled                   = false;
            //when are we displaying the edit link?
            //never display when customizing
            if ( ! TC_fpu::$instance->is_customizing ) {
              $edit_enabled                 = ( (is_user_logged_in()) && current_user_can('edit_pages') && is_page( $featured_page_id ) ) ? true : $edit_enabled;
              $edit_enabled                 = ( (is_user_logged_in()) && current_user_can('edit_post' , $featured_page_id ) && ! is_page( $featured_page_id ) ) ? true : $edit_enabled;
            }
            $edit_enabled                   = apply_filters( 'tc_edit_in_fp_title', $edit_enabled );

            $featured_text                  = apply_filters( 'fpc_text', tc__f( '__get_fpc_option' , 'tc_featured_text_'.$fp_single_id ), $fp_single_id, $featured_page_id );
            $featured_text                  = apply_filters( 'fpc_text_sanitize', html_entity_decode( $featured_text ) , $fp_single_id, $featured_page_id );

            //set page/post excerpt as default text if no $featured_text
            $text                           = ( empty($featured_text) && !post_password_required($featured_page_id) ) ? strip_tags(apply_filters( 'the_excerpt' , $_post->post_excerpt )) : $featured_text ;
            $text                           = ( empty($text) && !post_password_required($featured_page_id) ) ? strip_tags(apply_filters( 'the_content' , $_post->post_content )) : $text ;

            //limit text to 200 car
            $default_fp_text_length         = $tc_fp_text_limit ? apply_filters( 'fpc_text_length', 200 ) : 9999;
            $tc_fp_text_length = strlen($text);
            if ( $tc_fp_text_length > $default_fp_text_length ){
                /* strpos returns FALSE if the needle was not found this coudl mess up substr*/
                $end_substr = strpos( $text, ' ' , $default_fp_text_length);
                $end_substr = ( $end_substr !== FALSE ) ? $end_substr : $tc_fp_text_length;
                $text       = substr( $text , 0 , $end_substr );
                $text       = ( $end_substr == $tc_fp_text_length ) ? $text : $text . ' ...';
            }

            //set the image : uses thumbnail if any then >> the first attached image then >> a holder script
            $fp_img_size                    = apply_filters( 'fpc_img_size' , 'fpc-size' );
            $fp_img_id                      = apply_filters( 'fpc_img_id', false , $fp_single_id , $featured_page_id );

            //When do we look for images?
            //1) when random colors not enabled
            //2) when random colors enabled AND overidde boolean is true
            if ( ! $tc_random_colors || $tc_show_fp_img_override ) {
              //try to get "tc_thumb" , "tc_thumb_height" , "tc_thumb_width"
              //tc_get_thumbnail_model( $requested_size = null, $_post_id = null , $_thumb_id = null )
              $_fp_img_model = TC_utils_thumb::$instance -> tc_get_thumbnail_model( $fp_img_size, $featured_page_id, $fp_img_id );

              //finally we define a default holder if no thumbnail found or page is protected
              if ( isset( $_fp_img_model["tc_thumb"]) && ! empty( $_fp_img_model["tc_thumb"] ) && ! post_password_required( $featured_page_id ) )
                $fp_img = $_fp_img_model["tc_thumb"];
              else
                $fp_img = $fp_holder_img;
            }

            //finally we define a default holder if no thumbnail found or page is protected
            $fp_img                 = apply_filters ('fp_img_src' , $fp_img , $fp_single_id , $featured_page_id );
          }//end if

          $random_color_enabled_class = $tc_random_colors ? 'tc-random-colors-enabled' : '';

          //require holder js
          if ( $fp_holder_img == $fp_img )
            add_filter( 'tc_fp_holder_js_required', '__return_true' );

          //Let's render this
          ob_start();
          ?>

          <div class="fpc-widget-front  czr-link-mask-p <?php echo $random_color_enabled_class ?>">
            <?php
              $tc_fp_img_block = sprintf('<div class="fp-thumb-wrapper %1$s %2$s %3$s">%4$s%5$s</div>',
                    'czr__r-wFP', //<- aspect ratio, make it optional
                    $tc_show_fp_img ? '' : 'fpc-hide',
                    ( $fp_img == $fp_holder_img ) ? 'tc-holder' : '',
                    apply_filters('fpc_round_div' , sprintf('<a class="czr-link-mask" href="%1$s" title="%2$s"></a>',
                                                    $featured_page_link,
                                                    esc_attr( $featured_page_title )
                                                  ),
                                $fp_single_id
                    ),
                    $fp_img
              );

              echo apply_filters( 'fpc_img_block' , $tc_fp_img_block , $fp_single_id );


              //When do we show the random colors?
              //1) if random is set
              //2) no override
              $apply_random_to_text = isset($colors[$rand_color_key]) ? true : false;
              $apply_random_to_text = $tc_fp_text_color_override ? false : $apply_random_to_text;

              //title block
              $tc_fp_title_block  = sprintf('<%1$s %2$s class="fp-title %3$s %4$s">%5$s %6$s</%1$s>',
                                    apply_filters( 'fpc_title_tag' , 'h4' ),
                                    $apply_random_to_text ? 'style="color:'.$colors[$rand_color_key].'!important"' : '',
                                    $tc_show_fp_title ? '' : 'fpc-hide',
                                    $tc_fp_text_color_override ? 'text-random-override' : '',
                                    $featured_page_title,
                                    ( isset($edit_enabled) && $edit_enabled )? sprintf('<span class="edit-link fpc-btn fpc-btn-inverse btn btn-inverse btn-mini"><a class="post-edit-link" href="%1$s" title="%2$s" target="_blank">%2$s</a></span>',
                                              get_edit_post_link( $featured_page_id ),
                                              esc_attr__( 'Edit' , 'customizr-pro' )
                                              ) : ''
              );
              echo apply_filters( 'fpc_title_block' , $tc_fp_title_block , $featured_page_title );

              //text block
              $tc_fp_text_block   = sprintf('<p class="fp-excerpt fp-text-%1$s %2$s %3$s" %4$s>%5$s</p>',
                                    $fp_single_id,
                                    $tc_show_fp_text ? '' : 'fpc-hide',
                                    $tc_fp_text_color_override ? 'text-random-override' : '',
                                    $apply_random_to_text ? 'style="color:'.$colors[$rand_color_key].'!important"' : '',
                                    $text
              );
              echo apply_filters( 'fpc_text_block' , $tc_fp_text_block , $fp_single_id , $text);

              //BUTTON BLOCK
              //When do we show the random colors?
              //1) if random is set and no button style
              //2) random + button style + no override
              $apply_random_to_btn = isset($colors[$rand_color_key]) ? true : false;
              //$apply_random_to_btn = ('none' == $tc_fp_button_color ) ? $apply_random_to_btn : false;
              $apply_random_to_btn = ('none' != $tc_fp_button_color && $tc_fp_button_color_override ) ? false : $apply_random_to_btn;


              $fpc_button_color = ! $apply_random_to_btn ? $tc_fp_button_color : '';
              $fpc_button_class = 'fpc-btn fpc-btn-primary fpc-btn-link';

              if ( 'skin' == $fpc_button_color ) {
                if ( class_exists( 'TC___' ) || class_exists( 'CZR___' ) )
                  $fpc_button_class    = 'btn btn-primary btn-more btn-skin-dark fpc-btn-link'; //add btn-more btn-skin-dark for c4 compat
                else
                  $fpc_button_color    = 'none';
              }

              if ($apply_random_to_btn) {
                  $btn_style = sprintf('style="background-color:%1$s;border-color:%2$s;color:%3$s"',
                          $colors[$rand_color_key],
                          $colors[$rand_color_key],
                          $tc_button_text_color
                  );
              } else {
                  //#fff is the default
                  $btn_style = ( ! in_array( $fpc_button_color, array( 'none', 'original' ) ) && ! in_array( $tc_button_text_color, array( '#fff', '#ffffff' ) ) ) ? sprintf('style="color:%1$s!important"',
                          $tc_button_text_color
                  ) : '';
              }

              $fpc_button_text = apply_filters( 'fpc_button_text' , $tc_button_text , $fp_single_id );

              $tc_show_fp_button = $tc_show_fp_button && $fpc_button_text ? true : false;
              $tc_fp_button_block = sprintf('<span class="fp-button readmore-holder %1$s"><a class="%2$s" href="%3$s" title="%4$s" data-color="%5$s" %6$s>%7$s</a></span>',

                                    $tc_show_fp_button ? '' : 'fpc-hide',

                                    apply_filters( 'fpc_button_class' ,
                                                sprintf('%1$s %2$s %3$s %4$s',
                                                    $fpc_button_class,
                                                    $fpc_button_color,
                                                    isset($colors[$rand_color_key]) ? 'btn-random-colors' : '',
                                                    $tc_fp_button_color_override ? 'btn-random-override' : ''
                                                ),
                                                $fp_single_id
                                    ),//end filter
                                    $featured_page_link,
                                    esc_attr( $featured_page_title ),
                                    $fpc_button_color,
                                    $btn_style,
                                    $fpc_button_text
              );
              echo apply_filters( 'fpc_button_block' , $tc_fp_button_block , $featured_page_link , $featured_page_title , $fp_single_id );

            ?>

          </div><!-- /.fpc-widget-front -->

          <?php
          $html = ob_get_contents();
          if ($html) ob_end_clean();
          return apply_filters( 'fpc_single_display' , $html, $fp_single_id, $fp_img, $featured_page_link, $featured_page_title, $text );
    }//end of function


    /**
    * Helper : check whether the fps must be shown
    *
    * @return  boolean
    */
    private function tc_show_fp() {
        //gets display options
        $tc_show_featured_pages         = 0 != esc_attr( tc__f( '__get_fpc_option' , 'tc_show_fp' ) ) && tc__f('__is_home');

        //hide featured pages when page>1
        if ( apply_filters( 'tc_fp_hide_featured_pages_when_paged', is_main_query() ) ) {
          global $wp_query;

          $_is_paged = isset( $wp_query->query_vars['paged'] ) && $wp_query->query_vars['paged'] > 1 ||
                       isset( $wp_query->query_vars['page'] ) && $wp_query->query_vars['page'] > 1;

          $tc_show_featured_pages = $tc_show_featured_pages && ! $_is_paged;
        }

        return apply_filters( 'tc_show_fp',  $tc_show_featured_pages );
    }

    /**
    * Helper : check if the fp id exists in option AND is an existing post in db
    * handles the case when the post has been deleted
    *
    * @param $fp_single_id
    * @return  boolean
    */
    private function tc_fp_is_eligible($fp_single_id) {
      if ( false === (bool) tc__f( '__get_fpc_option' , 'tc_featured_page_'.$fp_single_id ) )
        return;

      //check if the post exists
      return null != get_post( tc__f( '__get_fpc_option' , 'tc_featured_page_'.$fp_single_id ) );
    }

    /*
    * Helper : add fpc container to the Customizr allowed img smart load selectors
    *
    * @params $options
    * @return array $options
    */
    function fpc_allow_img_smartload( $options ) {

      $img_to_smartload_parent_selector = '.fpc-widget-front';

      if ( is_array( $options ) ) {

        if ( array_key_exists( 'parentSelectors', $options ) && is_array( $options['parentSelectors'] ) ) {

          $options['parentSelectors'][] = $img_to_smartload_parent_selector;

        }
        else {

          $options['parentSelectors'] = array( $img_to_smartload_parent_selector );

        }

      }
      else {

        $options = array( 'parentSelectors' => array( $img_to_smartload_parent_selector ) );

      }

      return $options;
    }


    /*
    * Helper
    *
    * @hook tc_fp_notice_customizer_url_args
    * @since v2.0.17
    */
    function tc_fp_notice_customizer_url( $autofocus ) {
      return TC_utils_fpu::tc_get_customizer_url(
        $autofocus = array(
          'control' => 'tc_show_fp'
        )
      );
    }


    /*
    * Helper : add fpc container to the Customizr allowed img smart load selectors
    *
    * @params $options
    * @return array $options
    */
    function fpc_is_img_smart_load_enabled() {
      $theme_name               = TC_fpu::$instance -> tc_get_theme_name();
      $_maybe_smartload_enabled = 'customizr' == $theme_name && version_compare( TC_fpu::$instance -> tc_get_theme_version(), '3.4.8' , '>' );
      $_maybe_smartload_enabled = 'customizr-pro' == $theme_name ? true : $_maybe_smartload_enabled;

      if ( ! $_maybe_smartload_enabled )
        return false;

      /* is old customizr? */
      $_is_old_customizr             = TC_fpu::$instance -> tc_is_customizr_before_prefix_changes();

      return apply_filters( 'fpc_img_smartload', $_maybe_smartload_enabled &&
          esc_attr( $_is_old_customizr ?
            TC_utils::$instance -> tc_opt( 'tc_img_smart_load' ) :
            CZR_utils::$instance -> czr_fn_opt( 'tc_img_smart_load' )
          )
      );
    }


    function tc_enqueue_plug_resources() {
        $theme_name    = TC_fpu::$instance -> tc_get_theme_name();
        //we have different js and css for customizr4
        $res_suffix    = ( defined( 'CZR_IS_MODERN_STYLE' ) && CZR_IS_MODERN_STYLE ) ? '-modern' : '';

        do_action( 'fpu_enqueue_plug_resource_before' );
        wp_enqueue_style(
          'fpu-front-style' ,
          sprintf('%1$s/front/assets/css/fpu-front%2$s%3$s.css' , TC_FPU_BASE_URL, $res_suffix, ( defined('WP_DEBUG') && true === WP_DEBUG ) ? '' : '.min'),
          array(),
          ( defined('WP_DEBUG') && true === WP_DEBUG ) ? TC_fpu::$instance -> plug_version . time() : TC_fpu::$instance -> plug_version,
          $media = 'all'
        );

        //register and enqueue jQuery if necessary
        if ( ! wp_script_is( 'jquery', $list = 'registered') ) {
            wp_register_script('jquery', '//code.jquery.com/jquery-latest.min.js', array(), false, false );
        }
        if ( ! wp_script_is( 'jquery', $list = 'enqueued') ) {
          wp_enqueue_script( 'jquery');
        }

        //FPU Front end scripts
        if ( ! defined( 'CZR_IS_MODERN_STYLE' ) || ! CZR_IS_MODERN_STYLE ) {
              wp_enqueue_script(
                'fpu-front-js',
                sprintf('%1$s/front/assets/js/fpu-front%2$s.js' , TC_FPU_BASE_URL, ( defined('WP_DEBUG') && true === WP_DEBUG ) ? '' : '.min'),
                array('jquery'),
                ( defined('WP_DEBUG') && true === WP_DEBUG ) ? TC_fpu::$instance -> plug_version . time() : TC_fpu::$instance -> plug_version,
                false
              );
        }

        //enqueue imageCenter.js only if
        //1) not customizr
        //2) customizr v < 3.3.5
        //3) customizr pro v < 1.0.13
        $_imgcenter_bool = false;
        if ( false === strpos($theme_name, 'customizr') )
          $_imgcenter_bool = true;
        else {
          $theme_version   = TC_fpu::$instance -> tc_get_theme_version();
          if ( 'customizr' == $theme_name && version_compare( $theme_version , '3.3.5' , '<' ) )
            $_imgcenter_bool = true;
          if ( 'customizr-pro' == $theme_name && version_compare( $theme_version , '1.0.13' , '<' ) )
            $_imgcenter_bool = true;
        }
        $_imgcenter_bool = $_imgcenter_bool && 1 == esc_attr( tc__f( '__get_fpc_option' , 'tc_center_fp_img' ) );

        if ( apply_filters('fpu_enqueue_centerimagejs' , $_imgcenter_bool ) ) {
          wp_enqueue_script(
            'tc-center-images',
            sprintf('%1$s/front/assets/js/jqueryCenterImages%2$s.js' , TC_FPU_BASE_URL, ( defined('WP_DEBUG') && true === WP_DEBUG ) ? '' : '.min'),
            array('jquery', 'fpu-front-js'),
            ( defined('WP_DEBUG') && true === WP_DEBUG ) ? TC_fpu::$instance -> plug_version . time() : TC_fpu::$instance -> plug_version,
            false
          );
        }

        //localizes
        wp_localize_script(
          'fpu-front-js',
          'FPUFront',
          apply_filters('tc_fpc_js_front_params' ,
            array(
              'Spanvalue'               => $this -> tc_get_layout('span'),
              'ThemeName'               => str_replace( ' ' , '-', TC_fpu::$theme_name),
              'imageCentered'           => esc_attr( tc__f( '__get_fpc_option' , 'tc_center_fp_img' ) ),
              'smartLoad'               => $this -> fpc_is_img_smart_load_enabled()
            )
          )
        );

        //tc_show_wp_img is transported in postMessage
        $tc_show_featured_pages_img     = TC_fpu::$instance->is_customizing || 0 != esc_attr( tc__f( '__get_fpc_option' , 'tc_show_fp_img' ) );
        if ( apply_filters('tc_fp_holder_js_required', false ) && $tc_show_featured_pages_img ) {
          //holder image
          wp_enqueue_script(
            'holder' ,
            sprintf( '%s/front/assets/js/holder.js' , TC_FPU_BASE_URL ),
            array(),
            ( defined('WP_DEBUG') && true === WP_DEBUG ) ? TC_fpu::$instance -> plug_version . time() : TC_fpu::$instance -> plug_version,
            $in_footer = false
          );
        }

        do_action( 'fpu_enqueue_plug_resource_after' );
    }
} //end of class
