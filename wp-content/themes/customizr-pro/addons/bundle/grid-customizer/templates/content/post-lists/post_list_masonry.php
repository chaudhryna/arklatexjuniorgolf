<?php
/**
 * The template for displaying the masonry article wrapper
 *
 * In WP loop
 *
 */
?>
<?php if ( czr_fn_get_property( 'print_start_wrapper' ) ) : ?>
<div class="grid grid-container grid-container__masonry <?php czr_fn_echo('element_class') ?>"  <?php czr_fn_echo('element_attributes') ?>>
  <div class="masonry__wrapper row">
<?php endif ?>
    <article <?php czr_fn_echo( 'article_selectors' ) ?> >
      <div class="sections-wrapper grid__item">
      <?php
          czr_fn_render_template(
            'content/common/media',

            array(
              'model_id'   => 'media',
              'reset_to_defaults' => false,
              'model_args' => array(
                'element_class'            => czr_fn_get_property( 'media_class' ),
              )
            )
            
          );
      ?>
        <section class="tc-content entry-content__holder">
          <div class="entry-content__wrapper">
          <?php
            /* header */
            czr_fn_render_template(
              'content/post-lists/item-parts/headings/post_list_item_header',
              array(
                'model_args' => array(
                  'has_header_format_icon'  => czr_fn_get_property( 'has_header_format_icon' )
                )
              )
            );
            /* content inner */
            czr_fn_render_template( 'content/post-lists/item-parts/contents/post_list_item_content_inner',
              array(
                'model_args' => array(
                  'content_type'  => 'all'                  
                )
              )
            );

            /* footer */
            czr_fn_render_template( 'content/post-lists/item-parts/footers/post_list_item_footer' );
          ?>
          </div>
        </section>
      </div>
    </article>
<?php if ( czr_fn_get_property( 'print_end_wrapper' ) ) : ?>
  </div>
</div>
<?php endif ?>