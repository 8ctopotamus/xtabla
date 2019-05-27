(function($) {
  const { ajax_url: AJAX_URL } = wp_data
  const $viewButton = $('.view-spreadsheet')
  const $deleteButton = $('.delete-spreadsheet')

  // $viewButton.on('click', function(e) {
  //   console.log($(this).data('spreadsheetid'))
  // })

  $deleteButton.on('click', async function(e) {
    const file = $(this).data('spreadsheetid')
    const confirmed = confirm('Are you sure you want to delete ' + file + '?')
    if (confirmed) {
      var data = {
        'action': 'xtabla_actions',
        'do': 'delete_spreadsheet',
        file
      }
      jQuery.post(AJAX_URL, data, function(response) {
        console.log('Got this from the server: ', response)
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
      console.log(response)
    })
  })



})(jQuery)