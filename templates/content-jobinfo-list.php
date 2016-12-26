<?php
global $post;
$job_type_slug = '';
$data_groups = array();
$data_taxs = get_the_terms( get_the_ID(), $the_tax );
if ( !empty( $data_taxs ) && ! is_wp_error( $data_taxs ) ) {
  foreach ( $data_taxs as $my_tax ) {
    $data_groups[] = $my_tax->slug;
  }
}
if ( ( $job_type = get_the_job_type() ) && !empty( $job_type ) ) {
  $job_type_slug = sanitize_title( $job_type->name );
}
?>
<a href="<?php the_job_permalink(); ?>" data-groups='<?php echo json_encode( $data_groups ); ?>' data-longitude="<?php echo esc_attr( $post->geolocation_lat ); ?>" data-latitude="<?php echo esc_attr( $post->geolocation_long ); ?>" <?php job_listing_class(); ?>>
  <div class="job-list valign-wrap">
    <div class="company-icon valign-middle">
      <?php the_company_logo( 'company-thumb' ); ?>
    </div>
    <div class="separator"></div>
    <div class="company valign-middle">
      <div class="company-name">
        <div class="col-sm-6 name"><?php the_title(); ?></div>
        <div class="col-sm-6 text-right"><span class="<?php echo $job_type_slug; ?>"><?php the_job_type(); ?></span></div>
      </div>
      <div class="company-info">
        <div class="col-sm-4">
          <i class="fa fa-briefcase"></i>&nbsp; <?php the_company_name(); ?>
        </div>
        <div class="col-sm-4">
          <i class="fa fa-compass"></i>&nbsp; <?php the_job_location(false); ?>
        </div>
        <div class="col-sm-4">
          <i class="fa fa-calendar"></i>&nbsp; <?php the_date( 'M j, Y' ); ?>
        </div>
      </div>
    </div>
  </div>
</a>