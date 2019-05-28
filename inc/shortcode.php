<?php

function xtabla_func( $atts ) {
  if ( isset( $atts['file'] ) ):
    return generateSheet( $atts['file'] );
  else: 
    return 'No file provided.';
  endif;
}

add_shortcode( 'xtabla', 'xtabla_func' );
