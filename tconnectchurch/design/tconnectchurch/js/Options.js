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
		name	: 'sidr-main',
		side		: 'right',
		source	: '.top_navigation'
});<!--Eof Option for sliding menu-->

$(document).ready(function() {
<!--Bof JQuery to verify the OS type-->
	var isIOS = /iPad|iPhone|iPod/i.test(navigator.userAgent);	// is iOs?
	var isAndroid = /Android/i.test(navigator.userAgent);					// is Android?
	if (!isIOS && !isAndroid){
			$("body").attr('data-window', 'NO mobile');
	}
	else if (isIOS){
			$("body").attr('data-window', ' iOS');
			$("div#slider-area").each(function() {
					$(this).find(".slides").addClass('div-mobile');
			});
	}
	else if (isAndroid){
			$("body").attr('data-window', 'Android');
			$("div#slider-area").each(function() {
					$(this).find(".slides").addClass('div-mobile');
			});
	}

<!--Bof JQuery to verify the width of window-->
	var documentWidth = $(window).width(); //retrieve current document width
	$("body").attr('data-started-width', documentWidth);

	//document.getElementById("debugid").innerHTML = documentWidth;

	resize_video();	// Function to add style element for video
	accordionMenus(); // Function to call accodion menu

	//var $window = $(window);
	$(window).on('resize', function(){
		var windowWidth = $(window).width(); //retrieve current window width
		$("body").attr('data-window-width', windowWidth);
		$(".be-bg-video").attr("style", "display: block;");
		resize_video();	// Function to add style element for video

		<!--Bof JQuery Option for sliding menu -->
		if(windowWidth > 609 ) {
					$("body").removeAttr('style');
					$("body").removeAttr('class');
					//$("body").removeClass('sidr-open');
					$("#sidr-main").attr('style', "display:none;");
		}
		else {
				if($("html").attr("style")){
						//$("body").addClass('sidr-open');
						$("body").attr('style', "width: "+windowWidth+"px; position: absolute; right: 200px;");
						$("#sidr-main").attr('style', "display: block; right: 0px;");
				}
		}
	}).trigger('resize');

<!--Bof JQuery for Double Click-->
	$( 'li:has(ul)' ).doubleTapToGo();

<!--Bof JQuery for putting max width of image-->
	var org_width = $("p").find("img").attr("width");
	if(org_width > 700){
		var o_width = 700;
	}
	else {
		var o_width = org_width;
	}
	$("p img").css("max-width", o_width +"px");

<!--Bof JQuery for Preventing click activate-->
	$('a[href^="#"]').click(function(et) {
		et.preventDefault();

		return false;
	});
/*
<!--Bof JQuery for starting with a # in the URL-->

	$('a[href^="#"]').click(function() {
			var target = $($(this).attr('href')); // Find the target element
			var pos = target.offset( ); 						 // And get its position fallback to scrolling to top || {left: 0, top: 0};

			if(pos) {													// jQuery will return false if there's no element and your code will throw errors if it tries to do .offset().left;
				$('html, body').animate({ 						// Scroll the page
						scrollTop: pos.top,
						scrollLeft: pos.left
				}, 1000 );
			}

			return false;																	// Don't let them visit the url, we'll scroll you there
	});
*/
	$('.top_navigation li').hover(function(){
			$('ul', this).stop(true, true).slideDown(100);
      $(this).addClass("active");
	},
	function(){
		$('ul', this).stop(true, true).slideUp(200);
		$(this).removeClass("active");
	});

<!--Bof JQuery for making smaller-->
		var scrolled_position = $(document).scrollTop().valueOf();
		if (scrolled_position >= 60) {
		    $("header").addClass('smaller');
		}
		else {
		    $("header").removeClass("smaller");
		}

	  $(window).scroll(function() {
			<!--For making header smaller-->
			var scroll = $(window).scrollTop();
			if (scroll >= 60) {
					$("header").addClass('smaller');
					$(".head_navigation").addClass('nodisplay');
			}
			else {
					$("header").removeClass("smaller");
					$(".head_navigation").removeClass('nodisplay');
			}

			<!--For Back-to-top-->
			if ($(this).scrollTop() > 220) {
				$('.go-to-top').fadeIn(700);
			}
			else {
				$('.go-to-top').fadeOut(700);
			}
	   });

		$('.go-to-top').click(function(event) {
			event.preventDefault();
			$('html, body').animate({scrollTop: 0}, 700);

			return false;
		});

<!--Bof JQuery for Accordion video section-->
		var childContainer = $("div.embed_video");
		childContainer.hide();
		childContainer.each(function() {
    		$(this).find("iframe").attr("data-url", $(this).find("iframe").attr("src"));
		});

		$('a[data-columns^="#"]').click(function() {
			//  Find the target element
			var target = $($(this).attr("data-columns"));
			var url = target.find("iframe").attr("src");

		    if( target.hasClass("hover") ) {
				target.find("iframe").attr("src", "");
				childContainer.removeClass("hover");
				target.slideUp("fast");
				target.find("iframe").attr("src", target.find("iframe").attr("data-url"));
		    }
			else {
				if( childContainer.hasClass("hover") ){
					$("div.hover").find("iframe").attr("src", "");
					childContainer.slideUp("fast");
					$("div.hover").find("iframe").attr("src", $("div.hover").find("iframe").attr("data-url"));
					childContainer.removeClass("hover");
				}
				target.find("iframe").attr("src", url);
				target.addClass("hover");
				target.slideDown("fast");
		    }

			return false;
		});

<!--Bof JQuery Option for Tabs Sliding-->
			$(".tabs_content").hide(); //Hide all content
			$(".tabs_content:first").show(); //Show first tab content
			$("ul.tabs li:first").addClass("active").show(); //Activate first tab			

			//On Click Event
			$("ul.tabs li").click(function() {
					$("ul.tabs li").removeClass("active"); //Remove any "active" class
					$(this).addClass("active"); //Add "active" class to selected tab
					$(".tabs_content").hide(); //Hide all tab content
					var activeTab = $(this).find("div.kw").attr("data-link"); //Find the rel attribute value to identify the active tab + content
					//$(activeTab).fadeIn(); //Fade in the active content
					$(activeTab).show( "slide", {direction: "right" }, 400 );
					return false;
			});

<!--Bof JQuery Option for Tabs Fading -->
			$(".tabt_content").hide(); //Hide all content
			$("ul.tabt li:first").addClass("active").show(); //Activate first tab
			$(".tabt_content:first").show(); //Show first tab content

			var vodContainer = $("div.embed_video");
			vodContainer.show();
			vodContainer.each(function() {
					$(this).find("iframe").attr("data-url", $(this).find("iframe").attr("src"));
			});

			//On Click Event
			$("ul.tabt li").click(function() {
					$("ul.tabs li").removeClass("active"); //Remove any "active" class
					$(this).addClass("active"); //Add "active" class to selected tab
					$(".tabt_content").hide(); //Hide all tab content

					if(vodContainer.hasClass("hover") ){
							$("div.hover").find("iframe").attr("src", "");
							vodContainer.hide();
							$("div.hover").find("iframe").attr("src", $("div.hover").find("iframe").attr("data-url"));
							vodContainer.removeClass("hover");
					}

					var activeTabs = $(this).find("div").attr("data-link"); //Find the rel attribute value to identify the active tab + content
					$(activeTabs).fadeIn(200, function() {
							if ( $(this).find('div.embed_video').length) {
									vodContainer.addClass("hover");
									vodContainer.fadeIn(100);
							}
					});

					return false;
			});
});

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

		if(r_w>r_i){
				new_h=windowHeight;new_w=windowHeight/r_i;
		}
		else{
				new_h=windowWidth*r_i;new_w=windowWidth;
		}
		new_left = (windowWidth-new_w)/2;
		new_top = (windowHeight-new_h)/2;

		if(window.chrome) {
				$vdo.attr('style', "display:block; padding-bottom:56.25%; width:"+new_w+"px; left:"+new_left+"px; top:"+new_top+"px;");
		}
		else{
				$vdo.attr('style', "display:block; width:"+new_w+"px; height:"+new_h+"px; left:"+new_left+"px; top:"+new_top+"px;");
		}
}<!--Eof Function for Reszing video width-->
