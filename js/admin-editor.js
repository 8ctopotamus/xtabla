(function($) {
  const { ajax_url } = wp_data
  const $cells = $('.xtabla-table td:not(.not-editable)')
  const $uploadCells = $('.xtabla-table td.wp-media-upload-cell')
  const $loading = $('#xtabla-loading')

  let params = { 'action': 'xtabla_actions' }

  const setLoading = bool => {
    if (bool) {
      $loading.show()
    } else {
      $loading.hide()
    }
  }

  function openWPMediaLibrary(e) {
    e.preventDefault()
    var target = e.target
    var image = wp.media({ 
      title: 'Upload Image',
      multiple: false
    }).open()
    .on('select', function(){
      var uploaded_image = image.state().get('selection').first()
      console.log(uploaded_image)
      var image_url = uploaded_image.toJSON().url
      $(target).text(image_url)
    })
  }

  function updateCell(value, settings) {
    setLoading(true)
    var $self = $(this)
    params.do = 'update_spreadsheet'
    params.file = $(this).closest('table').data('spreadsheetid')
    params.cellId = this.id
    params.value = value
    $.post(ajax_url, params, function(response) {
      $self.addClass('success')
      console.info('Response: ', response)
      setTimeout(() => $self.removeClass('success'), 1000)
    })
    .fail(function(err) {
      $self.addClass('failed')
      console.info('Error: ', err)
      setTimeout(() => $self.removeClass('failed'), 1000)
    })
    .done(() => setLoading(false))
    return value
  }

  $cells.editable(updateCell)
  $uploadCells.on('click', openWPMediaLibrary)

})(jQuery)