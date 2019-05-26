<?php

function filterForSpreadsheets ($doc) {
  return strpos($doc, '.xlsx') !== false || strpos($doc, '.csv') !== false;
}

function get_spreadsheets() {
  $dir = __DIR__ . '/../uploads';
  $docs = scandir($dir);
  return array_filter($docs, 'filterForSpreadsheets');
}