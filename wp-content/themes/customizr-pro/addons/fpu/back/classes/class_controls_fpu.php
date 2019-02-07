<?php
/**
* Add controls to customizer
*
*
* @package      FPU
* @subpackage   classes
* @since        1.0
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    copyright (c) 2013-2015 Nicolas GUILLAUME
*/

class TC_controls_fpu extends WP_Customize_Control	{
    public $type;
    public $link;
    public $title;
    public $label;
    public $buttontext;
    public $settings;
    public $hr_after;
    public $notice;
    //number vars
    public $step;
    public $min;

    private static $tc_all_posts;

    public function render_content()  {
    	$plug_option_prefix     = TC_fpu::$instance -> plug_option_prefix;
    	$setting 				= str_replace( array('data-customize-setting-link=', $plug_option_prefix, '"' , "[" , "]" ) , '', $this -> get_link() );

    	$titles 				= array(
    		'tc_fp_position' 		=> __( 'Location, number &amp; layout' , 'customizr-pro' ),
    		'tc_fp_background' 		=> __( 'Main colors', 'customizr-pro' ),
    		'tc_show_fp_img' 		=> __( 'Thumbnails' , 'customizr-pro' ),
    		'tc_show_fp_title' 		=> __( 'Title and excerpt' , 'customizr-pro' ),
    		'tc_show_fp_button' 	=> __( 'Buttons' , 'customizr-pro' ),
    		'tc_featured_page_one' 	=> __( 'Featured pages selection' , 'customizr-pro' ),
    	);

    	if ( isset($titles[$setting]) ) {
    		printf('<h3 class="fpc-section-title">&middot; %1$s &middot;</h3>',
    			$titles[$setting]
    		);
    	}

        switch ( $this -> type) {
        	case 'hr':
        		echo '<hr class="tc-customizer-separator" />';
        		break;

        	case 'text':
				?>
				<label>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<input type="text" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> />
				</label>
				<?php if (isset( $this->notice)) : ?>
					<span class="tc-notice"><?php echo esc_html( $this-> notice ) ?></span>
				<?php endif; ?>
				<?php
				break;

        	case 'title' :
        		?>

        		<?php if (isset( $this->title)) : ?>
					<h3 class="tc-customizr-title"><?php echo esc_html( $this->title); ?></h3>
				<?php endif; ?>
				<?php if (isset( $this->notice)) : ?>
					<i class="tc-notice"><?php echo esc_html( $this-> notice ) ?></i>
				<?php endif; ?>

				<?php
				break;


        	case 'button':
        		echo '<a class="button-primary" href="'.admin_url( $this -> link ).'" target="_blank">'.$this -> buttontext.'</a>';
        		if ( $this -> hr_after == true)
        			echo '<hr class="tc-after-button">';
        		break;


        	case 'select':
				if ( empty( $this->choices ) )
					return;

				?>
				<?php if (isset( $this->title)) : ?>
					<h3 class="tc-customizr-title"><?php echo esc_html( $this->title); ?></h3>
				<?php endif; ?>
				<?php if (isset( $this->notice)) : ?>
					<span class="tc-notice"><?php echo esc_html( $this-> notice ) ?></span>
				<?php endif; ?>
				<label>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<select <?php $this->link(); ?>>
						<?php
						foreach ( $this->choices as $value => $label )
							echo '<option value="' . esc_attr( $value ) . '"' . selected( $this->value(), $value, false ) . '>' . $label . '</option>';
						?>
					</select>
				</label>
				<?php
			break;


        	case 'number':
        		?>
        		<label>
        			<span class="tc-number-label customize-control-title"><?php echo esc_html( $this->label ) ?></span>
	        		<input <?php $this->link() ?> type="number" step="<?php echo $this-> step ?>" min="<?php echo $this-> min ?>" id="posts_per_page" value="<?php echo $this->value() ?>" class="tc-number-input small-text">
	        		<?php if(!empty( $this -> notice)) : ?>
		        		<span class="tc-notice"><?php echo esc_html( $this-> notice ) ?></span>
		        	<?php endif; ?>
	        	</label>
	        	<?php
        		break;

        	case 'checkbox':
			?>
				<div class="tc-check-label">
					<label>
						<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					</label>
				</div>
				<input type="checkbox" class="iphonecheck" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); checked( $this->value() ); ?> />

				<?php if(!empty( $this -> notice)) : ?>
			       <span class="tc-notice"><?php echo esc_html( $this-> notice ) ?></span>
			     <?php endif; ?>
			     <!-- <hr class="tc-customizer-separator-invisible" /> -->
			<?php
			break;

        	case 'textarea':
        		?>
				<label>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<span class="tc-notice"><?php echo esc_html( $this-> notice); ?></span>
					<textarea class="widefat" rows="3" cols="10" <?php $this->link(); ?>><?php echo esc_html( $this->value() ); ?></textarea>
				</label>
				<?php
	        	break;

        	case 'url':
        		?>
				<label>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<input type="text" value="<?php echo esc_url( $this->value() ); ?>"  <?php $this->link(); ?> />
				</label>
				<?php
	        	break;

	        case 'dropdown-posts-pages':
                //retrieve post, pages and custom post types (if any) and generate the ordered select list for the button link
                if ( ! isset(self::$tc_all_posts) ){
                  $exclude    =  apply_filters('fpc_excluded_post_types', array('attachment', 'product') );
                  $post_types = ( array_diff(
                        array_values( get_post_types( array( 'public' => true ) ) ),
                        $exclude
                     )
                  );
                  global $wpdb;
                  $_join  = apply_filters( 'fpu_control_get_posts_join', '', $post_types );
                  $_where = "WHERE posts.post_status = 'publish' AND posts.post_type IN ('" . implode("', '", array_map( 'strval', $post_types) ) ."')";
                  $_where = apply_filters( 'fpu_control_get_posts_where', $_where, $post_types );
                  self::$tc_all_posts = $wpdb->get_results( "
                      SELECT posts.ID, posts.post_title, posts.post_type, posts.post_date
                      FROM $wpdb->posts as posts
                      $_join
                      $_where
                      ORDER BY posts.post_type, posts.post_date"
                  );
                }
		          ?>
			         <label>
						<span class="customize-control-title"><?php echo esc_html( $this->label );?></span>
				          <select <?php echo $this->link() ?>>
			                <?php //no link option ?>
			                <option value="0" <?php selected( $this->value(), 0, $echo = true ) ?>> &#45; Select &#45; </option>
			                <?php foreach( self::$tc_all_posts as $item) : ?>
			                  		<option value="<?php echo $item -> ID; ?>" <?php selected( $this->value(), $item -> ID, $echo = true ) ?>>{<?php echo esc_attr( $item -> post_type) ;?>}&nbsp;<?php echo esc_attr( $item -> post_title); ?></option>

			               <?php endforeach; ?>

			              </select>
					</label>
		          <?php
            break;

        	default:
        		screen_icon( $this -> type );
        		break;
        }//end switch
	 }//end function
}//end of class



class TC_Color_Control extends WP_Customize_Color_Control	{
	public $notice;
	public $no_hr;

	/**
	 * Render the control's content.
	 *
	 */
	public function render_content() {
		$plug_option_prefix     = TC_fpu::$instance -> plug_option_prefix;
    	$setting 				= str_replace( array('data-customize-setting-link=', $plug_option_prefix, '"' , "[" , "]" ) , '', $this -> get_link() );

    	$titles 				= array(
    		'tc_fp_position' 		=> __( 'Location, number &amp; layout' , 'customizr-pro' ),
    		'tc_fp_background' 		=> __( 'Main colors', 'customizr-pro' ),
    		'tc_show_fp_img' 		=> __( 'Thumbnails' , 'customizr-pro' ),
    		'tc_show_fp_title' 		=> __( 'Titles and excerpts' , 'customizr-pro' ),
    		'tc_show_fp_button' 	=> __( 'Buttons' , 'customizr-pro' ),
    		'tc_featured_page_one' 	=> __( 'Featured pages selection' , 'customizr-pro' ),
    	);

    	if ( isset($titles[$setting]) ) {
    		printf('<h3 class="fpc-section-title">&middot; %1$s &middot;</h3>',
    			$titles[$setting]
    		);
    	}

		$this_default = $this->setting->default;
		$default_attr = '';
		if ( $this_default ) {
			if ( false === strpos( $this_default, '#' ) )
				$this_default = '#' . $this_default;
			$default_attr = ' data-default-color="' . esc_attr( $this_default ) . '"';
		}
		// The input's value gets set by JS. Don't fill it.
		?>
		<label>
			<span class="tc-skin-gen-label customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<div class="tc-skin-gen-color-picker customize-control-content">
				<input class="color-picker-hex" type="text" maxlength="7" placeholder="<?php esc_attr_e( 'Hex Value', 'customizr-pro' ); ?>"<?php echo $default_attr; ?> />
			</div>
			<?php if(!empty( $this -> notice)) : ?>
			   <span class="tc-notice"><?php echo esc_html( $this-> notice ) ?></span>
			<?php endif; ?>
		</label>
		<?php if( true != $this -> no_hr ) : ?>
			<!-- <hr class="tc-customizer-separator-invisible" /> -->
		<?php endif; ?>
		<?php
	}
}//end of class
