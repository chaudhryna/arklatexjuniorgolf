<?php
class CZR_footer_credits_model_class extends CZR_Model {

      protected  $fc_copyright_text;
      protected  $fc_site_link;
      protected  $fc_site_link_target;
      protected  $fc_copyright_after_text;
      protected  $fc_copyright_after_separator_class;
      protected  $fc_show_copyright_after;
      protected  $fc_site_name;
      protected  $fc_show_designer_credits;
      protected  $fc_credit_text;
      protected  $fc_designer_link;
      protected  $fc_designer_name;
      protected  $fc_designer_link_target;
      protected  $fc_designer_class;
      protected  $fc_show_wp_powered;
      protected  $fc_wp_powered_separator_class;
      protected  $fc_wp_powered_class;

      /**
      * @override
      * fired before the model properties are parsed
      *
      * return model params array()
      */
      function czr_fn_extend_params( $model = array() ) {

            $_model = array();
            //get saved options and apply $_opt filter to them
            //this way we have two filters we can act on
            //a) tc_opt_{$_opt} to globally filter the options
            //b) $_opt to filter only the displayed option value ( used by q-translateX )
            foreach ( TC_fc::$instance -> default_options as $_opt => $_default_value ) {
                  $_model[$_opt] = apply_filters( $_opt, czr_fn_opt($_opt) );
            }

            //silent aborting
            if ( 1 != $_model['fc_show_footer_credits'] ) {
                  $model[ 'id' ] = FALSE;
                  return $model;
            }


            //sanitize some options
            foreach ( array( 'copyright', 'credit', 'copyright_after' ) as $text_option ) {
                  //we allow html in the text options
                  $_model[ "fc_{$text_option}_text" ] = isset( $_model[ "fc_{$text_option}_text" ] ) ? html_entity_decode( esc_attr( $_model[ "fc_{$text_option}_text" ] ) ) : '';
            }

            foreach ( array( 'site', 'designer' ) as $name_option ) {
                  $_model[ "fc_{$name_option}_name" ] = esc_attr( $_model[ "fc_{$name_option}_name" ] );
            }

            //sanitize urls
            foreach ( array( 'site', 'designer' ) as $link_option ) {
                  $_model[ "fc_{$link_option}_link" ] = esc_url( $_model[ "fc_{$link_option}_link" ] );
            }

            $_model[ 'fc_show_copyright_after' ] = isset( $_model[ 'fc_copyright_after_text' ] ) && !empty( $_model[ 'fc_copyright_after_text' ] );

            //targets computing
            foreach ( array( 'site', 'designer' ) as $target_option ) {
                  $_model[ "fc_{$target_option}_link_target" ] = 0 == esc_attr( $_model[ "fc_{$target_option}_link_target" ] ) ? '_self' : '_blank';
            }


            //separators visibility
            $_model[ 'fc_wp_powered_sep_class' ]            = isset( $_model[ 'fc_show_wp_powered' ] ) && $_model[ 'fc_show_wp_powered' ] && isset( $_model[ 'fc_show_designer_credits' ] ) && $_model[ 'fc_show_designer_credits' ] ? '' : 'hidden';
            $_model[ 'fc_copyright_after_sep_class' ]       = !empty( $_model[ 'fc_copyright_after_text' ] ) && ( !empty( $_model[ 'fc_copyright_text' ] ) || !empty( $_model[ 'fc_site_name' ] ) ) ? '' : 'hidden';


            // When customizing we display some elements but hide them through CSS class
            if ( PC_pro_bundle::$instance -> is_customizing ) {
                  $_model[ 'fc_designer_class' ]            = isset( $_model[ 'fc_show_designer_credits' ] ) && $_model[ 'fc_show_designer_credits' ]? '' : 'hidden';
                  $_model[ 'fc_wp_powered_class' ]          = isset( $_model[ 'fc_show_wp_powered' ] ) && $_model[ 'fc_show_wp_powered' ] ? '' : 'hidden';
                  $_model[ 'fc_show_wp_powered' ]           = true;
                  $_model[ 'fc_show_designer_credits' ]     = true;
                  $_model[ 'fc_show_copyright_after' ]      = true;
            }
            return array_merge( $model, $_model );
      }

}