<?php

require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

$imageFileExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp'];

function update_spreadsheet() {
  $file = $_POST['file'];
  $cellId = $_POST['cellId'];
  $value = $_POST['value'];
  if ( !empty($file) && !empty($cellId) && !empty($value) ) {
    // current directory
    $wp_admin_dir = getcwd(); // wp-admin

    chdir(XTABLA_UPLOADS_DIR);

    $parts = explode('.', $file);
    $filename = $parts[0];
    $extension = ucfirst( $parts[1] );
    $inputFilePath = XTABLA_UPLOADS_DIR .'/' . $file;
    
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load( $inputFilePath );
    $worksheet = $spreadsheet->getActiveSheet();
    $worksheet->getCell($cellId)->setValue($value);

    if ( $extension === 'Xlsx' ) {
      $writer = new Xlsx($spreadsheet);
    } else if ( $extension === 'Csv' ) {
      $writer = new Csv($spreadsheet);
    }

    $tempFile = 'temp.' . strtolower($extension);

    $writer->save( $tempFile );

    rename($tempFile, $file);

    chdir($wp_admin_dir);

    echo $file . ' saved!';
    http_response_code(200);
  } else {
    echo 'Bad request';
    http_response_code(400);
  }
}

function columnLetter($c){
  $c = intval($c);
  if ($c <= 0) return '';
  $letter = '';
  while($c != 0) {
    $p = ($c - 1) % 26;
    $c = intval(($c - $p) / 26);
    $letter = chr(65 + $p) . $letter;
  }
  return $letter;
}

function column_number($col){
  $col = str_pad($col,3, '0' , STR_PAD_LEFT);
  $i = 0;
  if ($col{0} != '0') {
  $i = ((ord($col{0}) - 64) * 676)+26;
  $i += ($col{1} == '0') ? 0 : (ord($col{1}) - 65) * 26;
  } else {
  $i += ($col{1} == '0') ? 0 : (ord($col{1}) - 64) * 26;
  }
  $i += ord($col{2}) - 64;
  return $i;
}

function renderCellContents( $cell ) {
  global $imageFileExtensions;
  if ( strpos($cell, 'http://') !== false || strpos($cell, 'https://') !== false ) {
    $preceedingEl = '<span class="dashicons dashicons-format-aside"></span>';
    foreach ( $imageFileExtensions as $ext) {
      if ( strpos($cell, $ext) ) {
        $preceedingEl = '<img src="' . $cell . '" width="50" height="auto" />';
        break;
      }
    }
    $cell = '<a href="'. $cell .'" target="_blank" rel="noreferrer noopener">' . $preceedingEl . '<span class="hidden-cell-val" style="display: none;">' . $cell . '</span></a>';
  }
  return $cell;
}

function renderSheets($file) {
  $parts = explode('.', $file);
  $filename = $parts[0];
  $extension = ucfirst( $parts[1] );

  $spreadsheet = new Spreadsheet();
  $inputFileType = $extension;
  $inputFileName = XTABLA_UPLOADS_DIR .'/' . $file;

  $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader( $inputFileType );
  $reader->setReadDataOnly(TRUE);
  $spreadsheet = $reader->load( $inputFileName );
  
  $worksheet = $spreadsheet->getActiveSheet();
  
  $html = '';
  $html .= '<div class="table-wrap">';
  $html .= '<table class="form-table widefat xtabla-table" data-spreadsheetid="' . $file . '">' . PHP_EOL;
  foreach ($worksheet->getRowIterator() as $row) {
    $html .= '<tr>' . PHP_EOL;
    $cellIterator = $row->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(FALSE);
    foreach ($cellIterator as $cell) {
      $html .= '<td id="' . $cell->getCoordinate() . '">';
      $html .= renderCellContents( $cell->getValue() );
      $html .= '</td>' . PHP_EOL;
    }
    $html .= '</tr>' . PHP_EOL;
  }
  $html .= '</table>' . PHP_EOL;
  $html .= '</div>' . PHP_EOL;

  return $html;
}

function filterForSpreadsheets( $doc ) {
  return strpos($doc, '.xlsx') !== false || strpos($doc, '.csv') !== false;
}

function get_spreadsheets() {
  $docs = scandir(XTABLA_UPLOADS_DIR);
  $spreadsheets = array_filter($docs, 'filterForSpreadsheets');
  return count($spreadsheets) > 0 ? $spreadsheets : false;
}

function redirect( $URL ) {
  if ( headers_sent() ) { echo ("<script>location.href='$URL'</script>"); }
  else { header("Location: $URL"); }
  exit;
}

function slugify_filename($string){
  $slug = preg_replace('/[^A-Za-z0-9-.]+/', '-', $string);
  return $slug;
}

function upload_spreadsheet() {
  if (isset($_FILES['files'])) {
    $errors = [];    
    $extensions = ['xlsx', 'csv'];

    $all_files = count($_FILES['files']['tmp_name']);

    for ($i = 0; $i < $all_files; $i++) {
      $file_name = slugify_filename($_FILES['files']['name'][$i]);
      $file_tmp = $_FILES['files']['tmp_name'][$i];
      $file_type = $_FILES['files']['type'][$i];
      $file_size = $_FILES['files']['size'][$i];
      $file_ext = strtolower(end(explode('.', $_FILES['files']['name'][$i])));

      $file = XTABLA_UPLOADS_DIR . '/' . $file_name;

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
}

function delete_spreadsheet() {
  if ( !empty($_POST['file']) ) {
    unlink(XTABLA_UPLOADS_DIR . '/' . $_POST['file']);
    echo $_POST['file'] . ' deleted.';
    http_response_code(200);
  }
}