<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoforms\Actions\Email;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
Class Email extends \GCore\Admin\Extensions\Chronoforms\Action{
	static $title = 'Email';
	static $setup = array('simple' => array('title' => 'Email'));

	public static function config(){
		/*$doc = \GCore\Libs\Document::getInstance();
		$doc->_('jquery');
		$doc->addJsCode('
			jQuery(document).ready(function($){
				$("#email_template_loader__XNX_").on("click", function(){
					$.ajax({
						url: "'.r_("index.php?ext=chronoforms&act=action_fn&action_name=email&fn=generate_email&tvout=ajax").'",
						data: obj
					}).done(function(msg){
						$("#loading_gif").remove();
						var $newElem = $(msg);
						$newElem.find("td").attr("style", "width: auto !important");
						Element.replaceWith($newElem);
						Element = $newElem.css("width", "100%").removeAttr("id");
						addLinks(Element);
					});
				});
			});
		');
		*/
		echo \GCore\Helpers\Html::formStart('action_config email_action_config', 'email_action_config__XNX_');
		?>
		<ul class="nav nav-tabs">
			<li class="active"><a href="#basic-_XNX_" data-g-toggle="tab"><?php echo l_('CF_BASIC'); ?></a></li>
			<li><a href="#advanced-_XNX_" data-g-toggle="tab"><?php echo l_('CF_ADVANCED'); ?></a></li>
			<li><a href="#encryption-_XNX_" data-g-toggle="tab"><?php echo l_('CF_ENCRYPTION'); ?></a></li>
			<li><a href="#body-template-_XNX_" data-g-toggle="tab"><?php echo l_('CF_EMAIL_BODY_TEMPLATE'); ?></a></li>
		</ul>
		<div class="tab-content">
			<div id="basic-_XNX_" class="tab-pane active">
			<?php
			echo \GCore\Helpers\Html::formSecStart();
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][action_label]', array('type' => 'text', 'label' => l_('CF_ACTION_LABEL'), 'class' => 'XL', 'sublabel' => l_('CF_ACTION_LABEL_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][enabled]', array('type' => 'dropdown', 'label' => l_('CF_ENABLED'), 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'sublabel' => l_('CF_ENABLED_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][to]', array('type' => 'text', 'label' => l_('CF_TO'), 'class' => 'XL', 'sublabel' => l_('CF_TO_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][subject]', array('type' => 'text', 'label' => l_('CF_SUBJECT'), 'class' => 'XL', 'sublabel' => l_('CF_SUBJECT_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][from_name]', array('type' => 'text', 'label' => l_('CF_FROM_NAME'), 'class' => 'XL', 'sublabel' => l_('CF_FROM_NAME_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][from_email]', array('type' => 'text', 'label' => l_('CF_FROM_EMAIL'), 'class' => 'XL', 'sublabel' => l_('CF_FROM_EMAIL_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][email_type]', array('type' => 'dropdown', 'label' => l_('CF_EMAIL_TYPE'), 'options' => array('html' => l_('CF_HTML'), 'text' => l_('CF_TEXT')), 'sublabel' => l_('CF_EMAIL_TYPE_DESC')));
			//echo \GCore\Helpers\Html::formLine('email_template_loader', array('type' => 'custom', 'code' => '<input type="button" class="email_template_loader" id="email_template_loader__XNX_" value="'.l_('CF_GENERATE_TEMPLATE').'" />', 'sublabel' => l_('CF_GENERATE_TEMPLATE_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][template_generation]', array('type' => 'dropdown', 'label' => l_('CF_EMAIL_TEMPLATE_GENERATION'), 'values' => 0, 'options' => array(0 => l_('CF_CUSTOM'), 1 => l_('CF_AUTO')), 'sublabel' => l_('CF_EMAIL_TEMPLATE_GENERATION_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][load_editor]', array('type' => 'button', 'class' => 'btn btn-primary', 'value' => l_('CF_LOAD_EDITOR'), 'onclick' => 'toggleEditor(this, \'email_template__XNX_\');'));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][template]', array('type' => 'textarea', 'label' => l_('CF_EMAIL_TEMPLATE'), 'id' => 'email_template__XNX_', 'style' => 'width:auto;', 'rows' => 20, 'cols' => 70, 'sublabel' => l_('CF_EMAIL_TEMPLATE_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][attach]', array('type' => 'textarea', 'label' => l_('CF_ATTACHMENT_FILES'), 'rows' => 3, 'cols' => 70, 'sublabel' => l_('CF_ATTACHMENT_FILES_DESC')));
			echo \GCore\Helpers\Html::formSecEnd();
			?>
			</div>
			<div id="advanced-_XNX_" class="tab-pane">
			<?php
			echo \GCore\Helpers\Html::formSecStart();
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][dto]', array('type' => 'text', 'label' => l_('CF_DYNAMIC_TO'), 'class' => 'XL', 'sublabel' => l_('CF_DYNAMIC_TO_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][dsubject]', array('type' => 'text', 'label' => l_('CF_DYNAMIC_SUBJECT'), 'class' => 'XL', 'sublabel' => l_('CF_DYNAMIC_SUBJECT_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][reply_name]', array('type' => 'text', 'label' => l_('CF_REPLY_TO_NAME'), 'class' => 'XL', 'sublabel' => l_('CF_REPLY_TO_NAME_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][reply_email]', array('type' => 'text', 'label' => l_('CF_REPLY_TO_EMAIL'), 'class' => 'XL', 'sublabel' => l_('CF_REPLY_TO_EMAIL_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][dreply_name]', array('type' => 'text', 'label' => l_('CF_DYNAMIC_REPLY_TO_NAME'), 'class' => 'XL', 'sublabel' => l_('CF_DYNAMIC_REPLY_TO_NAME_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][dreply_email]', array('type' => 'text', 'label' => l_('CF_DYNAMIC_REPLY_TO_EMAIL'), 'class' => 'XL', 'sublabel' => l_('CF_DYNAMIC_REPLY_TO_EMAIL_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][dfrom_name]', array('type' => 'text', 'label' => l_('CF_DYNAMIC_FROM_NAME'), 'class' => 'XL', 'sublabel' => l_('CF_DYNAMIC_FROM_NAME_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][dfrom_email]', array('type' => 'text', 'label' => l_('CF_DYNAMIC_FROM_EMAIL'), 'class' => 'XL', 'sublabel' => l_('CF_DYNAMIC_FROM_EMAIL_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][cc]', array('type' => 'text', 'label' => l_('CF_CC'), 'class' => 'XL', 'sublabel' => l_('CF_CC_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][bcc]', array('type' => 'text', 'label' => l_('CF_BCC'), 'class' => 'XL', 'sublabel' => l_('CF_BCC_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][dcc]', array('type' => 'text', 'label' => l_('CF_DYNAMIC_CC'), 'class' => 'XL', 'sublabel' => l_('CF_DYNAMIC_CC_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][dbcc]', array('type' => 'text', 'label' => l_('CF_DYNAMIC_BCC'), 'class' => 'XL', 'sublabel' => l_('CF_DYNAMIC_BCC_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][append_ip_address]', array('type' => 'dropdown', 'label' => l_('CF_EMAIL_APPEND_IP_ADDRESS'), 'values' => 1, 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'sublabel' => l_('CF_EMAIL_APPEND_IP_ADDRESS_DESC')));
			echo \GCore\Helpers\Html::input('Form[extras][actions_config][_XNX_][__action_title__]', array('type' => 'hidden', 'value' => 'email'));
			echo \GCore\Helpers\Html::formSecEnd();
			?>
			</div>
			<div id="encryption-_XNX_" class="tab-pane">
			<?php
			echo \GCore\Helpers\Html::formSecStart();
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][encrypt_enabled]', array('type' => 'dropdown', 'label' => l_('CF_EMAIL_ENABLE_ENCRYPTION'), 'values' => 0, 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'sublabel' => l_('CF_EMAIL_ENABLE_ENCRYPTION_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][gpg_sec_key]', array('type' => 'text', 'label' => l_('CF_EMAIL_ENCRYPTION_KEY'), 'class' => 'XL', 'sublabel' => l_('CF_EMAIL_ENCRYPTION_KEY_DESC')));
			echo \GCore\Helpers\Html::formSecEnd();
			?>
			</div>
			<div id="body-template-_XNX_" class="tab-pane">
			<?php
			echo \GCore\Helpers\Html::formSecStart();
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][template_header]', array('type' => 'textarea', 'label' => l_('CF_EMAIL_TEMPLATE_HEADER'), 'rows' => 5, 'cols' => 70, 'sublabel' => l_('CF_EMAIL_TEMPLATE_HEADER_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][template_body]', array('type' => 'textarea', 'label' => l_('CF_EMAIL_TEMPLATE_BODY'), 'rows' => 5, 'cols' => 70, 'sublabel' => l_('CF_EMAIL_TEMPLATE_BODY_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][template_footer]', array('type' => 'textarea', 'label' => l_('CF_EMAIL_TEMPLATE_FOOTER'), 'rows' => 5, 'cols' => 70, 'sublabel' => l_('CF_EMAIL_TEMPLATE_FOOTER_DESC')));
			echo \GCore\Helpers\Html::formSecEnd();
			?>
			</div>
		</div>
		<?php
		echo \GCore\Helpers\Html::formEnd();
	}
	
	public static function config_check($data = array()){
		$diags = array();
		$diags[l_('CF_DIAG_ENABLED')] = !empty($data['enabled']);
		$diags[l_('CF_DIAG_TO_ADDRESS_SET')] = (!empty($data['to']) OR !empty($data['dto'])) ? true : false;
		$diags[l_('CF_DIAG_SUBJECT_SET')] = (!empty($data['subject']) OR !empty($data['dsubject'])) ? true : false;
		$diags[l_('CF_DIAG_FROMNAME_SET')] = (!empty($data['from_name']) OR !empty($data['dfrom_name'])) ? true : false;
		$diags[l_('CF_DIAG_FROMEMAIL_SET')] = (!empty($data['from_email']) OR !empty($data['dfrom_email'])) ? true : false;
		$diags[l_('CF_DIAG_TEMPLATE_SET')] = !empty($data['template']);
		return $diags;
	}

	function on_form_save(&$data, $action_id){
		if(!empty($data['content']) AND (empty($data['extras']['actions_config'][$action_id]['template']) OR !empty($data['extras']['actions_config'][$action_id]['template_generation']))){
			$data['extras']['actions_config'][$action_id]['template'] = $this->field_replacer($data, $action_id);
		}
	}

	function execute(&$form, $action_id){
		$config =  $form->actions_config[$action_id];
		$config = new \GCore\Libs\Parameter($config);
		ob_start();
		eval('?>'.$config->get('template', ''));
		$body = ob_get_clean();
		$others = array();
		//get recipient
		$tos = array();
		if(strlen(trim($config->get('to', '')))){
			$tos = explode(',',  \GCore\Libs\Str::replacer(trim($config->get('to', '')), $form->data));
		}
		if(strlen(trim($config->get('dto', '')))){
			$dtos = explode(',', trim($config->get('dto', '')));
			foreach($dtos as $dto){
				$d_email = explode(',', $form->data($dto));
				$tos = array_merge((array)$d_email, $tos);
			}
		}
		
		$ccs = array();
		if(strlen(trim($config->get('cc', '')))){
			$ccs = explode(',', \GCore\Libs\Str::replacer(trim($config->get('cc', '')), $form->data));
		}
		if(strlen(trim($config->get('dcc', '')))){
			$dccs = explode(',', trim($config->get('dcc', '')));
			foreach($dccs as $dcc){
				$d_email = explode(',', $form->data($dcc));
				$ccs = array_merge((array)$d_email, $ccs);
			}
		}
		$others['cc'] = $ccs;
		
		$bccs = array();
		if(strlen(trim($config->get('bcc', '')))){
			$bccs = explode(',', \GCore\Libs\Str::replacer(trim($config->get('bcc', '')), $form->data));
		}
		if(strlen(trim($config->get('dbcc', '')))){
			$dbccs = explode(',', trim($config->get('dbcc', '')));
			foreach($dbccs as $dbcc){
				$d_email = explode(',', $form->data($dbcc));
				$bccs = array_merge((array)$d_email, $bccs);
			}
		}
		$others['bcc'] = $bccs;
		//subject
		$subject = trim($config->get('subject', '')) ?  \GCore\Libs\Str::replacer($config->get('subject', ''), $form->data) : $form->data($config->get('dsubject', ''));
		//from
		$others['from_name'] = trim($config->get('from_name', '')) ? \GCore\Libs\Str::replacer($config->get('from_name', ''), $form->data) : $form->data($config->get('dfrom_name'), null);
		$others['from_email'] = trim($config->get('from_email', '')) ? \GCore\Libs\Str::replacer($config->get('from_email', ''), $form->data) : $form->data($config->get('dfrom_email'), null);
		//reply to
		$others['reply_name'] = trim($config->get('reply_name', '')) ? \GCore\Libs\Str::replacer($config->get('reply_name', ''), $form->data) : $form->data($config->get('dreply_name'), null);
		$others['reply_email'] = trim($config->get('reply_email', '')) ? \GCore\Libs\Str::replacer($config->get('reply_email', ''), $form->data) : $form->data($config->get('dreply_email'), null);
		$others['type'] = $config->get('email_type', 'html');
		
		$form->data['ip_address'] = $_SERVER['REMOTE_ADDR'];
		
		if($others['type'] == 'html'){
			if($config->get('append_ip_address', 1)){
				$body = $body."<br /><br />"."IP: {ip_address}";
			}
			$body = \GCore\Libs\Str::replacer($body, $form->data, array('replace_null' => true, 'nl2br' => true, 'repeater' => 'repeater'));
		}else{
			if($config->get('append_ip_address', 1)){
				$body = $body."\n\n"."IP: {ip_address}";
			}
			$body = \GCore\Libs\Str::replacer($body, $form->data, array('replace_null' => true, 'repeater' => 'repeater'));
		}

		//attach
		$attachments = array();
		if(strlen(trim($config->get('attach', '')))){
			ob_start();
			$attach_fields = eval('?>'.trim($config->get('attach', '')));
			ob_end_clean();
			if(is_array($attach_fields)){
				$attachs = array_keys($attach_fields);
				foreach($form->files as $name => $file){
					if(in_array($name, $attachs)){
						if(\GCore\Libs\Arr::is_assoc($file)){
							$attachments[] = array_merge($attach_fields[$name], array('path' => $file['path']));
						}else{
							foreach($file as $fi => $fv){
								//$attachments[] = $fv['path'];
								$attachments[] = array_merge($attach_fields[$name], array('path' => $fv['path']));
							}
						}
					}
				}
			}else{
				$attachs = explode(',', trim($config->get('attach', '')));
				foreach($form->files as $name => $file){
					if(in_array($name, $attachs)){
						if(\GCore\Libs\Arr::is_assoc($file)){
							$attachments[] = $file['path'];
						}else{
							foreach($file as $fi => $fv){
								$attachments[] = $fv['path'];
							}
						}
					}
				}
			}
		}
		//load global settings
		$settings = $form::_settings();
		if(!empty($settings['mail'])){
			if(!empty($settings['mail']['smtp']) AND empty($settings['mail']['mail_method'])){
				$settings['mail']['mail_method'] = 'smtp';
			}
			foreach($settings['mail'] as $k => $v){
				\GCore\Libs\Base::setConfig($k, $v);
			}
		}
		
		//encrypt the email
		if($config->get('encrypt_enabled', 0) == 1 AND class_exists('Crypt_GPG')){
			$mySecretKeyId = trim($config->get('gpg_sec_key', '')); //Add Encryption key here
			$gpg = new Crypt_GPG();
			$gpg->addEncryptKey($mySecretKeyId);
			$body = $gpg->encrypt($body);
		}

		$sent = \GCore\Libs\Mailer::send($tos, $subject, $body, $attachments, $others);
		if($sent){
			$form->debug[$action_id][self::$title][] = "An email with the details below was sent successfully:";
		}else{
			$form->debug[$action_id][self::$title][] = "An email with the details below could NOT be sent:";
		}
		$form->debug[$action_id][self::$title][] = "To:".implode(", ", $tos);
		$form->debug[$action_id][self::$title][] = "Subject:".$subject;
		$form->debug[$action_id][self::$title][] = "From name:".$others['from_name'];
		$form->debug[$action_id][self::$title][] = "From email:".$others['from_email'];
		$form->debug[$action_id][self::$title][] = "CC:".implode(", ", $ccs);
		$form->debug[$action_id][self::$title][] = "BCC:".implode(", ", $bccs);
		$form->debug[$action_id][self::$title][] = "Reply name:".$others['reply_name'];
		$form->debug[$action_id][self::$title][] = "Reply email:".$others['reply_email'];
		$form->debug[$action_id][self::$title][] = "Attachments:";
		$form->debug[$action_id][self::$title][] = $attachments;
		$form->debug[$action_id][self::$title][] = "Body:\n".$body;
	}

	function field_replacer($data, $action_id){
		$htmlcode = $data['content'];
		
		$email_template_header = trim($data['extras']['actions_config'][$action_id]['template_header']) ? $data['extras']['actions_config'][$action_id]['template_header'] : '<table>';
		$email_template_body = trim($data['extras']['actions_config'][$action_id]['template_body']) ? $data['extras']['actions_config'][$action_id]['template_body'] : '<tr><td>{label}</td><td>{{name}}</td></tr>'."\n";
		$email_template_footer = trim($data['extras']['actions_config'][$action_id]['template_footer']) ? $data['extras']['actions_config'][$action_id]['template_footer'] : '</table>';
		
		if(!empty($data['form_type'])){
			$html_string = $email_template_header;
			$html_string .= "\n";
			foreach($data['extras']['fields'] as $k => $field){
				if(!in_array($field['type'], array('button', 'submit', 'reset', 'captcha', 'multi', 'container'))){
					$field['label'] = (isset($field['label']['text']) ? $field['label']['text'] : $field['label']);
					$field['name'] = implode('.', explode('[', str_replace(']', '', str_replace('[]', '', $field['name']))));
					/*$html_string .= '<tr>';
					$html_string .= '<td>'.(!empty($field['label']['text']) ? $field['label']['text'] : $field['label']).'</td>';
					$html_string .= '<td>{'.str_replace('[]', '', $field['name']).'}</td>';
					$html_string .= '</tr>';
					$html_string .= "\n";*/
					$html_string .= \GCore\Libs\Str::replacer($email_template_body, $field);
				}
				if(!empty($field['inputs'])){
					foreach($field['inputs'] as $fn => $field_input){
						if(!in_array($field_input['type'], array('button', 'submit', 'reset', 'captcha', 'multi'))){
							$field_input['label'] = (isset($field_input['label']['text']) ? $field_input['label']['text'] : $field_input['label']);
							$field_input['name'] = implode('.', explode('[', str_replace(']', '', str_replace('[]', '', $field_input['name']))));
							/*$html_string .= '<tr>';
							$html_string .= '<td>'.(!empty($field_input['label']['text']) ? $field_input['label']['text'] : $field_input['label']).'</td>';
							$html_string .= '<td>{'.str_replace('[]', '', $field_input['name']).'}</td>';
							$html_string .= '</tr>';
							$html_string .= "\n";*/
							$html_string .= \GCore\Libs\Str::replacer($email_template_body, $field_input);
						}
					}
				}
			}
			$html_string .= $email_template_footer;
			return $html_string;
		}
		//find any style code in the email template and get it here
		preg_match_all('/<style(.*?)<\/style>/is', $htmlcode, $style_matches);
		if(isset($style_matches[0]) && !empty($style_matches[0])){
			foreach($style_matches[0] as $style_code){
				$htmlcode = str_replace($style_code, '', $htmlcode);
			}
		}
		//ob_start();
		/*eval( "?>".$htmlcode);*/
		$html_string = $htmlcode;//ob_get_clean();
		$usednames = array();
		//end fields names
		//text fields
		$pattern_input = '/<input([^>]*?)type=("|\')(text|password|hidden|file)("|\')([^>]*?)>/is';
		$matches = array();
		preg_match_all($pattern_input, $html_string, $matches);
		foreach($matches[0] as $match){
			$pattern_name = '/name=("|\')([^(>|"|\')]*?)("|\')/i';
			preg_match($pattern_name, $match, $matches_name);
			if(isset($matches_name[2]) && trim(str_replace('[]', '', $matches_name[2]))){
				$email_data_name = "{".str_replace('[]', '', $matches_name[2])."}";
				$email_data_name = str_replace(array('[', ']'), array('.', ''), $email_data_name);
				if(!in_array($email_data_name, $usednames)){
					$html_string = str_replace($match, $email_data_name, $html_string);
					$usednames[] = $email_data_name;
				}else{
					$html_string = str_replace($match, "", $html_string);
				}
			}else{
				//$html_string = str_replace($match, "{This_element_has_no_name_attribute}", $html_string);
				$html_string = str_replace($match, "", $html_string);
			}
		}
		//buttons
		$pattern_input = '/<input([^>]*?)type=("|\')(submit|button|reset|image)("|\')([^>]*?)>/is';
		$matches = array();
		preg_match_all($pattern_input, $html_string, $matches);
		foreach($matches[0] as $match){
			$pattern_name = '/name=("|\')([^(>|"|\')]*?)("|\')/i';
			preg_match($pattern_name, $match, $matches_name);
			if(isset($matches_name[2]) && trim(str_replace('[]', '', $matches_name[2]))){
				$email_data_name = "";
				if(!in_array($email_data_name, $usednames)){
					$html_string = str_replace($match, $email_data_name, $html_string);
					$usednames[] = $email_data_name;
				}else{
					$html_string = str_replace($match, "", $html_string);
				}
			}else{
				//$html_string = str_replace($match, "{This_element_has_no_name_attribute}", $html_string);
				$html_string = str_replace($match, "", $html_string);
			}
		}
		//checkboxes or radios fields
		$pattern_input = '/<input([^>]*?)type=("|\')(checkbox|radio)("|\')([^>]*?)>/is';
		$matches = array();
		$check_radio_idslist = array();
		preg_match_all($pattern_input, $html_string, $matches);
		foreach($matches[0] as $match){
			$pattern_id = '/id=("|\')([^(>|"|\')]*?)("|\')/i';
			$pattern_name = '/name=("|\')([^(>|"|\')]*?)("|\')/i';
			preg_match($pattern_name, $match, $matches_name);
			preg_match($pattern_id, $match, $matches_id);
			if(isset($matches_name[2]) && trim(str_replace('[]', '', $matches_name[2]))){
				$check_radio_idslist[] = $matches_id[2];
				$email_data_name = "{".str_replace('[]', '', $matches_name[2])."}";
				$email_data_name = str_replace(array('[', ']'), array('.', ''), $email_data_name);
				if(!in_array($email_data_name, $usednames)){
					$html_string = str_replace($match, $email_data_name, $html_string);
					$usednames[] = $email_data_name;
				}else{
					$html_string = str_replace($match, "", $html_string);
				}
			}else{
				//$html_string = str_replace($match, "{This_element_has_no_name_attribute}", $html_string);
				$html_string = str_replace($match, "", $html_string);
			}
		}
		//radios-checks labels
		$pattern_label = '/<label([^>]*?)for=("|\')('.implode("|", $check_radio_idslist).')("|\')([^>]*?)>(.*?)<\/label>/is';
		$matches = array();
		preg_match_all($pattern_label, $html_string, $matches);
		foreach($matches[0] as $match){
			$html_string = str_replace($match, "", $html_string);
		}
		//textarea fields
		$pattern_textarea = '/<textarea([^>]*?)>(.*?)<\/textarea>/is';
		$matches = array();
		preg_match_all($pattern_textarea, $html_string, $matches);
		$namematch = '';
		foreach($matches[0] as $match){
			$pattern_name = '/name=("|\')([^(>|"|\')]*?)("|\')/i';
			preg_match($pattern_name, $match, $matches_name);
			if(isset($matches_name[2]) && trim(str_replace('[]', '', $matches_name[2]))){
				$email_data_name = "{".str_replace('[]', '', $matches_name[2])."}";
				$email_data_name = str_replace(array('[', ']'), array('.', ''), $email_data_name);
				if(!in_array($email_data_name, $usednames)){
					$html_string = str_replace($match, $email_data_name, $html_string);
					$usednames[] = $email_data_name;
				}else{
					$html_string = str_replace($match, "", $html_string);
				}
			}else{
				//$html_string = str_replace($match, "{This_element_has_no_name_attribute}", $html_string);
				$html_string = str_replace($match, "", $html_string);
			}
		}
		//select boxes
		$pattern_select = '/<select(.*?)select>/is';
		$matches = array();
		preg_match_all($pattern_select, $html_string, $matches);

		foreach($matches[0] as $match){
			$selectmatch = $match;
			$pattern_select2 = '/<select([^>]*?)>/is';
			preg_match_all($pattern_select2, $match, $matches2);
			$pattern_name = '/name=("|\')([^(>|"|\')]*?)("|\')/i';
			preg_match($pattern_name, $matches2[0][0], $matches_name);
			if(isset($matches_name[2]) && trim(str_replace('[]', '', $matches_name[2]))){
				$email_data_name = "{".str_replace('[]', '', $matches_name[2])."}";
				$email_data_name = str_replace(array('[', ']'), array('.', ''), $email_data_name);
				if(!in_array($email_data_name, $usednames)){
					$html_string = str_replace($match, $email_data_name, $html_string);
					$usednames[] = $email_data_name;
				}else{
					$html_string = str_replace($match, "", $html_string);
				}
			}else{
				//$html_string = str_replace($match, "{This_element_has_no_name_attribute}", $html_string);
				$html_string = str_replace($match, "", $html_string);
			}
		}
		return $html_string;

	}
}