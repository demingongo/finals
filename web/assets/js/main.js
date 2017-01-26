// angular app
//var myAngularApp = angular.module('AngularJSApp', ['ngSanitize']);

// fade in .fade-in-scroll-*
$(function () {

	$("body").css("margin-bottom", $("#footer").height()+30);

	$(window).resize(function () {
		$("body").css("margin-bottom", $("#footer").height()+30);
	});	

});