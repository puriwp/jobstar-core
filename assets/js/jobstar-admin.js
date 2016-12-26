window.carbon = window.carbon || {};

(function($,carbon) {  
	"use strict";
  
  if ( typeof carbon.fields === 'undefined' ) {
    return false;
  }

  function loadSelectIconElements() {

    var $self = $( '.carbon-container .fontawesome_select_icon .field-holder' ),
        $select = $self.find( 'select' ), value = $select.val();

    $( '<i class="icon-holder"></i>' ).prependTo( $self );
    
    $select.chosen({
      allow_single_deselect: true,
      search_contains: true
    });

    if ( value.length ) {
      $( 'i.icon-holder', $self ).attr( 'class', 'icon-holder fa fa-2x ' + value );
    }

    $select.on( 'change', function (e) {
      $( 'i.icon-holder', $self ).attr( 'class', 'icon-holder fa fa-2x ' + $(this).val() );
    });
  }

  $(document).ready( loadSelectIconElements );

})( window.jQuery, window.carbon );