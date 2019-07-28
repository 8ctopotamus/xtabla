<?php

function xtable_func( $atts ) {
  if ( isset( $atts['file'] ) ):
    return renderSheets( $atts['file'] );
  else: 
    return __('No file provided.', 'xtable');
  endif;
}

add_shortcode( 'xtable', 'xtable_func' );
