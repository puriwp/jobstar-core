<?php
use Carbon_Fields\Widget;
use Carbon_Fields\Field;

/**
 * Jobstar widget : Post Social Share
 *
 * @link       http://purithemes.com/
 * @since      1.0.0
 *
 * @package    Jobstar_Core
 * @subpackage Jobstar_Core/widgets
 */

class Jobstar_Share_Post_Widget extends Widget {
  
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
      esc_attr__( 'Jobstar : Social Share', 'jobstar-core' ),
      esc_attr__( 'Add social media button for share current post or page.', 'jobstar-core' ),
      array(
        
        Field::make( 'text', 'title', esc_attr__( 'Title', 'jobstar-core' ) )
        ->set_default_value( esc_html__( 'Share this', 'jobstar-core' ) ),
        
        Field::make( 'textarea', 'description', esc_attr__( 'Widget Description', 'jobstar-core' ) )
        ->set_rows(2),
        
        Field::make( 'set', 'socials', esc_attr__( 'Social Media', 'jobstar-core' ) )
        ->add_options(array(
          'facebook'    => 'Facebook',
          'twitter'     => 'Twitter',
          'linkedin'    => 'LinkedIn',
          'google-plus' => 'Google Plus',
          'pinterest'   => 'Pinterest',
          'digg'        => 'Digg',
          'tumblr'      => 'Tumblr',
          'reddit'      => 'Reddit'
        ))
        ->set_default_value( array( 'facebook', 'twitter', 'google-plus', 'linkedin' ) ),
        
        Field::make( 'select', 'design', esc_attr__( 'Widget Align', 'jobstar-core' ) )
        ->add_options(array(
          'left'    => esc_attr__( 'Align Left', 'jobstar-core' ),
          'center'  => esc_attr__( 'Align Center', 'jobstar-core' ),
          'right'   => esc_attr__( 'Align Right', 'jobstar-core' ),
        ))
        
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
    
    global $post;
    
    if ( ! is_singular() || empty( $post ) || empty( $post->ID ) ) {
      return;
    }
    
    $site_name  = get_bloginfo('name');
    $post_title = esc_attr( get_the_title( $post->ID ) );
    $post_url   = esc_attr( get_permalink( $post->ID ) );
    $post_url_s = esc_attr( wp_get_shortlink( $post->ID ) );
    $post_image = has_post_thumbnail( $post->ID ) ? esc_url(get_the_post_thumbnail_url( $post->ID )) : '';
    $social_opts= (array) $instance['socials']; 
    ?>
    <div class="share-this-block text-<?php echo esc_attr( $instance['design'] ) ?>">
      <?php if ( ! empty( $instance['title'] ) ) {
        echo '<h4>' . esc_html( $instance['title'] ) . '</h4>';
      } ?>
      <?php if ( ! empty( $instance['description'] ) ) {
        echo '<p>' . esc_html( $instance['description'] ) . '</p>';
      } ?>
      <div class="social-media-wrap">
      <?php
      if ( in_array( 'facebook', $social_opts ) ) {
        echo '<a href="https://www.facebook.com/sharer/sharer.php?u='.urlencode($post_url).'" class="social-media facebook"><i class="fa fa-facebook"></i></a>';
      }
      if ( in_array( 'linkedin', $social_opts ) ) {
        echo '<a href="https://www.linkedin.com/shareArticle?mini=true&url='.urlencode($post_url).'&title='.urlencode($post_title).'" class="social-media linkedin"><i class="fa fa-linkedin"></i></a>';
      }
      if ( in_array( 'google-plus', $social_opts ) ) {
        echo '<a href="https://www.linkedin.com/shareArticle?mini=true&url='.urlencode($post_url).'&title='.urlencode($post_title).'" class="social-media google-plus"><i class="fa fa-linkedin"></i></a>';
      }
      if ( in_array( 'twitter', $social_opts ) ) {
        echo '<a href="http://twitter.com/intent/tweet?text='.urlencode( $site_name.' - '.$post_title.' '.$post_url_s ).'" class="social-media twitter"><i class="fa fa-twitter"></i></a>';
      }
      if ( in_array( 'pinterest', $social_opts ) ) {
        echo '<a href="https://pinterest.com/pin/create/bookmarklet/?media='.urlencode($post_image).'&url='.urlencode($post_url).'&is_video=false&description='.urlencode($site_name.' - '.$post_title).'" class="social-media pinterest"><i class="fa fa-pinterest"></i></a>';
      }
      if ( in_array( 'digg', $social_opts ) ) {
        echo '<a href="http://digg.com/submit?url='.urlencode($post_url).'&title='.urlencode($site_name.' - '.$post_title).'" class="social-media digg"><i class="fa fa-digg"></i></a>';
      }
      if ( in_array( 'tumblr', $social_opts ) ) {
        echo '<a href="https://www.tumblr.com/widgets/share/tool?canonicalUrl='.urlencode($post_url).'&title='.urlencode($site_name.' - '.$post_title).'" class="social-media tumblr"><i class="fa fa-tumblr"></i></a>';
      }
      if ( in_array( 'reddit', $social_opts ) ) {
        echo '<a href="https://reddit.com/submit?url='.urlencode($post_url).'&title='.urlencode($site_name.' - '.$post_title).'" class="social-media reddit"><i class="fa fa-reddit-alien"></i></a>';
      }
      ?>
      </div>
    </div>
    <?php
  }
  
}