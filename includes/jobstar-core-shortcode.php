<?php
/**
 * Custom shortcode for jobstar theme.
 *
 * @link       http://purithemes.com/
 * @since      1.0.0
 *
 * @package    Jobstar_Core
 * @subpackage Jobstar_Core/includes
 */

class Jobstar_Core_Shortcodes {
  
  /**
	 * class constructor
	 */
  public function __construct() {
    
    add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
    
    add_shortcode( 'js_banner_slider', array( $this, 'jobstar_banner_slider' ) );
    add_shortcode( 'js_banner_slider_section', array( $this, 'jobstar_banner_slider_section' ) );
    add_shortcode( 'js_service_block', array( $this, 'jobstar_service_block' ) );
    add_shortcode( 'js_counter_box', array( $this, 'jobstar_counter_box' ) );
    add_shortcode( 'js_testimonial', array( $this, 'jobstar_testimonial' ) );
    add_shortcode( 'js_pricing', array( $this, 'jobstar_pricing' ) );
    add_shortcode( 'js_cta_box', array( $this, 'jobstar_cta_box' ) );
    add_shortcode( 'js_accordion', array( $this, 'jobstar_accordion' ) );
    add_shortcode( 'js_message', array( $this, 'jobstar_message' ) );
    
    // WP Job Manager
    add_filter( 'job_manager_locate_template', array( $this, 'locate_template' ), 13, 3 );
    add_shortcode( 'js_job_search_form', array( $this, 'jobstar_job_search_form' ) );
    add_shortcode( 'js_jobs_grid', array( $this, 'jobstar_jobs_grid' ) );
    add_shortcode( 'js_jobs_spotlight', array( $this, 'jobstar_jobs_spotlight' ) );
    add_shortcode( 'js_job_categories', array( $this, 'jobstar_job_categories' ) );
  }
  
  /**
   * Register required script for shortcodes.
   *
   * @return void
   */
  public function scripts() {
    
    global $wp_styles;
    
		if ( ! wp_script_is( 'waypoints', 'registered' ) || 
    ( isset( $wp_styles->registered['waypoints'] ) && !is_object( $wp_styles->registered['waypoints'] ) ) ) {
      wp_register_script( 'waypoints', JOBSTAR_CORE_URL . 'assets/js/jquery.waypoints.min.js', array( 'jquery' ), '4.0.1', true );
		}
    wp_register_script( 'countTo', JOBSTAR_CORE_URL . 'assets/js/jquery.countTo.min.js', array( 'jquery', 'waypoints', 'jobstar-core' ), null, true );
    wp_register_script( 'shuffle.js', JOBSTAR_CORE_URL . 'assets/js/jquery.shuffle.min.js', array( 'jquery', 'jobstar-core' ), '4.0.0', true );
    wp_register_script( 'slick', JOBSTAR_CORE_URL . 'assets/js/jquery.slick.min.js', array( 'jquery', 'jobstar-core' ), '1.6.0', true );
    
    wp_register_style( 'slick', JOBSTAR_CORE_URL . 'assets/css/slick.min.css', array(), '1.6.0', 'all' );
    if ( isset( $_GET['vc_editable'] ) && !empty( $_GET['vc_editable'] ) ) {
      wp_enqueue_style( 'slick' );
      wp_add_inline_style( 'jobstar', '.vc_element.vc_vc_column{padding:0 15px}' );
    }
  }
  
  /**
   * Fix autop on shortcodes content.
   *
   * @return string
   */
  public function fix_wpautop( $content, $autop = true ) {

    if ( $autop ) {
      $content = wpautop( preg_replace( '/<\/?p\>/', "\n", $content ) . "\n" );
    }

    return do_shortcode( shortcode_unautop( $content ) );
  }
  
  /**
   * Parse repeatable value string.
   *
   * @return mixed
   */
  public function param_group_parse_atts( $atts_string ) {
    
    $array = json_decode( urldecode( $atts_string ), true );
    
    return $array;
  }
  
  /**
   * Parse comma separated string.
   *
   * @return mixed
   */
  public function parse_comma_separated( $string, $array = false ) {
    
    $string = esc_attr( trim( $string ) );
    
    if ( empty( $string ) ) {
      return $array ? array() : '';
    }
    $new_string = array();
    $string = explode( ',', $string );

    foreach ( $string as $str ) {
      $str = absint( $str );
      if ( empty( $str ) || !is_numeric( $str ) ) continue;
      $new_string[] = trim( $str );
    }
    
    return ( $array ? $new_string : implode( ',', $new_string ) );
  }
  
  /**
   * Escape and sanitize CSS color value.
   *
   * @return string
   */
  public function esc_color( $color ) {

    if ( preg_match( '/rgba/', $color ) ) {
      $color = preg_replace( array(
        '/\s+/',
        '/^rgba\((\d+)\,(\d+)\,(\d+)\,([\d\.]+)\)$/',
      ), array(
        '',
        'rgb($1,$2,$3)',
      ), $color );
    } else {
      $color = strtolower( ltrim($color, '#') );
      if ( ctype_xdigit($color) && (strlen($color) == 6 || strlen($color) == 3) ) {
        $color = '#'.$color;
      } else {
        $color = '';
      }
    }
    
    return $color;
  }
  
  /**
   * Build CSS for HTML style attributes.
   *
   * @return string
   */
  public function build_style_attr( $args = array() ) {
    
    extract( wp_parse_args( $args, array(
      'bg_image'          => 0,
      'bg_color'          => '',
      'bg_repeat'         => '',
      'font_color'        => '',
      'p_top'             => '',
      'p_bottom'          => '',
      'p_left'            => '',
      'p_right'           => '',
      'm_top'             => '',
      'm_bottom'          => '',
      'm_left'            => '',
      'm_right'           => '',
      'bg_postions'       => '',
      'bg_attach'         => ''
    ) ) );

    $padding = array(
      'padding-top'    => $p_top,
      'padding-bottom' => $p_bottom,
      'padding-left'   => $p_left,
      'padding-right'  => $p_right,
    );

    $margin = array(
      'margin-top'    => $m_top,
      'margin-bottom' => $m_bottom,
      'margin-left'   => $m_left,
      'margin-right'  => $m_right,
    );

    $has_image = false;
    $style = '';

    if ( (int)$bg_image > 0 && ( $image_url = wp_get_attachment_url( $bg_image, 'full' ) ) !== false ) {
      $has_image = true;
      $style .= "background-image: url(" . esc_url( $image_url ) . ");";
    }

    if ( ! empty( $bg_color ) ) {
      $style .= 'background-color: ' . $this->esc_color( $bg_color ) . ';';
    }

    if ( ! empty( $bg_repeat ) && $has_image ) {
      if ( $bg_repeat === 'cover' ) {
        $style .= "background-repeat:no-repeat;background-size:cover;";
      } elseif ( $bg_repeat === 'contain' ) {
        $style .= "background-repeat:no-repeat;background-size:contain;";
      } elseif ( $bg_repeat === 'no-repeat' ) {
        $style .= 'background-repeat:no-repeat;';
      } elseif ( $bg_repeat === 'repeat' ) {
        $style .= 'background-repeat:repeat;background-size:auto;';
      }
    }

    if ( ! empty( $bg_postions ) && $has_image ) {
      $style .= 'background-position:' . esc_attr( $bg_postions ) . ';';
    }

    if ( ! empty( $bg_attach ) && $has_image ) {
      $style .= 'background-attachment:' . esc_attr( $bg_attach ) . ';';
    }

    if ( ! empty( $font_color ) ) {
      $style .= 'color: ' . $this->esc_color( $font_color ) . ';';
    }

    if ( ! empty( $padding ) && is_array( $padding ) ) {
      foreach ( $padding as $key => $value ) {
        if ( !empty( $value ) || is_numeric( $value ) ) {
          $style .= $key.':' . ( preg_match( '/(px|em|\%|pt|cm)$/', $value ) ? $value : $value . 'px' ) . ';';
        }
      }
    }

    if ( ! empty( $margin ) && is_array( $margin ) ) {
      foreach ( $margin as $key => $value ) {
        if ( !empty( $value ) || is_numeric( $value ) ) {
          $style .= $key.':' . ( preg_match( '/(px|em|\%|pt|cm)$/', $value ) ? $value : $value . 'px' ) . ';';
        }
      }
    }

    return empty( $style ) ? $style : ' style="' . $style . '"';
  }
  
  /**
   * Add css animation for Visual Composer.
   *
   * @return string
   */
  public function css_animation( $css_animation ) {
    $output = '';
    if ( '' !== $css_animation ) {
      wp_enqueue_script( 'waypoints' );
      $output = ' wpb_animate_when_almost_visible wpb_' . esc_attr( $css_animation );
    }
    return $output;
  }
  
  /**
   * Escaped class attribute for HTML element.
   *
   * @return string
   */
  public function css_class( $param_value, $prefix = '' ) {
    $css_class = preg_match( '/\s*\.([^\{]+)\s*\{\s*([^\}]+)\s*\}\s*/', $param_value ) ? $prefix . preg_replace( '/\s*\.([^\{]+)\s*\{\s*([^\}]+)\s*\}\s*/', '$1', $param_value ) : '';
    return $css_class;
  }
  
  /**
   * Build HTML Link from VC Shortcode.
   *
   * @return string
   */
  public function build_link( $value, $class = '', $content = '', $echo = true ) {

    $link = array();
    $value = ( '||' === $value ) ? '' : $value;
    $params_pairs = explode( '|', $value );
    
    if ( empty( $params_pairs ) ) {
      return;
    }
    
    foreach ( $params_pairs as $pair ) {
      $param = preg_split( '/\:/', $pair );
      if ( ! empty( $param[0] ) && isset( $param[1] ) ) {
        $link[ $param[0] ] = rawurldecode( $param[1] );
      }
    }

    $attrs = array(
      'href'    => !empty( $link['url'] ) ? esc_url( $link['url'] ) : '',
      'title'   => !empty( $link['title'] ) ? esc_attr( $link['title'] ) : '',
      'target'  => !empty( $link['target'] ) ? esc_attr( $link['target'] ) : '',
      'rel'     => !empty( $link['rel'] ) ? esc_attr( $link['rel'] ) : '',
      'class'   => !empty( $class ) ? esc_attr( $class ) : '',
      'id'      => !empty( $link['id'] ) ? sanitize_key( $link['id'] ) : '',
    );
    ksort( $attrs );

    $output = '<a';
    foreach ( $attrs as $attr => $att_value ) {
      if ( empty( $att_value ) ) continue;
      $output .= ' ' . $attr . '="' . $att_value . '"';
    }
    $output .= '>' . $content . '</a>';

    if ( $echo ) {
      echo $output;
    }
    return $output;
  }
  
  /**
	 * Jobstar Banner Slider Wrapper Shortcode.
   *
   * @since     1.0.0
	 */
  public function jobstar_banner_slider( $atts, $content = null ) {
        
    extract( shortcode_atts( array(
      'tab_id'   => '',
      'show_dot' => '',
      'show_nav' => '1',
      'interval' => '3',
      'fullh'    => '1',
      'height'   => '',
      'el_class' => '',
      'css_animation' => ''
    ), $atts, 'js_banner_slider' ) );
    
    $GLOBALS['js_banner_slider_section'] = 1;
    
    wp_enqueue_script( 'jobstar-core' );
    
    if ( empty( $content ) ) {
      return;
    }
    
    preg_match_all( '/\[js_banner_slider_section(.*?)\]/', $content, $matches );
    $c_item = isset( $matches[1] ) ? count( $matches[1] ) : 0;
    
    if ( ! $c_item )
      return;
    
    $content  = $this->fix_wpautop( $content );
    $el_class = $this->css_class( $el_class );
    $styles   = $this->build_style_attr( $atts );
    
    $carousel_id = !empty( $tab_id ) ? sanitize_title( $tab_id ) : 'banner-'.substr( uniqid(), 0, 4 );
    $interval    = ( empty( $interval ) || '0' === $interval ) ? 'false' : absint( $interval ) * 1000;
    
    $classses = array(
      'banner',
      'carousel',
      'slide',
    );
    
    if ( '1' === $fullh ) {
      $classses[] = 'full-height';
    } elseif ( !empty( $height ) ) {
      $height = preg_match( '/(px|em|\%|pt|cm)$/', $value ) ? esc_attr( $value ) : absint( $value ) . 'px';
      $height_style = 'height:'.$height.';';
      $styles = !empty($styles) ? str_replace( 'style="', 'style="'.$height_style, $styles ) : ' style="'.$height_style.'"';
    }
    
    $classses = array_unique( $classses );
    
    ob_start(); ?>
    <section id="<?php echo esc_attr($carousel_id) ?>" class="<?php echo esc_attr( join( ' ', $classses ) ); ?>" data-interval="<?php echo $interval; ?>" data-pause="null" data-ride="carousel" <?php echo $styles; ?>>
      
      <?php if ( '1' === $show_dot ) : ?>
      <ol class="carousel-indicators">
        <?php for ( $o = 1; $o <= $c_item; $o++ ) : ?>
        <li data-target="#<?php echo esc_attr($carousel_id) ?>" data-slide-to="<?php echo $o; ?>"<?php if(1===$o) echo ' class="active"'; ?>></li>
        <?php endfor; ?>
      </ol>
      <?php endif; ?>
      
      <div class="carousel-inner" role="listbox">
        <?php echo do_shortcode( $content ); ?>
      </div>
      
      <?php if ( '1' === $show_nav ) : ?>
      <a class="left carousel-control" href="#<?php echo esc_attr($carousel_id) ?>" role="button" data-slide="prev">
        <div class="control left">
          <div class="shape"><i class="fa fa-angle-left"></i></div>
        </div>
        <span class="sr-only">Previous</span>
      </a>
      <a class="right carousel-control" href="#<?php echo esc_attr($carousel_id) ?>" role="button" data-slide="next">
        <div class="control right">
          <div class="shape"><i class="fa fa-angle-right"></i></div>
        </div>
        <span class="sr-only">Next</span>
      </a>
      <?php endif; ?>
    </section>
    <?php
    return ob_get_clean();
  }
  
  /**
	 * Jobstar Banner Slider Item Shortcode.
   *
   * @since     1.0.0
	 */
  public function jobstar_banner_slider_section( $atts, $content = null ) {
    
    global $js_banner_slider_section;
    
    $atts = shortcode_atts( array(
      'title'    => '',
      'bg_image' => '',
      'bg_repeat'=> '',
      'bg_postions'=> '',
      'bg_overlay'=> '1',
      'overlay_color'=> 'rgba(0, 0, 0, 0.85)',
      'el_class' => '',
    ), $atts, 'js_banner_slider_section' );
    
    extract( $atts );
    
    $content  = $this->fix_wpautop( $content );
    $el_class = $this->css_class( $el_class );
    $styles   = $this->build_style_attr( $atts );
    
    $css_classes = array(
      'item',
      'slide-'.$js_banner_slider_section,
      $el_class,
      ( 1 === $js_banner_slider_section ) ? 'active' : ''
    );
    
    if ( '1' === $bg_overlay && !empty( $overlay_color ) ) {
      $css_classes[] = 'banner-overlay';
      $bg_overlay = '<div class="overlay" style="background-color:'.$this->esc_color( $overlay_color ).'"></div>';
    } else {
      $bg_overlay = '';
    }
    
    $css_classes = array_unique( $css_classes );
    
    ob_start(); ?>
    <div class="<?php echo esc_attr( join( ' ', $css_classes ) ); ?>" <?php echo $styles; ?>>
      
      <?php echo $bg_overlay; ?>
      
      <div class="container">
        <div class="content-wrap valign-wrap">
          <div class="content valign-middle">
            <div class="text-content col-md-8 col-md-offset-2 col-xs-12">
              <?php echo !empty( $content ) ? do_shortcode( $content ) : '&nbsp;'; ?>
            </div>
          </div>
        </div>
      </div>
      
    </div>
    <?php
    $js_banner_slider_section++;
    return ob_get_clean();
  }
  
  /**
	 * Jobstar Service Block Item Shortcode.
   *
   * @since     1.0.0
	 */
  public function jobstar_service_block( $atts, $content = '' ) {
    
    extract( shortcode_atts( array(
      'number'  => '1',
      'heading' => '',
      'title'   => '',
      'more'    => '1',
      'link'    => '',
      'el_class' => '',
      'css_animation' => ''
    ), $atts, 'js_service_block' ) );
        
    $content  = $this->fix_wpautop( $content );
    $el_class = $this->css_class( $el_class );
    $styles   = $this->build_style_attr( $atts );
    
    if ( ! empty( $css_animation ) ) {
      $el_class .= $this->css_animation( $css_animation );
    }
    
    $classses = array(
      'service-block-item',
      'service-' . absint( $number )
    );
    $classses = $classses + explode( ' ', $el_class );
    
    $heading = function_exists( 'jobstar_get_english_string' ) ? $heading . ' ' . jobstar_get_english_string( $number ) : $heading;
    $content = wp_trim_words( strip_tags( $content ), 30, '' );
    
    ob_start(); ?>
    <div id="service-<?php echo absint( $number ); ?>" class="<?php echo esc_attr( join( ' ', $classses ) ); ?>" data-number="<?php echo absint( $number ); ?>" <?php echo $styles; ?>>
      <h2><?php echo esc_html( $heading ); ?></h2>
      <h1><?php echo esc_html( $title ); ?></h1>
      <p><?php echo esc_html( $content ); ?><?php echo ( $more || $more == '1' ) ? ' ... ' : '.'; ?></p>
      <?php
      if ( ( $more || $more == '1' ) && ! empty( $link ) ) {
        $this->build_link( $link, 'btn def-btn btn-bg-primary', esc_attr__( 'Read More', 'jobstar-core' ) );
      } ?>
      <div class="sec-h-pad-b"></div>
    </div>
    <?php
    return ob_get_clean();
  }
  
  /**
	 * Jobstar Counter Box with Icon Item Shortcode.
   *
   * @since     1.0.0
	 */
  public function jobstar_counter_box( $atts, $content = '' ) {
    
    extract( shortcode_atts( array(
      'number'   => '',
      'title'    => '',
      'speed'    => '3',
      'add_icon' => '1',
      'i_type'   => '',
      'i_icon'   => '',
      'i_color'  => '',
      'n_color'  => '',
      't_color'  => '',
      'el_class' => '',
      'css_animation' => ''
    ), $atts, 'js_counter_box' ) );
    
    if ( empty( $number ) || empty( $title ) ) {
      return;
    }
    
    wp_enqueue_script( 'jobstar-core' );
    wp_enqueue_script( 'countTo' );
    
    $icon_html = $i_color_style = $n_color_style = $t_color_style = '';
    $i_color = $this->esc_color( $i_color );
    $n_color = $this->esc_color( $n_color );
    $t_color = $this->esc_color( $t_color );
    
    
    $speed    = !empty( $speed ) ? absint( $speed ) : 3;
    $speed    = $speed * 1000;
    $content  = $this->fix_wpautop( $content );
    $el_class = $this->css_class( $el_class );
    $styles   = $this->build_style_attr( $atts );
    if ( ! empty( $css_animation ) ) {
      $el_class .= $this->css_animation( $css_animation );
    }
    
    if ( '1' === $add_icon && !empty( $i_type ) ) {
      
      $icon_type  = 'i_icon_' . $i_type;
      if ( function_exists( 'vc_icon_element_fonts_enqueue' ) ) {
        vc_icon_element_fonts_enqueue( $i_type );
      }
      if ( isset( $atts[ $icon_type ] ) && ! empty( $atts[ $icon_type ] ) ) {
        $icon_class = $atts[ $icon_type ];
      } else {
        $icon_class = $i_type;
      }
      if ( !empty( $i_color ) ) {
        $i_color_style = ' style="color:' . esc_attr( $i_color ) . '"';
      }
      $icon_html = '<i class="vc_btn3-icon ' . esc_attr( $icon_class ) . '"' . $i_color_style . '></i>';
    }
    
    if ( ! empty( $n_color ) ) {
      $n_color_style = ' style="color:' . esc_attr( $n_color ) . '"';
    }
    
    if ( ! empty( $t_color ) ) {
      $t_color_style = ' style="color:' . esc_attr( $t_color ) . '"';
    }
    
    $classses = explode( ' ', $el_class );
    $classses[] = 'js-counters';
    $classses[] = 'clearfix';
    
    rsort( $classses );
    
    ob_start(); ?>
    <div id="counter-<?php echo sanitize_title( $title ); ?>" class="<?php echo esc_attr( trim( join( ' ', $classses ) ) ); ?>" <?php echo $styles; ?>>
      <div class="counter-wrap">
        <?php if ( ! empty( $icon_html ) ) : ?>
        <div class="icon"><?php echo $icon_html; ?></div>
        <div class="separator"></div>
        <?php endif; ?>
        <div class="counter-number" data-from="0" data-to="<?php echo absint($number); ?>" data-speed="<?php echo absint($speed); ?>"<?php echo $n_color_style; ?>></div>
        <div class="bottom-text"<?php echo $t_color_style; ?>><?php echo esc_html( $title ); ?></div>
      </div>
    </div>
    <?php
    return ob_get_clean();
  }
  
  /**
	 * Jobstar Testimonial Carousel Slider Shortcode.
   *
   * @since     1.0.0
	 */
  public function jobstar_testimonial( $atts, $content = '' ) {
    
    extract( shortcode_atts( array(
      'title'    => '',
      'type'     => 'transparent',
      'showed'   => '3', // type=bubble
      'loop'     => '1', // type=bubble
      'interval' => '',
      'show_nav' => '',
      'show_dot' => '',
      'values'   => '',
      'el_class' => '',
      'css_animation' => ''
    ), $atts, 'js_testimonial' ) );
    
    if ( empty( $values ) ) {
      return;
    }
    
    wp_enqueue_script( 'jobstar-core' );
    
    $data_interval = $slick_args = '';
    $carousel_id = 'testimonial-'.substr( uniqid(), 0, 4 );
    $interval = $interval == '0' ? 'false' : absint( $interval ) * 1000;
    $el_id    = sanitize_title( $carousel_id );
    $el_class = $this->css_class( $el_class );
    $styles   = $this->build_style_attr( $atts );
    if ( ! empty( $css_animation ) ) {
      $el_class .= $this->css_animation( $css_animation );
    }
    
    if ( ! empty( $title ) ) {
      $title = '<h2 class="heading">' . esc_html( $title ) . '</h2>';
    }
    
    if ( ! empty( $interval ) ) {
      $data_interval = ' data-interval="' . $interval . '"';
    }
    
    if ( 'bubble' === $type ) {
      
      wp_enqueue_script( 'slick' );
      wp_enqueue_style( 'slick' );
      
      $slick = array(
        'infinite'      => ( '1' === $loop ) ? true : false,
        'arrows'        => ( '1' === $show_nav ) ? true : false,
        'dots'          => ( '1' === $show_dot ) ? true : false,
        'slidesToShow'  => !empty($showed) ? absint($showed) : 3,
      );
      if ( !empty( $interval ) && 'false' !== $interval ) {
        $slick['autoplay'] = true;
        $slick['autoplaySpeed'] = $interval;
      }
      $slick_args = json_encode( $slick );
    }
    
    $values = (array) $this->param_group_parse_atts( $values );
    $datas  = array();
    
    foreach ( $values as $k => $v ) {
      if ( empty( $v['testi'] ) ) continue;
      $img_id = preg_replace( '/[^\d]/', '', $v['avatar'] );
      if ( empty( $img_id ) ) {
        $data_avatar = JOBSTAR_ASSETS.'img/default-avatar.png';
      } else {
        $avatar_src = wp_get_attachment_image_src( $img_id );
        $data_avatar = ( $avatar_src && !empty( $avatar_src[0] ) ) ? $avatar_src[0] : JOBSTAR_ASSETS.'img/default-avatar.png';
      }
      $datas[] = array(
        'id'     => absint( $k ) + 1,
        'ava'    => esc_url( $data_avatar ),
        'name'   => !empty( $v['name'] ) ? esc_attr( $v['name'] ) : '',
        'work'   => !empty( $v['work'] ) ? esc_attr( $v['work'] ) : '',
        'testi'  => !empty( $v['testi'] ) ? strip_tags( $v['testi'], '<br><b><i><em><strong><font>' ) : '',
      );
    }
    
    $classses = explode( ' ', $el_class );
    $classses[] = 'testimonial-slider';
    $classses[] = 'clearfix';
    rsort( $classses );
    
    ob_start(); ?>
    <div class="<?php echo esc_attr( trim( join( ' ', $classses ) ) ); ?>" <?php echo $styles; ?>>
      
      <?php echo $title; ?>
      
      <?php if ( 'bubble' === $type ) : ?>
      
      <div id="<?php echo $el_id ?>" class="slick_carousel" data-slick='<?php echo esc_attr( $slick_args ); ?>'>
      
        <?php foreach ( $datas as $data ) : ?>
        <div class="testimonial-bubble">
          <div class="col-md-12 text-center">
            <div class="photo-wrap">
              <img src="<?php echo $data['ava']; ?>" alt="<?php echo $data['name']; ?>">
            </div>
          </div>
          <div class="col-md-12">
            <div class="content">
              <h4><?php echo $data['name']; ?></h4>
              <?php echo !empty( $data['work'] ) ? '<span>'.$data['work'].'</span>' : ''; ?>
              <p><?php echo $data['testi']; ?></p>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
        
      </div>
            
      <?php elseif ( 'transparent' === $type ) : ?>
      
      <div class="carousel-wrapper clearfix">
        <?php if ( ! empty( $datas ) ) : ?>
        <div class="carousel slide" id="<?php echo $el_id; ?>" data-ride="carousel"<?php echo $data_interval; ?>>

          <!-- Dot Controls -->
          <?php if ( '1' === $show_dot ) : ?>
          <ol class="carousel-indicators">
            <?php foreach ( $datas as $dot_data ) : ?>
            <?php $is_active = ( 1 === $dot_data['id'] ) ? ' class="active"' : ''; ?>
            <li data-target="#<?php echo $el_id; ?>" data-slide-to="<?php echo absint( $dot_data['id'] ); ?>"<?php echo $is_active; ?>></li>
            <?php endforeach; ?>
          </ol>
          <?php endif; ?>

          <div class="carousel-inner" role="listbox">

            <?php foreach ( $datas as $data ) : ?>
            <?php $item_active = ( 1 === $data['id'] ) ? ' active' : ''; ?>
            <div class="item<?php echo $item_active; ?>">
              <div class="photo sec-q-pad-t">
                <img src="<?php echo $data['ava']; ?>">
              </div>
              <div class="text sec-q-pad-t">
                <p>“<?php echo $data['testi']; ?>”</p>
              </div>
              <div class="name-position sec-q-pad-t">
                <h4><?php echo $data['name']; ?></h4>
                <?php echo !empty( $data['work'] ) ? '<i>'.$data['work'].'</i>' : ''; ?>
              </div>
            </div><!--/.item -->
            <?php endforeach; ?>

          </div><!--/.carousel-inner -->

          <!-- Controls -->
          <?php if ( '1' === $show_nav ) : ?>
          <a class="left carousel-control" href="#<?php echo $el_id; ?>" role="button" data-slide="prev">
            <div class="control-button left"><i class="fa fa-angle-left"></i></div>
            <span class="sr-only">Previous</span>
          </a>
          <a class="right carousel-control" href="#<?php echo $el_id; ?>" role="button" data-slide="next">
            <div class="control-button right"><i class="fa fa-angle-right"></i></div>
            <span class="sr-only">Next</span>
          </a>
          <?php endif; ?>

        </div><!--/.carousel -->
        <?php endif; //$datas ?>
      </div><!--/.carousel-wrapper-->
      
      <?php endif; //$type ?>
      
    </div>
    <?php
    return ob_get_clean();
    
  }
  
  /**
	 * Jobstar TPricing Item Shortcode.
   *
   * @since     1.0.0
	 */
  public function jobstar_pricing( $atts, $content = '' ) {
    
    extract( shortcode_atts( array(
      'title'    => '',
      'price'    => '',
      'type'     => 'curved',
      'price_bg' => '',
      'featured' => '',
      'values'   => '',
      'btn_url'  => '#',
      'btn_txt'  => 'ORDER',
      'el_id'    => '',
      'el_class' => '',
      'css_animation' => ''
    ), $atts, 'js_pricing' ) );
    
    if ( empty( $values ) ) {
      return;
    }
    
    $price_id = 'price-'.substr( uniqid(), 0, 4 );
    $el_id    = sanitize_title( $price_id );
    $el_class = $this->css_class( $el_class );
    $styles   = $this->build_style_attr( $atts );
    $values = (array) $this->param_group_parse_atts( $values );
    
    if ( ! empty( $css_animation ) ) {
      $el_class .= $this->css_animation( $css_animation );
    }
    
    $classses = explode( ' ', $el_class );
    $classses[] = ( $type == 'overlay' ) ? 'pricing-item-overlay' : 'pricing-item-curved';
    $classses[] = ( $featured == '1' ) ? 'active' : '';
    
    ob_start(); ?>
    <div id="<?php echo sanitize_title($el_id) ?>" class="<?php echo esc_attr( join( ' ', $classses ) ); ?>" <?php echo $styles; ?>>
      
      <div class="price-text-wrap text-center valign-wrap"<?php if ( $type == 'overlay' && !empty( $price_bg ) ) : ?> style="background-image:url(<?php echo esc_url(wp_get_attachment_image_src($price_bg)[0]) ?>)"<?php endif; ?>>
        <?php if ( $type == 'overlay' ) : ?>
        <div class="overlay"></div>
        <div class="valign-middle">
          <div class="price-text">
        <?php else: ?>
        <div class="valign-middle price-text">
        <?php endif; ?>
            <p><?php echo esc_html( $title ) ?></p>
            <h2><?php echo esc_html( $price ) ?></h2>
        <?php if ( $type == 'overlay' ) : ?></div><?php endif; ?>
        </div>
      </div>
      <div class="list">
        <ul>
        <?php foreach ( $values as $value ) : ?>
          <li><i class="<?php echo esc_attr( ( !empty($value['icon'])?$value['icon']:'fa fa-plus' ) ) ?>"></i> &nbsp; <?php echo esc_html( $value['feature'] ) ?></li>
        <?php endforeach; ?>
        </ul>
        <div class="text-center">
          <a href="<?php echo esc_url($btn_url) ?>" class="def-btn btn-bg-secondary">
            <?php echo esc_attr($btn_txt) ?>
          </a>
        </div>
      </div>
      
    </div>
    <?php
    return ob_get_clean();
  }
  
  /**
	 * Jobstar Call to Action Box Shortcode.
   *
   * @since     1.0.0
	 */
  public function jobstar_cta_box( $atts, $content = '' ) {
    
    extract( shortcode_atts( array(
      'title'    => '',
      'align'    => '',
      'buttons'  => '',
      'btn_style'=> '',
      'btn_size' => '',
      'el_class' => '',
      'css_animation' => ''
    ), $atts, 'js_cta_box' ) );
    
    if ( empty( $content ) ) {
      return;
    }
    
    wp_enqueue_script( 'jobstar-core' );
    
    $content  = $this->fix_wpautop( $content );
    $el_class = $this->css_class( $el_class );
    
    if ( ! empty( $css_animation ) ) {
      $el_class .= $this->css_animation( $css_animation );
    }
    
    if ( strpos( $title, '|' ) !== false ) {
      $text_parse = explode( '|', $title );
      $title = '';
      foreach ( $text_parse as $tk => $t_value ) {
        if ( $tk === 1 ) {
          $title .= ' <span>'.trim($t_value).'</span>';
        } else {
          $title .= ' '.trim($t_value);
        }
      }
    }
    
    $classses = array(
      'cta-box',
      'content-wrap',
      'valign-wrap',
      'text-' . esc_attr( $align ),
    );
    $classses = array_merge( $classses, explode( ' ', $el_class ) );
    
    $button_sets = '';
    $btn_class = 'def-btn';
    $buttons = (array) $this->param_group_parse_atts( $buttons );
    
    if ( !empty( $btn_style ) ) {
      $btn_class .= ' '.esc_attr( $btn_style );
    }
    if ( !empty( $btn_size ) ) {
      $btn_class .= ' '.esc_attr( $btn_size );
    }
    
    foreach ( $buttons as $k => $v ) {
      if ( empty( $v['label'] ) ) continue;
      $mybtn_class = '';
      if ( !empty( $v['style'] ) ) {
        $mybtn_class = ' '.esc_attr( $v['style'] );
      }
      $button_sets .= $this->build_link( $v['link'], $btn_class.$mybtn_class, esc_attr( $v['label'] ), false );
    }
    
    ob_start(); ?>
    <div class="<?php echo esc_attr( join( ' ', $classses ) ); ?>">
      <div class="content valign-middle">
        <h1><?php echo $title; ?></h1>
        <div class="text">
          <?php echo wpautop( $content ); ?>
        </div>
        <?php echo $button_sets; ?>
      </div>
    </div>
    <?php
    return ob_get_clean();
  }
  
  /**
	 * Jobstar Accordion Shortcode.
   *
   * @since     1.0.0
	 */
  public function jobstar_accordion( $atts, $content = '' ) {
    
    extract( shortcode_atts( array(
      'title'    => '',
      'el_id'    => '',
      'items'    => '',
      'icon'     => '',
      'el_class' => '',
      'css_animation' => ''
    ), $atts, 'js_accordion' ) );
        
    $content  = $this->fix_wpautop( $content );
    $el_class = $this->css_class( $el_class );
    $items    = (array) $this->param_group_parse_atts( $items );
    
    if ( empty( $items ) ) {
      return;
    }
    
    if ( ! empty( $css_animation ) ) {
      $css_animation = ' '.$this->css_animation( $css_animation );
    }
    
    $el_id = !empty( $el_id ) ? sanitize_title( $el_id ) : 'js_accordion_'.substr( uniqid(), 0, 4 );
    
    $classses = array(
      'panel-group',
      'js_accordion-group'
    );
    $classses = array_merge( $classses, explode( ' ', $el_class ) );
    
    ob_start(); ?>
    <div id="<?php echo esc_attr( $el_id ) ?>" class="<?php echo esc_attr( join( ' ', $classses ) ); ?>" role="tablist" aria-multiselectable="true">
      
      <?php $i=1; foreach ( $items as $item ) : 
      if ( empty( $item['paneltitle'] ) || empty( $item['panelcontent'] ) ) continue;
      $paneltitle = str_replace( '{i}', $i, $item['paneltitle'] ) ?>
      <div class="panel panel-default def-accordion<?php echo esc_attr( $css_animation ) ?>">
        <div class="panel-heading" role="tab" id="<?php echo esc_attr( $el_id.'-'.$i ) ?>">
          <a class="no-effect<?php echo (1!==$i) ? ' collapsed' : '' ?>" role="button" data-toggle="collapse" data-parent="#<?php echo esc_attr( $el_id ) ?>" href="#<?php echo esc_attr( $el_id.'-collapse-'.$i ) ?>" aria-expanded="<?php echo (1===$i) ? 'true' : 'false' ?>" aria-controls="<?php echo esc_attr( $el_id.'-collapse-'.$i ) ?>">
            <?php if ( '1' === $icon ) : ?>
            <div class="accordion-shapes"><i class="fa"></i></div>
            <?php endif; ?>
            <div class="title-text"><?php echo esc_html( $paneltitle ) ?></div>
          </a>
        </div>
        <div id="<?php echo esc_attr( $el_id.'-collapse-'.$i ) ?>" class="panel-collapse collapse<?php echo (1===$i) ? ' in' : '' ?>" role"tabpanel" aria-labelledby="<?php echo esc_attr( $el_id.'-'.$i ) ?>">
          <div class="panel-body">
            <?php echo do_shortcode( $item['panelcontent'] ) ?>
          </div>
        </div>
      </div>
      <?php $i++; endforeach; ?>
      
    </div>
    <?php
    return ob_get_clean();
  }
  
  /**
	 * Jobstar Message Box Shortcode.
   *
   * @since     1.0.0
	 */
  public function jobstar_message( $atts, $content = '' ) {
    
    extract( shortcode_atts( array(
      'title'    => '',
      'align'    => '',
      'type'     => '',
      'dismiss'  => '',
      'el_class' => '',
      'css_animation' => ''
    ), $atts, 'js_message' ) );
    
    if ( empty( $content ) ) {
      return;
    }
        
    $content  = $this->fix_wpautop( $content );
    $el_class = $this->css_class( $el_class );
    
    if ( ! empty( $css_animation ) ) {
      $el_class .= $this->css_animation( $css_animation );
    }
    
    $type  = !empty( $type ) ? $type : 'info';
    $align = !empty( $align ) ? $align : 'left';
    
    $classses = array(
      'alert',
      'alert-' . esc_attr( $type ),
      'text-' . esc_attr( $align ),
    );
    if ( '1' === $dismiss ) {
      $classses[] = 'alert-dismissible';
    }
    $classses = array_merge( $classses, explode( ' ', $el_class ) );
    
    ob_start(); ?>
    <div class="<?php echo esc_attr( join( ' ', $classses ) ); ?>" role="alert">
      <?php if ( '1' === $dismiss ) : ?>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
      <?php endif; ?>
      <?php if ( !empty( $title ) ) : ?>
      <h4><?php echo esc_html( $title ); ?></h4>
      <?php endif; ?>
      <?php echo wptexturize( $content ); ?>
    </div>
    <?php
    return ob_get_clean();
  }
  
  /* ====================================================
	 *             WP Job Manager Shortcodes
   =================================================== */
  
  /**
	 * Locate template path if file exists.
   *
   * @since  1.0.0
   * @access public
	 */
	public function locate_template( $template, $template_name, $template_path ) {

		$default_path = trailingslashit( JOBSTAR_CORE_PATH . 'templates' );

		if ( is_dir( $default_path ) && file_exists( $default_path.$template_name ) ) {
			$template = $default_path.$template_name;
		}

		return $template;
	}
  
  /**
	 * Jobstar Job Search Form Shortcode.
   *
   * @since     1.0.0
	 */
  public function jobstar_job_search_form( $atts ) {
    
    extract( shortcode_atts( array(
      'headline'=> '',
      'type'    => 'inline',
      'submit'  => '1',
      'el_class' => '',
      'css_animation' => ''
    ), $atts, 'js_job_search_form' ) );
    
    wp_enqueue_script( 'jobstar-core' );
    
    if ( function_exists( 'job_manager_get_permalink' ) ) {
      $target = job_manager_get_permalink('jobs') ? job_manager_get_permalink('jobs') : '';
    } else {
      $target = home_url('/');
    }
    
    $el_class = $this->css_class( $el_class );
    $styles   = $this->build_style_attr( $atts );
    
    if ( ! empty( $css_animation ) ) {
      $el_class .= $this->css_animation( $css_animation );
    }
    
    ob_start(); ?>
    <div class="container-fluid mar-b-20 <?php echo esc_attr( $el_class ); ?>" <?php echo $styles; ?>>
      <?php if ( !empty( $headline ) ) : ?>
      <div class="heading text-center title-underlined">
        <h1><?php echo esc_html( $headline ); ?></h1>
      </div>
      <?php endif; ?>
      <form id="static_jobsearch" class="job_search_form row find-job-form" target="<?php echo esc_url($target); ?>" method="GET">
        <div class="col-md-5 form-wrap input--with-icon--rev">
          <input type="text" name="search_keywords" id="search_keywords" class="def-input search_keywords" placeholder="<?php esc_attr_e( 'Keyword', 'jobstar-core' ); ?>">
          <span class="fa fa-briefcase"></span>
        </div>
        <div class="col-md-5 form-wrap">
          <input type="text" name="search_location" id="search_location" class="def-input search_location" placeholder="<?php esc_attr_e( 'Location', 'jobstar-core' ); ?>">
          <span class="fa fa-compass"></span>
        </div>
        <?php if ( !empty( $submit ) && '0' !== $submit ) : ?>
        <div class="col-md-2 col-xs-12 form-wrap">
          <button type="submit" class="btn btn-submit" name="s" value="1"><i class="fa fa-search"></i></button>
        </div>
        <?php endif; ?>
      </form>
    </div>
    <?php
    return ob_get_clean();
  }
  
  /**
	 * Jobstar Job Listing Grid Shortcode.
   *
   * @since     1.0.0
	 */
  public function jobstar_jobs_grid( $atts ) {
    
    if ( ! class_exists( 'WP_Job_Manager' ) ) {
      return;
    }
    
    extract( shortcode_atts( array(
      'heading'  => '',
      'design'   => 'grid',
      'maxjob'   => '',
      'featured' => '',
      'filter'   => '',
      'filter_by' => 'type',
      'exclude_type' => '',
      'exclude_cat' => '',
      'more'     => '',
      'more_link'=> '',
      'el_class' => ''
    ), $atts, 'js_jobs_grid' ) );
    
    wp_enqueue_script( 'jobstar-core' );
    wp_enqueue_script( 'shuffle.js' );
    
    $job_title = $job_filter = $job_content = $job_more = $job_sizer = '';
    $design    = !empty( $design ) ? sanitize_key( $design ) : 'grid';
    $exclude   = 'cat' === $filter_by ? $exclude_cat : $exclude_type;
    $the_tax   = 'cat' === $filter_by ? 'job_listing_category' : 'job_listing_type';
    $grid_id   = $design.'-'.substr( uniqid(), 0, 4 );
    
    if ( ! empty( $heading ) ) {
      $job_title .= '<div class="heading"><h2>'.esc_html( $heading ).'</h2></div>';
    }
    
    $el_class = $this->css_class( $el_class );
    $styles   = $this->build_style_attr( $atts );
    $job_sizer= 'grid' === $design ? 'col-md-4 col-s-6 ' : '';
    
    $css_class = array(
      'job-info',
      'job-info-'.esc_attr( $design ),
      $el_class
    );
    
    if ( '1' === $filter && !empty( $filter_by ) ) {
      $f = 1;
      $exclude     = $this->parse_comma_separated( $exclude );
      $term_array  = get_terms( $the_tax, 'orderby=count&hide_empty=0&exclude='.$exclude );
      $term_length = count( $term_array );
      if ( ! empty( $term_array ) && ! is_wp_error( $term_array ) ) {
        $job_filter .= '<div class="job-filter">';
        $job_filter .= '<a href="#" data-group="all" class="current">'.esc_attr__('All','jobstar-core').'</a> / ';
        foreach ( $term_array as $term_filter ) {
          $job_filter .= '<a href="#'.esc_attr($term_filter->slug).'-jobs" data-group="'.esc_attr($term_filter->slug).'">'.esc_html($term_filter->name).'</a>';
          $job_filter .= ( $f === $term_length ) ? '' : ' / ';
          $f++;
        }
        $job_filter .= '</div>';
      }
    }
    
    if ( '1' === $more && !empty( $more_link ) ) {
      $job_more .= '<div class="job-info-more clearfix">';
      $job_more .= $this->build_link( $more_link, 'def-btn btn-bg-primary', esc_attr__( 'Show More', 'jobstar-core' ), false );
      $job_more .= '</div>';
    }
    
    $job_args = array(
      'posts_per_page' => !empty( $maxjob ) ? absint( $maxjob ) : 6
    );
    if ( '1' === $featured ) {
      $job_args['featured'] = true;
    }
    $jobs = get_job_listings( $job_args );
    
    ob_start(); ?>
    <div class="<?php echo esc_attr( join( ' ', $css_class ) ); ?>" <?php echo $styles; ?>>
      
      <?php echo $job_title; ?>
      
      <?php echo $job_filter; ?>
      
      <div id="<?php echo $grid_id ?>" class="job-content job-info-container clearfix">
      <?php if ( $jobs->have_posts() ) :
        echo '<div class="' . $job_sizer . 'grid_sizer"></div>';
        while ( $jobs->have_posts() ) : $jobs->the_post();
          get_job_manager_template( 'content-jobinfo-'.$design.'.php', array( 'the_tax' => $the_tax ) );
        endwhile;
      else:
        get_job_manager_template_part( 'content', 'no-jobs-found' );
      endif; // $jobs
      ?>
      </div>
      
      <?php echo $job_more; ?>
      
    </div>
    <?php
    $return = ob_get_clean();
    wp_reset_postdata();
    return $return;
  }
  
  /**
	 * Jobstar Job Listing Spotlight Widget Shortcode.
   *
   * @since     1.0.0
	 */
  public function jobstar_jobs_spotlight( $atts ) {
    
    if ( ! class_exists( 'WP_Job_Manager' ) ) {
      return;
    }
    
    extract( shortcode_atts( array(
      'heading'  => '',
      'maxjob'   => '',
      'featured' => '',
      'exclude'  => '',
      'nav'      => '',
      'el_class' => ''
    ), $atts, 'js_jobs_spotlight' ) );
    
    wp_enqueue_script( 'jobstar-core' );
    
    $job_title = $job_content = $job_navs = '';
    $wie_id = 'job-spotlight-'.substr( uniqid(), 0, 4 );
    
    if ( ! empty( $heading ) ) {
      $job_title .= '<h2 class="heading">'.esc_html( $heading ).'</h2>';
    }
    
    $el_class = $this->css_class( $el_class );
    $styles   = $this->build_style_attr( $atts );
    $exclude  = (array) $this->parse_comma_separated( $exclude, true );
    
    $css_class = array(
      'job-spotlight',
      $el_class
    );
    
    $job_args = array(
      'posts_per_page' => !empty( $maxjob ) ? absint( $maxjob ) : 6
    );
    if ( '1' === $featured ) {
      $job_args['featured'] = true;
    }
    $query = get_job_listings( $job_args );
    $jobs  = $query->get_posts();
    
    ob_start(); ?>
    <div class="<?php echo esc_attr( join( ' ', $css_class ) ); ?>" <?php echo $styles; ?>>
      
      <?php echo $job_title; ?>
      
      <div class="job-content carousel slide" id="<?php echo sanitize_title($wie_id); ?>" data-ride="carousel">
        
        <?php if ( '1' === $nav ) : ?>
        <a class="left carousel-control" href="#<?php echo sanitize_title($wie_id); ?>" role="button" data-slide="prev">
          <div class="control-button left"><i class="fa fa-angle-left"></i></div>
          <span class="sr-only">Previous</span>
        </a>
        <a class="right carousel-control" href="#<?php echo sanitize_title($wie_id); ?>" role="button" data-slide="next">
          <div class="control-button right"><i class="fa fa-angle-right"></i></div>
          <span class="sr-only">Next</span>
        </a>
        <?php endif; ?>
        
        <div class="carousel-inner" role="listbox">
        <?php if ( is_array( $jobs ) && !empty( $jobs ) ) :
          $j = 1;
          foreach ( $jobs as $job ) :
          if ( in_array( $job->ID, $exclude ) ) continue;
          $job_type_id  = '';
          $company_logo = get_the_company_logo( $job->ID, 'medium' );
          $company_name = get_the_company_name( $job->ID );
          $job_type     = get_the_job_type( $job->ID );
          if ( ! empty( $job_type ) ) {
            $job_type_id = sanitize_title( $job_type->name );
          }
          $x_class = array( 'item', ( ( $j === 1 ) ? 'active' : '' ) );
          ?>
          <div class="<?php echo join( ' ', get_job_listing_class( $x_class, $job->ID ) ); ?>">
            <div class="content-wrap">
              <div class="company-logo valign-wrap">
                <div class="valign-middle">
                  <?php if ( !empty( $company_logo ) ) : ?>
                  <img src="<?php echo esc_url( $company_logo ); ?>" alt="<?php echo esc_attr( $company_name ); ?>">
                  <?php endif; ?>
                </div>
              </div>
              <div class="company-info type-<?php echo $job_type_id; ?>">
                <div class="job-type">
                  <span><?php echo esc_attr( $job_type->name ); ?></span>
                </div>
                <div class="job-position">
                  <?php echo get_the_title( $job->ID ); ?>
                </div>
                <div class="job-description">
                  <?php echo get_the_excerpt(); ?>
                </div>
                <div class="release-date">
                  <?php echo get_the_date( get_option( 'date_format' ), $job->ID ); ?>
                </div>
                <a href="<?php echo esc_url( get_the_job_permalink( $job->ID ) ); ?>" class="read-more" rel="permalink">
                  <span class="text"><?php esc_attr_e( 'Details', 'jobstar' ); ?></span>
                  <span class="right-arrow"><i class="fa fa-angle-right"></i></span>
                </a>
              </div>
            </div>
          </div>
          <?php $j++;
          endforeach;
        endif;
        ?>
        </div>
        
      </div>
      
    </div>
    <?php
    $return = ob_get_clean();
    wp_reset_postdata();
    return $return;
  }
  
  /**
	 * Jobstar Job Listing Categories Shortcode.
   *
   * @since     1.0.0
	 */
  public function jobstar_job_categories( $atts, $content = null ) {
    
    if ( ! class_exists( 'WP_Job_Manager' ) ) {
      return;
    }
    
    if ( ! get_option( 'job_manager_enable_categories', false ) ) {
      return;
    }
    
    extract( shortcode_atts( array(
      'heading'  => '',
      'style'    => 'box',
      'maxcat'   => '8',
      'parent'   => '',
      'exclude'  => '',
      'orderby'  => '',
      'css_animation' => '',
      'el_class' => ''
    ), $atts, 'js_job_categories' ) );
    
    $header = $animate = '';
    $wrap_id = 'job-cat-'.substr( uniqid(), 0, 4 );
    $style   = !empty( $style ) ? esc_attr( $style ) : 'box';
    $taxonomy = get_option( 'job_manager_enable_categories', false ) ? 'job_listing_category' : 'job_listing_type';
    
    $el_class = $this->css_class( $el_class );
    $styles   = $this->build_style_attr( $atts );
    if ( ! empty( $css_animation ) ) {
      $animate .= $this->css_animation( $css_animation );
    }
    
    if ( ! empty( $heading ) ) {
      $header .= '<h2 class="heading">'.esc_html( $heading ).'</h2>';
    }
    
    $cat_args = array(
      'taxonomy'    => $taxonomy,
      'orderby'     => in_array( $orderby, array( 'date', 'term_id', 'name', 'slug', 'count' ) ) ? esc_attr($orderby) : 'name',
      'hide_empty'  => false,
      'exclude'     => $this->parse_comma_separated( $exclude ),
      'number'      => absint( $maxcat )
    );
    $categories = get_terms( $cat_args );
    
    $css_class = array(
      'js-job-cats',
      'js-job-cats-'.$style,
      'clearfix',
      $el_class
    );
    
    ob_start(); ?>
    <div id="<?php echo esc_attr( $wrap_id ) ?>" class="<?php echo esc_attr( join( ' ', $css_class ) ); ?>" <?php echo $styles; ?>>
      
      <?php echo $header; ?>
      
    <?php 
    if ( ! empty( $categories ) && !is_wp_error( $categories ) ) :
    foreach ( $categories as $cnt => $term ) { ?>
      <div class="col-md-3 col-sm-3 col-xs-6 category-wrap job-cat_<?php echo esc_attr( $term->slug.' '.$animate ) ?>">
        <?php $a_title = sprintf( __( 'View all post filed under %s', 'jobstar-core' ), $term->name ); ?>
        <a href="<?php echo esc_url( get_term_link( $term ) ) ?>" class="category-link" title="<?php echo esc_attr( $a_title ) ?>">
          <div class="category-content valign-wrap <?php echo (++$cnt%2 ? 'primary' : 'secondary'); ?>">
            <div class="category-logo valign-middle">
              <?php
              $termicon = get_term_meta( $term->term_id, '_job_cat_icon', true );
              $termicon = !empty( $termicon ) ? $termicon : 'fa-tag';
              echo '<i class="fa ' . esc_attr( $termicon ) . '"></i>';
              ?>
              <p><?php echo esc_html( $term->name ) ?></p>
            </div>
          </div>
        </a>
      </div>
    <?php
    } //endforeachx
    endif;
    ?>
      
    </div>
    <?php
    return ob_get_clean();
  }
  
}
$GLOBALS['Jobstar_Core_Shortcodes'] = new Jobstar_Core_Shortcodes;