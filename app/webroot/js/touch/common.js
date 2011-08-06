jQuery(document).ready(function($) {
	$("div:jqmData(role='page')").live('pageshow',function(){
		  $('a[href]').attr('href', function(index, href) {
			var param = 'app=' + Math.floor(Math.random()*100000);

			if (href.charAt(href.length - 1) === '?') //Very unlikely
				return href + param;
			else if (href.indexOf('?') > 0)
				return href + '&' + param;
			else
				return href + '?' + param;
		});
		$('div.message').fadeOut(12000, function() {
			$('div.message').remove();
		});
	});					
});