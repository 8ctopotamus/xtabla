(function($) {
  const $xtables = $('.xtable-table')

  function summerizeTable($table) {
    $table.each(function() {
      $table.find('td').each(function() {
        var $this = $(this)
        var col = $this.index()
        var html = $this.html()
        var row = $(this).parent()[0].rowIndex 
        var span = 1
        var cell_above = $($this.parent().prev().children()[col])
  
        // look for cells one above another with the same text
        while (cell_above.html() === html) { // if the text is the same
          span += 1 // increase the span
          cell_above_old = cell_above // store this cell
          cell_above = $(cell_above.parent().prev().children()[col]) // and go to the next cell above
        }
  
        // if there are at least two columns with the same value, 
        // set a new span to the first and hide the other
        if (span > 1) {
          // console.log(span)
          $(cell_above_old).attr('rowspan', span)
          $this.hide()
        }
        
      })
    })
  }

  // make them responsive
  $xtables.basictable()

  // merge cells
  // summerizeTable($xtables)
})(jQuery)