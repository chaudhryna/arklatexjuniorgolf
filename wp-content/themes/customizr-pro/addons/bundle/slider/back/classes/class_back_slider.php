<?php
/**
* Admin actions
*
*
* @package      Customizr
* @subpackage   classes
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>, Rocco Aliberti <rocco@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME - Rocco Aliberti
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_back_pro_slider' ) ) :
  class TC_back_pro_slider {
    static $instance;

    function __construct () {
      self::$instance =& $this;
      add_action( 'after_setup_theme'             , array( $this , 'tc_back_pro_slider_hooks'), 500 );
    }//end of construct




    /***************************************
    * HOOKS SETTINGS ***********************
    ****************************************/
    /**
    * hook : after_setup_theme
    */
    function tc_back_pro_slider_hooks() {
      /* On save_post/deleted_posts Customizr itself will refresh the home post slider, but which will be filtered by the cat query filter in this addon */
      //refresh the terms array (categories/tags pickers options) on term deletion and post sliders on term deletion
      add_action( 'delete_term'                     , array( $this, 'tc_refresh_home_slider_terms_picker_cb'), 20, 3 );

      /* Slider of posts as slider for posts/pages */
      // Add the slider of posts as selectable slider for posts/pages
      add_filter( 'czr_post_selectable_sliders'     , array( $this, 'tc_add_slider_of_posts_to_selectable_post_sliders' ) );

      // When there no slides to display in the post metabox maybe we have to display the posts sliders controls
      // 'cause it would mean that the user choose to display the slider of posts
      add_action( '__no_slides'                    , array( $this, 'tc_print_posts_slider_controls'), 10, 2 );

      // Ajax Save the slider of posts id
      add_action( '__before_ajax_save_slider_post' , array( $this, 'tc_pro_slider_ajax_save_slider_of_posts' ), 10, 2 );
      // Save slider of posts fields on save_post
      add_action( '__after_save_post_slider_fields', array( $this, 'tc_pro_slider_save_slider_fields' ) );

      // enqueue pro slider admin scripts
      // this action is documented in customizr class-admin-metaboxes
      //This way we're sure we're enqueuing select2 only where is required, meaning
      //only where the tc_slider_ajax script is enqueued
      //see theme core CZR_meta_boxes::czr_fn_slider_admin_scripts()
      add_action( 'tc_enqueue_ajax_slider_after'   , array( $this, 'tc_pro_slider_admin_scripts' ) );
    }



    /*
    * Update the term pickers related options
    * @return void
    *
    * @package Customizr
    * @since Customizr 3.4.10
    */
    function tc_refresh_home_slider_terms_picker_cb( $term, $tt_id, $taxonomy ) {

      if ( !class_exists( 'CZR_admin_init' ) )
        return;

      if ( 'tc_posts_slider' != czr_fn_opt( 'tc_front_slider' ) )
        return;

      switch ( $taxonomy ) {
       //delete categories based options
       case 'category':
          // refresh the posts slider option
          CZR_admin_init::$instance -> czr_fn_refresh_term_picker_options( $term, $option_name = 'tc_posts_slider_restrict_by_cat' );
      }
    }



    /*
    * Add the slider of posts as selectable slider for posts/pages
    * @param array of sliders
    *
    * @return array
    *
    * hook: tc_post_selectable_sliders
    */
    function tc_add_slider_of_posts_to_selectable_post_sliders( $sliders ) {
      $new_slider =  array(
            //ID => label
          'tc_posts_slider' => __('&mdash; Auto-generated slider from your blog posts &mdash;', 'customizr-pro')
      );
      return is_array($sliders) ? array_merge( $new_slider, $sliders) : $new_slider ;
    }



    /*
    *
    * Ajax: Save the slider of posts id
    * @param $POST the $_POST
    * @param $tc_slider_fields default slider fields
    *
    * @return void
    *
    * hook: __before_save_slider_post
    */
    function tc_pro_slider_ajax_save_slider_of_posts( $POST, $tc_slider_fields ) {
      if ( empty( $POST ) )
        return;

      //sanitize user input by looping on the fields
      foreach ( $tc_slider_fields as $tcid => $tckey) {
        if ( isset( $POST[ $tcid ] ) ) {
          switch ( $tckey ) {
            //different sanitizations
            //the slider name custom field for a post/page
            case 'post_slider_key' :
              $mydata = esc_attr( $POST[$tcid] );
              // Check if the key is the slider of posts, Customizr theme will take care of everything else
              if ( empty( $mydata ) || 'tc_posts_slider' != $mydata )
                break;

              //write in DB
              if ( isset( $POST['tc_post_id'] ) ) {
                add_post_meta( $POST['tc_post_id'], $tckey, $mydata, true) or
                update_post_meta( $POST['tc_post_id'], $tckey , $mydata);
              }
              break;
          }//end switch
        } //endif
      }//end for
    }//end function



    /*
    *
    * Save slider of posts fields on save_post
    * @param $POST the $_POST
    *
    * @return void
    *
    * hook: __after_save_post_slider_fields
    */
    function tc_pro_slider_save_slider_fields( $POST ) {
      if ( empty( $POST ) )
        return;

      $post_slider_fields = array(
        //field => key
        'slider_posts_number_field'      => 'limit',
        'slider_posts_sticky_field'      => 'stickies_only',
        'slider_posts_title_field'       => 'show_title',
        'slider_posts_excerpt_field'     => 'show_excerpt',
        'slider_posts_link_type_field'   => 'link_type',
        'slider_posts_button_text_field' => 'button_text',
        'slider_posts_categories_field'  => 'categories'
      );

      $data = array();
      //sanitize user input by looping on the fields
      foreach ( $post_slider_fields as $tcid  => $tckey )
        if ( isset( $POST[ $tcid ] ) ){
          switch ( $tcid ) {
            //different sanitizations
            case 'slider_posts_button_text_field':
              $sanitized_data = sanitize_text_field( $POST[$tcid] );
              break;
            case 'slider_posts_categories_field':
              //Do not sanitize arrays
              //http://codex.wordpress.org/Function_Reference/update_post_meta
              //A passed array will be serialized into a string.(this should be raw as opposed to sanitized for database queries)
              $sanitized_data = $POST[$tcid];
              break;
            default:
              $sanitized_data = esc_attr( $POST[$tcid] );
          }//end switch
          $data[ $tckey ]  = $sanitized_data;
        }//end if
      //end for

      if ( isset( $POST[ 'tc_post_id' ] ) ) {
        //remove the meta if no data to write
        if ( empty($data) )
          delete_post_meta( $POST['tc_post_id'], 'post_slider_posts_key' , $data);
        else
        //write in DB
        update_post_meta( $POST['tc_post_id'], 'post_slider_posts_key' , $data);
      }
    }//end function



    /*
    * Build and print the posts slider controls
    *
    * @param $postid the ID of the current post
    *
    * @return void
    *
    * hook: __before_save_slider_post
    */
    function tc_print_posts_slider_controls( $postid, $current_post_slider ) {
      if ( 'tc_posts_slider' != $current_post_slider || empty($postid) )
        return;
      //check value is ajax saved ?
      $slider_of_posts_value   = (array) get_post_meta( $postid, $key = 'post_slider_posts_key', true );
      extract( $slider_of_posts_value );

      /*** Fields setup ***/
      //Number of posts
      $number_of_posts_id            = 'slider_posts_number_field';
      $number_of_posts_value         = isset( $limit ) && ! empty( $limit ) ? $limit : 1;

      //Sticky only
      $sticky_only_id                = 'slider_posts_sticky_field';
      $sticky_only_value             = isset( $stickies_only ) ? $stickies_only : false;

      $display_title_id              = 'slider_posts_title_field';
      $display_title_value           = isset( $show_title ) ? $show_title : true;

      $display_excerpt_id            = 'slider_posts_excerpt_field';
      $display_excerpt_value         = isset( $show_excerpt ) ? $show_excerpt : true;

      // type of link
      $type_of_link_id               = 'slider_posts_link_type_field';
      $type_of_link_value            = isset( $link_type ) ? $link_type : 'cta';
      //build selectable type of link
      $type_of_link_choices          = array (
        'cta'        => __('Call to action button', 'customizr-pro' ),
        'slide'      => __('Entire slide', 'customizr-pro' ),
        'slide_cta'  => __('Entire slide and call to action button', 'customizr-pro' )
      );

      $button_text_id                = 'slider_posts_button_text_field';
      $button_text_value             = isset( $button_text ) ? $button_text : __( 'Read more &raquo;' , 'customizr-pro' );

      //categories
      $categories_id                 = 'slider_posts_categories_field';
      $categories_value              = isset( $categories )  ? (array)$categories : array();

      /*** Print ***/
      echo '<p></p>';
      /* We use here, most of the times, the 'customizr-pro' textdomain as most of these strings are already defined, hence localized, in Customizr free theme */
      /* For future use: depends on Customizr meta boxes */
      /*
      //Section title
      TC_meta_boxes::tc_title_view( array(
          'title_text' => __( 'Slider of posts settings', 'customizr-pro' ),
          'title_tag'  => 'h3'
      ));
      echo '<hr>';

      //Number of posts to display
      TC_meta_boxes::tc_generic_input_view( array(
          'input_name'  => $number_of_posts_id,
          'input_value' => $number_of_posts_value,
          'input_type'  => 'number',
          'title'       => array(
             'title_text' => __('Number of posts to display', 'customizr-pro')
          )
      ));
      //Sticky posts
      TC_meta_boxes::tc_checkbox_view( array(
          'input_state' => $sticky_only_value,
          'input_name'  => $sticky_only_id,
          'title'       => array(
              'title_text' => __( 'Include only sticky posts', 'customizr-pro')
          ),
          'content_after' => sprintf('<p><i>%1$s <a href="https://codex.wordpress.org/Sticky_Posts" target="_blank">%2$s</a></p></i>',
                             __( 'You can choose to display only the sticky posts. If you\'re not sure how to set a sticky post, check', 'customizr-pro' ),
                             __('the WordPress documentation.', 'customizr-pro' )
                         )
      ));
      //Display title
      TC_meta_boxes::tc_checkbox_view( array(
          'input_state' => $display_title_value,
          'input_name'  => $display_title_id,
          'title'       => array(
              'title_text' => __( 'Display the title', 'customizr-pro')
          ),
      ));
      //Display excerpt
      TC_meta_boxes::tc_checkbox_view( array(
          'input_state' => $display_excerpt_value,
          'input_name'  => $display_excerpt_id,
          'title'       => array(
              'title_text' => __( 'Display the excerpt', 'customizr-pro')
          ),
      ));
      //Type of link
      TC_meta_boxes::tc_selectbox_view( array(
          'select_name'  => $type_of_link_id,
          'choices'      => $type_of_link_choices,
          'selected'     => $type_of_link_value,
          'title'        => array(
              'title_text' => __( 'Link post with', 'customizr-pro')
          ),
      ));
      //Button text
      TC_meta_boxes::tc_generic_input_view( array(
          'input_name'  => $button_text_id,
          'input_value' => $button_text_value,
          'input_type'  => 'text',
          'title'       => array(
             'title_text' => __('Button text (80 char. max length)', 'customizr-pro')
          ),
          'input_class' => 'widefat',
          'custom_args' => 'style="width:50%"'
      ));

      //Categories multi-picker
      TC_meta_boxes::tc_title_view( array(
          'title_text' => __( 'Apply a category filter to your posts slider', 'customizr-pro' ),
      ));
      */

      /* HTML: Remove the following block when Customizr will merge the *_view methdos */
    ?>
      <?php //Section title ?>
      <div class="meta-box-item-title">
        <h3><?php _e( 'Slider of posts settings', 'customizr-pro' ); ?></h3>
      </div>
      <?php echo '<hr>'; ?>

      <?php //Number of posts to display ?>
      <div class="meta-box-item-title">
        <h4><?php _e( 'Number of posts to display', 'customizr-pro' ); ?></h4>
      </div>
      <div class="meta-box-item-content">
        <input name="<?php echo esc_attr( $number_of_posts_id ) ; ?>" id="<?php echo esc_attr( $number_of_posts_id ); ?>" value="<?php echo $number_of_posts_value ?>" type = "number"/>
      </div>
      <?php //Sticky posts ?>
      <div class="meta-box-item-title">
        <h4><?php _e( 'Include only sticky posts', 'customizr-pro' ); ?></h4>
      </div>
      <div class="meta-box-item-content">
        <input name="<?php echo esc_attr( $sticky_only_id ); ?>" type="hidden" value="0"/>
        <input name="<?php echo esc_attr( $sticky_only_id ); ?>" id="<?php echo esc_attr( $sticky_only_id ); ?>" type="checkbox" class="iphonecheck" value="1" <?php checked( $sticky_only_value, $current = true, $echo = true ) ?>/>
        <?php printf('<p><i>%1$s <a href="https://codex.wordpress.org/Sticky_Posts" target="_blank">%2$s</a></p></i>',
                             __( 'You can choose to display only the sticky posts. If you\'re not sure how to set a sticky post, check', 'customizr-pro' ),
                             __('the WordPress documentation.', 'customizr-pro' )
                         );
        ?>
      </div>
      <?php //Display title ?>
      <div class="meta-box-item-title">
        <h4><?php _e( 'Display the title', 'customizr-pro' ); ?></h4>
      </div>
      <div class="meta-box-item-content">
        <input name="<?php echo esc_attr( $display_title_id ); ?>" type="hidden" value="0"/>
        <input name="<?php echo esc_attr( $display_title_id ); ?>" id="<?php echo esc_attr( $display_title_id ); ?>" type="checkbox" class="iphonecheck" value="1" <?php checked( $display_title_value, $current = true, $echo = true ) ?>/>
      </div>
      <?php //Display excerpt ?>
      <div class="meta-box-item-title">
        <h4><?php _e( 'Display the excerpt', 'customizr-pro' ); ?></h4>
      </div>
      <div class="meta-box-item-content">
        <input name="<?php echo esc_attr( $display_excerpt_id ); ?>" type="hidden" value="0"/>
        <input name="<?php echo esc_attr( $display_excerpt_id ); ?>" id="<?php echo esc_attr( $display_excerpt_id ); ?>" type="checkbox" class="iphonecheck" value="1" <?php checked( $display_excerpt_value, $current = true, $echo = true ) ?>/>
      </div>
      <?php //Type of link ?>
      <div class="meta-box-item-title">
        <h4><?php _e( 'Link post with', 'customizr-pro' ); ?></h4>
      </div>
      <div class="meta-box-item-content">
        <select name="<?php echo esc_attr( $type_of_link_id); ?>" id="<?php echo esc_attr( $type_of_link_id ); ?>">
          <?php foreach( $type_of_link_choices as $type => $label ) : ?>
            <option value="<?php echo esc_attr( $type ); ?>" <?php selected( $type_of_link_value, $type, $echo = true ) ?>><?php echo esc_attr( $label ) ?></option>
         <?php endforeach; ?>
        </select>
      </div>
      <?php //Button text ?>
      <div class="meta-box-item-title">
        <h4><?php _e( 'Button text (80 char. max length)', 'customizr-pro' ); ?></h4>
      </div>
      <div class="meta-box-item-content">
        <input class="widefat" name="<?php echo esc_attr( $button_text_id); ?>" id="<?php echo esc_attr( $button_text_id); ?>" value="<?php echo esc_attr( $button_text_value); ?>" style="width:50%" type="text">
      </div>
      <?php //Categories multi-picker ?>
      <div class="meta-box-item-title">
        <h4><?php _e( 'Apply a category filter to your posts slider', 'customizr-pro' ); ?></h4>
      </div>
      <?php
      /* end of HTML */
      $cat_dropdown = wp_dropdown_categories(
        array(
              'name'               => $categories_id . '[]', //[] needed to send an array via $_POST
              'id'                 => $categories_id,
              //hide empty, set it to false to avoid complains
              'hide_empty'         => 0 ,
              'echo'               => 0 ,
              'walker'             => new TC_Walker_CategoryDropdown_Multipicker(),
              'hierarchical'       => 1,
              'class'              => 'select2 tc_multiple_picker widefat',
              'selected'           => implode(',', $categories_value )
        )
      );
      echo str_replace( '<select', '<select multiple="multiple" style="width:50%"', $cat_dropdown );
    }//end function




    /*****************************************
    * SETTERS / GETTERS / HELPERS / RESOURCES
    *****************************************/



    function tc_pro_slider_get_posts_with_posts_slider(){
      global $wpdb;

      //query to retrieve post_id(s) we'll use to build the transient
      $sql = $wpdb -> prepare(
          "
          SELECT t1.post_id FROM $wpdb->postmeta t1, $wpdb->postmeta t2
          WHERE t1.post_id = t2.post_id
          AND t2.meta_value = %s
          AND t2.meta_key = %s AND t1.meta_key = %s",
          'tc_posts_slider', 'post_slider_key', 'post_slider_posts_key'
      );
      //cache this with wp_cache?
      $posts_with_slider = $wpdb->get_col( $sql );

      return $posts_with_slider;
    }



    function tc_pro_slider_admin_scripts(){

      if( ! apply_filters( 'tc_pro_slider_enqueue_select2_res', true ) )
        return;

      $_min_version = ( defined('WP_DEBUG') && true === WP_DEBUG ) ? '' : '.min';


      //select2 stylesheet
      wp_enqueue_style(
        'tc-select2-css',
        sprintf( CZR_BASE_URL . CZR_ASSETS_PREFIX . '/czr/css/lib/select2%1$s.css', $_min_version ),
        CUSTOMIZR_VER,
        array(),
        $media = 'all'
      );
      //select2 script
      wp_enqueue_script(
        'selecter-script',
        //dev / debug mode mode?
        sprintf( CZR_BASE_URL . CZR_ASSETS_PREFIX . '/back/js/lib/select2%1$s.js', $_min_version ),
        $deps = array('jquery'),
        CUSTOMIZR_VER,
        $in_footer = true
      );
    }
  }//end class
endif;

/* In Customizr the following should be moved in a more convenient class, accessible in the backend */
/**
 * @ dropdown multi-select walker
 * Create HTML dropdown list of Categories.
 *
 * @package WordPress
 * @since 2.1.0
 * @uses Walker
 *
 * we need to allow more than one "selected" attribute
 */
if ( ! class_exists( 'TC_Walker_CategoryDropdown_Multipicker' ) ) :
  class TC_Walker_CategoryDropdown_Multipicker extends Walker_CategoryDropdown {
    /**
     * Start the element output.
     *
     * @Override
     *
     * @see Walker::start_el()
     *
     * @param string $output   Passed by reference. Used to append additional content.
     * @param object $category Category data object.
     * @param int    $depth    Depth of category. Used for padding.
     * @param array  $args     Uses 'selected', 'show_count', and 'value_field' keys, if they exist.
     *                         See {@see wp_dropdown_categories()}.
     */
    public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
      $pad = str_repeat('&mdash;', $depth );
      /** This filter is documented in wp-includes/category-template.php */
      $cat_name = apply_filters( 'list_cats', $category->name, $category );

      $value_field = 'term_id';

      $output .= "\t<option class=\"level-$depth\" value=\"" . esc_attr( $category->{$value_field} ) . "\"";
      //Treat selected arg as array
      if ( in_array( (string) $category->{$value_field}, explode( ',', $args['selected'] ) ) )
        $output .= ' selected="selected"';

      $output .= '>';
      $output .= $pad.$cat_name;
      if ( $args['show_count'] )
        $output .= '&nbsp;&nbsp;('. number_format_i18n( $category->count ) .')';
      $output .= "</option>\n";
    }
  }
endif;
