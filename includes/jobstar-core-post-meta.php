<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

/**
 * Custom Field containers are used to extend the post edit screens with additional fields.
 *
 * Field data is stored separately for each post as post meta.
 *
 * @link       http://purithemes.com/
 * @since      1.0.0
 *
 * @package    Jobstar_Core
 * @subpackage Jobstar_Core/includes
 */

Container::make( 'post_meta', esc_html__( 'Page Settings', 'jobstar-core' ) )
->show_on_post_type( array( 'post', 'page' ) )
->hide_on_template( 'builder.php' )
->set_context( 'normal' )
->set_priority( 'core' )
->add_fields( array(
  
  Field::make( 'checkbox', 'transparent_menu',  esc_attr__( 'Transparent Header on Top', 'jobstar-core' ) )
  ->set_option_value( 'yes' )
  ->set_default_value( '' ),
  
  Field::make( 'checkbox', 'page_heading',  esc_attr__( 'Display page heading container', 'jobstar-core' ) )
  ->set_option_value( 'yes' )
  ->set_default_value( '' )
  ->set_width(50)
  ->help_text( esc_html__( 'If enabled, will displayed heading container on the top of page.', 'jobstar-core' ) ),
  
  Field::make( 'text', 'page_heading_title',  esc_attr__( 'Custom Heading Title', 'jobstar-core' ) )
  ->help_text( esc_attr__( 'Leave empty to use default post or page title.', 'jobstar-core' ) )
  ->set_default_value( '' )
  ->set_width(50)
  ->set_conditional_logic( array(
    array( 'field' => 'page_heading', 'value' => 'yes' )
  ) ),
  
  Field::make( 'checkbox', 'page_cover_image',  esc_attr__( 'Use featured image', 'jobstar-core' ) )
  ->set_option_value( 'yes' )
  ->set_default_value( 'yes' )
  ->help_text( esc_attr__( 'Use featured image as heading container background, uncheck to disable background.', 'jobstar-core' ) )
  ->set_conditional_logic( array(
    array( 'field' => 'page_heading', 'value' => 'yes' )
  ) ),
  
  Field::make( 'select', 'page_position',  esc_attr__( 'Sidebar Position', 'jobstar-core' ) )
  ->add_options( array(
    'default' => esc_attr__( 'Default (use global value)', 'jobstar-core' ),
    'fluid'   => esc_attr__( 'Full Width (no sidebar)', 'jobstar-core' ),
    'left'    => esc_attr__( 'Left Sidebar', 'jobstar-core' ),
    'right'   => esc_attr__( 'Right Sidebar', 'jobstar-core' ),
  ) )
  ->set_width(50)
  ->help_text( esc_html__( 'Please note, if you are using page template "Page Builder" sidebar will always hidden.', 'jobstar-core' ) )
  ->set_default_value( 'default' ),
  
  Field::make( 'choose_sidebar', 'page_sidebar', esc_attr__( 'Custom Page Sidebar', 'jobstar-core' ) )
  ->set_conditional_logic( array(
    array( 'field' => 'page_position', 'value' => array( 'left', 'right' ), 'compare' => 'IN' )
  ) )
  ->set_sidebar_options( array(
    'default' => array(
      'before_widget' => '<section id="%1$s" class="widget %2$s">',
      'after_widget'  => '</section>',
      'before_title'  => '<h3 class="widget-title">',
      'after_title'   => '</h3>',
    )
  ) )
  ->set_width(50)
  ->help_text( esc_html__( 'Choose an existing sidebar or create new sidebar area.', 'jobstar-core' ) )
  ->set_default_value( 'blog-sidebar' ),
  
  Field::make( 'select', 'footer_widget_opt',  esc_attr__( 'Footer Widget Area', 'jobstar-core' ) )
  ->add_options( array(
    'default' => esc_attr__( 'Default (use global value)', 'jobstar-core' ),
    'hidden'  => esc_attr__( 'Force hide the footer widget area', 'jobstar-core' ),
    'show'    => esc_attr__( 'Show with about us', 'jobstar-core' ),
    'noabout' => esc_attr__( 'Show widget area only', 'jobstar-core' ),
  ) )
  ->set_width(50)
  ->help_text( esc_html__( 'You can edit about us content from WP Customizer.', 'jobstar-core' ) )
  ->set_default_value( 'default' ),
  
  Field::make( 'choose_sidebar', 'footer_widget_area', esc_attr__( 'Custom Widget Area', 'jobstar-core' ) )
  ->set_conditional_logic( array(
    array( 'field' => 'footer_widget_opt', 'value' => array( 'show', 'noabout' ), 'compare' => 'IN' )
  ) )
  ->set_sidebar_options( array(
    'default' => array(
      'before_widget' => '<div id="%1$s" class="widget %2$s col-md-4 content-wrap"><div class="widget-inner">',
      'after_widget'  => '</div></div>',
      'before_title'  => '<div class="title-underlined"><h3 class="widget-title">',
      'after_title'   => '</h3></div>',
    )
  ) )
  ->set_width(50)
  ->help_text( esc_html__( 'Choose an existing sidebar or create new sidebar area.', 'jobstar-core' ) )
  ->set_default_value( 'footer-widget' ),
  
) );