<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoforms\Actions\MetaTagger;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
Class MetaTagger extends \GCore\Admin\Extensions\Chronoforms\Action{
	static $title = 'Meta Tagger';
	//static $setup = array('simple' => array('title' => 'Permissions'));
	static $group = array('joomla' => 'Joomla');
	static $platforms = array('joomla');

	var $defaults = array(
		'description' => 'Our Contact Page.',
		'robots' => 'index, follow',
		'generator' => 'Joomla! - Chronoforms!',
		'keywords' => '',
		'title' => '',
		'content' => ''
	);

	function execute(&$form, $action_id){
		$config =  $form->actions_config[$action_id];
		$config = new \GCore\Libs\Parameter($config);
		
		$mainframe = \JFactory::getApplication();
		//settings, vars
		$doc = \JFactory::getDocument();
		//description
		$doc->setDescription($config->get('description', 'Our Contact Page.'));
		//keywords
		$doc->setMetaData('keywords', $config->get('keywords', ''));
		//robots
		$doc->setMetaData('robots', $config->get('robots', 'index, follow'));
		//generator
		$doc->setMetaData('generator', $config->get('generator', 'Joomla! - Chronoforms!'));
		//title
		$title = $config->get('title', '');
		if(trim($title)){
			$doc->setTitle($title);
		}
		//custom
		if($config->get('content', '')){
			$list = explode("\n", trim($config->get('content', '')));
			foreach($list as $item){
				$fields_data = explode("=", $item);
				$doc->setMetaData(trim($fields_data[0]), trim($fields_data[1]));
			}
		}
	}

	public static function config(){
		echo \GCore\Helpers\Html::formStart('action_config meta_tagger_action_config', 'meta_tagger_action_config__XNX_');
		echo \GCore\Helpers\Html::formSecStart();
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][title]', array('type' => 'text', 'class' => 'L', 'label' => l_('CF_META_TAGGER_TITLE'), 'sublabel' => ''));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][keywords]', array('type' => 'text', 'class' => 'L', 'label' => l_('CF_META_TAGGER_KEYWORDS'), 'sublabel' => ''));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][generator]', array('type' => 'text', 'class' => 'L', 'label' => l_('CF_META_TAGGER_GENERATOR'), 'sublabel' => ''));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][description]', array('type' => 'text', 'class' => 'L', 'label' => l_('CF_META_TAGGER_DESC'), 'sublabel' => ''));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][robots]', array('type' => 'text', 'class' => 'L', 'label' => l_('CF_META_TAGGER_ROBOTS'), 'sublabel' => ''));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][content]', array('type' => 'textarea', 'class' => 'XL', 'rows' => 5, 'cols' => 60, 'label' => l_('CF_META_TAGGER_CONTENT'), 'sublabel' => l_('CF_META_TAGGER_CONTENT_DESC')));
		echo \GCore\Helpers\Html::formSecEnd();
		echo \GCore\Helpers\Html::formEnd();
	}
}