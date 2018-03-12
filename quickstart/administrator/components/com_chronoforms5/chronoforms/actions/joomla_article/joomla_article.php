<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoforms\Actions\JoomlaArticle;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
Class JoomlaArticle extends \GCore\Admin\Extensions\Chronoforms\Action{
	static $title = 'Joomla Article';
	//static $setup = array('simple' => array('title' => 'Permissions'));
	static $group = array('joomla' => 'Joomla');
	static $platforms = array('joomla');
	//var $events = array('success' => 0, 'fail' => 0);

	var $defaults = array(
		'title' => '',
		'fulltext' => '',
		'introtext' => '',
		'created_by_alias' => '',
		'state' => 0,
		'catid' => 0,
		'access' => 1,
		'sectionid' => 0
	);

	function execute(&$form, $action_id){
		$config =  $form->actions_config[$action_id];
		$config = new \GCore\Libs\Parameter($config);
		
		$mainframe = \JFactory::getApplication();
		//set data		
		$user = \GCore\Libs\Base::getUser();
		$article_data['created_by'] = $user['id'];
		$article_data['access'] = $config->get('access', 1);
		$article_data['created'] = date("Y-m-d H:i:s");
		$article_data['catid'] = $config->get('catid', '');
		$article_data['sectionid'] = $config->get('sectionid', 0);
		$article_data['state'] = $config->get('state', 0);
		$article_data['title'] = $form->data($config->get('title', ''));
		$article_data['fulltext'] = $form->data($config->get('fulltext', ''));
		$article_data['introtext'] = strlen($form->data($config->get('introtext', ''))) ? $form->data($config->get('introtext', '')) : '';
		//$article_data['created_by_alias'] = $form->data[$config->get('created_by_alias', '')];
		$article_data['language'] = '*';
		//alias
		$article_data['alias'] = \JFilterOutput::stringURLSafe($article_data['title']);
		
		//$article_data['id'] = null;
		
		\GCore\Libs\GModel::generateModel('CFJArticle', array(
			'tablename' => '#__content',
		));
		\GCore\Models\CFJArticle::getInstance()->save($article_data);
	}

	public static function config(){
		echo \GCore\Helpers\Html::formStart('action_config joomla_article_action_config', 'joomla_article_action_config__XNX_');
		echo \GCore\Helpers\Html::formSecStart();
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][title]', array('type' => 'text', 'label' => l_('CF_JOOMLA_ARTICLE_TITLE'), 'class' => 'M', 'sublabel' => l_('CF_JOOMLA_ARTICLE_TITLE_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][fulltext]', array('type' => 'text', 'label' => l_('CF_JOOMLA_ARTICLE_FULLTEXT'), 'class' => 'M', 'sublabel' => l_('CF_JOOMLA_ARTICLE_FULLTEXT_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][introtext]', array('type' => 'text', 'label' => l_('CF_JOOMLA_ARTICLE_INTROTEXT'), 'class' => 'M', 'sublabel' => l_('CF_JOOMLA_ARTICLE_INTROTEXT_DESC')));
		//echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][created_by_alias]', array('type' => 'text', 'label' => l_('CF_JOOMLA_ARTICLE_CREATEDBY_ALIAS'), 'class' => 'M', 'sublabel' => l_('CF_JOOMLA_ARTICLE_CREATEDBY_ALIAS_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][state]', array('type' => 'dropdown', 'label' => l_('CF_JOOMLA_ARTICLE_STATE'), 'values' => 0, 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'sublabel' => l_('CF_JOOMLA_ARTICLE_STATE_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][catid]', array('type' => 'text', 'label' => l_('CF_JOOMLA_ARTICLE_CATID'), 'value' => 0, 'sublabel' => l_('CF_JOOMLA_ARTICLE_CATID_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][access]', array('type' => 'text', 'label' => l_('CF_JOOMLA_ARTICLE_ACCESS'), 'value' => 1, 'sublabel' => l_('CF_JOOMLA_ARTICLE_ACCESS_DESC')));
		//echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][sectionid]', array('type' => 'text', 'label' => l_('CF_JOOMLA_ARTICLE_SECTIONID'), 'value' => 0, 'sublabel' => l_('CF_JOOMLA_ARTICLE_SECTIONID_DESC')));
		echo \GCore\Helpers\Html::formSecEnd();
		echo \GCore\Helpers\Html::formEnd();
	}
}