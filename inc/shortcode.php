<?php

require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function createSheet($file) {
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
    
    $html .= "<h4>". $sheetName ."</h4>";
    $reader->setLoadSheetsOnly($sheetName);
    $spreadsheet = $reader->load($inputFileName);
    
    $worksheet = $spreadsheet->getActiveSheet();

    $html .= '<table class="xtabla-table"><thead>';
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
    foreach ($worksheet->toArray() as $sheet):
      if ( $count > 1 ):
        $html .= '<tr>';
          foreach($sheet as $cell):
            $html .= '<th>' . $cell . '</th>';
          endforeach;
        $html .= '</tr>';
      endif;
      $count++;
    endforeach;
    $html .= '</tbody></table>';
  }
  return $html;
}

function xtabla_func( $atts ) {
  if ( isset( $atts['file'] ) ):
    return createSheet( $atts['file'] );
  else: 
    return 'No file provided.';
  endif;
}

add_shortcode( 'xtabla', 'xtabla_func' );
