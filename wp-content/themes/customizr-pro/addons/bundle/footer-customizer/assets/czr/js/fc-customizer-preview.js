/*! Footer Customizer plugin by Nicolas Guillaume, GPL2+ licensed */
( function( api, $ ) {
  api( 'tc_theme_options[fc_copyright_text]' , function( value ) {
    value.bind( function( to ) {
      $( '.fc-copyright-text' ).html( to );
      _set_separator_visibility( 'fc_copyright_after_text', [ 'fc_copyright_text', 'fc_site_name' ],'.fc-copyright-after-text' );
    } );
  } );
  api( 'tc_theme_options[fc_site_name]' , function( value ) {
    value.bind( function( to ) {
      $( '.fc-copyright-link' ).html( to );
      _set_separator_visibility( 'fc_copyright_after_text', [ 'fc_copyright_text', 'fc_site_name' ],'.fc-copyright-after-text' );
    } );
  } );
  api( 'tc_theme_options[fc_site_link]' , function( value ) {
    value.bind( function( to ) {
      $( '.fc-copyright-link' ).attr( 'href' , to );
    } );
  } );
  api( 'tc_theme_options[fc_site_link_target]' , function( value ) {
    value.bind( function( to ) {
      $( '.fc-copyright-link' ).attr( 'target' , 1 == to ? '_blank' : '_self' );
    } );
  } );
  api( 'tc_theme_options[fc_copyright_after_text]' , function( value ) {
    value.bind( function( to ) {
      $( '.fc-copyright-after-text' ).html( to );
      _set_separator_visibility( 'fc_copyright_after_text', [ 'fc_copyright_text', 'fc_site_name' ],'.fc-copyright-after-text' );
    } );
  } );
  api( 'tc_theme_options[fc_show_designer_credits]', function( value ) {
    value.bind( function( to ) {
      $( '.fc-designer' ).toggleClass('hidden');
      _set_separator_visibility( 'fc_show_wp_powered', [ 'fc_designer_name', 'fc_credit_text', 'fc_show_designer_credits' ], '.fc-wp-powered' );
      _set_separator_visibility( 'fc_show_designer_credits', ['fc_show_wp_powered'], '.fc-wp-powered' );
    } );
  } );
  api( 'tc_theme_options[fc_credit_text]' , function( value ) {
    value.bind( function( to ) {
      $( '.fc-credits-text' ).html( to );
      _set_separator_visibility( 'fc_show_wp_powered', [ 'fc_designer_name', 'fc_credit_text', 'fc_show_designer_credits' ], '.fc-wp-powered' );
      _set_separator_visibility( 'fc_show_designer_credits', ['fc_show_wp_powered'], '.fc-wp-powered' );
    } );
  } );
  api( 'tc_theme_options[fc_designer_name]' , function( value ) {
    value.bind( function( to ) {
      $( '.fc-credits-link' ).html( to );
      _set_separator_visibility( 'fc_show_wp_powered', [ 'fc_designer_name', 'fc_credit_text', 'fc_show_designer_credits' ], '.fc-wp-powered' );
      _set_separator_visibility( 'fc_show_designer_credits', ['fc_show_wp_powered'], '.fc-wp-powered' );
    } );
  } );
  api( 'tc_theme_options[fc_designer_link]' , function( value ) {
    value.bind( function( to ) {
      $( '.fc-credits-link' ).attr( 'href' , to );
    } );
  } );
  api( 'tc_theme_options[fc_designer_link_target]' , function( value ) {
    value.bind( function( to ) {
      $( '.fc-credits-link' ).attr( 'target' , 1 == to ? '_blank' : '_self' );
    } );
  } );
  api( 'tc_theme_options[fc_show_wp_powered]', function( value ) {
    value.bind( function( to ) {
      $( '.fc-wp-powered' ).toggleClass('hidden');
      _set_separator_visibility( 'fc_show_wp_powered', [ 'fc_designer_name', 'fc_credit_text', 'fc_show_designer_credits' ], '.fc-wp-powered' );
      _set_separator_visibility( 'fc_show_designer_credits', ['fc_show_wp_powered'], '.fc-wp-powered' );
    } );
  } );


  function _set_separator_visibility( master, _settings, _el_after_selector ) {

    var _master = master ? api( api.CZR_preview.prototype._build_setId( master ) ) : false ,
        _do_hide = !_master ? false : _.isEmpty( _.compact( [ _master.get() ] ) );

    _do_hide = _do_hide || _.reduce( _settings,
            function( memo, setting ) {
              var _setting = api( api.CZR_preview.prototype._build_setId(setting) );

              if ( !_setting )
                return false;

              return memo && _.isEmpty( _.compact( [ _setting.get() ] ) );
            },
            true
    );

    $( _el_after_selector ).prev( '.fc-separator' ).toggleClass( 'hidden', _do_hide );

  }

}) ( wp.customize, jQuery, _);
