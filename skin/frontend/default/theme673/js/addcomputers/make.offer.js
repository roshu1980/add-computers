(function($) {

	$(document).ready(function() {

		setTimeout(function(){
			$('.header-modal').append($('.youama-ajaxlogin-loader'));
		}, 1000);

		$(document).on('click', '#makeOffer', function(){
			$('.youama-offer-window').slideDown(300, 'easeInOutCirc');	
			return false;
		})
	});

	$('.header-modal span.close').on('click', function(){
		animateCloseWindow('offer', true, true);
	});

})(jQuery);

function animateLoader(windowName, step) {
    // Start
    if (step == 'start') {
        jQuery('.header-modal .youama-ajaxlogin-loader').fadeIn();
        jQuery('.header-modal .youama-' + windowName + '-window')
            .animate({opacity : '0.4'});
    // Stop
    } else {
        jQuery('.header-modal .youama-ajaxlogin-loader').fadeOut('normal', function() {
            jQuery('.header-modal .youama-' + windowName + '-window')
                .animate({opacity : '1'});
        });
    }
}

function animateCloseWindow(windowName, quickly, closeParent) {
    if (opts.stop != true){
        if (quickly == true) {
            jQuery('.youama-' + windowName + '-window').hide();
            jQuery('.youama-ajaxlogin-error').hide(function() {
                if (closeParent) {
                    jQuery('#header-account').removeClass('skip-active');
                }
            });
        } else {
            jQuery('.youama-ajaxlogin-error').fadeOut();
            jQuery('.youama-' + windowName + '-window').slideUp(function() {
                if (closeParent) {
                    jQuery('#header-account').removeClass('skip-active');
                }
            });
        }
    }
}
