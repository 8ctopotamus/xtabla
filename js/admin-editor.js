(function($) {
  const { ajax_url } = wp_data
  const $body = $('body')
  const $cells = $body.find('.xtabla-table td:not(.not-editable)')
  const $loading = $body.find('#xtabla-loading')
  const $cellLabel = $body.find('.cell-label')
  const $uploadButton = $('<button class="open-wp-media"><span class="dashicons dashicons-upload"></span></button>')

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
    var $target = $(this).closest('td')    
    var image = wp.media({ 
      title: 'Upload Image',
      multiple: false
    }).open()
    .on('select', function() {
      var uploaded_image = image.state().get('selection').first()
      var image_url = uploaded_image.toJSON().url
      $target.text(image_url)
      updateSpreadsheet($target.get(0), image_url)
    })
  }

  function updateSpreadsheet(cell, value) {
    setLoading(true)
    var $self = $(cell)
    params.do = 'update_spreadsheet'
    params.cellId = cell.id
    params.file = $(cell).closest('table').data('spreadsheetid')
    params.value = value
    console.log('[updateSpreadsheet] params', params)
    // save file
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
  }

  function handleCellEdit(value, settings) {
    console.log(this)
    updateSpreadsheet(this, value)
    return value
  }
  
  $cells.editable(handleCellEdit, {
    cancel    : 'Cancel',
    submit    : 'OK',
    tooltip   : 'Click to edit...'
  })

  $cells.hover(function() {
    $cellLabel
    .text(this.id)
    .css({
      top: $(this).position().top,
      left: $(this).position().left
    })  
    .addClass('shown')
  }, function() {
    $cellLabel.removeClass('shown')
  })
  
  $cells.on('click', function() {
    $(this).find('form').append($uploadButton)
  })

  $body.on('click', '.open-wp-media', openWPMediaLibrary)

})(jQuery)