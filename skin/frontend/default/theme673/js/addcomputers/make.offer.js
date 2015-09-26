var offeredProduct = 0, thisUser;
(function($) {

	$(document).ready(function() {

		thisUser = (isLoggedIn) ? 'offer-user' : 'offer-guest';

		// show offer modal
		$(document).on('click', '.make-offer', function(){
			offeredProduct = $(this).attr('data-id');
			animateShowWindow(thisUser);
			return false;
		})

		// click on X(close) buttom
		$(document).on('click', '.header-modal span.close', function(){
			offeredProduct = 0;
			animateCloseWindow(thisUser, true, true);
		});

        // Switching to Register window
        $('#goToRegister').on('click', function() {
            $('html,body').animate({scrollTop : 0});
            animateCloseWindow(thisUser, false, false);
            animateShowWindow('register');
        });

        // Switching to Login window
        $('#goToLogin').on('click', function() {
            $('html,body').animate({scrollTop : 0});
            animateCloseWindow(thisUser, false, false);
            animateShowWindow('login');
        });

        // Press enter in make offer window
        $(document).keypress(function(e) {
            if(e.which == 13 && $('.youama-' + thisUser + '-window').css('display') == 'block') {
                var thisPrice = $('#offeredPrice').val();
                var thisQuantity = $('#quantityWanted').val();
		        checkTheOffer(thisPrice, thisQuantity);
            }
        });

        // Click on send offer button
        $('.youama-makeoffer-button').on('click', function() {
            var thisPrice = $('#offeredPrice').val();
            var thisQuantity = $('#quantityWanted').val();
	        checkTheOffer(thisPrice, thisQuantity);
            return false;
        });
	});
})(jQuery);

function setError(errors) {

    jQuery('.youama-' + thisUser + '-window .youama-ajaxlogin-error')
        .text('');
    jQuery('.youama-' + thisUser + '-window .youama-ajaxlogin-error')
        .hide();

    for (var i in errors) {
        jQuery('.youama-' + thisUser + '-window .err-' + i)
            .text(errors[i]);
    }

   	jQuery('.youama-' + thisUser + '-window .youama-ajaxlogin-error')
        .fadeIn();
}

function checkTheOffer(thisPrice, thisQuantity)
{
	var errors = {}, hasErrors = false;

    if (!jQuery.isNumeric(thisPrice)) {
    	errors.price = "The price must be a number";
    	hasErrors = true;
    }

    if (!jQuery.isNumeric(thisQuantity)) {
    	errors.quantity = "The quantity must be a number";
    	hasErrors = true;
    }

    if (offeredProduct == 0) {
    	alert("Product not found!");
    	return false;
    }

	if (hasErrors)
	{
		setError(errors);
		return false;
	}

    makeTheOffer(thisPrice, thisQuantity);
}

function makeTheOffer(thisPrice, thisQuantity)
{
	params = {price: thisPrice, quantity: thisQuantity, id: offeredProduct};
	animateLoader(thisUser, 'start')	
	jQuery.post('/makeoffer/index/sendoffer', params, function (data){
		console.log(data);
		animateLoader(thisUser, 'stop')	
	}, 'json');
}

function animateLoader(windowName, step) {
    // Start
    if (step == 'start') {
        jQuery('.header-modal .youama-offer-ajaxlogin-loader').fadeIn();
        jQuery('.header-modal .youama-' + windowName + '-window')
            .animate({opacity : '0.4'});
    // Stop
    } else {
        jQuery('.header-modal .youama-offer-ajaxlogin-loader').fadeOut('normal', function() {
            jQuery('.header-modal .youama-' + windowName + '-window')
                .animate({opacity : '1'});
        });
    }
}

function animateShowWindow(windowName) {
	jQuery('.youama-'+ windowName +'-window').slideDown(300, 'easeInOutCirc');
}

function animateCloseWindow(windowName, quickly, closeParent) {
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
