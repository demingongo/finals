// angular app
// var myAngularApp = angular.module('AngularJSApp', ['ngSanitize']);

// fade in .fade-in-scroll-*

// Disable function
jQuery.fn.extend({
    disable: function(state) {
        return this.each(function() {
            this.disabled = state;
        });
    }
});

$(function () {

	var noviceDataSubmitControl = 1;

	var handler = function(e){
		if(noviceDataSubmitControl == 0){
			noviceDataSubmitControl = 1;
			e.preventDefault();
			return false;
		}
	};
	
	$("form[data-novice='form-control']").submit(handler);

	$("form[data-novice='form-control']").on("click", "[data-novice-toggle='confirm']" ,function() {
		//alert('confirm');
		var attr = $(this).attr('type');
		// For some browsers, `attr` is undefined; for others,
		// `attr` is false.  Check for both.
		if (typeof attr !== typeof undefined && attr !== false && attr=="submit") {
			var text;
			text = $(this).attr('data-novice-text');
			if (typeof text === typeof undefined || text === false || $.trim(text) === "" ) {
				text = "Continue ?";
			}

			var r = confirm(text);			
			if(r == true) {
				noviceDataSubmitControl = 1;
			}
			else{
				noviceDataSubmitControl = 0;
			}
		}
	});

	/*$( "input[data-novice-toggle='confirm']" ).click(function() {
		//alert('confirm');
		var attr = $(this).attr('type');
		// For some browsers, `attr` is undefined; for others,
		// `attr` is false.  Check for both.
		if (typeof attr !== typeof undefined && attr !== false && attr=="submit") {
			var text;
			text = $(this).attr('data-novice-text');
			if (typeof text === typeof undefined || text === false || $.trim(text) === "" ) {
				text = "Continue ?";
			}

			var r = confirm(text);			
			if(r == true) {
				noviceDataSubmitControl = 1;
			}
			else{
				noviceDataSubmitControl = 0;
			}
		}
	});*/

	$("[data-novice='form-control'] [type=checkbox][data-novice-toggle='checkall']").click(function(){
		//alert('#checkAll');
		//var id = $(this.form).attr('id');
		//alert(id);
		//return;
		var v_boxes = $(this).closest("[data-novice='form-control']").find('input[type=checkbox]');//$(this.form).find('input[type=checkbox]');
		if($(this).is(":checked")){
			v_boxes.each(function(){
				$(this).prop("checked", true);
			});
		}
		else{
			v_boxes.each(function(){
				$(this).prop("checked", false);
			});
		}
	});

	$(".selectmenu").selectmenu();

	$('[data-toggle="tooltip"]').tooltip();

	$( "form[data-novice='form-control'] .selectmenu.selectmenu-submit" ).selectmenu({
       change: function( event, data ) {
		   //alert(data.item.value);
		   $(this).closest("form").submit();
       }
     });

	 $( "form[data-novice='form-control'] .chosen-select-submit" ).chosen().change( 
		function( event, data ) {
		   $(this).closest("form").submit();
       }	 
	 );

	$(".scroll-to-top").click(function(){
		$('html,body').animate({ scrollTop:0 },'slow');
		return false;
	});

	// hide .fade-in-scroll-*
	$(".fade-in-scroll").hide();
    
	$(window).scroll(function () {
		// set distance user needs to scroll before we start fadeIn
        if ($(this).scrollTop() > 100) {
			$(".fade-in-scroll").removeClass('hidden');
			$(".fade-in-scroll").fadeIn();
        } else {
			$(".fade-in-scroll").fadeOut();
        }
    });
});