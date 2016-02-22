// Jquery cutomzied fucntion
$(document).ready(function () {
  // preventing click activate with #
  $('a[href^="#"]').click(function(et) {
    et.preventDefault();
    return false;
  });

  // on click getting SUM or populate voted
  $('a').click(function(){
    var colorName = $(this).attr("data-name");
    if ( colorName == 'total'){
      var sum = 0;
      // iterate through each based on class and add the values
      $(".voted").each(function() {
          var value = $(this).text().replace(/[^\d\.\-\ ]/g, '');
          // add only if the value is number
          if(!isNaN(value) && value.length != 0) {
              sum += parseFloat(value);
          }
      });
      // add comma to sum number
      var commaNumber = commaToNumber(sum);
      $('#total').html(commaNumber);

    } else {
      // set data string
      var dataString = "trigger="+ colorName;
      // get the messages id.
      var messageId = $( '#'+colorName );
      // ajex to get number of voted for color
      $.ajax({
        type: 'GET',
        dateType: 'Html',
        data: dataString,
        url: 'getCount.php',
        success: function(response) {
          $(messageId).html(response);
        },
        error: function(data) {
          // Set the message text.
          if (data.responseText !== '') {
            $(messageId).text(data.responseText);
          } else {
            $(messageId).text('An error occured');
          }
        }
      });
    }

    // add comma to number
    function commaToNumber(val){
      while (/(\d+)(\d{3})/.test(val.toString())){
        val = val.toString().replace(/(\d+)(\d{3})/, '$1'+','+'$2');
      }
      return val;
    }

  });
});
