<?php

require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function generateSheet($file) {
  $parts = explode('.', $file);
  $filename = $parts[0];
  $extension = ucfirst( $parts[1] );

  $spreadsheet = new Spreadsheet();
  $inputFileType = $extension;
  $inputFileName = __DIR__ .'/../uploads/' . $file;
  
  $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
  $reader->setReadDataOnly(true);
  $worksheetData = $reader->listWorksheetInfo($inputFileName);
  
  $html = '';

  foreach ($worksheetData as $worksheet) {
    $sheetName = $worksheet['worksheetName'];    
    $html .= "<h3>". $sheetName ."</h3>";
    $reader->setLoadSheetsOnly($sheetName);
    $spreadsheet = $reader->load($inputFileName);
    $worksheet = $spreadsheet->getActiveSheet();
    // table
    $html .= '<table id="xtabla-table"><thead>';
    // headers
    $count = 0;
    foreach ($worksheet->toArray() as $sheet):
      if ( $count === 0 ):
        $html .= '<tr>';
          foreach($sheet as $cell):
            $html .= '<th>' . $cell . '</th>';
          endforeach;
        $html .= '</tr>';
        $count++;
      endif;
    endforeach;
    $html .= '</thead><tbody>';
    // body
    foreach ($worksheet->toArray() as $sheet):
      if ( $count > 1 ):
        $html .= '<tr>';
          foreach($sheet as $cell):
            $html .= renderCell($cell);
          endforeach;
        $html .= '</tr>';
      endif;
      $count++;
    endforeach;
    $html .= '</tbody></table>';
  }

  return $html;
}

function renderCell( $cell ) {
  if ( strpos($cell, 'http://') !== false || strpos($cell, 'https://') !== false ) {
    $cell = '<a href="'. $cell .'" target="_blank" rel="noreferrer noopener">' . $cell . '</a>';
  }
  return '<th>' . $cell . '</th>';
}

function filterForSpreadsheets( $doc ) {
  return strpos($doc, '.xlsx') !== false || strpos($doc, '.csv') !== false;
}

function get_spreadsheets() {
  $dir = __DIR__ . '/../uploads';
  $docs = scandir($dir);
  $spreadsheets = array_filter($docs, 'filterForSpreadsheets');
  return count($spreadsheets) > 0 ? $spreadsheets : false;
}