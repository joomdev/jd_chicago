<?php
/* @copyright:ChronoEngine.com @license:GPLv2 */
defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or define("GCORE_SITE", "front");
jimport('cegcore.joomla_gcloader');
if(!class_exists('JoomlaGCLoader')){
	JError::raiseWarning(100, "Please download the CEGCore framework from www.chronoengine.com then install it using the 'Extensions Manager'");
	return;
}

class PlgContentChronoforms5 extends JPlugin{

	public function onContentPrepare($context, &$row, &$params, $page = 0){
		$regex = '#{chronoforms5}(.*?){/chronoforms5}#s';
		if(isset($row->text)){
			preg_match_all($regex, $row->text, $matches);
			if(!empty($matches[1][0])){
				$chrono_data = $matches[1];
				foreach($chrono_data as $i => $match){
					$item_output = self::render_item($match);
					$row->text = str_replace($matches[0][$i], $item_output, $row->text);
				}
			}
		}else{
			$row->text = '';
		}
		return true;
	}

	public function render_item($match){
		$return = '';
		ob_start();
		$chronoforms5_setup = function() use($match){
			$mainframe = \JFactory::getApplication();
			parse_str($match, $params);
			foreach($params as $pk => $pv){
				\GCore\Libs\Request::set($pk, $pv);
			}
			$params_keys = array_keys($params);
			$formname = $params_keys[0];
			$chronoform = GCore\Libs\Request::data('chronoform', '');
			$event = GCore\Libs\Request::data('event', '');
			if(!empty($event)){
				if($formname != $chronoform){
					$event = 'load';
				}
			}
			return array('chronoform' => $formname, 'event' => $event);
		};

		$output = new JoomlaGCLoader('front', 'chronoforms5', 'chronoforms', $chronoforms5_setup, array('controller' => '', 'action' => ''));
		$return = ob_get_clean();
		return $return;
	}

}
?>