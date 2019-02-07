/*! Menu Customizer preview by Nicolas Guillaume - Rocco Aliberti, GPL2+ licensed */
( function( $ ) {
      var api                     = wp.customize,
          OptionPrefix            = PCMCPreviewParams.OptionPrefix,
          $_body                  = $('body'),
          $_bmenu                 = $('.tc-header [data-toggle="sidenav"]'),
          _excl_page_push_effects = [ 'mc_slide_top' ], //excluded push effects : effects that do not require the #tc-page-wrap to be pushed
          _page_push_class        = 'mc_page_push';

      api( OptionPrefix + '[tc_mc_effect]' , function( value ) {

            value.bind( function( to ) {
                /*
                * change the current on open effect
                * this means change the sidenav class sn-left|right(-EFFECT)
                * If already open, before the replacement takes place, we close the sidenav,
                * and simulate a click to re-open it afterwards
                */
                  var _refresh                  = false,
                      _current_effect           = $_body.attr('class').match(/sn-(left|right)-(mc_\w+)($|\s)/);


                  if ( ! ( _current_effect && _current_effect.length > 2 ) )
                    return;

                  var _to_remove_classes        = _current_effect[0],
                      _to_add_classes           = _current_effect[0].replace( _current_effect[2] , to );


                  if ( $_body.hasClass('tc-sn-visible') ) {

                        $_body.removeClass('tc-sn-visible');
                        _refresh = true;

                  }

                  //if the new effect doesn't push the page wrapper, remove the page push selector
                  if ( _.contains( _excl_page_push_effects, to ) ) {

                        _to_remove_classes         += ' ' + _page_push_class;
                  }
                  //else, if the new effect pushes the page wrapper, add teh page push selector
                  else {

                        _to_add_classes            += ' ' + _page_push_class;

                  }



                  $_body.removeClass( _to_remove_classes ).
                         addClass( _to_add_classes );

                  //maybe re-open the sidenav to show the new effect in place
                  if ( _refresh ) {

                        setTimeout( function(){

                              $_bmenu.trigger('click');

                        }, 300);

                  }

            });

      });
})( jQuery );
