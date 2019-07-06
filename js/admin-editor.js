(function($) {
  const { ajax_url } = wp_data
  const $body = $('body')
  const $tables = $body.find('.xtabla-table')
  const $cells = $body.find('.xtabla-table td:not(.not-editable)')
  const $loading = $body.find('#xtabla-loading')
  const $cellLabel = $body.find('.cell-label')
  const $addBtns = $body.find('.add')
  const $deleteBtn = $body.find('.delete')
  const checkboxClasses = '.select-row, .select-column'
  const $deleteCheckboxes = $body.find(checkboxClasses)
  const $uploadButton = $('<button class="open-wp-media upload-button"><span class="dashicons dashicons-upload"></span></button>')

  const editableCellOptions = {
    cancel    : 'Cancel',
    submit    : 'Save',
    tooltip   : 'Click to edit...'
  }

  let params = { 'action': 'xtabla_actions' }
  
  const setLoading = bool => bool ? $loading.show() : $loading.hide()

  const disableAddBtns = bool => $addBtns.attr('disabled', bool)

  const setDeleteBtnDisabled = bool => $deleteBtn.attr('disabled', bool) 
  
  const countChecked = _ => $deleteCheckboxes.filter(":checked").length

  const splitId = id => id.match(/[a-z]+|[^a-z]+/gi)

  function addRow() {
    params.do = 'add_spreadsheet_row'
    params.file = $tables.first().data('spreadsheetid')
    $clonedRow = $tables.find('tr:last-child').clone()
    let splitRowId = splitId($clonedRow.attr('id'))
    console.log('split', splitRowId)
    let newRowId = String(parseInt(splitRowId[1]) - 1)
    newRowId = splitRowId[0] + newRowId
    $clonedRow.attr('id', newRowId)
    $clonedRow.children().each(function(i, el) {
      if (i > 0) {
        const splitCurrentId = splitId(el.id)
        splitCurrentId[1] = parseInt(splitCurrentId[1]) + 1
        const newId = splitCurrentId.join('')
        $(this).attr('id', newId)
        $(this).text('')
        $(this).editable(handleCellEdit, editableCellOptions)
      } else {
        let count = parseInt($(this).find('input[type="checkbox"]').val())
        $(this).find('input[type="checkbox"]').val(count + 1)
      }
    })
    $tables.append($clonedRow)
    $.post(ajax_url, params, function(response) {
      // location.reload()
    })
    .fail(function(err) {
      alert('Addition failed :(')
    })
    .done(() => {
      setLoading(false)
      disableAddBtns(false)
    }) 
  }

  function addColumn() {
    const $rows = $tables.find('tr')
    $rows.each((i, row) => {
      if (i > 0) {
        const splitPrevId = splitId($(row).find('td:last-child').attr('id'))
        const nextLetter = nextString(splitPrevId[0])
        const newColId = nextLetter + splitPrevId[1]
        $newCell = $('<td id="' +  newColId + '"></td>')
        $newCell.editable(handleCellEdit, editableCellOptions)
        $(row).append($newCell)
      } else {
        let $newCell = $(row).find('td:last-child').clone()
        const $input = $newCell.find('input')
        const currentLetter = $input.val()
        const nextLetter = nextString(currentLetter)
        $input.val(nextLetter)
        $(row).append($newCell)
      }
    })
    params.do = 'add_spreadsheet_column'
    params.file = $tables.first().data('spreadsheetid')
    $.post(ajax_url, params, function(response) {
      // location.reload()
    })
    .fail(function(err) {
      alert('Addition failed :(')
    })
    .done(() => {
      setLoading(false)
      disableAddBtns(false)
    })    
  }
  
  function handleCellEdit(value, settings) {
    console.log(this)
    updateCell(this, value)
    return value
  }
  
  function checkIfDeleteBtnShouldBeDisabled() {
    countChecked() > 0 ? 
      setDeleteBtnDisabled(false) : 
      setDeleteBtnDisabled(true)
  }

  function deleteSelected() {
    const approved = confirm('Are you sure?')
    if (!approved) return
    setLoading(true)
    disableAddBtns(true)
    params.do = 'delete_selected_rows_columns'
    params.file = $tables.first().data('spreadsheetid')
    params.selected = { columns: [], rows: [] }
    $checked = $('input[type="checkbox"]:checked')
    $checked.each((i, el) => {
      const $el = $(el)
      if ($el.hasClass('select-row')) {
        params.selected.rows.push($el.val())
      } else if ($el.hasClass('select-column')) {
        params.selected.columns.push($el.val())
      }
    })
    console.log(params.selected)
    $.post(ajax_url, params, function(response) {
      console.log(response)
      location.reload()
    })
    .fail(function(err) {
      alert('Deletion failed :(')
    })
    .done(() => {
      setLoading(false)
      disableAddBtns(false)
    })  
  }
  
  const setHighlight = ($el, checked) => { 
    if (checked) {
      $el.addClass('selected')
    } else {
      $el.removeClass('selected')
    }
  }

  const determineHighlight = el => {
    const { checked, value } = el
    const $el = $(el)
    setHighlight($el.parent(), checked)
    switch( isNaN(parseInt(value)) ) {
      case true: // is a column
        const index = $el.parent().parent().find('td').index($el.parent())
        $('tr').each(function() {
          setHighlight($(this).children().eq(index), checked)
        })
        break
      case false: // is a row
        setHighlight($el.parent().parent(), checked)
        break
    }
  }

  function handleCheckboxChange() {
    determineHighlight(this)

    checkIfDeleteBtnShouldBeDisabled()
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
      updateCell($target.get(0), image_url)
    })
  }

  function nextString(str) {
    if (! str)
        return 'A' // return 'A' if str is empty or null
    let tail = ''
    let i = str.length -1
    let char = str[i]
    // find the index of the first character from the right that is not a 'Z'
    while (char === 'Z' && i > 0) {
        i--
        char = str[i]
        tail = 'A' + tail // tail contains a string of 'A'
    }
    if (char === 'Z') // the string was made only of 'Z'
        return 'AA' + tail
    // increment the character that was not a 'Z'
    return str.slice(0, i) + String.fromCharCode(char.charCodeAt(0) + 1) + tail
  }

  function updateCell(cell, value) {
    setLoading(true)
    var $self = $(cell)
    params.do = 'update_spreadsheet'
    params.cellId = cell.id
    params.file = $(cell).closest('table').data('spreadsheetid')
    params.value = value
    console.log('[updateCell] params', params)
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
  
  $cells.hover(function() {
    const $cell = $(this)
    $cellLabel
      .text(this.id)
      .css({
        top: $cell.position().top,
        left: $cell.position().left
      })  
      .addClass('shown')
  }, function() {
    $cellLabel.removeClass('shown')
  })
  
  $cells.on('click', function() {
    $(this).find('form').append($uploadButton)
  })

  $body.on('click', '.open-wp-media', openWPMediaLibrary)

  $addBtns.on('click', function() {
    setLoading(true)
    disableAddBtns(true)
    const direction = $(this).data('add')
    switch(direction) {
      case 'row':
        addRow()
        break
      case 'column':
        addColumn()
        break
    }
  })
  
  $deleteBtn.on('click', deleteSelected)
  
  $body.on('change', checkboxClasses, handleCheckboxChange)

  $cells.editable(handleCellEdit, editableCellOptions)

  // clear selected on load
  $deleteCheckboxes.each(function() { 
    $(this).attr('checked', false)
  })

  checkIfDeleteBtnShouldBeDisabled()

})(jQuery)