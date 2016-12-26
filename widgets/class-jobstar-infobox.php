<?php
use Carbon_Fields\Widget;
use Carbon_Fields\Field;

/**
 * Jobstar widget : Info Box
 *
 * @link       http://purithemes.com/
 * @since      1.0.0
 *
 * @package    Jobstar_Core
 * @subpackage Jobstar_Core/widgets
 */

class Jobstar_Infobox_Widget extends Widget {
  
  /**
	 * Set the widget options admin area.
	 *
	 * @since    1.0.0
	 */
  protected $print_wrappers = false;
  
  /**
	 * Set the widget options admin area.
	 *
	 * @since    1.0.0
	 */
  protected $form_options = array( 'width' => 420 );
  
  /**
	 * Class constructor.
   * Setup widget filed options admin area.
	 *
	 * @since    1.0.0
	 */
  function __construct() {
    
    $this->setup(
      esc_attr__( 'Jobstar : Info Box', 'jobstar-core' ),
      esc_attr__( 'Standard text block with style.', 'jobstar-core' ),
      array(
        
        Field::make( 'text', 'title', esc_attr__( 'Title', 'jobstar-core' ) ),
        Field::make( 'textarea', 'content', esc_attr__( 'Content', 'jobstar-core' ) )->set_rows(15),
        
        Field::make( 'color', 'bg_color', esc_attr__( 'Background', 'jobstar-core' ) )
        ->set_default_value( '#eeeeee' )->set_width(30),
        
        Field::make( 'checkbox', 'autop', esc_attr__( 'Automatically add paragraphs', 'jobstar-core' ) )
        ->set_option_value( 'yes' )
        ->set_default_value( '' )
        ->set_width(70)
        ->help_text( esc_html__( 'Automatically add &lt;p&gt; tag to new line.', 'jobstar-core' ) )
      )
    );
    
  }
  
  /**
	 * Function to show public frontend content of widget.
	 *
	 * @since    1.0.0
   * @return   void
	 */
  function front_end( $args, $instance ) {
    
    $bg_color = !empty( $instance['bg_color'] ) ? jobstar_esc_color( $instance['bg_color'] ) : '#eee';
    echo '<div class="widget infobox-wrap clearfix" style="background-color:'.esc_attr($bg_color).'">';
    
    if ( ! empty( $instance['title'] ) ) {
      echo $args['before_title'] . $instance['title'] . $args['after_title'];
    }
    
    $content = do_shortcode( $instance['content'] );
    
    echo ( 'yes' === $instance['autop'] ) ? wpautop( $content ) : $content;
    
    echo '</div>';
  }
  
}