<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=1180"/>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=9, IE=10, IE=11, IE=edge,chrome=1"/>
<title>Travel Nation Canada      </title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<link href="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/build/css/bootstrap-datetimepicker.css" rel="stylesheet">
<link rel="stylesheet" href="http://travelnationcanada.com/css/tnc_fonts.css">
<link rel="stylesheet" href="assets/css/hot_landing.css" type="text/css" />
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
<script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
<script src="assets/js/bootstrap.js"/></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.js"></script>
<script src="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/src/js/bootstrap-datetimepicker.js"></script>
<script>
<!--Bof Function for Month Picker-->
function monthPick(activeTabId, maxdateselect) {
	$('#m'+activeTabId).datetimepicker({
		viewMode: 'months',
		format: 'MMMM YYYY',
		minDate: "moment",
		maxDate: maxdateselect
	});
}
<!--Bof Function for Form submit-->
function formSubmiting(form, formMessages) {
	form.submit(function(e) {	// Set up an event listener for the contact form.
		var month 	  = formMessages['selector'].replace('#', '#m');
		var monthName = $(month).val();
		var fMId 	  = $('[data-link='+formMessages['selector']+']').children('#dM');
		$(fMId).html(monthName);
		// Replace Month for overlayer progress bar
		var overMonth = $(formMessages['selector']).children('div#pleaseWaitDialog').find('strong#monthDay');
		$(overMonth).html(monthName);
		// // Replace Month for overlayer progress bar
		// var fromSlt = $('.fromSelect select'+formMessages['selector']+' option:selected').text();
		// var overGateway = $(formMessages['selector']).children('div#pleaseWaitDialog').find('#gatewayID');
		// $(overGateway).html($.trim(fromSlt));
		// // Replace Month for overlayer progress bar
		// var toSlt = $('.toSelect select'+formMessages['selector']+' option:selected').text();
		// var overDestination = $(formMessages['selector']).children('div#pleaseWaitDialog').find('#destinationID');
		// $(overDestination).html($.trim(toSlt));

		e.preventDefault();	// Stop the browser from submitting the form.
		$.ajax({ // Submit the form using AJAX.
				type: 'POST',
				dataType: 'html', // request type html/json/xml
				url: form.attr('action'),
				data: form.serialize(),
				beforeSend: function() {
					$(formMessages['selector'] +' #pleaseWaitDialog').show();
					//$(formMessages).text('Sending....'); // change submit button text
				},
				success: function(response) {
					$(formMessages).html(response); // Set the message text.
					// Clear the form.
					form.trigger('reset'); // reset form
					getPopOver(form);
				},
				error: function(data) {
					console.log(data);
				},
				complete: function() {
					$(formMessages['selector'] +' #pleaseWaitDialog').hide();
			 	}
		});
	});
}
<!--Bof Function for Toggle Panel-->
function ToggleAction() {
	//Slide Toggleing of option panel
	$('#pageTabContent').on( 'click', '#showit', function() {
		var formIds = $(this).parents('form').attr('id');
		$('#'+formIds+' #filterPanel').slideToggle('fast');
		$('li').removeClass('actSec');
		$('#'+formIds+' .day').popover('hide');
		if ( $(this).parents('li').hasClass('filterOff') ){
			$(this).parents('li').removeClass('filterOff');
			$('#'+formIds).children('div#switchPanel').find('span#showit').html('<i class="glyphicon glyphicon-menu-hamburger rotateAct"></i>&nbsp;&nbsp;Close Filters');
		} else {
			$(this).parents('li').addClass('filterOff');
			$('#'+formIds).children('div#switchPanel').find('span#showit').html('<i class="glyphicon glyphicon-menu-hamburger"></i>&nbsp;&nbsp;Show Filters');
		}
	});
}
function updateAction() {
	//Update
	$('#pageTabContent').on( 'click', '.update-btn', function() {
		var formIds = $(this).parents('form').attr('id');
		$('#'+formIds+' #filterPanel').slideUp('fast');

		$(this).parents('li').addClass('filterOff');
		$('#'+formIds).children('div#switchPanel').find('span#showit').html('<i class="glyphicon glyphicon-menu-hamburger"></i>&nbsp;&nbsp;Show Filters');
	});
}
function switchAction(Ids, txtSpan) {
	if($('#'+Ids+' #calender_list').hasClass('closePanel')){
		$('#'+Ids+' #calender_list').removeClass('closePanel');
		$('#'+Ids+' #calender_list').addClass('openPanel');
		$('#'+Ids+' '+txtSpan).parents('div.material-switch').find('span').html('&nbsp;&nbsp;Calendar View');
		$('#'+Ids+' #calendar').removeClass('openPanel');
		$('#'+Ids+' #calendar').addClass('closePanel');
	} else {
		$('#'+Ids+' #calender_list').removeClass('openPanel');
		$('#'+Ids+' #calender_list').addClass('closePanel');
		$('#'+Ids+' '+txtSpan).parents('div.material-switch').find('span').html('&nbsp;&nbsp;List View');
		$('#'+Ids+' #calendar').removeClass('closePanel');
		$('#'+Ids+' #calendar').addClass('openPanel');
	}
}
function getFormId( containerValue ) {
	return $(containerValue).parents('form').attr('id');
}
function getPopOver(form) {
	// Popover information by clicking and change calendar color to red
	$(form['selector']+' .day').each(function () {
		var $elem = $(this);
		$elem.popover({
			html: true,
			trigger: 'manual',
			container: $elem,
			content: function() {
				var largeContent = $(this).data('id');
				return $('#'+largeContent).html();
			}
		}).on('click', function (e) {
			$(form['selector']+' .day').not(this).popover('hide');
			$('li').removeClass('actSec');
			if($elem.hasClass('otherMonth') || $elem.hasClass('passedDay')){
				e.preventDefault();
			}else{
				if($elem.children('div').hasClass('popover')) {
					$elem.removeClass('actSec');
				} else {
					$elem.addClass('actSec');
				}
				$elem.popover('toggle');
			}
		}).on('shown.bs.popover', function () {
			$(this).parent().find('div.popover .closebtn').click(function (e) {
				$elem.removeClass('actSec');
			});
		});
	});
}
$(document).ready(function() {
	 // Add a Tab
	 var pageNum = 1;
	 var activeTabId = 'page1';
	 var form = $('#form1'); // Get the form.
	 var formMessages = $('#page1'); // Get the messages div.
	 var maxdateselect = new Date(new Date().setFullYear(new Date().getFullYear() + 1));

	 $('#btnAddPage').click(function() {
		// If it has 'grayOut' class, than not adding any tab adn tab_content
		if( !$('#btnAddPage').hasClass("grayOut") ) {
			$('#btnAddPage').addClass("grayOut");
			var tabNum = $('#pageTab > li').length;
			pageNum++;
			if (tabNum < 7) {
				$('#pageTab li').removeClass('active');
				// Add tab li
				var tabHtml = $('<li class="active"><div data-link="#page' + pageNum + '" class="rightborder"><span id="fN">Toronto</span><span id="dN">to<br/>All Countries</span><span id="dM">New Month</span></div><button type="button" class="newbtn btn-primary closeTab2">X</button></li>');
				$('#pageTab').append(tabHtml);

				// Copy tab content from HTML
				newElem = $('#page0').clone().appendTo('#pageTabContent').attr('id', 'page' + pageNum).fadeIn('fast');
				newElem.find('.form').attr('id', 'form' + pageNum);
				newElem.find('.hidden').attr('id', 'hidden'+ pageNum ).attr('name', 'page_num').val(pageNum);
				newElem.find('#to_name').attr('data-to', 'page'+ pageNum);
				newElem.find('#fo_name').attr('data-fo', 'page'+ pageNum);
				newElem.find('.slt').attr('id', 'page' + pageNum);
				newElem.find('.calendarStars').attr('class', 'calendarStars' + pageNum);
				newElem.find('.starNum').attr('id', pageNum);
				newElem.find('.NoIcon').attr('id', 'mpage' + pageNum);

				// var btnHeml = '<button class="closeTab" type="button" title="Remove this page">CLOSE <strong>X</strong></button>';
				// $('#page' + pageNum).children('.emptyArea').replaceWith(btnHeml);

				// Just show last add one
				$(".tab_content").hide(); //Hide all content
				$('#page' + pageNum).fadeIn('fast');
				// If there are 6 tabs, 'add tag' will be gray out
				setTimeout(function(){
					if ( tabNum != 6 ){
						$('#btnAddPage').removeClass("grayOut");
					}
				}, 250);
				activeTabId  = $('#pageTab').find('.active').children('div').attr('data-link').replace('#', '');
				form 		 = $('#form'+ pageNum);
				formMessages = $('#page'+ pageNum);
				monthPick(activeTabId, maxdateselect);
				formSubmiting(form, formMessages);
			}
		}
	});
	// Click Tab to show its content
	$('#pageTab').on('click', 'li div', function(e) {
		e.preventDefault();
		var activeTab = $(this).attr('data-link');
		$('.tab_content').hide(); //Hide all content
		$(this).tab('show');
		$(activeTab).fadeIn('fast'); //Fade in the active content
		activeTabId  = $(this).attr('data-link').replace('#', '');
		formId 		 = $(this).attr('data-link').replace('#page', 'form');
		form 		 = $('#'+ formId);
		formMessages = $('#'+ activeTabId);
		monthPick(activeTabId, maxdateselect);
		formSubmiting(form, formMessages);
	});
	// Remove a Tab
	$('.tab-bar').on('click', 'button.closeTab2', function() {
		// Remove tab
		var pId   = $(this).parents('li').find('div').attr('data-link');
		$(this).parents('li').remove();
		$('#pageTabContent').children('div'+pId).remove();

		var tabCountOne = $('#pageTab > li').length;
		$('#pageTab li:nth-child('+ tabCountOne +')').addClass('active');

		var tabCount = $('#pageTab > li').length - 1;
		$('#pageTabContent div:nth-child('+ tabCount +')').fadeIn('fast');
		$('#pageTabContent div:nth-child('+ tabCount +') #pleaseWaitDialog').hide();
		if ( tabCount < 6 ){
			$('#btnAddPage').removeClass("grayOut");
		}
	});
	// Remove a Tab
	// $('#pageTabContent').on('click', 'button.closeTab', function() {
	// 	// Remove tab
	// 	var pId   = $(this).parents('div').attr('id');
	// 	var tabId = $("[data-link=#"+pId+"]").parents('li');
	// 	$(tabId).remove();
	// 	$('#pageTab li').removeClass('active');
	// 	// Remove itself
	// 	$(this).parents('div').remove('div.tab_content');
	//
	// 	var tabCountOne = $('#pageTab > li').length;
	// 	$('#pageTab li:nth-child('+ tabCountOne +')').addClass('active');
	//
	// 	var tabCount = $('#pageTab > li').length - 1;
	// 	$('#pageTabContent div:nth-child('+ tabCount +')').fadeIn('fast');
	//
	// 	if ( tabCount < 6 ){
	// 		$('#btnAddPage').removeClass("grayOut");
	// 	}
	// });
    // Check all or not for Stars
    $('#pageTabContent').on('change', '.starsAll', function() {
		var starNum = $(this).parents('ul').attr('id');
		$(".calendarStars"+starNum).prop('checked', $(this).prop("checked"));
	});
	// Replace Departure location on tab
    $('#pageTabContent').on('change', '.fromSelect select', function() {
		var pfIds 	= $(this).attr('id');
		var fNId 	= $('[data-link=#'+pfIds+']').children('#fN');
		var fromSlt = $('.fromSelect select#'+pfIds+' option:selected').text();
		$(fNId).html(fromSlt);
		$("[data-fo="+pfIds+"]").val(fromSlt);
		// Replace Month for overlayer progress bar
		var overGateway = $('#'+pfIds).children('div#pleaseWaitDialog').find('#gatewayID');
		$(overGateway).html($.trim(fromSlt));
    });
	// Replace Destination on tab
    $('#pageTabContent').on('change', '.toSelect select', function() {
		var ptIds = $(this).attr('id');
		var dNId  = $('[data-link=#'+ptIds+']').children('#dN');
		var toSlt = $('.toSelect select#'+ptIds+' option:selected').text();
		$(dNId).html( 'to<br/>'+ toSlt);
		$("[data-to="+ptIds+"]").val(toSlt);
		// Replace Month for overlayer progress bar
		var overDestination = $('#'+ptIds).children('div#pleaseWaitDialog').find('#destinationID');
		$(overDestination).html($.trim(toSlt));
    });
	// Replace Month on tab
	$('#pageTabContent').on('blur', '.NoIcon', function() {
	    var pMIds 	  = $(this).attr('id').replace('m', '');
	    var fMId 	  = $('[data-link=#'+pMIds+']').children('#dM');
	    var dMonthSlt = $('#m'+pMIds).val();
	    $(fMId).html(dMonthSlt);
		// Replace Month for overlayer progress bar
		var overMonth = $('#'+pMIds).children('div#pleaseWaitDialog').find('strong#monthDay');
		$(overMonth).html(dMonthSlt);
    });
	// Grab departure month
	monthPick(activeTabId, maxdateselect);
	formSubmiting(form, formMessages);

	// Uncheck by click on remove mark of presented options
	$('#pageTabContent').on('click', '.glyphicon-remove', function() {
		var Ids = getFormId(this);
		var vuName = $(this).parents('li').attr('value');
		var idName = $(this).parents('li').parents('ul').attr('data-id');
		$(this).parents('li').remove();
		$('[data-id='+idName+']').find('[name='+vuName+']').prop('checked', false);
		// Remove ColumnCell from List view table
		var th = $('#'+Ids+' th[data-id="'+vuName+'"]');
	    var colIndex = $(th).index();
	    $(th).parents("table").find("tbody tr").each(function() {
	       var columnCell = $(this).children("td:eq(" + colIndex + ")");
		   $(th).remove();
	       $(columnCell).remove();
	    });
	});

	//Slide Toggleing of option panel
	ToggleAction();
	updateAction();

	//Toggle switch for LIST VIEW
	$('#pageTabContent').on( 'click', 'div.material-switch span', function() {
		var inputNm = $('#'+getFormId(this)+' div.material-switch input');
			inputNm.prop("checked", !inputNm.prop("checked"));
		switchAction(getFormId(this), 'div.material-switch span');
	});
	$('#pageTabContent').on( 'click', 'input[type=checkbox][id^=switchView]', function() {
		switchAction(getFormId(this), 'input[type=checkbox][id^=switchView]');
	});
});
</script>
</head>
<body>

  <div id="header-block" class="redbar">
    <div class="red-box-container">
      <div class="red-box-container2">
      <img src="assets/images/logo.jpg" class="logo">
      <div class="header-block-left"><span class="header-label">Vacations</span></div>

      <div class="header-block-right-2">
        <span class="contact-hours"><p>Monday to Friday: 9am to 10pm</p><p>Saturday: 9am to 8pm</p><p>Sunday: 10am to 6pm</p></span></div>
      <div class="header-block-right">
        <span class="contact-note">Call us, we’re here to help.</span><br><span class="contact-number">1-844-562-8466</span></div>
      </div>
    </div>
  </div>
  <div style="clear:both"></div>
<!-- Content container -->
<div class="container-fluid">
    <article class="landing-container">
        <div class="price-calendar">
            <div class="tab-bar">
                <ul id="pageTab" class="tabs">
                    <li id="btnAddPage"><p>Add New<br/>Calendar</p><i class="glyphicon glyphicon-plus"></i></li>
                    <li class="active"><div data-link="#page1" class="rightborder"><span id="fN">Toronto<br/></span><span id="dN">to<br/>All South</span><span id="dM"><?php echo date('F Y'); ?></span></div></li>
                </ul>
            </div>
            <div id="pageTabContent" class="price-calendar-search" style="clear:both;">
				<div class="tab_content" id="page1">
				<!-- <div class="emptyArea"></div> -->
				 <form action="hot_price.php" method="post" class="form" id="form1">
					<h2>Search the Lowest Prices in any Month</h2>
					<div class="destElements form-group row form-inline">
							<div class="input-group fromSelect">
								<span class="input-group-addon"> From </span>
								<select class="form-control gateways slt" required="required" name="gateway_dep" id="page1">
								<option value="YXX">Abbotsford</option>
								<option value="YBG">Bagotville</option>
								<option value="BOS">Boston</option>
								<option value="YBR">Brandon</option>
								<option value="BUF">Buffalo</option>
								<option value="BTV">Burlington</option>
								<option value="YYC">Calgary</option>
								<option value="YYG">Charlottetown</option>
								<option value="MDW">Chicago</option>
								<option value="YQQ">Comox</option>
								<option value="YDF">Deer Lake</option>
								<option value="YEG">Edmonton</option>
								<option value="YMM">Fort McMurray</option>
								<option value="YXJ">Fort Saint John</option>
								<option value="YFC">Fredericton</option>
								<option value="YQX">Gander</option>
								<option value="YQU">Grande Prairie</option>
								<option value="YHZ">Halifax</option>
								<option value="YHM">Hamilton</option>
								<option value="YKA">Kamloops</option>
								<option value="YLW">Kelowna</option>
								<option value="YGK">Kingston</option>
								<option value="YKF">Kitchener</option>
								<option value="YXU">London</option>
								<option value="MLB">Melbourne</option>
								<option value="YQM">Moncton</option>
								<option value="YTM">Mont-Tremblant</option>
								<option value="YUL">Montreal</option>
								<option value="MYR">Myrtle Beach</option>
								<option value="YCD">Nanaimo</option>
								<option value="EWR">Newark</option>
								<option value="YYB">North Bay</option>
								<option value="YOW">Ottawa</option>
								<option value="YYF">Penticton</option>
								<option value="PIT">Pittsburgh</option>
								<option value="YXS">Prince George</option>
								<option value="YQB">Quebec City</option>
								<option value="YQR">Regina</option>
								<option value="YUY">Rouyn-Noranda</option>
								<option value="YSJ">Saint John</option>
								<option value="YXE">Saskatoon</option>
								<option value="YAM">Sault Ste Marie</option>
								<option value="YZV">Sept Iles</option>
								<option value="YYT">St. John's</option>
								<option value="YSB">Sudbury</option>
								<option value="YQY">Sydney</option>
								<option value="YXT">Terrace</option>
								<option value="YQT">Thunder Bay</option>
								<option value="YTS">Timmins</option>
								<option selected="selected" value="YYZ,YTZ">Toronto</option>
								<option value="YVO">Val d'Or</option>
								<option value="YVR">Vancouver</option>
								<option value="YYJ">Victoria</option>
								<option value="IAD">Washington</option>
								<option value="YXY">Whitehorse</option>
								<option value="YQG">Windsor</option>
								<option value="YWG">Winnipeg</option>
								<option value="YZF">Yellowknife</option>
								</select>
							</div>
							<div class="input-group toSelect">
								<span class="input-group-addon">To</span>
								<select class="form-control destinations slt" name="dest_dep" id="page1">
								<option data-durations="["1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","22","29"]" value="2742791,84,2807730,2741968,2741642,2739917,3006,46,86,1376,78,33,60,2408,152,772385,761711,2154965,173,3281,2145842,19,2139425,2138511,100,143,1332887,1328096,1318341,228,974878,66,114,68,113,110,108,836519,117,125,235,103,96,2751021,80,4413,74,58,131,35,2521,93,45,104,61,875199,2974,569962,569714,97,2752241,1017,43,1434,2751301,120,151,2750660,20,2750048,2750009,65,2749976,147,2749698,63,13,37,568522,51,81,71,2750814,148,79,1578,31,62,36,1899141,1341882,34,568430,30,188,27,39,2488,69,5,7,44,1,1808,12,76,25,26,29,226,59,156,24,9,77,17,2,1341400,1843,4244,18,83,14,40,10,8,73,21,64,22,70,16,568546,567874,15,52,53,6,47,4,87,3,92,206,2791027,232,32,4100,192,207,234,2864646,2810039,2868424,3531,2790129,54,190,2789888,2789883,2789758,2813782,189,2781986,2865433,209,215,212,213,169,210,186,185,231,163,349950,214,211,162,2738,348147,348023,347115">All Countries</option>
								<option data-durations="["1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16"]" value="2742791,84,2807730,2741968,2741642,2739917,3006,96,2751021,80,4413,97,2752241,1017,43,1434,2751301,120,151,2750660,20,2750048,2750009,65,2749976,147,2749698,51,81,71,2750814,148,79,1578,31,206,2791027,232,32,4100,192,207,234,2864646,2810039,2868424,3531,2790129,54,190,2789888,2789883,2789758,2813782,189,2781986,2865433,209,215,212,213,169,210,186,185,231,163,349950,214,211,162,2738,348147,348023,347115">All Canada and United States</option>
								<option data-durations="["4","5","6","7","8","9","10","11","12","13","14","15","16","22","29"]" value="2408,152,772385,761711,2154965,173,3281,2145842,19,2139425,2138511,100,143,1332887,1328096,1318341,228,974878,66,114,68,113,110,108,836519,117,125,235,103,74,58,131,93,45,104,61,875199">All Europe</option>
								<option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15","16","17"]" value="46,86,1376,78,33,60,35,2521,2974,569962,569714,63,13,37,568522,62,36,1899141,1341882,34,568430,30,188,27,39,2488,69,5,7,44,1,1808,12,76,25,26,29,226,59,156,24,9,77,17,2,1341400,1843,4244,18,83,14,40,10,8,73,21,64,22,70,16,568546,567874,15,52,53,6,47,4,87,3,92" selected="selected">All South</option>
								<option data-durations="["3","4","6","7","10","14"]" value="27">Antigua and Barbuda(Antigua)</option>
								<option data-durations="["3","4","5","6","7","8","9","10","12","13","14","15"]" value="29">Aruba(Aruba)</option>
								<option data-durations="["3","4","5","6","7","10","11","14"]" value="188,26,25">Bahamas</option>
								<option data-durations="["3","4","7","10","11","14"]" value="26">    Freeport</option>
								<option data-durations="["3","4","7","14"]" value="188">    Great Exuma</option>
								<option data-durations="["3","4","5","6","7","10","11","14"]" value="25">    Nassau</option>
								<option data-durations="["3","4","5","6","7","8","10","14"]" value="30">Barbados(Bridgetown)</option>
								<option data-durations="["8","15"]" value="103">Belgium(Brussels)</option>
								<option data-durations="["5","7","10","14"]" value="60">Bermuda(Bermuda)</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="349950,348147,348023,347115,2738,231,215,214,213,212,211,210,209,186,185,169,163,162">Canada</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="347115">    Baie-Saint-Paul</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="348023">    Dartmouth</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="348147">    Dorval</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="2738">    Gatineau Hull</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="162">    Halifax</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="211">    Moncton</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="214">    Mont-Tremblant</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="349950">    Montebello</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="163">    Montreal</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="231">    North Bay</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="185">    Ottawa</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="186">    Quebec City</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="210">    Sault Ste Marie</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="169">    St. John s</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="213">    Sudbury</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="212">    Thunder Bay</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="215">    Timmins</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="209">    Windsor</option>
								<option data-durations="["7","10","14"]" value="33">Cayman Islands(Grand Cayman)</option>
								<option data-durations="["3","4","5","6","7","9","10","11","12","14","16"]" value="569962,569714,2974,568522,568430,568546,567874,70,64,22,21,16">Costa Rica</option>
								<option data-durations="["7","14"]" value="567874">    Bagaces</option>
								<option data-durations="["6","7","14"]" value="568430">    Culebra</option>
								<option data-durations="["5","6","7","9","10","11","12","14","16"]" value="568522">    El Coco</option>
								<option data-durations="["5","6","7","9","10","11","12","14","16"]" value="568546">    El Jobo</option>
								<option data-durations="["3","4","5","6","7","14"]" value="16">    Jaco beach</option>
								<option data-durations="["5","6","7","9","10","11","12","14","16"]" value="70">    Liberia</option>
								<option data-durations="["3","4","5","6","7","14"]" value="22">    Playa Tambor</option>
								<option data-durations="["6","7","14"]" value="569714">    Potrero</option>
								<option data-durations="["3","4","5","6","7","9","10","11","14","16"]" value="64">    Puntarenas</option>
								<option data-durations="["5","7","10","14"]" value="569962">    Samara</option>
								<option data-durations="["3","4","5","6","7","14"]" value="21">    San Jose</option>
								<option data-durations="["6","7","14"]" value="2974">    Tamarindo</option>
								<option data-durations="["8","15"]" value="235">Croatia(Zagreb)</option>
								<option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15","16","17"]" value="37,92,87,53,52,47,15,6,4,3">Cuba</option>
								<option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15","16"]" value="92">    Cayo Coco</option>
								<option data-durations="["5","7","9","12","14","16"]" value="3">    Cayo Largo</option>
								<option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15"]" value="87">    Cayo Santa Maria</option>
								<option data-durations="["3","4","5","6","7","8","9","11","12","13","14","15"]" value="4">    Cienfuegos</option>
								<option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15"]" value="47">    Havana</option>
								<option data-durations="["3","4","5","6","7","8","10","11","12","14","15","16","17"]" value="6">    Holguin</option>
								<option data-durations="["7","14"]" value="37">    Manzanillo de Cuba</option>
								<option data-durations="["5","6","7","8","12","13","14","15"]" value="53">    Santa Lucia (Camaguey)</option>
								<option data-durations="["4","7","10","11","14","16","17"]" value="52">    Santiago de Cuba</option>
								<option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15"]" value="15">    Varadero</option>
								<option data-durations="["6","7","14"]" value="76">Curacao(Curacao)</option>
								<option data-durations="["8","15"]" value="125">Czech Republic(Prague)</option>
								<option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15"]" value="13,12,73,40,14,10,8">Dominican Republic</option>
								<option data-durations="["5","6","7","9","14"]" value="13">    Cabarete</option>
								<option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15"]" value="73">    La Romana</option>
								<option data-durations="["3","4","5","6","7","8","9","10","11","12","14"]" value="8">    Puerto Plata</option>
								<option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15"]" value="10">    Punta Cana</option>
								<option data-durations="["3","4","5","6","7","10","14"]" value="40">    Samana</option>
								<option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15"]" value="14">    Santo Domingo</option>
								<option data-durations="["3","4","5","6","7","8","9","10","11","12","14"]" value="12">    Sosua</option>
								<option data-durations="["4","6","7","8","9","10","11","12","13","14","15","16"]" value="836519,117,114,113,110,108,68,875199,61">France</option>
								<option data-durations="["8","15"]" value="117">    Bordeaux</option>
								<option data-durations="["8","15"]" value="836519">    Bruges</option>
								<option data-durations="["10","11","13"]" value="108">    Lyon</option>
								<option data-durations="["8","10","11","13","15"]" value="110">    Marseille</option>
								<option data-durations="["4","6","7","8","9","10","14"]" value="875199">    Montrouge</option>
								<option data-durations="["8","15"]" value="113">    Nantes</option>
								<option data-durations="["8","15"]" value="68">    Nice</option>
								<option data-durations="["4","6","7","8","9","10","11","12","13","14","15","16"]" value="61">    Paris</option>
								<option data-durations="["10"]" value="114">    Toulouse</option>
								<option data-durations="["8","11","12","15"]" value="974878,66">Greece</option>
								<option data-durations="["8","11","12","15"]" value="66">    Athens</option>
								<option data-durations="["8","15"]" value="974878">    Kinetta</option>
								<option data-durations="["4","7","10","14"]" value="34">Grenada(Grenada)</option>
								<option data-durations="["7","14"]" value="63,1808,83">Honduras</option>
								<option data-durations="["7","14"]" value="63">    La Ceiba</option>
								<option data-durations="["7","14"]" value="83">    Roatan</option>
								<option data-durations="["7","14"]" value="1808">    Tela</option>
								<option data-durations="["8","15"]" value="228">Hungary(Budapest)</option>
								<option data-durations="["6","7","8","9","10","15"]" value="104">Ireland(Dublin)</option>
								<option data-durations="["4","6","7","8","9","10","11","12","13","14","15","16"]" value="1332887,1328096,1318341,143,45">Italy</option>
								<option data-durations="["8","15"]" value="1318341">    Amalfi</option>
								<option data-durations="["8","15"]" value="1328096">    Maiori</option>
								<option data-durations="["8"]" value="1332887">    Ravello</option>
								<option data-durations="["4","6","7","8","9","10","11","12","13","14","15","16"]" value="45">    Rome</option>
								<option data-durations="["8","15"]" value="143">    Sorrento</option>
								<option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15","16"]" value="78,1341882,1341400,4244,1843,18">Jamaica</option>
								<option data-durations="["5","7","10","14"]" value="78">    Kingston</option>
								<option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15","16"]" value="18">    Montego Bay</option>
								<option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15","16"]" value="4244">    Negril</option>
								<option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15","16"]" value="1843">    Ocho Rios</option>
								<option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15","16"]" value="1341400">    Runaway Bay</option>
								<option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","16"]" value="1341882">    Whitehouse</option>
								<option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15","16"]" value="1376,86,2488,69,44,7,5,1,156,77,24,17,9,2">Mexico</option>
								<option data-durations="["7","14"]" value="1">    Acapulco</option>
								<option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15","16"]" value="2">    Cancun</option>
								<option data-durations="["7","10","14"]" value="17">    Cozumel</option>
								<option data-durations="["5","7","10","14"]" value="44">    Huatulco</option>
								<option data-durations="["5","6","7","10","14"]" value="7">    Ixtapa</option>
								<option data-durations="["5","7","10","14"]" value="1376">    La Paz</option>
								<option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15"]" value="77">    Los Cabos</option>
								<option data-durations="["7","14"]" value="5">    Manzanillo</option>
								<option data-durations="["7","14"]" value="69">    Mazatlan</option>
								<option data-durations="["7","14"]" value="86">    Merida</option>
								<option data-durations="["5","7","10","14"]" value="2488">    Puerto Escondido</option>
								<option data-durations="["3","4","5","6","7","9","10","11","14"]" value="9">    Puerto Vallarta</option>
								<option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15","16"]" value="24">    Riviera Maya</option>
								<option data-durations="["3","4","5","6","7","9","10","11","14"]" value="156">    Riviera Nayarit</option>
								<option data-durations="["8","10","14","15"]" value="100">Netherlands(Amsterdam)</option>
								<option data-durations="["3","4","7","10","11","14"]" value="1899141,226,59">Panama</option>
								<option data-durations="["4","7","10","14"]" value="1899141">    Gamboa</option>
								<option data-durations="["3","4","7","10","11","14"]" value="59">    Panama City</option>
								<option data-durations="["4","7","10","11","14"]" value="226">    Playa Blanca</option>
								<option data-durations="["7","8","9","10","11","12","13","14","15","16","22","29"]" value="2154965,2145842,2139425,2138511,3281,173,19,93">Portugal</option>
								<option data-durations="["8","9","10","12","14","15","22","29"]" value="2138511">    Albufeira</option>
								<option data-durations="["7","8","9","10","12","14","15","16"]" value="2139425">    Armacao de Pera</option>
								<option data-durations="["7","8","9","10","12","14","15","16","22","29"]" value="19">    Faro</option>
								<option data-durations="["7","8","9","10","11","12","13","14","15","16"]" value="2145842">    Lagos</option>
								<option data-durations="["7","8","9","10","11","12","13","14","15","16"]" value="93">    Lisbon</option>
								<option data-durations="["7","8","9","10","12","14","15","16","22","29"]" value="3281">    Portimao</option>
								<option data-durations="["8","10","11","15"]" value="173">    Porto</option>
								<option data-durations="["7","8","9","10","12","14","15","16"]" value="2154965">    Vilamoura</option>
								<option data-durations="["7","8","14","15"]" value="46">Puerto Rico(San Juan)</option>
								<option data-durations="["7","14"]" value="2521,35">Saint Kitts Nevis</option>
								<option data-durations="["7","14"]" value="2521">    Nevis</option>
								<option data-durations="["7","14"]" value="35">    St Kitts</option>
								<option data-durations="["3","4","5","6","7","8","10","14"]" value="36">Saint Lucia(St Lucia)</option>
								<option data-durations="["6","7","8","9","10","12","13","14","15","16","22","29"]" value="772385,761711,152,131,58">Spain</option>
								<option data-durations="["6","7","8","9","10","12","13","14","15","16"]" value="131">    Barcelona</option>
								<option data-durations="["8","15","22","29"]" value="761711">    Benalmadena</option>
								<option data-durations="["8","15","22","29"]" value="772385">    Fuengirola</option>
								<option data-durations="["8"]" value="58">    Madrid</option>
								<option data-durations="["8","15","22","29"]" value="152">    Torremolinos</option>
								<option data-durations="["3","4","5","7","10","11","14"]" value="39">St Maarten(St Maarten)</option>
								<option data-durations="["3","4","5","6","7","10","14"]" value="62">Turks And Caicos(Providenciales)</option>
								<option data-durations="["4","5","6","7","8","9","10","11","13","14","15","16"]" value="2408,74">United Kingdom</option>
								<option data-durations="["4","5","6","7","8","9","10","11","13","14","15","16"]" value="2408">    Gatwick</option>
								<option data-durations="["4","5","6","7","8","9","10","11","13","14","15","16"]" value="74">    London</option>
								<option data-durations="["1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16"]" value="2807730,2742791,2741968,2741642,2739917,3006,84,2751021,4413,96,80,2752241,2751301,2750660,2750048,2750009,2749976,2749698,1434,1017,151,147,120,97,65,43,20,2750814,1578,148,81,79,71,51,31,2868424,2865433,2864646,2813782,2810039,2791027,2790129,2789888,2789883,2789758,2781986,4100,3531,234,232,207,206,192,190,189,54,32">United States</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="2865433">    Alexandria</option>
								<option data-durations="["3","4","5","7"]" value="3006">    Anaheim</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="2781986">    Bethesda</option>
								<option data-durations="["3","4","6","7","14"]" value="4413">    Boca Raton</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="189">    Boston</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="2813782">    Brooklyn</option>
								<option data-durations="["3","4","5","7"]" value="2739917">    Buena Park</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="2789758">    Cambridge</option>
								<option data-durations="["4"]" value="2749698">    Cape Coral</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="2789883">    Chatham</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="2789888">    Chelsea</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="190">    Chicago</option>
								<option data-durations="["3","4","5","6","7","8","9","12","13","14"]" value="147">    Clearwater</option>
								<option data-durations="["7"]" value="54">    Cocoa beach</option>
								<option data-durations="["4","5","6","7","8","9","12","13","14","15"]" value="2749976">    Davenport</option>
								<option data-durations="["3","4","5","6","7","14"]" value="65">    Daytona Beach</option>
								<option data-durations="["6","7","8","13","14","15"]" value="2750009">    Delray Beach</option>
								<option data-durations="["6","7","14"]" value="2750048">    Duck Key</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="2790129">    Edgartown</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="3531">    Falmouth</option>
								<option data-durations="["3","4","5","6","7","8","13","14"]" value="31">    Fort Lauderdale</option>
								<option data-durations="["4"]" value="20">    Fort Myers</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="2868424">    Herndon</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="2810039">    Hoboken</option>
								<option data-durations="["3","4","5","6","7","8","14"]" value="1578">    Hollywood (Florida)</option>
								<option data-durations="["3","4","5","6","7","10","14"]" value="79">    Honolulu</option>
								<option data-durations="["3","4","5","7"]" value="2741642">    Huntington Beach</option>
								<option data-durations="["3","4","5","6","7","9","11","14"]" value="2750660">    Indian Rocks Beach</option>
								<option data-durations="["3"]" value="2864646">    Jeffersonville</option>
								<option data-durations="["6","7","8","13","14","15"]" value="151">    Key Largo</option>
								<option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15","16"]" value="148">    Kissimmee</option>
								<option data-durations="["8"]" value="80">    Kona</option>
								<option data-durations="["3","4","5","7"]" value="2741968">    Laguna Beach</option>
								<option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15","16"]" value="2750814">    Lake Buena Vista</option>
								<option data-durations="["3","4","5","6","7","8","10","14"]" value="71">    Las Vegas</option>
								<option data-durations="["3","4","5","6","7"]" value="2807730">    Laughlin</option>
								<option data-durations="["3","4","5","7"]" value="84">    Los Angeles</option>
								<option data-durations="["3","4","6","7","14"]" value="2751021">    Manalapan</option>
								<option data-durations="["5","7","10","14"]" value="81">    Maui</option>
								<option data-durations="["7"]" value="234">    Melbourne</option>
								<option data-durations="["3","4","5","6","7","8","13","14"]" value="51">    Miami</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="207">    Myrtle Beach</option>
								<option data-durations="["7"]" value="120">    Naples</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="192">    New York</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="4100">    Newark</option>
								<option data-durations="["3","4","5","7"]" value="2742791">    Newport Beach</option>
								<option data-durations="["3","4","5","6","7","14","15"]" value="2751301">    North Redington Beach</option>
								<option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15","16"]" value="32">    Orlando</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="232">    Pittsburgh</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="2791027">    Plymouth</option>
								<option data-durations="["6","7","8","14"]" value="1434">    Pompano Beach</option>
								<option data-durations="["3","4","6","7","14"]" value="96">    Sarasota</option>
								<option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15","16"]" value="43">    St Petersburg</option>
								<option data-durations="["4","5","6","7","8","9","12","13","14","15"]" value="1017">    Tampa</option>
								<option data-durations="["7","14"]" value="2752241">    Treasure Island</option>
								<option data-durations="["1","2","3","4","5","6","7","8"]" value="206">    Washington</option>
								<option data-durations="["3","4","6","7","8","13","14","15"]" value="97">    West Palm Beach</option>
								</select>
							</div>

							<div class="input-group montheSelect">
								<span class="input-group-addon">Month</span>
								<input class="form-control NoIcon" type="text" name="month" id="mpage1">
							</div>

							<div class="input-group li-4">
								<span class="input-group-addon">Duration</span>
								<select class="form-control durations slt" name="duration">
								<option value="3days,4days">3 or 4 days</option>
								<option value="7days,8days" selected="selected">7 or 8 days</option>
								<option value="5days,6days,7days,8days,9days,10days">5 to 10 days</option>
								<option value="11days,12days,13days,14days,15days,16days">11 to 16 days</option>
								</select>
							</div>
					</div>
					<div class="optionElements">
							<div class="checkboxes inclusiveList">
									<dl class="form-group row form-inline">
										<dt class="dt-4">
											<label>
											<input type="checkbox" class="calendarOptionsAll" name="allInclusive" value="1"><span>All Inclusive</span>
											</label>
										</dt>
										<dd class="dd-10">
											<ul class="unstyled">
												<li class="li-3">
													<label>
													<input type="checkbox" class="calendarOptions" name="adults" value="1"><span>Adults Only</span>
													</label>
												</li>
												<li class="li-3">
													<label>
													<input type="checkbox" class="calendarOptions" name="beach" value="1"><span>Beach</span>
													</label>
												</li>
												<li class="li-3">
													<label>
													<input type="checkbox" class="calendarOptions" name="casino" value="1"><span>Casino</span>
													</label>
												</li>
												<li class="li-3">
													<label>
													<input type="checkbox" class="calendarOptions" name="family" value="1"><span>Family</span>
													</label>
												</li>
												<li class="li-3">
													<label>
													<input type="checkbox" class="calendarOptions" name="golf" value="1"><span>Golf</span>
													</label>
												</li>
											</ul>
											<ul class="unstyled form-inline">
												<li class="li-3">
													<label>
													<input type="checkbox" class="calendarOptions" name="kitchenette" value="1"><span>Kichenette</span>
													</label>
												</li>
												<li class="li-3">
													<label>
													<input type="checkbox" class="calendarOptions" name="miniclub" value="1"><span>Mini Club</span>
													</label>
												</li>
												<li class="li-3">
													<label>
													<input type="checkbox" class="calendarOptions" name="oceanview" value="1"><span>OceanView</span>
													</label>
												</li>
												<li class="li-3">
													<label>
													<input type="checkbox" class="calendarOptions" name="spa" value="1"><span>Spa</span>
													</label>
												</li>
												<li class="li-3">
													<label>
													<input type="checkbox" class="calendarOptions" name="wedding" value="1"><span>Wedding</span>
													</label>
												</li>
											</ul>
										</dd>
									</dl>
							</div>
							<div class="checkboxes star-rating">
								<ul class="unstyled form-group row form-inline starNum" id="">
									<li class="li-2">
										<label>
										<input type="checkbox" class="calendarStars" name="2.0"><span>2 <i class="glyphicon glyphicon-star"></i></span>
										</label>
									</li>
									<li class="li-2">
										<label>
										<input type="checkbox" class="calendarStars" name="2.5"><span>2.5 <i class="glyphicon glyphicon-star"></i></span>
										</label>
									</li>
									<li class="li-2">
										<label>
										<input type="checkbox" class="calendarStars" name="3.0"><span>3 <i class="glyphicon glyphicon-star"></i></span>
										</label>
									</li>
									<li class="li-2">
										<label>
										<input type="checkbox" class="calendarStars" name="3.5" checked ><span>3.5 <i class="glyphicon glyphicon-star"></i></span>
										</label>
									</li>
									<li class="li-2">
										<label>
										<input type="checkbox" class="calendarStars" name="4.0"  checked ><span>4 <i class="glyphicon glyphicon-star"></i></span>
										</label>
									</li>
									<li class="li-2">
										<label>
										<input type="checkbox" class="calendarStars" name="4.5" checked ><span>4.5 <i class="glyphicon glyphicon-star"></i></span>
										</label>
									</li>
									<li class="li-2">
										<label>
										<input type="checkbox" class="calendarStars" name="5.0" checked ><span>5 <i class="glyphicon glyphicon-star"></i></span>
										</label>
									</li>
									<li class="li-4">
										<label>
										<input type="checkbox" class="starsAll" value="all" ><span>Check/Uncheck All</span>
										</label>
									</li>
								</ul>
							</div>
					</div>
					<input type="hidden" id="hidden" name="page_num" class="hidden" value="1">
					<input type="hidden" id="to_name" data-to="page1" name="destination" value="All South">
					<input type="hidden" id="fo_name" data-fo="page1" name="gateway" value="Toronto">
					<input type="submit" name="submit" value="Search" class="own-btn search-btn">
				 </form>

				 <div id="pleaseWaitDialog" class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog" aria-hidden="true">
				 	<div class="modal-dialog modal-m">
				 		<div class="modal-content">
				 			<div class="modal-header"><h4><span id="gatewayID">Toronto</span><div></div><span id="destinationID">All South</span></h4></div>
				 			<div class="modal-body">
				 				<div class="progress progress-striped active" style="margin-bottom:0;"><div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="100" aria-valuemin="80" aria-valuemax="100" style="width: 100%"></div></div>
				 			</div>
				 			<div class="modal-footer"><h4>Finding Lowest Price for <strong id="monthDay"><?php echo date('F Y'); ?></strong></h4></div>
				 		</div>
				 	</div>
				 </div>

				</div>
            </div>
        </div>
    </article>
</div>
<!-- end Content container -->
<!-- html elements to copy -->
<div class="tab_content" id="page0">
    <!-- <div class="emptyArea"></div> -->
    <form action="hot_price.php" method="post" class="form" id="form1">
        <h2>Search the Lowest Prices in any Month</h2>
        <div class="destElements form-group row form-inline">
            <div class="input-group fromSelect">
                <span class="input-group-addon"> From </span>
                <select class="form-control gateways slt" required="required" name="gateway_dep" id="page0">
                <option value="YXX">Abbotsford</option>
                <option value="YBG">Bagotville</option>
                <option value="BOS">Boston</option>
                <option value="YBR">Brandon</option>
                <option value="BUF">Buffalo</option>
                <option value="BTV">Burlington</option>
                <option value="YYC">Calgary</option>
                <option value="YYG">Charlottetown</option>
                <option value="MDW">Chicago</option>
                <option value="YQQ">Comox</option>
                <option value="YDF">Deer Lake</option>
                <option value="YEG">Edmonton</option>
                <option value="YMM">Fort McMurray</option>
                <option value="YXJ">Fort Saint John</option>
                <option value="YFC">Fredericton</option>
                <option value="YQX">Gander</option>
                <option value="YQU">Grande Prairie</option>
                <option value="YHZ">Halifax</option>
                <option value="YHM">Hamilton</option>
                <option value="YKA">Kamloops</option>
                <option value="YLW">Kelowna</option>
                <option value="YGK">Kingston</option>
                <option value="YKF">Kitchener</option>
                <option value="YXU">London</option>
                <option value="MLB">Melbourne</option>
                <option value="YQM">Moncton</option>
                <option value="YTM">Mont-Tremblant</option>
                <option value="YUL">Montreal</option>
                <option value="MYR">Myrtle Beach</option>
                <option value="YCD">Nanaimo</option>
                <option value="EWR">Newark</option>
                <option value="YYB">North Bay</option>
                <option value="YOW">Ottawa</option>
                <option value="YYF">Penticton</option>
                <option value="PIT">Pittsburgh</option>
                <option value="YXS">Prince George</option>
                <option value="YQB">Quebec City</option>
                <option value="YQR">Regina</option>
                <option value="YUY">Rouyn-Noranda</option>
                <option value="YSJ">Saint John</option>
                <option value="YXE">Saskatoon</option>
                <option value="YAM">Sault Ste Marie</option>
                <option value="YZV">Sept Iles</option>
                <option value="YYT">St. John's</option>
                <option value="YSB">Sudbury</option>
                <option value="YQY">Sydney</option>
                <option value="YXT">Terrace</option>
                <option value="YQT">Thunder Bay</option>
                <option value="YTS">Timmins</option>
                <option selected="selected" value="YYZ,YTZ">Toronto</option>
                <option value="YVO">Val d'Or</option>
                <option value="YVR">Vancouver</option>
                <option value="YYJ">Victoria</option>
                <option value="IAD">Washington</option>
                <option value="YXY">Whitehorse</option>
                <option value="YQG">Windsor</option>
                <option value="YWG">Winnipeg</option>
                <option value="YZF">Yellowknife</option>
                </select>
            </div>
            <div class="input-group toSelect">
                <span class="input-group-addon">To</span>
                <select class="form-control destinations slt" name="dest_dep" id="page0">
                <option data-durations="["1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","22","29"]" value="2742791,84,2807730,2741968,2741642,2739917,3006,46,86,1376,78,33,60,2408,152,772385,761711,2154965,173,3281,2145842,19,2139425,2138511,100,143,1332887,1328096,1318341,228,974878,66,114,68,113,110,108,836519,117,125,235,103,96,2751021,80,4413,74,58,131,35,2521,93,45,104,61,875199,2974,569962,569714,97,2752241,1017,43,1434,2751301,120,151,2750660,20,2750048,2750009,65,2749976,147,2749698,63,13,37,568522,51,81,71,2750814,148,79,1578,31,62,36,1899141,1341882,34,568430,30,188,27,39,2488,69,5,7,44,1,1808,12,76,25,26,29,226,59,156,24,9,77,17,2,1341400,1843,4244,18,83,14,40,10,8,73,21,64,22,70,16,568546,567874,15,52,53,6,47,4,87,3,92,206,2791027,232,32,4100,192,207,234,2864646,2810039,2868424,3531,2790129,54,190,2789888,2789883,2789758,2813782,189,2781986,2865433,209,215,212,213,169,210,186,185,231,163,349950,214,211,162,2738,348147,348023,347115" selected="selected">All Countries</option>
                <option data-durations="["1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16"]" value="2742791,84,2807730,2741968,2741642,2739917,3006,96,2751021,80,4413,97,2752241,1017,43,1434,2751301,120,151,2750660,20,2750048,2750009,65,2749976,147,2749698,51,81,71,2750814,148,79,1578,31,206,2791027,232,32,4100,192,207,234,2864646,2810039,2868424,3531,2790129,54,190,2789888,2789883,2789758,2813782,189,2781986,2865433,209,215,212,213,169,210,186,185,231,163,349950,214,211,162,2738,348147,348023,347115">All Canada and United States</option>
                <option data-durations="["4","5","6","7","8","9","10","11","12","13","14","15","16","22","29"]" value="2408,152,772385,761711,2154965,173,3281,2145842,19,2139425,2138511,100,143,1332887,1328096,1318341,228,974878,66,114,68,113,110,108,836519,117,125,235,103,74,58,131,93,45,104,61,875199">All Europe</option>
                <option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15","16","17"]" value="46,86,1376,78,33,60,35,2521,2974,569962,569714,63,13,37,568522,62,36,1899141,1341882,34,568430,30,188,27,39,2488,69,5,7,44,1,1808,12,76,25,26,29,226,59,156,24,9,77,17,2,1341400,1843,4244,18,83,14,40,10,8,73,21,64,22,70,16,568546,567874,15,52,53,6,47,4,87,3,92">All South</option>
                <option data-durations="["3","4","6","7","10","14"]" value="27">Antigua and Barbuda(Antigua)</option>
                <option data-durations="["3","4","5","6","7","8","9","10","12","13","14","15"]" value="29">Aruba(Aruba)</option>
                <option data-durations="["3","4","5","6","7","10","11","14"]" value="188,26,25">Bahamas</option>
                <option data-durations="["3","4","7","10","11","14"]" value="26">    Freeport</option>
                <option data-durations="["3","4","7","14"]" value="188">    Great Exuma</option>
                <option data-durations="["3","4","5","6","7","10","11","14"]" value="25">    Nassau</option>
                <option data-durations="["3","4","5","6","7","8","10","14"]" value="30">Barbados(Bridgetown)</option>
                <option data-durations="["8","15"]" value="103">Belgium(Brussels)</option>
                <option data-durations="["5","7","10","14"]" value="60">Bermuda(Bermuda)</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="349950,348147,348023,347115,2738,231,215,214,213,212,211,210,209,186,185,169,163,162">Canada</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="347115">    Baie-Saint-Paul</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="348023">    Dartmouth</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="348147">    Dorval</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="2738">    Gatineau Hull</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="162">    Halifax</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="211">    Moncton</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="214">    Mont-Tremblant</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="349950">    Montebello</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="163">    Montreal</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="231">    North Bay</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="185">    Ottawa</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="186">    Quebec City</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="210">    Sault Ste Marie</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="169">    St. John s</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="213">    Sudbury</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="212">    Thunder Bay</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="215">    Timmins</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="209">    Windsor</option>
                <option data-durations="["7","10","14"]" value="33">Cayman Islands(Grand Cayman)</option>
                <option data-durations="["3","4","5","6","7","9","10","11","12","14","16"]" value="569962,569714,2974,568522,568430,568546,567874,70,64,22,21,16">Costa Rica</option>
                <option data-durations="["7","14"]" value="567874">    Bagaces</option>
                <option data-durations="["6","7","14"]" value="568430">    Culebra</option>
                <option data-durations="["5","6","7","9","10","11","12","14","16"]" value="568522">    El Coco</option>
                <option data-durations="["5","6","7","9","10","11","12","14","16"]" value="568546">    El Jobo</option>
                <option data-durations="["3","4","5","6","7","14"]" value="16">    Jaco beach</option>
                <option data-durations="["5","6","7","9","10","11","12","14","16"]" value="70">    Liberia</option>
                <option data-durations="["3","4","5","6","7","14"]" value="22">    Playa Tambor</option>
                <option data-durations="["6","7","14"]" value="569714">    Potrero</option>
                <option data-durations="["3","4","5","6","7","9","10","11","14","16"]" value="64">    Puntarenas</option>
                <option data-durations="["5","7","10","14"]" value="569962">    Samara</option>
                <option data-durations="["3","4","5","6","7","14"]" value="21">    San Jose</option>
                <option data-durations="["6","7","14"]" value="2974">    Tamarindo</option>
                <option data-durations="["8","15"]" value="235">Croatia(Zagreb)</option>
                <option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15","16","17"]" value="37,92,87,53,52,47,15,6,4,3">Cuba</option>
                <option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15","16"]" value="92">    Cayo Coco</option>
                <option data-durations="["5","7","9","12","14","16"]" value="3">    Cayo Largo</option>
                <option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15"]" value="87">    Cayo Santa Maria</option>
                <option data-durations="["3","4","5","6","7","8","9","11","12","13","14","15"]" value="4">    Cienfuegos</option>
                <option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15"]" value="47">    Havana</option>
                <option data-durations="["3","4","5","6","7","8","10","11","12","14","15","16","17"]" value="6">    Holguin</option>
                <option data-durations="["7","14"]" value="37">    Manzanillo de Cuba</option>
                <option data-durations="["5","6","7","8","12","13","14","15"]" value="53">    Santa Lucia (Camaguey)</option>
                <option data-durations="["4","7","10","11","14","16","17"]" value="52">    Santiago de Cuba</option>
                <option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15"]" value="15">    Varadero</option>
                <option data-durations="["6","7","14"]" value="76">Curacao(Curacao)</option>
                <option data-durations="["8","15"]" value="125">Czech Republic(Prague)</option>
                <option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15"]" value="13,12,73,40,14,10,8">Dominican Republic</option>
                <option data-durations="["5","6","7","9","14"]" value="13">    Cabarete</option>
                <option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15"]" value="73">    La Romana</option>
                <option data-durations="["3","4","5","6","7","8","9","10","11","12","14"]" value="8">    Puerto Plata</option>
                <option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15"]" value="10">    Punta Cana</option>
                <option data-durations="["3","4","5","6","7","10","14"]" value="40">    Samana</option>
                <option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15"]" value="14">    Santo Domingo</option>
                <option data-durations="["3","4","5","6","7","8","9","10","11","12","14"]" value="12">    Sosua</option>
                <option data-durations="["4","6","7","8","9","10","11","12","13","14","15","16"]" value="836519,117,114,113,110,108,68,875199,61">France</option>
                <option data-durations="["8","15"]" value="117">    Bordeaux</option>
                <option data-durations="["8","15"]" value="836519">    Bruges</option>
                <option data-durations="["10","11","13"]" value="108">    Lyon</option>
                <option data-durations="["8","10","11","13","15"]" value="110">    Marseille</option>
                <option data-durations="["4","6","7","8","9","10","14"]" value="875199">    Montrouge</option>
                <option data-durations="["8","15"]" value="113">    Nantes</option>
                <option data-durations="["8","15"]" value="68">    Nice</option>
                <option data-durations="["4","6","7","8","9","10","11","12","13","14","15","16"]" value="61">    Paris</option>
                <option data-durations="["10"]" value="114">    Toulouse</option>
                <option data-durations="["8","11","12","15"]" value="974878,66">Greece</option>
                <option data-durations="["8","11","12","15"]" value="66">    Athens</option>
                <option data-durations="["8","15"]" value="974878">    Kinetta</option>
                <option data-durations="["4","7","10","14"]" value="34">Grenada(Grenada)</option>
                <option data-durations="["7","14"]" value="63,1808,83">Honduras</option>
                <option data-durations="["7","14"]" value="63">    La Ceiba</option>
                <option data-durations="["7","14"]" value="83">    Roatan</option>
                <option data-durations="["7","14"]" value="1808">    Tela</option>
                <option data-durations="["8","15"]" value="228">Hungary(Budapest)</option>
                <option data-durations="["6","7","8","9","10","15"]" value="104">Ireland(Dublin)</option>
                <option data-durations="["4","6","7","8","9","10","11","12","13","14","15","16"]" value="1332887,1328096,1318341,143,45">Italy</option>
                <option data-durations="["8","15"]" value="1318341">    Amalfi</option>
                <option data-durations="["8","15"]" value="1328096">    Maiori</option>
                <option data-durations="["8"]" value="1332887">    Ravello</option>
                <option data-durations="["4","6","7","8","9","10","11","12","13","14","15","16"]" value="45">    Rome</option>
                <option data-durations="["8","15"]" value="143">    Sorrento</option>
                <option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15","16"]" value="78,1341882,1341400,4244,1843,18">Jamaica</option>
                <option data-durations="["5","7","10","14"]" value="78">    Kingston</option>
                <option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15","16"]" value="18">    Montego Bay</option>
                <option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15","16"]" value="4244">    Negril</option>
                <option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15","16"]" value="1843">    Ocho Rios</option>
                <option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15","16"]" value="1341400">    Runaway Bay</option>
                <option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","16"]" value="1341882">    Whitehouse</option>
                <option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15","16"]" value="1376,86,2488,69,44,7,5,1,156,77,24,17,9,2">Mexico</option>
                <option data-durations="["7","14"]" value="1">    Acapulco</option>
                <option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15","16"]" value="2">    Cancun</option>
                <option data-durations="["7","10","14"]" value="17">    Cozumel</option>
                <option data-durations="["5","7","10","14"]" value="44">    Huatulco</option>
                <option data-durations="["5","6","7","10","14"]" value="7">    Ixtapa</option>
                <option data-durations="["5","7","10","14"]" value="1376">    La Paz</option>
                <option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15"]" value="77">    Los Cabos</option>
                <option data-durations="["7","14"]" value="5">    Manzanillo</option>
                <option data-durations="["7","14"]" value="69">    Mazatlan</option>
                <option data-durations="["7","14"]" value="86">    Merida</option>
                <option data-durations="["5","7","10","14"]" value="2488">    Puerto Escondido</option>
                <option data-durations="["3","4","5","6","7","9","10","11","14"]" value="9">    Puerto Vallarta</option>
                <option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15","16"]" value="24">    Riviera Maya</option>
                <option data-durations="["3","4","5","6","7","9","10","11","14"]" value="156">    Riviera Nayarit</option>
                <option data-durations="["8","10","14","15"]" value="100">Netherlands(Amsterdam)</option>
                <option data-durations="["3","4","7","10","11","14"]" value="1899141,226,59">Panama</option>
                <option data-durations="["4","7","10","14"]" value="1899141">    Gamboa</option>
                <option data-durations="["3","4","7","10","11","14"]" value="59">    Panama City</option>
                <option data-durations="["4","7","10","11","14"]" value="226">    Playa Blanca</option>
                <option data-durations="["7","8","9","10","11","12","13","14","15","16","22","29"]" value="2154965,2145842,2139425,2138511,3281,173,19,93">Portugal</option>
                <option data-durations="["8","9","10","12","14","15","22","29"]" value="2138511">    Albufeira</option>
                <option data-durations="["7","8","9","10","12","14","15","16"]" value="2139425">    Armacao de Pera</option>
                <option data-durations="["7","8","9","10","12","14","15","16","22","29"]" value="19">    Faro</option>
                <option data-durations="["7","8","9","10","11","12","13","14","15","16"]" value="2145842">    Lagos</option>
                <option data-durations="["7","8","9","10","11","12","13","14","15","16"]" value="93">    Lisbon</option>
                <option data-durations="["7","8","9","10","12","14","15","16","22","29"]" value="3281">    Portimao</option>
                <option data-durations="["8","10","11","15"]" value="173">    Porto</option>
                <option data-durations="["7","8","9","10","12","14","15","16"]" value="2154965">    Vilamoura</option>
                <option data-durations="["7","8","14","15"]" value="46">Puerto Rico(San Juan)</option>
                <option data-durations="["7","14"]" value="2521,35">Saint Kitts Nevis</option>
                <option data-durations="["7","14"]" value="2521">    Nevis</option>
                <option data-durations="["7","14"]" value="35">    St Kitts</option>
                <option data-durations="["3","4","5","6","7","8","10","14"]" value="36">Saint Lucia(St Lucia)</option>
                <option data-durations="["6","7","8","9","10","12","13","14","15","16","22","29"]" value="772385,761711,152,131,58">Spain</option>
                <option data-durations="["6","7","8","9","10","12","13","14","15","16"]" value="131">    Barcelona</option>
                <option data-durations="["8","15","22","29"]" value="761711">    Benalmadena</option>
                <option data-durations="["8","15","22","29"]" value="772385">    Fuengirola</option>
                <option data-durations="["8"]" value="58">    Madrid</option>
                <option data-durations="["8","15","22","29"]" value="152">    Torremolinos</option>
                <option data-durations="["3","4","5","7","10","11","14"]" value="39">St Maarten(St Maarten)</option>
                <option data-durations="["3","4","5","6","7","10","14"]" value="62">Turks And Caicos(Providenciales)</option>
                <option data-durations="["4","5","6","7","8","9","10","11","13","14","15","16"]" value="2408,74">United Kingdom</option>
                <option data-durations="["4","5","6","7","8","9","10","11","13","14","15","16"]" value="2408">    Gatwick</option>
                <option data-durations="["4","5","6","7","8","9","10","11","13","14","15","16"]" value="74">    London</option>
                <option data-durations="["1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16"]" value="2807730,2742791,2741968,2741642,2739917,3006,84,2751021,4413,96,80,2752241,2751301,2750660,2750048,2750009,2749976,2749698,1434,1017,151,147,120,97,65,43,20,2750814,1578,148,81,79,71,51,31,2868424,2865433,2864646,2813782,2810039,2791027,2790129,2789888,2789883,2789758,2781986,4100,3531,234,232,207,206,192,190,189,54,32">United States</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="2865433">    Alexandria</option>
                <option data-durations="["3","4","5","7"]" value="3006">    Anaheim</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="2781986">    Bethesda</option>
                <option data-durations="["3","4","6","7","14"]" value="4413">    Boca Raton</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="189">    Boston</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="2813782">    Brooklyn</option>
                <option data-durations="["3","4","5","7"]" value="2739917">    Buena Park</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="2789758">    Cambridge</option>
                <option data-durations="["4"]" value="2749698">    Cape Coral</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="2789883">    Chatham</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="2789888">    Chelsea</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="190">    Chicago</option>
                <option data-durations="["3","4","5","6","7","8","9","12","13","14"]" value="147">    Clearwater</option>
                <option data-durations="["7"]" value="54">    Cocoa beach</option>
                <option data-durations="["4","5","6","7","8","9","12","13","14","15"]" value="2749976">    Davenport</option>
                <option data-durations="["3","4","5","6","7","14"]" value="65">    Daytona Beach</option>
                <option data-durations="["6","7","8","13","14","15"]" value="2750009">    Delray Beach</option>
                <option data-durations="["6","7","14"]" value="2750048">    Duck Key</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="2790129">    Edgartown</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="3531">    Falmouth</option>
                <option data-durations="["3","4","5","6","7","8","13","14"]" value="31">    Fort Lauderdale</option>
                <option data-durations="["4"]" value="20">    Fort Myers</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="2868424">    Herndon</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="2810039">    Hoboken</option>
                <option data-durations="["3","4","5","6","7","8","14"]" value="1578">    Hollywood (Florida)</option>
                <option data-durations="["3","4","5","6","7","10","14"]" value="79">    Honolulu</option>
                <option data-durations="["3","4","5","7"]" value="2741642">    Huntington Beach</option>
                <option data-durations="["3","4","5","6","7","9","11","14"]" value="2750660">    Indian Rocks Beach</option>
                <option data-durations="["3"]" value="2864646">    Jeffersonville</option>
                <option data-durations="["6","7","8","13","14","15"]" value="151">    Key Largo</option>
                <option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15","16"]" value="148">    Kissimmee</option>
                <option data-durations="["8"]" value="80">    Kona</option>
                <option data-durations="["3","4","5","7"]" value="2741968">    Laguna Beach</option>
                <option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15","16"]" value="2750814">    Lake Buena Vista</option>
                <option data-durations="["3","4","5","6","7","8","10","14"]" value="71">    Las Vegas</option>
                <option data-durations="["3","4","5","6","7"]" value="2807730">    Laughlin</option>
                <option data-durations="["3","4","5","7"]" value="84">    Los Angeles</option>
                <option data-durations="["3","4","6","7","14"]" value="2751021">    Manalapan</option>
                <option data-durations="["5","7","10","14"]" value="81">    Maui</option>
                <option data-durations="["7"]" value="234">    Melbourne</option>
                <option data-durations="["3","4","5","6","7","8","13","14"]" value="51">    Miami</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="207">    Myrtle Beach</option>
                <option data-durations="["7"]" value="120">    Naples</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="192">    New York</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="4100">    Newark</option>
                <option data-durations="["3","4","5","7"]" value="2742791">    Newport Beach</option>
                <option data-durations="["3","4","5","6","7","14","15"]" value="2751301">    North Redington Beach</option>
                <option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15","16"]" value="32">    Orlando</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="232">    Pittsburgh</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="2791027">    Plymouth</option>
                <option data-durations="["6","7","8","14"]" value="1434">    Pompano Beach</option>
                <option data-durations="["3","4","6","7","14"]" value="96">    Sarasota</option>
                <option data-durations="["3","4","5","6","7","8","9","10","11","12","13","14","15","16"]" value="43">    St Petersburg</option>
                <option data-durations="["4","5","6","7","8","9","12","13","14","15"]" value="1017">    Tampa</option>
                <option data-durations="["7","14"]" value="2752241">    Treasure Island</option>
                <option data-durations="["1","2","3","4","5","6","7","8"]" value="206">    Washington</option>
                <option data-durations="["3","4","6","7","8","13","14","15"]" value="97">    West Palm Beach</option>
                </select>
            </div>

            <div class="input-group montheSelect">
                <span class="input-group-addon">Month</span>
                <input class="form-control NoIcon" type="text" name="month" id="mpage0">
            </div>

            <div class="input-group li-4">
                <span class="input-group-addon">Duration</span>
                <select class="form-control durations slt" name="duration">
                <option value="3days,4days">3 or 4 days</option>
                <option value="7days,8days" selected="selected">7 or 8 days</option>
                <option value="5days,6days,7days,8days,9days,10days">5 to 10 days</option>
                <option value="11days,12days,13days,14days,15days,16days">11 to 16 days</option>
                </select>
            </div>
        </div>

        <div class="optionElements">
            <div class="checkboxes inclusiveList">
                <dl class="form-group row form-inline">
                    <dt class="dt-4">
                        <label>
                        <input type="checkbox" class="calendarOptionsAll" name="allInclusive" value="1"><span>All Inclusive</span>
                        </label>
                    </dt>
                    <dd class="dd-10">
                        <ul class="unstyled">
                            <li class="li-3">
                                <label>
                                <input type="checkbox" class="calendarOptions" name="adults" value="1"><span>Adults Only</span>
                                </label>
                            </li>
                            <li class="li-3">
                                <label>
                                <input type="checkbox" class="calendarOptions" name="beach" value="1"><span>Beach</span>
                                </label>
                            </li>
                            <li class="li-3">
                                <label>
                                <input type="checkbox" class="calendarOptions" name="casino" value="1"><span>Casino</span>
                                </label>
                            </li>
                            <li class="li-3">
                                <label>
                                <input type="checkbox" class="calendarOptions" name="family" value="1"><span>Family</span>
                                </label>
                            </li>
                            <li class="li-3">
                                <label>
                                <input type="checkbox" class="calendarOptions" name="golf" value="1"><span>Golf</span>
                                </label>
                            </li>
                        </ul>

                        <ul class="unstyled form-inline">
                            <li class="li-3">
                                <label>
                                <input type="checkbox" class="calendarOptions" name="kitchenette" value="1"><span>Kichenette</span>
                                </label>
                            </li>
                            <li class="li-3">
                                <label>
                                <input type="checkbox" class="calendarOptions" name="miniclub" value="1"><span>Mini Club</span>
                                </label>
                            </li>
                            <li class="li-3">
                                <label>
                                <input type="checkbox" class="calendarOptions" name="oceanview" value="1"><span>OceanView</span>
                                </label>
                            </li>
                            <li class="li-3">
                                <label>
                                <input type="checkbox" class="calendarOptions" name="spa" value="1"><span>Spa</span>
                                </label>
                            </li>
                            <li class="li-3">
                                <label>
                                <input type="checkbox" class="calendarOptions" name="wedding" value="1"><span>Wedding</span>
                                </label>
                            </li>
                        </ul>
                    </dd>
                </dl>
            </div>
            <div class="checkboxes star-rating">
                <ul class="unstyled form-group row form-inline starNum" id="">
					<li class="li-2">
							<label>
							<input type="checkbox" class="calendarStars" name="2.0"><span>2 <i class="glyphicon glyphicon-star"></i></span>
							</label>
					</li>
					<li class="li-2">
							<label>
							<input type="checkbox" class="calendarStars" name="2.5"><span>2.5 <i class="glyphicon glyphicon-star"></i></span>
							</label>
					</li>
					<li class="li-2">
							<label>
							<input type="checkbox" class="calendarStars" name="3.0"><span>3 <i class="glyphicon glyphicon-star"></i></span>
							</label>
					</li>
					<li class="li-2">
							<label>
							<input type="checkbox" class="calendarStars" name="3.5" checked ><span>3.5 <i class="glyphicon glyphicon-star"></i></span>
							</label>
					</li>
					<li class="li-2">
							<label>
							<input type="checkbox" class="calendarStars" name="4.0"  checked ><span>4 <i class="glyphicon glyphicon-star"></i></span>
							</label>
					</li>
					<li class="li-2">
							<label>
							<input type="checkbox" class="calendarStars" name="4.5" checked ><span>4.5 <i class="glyphicon glyphicon-star"></i></span>
							</label>
					</li>
					<li class="li-2">
							<label>
							<input type="checkbox" class="calendarStars" name="5.0" checked ><span>5 <i class="glyphicon glyphicon-star"></i></span>
							</label>
					</li>
					<li class="li-4">
							<label>
							<input type="checkbox" class="starsAll" value="all" ><span>Check/Uncheck All</span>
							</label>
					</li>
                </ul>
            </div>
        </div>
        <input type="hidden" id="hidden" name="hidden" class="hidden" value="0">
		<input type="hidden" id="to_name" data-to="page0" name="destination" value="All Countries">
		<input type="hidden" id="fo_name" data-fo="page0" name="gateway" value="Toronto">
        <input type="submit" name="submit" value="Search" class="own-btn search-btn">
    </form>

	<div id="pleaseWaitDialog" class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog" aria-hidden="true">
	   <div class="modal-dialog modal-m">
		   <div class="modal-content">
			   <div class="modal-header"><h4><span id="gatewayID">Toronto</span><div></div><span id="destinationID">All Countries</span></h4></div>
			   <div class="modal-body">
				   <div class="progress progress-striped active" style="margin-bottom:0;"><div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="100" aria-valuemin="80" aria-valuemax="100" style="width: 100%"></div></div>
			   </div>
			   <div class="modal-footer"><h4>W Finding Lowest Price for <strong id="monthDay"><?php echo date('F Y'); ?></strong></h4></div>
		   </div>
	   </div>
	</div>
</div>
</body>
</html>
