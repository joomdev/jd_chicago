<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoforms\Actions\DbSave;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
Class DbSave extends \GCore\Admin\Extensions\Chronoforms\Action{
	static $title = 'DB Save';
	static $setup = array('simple' => array('title' => 'Data Save'));
	static $group = array('data_management' => 'Data Management');

	var $defaults = array(
		'tablename' => '',
		'enabled' => 1,
		'model_id' => 'Data',
		'save_under_modelid' => 0,
		'force_save' => 0,
		'multi_save' => 0,
		'ndb_enable' => 0,
		'ndb_driver' => 'mysql',
		'ndb_host' => 'localhost',
		'ndb_user' => '',
		'ndb_password' => '',
		'ndb_database' => '',
		'ndb_table_name' => '',
		'ndb_prefix' => 'jos_'
	);

	function execute(&$form, $action_id){
		$config =  $form->actions_config[$action_id];
		$config = new \GCore\Libs\Parameter($config);

		$model_id = $config->get('model_id', 'Data');
		$model_id = empty($model_id) ? 'Data' : $model_id;
		
		if($config->get('enabled', 0)){
			if($config->get('ndb_enable', 0)){
				\GCore\Libs\Model::generateModel($model_id, array(
					'tablename' => $config->get('ndb_table_name', ''),
					'dbo_config' => array(
						'type' => $config->get('ndb_driver', 'mysql'), 
						'host' => $config->get('ndb_host', 'localhost'), 
						'name' => $config->get('ndb_database', ''), 
						'user' => $config->get('ndb_user', ''), 
						'pass' => $config->get('ndb_password', ''), 
						'prefix' => $config->get('ndb_prefix', 'jos_')
					),
				));
			}else{
				if(!$config->get('tablename', '')){
					return;
				}
				//\GCore\Libs\Model::generateModel($model_id, array('tablename' => $config->get('tablename', '')));
				$class_code = '
					namespace GCore\Models;
					if(!class_exists("\GCore\Models\\'.$model_id.'", false)){
						class '.$model_id.' extends \GCore\Libs\Model {
							var $tablename = "'.$config->get('tablename', '').'";
							
							function beforeSave(&$data, &$params, $mode){
								if($mode == "create" AND empty($data["uniq_id"])){
									$data["uniq_id"] = \GCore\Libs\Str::rand();
								}
							}
						}
					}
				';
				eval($class_code);
			}
			$model_class = '\GCore\Models\\'.$model_id;
			if(!class_exists($model_class)){
				$form->debug[$action_id][self::$title]['Queries'] = "Error creating the model class, please try a different model id.";
				return;
			}
			$data = $form->data;
			if((bool)$config->get('save_under_modelid', 0) === true){
				$data = $form->data[$model_id];
			}else{
				if(!empty($data[$model_id])){
					unset($data[$model_id]);
				}
			}
			$user = \GCore\Libs\Base::getUser();
			
			$conditions = eval('?>'.$config->get('conditions', ''));

			$initial_queries = $model_class::getInstance()->dbo->log;
			if((bool)$config->get('multi_save', 0) === true){
				//$data['user_id'] = !empty($data['user_id']) ? $data['user_id'] : $user['id'];
				$model_class::getInstance()->saveAll($data, array('new' => (bool)$config->get('force_save', 0), 'conditions' => $conditions));
			}else{
				$data['user_id'] = !empty($data['user_id']) ? $data['user_id'] : $user['id'];
				$model_class::getInstance()->save($data, array('new' => (bool)$config->get('force_save', 0), 'conditions' => $conditions));
			}
			//insert the pkey value to data
			if((bool)$config->get('save_under_modelid', 0) === true){
				$form->data[$model_id][$model_class::getInstance()->pkey] = $model_class::getInstance()->id;
			}else{
				$form->data[$model_class::getInstance()->pkey] = $model_class::getInstance()->id;
			}
			$form->debug[$action_id][self::$title]['Queries'] = array_values(array_diff($model_class::getInstance()->dbo->log, $initial_queries));
		}
	}
	
	function load_tables($data = array()){
		$data = array_values($data['Form']['extras']['actions_config']);
		$data = $data[0];
		$dbo = \GCore\Libs\Database::getInstance(array(
			'type' => $data['ndb_driver'], 
			'host' => $data['ndb_host'], 
			'name' => $data['ndb_database'], 
			'user' => $data['ndb_user'], 
			'pass' => $data['ndb_password'], 
			'prefix' => $data['ndb_prefix']
		));
		//pr($dbo);
		$tables = $dbo->getTablesList();
		if(is_array($tables)){
			$tables = array_combine($tables, $tables);
			echo json_encode($tables);
		}else{
			echo json_encode(array('Failed to connect to database'));
		}
	}

	public static function config($data = array()){
		$tables = \GCore\Libs\Database::getInstance()->getTablesList();
		array_unshift($tables, '');
		$tables = array_combine($tables, $tables);
		
		$ndb_tables = array();
		if(!empty($data['ndb_table_name'])){
			/*$ndb_tables = \GCore\Libs\Database::getInstance(array(
				'type' => $data['ndb_driver'], 
				'host' => $data['ndb_host'], 
				'name' => $data['ndb_database'], 
				'user' => $data['ndb_user'], 
				'pass' => $data['ndb_password'], 
				'prefix' => $data['ndb_prefix']
			))->getTablesList();
			$ndb_tables = array_combine($ndb_tables, $ndb_tables);*/
			$ndb_tables = array($data['ndb_table_name'] => $data['ndb_table_name']);
		}

		echo \GCore\Helpers\Html::formStart('action_config db_save_action_config', 'db_save_action_config__XNX_');
		?>
		<script>
			function db_save_ndb_load_tables(elem, SID){
				jQuery('#db_save_ndb_table_name_'+SID).empty();
				jQuery('#db_save_ndb_table_name_'+SID).append('<option value="">Loading....</option>');
				jQuery.ajax({
					"type" : "POST",
					"url" : "<?php echo r_('index.php?ext=chronoforms&act=action_task&action_name=db_save&action_fn=load_tables&tvout=ajax'); ?>",
					"data" : jQuery("#external-"+SID+" :input").serialize(),
					"success" : function(res){
						try{
							jQuery('#db_save_ndb_table_name_'+SID).empty();
							jQuery.each(jQuery.parseJSON(res), function(id, val){
								jQuery('#db_save_ndb_table_name_'+SID).append('<option value="'+id+'">'+val+'</option>');
							});
						}catch(error){
							jQuery('#db_save_ndb_table_name_'+SID).empty();
							jQuery('#db_save_ndb_table_name_'+SID).append('<option value="">Failed to connect!!</option>');
						}
					},
					"error" : function(){
						jQuery('#db_save_ndb_table_name_'+SID).empty();
						jQuery('#db_save_ndb_table_name_'+SID).append('<option value="">Failed to connect!!</option>');
					},
				});
			}
		</script>
		<ul class="nav nav-tabs">
			<li class="active"><a href="#basic-_XNX_" data-g-toggle="tab"><?php echo l_('CF_BASIC'); ?></a></li>
			<li><a href="#external-_XNX_" data-g-toggle="tab"><?php echo l_('CF_EXTERNAL_DB'); ?></a></li>
		</ul>
		<div class="tab-content">
			<div id="basic-_XNX_" class="tab-pane active">
			<?php
			echo \GCore\Helpers\Html::formSecStart();
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][action_label]', array('type' => 'text', 'label' => l_('CF_ACTION_LABEL'), 'class' => 'XL', 'sublabel' => l_('CF_ACTION_LABEL_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][enabled]', array('type' => 'dropdown', 'label' => l_('CF_ENABLED'), 'options' => array(0 => l_('NO'), 1 => l_('YES'))));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][tablename]', array('type' => 'dropdown', 'label' => l_('CF_TABLENAME'), 'options' => $tables, 'sublabel' => l_('CF_TABLENAME_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][save_under_modelid]', array('type' => 'dropdown', 'label' => l_('CF_SAVE_UNDER_MODELID'), 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'sublabel' => l_('CF_SAVE_UNDER_MODELID_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][multi_save]', array('type' => 'dropdown', 'label' => l_('CF_MULTI_SAVE'), 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'sublabel' => l_('CF_MULTI_SAVE_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][model_id]', array('type' => 'text', 'label' => l_('CF_MODEL_ID'), 'sublabel' => l_('CF_MODEL_ID_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][force_save]', array('type' => 'dropdown', 'label' => l_('CF_FORCE_SAVE'), 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'sublabel' => l_('CF_FORCE_SAVE_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][conditions]', array('type' => 'textarea', 'rows' => 5, 'cols' => 70, 'label' => l_('CF_DB_SAVE_CONDITIONS'), 'sublabel' => l_('CF_DB_SAVE_CONDITIONS_DESC')));
			echo \GCore\Helpers\Html::formSecEnd();
			?>
			</div>
			<div id="external-_XNX_" class="tab-pane">
			<?php
			echo \GCore\Helpers\Html::formSecStart();
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][ndb_enable]', array('type' => 'dropdown', 'label' => l_('CF_DB_SAVE_EXTERNAL_ENABLED'), 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'sublabel' => l_('CF_DB_SAVE_EXTERNAL_ENABLED_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][ndb_driver]', array('type' => 'text', 'label' => l_('CF_DB_SAVE_EXTERNAL_DB_DRIVER'), 'sublabel' => l_('CF_DB_SAVE_EXTERNAL_DB_DRIVER_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][ndb_host]', array('type' => 'text', 'label' => l_('CF_DB_SAVE_EXTERNAL_DB_HOST'), 'sublabel' => l_('CF_DB_SAVE_EXTERNAL_DB_HOST_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][ndb_database]', array('type' => 'text', 'label' => l_('CF_DB_SAVE_EXTERNAL_DB_NAME'), 'sublabel' => l_('CF_DB_SAVE_EXTERNAL_DB_NAME_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][ndb_user]', array('type' => 'text', 'label' => l_('CF_DB_SAVE_EXTERNAL_DB_USER'), 'sublabel' => l_('CF_DB_SAVE_EXTERNAL_DB_USER_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][ndb_password]', array('type' => 'text', 'label' => l_('CF_DB_SAVE_EXTERNAL_DB_PASSWORD'), 'sublabel' => l_('CF_DB_SAVE_EXTERNAL_DB_PASSWORD_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][ndb_prefix]', array('type' => 'text', 'label' => l_('CF_DB_SAVE_EXTERNAL_DB_PREFIX'), 'sublabel' => l_('CF_DB_SAVE_EXTERNAL_DB_PREFIX_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][ndb_load_tables]', array('type' => 'button', 'value' => l_('CF_DB_SAVE_EXTERNAL_DB_LOAD_TABLES'), 'onclick' => 'db_save_ndb_load_tables(this, \'_XNX_\')', 'sublabel' => ''));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][ndb_table_name]', array('type' => 'dropdown', 'label' => l_('CF_DB_SAVE_EXTERNAL_DB_TABLE'), 'id' => 'db_save_ndb_table_name__XNX_', 'options' => $ndb_tables, 'sublabel' => l_('CF_DB_SAVE_EXTERNAL_DB_TABLE_DESC')));
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
		$diags[l_('CF_DIAG_TABLE_SELECTED')] = !empty($data['tablename']);
		return $diags;
	}
}