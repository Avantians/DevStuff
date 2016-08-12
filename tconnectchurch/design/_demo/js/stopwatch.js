var $start      = $('#start'),
	startText   = $start.text(),
	stopText    = $start.attr('alternate'),
	$set      = $('#set'),
	$reset      = $('#reset'),
	$stopwatch      = $('#stopwatch'),
	$sets     = $('#sets'),

	/*
	 * I found this code on a few sites and am unsure of the original author.
	 * If you know please inform me so I can credit them here.
	 *
	 * 0 = start time
	 * 1 = end time
	 * 2 = state (stopped or counting)
	 * 3 = total elapsed time in ms
	 * 4 = stopwatch (interval object)
	 * 5 = epoch (January 1, 1970)
	 * 6 = element (not used here, normally stores the DOM element to update with the time)
	 * 7 = set count
	 */
	t = [0, 0, 0, 0, 0, 0, 0, 0],
	
	format = function(ms) {
		var d = new Date(ms + t[5]).toString().replace(/.*(\d{2}:\d{2}:\d{2}).*/, '$1');
		var x = String(ms % 1000);
		while (x.length < 3) {
			x = '0' + x;
		}
		d += '.' + x;
		return d.substr(0, d.length - 1);
	},
	
	zero = function(num) {
		if (parseInt(num) < 0) var neg = true;
		if (Math.abs(parseInt(num)) < 10) {
			num = '0' + Math.abs(num);
		}
		if (neg) num = '-' + num;
		return num;
	},
	
	startStop = function() {
		t[t[2]] = (+new Date()).valueOf();
		t[2] = 1 - t[2];
		
		if (t[2] == 0) {
			clearInterval(t[4]);
			t[3] += t[1] - t[0];
			
			$start.text(startText);
			
			t[7]++;
			$sets.show();
			$('<li><span>' + zero(t[7]) + '</span> ' + format(t[3]) + '</li>').appendTo($sets).slideDown('fast');
			$sets.find('li').removeClass('first last');
			$sets.find('li:first').addClass('first').end().find('li:last').addClass('last');
			
			t[4] = t[1] = t[0] = 0;
			
			display();
		}
		else {
			$start.text(stopText);
			t[4] = setInterval(display, 43);
		}
		
		return false;
	},

	reset = function() {
		if (t[2]) {
			startStop();
		}
		
		t[4] = t[3] = t[2] = t[1] = t[0] = 0;
		
		display();
		
		$start.text(startText);
		$sets.slideUp('fast', function() {
			$sets.empty();
		});
		
		t[7] = 0;
		
		return false;
	},
	
	display = function() {
		if (t[2]) {
			t[1] = (new Date()).valueOf();
		}
		
		$stopwatch.text(format(t[3] + t[1] - t[0]));
	},
	
	set = function() {
		if (t[2] !== 0) {
			t[7]++;
			$sets.show();
			$('<li><span>' + zero(t[7]) + '</span> ' + format(t[3] + t[1] - t[0]) + '</li>').appendTo($sets).slideDown('fast');
			$sets.find('li').removeClass('first last');
			$sets.find('li:first').addClass('first').end().find('li:last').addClass('last');
		}
		
		return false;
	},
	
	load = function() {
		t[5] = new Date(1970, 1, 1, 0, 0, 0, 0).valueOf();

		display();
	};

$(function() {

	$sets.empty();
	
	load();
	
	$start.click(startStop);
	$set.click(set);
	$reset.click(reset);
	
		
	
	// Assign classes to special items
	$('#swlabels li:last').addClass('last');

});
        
