/*! Footer Customizer plugin by Nicolas Guillaume, GPL2+ licensed */
(function (api, $, _) {
          //@return boolean
          var _is_checked = function( to ) {
                  return 0 !== to && '0' !== to && false !== to && 'off' !== to;
          };

          //when a dominus object define both visibility and action callbacks, the visibility can return 'unchanged' for non relevant servi
           //=> when getting the visibility result, the 'unchanged' value will always be checked and resumed to the servus control current active() state
           api.CZR_ctrlDependencies.prototype.dominiDeps = _.extend(
                 api.CZR_ctrlDependencies.prototype.dominiDeps,
                 [
                     {
                             dominus : 'fc_show_footer_credits',
                             servi   : [
                               'fc_copyright_text',
                               'fc_site_name',
                               'fc_site_link',
                               'fc_site_link_target',
                               'fc_show_designer_credits',
                               'fc_credit_text',
                               'fc_designer_name',
                               'fc_designer_link',
                               'fc_designer_link_target',
                               'fc_show_wp_powered'
                             ],
                             visibility : function( to, servusShortId ) {

                                if ( !_is_checked(to) ) {
                                  return false;
                                }

                                var _to_return = true;
                                 //cross: depending on fc_show_designer credits
                                  if ( _.contains([
                                      'fc_credit_text',
                                      'fc_designer_name',
                                      'fc_designer_link',
                                      'fc_designer_link_target'], servusShortId ) ) {

                                    _to_return = _to_return && _is_checked( api( api.CZR_Helpers.build_setId('fc_show_designer_credits') ).get() );
                                  }
                                  //link targets
                                  if ( 'fc_site_link_target' == servusShortId ) {
                                      _to_return = _to_return && !_.isEmpty( api( api.CZR_Helpers.build_setId('fc_site_link') ).get() );
                                  }
                                  else if ( 'fc_designer_link_target' == servusShortId ) {
                                      _to_return = _to_return && !_.isEmpty( api( api.CZR_Helpers.build_setId('fc_designer_link') ).get() );
                                  }

                                  return _to_return;
                             },
                      },
                      {
                             dominus : 'fc_show_designer_credits',
                             servi   : [
                               'fc_credit_text',
                               'fc_designer_name',
                               'fc_designer_link',
                               'fc_designer_link_target'
                             ],
                             visibility : function( to, servusShortId ) {
                                  var _to_return = _is_checked( to ) && _is_checked( api( api.CZR_Helpers.build_setId('fc_show_footer_credits') ).get() );
                                  if ( 'fc_designer_link_target' == servusShortId ) {
                                    _to_return   = _to_return && !_.isEmpty( api( api.CZR_Helpers.build_setId('fc_designer_link') ).get() );
                                  }
                                  //with cross
                                  return _to_return;
                             },
                      },
                      {
                             dominus : 'fc_site_link',
                             servi   : [
                               'fc_site_link_target'
                             ],
                             visibility : function( to ) {
                                  return !_.isEmpty( to ) && _is_checked( api( api.CZR_Helpers.build_setId('fc_show_footer_credits') ).get() );
                             },
                      },
                      {
                             dominus : 'fc_designer_link',
                             servi   : [
                               'fc_designer_link_target'
                             ],
                             visibility : function( to ) {
                                  return !_.isEmpty( to ) && _is_checked( api( api.CZR_Helpers.build_setId('fc_show_footer_credits') ).get() ) && _is_checked( api( api.CZR_Helpers.build_setId('fc_show_designer_credits') ).get() );
                             },
                      }
                ]//dominiDeps {}
          );//_.extend()

}) ( wp.customize, jQuery, _);