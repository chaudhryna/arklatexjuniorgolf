<?php
/**
 * The template for displaying the topbar contact info
*/
?>
<div class="topbar-contact__info <?php czr_fn_echo( 'element_class' ) ?>">
    <?php
        czr_fn_render_template( 'modules/common/contact_info', array(
       	    'model_args' => array(
            'element_class' => 'nav header-contact__info'
        )
    ) );
   ?>
</div>