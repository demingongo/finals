<?php
/**
 * Smarty plugin
 */

/**
 * Smarty {captcha} function plugin
 * Type:     function<br>
 * Name:     filemanager<br>
 * Purpose:  filemanager in template
 *
 * Params:
 * <pre>
 * - hrefDir		- string 
 * - query      - array
 * </pre>
 *
 * @author   Demingongo-Litemo Stephane
 *
 * @param array                    $params   parameters
 *
 * @return string  The generated HTML code for displaying the captcha
 */

function smarty_function_filemanager($params, &$smarty)
{	
	//$request = $smarty->getContainer()->get('request');

	//exit($_SERVER['SCRIPT_NAME']);

	$id = "responsiveFilemanager_".rand(100000, 200000);

	$text="Open Filemanager";
	
	$filemanagerDir = (string)$params['hrefDir'];
	$pos = strpos($filemanagerDir, "?");
	if($pos != false){
		$filemanagerDir = substr($filemanagerDir, 0, $pos);
	}
	
	if(substr($filemanagerDir, -1) != "/" && substr($filemanagerDir, -1) != "\\"){
		$filemanagerDir .= "/";
	}
	
	$query="";

	$base_url = $smarty->getAssets()->getPackage()->getBasePath();
	
	//$base_url = $request->getBaseUrl();
	//exit($base_url);
	/*$base_url = "http";
	if($request->isSecure()){
		$base_url .= "s";
	}
	$base_url .= "://";
	$base_url .= $request->getHost().$request->getBaseUrl();*/

	$params['query']['base_url'] = $base_url;
	if(!empty($params['query']) && is_array($params['query']) && 
		!empty($params['query']['type']) && is_numeric($params['query']['type'])){
		if(empty($params['text'])){
			switch(intval($params['query']['type']))
			{
				case 1:
					$text="Select Image";
					break;
				case 2:
					$text="Select File";
					break;
				case 3:
					$text="Select Video";
					break;
			}
		}
	}
	else{
		if(!empty($params['query']) && !is_array($params['query'])){
			$params['query'] = array();
		}
		$params['query']['type'] = 0;
	}

	if(empty($params['query']['field_id'])){
		$params['query']['field_id'] = "fm_file";
	}

	$input_id = $params['query']['field_id'];

	$input="";
	$removeBtn="";
	$script="";
	if(intval($params['query']['type']) != 0 && 
		(!empty($params['input']) || (!empty($params['input']) && intval($params['input']) != 0))
	)
	{
		$input = "<input type='text' id='".$input_id."' name='".$input_id."' class='form-control' value='' />";
		$removeBtn_id = "removeFile_".rand(1, 15);
		$removeBtn = "<button id='".$removeBtn_id."' class='btn btn-danger'>Remove</button>";
		$script .="
		$('#".$removeBtn_id."').click(function(){
			$('#".$input_id."').val('');
		});
		";
	}
	
	foreach($params['query'] as $k => $v){
		$query=$query.$k."=".$v."&";
	}
	$query = substr($query, 0, -1);

	if(!empty($params['text'])){
		$text = $params['text'];
	}

	$href = $filemanagerDir."dialog_novice.php";
	if(!empty($query)){
		$href .= "?".$query;
	}

	$script.="$('#".$id."').fancybox({	
	'width'		: 900,
	'height'	: 600,
	'type'		: 'iframe',
    'autoScale' : false
    });";

	return "<div><div class='input-group'>".$input
		."<span class='input-group-btn'>
	<a href='".$href."' id='".$id."' class='btn btn-info iframe-btn' >".$text."</a>
	".$removeBtn."
	</span>
	</div>
	<script>
	".$script."
	</script>
	</div>";

	exit('smarty_function_filemanager: '.$href);
}

?>