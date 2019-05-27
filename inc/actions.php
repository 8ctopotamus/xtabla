<?php
if ( empty($_POST['do']) ) {
  http_response_code(400);
  die();
}

$do = $_POST['do'];
$file = $_POST['file'];

$uploads_path = __DIR__ . '/../uploads/';

// upload
if ( $do === 'upload_spreadsheet'):
  if (isset($_FILES['files'])) {
    $errors = [];    
    $extensions = ['xlsx', 'csv'];

    $all_files = count($_FILES['files']['tmp_name']);

    for ($i = 0; $i < $all_files; $i++) {
      $file_name = $_FILES['files']['name'][$i];
      $file_tmp = $_FILES['files']['tmp_name'][$i];
      $file_type = $_FILES['files']['type'][$i];
      $file_size = $_FILES['files']['size'][$i];
      $file_ext = strtolower(end(explode('.', $_FILES['files']['name'][$i])));

      $file = $uploads_path . $file_name;

      if (!in_array($file_ext, $extensions)) {
          $errors[] = 'Extension not allowed: ' . $file_name . ' ' . $file_type;
      }

      if ($file_size > 2097152) {
          $errors[] = 'File size exceeds limit: ' . $file_name . ' ' . $file_type;
      }

      if (empty($errors)) {
        move_uploaded_file($file_tmp, $file);
      }
    }

    if ($errors) print_r($errors);
  }
endif;


// delete
if ( $do === 'delete_spreadsheet' && !empty($file)):
  unlink($uploads_path . $file);
  echo $file . ' deleted.';
  http_response_code(200);
endif;

wp_die();
