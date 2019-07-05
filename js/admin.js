(function($) {
  const { ajax_url } = wp_data
  const $customCSS = $('#xtable-custom-css')
  const form = document.querySelector('form')
  const copyEmailBtn = Array.from(document.getElementsByClassName('copy-shortcode'))
  var tabs = document.getElementsByClassName('tab')
  var tabContent = document.getElementsByClassName('tab-content')

  const animationName = 'animated heartBeat fast';
  const animationend = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend'
  const copyMsg = 'copy'

  let intervalId

  // init codemirror
  wp.codeEditor.initialize($customCSS, cm_settings)

  function copyToClipboard() {
    var shortcode = this
    var range = document.createRange();  
    range.selectNode(shortcode);
    window.getSelection().addRange(range);    
    try {  
      var successful = document.execCommand('copy');  
      var msg = successful ? 'successful' : 'unsuccessful';  
      console.log('Copy email command was ' + msg);  
      shortcode.setAttribute('data-msg', 'copied!')
      document.documentElement.style.setProperty('--copyMsgColor', 'green')
      clearInterval(intervalId)
      intervalId = setInterval(() => {
        shortcode.setAttribute('data-msg', copyMsg)
        document.documentElement.style.setProperty('--copyMsgColor', 'inherit')
      }, 1400)
      // bounce
      // $(shortcode).addClass(animationName).one(animationend,function() {
      //   $(this).removeClass(animationName);
      // });
    } catch(err) {  
      console.log('Oops, unable to copy');  
    }  
    // Remove the selections - NOTE: Should use
    removeRange(range) // when it is supported  
    window.getSelection().removeAllRanges();
  }

  function deleteSpreadsheet() {
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
  copyEmailBtn.forEach(btn => {
    btn.setAttribute('data-msg', copyMsg);
    btn.addEventListener('click', copyToClipboard)
  }) 

  // Tabs
  function handleTabClick(e) {
    e.preventDefault()
    var targetId = e.target.getAttribute('href').replace('#', '')
    Object.keys(tabs).forEach(function(el) {
      tabs[el].classList.remove('active')
    })
    Object.keys(tabContent).forEach(function(el) {
      tabContent[el].classList.remove('show')
      if (tabContent[el].id === targetId) {
        tabContent[el].classList.add('show')
      }
    })
    e.target.classList.add('active')
  }

  // Tabs addListeners
  Object.keys(tabs).forEach(function(el) {
    tabs[el].addEventListener('click', handleTabClick)
  })

})(jQuery)