<?php
global $post;
$job_type_slug = $job_type_link = $job_type_title = '';
$data_groups = array();
$data_taxs = get_the_terms( get_the_ID(), $the_tax );
if ( !empty( $data_taxs ) && ! is_wp_error( $data_taxs ) ) {
  foreach ( $data_taxs as $my_tax ) {
    $data_groups[] = $my_tax->slug;
  }
}
if ( ( $job_type = get_the_job_type() ) && !empty( $job_type ) ) {
  $job_type_slug = sanitize_title( $job_type->name );
  $job_type_link = get_term_link( $job_type );
  $job_type_title = sprintf( __( 'View all job type : %s', 'jobstar-core' ), $job_type->name );
}
$x_class = array( 'col-md-4', 'col-sm-6' );
?>
<div data-groups='<?php echo json_encode( $data_groups ); ?>' data-longitude="<?php echo esc_attr( $post->geolocation_lat ); ?>" data-latitude="<?php echo esc_attr( $post->geolocation_long ); ?>" <?php job_listing_class( $x_class ); ?>>
  <div class="content-wrap">
    <div class="company-logo valign-wrap">
      <div class="valign-middle">
        <?php the_company_logo( 'medium' ); ?>
      </div>
    </div>
    <div class="company-info type-<?php echo $job_type_slug; ?>">
      <div class="job-type">
        <a href="<?php echo esc_url($job_type_link) ?>" title="<?php echo esc_attr($job_type_title) ?>"><?php the_job_type(); ?></a>
      </div>
      <div class="job-position">
        <?php the_title(); ?>
      </div>
      <div class="job-description">
        <?php the_excerpt(); ?>
      </div>
      <div class="release-date">
        <?php the_date( get_option( 'date_format' ) ); ?>
      </div>
      <a href="<?php the_job_permalink(); ?>" class="read-more" rel="permalink">
        <span class="text"><?php esc_attr_e( 'Details', 'jobstar' ); ?></span>
        <span class="right-arrow"><i class="fa fa-angle-right"></i></span>
      </a>
    </div>
  </div>
</div>