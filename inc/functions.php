<?php

require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function add_spreadsheet_row() {
  $file = $_POST['file'];
  if (!empty($file)) {
    $wp_admin_dir = getcwd(); // wp-admin
    chdir(XTABLE_UPLOADS_DIR);

    $parts = explode('.', $file);
    $filename = $parts[0];
    $extension = ucfirst( $parts[1] );
    $inputFilePath = XTABLE_UPLOADS_DIR .'/' . $file;
    
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load( $inputFilePath );
    $worksheet = $spreadsheet->getActiveSheet();

    // insert new row
    $num_rows = $worksheet->getHighestRow();
    $worksheet->insertNewRowBefore($num_rows + 1, 1);
    
    if ( $extension === 'Xlsx' ) {
      $writer = new Xlsx($spreadsheet);
    } else if ( $extension === 'Xls' ) {
      $writer = new Xls($spreadsheet);
    } else if ( $extension === 'Csv' ) {
      $writer = new Csv($spreadsheet);
    }
    
    $tempFile = 'temp.' . strtolower($extension);
    
    $writer->save( $tempFile );
    
    rename($tempFile, $file);


    chdir($wp_admin_dir);
    echo $file . ' - New row added!';
  }
}

function add_spreadsheet_column() {
  $file = $_POST['file'];
  if (!empty($file)) {
    $wp_admin_dir = getcwd(); // wp-admin
    chdir(XTABLE_UPLOADS_DIR);

    $parts = explode('.', $file);
    $filename = $parts[0];
    $extension = ucfirst( $parts[1] );
    $inputFilePath = XTABLE_UPLOADS_DIR .'/' . $file;
    
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load( $inputFilePath );
    $worksheet = $spreadsheet->getActiveSheet();

    // insert new row
    $newColNum = column_number($worksheet->getHighestColumn()) + 1;
    $newColLetter = column_letter($newColNum);
    $worksheet->insertNewColumnBefore($newColLetter, 1);
    
    if ( $extension === 'Xlsx' ) {
      $writer = new Xlsx($spreadsheet);
    } else if ( $extension === 'Xls' ) {
      $writer = new Xls($spreadsheet);
    } else if ( $extension === 'Csv' ) {
      $writer = new Csv($spreadsheet);
    }
    
    $tempFile = 'temp.' . strtolower($extension);
    
    $writer->save( $tempFile );
    
    rename($tempFile, $file);

    chdir($wp_admin_dir);
    echo $file . ' - New row added!';
  }
}

function delete_selected_rows_columns() {
  $file = $_POST['file'];
  $selected = $_POST['selected'];
  if ( !empty($file) ) {
    $wp_admin_dir = getcwd(); // wp-admin
    chdir(XTABLE_UPLOADS_DIR);

    $parts = explode('.', $file);
    $filename = $parts[0];
    $extension = ucfirst( $parts[1] );
    $inputFilePath = XTABLE_UPLOADS_DIR .'/' . $file;
    
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load( $inputFilePath );
    $worksheet = $spreadsheet->getActiveSheet();

    foreach ($selected['rows'] as $row) {
      $worksheet->removeRow( $row, 1);
    }

    foreach ($selected['columns'] as $col) {
      $worksheet->removeColumn( $col, 1);
    }
    
    if ( $extension === 'Xlsx' ) {
      $writer = new Xlsx($spreadsheet);
    } else if ( $extension === 'Xls' ) {
      $writer = new Xls($spreadsheet);
    } else if ( $extension === 'Csv' ) {
      $writer = new Csv($spreadsheet);
    }
    
    $tempFile = 'temp.' . strtolower($extension);
    
    $writer->save( $tempFile );
    
    rename($tempFile, $file);

    chdir($wp_admin_dir);
    echo $file . ' - Row and cols removed';
  }
}

function update_spreadsheet() {
  $file = $_POST['file'];
  $cellId = $_POST['cellId'];
  $value = htmlspecialchars($_POST['value']);
  if ( !empty($file) && !empty($cellId) && !empty($value) ) {
    $wp_admin_dir = getcwd(); // wp-admin

    chdir(XTABLE_UPLOADS_DIR);

    $parts = explode('.', $file);
    $filename = $parts[0];
    $extension = ucfirst( $parts[1] );
    $inputFilePath = XTABLE_UPLOADS_DIR .'/' . $file;
    
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load( $inputFilePath );
    $worksheet = $spreadsheet->getActiveSheet();
    $worksheet->getCell($cellId)->setValue($value);
    
    if ( $extension === 'Xlsx' ) {
      $writer = new Xlsx($spreadsheet);
    } else if ( $extension === 'Xls' ) {
      $writer = new Xls($spreadsheet);
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

function column_letter($c){
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

// add delete row control
function renderAdminControl($controlName, $id) {
  $html = '';
  if ( is_admin() ) {
    switch($controlName) {
      case 'delete-row':
        $html .= '<td class="not-editable row-control">';
        $html .= '<input type="checkbox" class="select-row" value="' . $id . '">';        
        $html .= '</td>';
        break;
      case 'delete-column':
          // add a row to hold column controls
        $html .='<tr id="column-control">';
        $html .='<td class="not-editable"></td>';
        for ($i = 1; $i <= $id; $i++) {
          $cellContent = $i > 0 ? '<input class="select-column" type="checkbox" value="'. column_letter($i) .'">' : '';
          $html .= '<td class="not-editable">' . $cellContent . '</td>';
        }
        $html .= '</tr>';
        break;
    }
  }
  return $html;
}

function renderCellContents( $cell ) {
  global $imageFileExtensions;
  global $spreadsheetFileExtensions;
  global $documentFileExtensions;

  $shouldCreateLink = false;
  $isDownload = false;
  $needsHiddenVal = false;

  $val = $cell->getFormattedValue();
  $content = $val;

  // if image link
  foreach ( $imageFileExtensions as $ext) {
    if ( strpos($cell->getValue(), $ext) ) {
      $content = '<img src="' . $cell->getValue() . '" width="50" height="auto" />';
      $needsHiddenVal = true;
      break;
    }
  }

  // if PDF link
  foreach ( array_merge($documentFileExtensions, $spreadsheetFileExtensions) as $ext) {
    if ( strpos($cell->getValue(), $ext) ) {
      $content = '<img class="download-file-icon" src="' . plugin_dir_url( __DIR__ ) . "/img/download-file-icon.svg" . '" />';
      $needsHiddenVal = true;
      // Supply filename to download attribute
      $pathArr = explode("/", $cell->getValue());
      $isDownload = $pathArr[count($pathArr) - 1];
      break;
    }
  }

  // render the result
  $html = '';
  $isLink = strpos($val, 'http://') !== false || strpos($val, 'https://') !== false;

  // if cell value is a link
  if ( $isLink ) {
    $url = $val;
    if ( $cell->hasHyperlink() ) {
      $url = $cell->getHyperlink()->getUrl();
    }
    // open link
    $html .= '<a href="' . $url . '" target="_blank" rel="noreferrer noopener" download='. $isDownload . '>';
  }
  
  $html .= $content;

  if ($needsHiddenVal) {
    $html .= '<small class="hidden-cell-val">' . $cell->getValue() . '</small></a>';
  }

  // close link
  if ( $isLink ) {
    $html .= '</a>';
  }

  return $html;
}

function renderSheets($file) {
  $html = '';
  $parts = explode('.', $file);
  $filename = $parts[0];
  $extension = ucfirst( $parts[1] );
  
  $spreadsheet = new Spreadsheet();
  $inputFileType = $extension;
  $inputFileName = XTABLE_UPLOADS_DIR .'/' . $file;
  
  if (!file_exists($inputFileName)) {
    $html = '<strong>' . $file . '</strong> does not exist.';
    return $html;
  }

  $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader( $inputFileType );
  $spreadsheet = $reader->load( $inputFileName );
  
  $worksheet = $spreadsheet->getActiveSheet();

  $html .= '<div class="table-wrap">';
  $html .= '<table class="form-table widefat xtable-table" data-spreadsheetid="' . $file . '">' . PHP_EOL;
  $rowCount = 1;
  $html .= renderAdminControl('delete-column', column_number($worksheet->getHighestColumn()));
  
  foreach ($worksheet->getRowIterator() as $row) {
    $cellIterator = $row->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(FALSE);
    $html .= '<tr id="row-' . $rowCount . '">' . PHP_EOL;
    $html .= renderAdminControl('delete-row', $rowCount);
    foreach ($cellIterator as $cell) {
      // handle merged cells
      $rowspan = 1;
      if ( !is_admin() && $cell->isInMergeRange() ) {
        if ( $cell->isMergeRangeValueCell() ) {
          $rowspan = count( $worksheet->rangeToArray( $cell->getMergeRange() ) );
        } else {
          continue;
        }
      }
      // render TD
      $html .= '<td id="' . $cell->getCoordinate() . '" rowspan="' . $rowspan . '">';
      $html .= renderCellContents( $cell );
      $html .= '</td>' . PHP_EOL;
    }
    $html .= '</tr>' . PHP_EOL;
    $rowCount++;
  }
  $html .= '</table>' . PHP_EOL;
  $html .= '</div>' . PHP_EOL;

  return $html;
}

function filterForSpreadsheets( $doc ) {
  return strpos($doc, '.xlsx') !== false || 
         strpos($doc, '.xls') !== false || 
         strpos($doc, '.csv') !== false;
}

function get_spreadsheets() {
  $docs = scandir(XTABLE_UPLOADS_DIR);
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
    $extensions = ['csv', 'xlsx', 'xls'];

    $all_files = count($_FILES['files']['tmp_name']);

    for ($i = 0; $i < $all_files; $i++) {
      $file_name = slugify_filename($_FILES['files']['name'][$i]);
      $file_tmp = $_FILES['files']['tmp_name'][$i];
      $file_type = $_FILES['files']['type'][$i];
      $file_size = $_FILES['files']['size'][$i];
      $file_ext = strtolower(end(explode('.', $_FILES['files']['name'][$i])));

      $file = XTABLE_UPLOADS_DIR . '/' . $file_name;

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
    unlink(XTABLE_UPLOADS_DIR . '/' . $_POST['file']);
    echo $_POST['file'] . ' deleted.';
    http_response_code(200);
  }
}