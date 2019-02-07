<?php
class CZR_gc_grid_wrapper_model_class extends CZR_grid_wrapper_model_class {


  function __construct( $model = array() ) {
    parent::__construct( $model );

    if ( $this->gc_enabled ) {
      add_action( 'wp_enqueue_scripts'                         , array( $this , 'czr_fn_enqueue_plug_resources'), 0 );
      //add title skin color
      add_filter( 'czr_dynamic_skin_color_color_prop_selectors', array( $this, 'czr_fn_maybe_add_skin_title_color' ) );

      $this -> title_in_caption_below = false;

      $this -> global_effect          = $this -> czr_fn_gc_maybe_randomize_global_effect( $this->global_effect );


    }
  }


  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model preset array()
  */
  function czr_fn_get_preset_model() {

    $_preset = parent::czr_fn_get_preset_model();

    $_preset = array_merge( $_preset, array(
      'gc_enabled'              => esc_attr( czr_fn_opt( 'tc_gc_enabled' ) ),

      'gc_title_caps'           => esc_attr( czr_fn_opt( 'tc_gc_title_caps' ) ),
      'gc_transp_bg'            => esc_attr( czr_fn_opt( 'tc_gc_transp_bg' ) ),
      'gc_title_color'          => esc_attr( czr_fn_opt( 'tc_gc_title_color' ) ),
      'gc_title_custom_color'   => esc_attr( czr_fn_opt( 'tc_gc_title_custom_color' ) ),
      'gc_title_location'       => esc_attr( czr_fn_opt( 'tc_gc_title_location' ) ),
      'gc_white_title_hover'    => true,

      'gc_limit_excerpt_length' => esc_attr( czr_fn_opt( 'tc_gc_limit_excerpt_length' ) ),
      'global_effect'           => esc_attr( czr_fn_opt( 'tc_gc_effect' ) ),
      'randomize_effect'        => esc_attr( czr_fn_opt( 'tc_gc_random' ) ),
    ));

    return $_preset;
  }


  /******************************
  GRID ITEM SETUP
  *******************************/


  /**
  *
  * @override
  *
  * add custom classes to the grid container element
  */
  function czr_fn_get_element_class() {
    $_classes = parent::czr_fn_get_element_class();

    if ( $this->gc_enabled ) {
      $_classes[] = 'tc-gc';

      /* SOME OPTIONS */
      if ( $this->gc_title_caps )
        $_classes[] = 'gc-title-caps';

      //TITLE / POST BACKGROUND CLASS
      $_bg_class = $this->gc_transp_bg;
      if ( ! $_bg_class || empty( $_bg_class ) )
        $_bg_class = 'gc-title-dark-bg';
      else
        $_bg_class = sprintf('gc-%s' , $_bg_class );

      $_classes[] =  $_bg_class;

      //TITLE COLOR CLASS
      if ( $this->gc_white_title_hover )
        $_classes[] = 'gc-white-title-hover';

      $_color_class = $this->gc_title_color;
      if ( ! $_color_class || empty( $_color_class ) )
        $_color_class = 'gc-white-title';
      else
        $_color_class = sprintf('gc-%s-title' , $_color_class );

      $_classes[] = $_color_class;

      //Make sure we don't add the move on hover effect to the pro grid elements
      //the pro grid elements already have their effects
      unset( $_classes[ 'tc-grid-hover-move' ] );
    }

    return $_classes;
  }

  /*
  * @override
  */
  function czr_fn_get_grid_item_article_selectors( $section_cols, $is_expanded ) {
    $article_selectors = parent::czr_fn_get_grid_item_article_selectors( $section_cols, $is_expanded );

    if ( $this->gc_enabled )
      $article_selectors = str_replace( 'expanded', 'gc-expanded', $article_selectors );

    return $article_selectors;
  }

  /*
  * @override
  */
  function czr_fn_get_grid_item_entry_summary_class( $is_expanded ) {

    if ( $this->gc_enabled ) {

      $_effects_to_vertically_center = array(
          'effect-6',
          'effect-12',
          'effect-13',
          'effect-14',
          'effect-15',
          'effect-17',
          'effect-19',
      );

      $class = '';

      if ( $is_expanded || in_array( $this->czr_fn_gc_get_current_effect(), $_effects_to_vertically_center ) ) {

          $class = 'czr-talign'; //text alignment


      }

      return $class;

    }
    //else
    return parent::czr_fn_get_grid_item_entry_summary_class( $is_expanded );
  }

  /*
  * @override
  */
  function czr_fn_get_grid_item_gcont_class( $is_expanded ) {
    return ( $this->gc_enabled ) ? '' : parent::czr_fn_get_grid_item_gcont_class( $is_expanded );
  }

  /*
  * has edit in caption
  * @override
  */
  function czr_fn_grid_item_has_edit_above_thumb( $is_expanded ) {
    return ( $this->gc_enabled ) ? 'over' == $this->gc_title_location : parent::czr_fn_grid_item_has_edit_above_thumb( $is_expanded );
  }

  /*
  * has title in caption
  * @override
  */
  function czr_fn_grid_item_has_title_in_caption( $is_expanded ) {
    return ( $this->gc_enabled ) ? 'over' == $this->gc_title_location : parent::czr_fn_grid_item_has_title_in_caption( $is_expanded );
  }

  /*
  * has fade expt
  * @override
  */
  function czr_fn_grid_item_has_fade_expt( $is_expanded, $thumb_img ) {
    return ( $this->gc_enabled ) ? false : parent::czr_fn_grid_item_has_fade_expt( $is_expanded, $thumb_img );
  }


  /*
  * figure class
  * @override
  */
  function czr_fn_get_grid_item_figure_class( $has_thumb, $section_cols, $is_expanded ) {
    $_classes = parent::czr_fn_get_grid_item_figure_class( $has_thumb, $section_cols, $is_expanded );

    if ( $this->gc_enabled ) {
      array_push( $_classes, apply_filters( 'czr_fn_gc_effect_class' , $this -> czr_fn_gc_get_current_effect() ) );

      if ( !get_the_excerpt() )
        array_push( $_classes, 'gc-no-excerpt' );
    }

    return $_classes;
  }

  /*
  * @override
  */
  function czr_fn_get_grid_item_text() {
    return ( $this->gc_enabled ) ? $this->czr_fn_gc_wrap_the_excerpt( get_the_excerpt() ) : parent::czr_fn_get_grid_item_text();
  }



  /******************************
  HELPERS
  *******************************/
  /**
  * @return string
  */
  private function czr_fn_gc_get_current_effect() {
    if ( ! empty( $this -> current_effect ) )
      $effect = $this -> current_effect;
    else
      $effect = $this -> czr_fn_maybe_randomize_gc_effect( $this -> global_effect );
    return $effect;
  }


  /**
  * Return string wrapped into the passed tag
  * @return string
  */
  private function czr_fn_gc_wrap_string_tag( $_string, $_tag, $_attr = '' ) {
    return sprintf('<%1$s %2$s>%3$s</%1$s>',
      $_tag,
      $_attr,
      $_string
    );
  }


  /**
  * Set excerpt length in number of words
  * hook : excerpt_length
  * @return int
  */
  function czr_fn_set_excerpt_length( $length ) {
    $_length = parent::czr_fn_set_excerpt_length( $length );

    if ( !$this->gc_enabled || !$this->gc_limit_excerpt_length )
      return $_length;

    $_user_length = $_length;
    $_length      = 'effect-4' != $this->czr_fn_gc_get_current_effect() ? 18 : 15;

    return $_user_length > $_length ? $_length : $_user_length;
  }

  /**
  * hook : get_the_excerpt
  * inside loop
  * wraps the excerpt into convenients p tags
  */
  function czr_fn_gc_wrap_the_excerpt( $_excerpt ) {
    //removes potential spaces at the beginning
    $_excerpt = trim( str_replace( '&nbsp;', ' ', $_excerpt ) );
    if ( ! $_excerpt || 0 == strlen( $_excerpt ) )
      return;


    if ( 'effect-4' != $this -> czr_fn_gc_get_current_effect() ) {
      $_excerpt = '<p>' . $_excerpt . '</p>';
    }
    else {
      $_excerpt_words = $this -> czr_fn_split_text_chunks( $_excerpt, 3 );
      if ( empty($_excerpt_words) )
        return;

      $_excerpt_p = array_map( array( $this, 'czr_fn_gc_wrap_string_tag'), $_excerpt_words, array_fill( 0, sizeof ($_excerpt_words),'p') ) ;
      $_excerpt = implode('', $_excerpt_p);
    }

    return $_excerpt;
  }


  /**
  * Return array of strings, with the $_chunks size
  * @return array
  */
  private function czr_fn_split_text_chunks( $_string, $_chunks ) {
    $_string = trim( str_replace('&nbsp;', ' ',  $_string ) );
    if ( 0 == strlen(str_replace(' ', '',$_string) ) )
      return array();

    $_string_words = explode( ' ', $_string );
    $_chunk_size = ceil( sizeof($_string_words) / $_chunks );
    $_return = array();

    $_string_chunks = array_chunk( $_string_words, $_chunk_size, true);

    foreach ( $_string_chunks as $key => $value )
      array_push($_return, implode( ' ', $value) );

    return $_return;
  }




  /**
  * Return a random effect from the list
  * @return string effect name
  */
  function czr_fn_gc_maybe_randomize_global_effect( $_effect ) {
    if ( 'rand-global' != $this->randomize_effect )
      return $_effect;

    return $this -> czr_fn_get_random_effect();
  }

  /**
  * Applies randoms effect to the grid
  * @return string effect name
  */
  function czr_fn_maybe_randomize_gc_effect( $_effect  ) {
    if ( 'rand-each' != $this->randomize_effect )
      return $_effect;
    return $this -> czr_fn_get_random_effect();
  }

  /**
  * Return a random effect from the list
  * @return string effect name
  */
  private function czr_fn_get_random_effect() {
    return array_rand( TC_utils_gc::$instance -> tc_get_effects_list() , 1);
  }



  /**
  * @return css string
  * hook : tc_user_options_style
  */
  function czr_fn_user_options_style_cb( $_css ) {

    $_css = parent::czr_fn_user_options_style_cb( $_css );

    if ( $this->gc_enabled ) {
      $_color = czr_fn_opt( 'tc_skin_color' );

      $_css = sprintf("%s\n%s",
        $_css,
        "
        #{$this->element_id} .grid-item.hover .has-thumb.effect-2 {
          background: {$_color};
        }\n"
      );

      //TITLE CUSTOM COLOR
      $_user_color = $this->gc_title_color;
      if ( 'white' != $_user_color ) {
        $_color = 'custom' == $_user_color ? $this->gc_title_custom_color : $_color;
        $_css = sprintf("%s\n%s",
          $_css,
          "
          #{$this->element_id}.gc-custom-title .tc-grid-figure .entry-title a {
            color:{$_color};
          }\n"
        );
      }
    }
    return $_css;
  }


  function czr_fn_maybe_add_skin_title_color( $selectors ) {
    //this must be fired only once
    static $_to_do = true;

    if ( ! $_to_do )
      return $selectors;

    $_to_do = false;

    return array_merge( $selectors, array(
      '.gc-skin-title .tc-grid-figure .entry-title a'
    ));
  }

  /******************************
  * ASSETS
  *******************************/
  /* Enqueue Plugin resources */
  function czr_fn_enqueue_plug_resources() {
     wp_enqueue_style(
      'gc-front-style' ,
      TC_GC_BASE_URL . sprintf('/assets/front/css/gc-front%1$s.css' ,  ( defined('WP_DEBUG') && true === WP_DEBUG ) ? '' : '.min'),
      null,
      PC_pro_bundle::$instance -> plug_version,
      $media = 'all'
    );
  }

}