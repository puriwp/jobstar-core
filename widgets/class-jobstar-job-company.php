<?php
use Carbon_Fields\Widget;
use Carbon_Fields\Field;

/**
 * Jobstar widget : Job Company
 *
 * @link       http://purithemes.com/
 * @since      1.0.0
 *
 * @package    Jobstar_Core
 * @subpackage Jobstar_Core/widgets
 */

class Jobstar_Job_Company_Widget extends Widget {
  
  /**
	 * Set the widget options admin area.
	 *
	 * @since    1.0.0
	 */
  protected $form_options = array( 'width' => 250 );
  
  /**
	 * Class constructor.
   * Setup widget filed options admin area.
	 *
	 * @since    1.0.0
	 */
  function __construct() {
    
    $this->setup(
      esc_attr__( 'Jobstar : Job Company', 'jobstar-core' ),
      esc_attr__( 'Current job company details.', 'jobstar-core' ),
      array(
        
        Field::make( 'text', 'title', esc_attr__( 'Title', 'jobstar-core' ) ),
        
        Field::make( 'set', 'items', esc_attr__( 'Displayed Items', 'jobstar-core' ) )
        ->add_options(array(
          'comp_logo'   => esc_attr__( 'Company Logo', 'jobstar-core' ),
          'comp_name'   => esc_attr__( 'Company Name', 'jobstar-core' ),
          'comp_tag'    => esc_attr__( 'Company Tagline', 'jobstar-core' ),
          'website'     => esc_attr__( 'Website URL', 'jobstar-core' ),
          'twitter'     => 'Twitter',
          'facebook'    => 'Facebook',
          'gplus'       => 'Google Plus',
          'links'       => esc_attr__( 'Company Jobs', 'jobstar-core' ),
        ))
        ->set_default_value( array( 'comp_logo', 'comp_name', 'comp_tag', 'website' ) ),
        
      ),
      'company-about'
    );
    
  }
  
  /**
	 * Function to show public frontend content of widget.
	 *
	 * @since    1.0.0
   * @return   void
	 */
  function front_end( $args, $instance ) {
    
    global $post;
    
    if ( ! is_singular( 'job_listing' ) || empty( $post ) || empty( $post->ID ) ) {
      return;
    }
    
    if ( ! empty( $instance['title'] ) ) {
      echo $args['before_title'] . $instance['title'] . $args['after_title'];
    }
    
    $items = is_array( $instance['items'] ) ? $instance['items'] : array();
    $html  = '<div id="company-about" class="company-wrap clearfix">';
    $comp_logo = get_the_company_logo( $post->ID, 'company-image' );
    if ( in_array( 'comp_logo', $items ) && !empty( $comp_logo ) ) {
      $html .= '<div class="company-logo">';
      $html .= '<img src="'.esc_url($comp_logo).'" alt="'.esc_attr(get_the_company_name( $post->ID )).'" />';
      $html .= '</div>';
    }
    $html .= in_array( 'comp_name', $items ) ? '<h4>'.sprintf( esc_html__( 'About %s', 'jobstar-core' ), get_the_company_name( $post->ID ) ).'</h4>' : '';
    $html .= in_array( 'comp_tag', $items ) ? '<p>'.esc_html( get_the_company_tagline( $post->ID ) ).'</p>' : '';
    if ( count(array_intersect( $items, array( 'website', 'facebook', 'twitter', 'gplus' ) )) > 0 ) {
      $html .= '<h4 class="connect">'.esc_attr__( 'Connect With us', 'jobstar-core' ).'</h4>';
      $html .= '<div class="social-media-wrap">';
      $html .= in_array( 'website', $items ) ? '<a href="'.esc_url( get_the_company_website( $post->ID ) ).'" target="_blank" class="link" rel="nofollow">'.get_the_company_website( $post->ID ).'</a>' : '';
      $html .= in_array( 'twitter', $items ) ? '<a href="https://twitter.com/'.esc_attr( get_the_company_twitter( $post->ID ) ).'" target="_blank" rel="nofollow" class="social-media twitter"><i class="fa fa-twitter"></i></a>' : '';
      $html .= '</div>';
    }
    $html .= '</div>';
    
    echo $html;
  }
  
}