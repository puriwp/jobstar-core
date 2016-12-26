<?php
/**
 * Widget containers are used to create custom widgets for jobstar theme.
 *
 * @link       http://purithemes.com/
 * @since      1.0.0
 *
 * @package    Jobstar_Core
 * @subpackage Jobstar_Core/includes
 */
class Jobstar_Core_Widgets {
  
  /**
	 * Hold widget base name and class name.
	 *
	 * @since    1.0.0
	 * @var      mixed
	 */
  public $widgets = array();
  
  /**
	 * Class constructor.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
  public function __construct() {
    
    $this->load_widgets();
    
    add_action( 'widgets_init', array( $this, 'register_widgets' ) );
  }
  
  /**
	 * Loads widget files.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
  private function load_widgets() {

    $dirs = apply_filters( 'jobstar_core_widget_files', trailingslashit( JOBSTAR_CORE_PATH . 'widgets' ) );
    $files = glob( $dirs.'*.php', GLOB_NOSORT );

    if ( empty( $files ) ) {
      return;
    }

    foreach ( $files as $file ) {
      $base  = basename( $file, '.php' );
      $class = jobstar_get_class_name( $file, '', 'Widget' );
      if( file_exists( $file ) && !class_exists( $class ) ) {
        include_once( $file );
        if ( !array_key_exists( $base, $this->widgets ) )
          $this->widgets[ $base ] = $class;
      }
    }
  
  }
  
  /**
	 * Register widget area.
	 *
	 * @link     https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
	 * @since    1.0.0
	 * @access   public
	 */
  public function register_widgets() {
    
    if ( empty( $this->widgets ) || !is_array( $this->widgets ) ) {
      return;
    }

    foreach ( $this->widgets as $widget_class ) {
      if ( class_exists( $widget_class ) )
        register_widget( $widget_class );
    }
  }

}
$GLOBALS['Jobstar_Core_Widgets'] = new Jobstar_Core_Widgets;