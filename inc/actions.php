<?php

$do = $_POST['do'];

if ( empty( $do ) ) {
  http_response_code(400);
  die();
}

switch ( $do ) {
  case 'get_spreadsheets':
    get_spreadsheets();
    break;
  case 'upload_spreadsheet':
    upload_spreadsheet();
    break;
  case 'delete_spreadsheet':
    delete_spreadsheet();
    break;
  case 'update_spreadsheet':
    update_spreadsheet();
    break;
}

wp_die();
