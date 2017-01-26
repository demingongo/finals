<?php
/**
 * Smarty plugin
 */

/**
 * Smarty stylesheets block plugin
 * Type:     block<br>
 * Name:     bootstrap_modal<br>
 * Purpose:  create modal
 *
 * @author Demingongo-Litemo Stephane
 *
 * @param array                    $params   parameters
 * @param string                   $content  contents of the block
 * @param Smarty_Internal_Template $template template object
 * @param boolean                  &$repeat  repeat flag
 *
 * @return  html bootstrap modal
 */

function smarty_block_bootstrap_modal($params, $content, &$smarty, &$repeat)
{
	// n'affiche que lors de la balise fermante
	if(!$repeat){
		$id = (isset($params['id']) && !empty($params['id'])) ? htmlspecialchars($params['id']) : "myModal".rand(1, 999);
		$title = (isset($params['title'])) ? $params['title'] : "";
		$class = (isset($params['class'])) ? " ".htmlspecialchars($params['class']) : "";
		$show_button = (isset($params['show_button'])) ? (bool)$params['show_button'] : true;
		$btn_title = (isset($params['btn_title'])) ? $params['btn_title'] : $title;
		$btn_class = (isset($params['btn_class'])) ? " ".htmlspecialchars($params['btn_class']) : " btn-primary";

		$modal_label = $title.'_'.$id;

		if(empty($title)){
			$title="&nbsp;";
		}

		if($show_button){
			$btn = '<button type="button" class="btn btn-bootstrap-modal'.$btn_class.'" data-toggle="modal" data-target="#'.$id.'">'.
						$btn_title.
					'</button>';
		}
		else{
			$btn = '';
		}
		
		$html = $btn.'<div class="modal'.$class.'" id="'.$id.'" tabindex="-1" role="dialog" aria-labelledby="'.$modal_label.'">'.
			'<div class="modal-dialog">'.
            '<div class="modal-content">'.
              '<div class="modal-header">'.
                '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>'.
                '<h4 id="'.$modal_label.'" class="modal-title">'.$title.'</h4>'.
              '</div>'.
              '<div class="modal-body">'.$content.'</div>'.
            '</div>'.
          '</div>'.
        '</div>';

		return $html;
	}
}
?>