# Xtabla
Upload and edit Spreadsheets. Display in your site using the shortcode `[xtabla file="example.xlsx"]`.

Uploaded files saved in `wp-content/uploads/xtabla-uploads`.

# Setup
Run `composer install` and `npm install` to install dependencies.

## Server Requirements
Same as [PHPOffice/PhpSpreadsheet](https://github.com/PHPOffice/PhpSpreadsheet).

### Project Scope
- [x] editing cell data 
- [ ] sanitize input
- [x] click to copy shortcode to clipboard
- [ ] add/delete rows and columns
- [x] User can upload asset to media library, URL to asset is saved in cell
- [x] Hyperlinks with file extentions (eg: .pdf) render as linked icons

### Future Development
- [ ] Spreadsheet Filtering
- [ ] Spreadsheet Pagination