(function($) {
  const { ajax_url } = wp_data
  const form = document.querySelector('form')
  const copyEmailBtn = Array.from(document.getElementsByClassName('copy-shortcode'));

  function copyToClipboard(event) {  
    var emailLink = this
    var range = document.createRange();  
    range.selectNode(emailLink);  
    window.getSelection().addRange(range);  
  
    try {  
      // Now that we've selected the anchor text, execute the copy command  
      var successful = document.execCommand('copy');  
      var msg = successful ? 'successful' : 'unsuccessful';  
      console.log('Copy email command was ' + msg);  
    } catch(err) {  
      console.log('Oops, unable to copy');  
    }  
    
    // Remove the selections - NOTE: Should use
    removeRange(range) // when it is supported  
    window.getSelection().removeAllRanges();
    
  }

  function deleteSpreadsheet(e) {
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
  }

  function uploadSpreadsheet(e) {
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
  }

  $('body').on('click', '.delete-spreadsheet', deleteSpreadsheet)
  form.addEventListener('submit', uploadSpreadsheet)
  copyEmailBtn.forEach(btn => btn.addEventListener('click', copyToClipboard)) 


})(jQuery)