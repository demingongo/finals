<?php

namespace Novice\Form\Extension\Filemanager;

use Novice\Form\Form;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Novice\Form\Extension\Securimage\Field\SecurimageField,
	Novice\Form\Extension\Securimage\Validator\SecurimageValidator,
	Novice\Form\Validator\NotNullValidator;

use Novice\Form\Event\Event,
	Novice\Form\Event\FilterFormEvent,
	Novice\Form\Event\FilterRequestEvent,
	Novice\Form\FormEvents;

class Filemanager
{ 

	public static function getHtml($options = array())
    {
        static $javascript_init = false;

        if (!isset($options['filemanager_path'])) {
            $docroot = (isset($_SERVER['DOCUMENT_ROOT'])) ? $_SERVER['DOCUMENT_ROOT'] : substr($_SERVER['SCRIPT_FILENAME'], 0, -strlen($_SERVER['SCRIPT_NAME']));
            $docroot = realpath($docroot);
            $fmpath  = dirname(__FILE__);
            $filemanager_path = str_replace($docroot, '', $fmpath);
        } else {
            $filemanager_path = $options['filemanager_path'];
        }

		$base_url			   = (isset($options['base_url'])) ? $options['base_url'] : "/";
        $show_input        = (isset($options['show_input'])) ? (bool)$options['show_input'] : true;
        $text_open_btn			   = (isset($options['text_open_btn'])) ? $options['text_open_btn'] : '';
		$show_remove_btn			   = (isset($options['show_remove_btn'])) ? (bool)$options['show_remove_btn'] : true;
		$text_remove_btn			   = (isset($options['text_remove_btn'])) ? $options['text_remove_btn'] : 'Remove';
		$show_input			   = (isset($options['show_input'])) ? (bool)$options['show_input'] : true;
        $input_id          = (isset($options['input_id'])) ? $options['input_id'] : 'filemanager';
        $input_name        = (isset($options['input_name'])) ? $options['input_name'] :  $input_id;
        $input_attrs       = (isset($options['input_attributes'])) ? $options['input_attributes'] : array();
		$value		   = (isset($options['value'])) ? $options['value'] : "";
		
		$query = "";
		$arrayQ = array();

		$arrayQ['base_url']		   = $base_url;
		$arrayQ['type']			   = (isset($options['type'])) ? $options['type'] : 0;
		$arrayQ['fldr']			   = (isset($options['fldr'])) ? $options['fldr'] : "";
		$arrayQ['sort_by']		   = (isset($options['sort_by'])) ? $options['sort_by'] : "";
		$arrayQ['descending']	   = (isset($options['descending'])) ? $options['descending'] : "0";
		$arrayQ['lang']		       = (isset($options['lang'])) ? $options['lang'] : "en_EN";
		$arrayQ['relative_url']	   = (isset($options['relative_url'])) ? $options['relative_url'] : "0";
		$arrayQ['popup']		   = (isset($options['popup'])) ? $options['popup'] : "0";
		$arrayQ['akey']			   = (isset($options['akey'])) ? $options['akey'] : "";

		if($arrayQ['type'] != 0){
			$arrayQ['field_id'] = $input_id;
		}

		if(empty($text_open_btn)){
			switch(intval($arrayQ['type']))
			{
				case 1:
					$text_open_btn="Select Image";
					break;
				case 2:
					$text_open_btn="Select File";
					break;
				case 3:
					$text_open_btn="Select Video";
					break;
				default:
					$text_open_btn="Open Filemanager";
			}
		}

		foreach($arrayQ as $k => $v){
			if(empty($v) || (is_string($v) && $v == "0")){
				continue;
			}
			$query=$query.$k."=".$v."&";
		}
		$query = substr($query, 0, -1);

        $filemanager_path   = rtrim($filemanager_path, '/\\');
        $filemanager_path   = str_replace('\\', '/', $filemanager_path);

        $href = htmlspecialchars($base_url.$filemanager_path . '/dialog_novice.php?' . $query);
        /*if ($show_image_url) {
            if (parse_url($show_image_url, PHP_URL_QUERY)) {
                $show_path = "{$show_image_url}&";
            } else {
                $show_path = "{$show_image_url}?";
            }
        }*/
		
		$html = '<div class="input-group">'; //start input-group

		//input
		if($show_input && $arrayQ['type'] != 0){
			$input = '<input type="text" id="'.$input_id.'" name="'.$input_name.'" class="form-control" value="'.$value.'" ';

			$forbidden = array('type','id','name','class');			
			foreach($forbidden as $f){
				if(in_array( $f , array_keys($input_attrs) )){
					unset($input_attrs[$f]);
				}
			}
			$attrs="";
			foreach($input_attrs as $k => $v){
				$attrs=$attrs.$k.'="'.$v.'" ';
			}

			$input .= $attrs.' />';

			$html .= $input;
		}

		$rand = md5(uniqid($_SERVER['REMOTE_PORT'], true));

		$fileManager_id = "responsiveFilemanager_".$rand;

        $html .= '<span class="input-group-btn">
	<a href="'.$href.'" id="'.$fileManager_id.'" class="btn btn-info iframe-btn" >'.$text_open_btn.'</a>';

		//script_open_btn
		$script ="$('#".$fileManager_id."').fancybox({	
	'width'		: 900,
	'height'	: 600,
	'type'		: 'iframe',
    'autoScale' : false
    });";

		//removeBtn
		if($show_remove_btn && $arrayQ['type'] != 0){
			$removeBtn_id = "removeFile_".$rand;
			$removeBtn = "<button type='button' id='".$removeBtn_id."' class='btn btn-danger'>".$text_remove_btn."</button>";
			$script .="
		$('#".$removeBtn_id."').click(function(){
			$('#".$input_id."').val('');
		});
		";
			$html .= $removeBtn;
		}

		$html .= '</span>';

		$html .= '</div>'; //end input-group

		$html .= '<script>';

		$html .=  $script;

		$html .= '</script>';

		/*dump($href);
		exit(__METHOD__);*/

        return $html;
    }

	public static function getPath($options = array())
    {
		if (!isset($options['filemanager_path'])) {
            $docroot = (isset($_SERVER['DOCUMENT_ROOT'])) ? $_SERVER['DOCUMENT_ROOT'] : substr($_SERVER['SCRIPT_FILENAME'], 0, -strlen($_SERVER['SCRIPT_NAME']));
            $docroot = realpath($docroot);
            $fmpath  = dirname(__FILE__);
            $filemanager_path = str_replace($docroot, '', $fmpath);
        } else {
            $filemanager_path = $options['filemanager_path'];
        }

		$query = "";
		$arrayQ = array();

		$base_url			   = (isset($options['base_url'])) ? $options['base_url'] : "/";
		$arrayQ['base_url']		   = $base_url;
		$arrayQ['type']			   = (isset($options['type'])) ? $options['type'] : 0;
		$arrayQ['fldr']			   = (isset($options['fldr'])) ? $options['fldr'] : "";
		$arrayQ['sort_by']		   = (isset($options['sort_by'])) ? $options['sort_by'] : "";
		$arrayQ['descending']	   = (isset($options['descending'])) ? $options['descending'] : "0";
		$arrayQ['lang']		       = (isset($options['lang'])) ? $options['lang'] : "en_EN";
		$arrayQ['relative_url']	   = (isset($options['relative_url'])) ? $options['relative_url'] : "0";
		$arrayQ['popup']		   = (isset($options['popup'])) ? $options['popup'] : "0";
		$arrayQ['akey']			   = (isset($options['akey'])) ? $options['akey'] : "";

		foreach($arrayQ as $k => $v){
			if(empty($v) || (is_string($v) && $v == "0")){
				continue;
			}
			$query=$query.$k."=".$v."&";
		}
		$query = substr($query, 0, -1);

        $filemanager_path   = rtrim($filemanager_path, '/\\');
        $filemanager_path   = str_replace('\\', '/', $filemanager_path);

        return $href = $base_url.$filemanager_path . '/dialog_novice.php?' . $query;
	}
}