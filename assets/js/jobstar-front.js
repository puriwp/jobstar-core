(function($) {
  
	"use strict";
  
  var shuffleGrid = function() {
    
    var $grid = $('.job-info-container'), //locate what we want to sort 
        $filterOptions = $('.job-info .job-filter'),  //locate the filter categories
        $sizer = $grid.find('.grid_sizer'),    //sizer stores the size of the items

    // Set up button clicks
    setupFilters = function() {
      var $btns = $filterOptions.children();
      $btns.on('click', function(e) {
        e.preventDefault();
        var $this = $(this),
            isActive = $this.hasClass( 'current' ),
            group = isActive ? 'all' : $this.data('group');

        // Hide current label, show current label in title
        if ( !isActive ) {
          $('.job-info .job-filter a').removeClass('current');
        }

        $this.toggleClass('current');

        // Filter elements
        $grid.shuffle( 'shuffle', group );
      });

      $btns = null;
    },

    // Re layout shuffle when images load. This is only needed
    // below 768 pixels because the .picture-item height is auto and therefore
    // the height of the picture-item is dependent on the image
    // I recommend using imagesloaded to determine when an image is loaded
    // but that doesn't support IE7
    listen = function() {
      var debouncedLayout = $.throttle( 300, function() {
        $grid.shuffle('update');
      });

      // Get all images inside shuffle
      $grid.find('img').each(function() {
        var proxyImage;

        // Image already loaded
        if ( this.complete && this.naturalWidth !== undefined ) {
          return;
        }

        // If none of the checks above matched, simulate loading on detached element.
        proxyImage = new Image();
        $( proxyImage ).on('load', function() {
          $(this).off('load');
          debouncedLayout();
        });

        proxyImage.src = this.src;
      });

      // Because this method doesn't seem to be perfect.
      setTimeout(function() {
        debouncedLayout();
      }, 500);
    };
    
    setTimeout(function() {
      listen();
      setupFilters();
    }, 100);

    // instantiate the plugin
    $grid.shuffle({
      itemSelector: '.job_listing',
      sizer: $sizer    
    });
  }
  
  $(document).ready( function () {
    
    if ( $.fn.shuffle ) {
      shuffleGrid();
    }
    
    if ( $.fn.slick ) {
      $( '.slick_carousel' ).slick();
    }
    
    if ( $.fn.countTo && $.fn.waypoint ) {
      $('.js-counters .counter-number').each( function(i, el) {
        var $el = $(el);
        $.waypoints('refresh');
        $el.waypoint( function (direction) {
          var options = $.extend({}, options || {}, $el.data('countToOptions') || {});
          $el.countTo( options );
        }, { 
          triggerOnce: true, 
          offset: 'bottom-in-view', 
          group: 'jobstar-counter' 
        });
      });
    }
    
    if ( $.fn.filterizr ) {
      var filterizd = $( '.filtr-container' ).filterizr({
        layout: 'sameSize',
        selector: '.filtr-container',
        setupControls: false
      });
    }
    
  })
  
})(window.jQuery);