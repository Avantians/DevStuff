$(document).ready(function() {
    <!--Bof JQuery for getting width-->
		var documentWidth = $(window).width(); //retrieve current document width
		$("body").attr('data-started-width', documentWidth);
		
		resize_video();
		accordionMenus();
		
		var $window = $(window);
	    $window.on('resize', function(){
			var windowWidth = $(window).width(); //retrieve current window width
			$("body").attr('data-window-width', windowWidth);
			resize_video();
			
			<!--Bof JQuery Option for sliding menu -->
			if(windowWidth > 609) {
				$("body").removeAttr('style');
				$("body").removeClass('sidr-open sidr-main-open');
				$("#sidr-main").attr('style', "display:none;");
			}
			else {
					if($("html").attr("style")){
						$("body").addClass('sidr-open sidr-main-open');
						$("body").attr('style', "width: "+windowWidth+"px; position: absolute; right: 200px;");
						$("#sidr-main").attr('style', "display: block; right: 0px;");
					}
			}
	    }).trigger('resize');

    <!--Bof JQuery for Double Click-->
    $( 'nav li:has(ul)' ).doubleTapToGo();
    <!--Eof JQuery for Double Click-->

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

    <!--Bof JQuery to handle top menu with child menu slide -->
/*    var w = $(window).width();
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
*/
    <!--Bof JQuery for Accordion video section-->
	var videoId = $("div.videoid");
    var childContainer = $("div.embed_video");
	var SecondChildContainer = $("div.opened_embed_video");
		
    childContainer.hide();
    SecondChildContainer.show();
		
    $(videoId).each(function() {
        $(this).find("iframe").attr("data-link", $(this).find("iframe").attr("src"));
    });
		
	SecondChildContainer.addClass("hover");
								
    $('a[data-columns^="#"]').click(function() {
        //  Find the target element
        var target = $($(this).attr("data-columns"));
        var url = target.find("iframe").attr("src");

        if( target.hasClass("hover") ) {
            target.find("iframe").attr("src", "");
            target.removeClass("hover");
            target.slideUp("normal");
            target.find("iframe").attr("src", target.find("iframe").attr("data-link"));
						
			SecondChildContainer.addClass("hover");
			SecondChildContainer.slideDown("normal");
			SecondChildContainer.find("iframe").attr("src", SecondChildContainer.find("iframe").attr("data-link"));								
		}
		else {
            if( childContainer.hasClass("hover") ){
                $("div.hover").find("iframe").attr("src", "");
                childContainer.slideUp("normal");
                $("div.hover").find("iframe").attr("src", $("div.hover").find("iframe").attr("data-link"));
                childContainer.removeClass("hover");
            }
						
            target.find("iframe").attr("src", url);
            target.addClass("hover");
            target.slideDown("normal");
						
			if( SecondChildContainer.hasClass("hover") ){
					SecondChildContainer.find("iframe").attr("src", "");
					SecondChildContainer.removeClass("hover");
					SecondChildContainer.slideUp("normal");
					SecondChildContainer.find("iframe").attr("src", SecondChildContainer.find("iframe").attr("data-link"));							
			}								
        }

        return false;
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
        var scroll = $(window).scrollTop();

        if (scroll >= 60) {
            $("header").addClass('smaller');
            $(".head_navigation").addClass('nodisplay');
        }
		else {
            $("header").removeClass("smaller");
            $(".head_navigation").removeClass('nodisplay');
        }
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