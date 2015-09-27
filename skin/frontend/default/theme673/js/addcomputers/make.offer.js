var offeredProduct = 0, thisUser,
    errorMessages = {
        'has3Offers': 'You already made 3 offers for this product. You will be able to make another offer in 24 hours.',
        'productError': 'Something went wrong. Please try again or contact the site administrator.',
        'priceTooBig': 'We are glad that you are so generous but, your offer is bigger then our standard price.',
        'productNotInStock': 'We are sorry, this product is not in stock anymore.',
        'offerRejected': 'We are sorry but, your offer has been rejected.'
    };
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

        // Click on add to cart button
        $('.youama-add-tocart-button').on('click', function() {

            animateLoader(thisUser, 'start');
            jQuery.post('/makeoffer/index/addtocart', {}, function (result){

                // stopping both loaders (maybe the user is not logged in anymore)
                animateLoader('offer-user', 'stop');
                animateLoader('offer-guest', 'stop');
                
                // if failed show the error
                if (!result.success)
                {
                    if (result.error == 'notLoggedIn')
                    {
                        animateCloseWindow(thisUser, true, true);
                        animateShowWindow('offer-guest');
                    }
                    else
                    {
                        var errors = {};
                        if (typeof eval('errorMessages.' + result.error) != 'undefined')
                            error = eval('errorMessages.' + result.error);
                        else
                            error = errorMessages.productError;
                        errors.main = error;
                        setError(errors);
                    }
                }
                // if all ok --> got to CART!
                else
                {
                    animateCloseWindow(thisUser, false, true);
                    window.location.href = '/checkout/cart';
                }

                return false;
            }, 'json');


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
	animateLoader(thisUser, 'start');	
	jQuery.post('/makeoffer/index/sendoffer', params, function (result){

        // stopping both loaders (maybe the user is not logged in anymore)
        animateLoader('offer-user', 'stop');
        animateLoader('offer-guest', 'stop');
        
        // if failed show the error
        if (!result.success)
        {
            if (result.error == 'notLoggedIn')
            {
                animateCloseWindow(thisUser, true, true);
                animateShowWindow('offer-guest');
            }
            else
            {
                var errors = {};
                if (typeof eval('errorMessages.' + result.error) != 'undefined')
                    error = eval('errorMessages.' + result.error);
                else
                    error = errorMessages.productError;
                errors.main = error;
                setError(errors);
            }
        }
        // if all ok show the add to cart button
        else
        {
            jQuery('.youama-offer-user-window input').attr('disabled', 'disabled');
            jQuery('.youama-' + thisUser + '-window .youama-ajaxlogin-success').fadeIn();
            jQuery('.youama-makeoffer-button').hide();
            jQuery('.youama-add-tocart-button').show();
        }

        return false;
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

    jQuery('.youama-' + thisUser + '-window .youama-ajaxlogin-success').hide();
    jQuery('.youama-makeoffer-button').show();
    jQuery('.youama-add-tocart-button').hide();
    jQuery('.youama-window-content .input-fly input').val('');

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
