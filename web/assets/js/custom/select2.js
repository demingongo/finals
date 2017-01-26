var select2_config = {
      'select.select2'           : {},
      'select.select2-deselect'  : {allowClear:true},
	  'select.select2-hide-search'  : {minimumResultsForSearch: Infinity},
	  'select.select2-classic'  : {theme: "classic"},
	  
	  'select.select2-deselect-hide-search'  : {allowClear:true,minimumResultsForSearch: Infinity},
	  'select.select2-deselect-classic'  : {allowClear:true,theme: "classic"},
	  'select.select2-hide-search-classic'  : {minimumResultsForSearch: Infinity,theme: "classic"},

	  'select.select2-tags'  : {tags:true},
	  'select.select2-tokenizer'  : {tags:true,tokenSeparators: [',', ' ']},
	  
	  'select.select2-tags-classic'  : {tags:true,theme: "classic"},
	  'select.select2-tokenizer-classic'  : {tags:true,tokenSeparators: [',', ' '],theme: "classic"},
    }
    for (var selector in select2_config) {
      $(selector).select2(select2_config[selector]);
    }