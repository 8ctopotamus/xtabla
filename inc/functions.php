<?php

require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

function update_spreadsheet() {
  $file = $_POST['file'];
  $cellId = $_POST['cellId'];
  $value = $_POST['value'];
  if ( !empty($file) && !empty($cellId) && !empty($value) ) {
    // current directory
    $wp_admin_dir = getcwd();

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
    http_response_code(200);
  }
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
  
  $html = '<table class="form-table widefat xtabla-table" data-spreadsheetid="' . $file . '">' . PHP_EOL;
  foreach ($worksheet->getRowIterator() as $row) {
    $html .= '<tr>' . PHP_EOL;
    $cellIterator = $row->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(FALSE);
    foreach ($cellIterator as $cell) {
      $html .= '<td id="' . $cell->getCoordinate() . '">';
      $html .= $cell->getValue();
      $html .= '</td>' . PHP_EOL;
    }
    $html .= '</tr>' . PHP_EOL;
  }
  $html .= '</table>' . PHP_EOL;


  // $parts = explode('.', $file);
  // $filename = $parts[0];
  // $extension = ucfirst( $parts[1] );

  // $spreadsheet = new Spreadsheet();
  // $inputFileType = $extension;
  // $inputFileName = XTABLA_UPLOADS_DIR .'/' . $file;
  
  // $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
  // $reader->setReadDataOnly(true);
  // $worksheetData = $reader->listWorksheetInfo($inputFileName);
  
  // $html = '';

  // foreach ($worksheetData as $worksheet) {
  //   $sheetName = $worksheet['worksheetName'];    
  //   $html .= "<h3>". $sheetName ."</h3>";
  //   $reader->setLoadSheetsOnly($sheetName);
  //   $spreadsheet = $reader->load($inputFileName);
  //   $worksheet = $spreadsheet->getActiveSheet();
  //   // table
  //   $html .= '<table class="form-table widefat xtabla-table"><thead>';
  //   // headers
  //   $count = 0;
  //   foreach ($worksheet->toArray() as $sheet):
  //     if ( $count === 0 ):
  //       $html .= '<tr>';
  //         foreach($sheet as $cell):
  //           var_dump($cell->getCoordinate() );
  //           $html .= '<th>' . $cell . '</th>';
  //         endforeach;
  //       $html .= '</tr>';
  //       $count++;
  //     endif;
  //   endforeach;
  //   $html .= '</thead><tbody>';
  //   // body
  //   foreach ($worksheet->toArray() as $sheet):
  //     if ( $count > 1 ):
  //       $html .= '<tr>';
  //         foreach($sheet as $cell):
  //           $html .= renderCell($cell);
  //         endforeach;
  //       $html .= '</tr>';
  //     endif;
  //     $count++;
  //   endforeach;
  //   $html .= '</tbody></table>';
  // }

  return $html;
}

function renderCell( $cell ) {
  if ( strpos($cell, 'http://') !== false || strpos($cell, 'https://') !== false ) {
    $cell = '<a href="'. $cell .'" target="_blank" rel="noreferrer noopener">' . $cell . '</a>';
  }
  return '<td>' . $cell . '</td>';
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