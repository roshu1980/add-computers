function dailydeal_slider(prefix, from, to, min_value, max_value) {
	
	from = parseFloat(from);
	to = parseFloat(to);
	max_value = parseFloat(max_value);
	min_value = parseFloat(min_value);
	
	var slider = $(prefix); 
	//var allowedVals = new Array(11);

   return new Control.Slider(slider.select('.handle'), slider, {
        range: $R(min_value, max_value),
        sliderValue: [from, to],
        restricted: true,
        //values: allowedVals,
        
        onChange: function (values){
			
          this.onSlide(values);
		  //alert(removeParam('abc', window.location.href)); alert(document.URL);
			setLocation(removeParam('day', removeParam('day', window.location.href, Math.round(values[1]), 'to'), Math.round(values[0]), 'from'));
        },
        onSlide: function(values) {
      	
			var fromValue = Math.round(values[0]);
			var toValue = Math.round(values[1]);
			
			if ($(prefix + '-from-slider')) {
				$(prefix + '-from-slider').update(fromValue);
				$(prefix + '-to-slider').update(toValue);
			}	  
        }
		
      });
    
}


function removeParam(key, sourceURL, value, key) {
    var rtn = sourceURL.split("?")[0],
        param,
        params_arr = [],
        queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
	var hasPram = false;
    if (queryString !== "") {
        params_arr = queryString.split("&");
        for (var i = params_arr.length - 1; i >= 0; i -= 1) {
            param = params_arr[i].split("=")[0];
            if (param === key) {
				hasPram = true;
				params_arr[i] = key + '=' + value;
                //params_arr.splice(i, 1);
            }
        }
		
		if(!hasPram){
			params_arr[params_arr.length] = key + '=' + value;
		}
		
        rtn = rtn + "?" + params_arr.join("&");
    }else{
		params_arr[0] = key + '=' + value;
		rtn = rtn + "?" + params_arr.join("&");
	}
    return rtn;
}

function runsliderdeal(){
	$$('.dailydeal-slider-param').each(function (item) {
		param = item.value.split(',');
		// alert(param[2]);
		dailydeal_slider(param[0], param[1], param[2], param[3], param[4]);
	});
}

function ClockTimer()
{
	$j.each($j(".clocktimer", $j('.col-main')), function(i, vl){
	
		var objper = $j(vl);	
		var currentSeconds1 = objper.attr('value');
		mySeconds = currentSeconds1 - 1;
		if(mySeconds < 0) {
			objper.html('left time');	
			return;
		}
		
		objper.attr('value', mySeconds);
		
		var currentdays = Math.floor(mySeconds/86400);
		var remainiday = mySeconds%86400;
		var currentHours = Math.floor(remainiday/3600);
		var myHours = remainiday%3600;
		var currentMinutes = Math.floor(myHours/60);
		var currentSeconds = myHours%60;

		currentHours = (currentHours < 10? '0' : '') + currentHours; 
		currentMinutes = (currentMinutes < 10? '0' : '') + currentMinutes;
		currentSeconds = (currentSeconds < 10? '0' : '') + currentSeconds;
		//var currentTimeString = '<span><span class="dailydeal_bg">' + Math.floor(currentdays/10) + '</span><span class="dailydeal_bg">' + (currentdays%10) + '</span><br/>' + dailydealDay + '</span><span class="dailydeal_space">:</span><span><span class="dailydeal_bg">' + Math.floor(currentHours/10) + '</span><span class="dailydeal_bg">' + (currentHours%10) + '</span><br/>' + dailydealHour + '</span><span class="dailydeal_space">:</span><span><span class="dailydeal_bg">' + Math.floor(currentMinutes/10) + '</span><span class="dailydeal_bg">' + (currentMinutes%10) + '</span><br/>' + dailydealMin + '</span><span class="dailydeal_space">:</span><span><span class="dailydeal_bg">' + Math.floor(currentSeconds/10) + '</span><span class="dailydeal_bg">' + (currentSeconds%10) + '</span><br/>' + dailydealSecs + '</span>';
		var currentTimeString = '<span><span class="dailydeal_bg"><span>' + currentdays + '</span></span><br/>' + dailydealDay + '</span><span><span class="dailydeal_bg"><span>' + currentHours + '</span></span><br/>' + dailydealHour + '</span><span><span class="dailydeal_bg"><span>' + currentMinutes + '</span></span><br/>' + dailydealMin + '</span><span><span class="dailydeal_bg"><span>' + currentSeconds + '</span></span><br/>' + dailydealSecs + '</span>';
		objper.html(currentTimeString);	
	
	});

	$j.each($j(".clocktimer", $j('.sidebar')), function(i, vl){
	
		var objper = $j(vl);	
		var currentSeconds1 = objper.attr('value');
		mySeconds = currentSeconds1 - 1;
		if(mySeconds < 0) {
			objper.html('left time');	
			return;
		}
		
		objper.attr('value', mySeconds);
		
		var currentdays = Math.floor(mySeconds/86400);
		var remainiday = mySeconds%86400;
		var currentHours = Math.floor(remainiday/3600);
		var myHours = remainiday%3600;
		var currentMinutes = Math.floor(myHours/60);
		var currentSeconds = myHours%60;

		currentHours = (currentHours < 10? '0' : '') + currentHours; 
		currentMinutes = (currentMinutes < 10? '0' : '') + currentMinutes;
		currentSeconds = (currentSeconds < 10? '0' : '') + currentSeconds;
		var currentTimeString = '<span class="dailydeal_bg_sibar">' + currentdays + ' <span class="textoftimedeal">' + dailydealSidebarDay + '</span></span> : <span class="dailydeal_bg_sibar">' + currentHours + '<span class="textoftimedeal">' + dailydealSidebarHour + '</span></span> : <span class="dailydeal_bg_sibar">' + currentMinutes +  '<span class="textoftimedeal">' + dailydealSidebarMin + '</span></span> : <span class="dailydeal_bg_sibar">' + currentSeconds +  '<span class="textoftimedeal">' + dailydealSidebarSecs + '</span></span>';
		objper.html(currentTimeString);	
	
	});

}
var dailydealDay = 'Days';
var dailydealHour = 'Hours';
var dailydealMin = 'Mins';
var dailydealSecs = 'Secs';

var dailydealSidebarDay = 'Days';
var dailydealSidebarHour = 'h';
var dailydealSidebarMin = 'm';
var dailydealSidebarSecs = 's';

$j(document).ready(function()
{
	// ClockTimer();
	setInterval('ClockTimer()', 1000);
});