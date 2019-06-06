(function($) {
  const { ajax_url } = wp_data
  const $cells = $('.xtabla-table td')
  let data = { 'action': 'xtabla_actions' }
  $cells.editable(function(value, settings ) {
    data.value = value
    data.cellId = this.id
    data.do = 'update_spreadsheet'
    data.file = $(this).closest('table').data('spreadsheetid')
    // console.table({ data, value, settings })
    $.post(ajax_url, data, function(response) {
      console.info('Response: ', response)
    })
    return value
  },
    // { 
    //   callback: function(result, settings, submitdata) { console.log(result) }
    // }
  );
})(jQuery)