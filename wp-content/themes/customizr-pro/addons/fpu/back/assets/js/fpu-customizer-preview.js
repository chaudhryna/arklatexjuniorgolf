/*! Featured Pages Unlimited Customizer controls by Nicolas Guillaume, GPL2+ licensed */
( function( $ ) {
      var OptionPrefix        = TCFPCPreviewParams.OptionPrefix,
        //handle the two versions
        ButtonSelector        = $( '.fp-button .fpc-btn-link' ).length > 0 ? '.fp-button .fpc-btn-link' : '.fp-button',


        CurrentBtnColor       = $( ButtonSelector ).attr('data-color'),
        is_random_enabled     = $( '.fpc-widget-front' ).hasClass('tc-random-colors-enabled'),
        btn_random_override   = $( ButtonSelector, '.fpc-widget-front' ).hasClass('btn-random-override'),
        title_random_override = $( '.fpc-widget-front .fp-title' ).hasClass('text-random-override'),
        text_random_override  = $( '.fpc-widget-front .fp-excerpt' ).hasClass('text-random-override');



      //gets the param object and turn into array
      var FPpreviewParams = $.map(TCFPCPreviewParams.FPpreview, function(value) {
            return [value];
      });

      //iterates on the array
      FPpreviewParams.forEach(ApplyPreview);

      function ApplyPreview(element, index) {
            wp.customize( element , function( value ) {
                  value.bind( function( to ) {
                        $( '.fpc-widget-front .fp-text-' + (index + 4) ).html( to );
                  } );
            } );
      }

      //show image
      wp.customize( OptionPrefix + '[tc_show_fp_img]' , function( value ) {
            //handle the two versions
            var ThumbWrapperSelector  = $( '.fpc-widget-front .fp-thumb-wrapper' ).length > 0 ? '.fp-thumb-wrapper' : '.thumb-wrapper';

            value.bind( function( to ) {
                  if ( false === to ) {
                        $( ThumbWrapperSelector, '.fpc-widget-front' ).addClass('fpc-hide');
                  }
                  else {
                        $( ThumbWrapperSelector, '.fpc-widget-front' ).removeClass('fpc-hide').find('img').trigger('fpu-recenter');
                  }
            } );
      } );
      //featured page background
      wp.customize( OptionPrefix + '[tc_fp_background]' , function( value ) {
            var LinkMaskSelector      = $( '.fpc-marketing .fpc-widget-front .czr-link-mask' ).length > 0 ? '.czr-link-mask' : '.round-div';

            value.bind( function( to ) {
                  $(  LinkMaskSelector, '.fpc-marketing .fpc-widget-front' ).attr('style' , 'border-color:' + to + '!important');
                  $( '.fpc-container' ).attr('style' , 'background-color:' + to + '!important').data( 'bgcolor', to );
            } );
      } );

      //featured page text color
      wp.customize( OptionPrefix + '[tc_fp_text_color]' , function( value ) {
            value.bind( function( to ) {
                  if ( title_random_override || text_random_override ) {
                    $( '.fpc-marketing .fpc-widget-front .fp-title, .fpc-widget-front > .fp-excerpt' ).attr('style' , 'color:' + to + '!important');
                  }
                  else {
                    $( '.fpc-marketing .fpc-widget-front .fp-title, .fpc-widget-front > .fp-excerpt' ).attr('style' , '');
                  }
            } );
      } );

      //fp titles
      wp.customize( OptionPrefix + '[tc_show_fp_title]' , function( value ) {
            value.bind( function( to ) {
                  if ( false === to ) {
                        $( '.fpc-widget-front .fp-title' ).addClass('fpc-hide');
                  }
                  else {
                        $( '.fpc-widget-front .fp-title' ).removeClass('fpc-hide');
                  }
            } );
      } );

      //fp excerpts
      wp.customize( OptionPrefix + '[tc_show_fp_text]' , function( value ) {
            value.bind( function( to ) {
                  if ( false === to ) {
                        $( '.fpc-widget-front .fp-excerpt' ).addClass('fpc-hide');
                  }
                  else {
                        $( '.fpc-widget-front .fp-excerpt' ).removeClass('fpc-hide');
                  }
            } );
      } );
      //fp button
      wp.customize( OptionPrefix + '[tc_show_fp_button]' , function( value ) {
            value.bind( function( to ) {
                  if ( false === to ) {
                        $( '.fpc-widget-front .fp-button' ).addClass('fpc-hide');
                  }
                  else {
                        $( '.fpc-widget-front .fp-button' ).removeClass('fpc-hide');
                  }
            } );
      } );
      //button color
      wp.customize( OptionPrefix + '[tc_fp_button_color]' , function( value ) {
            value.bind( function( to ) {
                  if ( is_random_enabled && ! btn_random_override )
                        return;

                  var to_remove        = CurrentBtnColor,
                      to_add           = to;

                  if ( 'skin' == CurrentBtnColor ){
                        to_remove += ' btn btn-primary btn-more btn-skin-dark';
                        to_add    += ' fpc-btn fpc-btn-primary ';
                  }
                  else if ( 'skin' == to ){
                        to_remove += ' fpc-btn fpc-btn-primary';
                        to_add    += ' btn btn-primary btn-more btn-skin-dark';
                  }

                  $( ButtonSelector ).removeClass(to_remove).addClass(to_add);
                  $( ButtonSelector ).attr('style' , '');

                  CurrentBtnColor = to;
            } );
      } );

      //featured page button text
      wp.customize( OptionPrefix + '[tc_fp_button_text]' , function( value ) {
            value.bind( function( to ) {
                  if ( to )
                        $( '.fpc-widget-front .fp-button' ).html( to ).removeClass( 'fpc-hide' );
                  else
                        $( '.fpc-widget-front .fp-button' ).addClass( 'fpc-hide' );
            } );
      } );

      //featured page button color
      wp.customize( OptionPrefix + '[tc_fp_button_text_color]' , function( value ) {
            value.bind( function( to ) {
                  $( ButtonSelector, '.fpc-marketing .fpc-widget-front' ).attr('style' , 'color:' + to + '!important');
            } );
      } );

      //featured page one text
      wp.customize( OptionPrefix + '[tc_featured_text_one]' , function( value ) {
            value.bind( function( to ) {
                  $( '.fpc-widget-front p.fp-text-one' ).html( to );
            } );
      } );

      //featured page two text
      wp.customize( OptionPrefix + '[tc_featured_text_two]' , function( value ) {
            value.bind( function( to ) {
                  $( '.fpc-widget-front p.fp-text-two' ).html( to );
            } );
      } );

      //featured page three text
      wp.customize( OptionPrefix + '[tc_featured_text_three]' , function( value ) {
            value.bind( function( to ) {
                  $( '.fpc-widget-front p.fp-text-three' ).html( to );
            } );
      } );
      //Shape
      wp.customize( OptionPrefix + '[tc_thumb_shape]' , function( value ) {
            value.bind( function( to ) {
                  $( '.fpc-row-fluid', '.fpc-container' )
                        .removeClass("fp-rounded-expanded fp-squared fp-squared-expanded")
                        .addClass(to);
            } );
      } );
} )( jQuery );
