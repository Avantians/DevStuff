<!--Bof for HTML5 placeholder fix-->
$('[placeholder]').focus(function() {
		var input = $(this);
		if (input.val() == input.attr('placeholder')) {
			input.val('');
			input.removeClass('placeholder');
		}
}).blur(function() {
		var input = $(this);
		if (input.val() == '' || input.val() == input.attr('placeholder')) {
			input.addClass('placeholder');
			input.val(input.attr('placeholder'));
		}
}).blur().parents('form').submit(function() {
		$(this).find('[placeholder]').each(function() {
			var input = $(this);
			if (input.val() == input.attr('placeholder')) {
				input.val('');
			}
		})
});<!--Eof for HTML5 placeholder fix-->

<!--Bof Option for sliding menu-->
$('#pull').sidr({
		name: 'sidr-main',
		side: 'right',
		source: '.topnav'
});<!--Eof Option for sliding menu-->

<!--Bof Function for Accordion Menu-->
function accordionMenus() {
	$('ul.sidr-inner ul').hide();
	$('ul.sidr-inner li a').click(function() {
			var checkElement = $(this).next();
			var parent = this.parentNode.parentNode.id;

			if((checkElement.is('ul')) && (checkElement.is(':visible'))) {
					if($('#' + parent).hasClass('collapsible')) {
						$('#' + parent + ' ul:visible').slideUp('normal');
					}
					return false;
			}
			if((checkElement.is('ul')) && (!checkElement.is(':visible'))) {
					$('#' + parent + ' ul:visible').slideUp('normal');
					checkElement.slideDown('normal');
					return false;
			}
		}
	);
}<!--Eof Function for Accordion Menu-->

<!--Bof Function for Reszing video width-->
function resize_video() {
	var videoContainer = $(".page .be-bg-video"),
				$vdo=$(videoContainer),
				$section=$vdo.parent(),
				windowWidth=$section.width(),
				windowHeight=$section.outerHeight(),
				r_w=windowHeight/windowWidth,
				i_w=$vdo.width(),
				i_h=$vdo.height(),
				r_i=i_h/i_w;

	if(r_w>r_i){new_h=windowHeight;new_w=windowHeight/r_i;}else{new_h=windowWidth*r_i;new_w=windowWidth;}
	new_left = (windowWidth-new_w)/2;
	new_top = (windowHeight-new_h)/2;

	if(window.chrome) {
		$vdo.attr('style', "display:block; padding-bottom:56.25%; width:"+new_w+"px; left:"+new_left+"px; top:"+new_top+"px;");
	}
	else {
		$vdo.attr('style', "display:block; width:"+new_w+"px; height:"+new_h+"px; left:"+new_left+"px; top:"+new_top+"px;");
	}
}<!--Eof Function for Reszing video width-->

$(document).ready(function() {
    <!--Bof JQuery for checking type of OS-->
		var isIOS = /iPad|iPhone|iPod/i.test(navigator.userAgent); //is iOs?
		var isAndroid = /Android/i.test(navigator.userAgent); //is Android?

		if (!isIOS && !isAndroid){
			$("body").attr('data-window', 'NO Mobile');
		}
		else if (isIOS){
			$("body").attr('data-window', ' iOS');
		}
		else if (isAndroid){
			$("body").attr('data-window', 'Android');
		}

    <!--Bof JQuery for getting width-->
		var documentWidth = $(window).width(); //retrieve current document width
		$("body").attr('data-started-width', documentWidth);
		resize_video();

		var $window = $(window);
    $window.on('resize', function(){
				var windowWidth = $(window).width(); //retrieve current window width
				$("body").attr('data-window-width', windowWidth);
				resize_video();
    }).trigger('resize');

		<!--Bof JQuery for Double Click-->
    $( 'nav li:has(ul)' ).doubleTapToGo();
		<!--Eof JQuery for Double Click-->

		<!--Bof JQuery for click activate on #-->
		var full_url = document.URL; // Get current url
		var url_array = full_url.split('#') // Split the string into an array with / as separator alert(url_array.length);
		var last_segment = url_array[url_array.length-1];  // Get the last part of the array (-1)
		if(url_array.length == 2){
				//alert(url_array.length);
				$("html, body").animate({ scrollTop: $('#' + last_segment).offset().top - 70 }, 1000, 'swing');
		}
		$('a[href*=#]:not([href=#])').click(function() {
				$("html, body").animate({ scrollTop: $('#' + last_segment).offset().top - 70 }, 1000, 'swing');
		});
		<!--Bof JQuery for Preventing click activate-->
		$('a[href^="#"]').click(function(et) {
					et.preventDefault();
					return false;
		});

		var org_width = $(".pageentry p").find("img").attr("width");
		if(org_width > 700){
				var o_width = 700;
		} else {
				var o_width = org_width;
		}
		$(".pageentry p img").attr('style', "margin-left:auto; margin-right:auto; max-width:"+o_width+"px !important; display: table;");
		$(".pageentry p img").attr('id', "topImage");

		var foto_width = $(".pageimg").find("img").attr("data-width");
		if(foto_width > 700){
			$(".pageimg img").attr('style', "margin-left:auto; margin-right:auto; max-width:700px !important; display: table;");
		}	else {
			$(".pageimg img").attr('style', "margin-left:auto; margin-right:auto; max-width:"+foto_width+"px !important; display: table;");
		}

	if(window.chrome) {
		$('.banner li').css('background-size', '100% 100%');
		$('.promosection li').css('background-size', '100% 100%');
	}
	$('.banner').unslider({
		fluid: true,
		dots: true,
		delay: 4000,
		speed: 1000
	});
	$('.promosection').unslider({
		fluid: false,
		dots: true,
		delay: 10000,
		speed: 3000
	});

<!--Bof JQuery to handle top menu with child menu slide -->
			var w = $(window).width();
			if(w > 624 ) {
					$("nav ul").addClass('jopen');
			}
			else {
					$("nav ul").removeClass('jopen');
			}
			$("ul.jopen li").hover(
					function(){
							$(this).children('ul').hide();
							$(this).children('ul').slideDown('fast');
					},
					function () {
							$('ul', this).slideUp('fast');
			});

<!--Bof JQuery Option for Togle menu -->
			accordionMenus();
			$(window).resize(function(){
				if(w > 624 ) {
						$("body").removeAttr('style');
						$("body").removeClass('sidr-open sidr-main-open');
						$("#sidr-main").attr('style', "display:none;");
				} else {
						if($("html").attr("style")){
							$("body").addClass('sidr-open sidr-main-open');
							$("body").attr('style', "width: "+w+"px; position: absolute; right: 200px;");
							$("#sidr-main").attr('style', "display: block; right: 0px;");
						}
				}
			});
<!--Eof JQuery Option for Togle menu -->

<!--Bof JQuery Option for Back-to-top-->
			var offset = 220;
			var duration = 500;
			$(window).scroll(function() {
				if ($(this).scrollTop() > offset) {
						$('.back-to-top').fadeIn(duration);
				} else {
						$('.back-to-top').fadeOut(duration);
				}
			});

			$('.back-to-top').click(function(event) {
				event.preventDefault();
				$('html, body').animate({scrollTop: 0}, duration);
				return false;
			})
<!--Eof JQuery Option for Back-to-top-->

<!--Bof JQuery Option for Tabs -->
			$(".tab_content").hide(); //Hide all content
			$("ul.tabs li:first").addClass("active").show(); //Activate first tab
			$(".tab_content:first").show(); //Show first tab content

			//On Click Event
			$("ul.tabs li").click(function() {
				$("ul.tabs li").removeClass("active"); //Remove any "active" class
				$(this).addClass("active"); //Add "active" class to selected tab
				$(".tab_content").hide(); //Hide all tab content
				var activeTab = $(this).find("a").attr("data-link"); //Find the rel attribute value to identify the active tab + content
				$(activeTab).fadeIn(); //Fade in the active content
				return false;
			});
<!--Eof JQuery Option for Tabs -->
});