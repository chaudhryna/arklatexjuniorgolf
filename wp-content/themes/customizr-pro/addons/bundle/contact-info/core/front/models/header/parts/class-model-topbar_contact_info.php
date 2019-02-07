<?php
class CZR_topbar_contact_info_model_class extends CZR_Model {
    /*
    * @override
    * fired before the model properties are parsed
    *
    * return model params array()
    */
    function czr_fn_extend_params( $model = array() ) {
        //based on the model hook and the options we set different classes, mainly for visibility
        if ( empty( $model[ 'hook' ] ) )
            return FALSE;

        $element_class = array();

        //the controller already allowed this model, so we're sure we're matching the main visibility conditions
        switch ( $model[ 'hook' ] ) :

            //before the WHOLE topbar row which contains: menu, tagline, socials, cart
            case '__before_topbar_navbar_row' :
                //never displayed in mobiles
                $element_class = array( 'd-none', 'd-lg-flex' );
                break;

            //in topbar navbar row, but before any other elements like: menu, tagline, socials, cart
            case '__before_topbar_navbar_row_inner' :
                //we're in a row, let's add column classes
                $element_class = array( 'col', 'col-auto' );
                /*
                * This model is allowed when
                * a) no topbar_menu
                *   1) ci displayed in desktop => hide in mobiles
                *   2) ci displayed in mobiles => hide in desktops
                *   3) ci displayed in mobiles and desktops => no class to add
                * b) topbar menu => ci always hidden in desktops (in desktops it will be displayed before the topbar navbar row)
                */
                $_show_topbar_ci          = esc_attr( czr_fn_opt( 'tc_header_show_contact_info' ) );
                $_show_topbar_menu        = czr_fn_is_registered_or_possible( 'topbar_menu' );

                //a
                if ( ! $_show_topbar_menu ) {
                    switch ( $_show_topbar_ci ) :
                        //a.1
                        case 'desktop'    : $element_class  = array_merge( $element_class, array('d-none', 'd-lg-flex' ) );
                            break;
                        //a.2
                        case 'mobile'     : $element_class[] = 'd-lg-none';
                            break;
                    endswitch;
                } else {//b
                    //hide in desktop if the topbar menu is possible
                    $element_class[] = 'd-lg-none';
                }

                break;
        endswitch;

        $model[ 'element_class' ] = $element_class;
        return parent::czr_fn_extend_params( $model );
    }

}