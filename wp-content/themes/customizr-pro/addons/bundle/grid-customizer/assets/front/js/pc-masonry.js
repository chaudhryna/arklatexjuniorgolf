var czrapp = czrapp || {};
/************************************************
* MASONRY GRID SUB CLASS
*************************************************/
/*
* In this script we fire the grid masonry on the grid only when all the images
* therein are fully loaded in case we're not using the images on scroll loading
* Imho would be better use a reliable plugin like imagesLoaded (from the same masonry's author)
* which addresses various cases, failing etc, as it is not very big. Or at least dive into it
* to see if it really suits our needs.
*
* We can use different approaches while the images are loaded:
* 1) loading animation
* 2) display the grid in a standard way (organized in rows) and modify che html once the masonry is fired.
* 3) use namespaced events
* This way we "ensure" a compatibility with browsers not running js
*
* Or we can also fire the masonry at the start and re-fire it once the images are loaded
*/
(function( $, czrapp ) {
    var _methods =  {

        initOnCzrReady : function() {

            if ( typeof undefined === typeof $.fn.masonry )
                  return;

            var $grid_container = $('.masonry__wrapper'),
                masonryReady = $.Deferred(),
                _isMobileOnPageLoad = czrapp.base.matchMedia && czrapp.base.matchMedia(575),//<=prevent any masonry allowed on resize or device swap afterwards
                _debouncedMasonryLayoutRefresh = _.debounce( function(){
                                $grid_container.masonry( 'layout' );
                }, 200 );

            if ( 1 > $grid_container.length ) {
                  czrapp.errorLog('Masonry container does not exist in the DOM.');
                  return;
            }

            $grid_container.bind( 'masonry-init.customizr', function() {
                  masonryReady.resolve();
            });

            //Init Masonry on imagesLoaded
            //@see https://github.com/desandro/imagesloaded
            //
            //Even if masonry is not fired, let's emit the event anyway
            //It might be listen to !
            $grid_container.imagesLoaded( function() {
                  if ( ! _isMobileOnPageLoad ) {
                        // init Masonry after all images have loaded
                        $grid_container.masonry({
                              itemSelector: '.grid-item',
                              //to avoid scale transition of the masonry elements when revealed (by masonry.js) after appending
                              hiddenStyle: { opacity: 0 },
                              visibleStyle: { opacity: 1 },
                        })
                        //Refresh layout on image loading
                        .on( 'smartload simple_load', 'img', function(evt) {
                              //We don't need to refresh the masonry layout for images in containers with fixed aspect ratio
                              //as they won't alter the items size. These containers are those .grid-item with full-image class
                              if ( $(this).closest( '.grid-item' ).hasClass( 'full-image' ) ) {
                                    return;
                              }
                              _debouncedMasonryLayoutRefresh();
                        });
                  }
                  $grid_container.trigger( 'masonry-init.customizr' );
            });

            //Reacts to the infinite post appended
            czrapp.$_body.on( 'post-load', function( evt, data ) {
                  var _do = function( evt, data ) {
                      if( data && data.type && 'success' == data.type && data.collection && data.html ) {
                            if ( ! _isMobileOnPageLoad ) {
                                  //get jquery items from the collection which is like

                                  //[ post-ID1, post-ID2, ..]
                                  //we grab the jQuery elements with those ids in our $grid_container
                                  var $_items = $( data.collection.join(), $grid_container );

                                  if ( $_items.length > 0 ) {
                                        $_items.imagesLoaded( function() {
                                              //inform masonry that items have been appended: will also re-layout
                                              $grid_container.masonry( 'appended', $_items )
                                                             //fire masonry done passing our data (we'll listen to this to trigger the animation)
                                                             .trigger( 'masonry.customizr', data );

                                              setTimeout( function(){
                                                    //trigger scroll
                                                    $(window).trigger('scroll.infinity');
                                              }, 150);
                                        });
                                  }
                            } else {
                                //even if masonry is disabled we still need to emit 'masonry.customizr' because listened to by the infinite code to trigger the animation
                                //@see pc-pro-bundle/infinite/init-pro-infinite.php
                                $grid_container.imagesLoaded( function() { $grid_container.trigger( 'masonry.customizr', data ); } );
                            }
                      }//if data
                };
                if ( 'resolved' == masonryReady.state() ) {
                      _do( evt, data );
                } else {
                      masonryReady.then( function() {
                            _do( evt, data );
                      });
                }
            });

        }
    };//_methods{}


    czrapp.methods.MasonryGrid = {};
    $.extend( czrapp.methods.MasonryGrid , _methods );

    //Instantiate and fire on czrapp ready
    czrapp.Base.extend( czrapp.methods.MasonryGrid );
    czrapp.ready.done( function() {
      czrapp.methods.MasonryGrid.initOnCzrReady();
    });

})( jQuery, czrapp );