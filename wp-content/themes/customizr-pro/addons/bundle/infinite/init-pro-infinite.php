<?php
/**
* PRO INFINITE SCROLL INIT CLASS
*
* @author Nicolas GUILLAUME
* @since 1.0
*/
final class PC_init_infinite {
      static $instance;

      public $infinite_class;//Will store the pro infinite scroll instance


      public function __construct () {

            self::$instance     =& $this;

            add_action( 'czr_after_load'                       , array( $this,  'set_on_czr_after_load_hooks') );

            //add customizer settings
            add_filter( 'czr_fn_post_list_option_map'          , array( $this, 'pc_register_pro_infinte_settings' ) );


            if ( ! defined( 'PC_INFINITE_BASE_URL' ) ) {
                  define( 'PC_INFINITE_BASE_URL' , TC_PRO_BUNDLE_BASE_URL . '/infinite' );
            }
            if ( ! defined( 'PC_INFINITE_BASE' ) ) {
                  define( 'PC_INFINITE_BASE' , dirname( __FILE__ ) );
            }

            //The animation is too expensive on mobile.
            //Disable it if wp_is_mobile().
            add_filter( 'czr_animate_on', array( $this, 'is_animation_on' ) );

            //TODO:
            //port this:
            //https://github.com/Automattic/jetpack/blob/f09f26fd08feff3cd6c042707aabd055fcb6e0c8/modules/infinite-scroll.php
      }//end of construct

      //hook : 'czr_animate_on'
      function is_animation_on() {
            return ! wp_is_mobile();
      }

      //hook : 'czr_after_load'
      //set up hooks
      public function set_on_czr_after_load_hooks() {

            //do nothing if in customizer preview
            /*if ( hu_is_customize_preview_frame() )
                  return;*/

            add_action( 'wp'                                      , array( $this, 'pc_infinite_scroll_class_and_functions' ) );

            add_action( 'wp'                                      , array( $this, 'pc_infinite_scroll_init' ) );

            //disable pagination
            add_filter( 'tc_opt_tc_show_post_navigation'          , array( $this, 'pc_infinite_disable_pagination' ), 10, 2 );


            if ( !CZR_IS_MODERN_STYLE ) {

                  add_action( 'wp'                                 , array( $this, 'pc_maybe_regenerate_query_for_classic_grid' ), 50 );

                  //filter infinite scroll query args to set correct posts per page in the classic grid
                  add_filter( 'infinite_scroll_query_args'         , array( $this, 'pc_maybe_alter_endlessly_query_args_for_classic_grid' ), 999 );

                  //disable sticky expansion in ajax calls
                  if ( czr_fn_is_ajax() )
                        add_filter( 'tc_opt_tc_grid_expand_featured', '__return_false' );
            }


            add_action( 'pc__before_infinite_scroll_render_loop'  , array( $this, 'pc_infinite_prepare_grids_for_render' ) );

            /* TESTING PROPEDEUTICAL CSS */
            add_action( 'wp_head'                                 , array( $this , 'pc_various_infinite_css' ), 9999 );

            /* TESTING PURPOSE APPEARING EFFECTS */
            if ( apply_filters( 'czr_animate_on', false ) ) {

                  add_action( 'wp_head'                      , array( $this , '_appearing_animation_css' ), 999 );

                  //testing purpose only
                  //animation should be fired after masonry otherwise waypoint can be triggered too much early
                  //masonry bricks are absolute, appended new elements might result in the viewport before masonry
                  //moves them (performing masonry('layout') )
                  //would be great having some sort of ordered callbacks (like wp hooks)
                  add_action( 'wp_footer'                    , array( $this , '_appearing_animation_js' ), 999999999 );
            }

            add_filter( 'body_class'      , array( $this, 'set_body_class' ), 99999 );

      }//set_on_czr_after_load_hooks



      //hook : 'init'
      //Require Infinite scroll class and instantiate it
      public function pc_infinite_scroll_class_and_functions() {

            if ( czr_fn_is_ajax() || $this->pc_is_infinite_scroll_enabled_in_current_context() ) {
                  require_once(  PC_INFINITE_BASE  . '/infinite-scroll/class_infinite.php' );
                  $this->infinite_class = new PC_infinite_scroll( array(
                      'type'              => 'scroll',
                      'isClickTypeOnDesktop' => ! czr_fn_is_checked( 'tc_load_on_scroll_desktop' ),
                      'isClickTypeOnMobile' => ! czr_fn_is_checked( 'tc_load_on_scroll_mobile' ),
                      'handle'            => '<div id="infinite-handle"><a class="btn btn-primary btn-more btn-skin-dark" href="javascript:void(0)">{text}</a></div>',
                      'appendHandleTo'    => '.article-container',
                      'minWidthForDetermineUrl' => 1024
                  ));
            }

      }

      //hook : 'body_class'
      function set_body_class( $classes ) {
          $classes = is_array( $classes ) ? $classes : array();
          if ( $this->pc_is_infinite_scroll_enabled_in_current_context() ) {
            $classes[] = 'czr-infinite-scroll-on';
          }
          return $classes;
      }

      //hook : 'wp'
      //Initialize PC_Infinite_Scroll
      public function pc_infinite_scroll_init() {

            if ( $this->infinite_class && class_exists( 'PC_infinite_scroll' ) ) {

                  //TODO: DO THIS BETTER, meaning change the class_customizr_infinite.php
                  PC_infinite_scroll::$settings = null;

                  $_settings = array(
                        'wrapper'           => false,
                        'render_inner_loop' => array($this, 'czr_fn_render_modern_template'),
                        'container'         => 'content .grid-container > .row'
                  );

                  if ( CZR_IS_MODERN_STYLE ) {
                        //allow browser's history page numbers push for non masonry or classic grid post lists
                        //this is possible by setting the wrapper param as true
                        if ( in_array( esc_attr( czr_fn_opt( 'tc_post_list_grid') ), array('alternate', 'plain', 'plain_excerpt') ) ) {
                              $_settings[ 'wrapper' ] = true;
                        }
                        if ( 'grid' == esc_attr( czr_fn_opt( 'tc_post_list_grid') ) ) {
                              $_settings[ 'container' ] = 'content .grid-container .grid-section-not-featured';
                        }

                        $_settings[ 'css_pattern' ]  = false; //the container html selector for the modern style is not only an id so we skip the validation
                  }
                  else {
                        $_settings[ 'render_inner_loop' ] = array($this, 'czr_fn_render_classic_template');
                        //allow browser's history page numbers push for post lists
                        //this is possible by setting the wrapper param as true
                        $_settings[ 'wrapper' ]           = true;
                        $_settings[ 'container' ]         = 'content';
                  }


                  //we can pass settings to this
                  add_theme_support( 'pc-infinite-scroll', $_settings );

                  //regenerate settings
                  PC_infinite_scroll::get_settings();

            }

      }




      //hook: 'wp'
      //fill the blog classic grid in classic style
      public function pc_maybe_regenerate_query_for_classic_grid() {

            //we do this only for the for archive/blog page
            //don't run this on the endlessly query
            //instead filter the args passed to WP_Query using the filter 'infinite_scroll_query_args'
            if ( did_action( 'pc_before_endlessly_query' ) )
                  return;

            //Bail if:
            // a) infinite scroll option not checked
            // or
            // b) is_admin
            // or
            // c) we're not in a post list context
            //if ( ! esc_attr( czr_fn_opt( 'infinite-scroll' ) ) || is_admin() || !hu_is_post_list() )
            //      return;

            //Do this only for main query and classical grid
            if ( ! ( !is_admin() && 'grid'== esc_attr( czr_fn_opt( 'tc_post_list_grid' ) ) && $this->pc_is_infinite_scroll_enabled_in_current_context() ) )
                  return;

            global $wp_query, $wp_the_query;

            if ( !$wp_query->is_main_query() )
                  return;

            /*
            * Maybe improve the algorithm to set a
            */
            //get the number of posts based on the
            //1) $wp_query->post_count
            //2) $query_vars['posts_per_page']
            $query_vars       = $wp_query->query_vars;

            $new_posts_per_page_param   = $this->pc_calculate_classic_grid_posts_per_page( $query_vars['posts_per_page'], $wp_query->post_count );


            if ( !$new_posts_per_page_param )
                  return;

            //rebuild the query;
            $query_args    =  array_merge( $query_vars, array( 'posts_per_page' => $new_posts_per_page_param ) );

            /*From jetpack infinite scroll */
            // 4.0 ?s= compatibility, see https://core.trac.wordpress.org/ticket/11330#comment:50
            if ( empty( $query_args['s'] ) && ! isset( $wp_query->query['s'] ) ) {

                  unset( $query_args['s'] );
            }

            // By default, don't query for a specific page of a paged post object.
            // This argument can come from merging self::wp_query() into $query_args above.
            // Since IS is only used on archives, we should always display the first page of any paged content.
            unset( $query_args['page'] );

            $wp_query = $wp_the_query = new WP_Query( $query_args );

      }




      //hook: 'infinite_scroll_query_args'
      //fill the blog grid (not masonry, not standard) last row
      public function pc_maybe_alter_endlessly_query_args_for_classic_grid( $args ) {

            //Do this only for main query and classical grid
            if ( 'grid' != esc_attr( czr_fn_opt( 'tc_post_list_grid' ) ) )
                  return $args;

            //avoid sticky expansion

            //hack: the infinite query takes in account the post__not_in of the original query (the one ran before the infinite query)
            //when we display

            if ( !isset( $args['posts_per_page'] ) )
                  return $args;
            //we have to know if we are in a classic grid design otherwise we don't have to alter the args
            //this might be heavy but I don't have any other way to do this at the moment
            //We have to build the query and skope the options.
            //would be great if we could skope just the options we need
            //Another idea would be to pass via js (in class_customizr_infinite.php) something like the "current skope id"
            //and be able to retrieve the skoped options without:
            //1) run any query
            //and maybe
            //2) cache the skope
            global $wp_query, $wp_the_query;
            $wp_the_query = $wp_query = new WP_Query( $args );


            if ( !$wp_query->is_main_query() )
                  return $args;

            //we just want to alter the posts per page if
            $new_posts_per_page_param = $this->pc_calculate_classic_grid_posts_per_page( $args['posts_per_page'], $args['posts_per_page'] );

            if ( ! $new_posts_per_page_param )
                  return $args;

            $args['posts_per_page'] = $new_posts_per_page_param;

            return $args;

      }



      //hook : 'pc__before_infinite_scroll_render_loop'
      public function pc_infinite_prepare_grids_for_render() {
            if ( !CZR_IS_MODERN_STYLE ) {

                  if ( did_action( '__post_list_grid' ) )  {

                         CZR_post_list_grid::$instance->czr_fn_set_grid_before_loop_hooks();
                         CZR_post_list_grid::$instance->czr_fn_set_grid_loop_hooks();
                         CZR_post_list_grid::$instance->czr_fn_grid_prepare_expand_sticky();

                         //Grid customizer
                         if ( esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_gc_enabled') ) ) {
                              TC_front_gc::$instance->tc_set_gc_before_loop_hooks();
                              TC_front_gc::$instance->tc_set_gc_loop_hooks();
                         }


                  }

                  //headings are initialized at template redirect 10, the inifinite dies at template redirect 5
                  CZR_headings::$instance->czr_fn_set_post_page_heading_hooks();
                  CZR_headings::$instance->czr_fn_set_headings_options();

                  //post metas are initialized at template redirect > 5, the inifinite dies at template redirect 5
                  CZR_post_metas::$instance->czr_fn_set_visibility_options();
                  CZR_post_metas::$instance->czr_fn_set_design_options();



            } else { //CZR_IS_MODERN_STYLE
                  // For the modern style, each grid has a parameter named "wrapped" that
                  // can be used to avoid the printing of the whole grid element wrapper
                  // That's particulary useful in this case since we want to print only the grid items
                  add_filter(  'czr_main_content_loop_item'       , array( $this, 'pc_force_modern_grid_to_avoid_printing_the_wrappers' ), 999 );

                  //avoid opacity treatment by adding the opacity-forced class to the post thumbnails and placeholders
                  add_filter( 'wp_get_attachment_image_attributes', array( $this, 'pc_avoid_modern_thumb_opacity_treatment' ), 999 );
                  add_filter( 'czr_placeholder_image_attributes'  , array( $this, 'pc_avoid_modern_thumb_opacity_treatment' ), 999 );

            }

            //in any case let's avoid the smartload when doing infinite scroll by removing the filters
            //added after_setup_theme, which is too early to know if czr_fn_is_ajax
            //DOING_AJAX constant is set in the PC_infinite_scroll::ajax_response method, fired at template_redirect|5
            remove_filter( 'the_content'    , 'czr_fn_parse_imgs', PHP_INT_MAX );
            remove_filter( 'czr_thumb_html' , 'czr_fn_parse_imgs'  );
      }



      //hook: czr_main_content_loop_item
      public function pc_force_modern_grid_to_avoid_printing_the_wrappers( $loop_item ) {


            if ( ! is_array( $loop_item ) ) {
                  return $loop_item;
            }

            if ( !isset( $loop_item[ 'loop_item_model' ] ) || !is_array( $loop_item[ 'loop_item_model' ] ) ) {
                  $loop_item[ 'loop_item_model' ] = array();
            }

            if ( !isset( $loop_item[ 'loop_item_model' ][ 'model_args' ] ) || !is_array( $loop_item[ 'loop_item_model' ][ 'model_args' ] ) ) {
                  $loop_item[ 'loop_item_model' ][ 'model_args' ] = array();
            }


            $loop_item[ 'loop_item_model' ][ 'model_args' ][ 'wrapped' ] = false;

            return $loop_item;

      }





      /* Infinite render loop item callbacks */

      //MODERN STYLE
      public function czr_fn_render_modern_template(){

            $loop_item_tmpl = 'modules/grid/grid_wrapper';
            $loop_item_model = array( 'model_id' => 'post_list_grid' );


            if ( czr_fn_is_registered_or_possible('post_list') ) {
                  $loop_item_tmpl = 'content/post-lists/post_list_alternate';
                  $loop_item_model = array();
            } elseif ( czr_fn_is_registered_or_possible('post_list_plain') ) {
                  $loop_item_tmpl = 'content/post-lists/post_list_plain';
                  $loop_item_model = array();
            }

            $loop_item_tmpl = apply_filters( 'czr_post_list_loop_item_template', $loop_item_tmpl);
            $loop_item_model = apply_filters( 'czr_post_list_loop_item_model', $loop_item_model );


            $loop_item = apply_filters( "czr_main_content_loop_item", array(
                'loop_item_tmpl' => $loop_item_tmpl,
                'loop_item_model' => $loop_item_model
            ) );


            czr_fn_render_template(
                  $loop_item['loop_item_tmpl'],//<= is a relative path
                  $loop_item['loop_item_model']
            );
      }



      //CLASSIC STYLE
      public function czr_fn_render_classic_template(){
            do_action ('__before_article') ?>
               <article <?php czr_fn__f('__article_selectors') ?>>
                   <?php do_action( '__loop' ); ?>
               </article>
           <?php do_action ('__after_article');
      }
      /* render loop item callbacks end */





      //hook: 'tc_opt_tc_show_post_navigation'
      //disable pagination
      public function pc_infinite_disable_pagination( $bool ) {

            return $this->pc_is_infinite_scroll_enabled_in_current_context() ? false : $bool;

      }





      //hook: czr_placeholder_image_attributes, wp_get_attachment_image_attributes
      public function pc_avoid_modern_thumb_opacity_treatment( $atts ) {
            if ( !is_array( $atts) )
                  return $atts;

            $atts[ 'class' ] .= ' opacity-forced';
            return $atts;
      }




      //hook : czr_fn_post_list_option_map
      public function pc_register_pro_infinte_settings( $settings ) {

            $infinite_settings = array(
                  'tc_infinite_scroll'  =>  array(
                        'default'   => false,
                        'control'   => 'CZR_controls' ,
                        'title'     => __( 'Infinite scroll', 'customizr-pro' ),
                        'label'     => __( 'Enable infinite scroll' , 'customizr-pro' ),
                        'section'   => 'post_lists_sec' ,
                        'type'      => 'checkbox' ,
                        //'active_callback' => 'czr_fn_is_list_of_posts',
                        'priority'        => 38,
                        'notice'          =>   __( 'When this option is enabled, your posts are revealed when scrolling down, from the most recent to the oldest one, like on a Facebook wall.', 'customizr-pro' ),
                        //temporary hack
                        //since atm this option is not available in the preview, let's avoid refresh
                        //'transport' => 'postMessage',
                        /*'ubq_section'   => array(
                            'section' => 'static_front_page',
                            'priority' => '12'
                        )*/
                  ),
                  'tc_load_on_scroll_desktop'  =>  array(
                        'default'   => true,
                        'control'   => 'CZR_controls' ,
                        'label'     => __( 'Desktop and laptop devices : when the infinite scroll is enabled, load posts automatically when scrolling.' , 'customizr-pro' ),
                        'section'   => 'post_lists_sec' ,
                        'type'      => 'checkbox' ,
                        //'active_callback' => 'czr_fn_is_list_of_posts',
                        'priority'        => 38,
                        'notice'          =>   __( 'When this option is disabled, a "Load more posts" button will be printed.', 'customizr-pro' ),
                        'ubq_section'   => array(
                                'section' => 'performances_sec',
                                'priority' => '60'
                            )
                  ),
                  'tc_load_on_scroll_mobile'  =>  array(
                        'default'   => false,
                        'control'   => 'CZR_controls' ,
                        'label'     => __( 'Mobile devices : when the infinite scroll is enabled, load posts automatically when scrolling.' , 'customizr-pro' ),
                        'section'   => 'post_lists_sec' ,
                        'type'      => 'checkbox' ,
                        //'active_callback' => 'czr_fn_is_list_of_posts',
                        'priority'        => 38,
                        'notice'          =>   __( 'For better performances on mobile devices, we recommend to let this option unchecked.', 'customizr-pro' ),
                        'ubq_section'   => array(
                                'section' => 'performances_sec',
                                'priority' => '60'
                            )
                  ),
                  'tc_infinite_scroll_in_home'  =>  array(
                        'default'   => true,
                        'control'   => 'CZR_controls' ,
                        'label'     => __( 'Enable infinite scroll in your home/blog' , 'customizr-pro' ),
                        'section'   => 'post_lists_sec' ,
                        'type'      => 'checkbox' ,
                        //'active_callback' => 'czr_fn_is_list_of_posts',
                        'priority'        => 38,
                  ),
                  'tc_infinite_scroll_in_archive'  =>  array(
                        'default'   => false,
                        'control'   => 'CZR_controls' ,
                        'label'     => __( 'Enable infinite scroll in your Archives (archives, categories, author posts)' , 'customizr-pro' ),
                        'section'   => 'post_lists_sec' ,
                        'type'      => 'checkbox' ,
                        //'active_callback' => 'czr_fn_is_list_of_posts',
                        'priority'        => 38,
                  ),
                  'tc_infinite_scroll_in_search'  =>  array(
                        'default'   => false,
                        'control'   => 'CZR_controls' ,
                        'label'     => __( 'Enable infinite scroll in your search results' , 'customizr-pro' ),
                        'section'   => 'post_lists_sec' ,
                        'type'      => 'checkbox' ,
                        //'active_callback' => 'czr_fn_is_list_of_posts',
                        'priority'        => 38,
                  ),

            );

            return array_merge( $infinite_settings, $settings );

      }



      /* HELPERS */
      //helper
      //@return bool or int
      public function pc_calculate_classic_grid_posts_per_page( $current_number_of_posts_set_to_retrieve, $current_number_of_posts_retrieved ) {

            //always fill the rows (2 columns by default )
            $nb_columns = apply_filters( 'tc_get_grid_cols',
                  esc_attr( czr_fn_opt( 'tc_grid_columns') ),
                  CZR_utils::czr_fn_get_layout( czr_fn_get_id() , 'class' )
            );

            $nb_columns = is_numeric( $nb_columns ) ? $nb_columns : 3;

            if ( 0 == $current_number_of_posts_retrieved % $nb_columns )
                  return;

            //we don't want to risk that in the new query there's a sticky post
            //Example:
            //posts_per_page = 2
            //
            //old query: sticky-post_1, post1, post2
            //we want to make it:
            //
            //posts_per_page = 3
            //new query: sticky-post_1, post1, post2, post3 to fill the rows
            //
            //if post3 is a sticky-post (sticky-post_2) we'll end up with
            //new query: sticky-post_1, sticky_post2, post1, post2
            // and that's fine
            $posts_per_page =  $current_number_of_posts_set_to_retrieve + ( $nb_columns - ( $current_number_of_posts_retrieved % $nb_columns ) );

            return $posts_per_page;
      }





      public function pc_is_infinite_scroll_enabled_in_current_context() {
            return esc_attr( czr_fn_opt( 'tc_infinite_scroll' ) ) && $this->pc_is_post_list_context_matching();
      }





      /* performs the match between the option where to use post list grid
       * and the post list we're in */
      public function pc_is_post_list_context_matching() {
            $_type = $this->pc_get_post_list_context();

            return $_type && esc_attr( czr_fn_opt( 'tc_infinite_scroll_in_' . $_type ) );
      }




      /* returns the type of post list we're in if any, an empty string otherwise */
      public function pc_get_post_list_context() {
            global $wp_query;

            if ( ( is_home() && 'posts' == get_option('show_on_front') ) ||
                    $wp_query->is_posts_page )
                return 'home';
            else if ( is_search() && $wp_query->post_count > 0 )
                return 'search';
            else if ( is_archive() )
                return 'archive';
            return false;
      }





      //CSS AND JS

      public function pc_various_infinite_css() {

            if ( !$this->pc_is_infinite_scroll_enabled_in_current_context() )
                  return;

            ?>
            <style id="infinite-css" type="text/css">

                  [class*="infinite-view-"] {

                        width: 100%;
                  }

                  .infinite-loader {
                        position: relative !important;
                  }
                  .masonry__wrapper .infinite-loader {
                        position: absolute !important;
                  }
            </style>
            <?php
      }




      // hook : wp_head
      // printed if ( apply_filters( 'czr_animate_on', wp_is_mobile() )  )
      public function _appearing_animation_css() {

            if ( !$this->pc_is_infinite_scroll_enabled_in_current_context() )
                  return;

            ?>
            <style id="appearing-animation-css" type="text/css">
                  /* Bottom to top keyframes */
                  @-webkit-keyframes btt-fade-in {
                        from{ -webkit-transform: translate3d(0, 100%, 0); opacity: 0; }
                        99% { -webkit-transform: translate3d(0, 0, 0); }
                        to { opacity: 1; }
                  }
                  @-moz-keyframes btt-fade-in {
                        from{ -moz-transform: translate3d(0, 100%, 0); opacity: 0; }
                        99% { -moz-transform: translate3d(0, 0, 0); }
                        to { opacity: 1; }
                  }

                  @-o-keyframes btt-fade-in {
                        from{ -o-transform: translate3d(0, 100%, 0); opacity: 0; }
                        99% { -o-transform: translate3d(0, 0, 0); }
                        to { opacity: 1; }
                  }

                  @keyframes btt-fade-in {
                        from { transform: translate3d(0, 100%, 0); opacity: 0; }
                        99% { transform: translate3d(0, 0, 0); }
                        to { opacity: 1; }
                  }
                  /*
                  * Hack: since ie11 doesn't animate 3d transforms in the right way
                  * with this specific vendor we override the non prefixes keyframes btt-in
                  * only for ms
                  */
                  @-ms-keyframes btt-fade-in {
                        from { transform: translate(0, 100%);  opacity: 0; }
                        99% { transform: translate(0, 0); }
                        to { opacity: 1; }
                  }


                  #content {
                        overflow: hidden;
                  }
                  .grid-container {
                        position: relative;
                  }
                  .grid-container .grid-item {
                        overflow: visible;
                  }

                  .grid-container .grid-item .grid__item {
                        opacity: 0;
                  }

                  .grid-container.advanced-animation .grid-item .grid__item {
                        -webkit-animation-duration: 0.8s;
                           -moz-animation-duration: 0.8s;
                             -o-animation-duration: 0.8s;
                                animation-duration: 0.8s;
                        -webkit-perspective: 1000;
                        -webkit-backface-visibility: hidden;
                           -moz-backface-visibility: hidden;
                             -o-backface-visibility: hidden;
                            -ms-backface-visibility: hidden;
                                backface-visibility: hidden;
                  -webkit-animation-timing-function: ease-in-out;
                     -moz-animation-timing-function: ease-in-out;
                       -o-animation-timing-function: ease-in-out;
                          animation-timing-function: ease-in-out;
                        -webkit-animation-fill-mode: forwards;
                           -moz-animation-fill-mode: forwards;
                             -o-animation-fill-mode: forwards;
                                animation-fill-mode: forwards;
                  }

                  .grid-container.simple-animation .grid-item .grid__item {
                        -webkit-transition: opacity 2s ease-in-out;
                        -moz-transition: opacity 2s ease-in-out;
                        -ms-transition: opacity 2s ease-in-out;
                        -o-transition: opacity 2s ease-in-out;
                        transition: opacity 2s ease-in-out;
                  }

                  /*
                  * .start_animation here is "hardcoded",
                  * we might want to have different animations in the future
                  */
                  .grid-container.advanced-animation .grid-item .grid__item.start_animation {
                        -webkit-animation-name: btt-fade-in;
                           -moz-animation-name: btt-fade-in;
                             -o-animation-name: btt-fade-in;
                                animation-name: btt-fade-in;
                                overflow: hidden;
                  }

                  .no-js .grid-container .grid-item .grid__item,
                  .no-cssanimations .grid-container .grid-item .grid__item,
                  .grid-container.advanced-animation .grid-item .grid__item.end_animation {opacity: 1;}

            </style>
            <?php
      }



      // hook : wp_footer
      // printed if ( apply_filters( 'czr_animate_on', wp_is_mobile() )  )
      public function _appearing_animation_js() {

            if ( !$this->pc_is_infinite_scroll_enabled_in_current_context() )
                  return;

            ?>
            <script id="appearing-animation-js" type="text/javascript">

                  !( function(czrapp, $){
                        czrapp.ready.done( function() {

                              var animationEnd              = 'webkitAnimationEnd animationend msAnimationEnd oAnimationEnd',
                                  wrapperSelector           = '.grid-container',
                                  animatableSelector        = '.grid__item',
                                  animatableParentSelector  = '.grid-item',
                                  $_container               = $( wrapperSelector );

                              if ( !$_container.length )
                                    return;

                              var   $_collection            = $( animatableParentSelector, $_container );

                              //wait for masonry init before animate
                              if ( $_container.hasClass( 'masonry' ) ) {
                                    $_container.on( 'masonry-init.customizr', function() {
                                          animateMe(
                                              $_collection,
                                              $_container,
                                              animatableSelector,
                                              animatableParentSelector
                                          );
                                    });

                              } else {
                                    animateMe(
                                        $_collection,
                                        $_container,
                                        animatableSelector,
                                        animatableParentSelector
                                    );
                              }

                              var _event = $_container.find( '.masonry__wrapper' ).length ? 'masonry.customizr' : 'post-load';

                              //maybe animate infinite appended elements
                              czrapp.$_body.on( _event, function( e, response ) {
                                    if ( 'success' == response.type && response.collection && response.container ) {
                                          animateMe(
                                              response.collection,
                                              $( '#'+response.container ), //_container
                                              animatableSelector,//_to_animate_selector
                                              animatableParentSelector//_to_animate_parent_selector
                                          );
                                    }

                              } );

                              /*
                              * params:
                              * _collection                  : an object of the type { id : element [...] } || a jquery object (e.g. list of jquery elements)
                              * _container                   : the jquery container element or the items to animate, or the selector
                              * _to_animate_selector         : item selector to animate
                              * _to_animate_parent_selector  : item to animate parent selector
                              */
                              function animateMe( _collection, _container, _to_animate_selector, _to_animate_parent_selector, type ) {
                                    var   $_container        = $(_container),
                                          collection         = null;

                                    //from array of jquery elements to collection ?
                                    //create an array of selectors
                                    if ( _collection instanceof jQuery || 'object' !== typeof _collection ) {
                                          collection = _.chain( $( _to_animate_parent_selector, $_container ) )
                                                .map( function( _element ) {
                                                      return '#' + $(_element).attr( 'id' );
                                                })
                                                //remove falsy
                                                .compact()
                                                //values the chain
                                                .value();
                                    }
                                    else {
                                          collection = _collection;
                                    }


                                    if ( 'object' !== typeof collection ) {
                                          return;
                                    }

                                    type = type || 'advanced-animation';//simple-animation';

                                    $( wrapperSelector ).addClass( type );
                                    /*
                                    * see boxAnimation function in library/js/app.js in the theme you know
                                    */
                                    var   $allItems    = _.size( collection ),
                                          startIndex   = 0,
                                          shown        = 0,
                                          index        = 0,
                                          sequential   = true;

                                    var _simpleAnimation = function( elementSelector ) {
                                          $(  elementSelector, $_container).find( animatableSelector ).css( 'opacity' , 1 );
                                    };

                                    var _advancedAnimation = function( elementSelector ) {
                                          //store the collection index into the element to animate
                                          var $_to_animate = $(  elementSelector + ' ' + _to_animate_selector , $_container);

                                          if ( $_to_animate.hasClass( 'end_animation' ) ) {
                                                return;//continue
                                          }

                                          $_to_animate.attr('data-collection-index', index );

                                          new Waypoint({

                                                element: $( elementSelector, $_container ),
                                                handler: function() {
                                                      var   element = $( _to_animate_selector, this.element),
                                                            parent  = $(this.element),
                                                            currentIndex,
                                                            isLast;

                                                      //in case posts are per row the delay is based on the index in the row
                                                      if ( parent.parent('[class*=grid-cols].row-fluid').length ) {

                                                            currentIndex = parent.index();
                                                            isLast       = parent.is(':last-child');
                                                      } else {
                                                            currentIndex = element.attr('data-collection-index');
                                                            isLast       = false
                                                      }

                                                      //testing purpose
                                                     // element.attr('data-index', currentIndex );
                                                      var  delay = (!sequential) ? index : ((startIndex !== 0) ? currentIndex - $allItems : currentIndex),
                                                          delayAttr = parseInt(element.attr('data-delay'));

                                                      if (isNaN(delayAttr)) delayAttr = 100;
                                                      delay -= shown;

                                                      var objTimeout = setTimeout(function() {

                                                            //replace start_animation with an animation class
                                                            //the animationEnd routine is needed only because
                                                            //IS removes not visible nodes (in classical grid and classical blog)
                                                            //and re-adds them when needed. In the latter case, a new animation
                                                            //will be triggered,
                                                            element.addClass('start_animation')
                                                                  .on( animationEnd, function(evt) {
                                                                        if ( element.get()[0] == evt.target ) {
                                                                              element.removeClass('start_animation')
                                                                                     .addClass('end_animation');
                                                                              element.parent(_to_animate_parent_selector).removeClass( 'infinite-doing-animation' );
                                                                        }
                                                                  })
                                                                  .parent(_to_animate_parent_selector).addClass( 'infinite-doing-animation' );
                                                            shown = isLast ? 0 : currentIndex;

                                                      }, delay * delayAttr );

                                                      parent.data('objTimeout', objTimeout);
                                                      this.destroy();
                                                },//end handler

                                                offset: '150%'//might be tied to a fn() of matchMedia and user choosen grid type in the future

                                          }).context.refresh(); //end Waypoint

                                          index++;
                                    };

                                    //Fire an animation
                                    _.each( collection, function( elementSelector ) {
                                          if ( 'simple-animation' == type ) {
                                                _simpleAnimation( elementSelector );
                                          } else {
                                                _advancedAnimation( elementSelector );
                                          }

                                    });//end each on the collection
                              };//end animateMe
                        });//end czrapp.ready.done
                  })(czrapp, jQuery);
            </script>
            <?php
      }//end function
} //end of class