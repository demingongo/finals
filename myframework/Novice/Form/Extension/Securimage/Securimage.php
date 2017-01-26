<?php

namespace Novice\Form\Extension\Securimage;

use Novice\Form\Form;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Novice\Form\Extension\Securimage\Field\SecurimageField,
	Novice\Form\Extension\Securimage\Validator\SecurimageValidator,
	Novice\Form\Validator\NotNullValidator;

use Novice\Form\Event\Event,
	Novice\Form\Event\FilterFormEvent,
	Novice\Form\Event\FilterRequestEvent,
	Novice\Form\FormEvents;

class Securimage extends \Securimage
{ 

	/** 
	 *inherit 
	 */
	public static function getCaptchaHtml($options = array())
    {
        static $javascript_init = false;

        if (!isset($options['securimage_path'])) {
            $docroot = (isset($_SERVER['DOCUMENT_ROOT'])) ? $_SERVER['DOCUMENT_ROOT'] : substr($_SERVER['SCRIPT_FILENAME'], 0, -strlen($_SERVER['SCRIPT_NAME']));
			$docroot = realpath($docroot);
			$r = new \ReflectionObject(new \Securimage());
			$securimage_path = str_replace($docroot, '', dirname($r->getFileName()));
        } else {
            $securimage_path = $options['securimage_path'];
        }

        $show_image_url    = (isset($options['show_image_url'])) ? $options['show_image_url'] : null;
        $image_id          = (isset($options['image_id'])) ? $options['image_id'] : 'captcha_image';
        $image_alt         = (isset($options['image_alt_text'])) ? $options['image_alt_text'] : 'CAPTCHA Image';
        $show_audio_btn    = (isset($options['show_audio_button'])) ? (bool)$options['show_audio_button'] : true;
        $disable_flash_fbk = (isset($options['disable_flash_fallback'])) ? (bool)$options['disable_flash_fallback'] : false;
        $show_refresh_btn  = (isset($options['show_refresh_button'])) ? (bool)$options['show_refresh_button'] : true;
        $refresh_icon_url  = (isset($options['refresh_icon_url'])) ? $options['refresh_icon_url'] : null;
        $audio_but_bg_col  = (isset($options['audio_button_bgcol'])) ? $options['audio_button_bgcol'] : '#ffffff';
        $audio_icon_url    = (isset($options['audio_icon_url'])) ? $options['audio_icon_url'] : null;
        $loading_icon_url  = (isset($options['loading_icon_url'])) ? $options['loading_icon_url'] : null;
        $icon_size         = (isset($options['icon_size'])) ? $options['icon_size'] : 32 ;
        $audio_play_url    = (isset($options['audio_play_url'])) ? $options['audio_play_url'] : null;
        $audio_swf_url     = (isset($options['audio_swf_url'])) ? $options['audio_swf_url'] : null;
        $show_input        = (isset($options['show_text_input'])) ? (bool)$options['show_text_input'] : true;
        $refresh_alt       = (isset($options['refresh_alt_text'])) ? $options['refresh_alt_text'] : 'Refresh Image';
        $refresh_title     = (isset($options['refresh_title_text'])) ? $options['refresh_title_text'] : 'Refresh Image';
        $input_text        = (isset($options['input_text'])) ? $options['input_text'] : 'Type the text:';
        $input_id          = (isset($options['input_id'])) ? $options['input_id'] : 'captcha_code';
        $input_name        = (isset($options['input_name'])) ? $options['input_name'] :  $input_id;
        $input_attrs       = (isset($options['input_attributes'])) ? $options['input_attributes'] : array();
        $image_attrs       = (isset($options['image_attributes'])) ? $options['image_attributes'] : array();
        $error_html        = (isset($options['error_html'])) ? $options['error_html'] : null;
        $namespace         = (isset($options['namespace'])) ? $options['namespace'] : '';

        $rand              = md5(uniqid($_SERVER['REMOTE_PORT'], true));
        $securimage_path   = rtrim($securimage_path, '/\\');
        $securimage_path   = str_replace('\\', '/', $securimage_path);

        $image_attr = '';
        if (!is_array($image_attrs)) $image_attrs = array();
        if (!isset($image_attrs['style'])) $image_attrs['style'] = 'float: left; padding-right: 5px';
        $image_attrs['id']  = $image_id;

        $show_path = $securimage_path . '/securimage_show.php?';
        if ($show_image_url) {
            if (parse_url($show_image_url, PHP_URL_QUERY)) {
                $show_path = "{$show_image_url}&";
            } else {
                $show_path = "{$show_image_url}?";
            }
        }
        if (!empty($namespace)) {
            $show_path .= sprintf('namespace=%s&amp;', $namespace);
        }
        $image_attrs['src'] = $show_path . $rand;

        $image_attrs['alt'] = $image_alt;

        foreach($image_attrs as $name => $val) {
            $image_attr .= sprintf('%s="%s" ', $name, htmlspecialchars($val));
        }

        $audio_obj = null;
		
		$html = '<div id="captcha_frame" class="bg-primary" style="border: 1px solid black;  width: '.(300+10).'px; padding: 4px; border-radius: 5px;">'; //start

        $html .= sprintf('<img %s class="img-rounded" />', $image_attr);

		$html .= '<div style="clear: both"></div>';

		$html .= '<div style="border: 0px solid black; max-width: 230px; display: inline-block;">'; //start_screen

		$html .= sprintf('<label for="%s" class=" small">%s</label> ',
                htmlspecialchars($input_id),
                htmlspecialchars($input_text));

        if (!empty($error_html)) {
            $html .= $error_html;
        }

        $input_attr = '';
        if (!is_array($input_attrs)) $input_attrs = array();
        $input_attrs['type'] = 'text';
        $input_attrs['name'] = $input_name;
        $input_attrs['id']   = $input_id;

        foreach($input_attrs as $name => $val) {
            $input_attr .= sprintf('%s="%s" ', $name, htmlspecialchars($val));
        }

        $html .= sprintf('<input %s class="form-control input-sm" />', $input_attr);

		$html .= '</div>'; //end_screen

		$html .= '<div class="pull-right" style="border: 0px solid blue; max-width: 230px; display: inline-block; padding: 5px; vertical-align: top;">'; //start_control

        if ($show_audio_btn) {
            $swf_path  = $securimage_path . '/securimage_play.swf';
            $play_path = $securimage_path . '/securimage_play.php?';
            $icon_path = $securimage_path . '/images/audio_icon.png';
            $load_path = $securimage_path . '/images/loading.png';
            $js_path   = $securimage_path . '/securimage.js';
            $audio_obj = $image_id . '_audioObj';

            if (!empty($audio_icon_url)) {
                $icon_path = $audio_icon_url;
            }

            if (!empty($loading_icon_url)) {
                $load_path = $loading_icon_url;
            }

            if (!empty($audio_play_url)) {
                if (parse_url($audio_play_url, PHP_URL_QUERY)) {
                    $play_path = "{$audio_play_url}&";
                } else {
                    $play_path = "{$audio_play_url}?";
                }
            }

            if (!empty($namespace)) {
                $play_path .= sprintf('namespace=%s&amp;', $namespace);
            }

            if (!empty($audio_swf_url)) {
                $swf_path = $audio_swf_url;
            }

            // html5 audio
            $html .= sprintf('<div id="%s_audio_div">', $image_id) . "\n" .
                     sprintf('<audio id="%s_audio" preload="none" style="display: none">', $image_id) . "\n";

            // check for existence and executability of LAME binary
            // prefer mp3 over wav by sourcing it first, if available
            if (is_executable(Securimage::$lame_binary_path)) {
                $html .= sprintf('<source id="%s_source_mp3" src="%sid=%s&amp;format=mp3" type="audio/mpeg">', $image_id, $play_path, uniqid()) . "\n";
            }

            // output wav source
            $html .= sprintf('<source id="%s_source_wav" src="%sid=%s" type="audio/wav">', $image_id, $play_path, uniqid()) . "\n";

            // flash audio button
            if (!$disable_flash_fbk) {
                $html .= sprintf('<object type="application/x-shockwave-flash" data="%s?bgcol=%s&amp;icon_file=%s&amp;audio_file=%s" height="%d" width="%d">',
                        htmlspecialchars($swf_path),
                        urlencode($audio_but_bg_col),
                        urlencode($icon_path),
                        urlencode(html_entity_decode($play_path)),
                        $icon_size, $icon_size
                );

                $html .= sprintf('<param name="movie" value="%s?bgcol=%s&amp;icon_file=%s&amp;audio_file=%s" />',
                        htmlspecialchars($swf_path),
                        urlencode($audio_but_bg_col),
                        urlencode($icon_path),
                        urlencode(html_entity_decode($play_path))
                );

                $html .= '</object><br />';
            }

            // html5 audio close
            $html .= "</audio>\n</div>\n";

            // html5 audio controls
            $html .= sprintf('<div id="%s_audio_controls">', $image_id) . "\n" .
                     sprintf('<a tabindex="-1" class="captcha_play_button" href="%sid=%s" onclick="return false">',
                             $play_path, uniqid()
                     ) . "\n" .
                     sprintf('<img class="captcha_play_image" height="%d" width="%d" src="%s" alt="Play CAPTCHA Audio" style="border: 0px">', $icon_size, $icon_size, htmlspecialchars($icon_path)) . "\n" .
                     sprintf('<img class="captcha_loading_image rotating" height="%d" width="%d" src="%s" alt="Loading audio" style="display: none">', $icon_size, $icon_size, htmlspecialchars($load_path)) . "\n" .
                     "</a>\n<noscript>Enable Javascript for audio controls</noscript>\n" .
                     "</div>\n";
        }

        if ($show_refresh_btn) {
            $icon_path = $securimage_path . '/images/refresh.png';
            if ($refresh_icon_url) {
                $icon_path = $refresh_icon_url;
            }
            $img_tag = sprintf('<img height="%d" width="%d" src="%s" alt="%s" onclick="this.blur()" style="border: 0px; vertical-align: bottom" />',
                               $icon_size, $icon_size, htmlspecialchars($icon_path), htmlspecialchars($refresh_alt));

            $html .= sprintf('<a tabindex="-1" style="border: 0" href="#" title="%s" onclick="%sdocument.getElementById(\'%s\').src = \'%s\' + Math.random(); this.blur(); return false">%s</a><br />',
                    htmlspecialchars($refresh_title),
                    ($audio_obj) ? "{$audio_obj}.refresh(); " : '',
                    $image_id,
                    $show_path,
                    $img_tag
            );
        }

        if ($show_audio_btn) {
            // html5 javascript
            if (!$javascript_init) {
                $html .= sprintf('<script type="text/javascript" src="%s"></script>', $js_path) . "\n";
                $javascript_init = true;
            }
            $html .= '<script type="text/javascript">' .
                     "$audio_obj = new SecurimageAudio({ audioElement: '{$image_id}_audio', controlsElement: '{$image_id}_audio_controls' });" .
                     "</script>\n";
        }

		$html .= '</div>'; //end_control

        /*$html .= '<div style="clear: both"></div>';

        $html .= sprintf('<label for="%s" class="control-label">%s</label> ',
                htmlspecialchars($input_id),
                htmlspecialchars($input_text));

        if (!empty($error_html)) {
            $html .= $error_html;
        }

        $input_attr = '';
        if (!is_array($input_attrs)) $input_attrs = array();
        $input_attrs['type'] = 'text';
        $input_attrs['name'] = $input_name;
        $input_attrs['id']   = $input_id;

        foreach($input_attrs as $name => $val) {
            $input_attr .= sprintf('%s="%s" ', $name, htmlspecialchars($val));
        }

        $html .= sprintf('<input %s class="form-control"/>', $input_attr);*/

		$html .= '</div>'; //end

        return $html;
    }
}