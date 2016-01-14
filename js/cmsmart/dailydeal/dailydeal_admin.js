function updateProductName(){
	$('product_name').value = $('newproduct_name').value;
	$('dailydeal_tabs_form_section').addClassName('active');
	$('dailydeal_tabs_options_section').removeClassName('active');
	$('dailydeal_tabs_form_section_content').style.display="";
	$('dailydeal_tabs_options_section_content').style.display="none";
}
