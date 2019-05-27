(function($) {
  const { ajax_url: AJAX_URL } = wp_data
  const list = document.getElementById('shortcodes-list')
  // const $viewButton = $('.view-spreadsheet')

  // $viewButton.on('click', function(e) {
  //   console.log($(this).data('spreadsheetid'))
  // })

  const refreshSheets = () => {
    var data = {
      'action': 'xtabla_actions',
      'do': 'get_spreadsheets',
    }
    jQuery.post(AJAX_URL, data, function(response) {
      console.log(JSON.parse(response))
    })
  }

  $('body').on('click', '.delete-spreadsheet', function(e) {
    const $btn = $(this)
    const file = $btn.data('spreadsheetid')
    const confirmed = confirm('Are you sure you want to delete ' + file + '?')
    if (confirmed) {
      var data = {
        'action': 'xtabla_actions',
        'do': 'delete_spreadsheet',
        file
      }
      jQuery.post(AJAX_URL, data, function(response) {
        $('#sheet-' + file).remove()
      })
    }
  })

  const form = document.querySelector('form')
  form.addEventListener('submit', e => {
    e.preventDefault()
    const files = document.querySelector('[type=file]').files
    const formData = new FormData()
    for (let i = 0; i < files.length; i++) {
      let file = files[i]
      formData.append('files[]', file)
    }
    formData.append('action', 'xtabla_actions')
    formData.append('do', 'upload_spreadsheet')
    fetch(AJAX_URL, {
      method: 'POST',
      body: formData,
    }).then(response => {
      // tb_remove()
      location.reload()
    }).catch(err => console.log(err))
  })

})(jQuery)