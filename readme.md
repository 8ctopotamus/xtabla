# Xtable
Upload and edit Spreadsheets. Display in your site using the shortcode `[xtabla file="example.xlsx"]`.

Uploaded files saved in `wp-content/uploads/xtable-uploads`.

*¡Ojo!* _Currently, this folder does not get removed upon uninstallation. You need to manually delete it if you no longer want to keep the spreadsheets on your server._

# Dev Setup
Run `composer install` and `npm install` to install dependencies.

## Server Requirements
Same as [PHPOffice/PhpSpreadsheet](https://github.com/PHPOffice/PhpSpreadsheet).