$(function() {

$(window).resize(function() {
});


if($(window).width() > 463){
	tinymceActivate();
}

function tinymceActivate(){

var tinymce_base_url = $("[data-tinymce-base-url]:first").attr('data-tinymce-base-url');

if(typeof tinymce_base_url == 'undefined'){
	tinymce_base_url = "";
}

tinymce.init({
    selector: ".tinymce" ,
	width: 600 ,
	language : 'fr_FR' ,
	 plugins: [
		"pagebreak",
		"textcolor",
		"colorpicker",
		"nonbreaking",
        "advlist autolink lists link image charmap print preview ",
        "searchreplace visualblocks code fullscreen",
        "insertdatetime media table contextmenu paste",
		"placeholder"
    ],
	image_list: [
		{title: 'Image-1', value: tinymce_base_url+'/images/logo.png'}
	] ,
	image_class_list: [
        {title: 'None', value: ''},
        {title: 'Dog', value: 'dog'},
        {title: 'Cat', value: 'cat'}
    ],
    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | forecolor backcolor ",
    pagebreak_separator: "<!-- pagebreak -->" ,
	nonbreaking_force_tab: true , //enables you to force TinyMCE to insert three &nbsp; entities when the user presses the tab key	
	link_list: [
        {title: 'Home', value: 'index'},
        {title: 'Category Level 1', menu: [
            {title: 'C1 Page 1', value: '/c1/foo'},
            {title: 'C1 Page 2', value: '/c1/bar'},
            {title: 'Category Level 2', menu: [
              {title: 'C2 Page 1', value: '/c1/c2/foo'},
              {title: 'C2 Page 2', value: '/c1/c2/bar'}
           ]}
        ]}
    ] ,
	target_list: [
        {title: 'n/a', value: ''},
        {title: 'Meme page', value: '_self'},
        {title: 'Nouvelle page', value: '_blank'}
	],
	rel_list: [
		{title: 'n/a', value: ''},
        {title: 'Lightbox', value: 'lightbox'},
        {title: 'Bookmark', value: 'bookmark'}
    ],
	relative_urls : false,
	remove_script_host : true,
	document_base_url : tinymce_base_url,
	convert_urls : true
 });

}
});