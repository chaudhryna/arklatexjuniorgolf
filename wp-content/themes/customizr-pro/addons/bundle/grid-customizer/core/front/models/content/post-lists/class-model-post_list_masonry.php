<?php
/*
*
* TODO: treat case post format image with no text and post format gallery
*/
class CZR_post_list_masonry_model_class extends CZR_Model {

    //Default post list layout
    private static $default_post_list_layout   = array(
                //content_wrapper_breadth => (order ASC) column classes array()
                'full'        => array(
                             array( 'col-sm-6', 'col-12' ), //user opt is 2 columns
                             array( 'col-lg-4', 'col-md-6', 'col-12' ), //user opt is 3 columns
                             array( 'col-xl-3', 'col-lg-4', 'col-md-6', 'col-12' ), //user opt is 4 columns
                ),
                //stays for one sidebar
                'semi-narrow' => array(
                             array( 'col-md-6', 'col-12' ), //user opt is 2 columns
                             array( 'col-lg-4', 'col-md-6', 'col-12' ), //user opt is 3 columns
                             array( 'col-lg-4', 'col-md-6', 'col-12' ), //user opt is 4 columns
                ),
                //stays for one sidebar
                'narrow' => array(
                             array( 'col-md-6', 'col-12' ), //user opt is 2 columns
                             array( 'col-md-6', 'col-12' ), //user opt is 3 columns
                             array( 'col-md-6', 'col-12' ), //user opt is 4 columns
                )
            );


    private static $post_formats_with_icon_allowed  = array( 'quote', 'link', 'status', 'aside', 'chat' );
    private static $post_formats_without_media      = array( 'quote', 'link', 'status', 'aside', 'chat' );

    /*
    * We decided that in masonry all the images (even those with text) should be displayed like the gallery
    */
    private static $post_formats_with_big_image     = array( 'gallery', 'image' );

    private static $default_thumb_img_size_name     = 'tc-masonry-thumb-size';
    private static $big_thumb_img_size_name         = 'tc-masonry-bi-thumb-size';

    private static $big_image_ratio_wrapper_class   = 'czr__r-w10by15';
    private static $video_ratio_wrapper_class       = 'czr__r-w16by9';

    public $post_class                              = array( 'grid-item' );

    public $post_list_items                         = array();



    /**
    * @override
    */
    public function __construct( $model ) {

        parent::__construct( $model );

        //can be set here as it's the same for all the elements.
        //must be called after the eventual model args (parameters) are merged
        //as the columns_class needs to access this model's grid_columns property
        $column_class       = $this->czr_fn__get_columns_class();

        $this->post_class   = array_merge( $this->post_class, $column_class );

        //require masonry scripts
        //regardless of when this model is instantiated, let's enqueue them in the footer
        //they will be enqueued by wp as late scripts
        if ( !did_action( 'wp_footer' ) ) {
            add_action( 'wp_footer', array( $this, 'czr_fn_require_masonry_scripts' ), -1 );
        }
        else {
            $this->czr_fn_require_masonry_scripts();
        }

    }



    /**
    * @override
    * fired before the model properties are parsed
    *
    * return model preset array()
    */
    function czr_fn_get_preset_model() {

        $_preset = array(
            'excerpt_length'           => esc_attr( czr_fn_opt( 'tc_post_list_excerpt_length' ) ),
            'show_thumb'               => esc_attr( czr_fn_opt( 'tc_post_list_show_thumb' ) ),
            'show_comment_meta'        => esc_attr( czr_fn_opt( 'tc_show_comment_list' ) ) && esc_attr( czr_fn_opt( 'tc_comment_show_bubble' ) ),
            'content_wrapper_breadth'  => czr_fn_get_content_breadth(),
            'grid_columns'             => esc_attr( czr_fn_opt( 'tc_masonry_columns') ),
            'grid_hover_move'          => true,
            'grid_shadow'              => true,
            'image_centering'          => 'js-centering',
            'contained'                => false,
            'wrapped'                  => true,
        );

        return $_preset;
    }

    /**
    * @override
    * fired before the model properties are parsed
    *
    * return model params array()
    */
    function czr_fn_extend_params( $model = array() ) {
        //to merge args
        $model                              = parent::czr_fn_extend_params( $model );

        $model[ 'content_wrapper_breadth' ] = in_array( $model[ 'content_wrapper_breadth' ], array( 'full', 'semi-narrow', 'narrow' ) ) ?
              $model[ 'content_wrapper_breadth' ] : 'full';

        return $model;
    }




    /**
    * add custom classes to the masonry container element
    */
    function czr_fn_get_element_class() {
        $_classes = $this->content_wrapper_breadth ? array( $this->content_wrapper_breadth ) : array();

        if ( ! empty( $this->grid_shadow ) )
            $_classes[] = 'tc-grid-shadow-soft';
        if ( ! empty( $this->grid_hover_move ) )
            $_classes[] = 'tc-grid-hover-move';
        if ( ! empty( $this->contained ) )
            $_classes[] = 'container';

        return $_classes;
    }


    /*
    * Fired just before the view is rendered
    * @hook: pre_rendering_view_{$this -> id}, 9999
    */
    /*
    * Each time this model view is rendered setup the current post list item
    * and add it to the post_list_items_array
    */
    function czr_fn_setup_late_properties() {
        //all post lists do this
        if ( czr_fn_is_loop_start() )
            $this -> czr_fn_setup_text_hooks();

        $this->post_list_items[] = $this->czr_fn__get_post_list_item();
    }

   /*
    * Fired just before the view is rendered
    * @hook: post_rendering_view_{$this -> id}, 9999
    */
    function czr_fn_reset_late_properties() {
        if ( czr_fn_is_loop_end() ) {
            //all post lists do this
            $this -> czr_fn_reset_text_hooks();
            //reset alternate items at loop end
            $this -> czr_fn_reset_post_list_items();
        }
    }

    /*
    *  Public getters
    */

    function czr_fn_get_article_selectors() {
        return $this -> czr_fn__get_post_list_item_property( 'article_selectors' );
    }

    function czr_fn_get_has_header_format_icon() {
        return $this -> czr_fn__get_post_list_item_property( 'has_header_format_icon' );
    }

    function czr_fn_get_show_comment_meta() {
        return $this -> czr_fn__get_post_list_item_property( 'show_comment_meta' );
    }


    function czr_fn_get_media_class() {
        return $this -> czr_fn__get_post_list_item_property( 'media_class' );
    }

    function czr_fn_get_print_start_wrapper() {
        return $this -> wrapped && czr_fn_is_loop_start();
    }

    function czr_fn_get_print_end_wrapper() {
        return $this -> wrapped && czr_fn_is_loop_end();
    }

    /*
    * Private/protected getters
    */

    /*
    *  Method to compute the properties of the current (in a loop) post list item
    *  @return array
    */
    protected function czr_fn__get_post_list_item() {

        $current_post_format          = get_post_format();
        $is_full_image_candidate      = $this -> czr_fn__get_is_full_image_candidate( $current_post_format );

        $is_post_with_media           = $this -> czr_fn__get_is_post_with_media( $current_post_format );

        $post_media                   = false;

        if ( $is_post_with_media ) {
            $post_media                 = $this->czr_fn__get_post_media ( array(
                'post_format'           => $current_post_format,
                'thumb_size'            => $is_full_image_candidate ? self::$big_thumb_img_size_name : self::$default_thumb_img_size_name,
                'image_centering'       => $is_full_image_candidate ? $this->image_centering : 'no-js-centering'
            ) );
        } else {//reset post media instance
            $this->czr_fn_reset_post_media_instance();
        }

        $has_post_media               = !empty( $post_media );
        $has_header_format_icon       = !$has_post_media && $this -> czr_fn__get_has_header_format_icon( $current_post_format );

        $show_comment_meta            = $this -> show_comment_meta && czr_fn_is_possible( 'comment_info' );

        $is_full_image                = $is_full_image_candidate && $post_media;

        $article_selectors            = $this -> czr_fn__get_article_selectors( $is_full_image, $has_post_media );

        //add the aspect ratio class for full-images and video
        $media_class                  = $is_full_image ? self::$big_image_ratio_wrapper_class : '';
        $media_class                  = ! $media_class && 'video' == $current_post_format ? self::$video_ratio_wrapper_class : $media_class;


        return array(
            'article_selectors'      => $article_selectors,
            'media_class'            => $media_class,
            'has_post_media'         => $has_post_media,
            'has_header_format_icon' => $has_header_format_icon,
            'show_comment_meta'      => $show_comment_meta,
        );

    }

    protected function czr_fn__get_post_list_item_property( $_property ) {
        if ( ! $_property )
            return;

        $_properties = end( $this->post_list_items );
        return isset( $_properties[ $_property ] ) ? $_properties[ $_property ] : null;
    }

    /*
    * Very similar to the one in the alternate...
    * probably the no-thumb/no-text should be ported somewhere else (in czr_fn_get_the_post_list_article_selectors maybe)
    */
    protected function czr_fn__get_article_selectors( $is_full_image, $has_post_media ) {
        $post_class                = $this -> post_class;

        /* Extend article selectors with info about the presence of an excerpt and/or thumb */
        $post_class[]              = $is_full_image && $has_post_media ? 'full-image' : '';

        $id_suffix                 = is_main_query() ? '' : "_{$this -> id}";

        return czr_fn_get_the_post_list_article_selectors( array_filter($post_class), $id_suffix );
    }


    //@return array
    protected function czr_fn__get_columns_class() {
        $number_of_columns = intval( $this->grid_columns );

        //must be between 2 and 4
        $number_of_columns = ( $number_of_columns > 1 ) && ( 5 > $number_of_columns ) ? $number_of_columns : 3;//default


        //get the columns classes map for the corrent content wrapper breadth (full|semi-narrow|narrow)
        //the content_wrapper_breadth, which is a paramenter is already sanitized, we don't need further checks
        $column_classes_map = self::$default_post_list_layout[ $this->content_wrapper_breadth ];


        //now return the column classes based on the user option (number_of_columns), sanitized above
        return $column_classes_map[ $number_of_columns - 2 ];
    }



    protected function czr_fn__get_is_post_with_media( $current_post_format ) {
        return $this->show_thumb && !in_array( $current_post_format, self::$post_formats_without_media );
    }


    protected function czr_fn__get_is_full_image_candidate( $current_post_format ) {
        return in_array(  $current_post_format  , self::$post_formats_with_big_image );
    }


    protected function czr_fn__get_has_header_format_icon( $current_post_format ) {
        return in_array(  $current_post_format  , self::$post_formats_with_icon_allowed );
    }





    protected function czr_fn__get_post_media_model_identifiers() {
        return array(
                'id'          => 'media', //this must be the same of the first param used in the render_template
                'model_class' => 'content/common/media',
        );
    }



    protected function czr_fn_reset_post_media_instance( $media_model_instance = null ) {
        if ( is_null( $media_model_instance ) ) {
          $identifiers          = $this->czr_fn__get_post_media_model_identifiers();
          $id                   = $identifiers[ 'id' ];
          $media_model_instance = czr_fn_get_model_instance( $id );
        }

        if ( $media_model_instance ) {
            $media_model_instance->czr_fn_reset_to_defaults();
        } else {
            //this must be the same of the first param used in the render_template
            $registered_id        = czr_fn_maybe_register( $identifiers );
            $media_model_instance = czr_fn_get_model_instance( $registered_id );
        }

        if ( $media_model_instance ) {
            $media_model_instance->czr_fn_set_property( 'visibility', false );
        }
    }



    protected function czr_fn__get_post_media( $media_args = array(), $media_model_instance = null ) {

        if ( is_null( $media_model_instance ) ) {
            $identifiers          = $this->czr_fn__get_post_media_model_identifiers();

            //this must be the same of the first param used in the render_template
            $registered_id        = czr_fn_maybe_register( $identifiers );

            $media_model_instance = czr_fn_get_model_instance( $registered_id );
        }

        if ( !$media_model_instance )
            return false;

        $defaults = array(
              'post_id'               => null,
              'post_format'           => null,
              'type'                  => 'all',
              'use_img_placeholder'   => false,
              'has_format_icon_media' => false,
              'force_icon'            => false,
              'use_icon'              => false,
              'thumb_size'            => 'full',
              'image_centering'       => 'no-js-centering'
        );

        $media_args = wp_parse_args( $media_args, $defaults );

        //setup the media
        $media_model_instance -> czr_fn_setup( $media_args );

        return $media_model_instance -> czr_fn_get_raw_media();
    }

    /* HELPERS AND CALLBACKS */

    /*
    * Following methods: czr_fn_setup_text_hooks, czr_fn_reset_text_hooks, czr_fn_set_excerpt_length
    * are shared by the post lists classes, do we want to build a common class?
    */

    /**
    * @package Customizr
    * @since Customizr 4.0
    */
    function czr_fn_setup_text_hooks() {
        //filter the excerpt length
        add_filter( 'excerpt_length'        , array( $this , 'czr_fn_set_excerpt_length') , 999 );
    }


    /**
    * @package Customizr
    * @since Customizr 4.0
    */
    function czr_fn_reset_text_hooks() {
        remove_filter( 'excerpt_length'     , array( $this , 'czr_fn_set_excerpt_length') , 999 );
    }


    /**
    * @package Customizr
    * @since Customizr 4.0
    */
    function czr_fn_reset_post_list_items() {
        $this -> post_list_items = array();
    }


    /**
    * hook : excerpt_length hook
    * @return string
    * @package Customizr
    * @since Customizr 3.2.0
    */
    function czr_fn_set_excerpt_length( $length ) {
        $_custom = $this -> excerpt_length;
        return ( false === $_custom || !is_numeric($_custom) ) ? $length : $_custom;
    }


    function czr_fn_require_masonry_scripts() {
        //CZR_DEBUG_MODE, CZR_DEV_MODE, CZR_REFRESH_ASSETS are defined in the theme czr_fn_setup_constants() [core/core-functions.php]
        wp_enqueue_script( 'masonry' );
        wp_enqueue_script(
          'pc-masonry' ,
          TC_GC_BASE_URL . sprintf('/assets/front/js/pc-masonry%1$s.js' ,  ( CZR_DEBUG_MODE || CZR_DEV_MODE ) ? '' : '.min'),
          array( 'masonry', CZR_resources_scripts::$instance->czr_fn_load_concatenated_front_scripts() ? 'tc-scripts' : 'tc-js-params' ),
          CZR_DEBUG_MODE || CZR_DEV_MODE || CZR_REFRESH_ASSETS ? PC_pro_bundle::$instance -> plug_version . time() : PC_pro_bundle::$instance -> plug_version,
          $_in_footer = true
        );
    }



    /**
    * @return css string
    * hook : czr_fn_user_options_style
    */
    function czr_fn_user_options_style_cb( $_css ) {
        $icon_dir_alignment = !is_rtl() ? 'right' : 'left';
        $_css = sprintf("%s\n%s\n",
            $_css,
            "
            .grid-container__masonry .full-image .entry-header {
              position: relative;
            }
            .grid-container__masonry .entry-footer {
              margin-top: 2.5em;
              position: relative;
            }
            .grid-container__masonry .format-audio .audio-container iframe {
              height: 80px;
            }
            .grid-container__masonry .full-image .tc-thumbnail img {
                width: auto;
                height: 100%;
                max-width: none;
            }
            .grid-container__masonry .post-type__icon {
                background: transparent;
                height: auto;
                line-height: 1;
                border-radius: 0;
                font-size: 1em;
                position: static;
                width: 100%;
                float: {$icon_dir_alignment};
                text-align: {$icon_dir_alignment};
                margin-top: -1em;
                padding: 2% 0;
            }
            .grid-container__masonry .tc-content {
                font-size: 0.95em;
                line-height: 1.65em;
            }

            .grid-container__masonry blockquote,
            .grid-container__masonry .entry-link {
                border: none;
                padding-top: 0;
                padding-bottom: 0;
            }
            .grid-container__masonry .entry-link a,
            .grid-container__masonry blockquote p {
                margin: 0;
                max-width: 100%;
                padding-left: 0;
            }
            .grid-container__masonry blockquote::before,
            .grid-container__masonry .entry-link::before {
                content: none;
            }
            "
        );
        return $_css;

    }

}