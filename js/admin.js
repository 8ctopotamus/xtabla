(function($) {
  const { ajax_url } = wp_data
  const form = document.querySelector('form')

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
      $.post(ajax_url, data, function(response) {
        $('#sheet-' + file).remove()
      })
    }
  })

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
    fetch(ajax_url, {
      method: 'POST',
      body: formData,
    }).then(response => {
      location.reload()
    }).catch(err => console.log(err))
  })

})(jQuery)