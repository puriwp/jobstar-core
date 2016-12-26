<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

/**
 * Term meta containers are used to extend the term edit screens with additional fields.
 *
 * Field data is stored in a custom table ($wpdb->termmeta).
 *
 * @link       http://purithemes.com/
 * @since      1.0.0
 *
 * @package    Jobstar_Core
 * @subpackage Jobstar_Core/includes
 */

/**
 * Register term_meta for job_listing_category
 */
Container::make( 'term_meta', 'category_options' )
->show_on_taxonomy( 'job_listing_category' )
->add_fields( array(
  
  Field::make( 'select', 'job_cat_icon', esc_html__( 'Category Icon', 'jobstar-core' ) )
  ->add_options( jobstar_core_fontawesome_option() )
  ->help_text( esc_html__( 'Choose an icon to representative the category', 'jobstar-core' ) )
  ->add_class( 'fontawesome_select_icon' )
));

/**
 * Register term_meta for job_listing_type
 */
Container::make( 'term_meta', 'type_options' )
->show_on_taxonomy( 'job_listing_type' )
->add_fields( array(
  
  Field::make( 'color', 'color', esc_html__( 'Label Color', 'jobstar-core' ) )
  ->help_text( esc_html__( 'Pick a color to representative the job type label.', 'jobstar-core' ) )
  
));

/**
 * Add column job_cat_icon to job_listing_category taxonomy.
 */
function jobstar_core_add_job_cat_columns( $columns ) {
  $new_cols = array();
  foreach ( $columns as $key => $col ) {
    if ( $key == 'name' ) {
      $new_cols['icon'] = 'Icon';
    }
    $new_cols[ $key ] = $col;
  }
  return $new_cols;
}
function jobstar_core_add_job_cat_column_content( $content, $column_name, $term_id ) {
  if ( 'icon' === $column_name ) {
    $icon = carbon_get_term_meta( $term_id, 'job_cat_icon' );
    $content .= !empty( $icon ) ? '<i class="fa fa-2x '.esc_attr($icon).'"></i>' : '-';
  }
  return $content;
}
add_filter( 'manage_edit-job_listing_category_columns', 'jobstar_core_add_job_cat_columns' );
add_filter( 'manage_job_listing_category_custom_column', 'jobstar_core_add_job_cat_column_content', 10, 3 );

/**
 * Add Default values for job_listing_type term color.
 */
function jobstar_job_type_set_defaults() {

  if ( get_option( 'jobstar_set_default_job_types', false ) ) {
    return;
  }
  $defaults = array(
    'full-time' => '#90da36',
    'part-time' => '#f08d3c',
    'temporary' => '#d93674',
    'freelance' => '#0073aa',
    'internship'=> '#6033cc'
  );
  $types = get_terms( array(
    'taxonomy' => 'job_listing_type',
    'hide_empty' => false,
    'fields' => 'id=>slug'
  ) );
  foreach ( $types as $type_id => $type ) {
    if ( array_key_exists( $type, $defaults ) ) {
      add_term_meta( $type_id, '_color', $defaults[ $type ], true );
    }
  }
  update_option( 'jobstar_set_default_job_types', true );
}
add_action( 'carbon_after_register_fields', 'jobstar_job_type_set_defaults', 20 );

/**
 * Custom CSS from term meta color.
 */
function jobstar_core_job_type_color() {
  $types = get_terms( array(
    'taxonomy' => 'job_listing_type',
    'hide_empty' => false,
    'fields' => 'id=>slug'
  ) );
  if ( !empty( $types ) ) : ?>
  <style type="text/css">
    <?php foreach ( $types as $type_id => $type ) :
    $color_type = get_term_meta( $type_id, '_color', true );
    if ( empty( $color_type ) ) continue; ?>
    body.post-type-job_listing .widefat .column-job_listing_type .<?php echo $type; ?>,
    body.edit-tags-php.post-type-job_listing.taxonomy-job_listing_type tr#tag-<?php echo $type_id; ?> a.row-title {
      background-color: <?php echo esc_attr($color_type); ?>;
    }
    <?php endforeach; ?>
  </style>
<?php endif;
}
add_action( 'admin_head', 'jobstar_core_job_type_color', 999 );