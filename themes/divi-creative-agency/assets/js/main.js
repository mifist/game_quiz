jQuery(document).ready(function($) {

	$(".btn-light-filter" ).click(function() {

		var filter = "product_cat-" + $(this).attr("data-category");
		// var filterType =  $("#tcf-filter-type").find(".active").attr('data-filter');
		console.log(filter);

		$(".btn-light-filter").removeClass( "active-filter" );
		$(this).addClass( "active-filter" );

		// product_cat-t-shirts
		if(filter == "product_cat-all"){
			$(".products").find(".product").show('3000');
		} else {
			$(".products").find(".product").hide();
			$(".products").find('.'+filter).show('3000');
		}

		// active

		// $("#loadMore").show();
	});


});


// loader animation with company logo
// jQuery(window).on('load', function () {
// 		var preloader = jQuery('.loaderArea'),
// 				loader = preloader.find('.loader');
// 		loader.fadeOut();
// 		preloader.delay(350).fadeOut('slow');
// });


function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}


// deals section rules

jQuery(function($){
	// check for cookie allow
		if(sessionStorage.userInfo){
				var userInfo = JSON.parse(sessionStorage.userInfo);
				console.log(userInfo);
				$("#tcf-deals-type").val(userInfo.type);
				$("#tcf-deals-country").val(userInfo.country);
				$("#tcf-deals-check").prop('checked', true);
				$("#tcf-deals-disclaimer-section").hide();
				displayAvailableDeals($, userInfo);
		}

	// display udpate button on change
	$("#tcf-deals-type, #tcf-deals-country").change(function () {
			if(!$("#tcf-deals-disclaimer-section").is(":visible")) {
				$("#update-info").show();
			}
	});

	// on click on accept + udpate button
	$("#disclaimer-btn, #update-info").click(function(){
		$(".Retail").show();
		$(".Institutionnel").show();
		$("#deals-alert").hide();
		$("#country-disclaimer").hide();
		$("#update-info").hide();
		if ($("#tcf-deals-check").prop("checked")) {
			$("#other-disclaimer").hide();
			var userInfo = {
				type : $("#tcf-deals-type").val(),
				country : $("#tcf-deals-country").val()
			};
			console.log(userInfo);
			displayAvailableDeals($, userInfo);

			// scroll to main div because of the hide
			// $('html, body').animate({
			// 		scrollTop: $("#deals").offset().top - 50
			// }, 900);

			$("#tcf-deals-disclaimer-section").hide('1000');


		} else {
			$("#country-disclaimer").hide();
			$("#other-disclaimer").show();
			$("#deals-alert").show('1000');
		}

	});
});



function  displayAvailableDeals($, userInfo) {
	var hidden = "";
	sessionStorage.userInfo = JSON.stringify(userInfo);
	if (userInfo.country === "other") {
		$("#country-disclaimer").show();
		$("#tcf-deals-table-section").hide('1000');
	} else {
		// normal check
		displayDeal($, userInfo);
	}
}

function displayDeal($, userInfo) {
	$("#tcf-deals-table-section").show('1000');
	if(userInfo.type === "institutional"){
		$(".Institutionnel").show();
		$(".Retail").hide();
		$("#tcf-deals-table-section").show();
	} else {
		$(".Institutionnel").hide();
		$(".Retail").show();
		$("#tcf-deals-table-section").show();
	}
}
