(function($) {
  const { ajax_url } = wp_data
  const $body = $('body')
  const $tables = $('.xtabla-table')
  const $cells = $body.find('.xtabla-table td:not(.not-editable)')
  const $loading = $body.find('#xtabla-loading')
  const $uploadButton = $('<button class="open-wp-media upload-button"><span class="dashicons dashicons-upload"></span></button>')
  const $cellLabel = $body.find('.cell-label')
  // const $addRowTop = $body.find('.add-row-top')
  // const $addRowBottom = $body.find('.add-row-bottom')
  // const $addColLeft = $body.find('.add-col-left')
  // const $addColRight = $body.find('.add-col-right')

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
      updateSpreadsheetCell($target.get(0), image_url)
    })
  }

  function updateSpreadsheetCell(cell, value) {
    setLoading(true)
    var $self = $(cell)
    params.do = 'update_spreadsheet'
    params.cellId = cell.id
    params.file = $(cell).closest('table').data('spreadsheetid')
    params.value = value
    console.log('[updateSpreadsheetCell] params', params)
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
    updateSpreadsheetCell(this, value)
    return value
  }
  
  $cells.editable(handleCellEdit, {
    cancel    : 'Cancel',
    submit    : 'OK',
    tooltip   : 'Click to edit...'
  })

  $cells.hover(function() {
    const $cell = $(this)
    $cellLabel
      .text(this.id)
      .css({
        top: $cell.position().top,
        left: $cell.position().left
      })  
      .addClass('shown')
    // $addRowTop
    //   .css({
    //     top: $cell.position().top,
    //     left: $cell.position().left + $cell.width() / 2
    //   })  
    //   .addClass('shown')
    // $addRowBottom
    //   .css({
    //     top: $cell.position().top + $cell.height(),
    //     left: $cell.position().left + $cell.width() / 2
    //   })  
    //   .addClass('shown')
    // $addColLeft
    //   .css({
    //     top: $cell.position().top + $cell.height() / 2,
    //     left: $cell.position().left
    //   })  
    //   .addClass('shown')
    // $addColRight
    //   .css({
    //     top: $cell.position().top + $cell.height() / 2,
    //     left: $cell.position().left + $cell.width()
    //   })  
    //   .addClass('shown')
  }, function() {
    $cellLabel.removeClass('shown')
    // $addRowTop.removeClass('shown')
    // $addRowBottom.removeClass('shown')
    // $addRowLeft.removeClass('shown')
    // $addRowRight.removeClass('shown')
  })
  
  $cells.on('click', function() {
    $(this).find('form').append($uploadButton)
  })

  $body.on('click', '.open-wp-media', openWPMediaLibrary)

  $('.add-row').on('click', function() {
    $clonedRow = $tables.find('tr:last-child').clone()
    $clonedRow.children().each(function() {
      $(this).text('')
      var id = $(this).attr('id')
      for (var i = 0; i < id.length; i++) {
        if (!isNaN(parseInt(id[i]))) {
          
        }
      }      
      $(this).editable(handleCellEdit, {
        cancel    : 'Cancel',
        submit    : 'OK',
        tooltip   : 'Click to edit...'
      })
    })
    $tables.append($clonedRow)
  })

})(jQuery)