/** IMPORTANT!!! THIS FILE USES VENDOR LIBRARIES :
						
						- 'JQuery' version '1.11.2' or more // ref: https://jquery.com/
						- 'JQueryUI' //ref: 'https://jqueryui.com/'
						- 'Select2' version '4.0.1' ('select2-4.0.1') // ref: 'https://select2.github.io/'
						- 'Chosen' version '1.4.2' ('chosen_v1.4.2') // ref: 'https://harvesthq.github.io/chosen/'
  */


/*** BEGIN PROTOTYPES ***/

String.prototype.isEmpty = function() {
    return (this.length === 0 || !this.trim());
};


/*** END PROTOTYPES ***/


/*** BEGIN JQUERY EXTENDS ***/

jQuery.fn.extend({
    disable: function(state) {
        return this.each(function() {
            this.disabled = state;
        });
    }
});


/*** END JQUERY EXTENDS ***/


$(function () {

	
	/*** BEGIN NAVBAR ***/

	var pathname = window.location.pathname;
    var url = window.location.href;
    var navItems = $(".navbar div div ul").find("a");
    
    var string;
    var bool;
	
	//console.log(pathname);
	//console.log(navItems);
	//console.log(url); 
    navItems.each(
        function(index){
			if($(this).parent("li").hasClass("no-action")){
				return;
			}
           string = $(this).prop("href").toString();
		   //console.log(string);
           bool = url.indexOf(string);
		   bool2 = string.indexOf(pathname);
           if(bool >= 0 && bool2 >= 0){
               $(this).parent("li").addClass("active");
           }
           //console.log(index + ": " + $(this).prop("href")); 
        }
    ).click(function() {
		if($(this).parent("li").hasClass("no-action")){
			return;
		}
  		$(this).parent("li").addClass("active");
        url = $(this).prop("href").toString();
		
		var l = document.createElement("a");
    	l.href = url;		
		pathname = l.pathname;
    		
		//console.log(pathname);
		navItems.each(
    	    function(index){
				if($(this).parent("li").hasClass("no-action")){
					return;
				}
        	   string = $(this).prop("href").toString();
		   	//console.log(string);
           		bool = url.indexOf(string);
		   		bool2 = string.indexOf(pathname);
           		if(bool >= 0 && bool2 >= 0){
               		$(this).parent("li").addClass("active");
           		}
				else{
					$(this).parent("li").removeClass("active");
				}
           //console.log(index + ": " + $(this).prop("href")); 
        	}
    	);
	});

	// Highlight the top nav as scrolling occurs (used with ancre) // ref: 'http://www.w3schools.com/bootstrap/bootstrap_ref_js_scrollspy.asp'
	$('body').scrollspy({
		target: '.navbar-fixed-top'
	});

	// Closes the Responsive Menu on Menu Item Click
	$('.navbar-collapse ul li a[href!="#"]').click(function() {
		$('.navbar-toggle:visible').click();
	});
	
	
	$("#mainNav").affix({
		offset:{
			top:50,
		}
	});

	
	/*** END NAVBAR ***/
	

	/*** BEGIN FOOTER SELECT NAV ***/


	$( "#footernav" ).change(function() {
		//alert( "Handler for .change() called to "+$(this).val()+"." );
		$(location).attr('href', $(this).val());
	});


	/*** END FOOTER SELECT NAV ***/


	/*** BEGIN SCROLL ***/

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


	/*** END SCROLL ***/


	/*** BEGIN FORM-CONTROL ***/

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


	try{
		 $( "form[data-novice='form-control'] .chosen-select-submit" ).chosen().change( 
			function( event, data ) {
				$(this).closest("form").submit();
		 });
	}
	catch(err){
		//console.log('chosen() doesnt exists');
	}
	
	/*** END FORM-CONTROL ***/

	/*** BEGIN IMAGE ***/

	$("img[data-fallback-src]").bind('error', function() {
		  var imgSrc = $(this).attr('src');
		  var fallbackSrc = $(this).attr('data-fallback-src');
          if (imgSrc != fallbackSrc && typeof fallbackSrc === 'string' && !fallbackSrc.isEmpty()) {
			$(this).attr('src', fallbackSrc);
          }
    });

	/*** END IMAGE **/

});