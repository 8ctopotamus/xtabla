<?php
/*
 * Admin scripts and styles
 */
function load_xtabla_wp_admin_assets( $hook ) {  
  wp_register_style( 'xtabla_admin_styles', plugin_dir_url( __DIR__ ) . '/css/admin.css', false, '1.0.0' );
  wp_register_script('xtabla_admin_js', plugin_dir_url( __DIR__ ) . '/js/admin.js', array('jquery'), '', true );

  if ( $hook != 'toplevel_page_xtabla' ) {
    return;
  }

  wp_enqueue_style( 'xtabla_admin_styles' );
  wp_enqueue_script( 'xtabla_admin_js' );
}
add_action( 'admin_enqueue_scripts', 'load_xtabla_wp_admin_assets' );






// require 'vendor/autoload.php';

// use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
 
// $spreadsheet = new Spreadsheet();
 
// $inputFileType = 'Xlsx';
// $inputFileName = './ejemplo-sec.xlsx';
 
// /**  Create a new Reader of the type defined in $inputFileType  **/
// $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
// /**  Advise the Reader that we only want to load cell data  **/
// $reader->setReadDataOnly(true);
 
// $worksheetData = $reader->listWorksheetInfo($inputFileName);

// echo '<pre>';
// foreach ($worksheetData as $worksheet) {
//     $sheetName = $worksheet['worksheetName'];

//     echo "<h4>$sheetName</h4>";
//     /**  Load $inputFileName to a Spreadsheet Object  **/
//     $reader->setLoadSheetsOnly($sheetName);
//     $spreadsheet = $reader->load($inputFileName);
 
//     $worksheet = $spreadsheet->getActiveSheet();
//     print_r($worksheet->toArray());
    
// }
// echo '</pre>';