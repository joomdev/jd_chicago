<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoforms\Actions\DbRead;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
Class DbRead extends \GCore\Admin\Extensions\Chronoforms\Action{
	static $title = 'DB Read';
	static $group = array('data_management' => 'Data Management');
	var $events = array('found' => 0, 'not_found' => 0);
	var $events_status = array('found' => 'success', 'not_found' => 'fail');

	var $defaults = array(
		'tablename' => '',
		'enabled' => 1,
		'model_id' => 'Data_XNX_',
		'load_under_modelid' => 1,
		'multi_read' => 0,
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
		if(!$config->get('enabled', 1)){
			return;
		}
		
		if($config->get('tablename', '') OR $config->get('ndb_enable', 0)){
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
				\GCore\Libs\Model::generateModel($model_id, array('tablename' => $config->get('tablename', '')));
			}
			$model_class = '\GCore\Models\\'.$model_id;
			$model_class = $model_class::getInstance();
			
			if($config->get('enable_relations', 0)){
				$relations = $config->get('relations', array());
				foreach($relations as $relation){
					\GCore\Libs\Model::generateModel($relation['model'], array('tablename' => $relation['tablename']));
					$join_conditions = !empty($relation['join_conditions']) ? eval('?>'.$relation['join_conditions']) : array();
					
					$model_class->bindModels($relation['type'], array(
						$relation['model'] => array(
							'className' => '\GCore\Models\\'.$relation['model'],
							'foreignKey' => $relation['fkey'],
							'join_conditions' => is_array($join_conditions) ? $join_conditions : array(),
						),
					));
				}
			}
			//$data = $form->data;
			$find_type = 'first';
			if((bool)$config->get('multi_read', 0) === true){
				$find_type = 'all';
			}

			$find_params = array();
			$conditions = eval('?>'.$config->get('conditions', ''));
			$model_class->conditions = is_array($conditions) ? $conditions : array();
			
			if($config->get('fields', '')){
				$find_params['fields'] = array_map('trim', explode(',', $config->get('fields', '')));
			}
			if($config->get('order', '')){
				$find_params['order'] = array_map('trim', explode(',', $config->get('order', '')));
			}
			if($config->get('group', '')){
				$find_params['group'] = array_map('trim', explode(',', $config->get('group', '')));
			}
			
			$initial_queries = $model_class->dbo->log;
			//run query
			$rows = $model_class->find($find_type, $find_params);
			if(!empty($rows)){
				$this->events['found'] = 1;
			}else{
				$this->events['not_found'] = 1;
			}
			$form->debug[$action_id][self::$title]['Queries'] = array_values(array_diff($model_class->dbo->log, $initial_queries));
			
			if($config->get('enable_relations', 0)){
				$form->data = array_merge($form->data, $rows);
			}else{
				$data = array();
				if((bool)$config->get('multi_read', 0) === true){
					foreach($rows as $k => $row){
						$data[$k] = $row[$model_id];
					}
				}else{
					$data = !empty($rows[$model_id]) ? $rows[$model_id] : array();
				}
				
				if((bool)$config->get('load_under_modelid', 0) === true){
					$form->data[$model_id] = $data;
				}else{
					$form->data = array_merge($form->data, $data);
				}
			}
		}
		//pr($form->data);
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

		echo \GCore\Helpers\Html::formStart('action_config db_read_action_config', 'db_read_action_config__XNX_');
		?>
		<script>
			function db_read_ndb_load_tables(elem, SID){
				jQuery('#db_read_ndb_table_name_'+SID).empty();
				jQuery('#db_read_ndb_table_name_'+SID).append('<option value="">Loading....</option>');
				jQuery.ajax({
					"type" : "POST",
					"url" : "<?php echo r_('index.php?ext=chronoforms&act=action_task&action_name=db_read&action_fn=load_tables&tvout=ajax'); ?>",
					"data" : jQuery("#external-"+SID+" :input").serialize(),
					"success" : function(res){
						try{
							jQuery('#db_read_ndb_table_name_'+SID).empty();
							jQuery.each(jQuery.parseJSON(res), function(id, val){
								jQuery('#db_read_ndb_table_name_'+SID).append('<option value="'+id+'">'+val+'</option>');
							});
						}catch(error){
							jQuery('#db_read_ndb_table_name_'+SID).empty();
							jQuery('#db_read_ndb_table_name_'+SID).append('<option value="">Failed to connect!!</option>');
						}
					},
					"error" : function(){
						jQuery('#db_read_ndb_table_name_'+SID).empty();
						jQuery('#db_read_ndb_table_name_'+SID).append('<option value="">Failed to connect!!</option>');
					},
				});
			}
			
			function addRelation(elem, SID){
				var last = jQuery(elem).closest('.form-group').prev();
				var count = parseInt(last.clone().wrap('<p>').parent().html().match(/\[relations\]\[[0-9]+\]/).pop().replace('[relations][', '').replace(']', '')) + 1;
				jQuery(elem).closest('.form-group').before(last.clone().wrap('<p>').parent().html().replace(/\[relations\]\[[0-9]+\]/g, '[relations]['+count+']'));
			}
			function removeRelation(elem, button_id){
				var last = jQuery(elem).closest('.form-group').prev();
				var count = last.clone().wrap('<p>').parent().html().match(/\[relations\]\[[0-9]+\]/).pop().replace('[relations][', '').replace(']', '');
				if(count != '0'){
					last.remove();
				}
			}
		</script>
		<ul class="nav nav-tabs">
			<li class="active"><a href="#basic-_XNX_" data-g-toggle="tab"><?php echo l_('CF_BASIC'); ?></a></li>
			<li><a href="#relations-_XNX_" data-g-toggle="tab"><?php echo l_('CF_RELATIONS'); ?></a></li>
			<li><a href="#external-_XNX_" data-g-toggle="tab"><?php echo l_('CF_EXTERNAL_DB'); ?></a></li>
		</ul>
		<div class="tab-content">
			<div id="basic-_XNX_" class="tab-pane active">
			<?php
			echo \GCore\Helpers\Html::formSecStart();
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][action_label]', array('type' => 'text', 'label' => l_('CF_ACTION_LABEL'), 'class' => 'XL', 'sublabel' => l_('CF_ACTION_LABEL_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][enabled]', array('type' => 'dropdown', 'label' => l_('CF_ENABLED'), 'options' => array(0 => l_('NO'), 1 => l_('YES'))));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][tablename]', array('type' => 'dropdown', 'label' => l_('CF_TABLENAME'), 'options' => $tables, 'sublabel' => l_('CF_DB_READ_TABLENAME_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][multi_read]', array('type' => 'dropdown', 'label' => l_('CF_DB_READ_MULTI'), 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'sublabel' => l_('CF_DB_READ_MULTI_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][load_under_modelid]', array('type' => 'dropdown', 'label' => l_('CF_DB_READ_UNDER_MODELID'), 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'sublabel' => l_('CF_DB_READ_UNDER_MODELID_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][model_id]', array('type' => 'text', 'label' => l_('CF_MODEL_ID'), 'sublabel' => l_('CF_DB_READ_MODEL_ID_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][fields]', array('type' => 'text', 'class' => 'L', 'label' => l_('CF_DB_READ_FIELDS'), 'sublabel' => l_('CF_DB_READ_FIELDS_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][order]', array('type' => 'text', 'class' => 'L', 'label' => l_('CF_DB_READ_ORDER'), 'sublabel' => l_('CF_DB_READ_ORDER_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][group]', array('type' => 'text', 'class' => 'L', 'label' => l_('CF_DB_READ_GROUP'), 'sublabel' => l_('CF_DB_READ_GROUP_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][conditions]', array('type' => 'textarea', 'rows' => 8, 'cols' => 70, 'label' => l_('CF_DB_READ_CONDITIONS'), 'sublabel' => l_('CF_DB_READ_CONDITIONS_DESC')));
			echo \GCore\Helpers\Html::formSecEnd();
			?>
			</div>
			<div id="relations-_XNX_" class="tab-pane">
			<?php
			echo \GCore\Helpers\Html::formSecStart();
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][enable_relations]', array('type' => 'dropdown', 'label' => l_('CF_DB_READ_ENABLE_RELATIONS'), 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'sublabel' => l_('CF_DB_READ_ENABLE_RELATIONS_DESC')));
			
			if(empty($data['relations'])){
				$data['relations'] = array(array());
			}
			foreach($data['relations'] as $i => $relation){
				echo '<div class="panel panel-default"><div class="panel-body">';
				echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][relations]['.$i.'][model]', array('type' => 'text', 'label' => l_('CF_DB_READ_RELATIONS_MODEL'), 'class' => 'M', 'sublabel' => l_('CF_DB_READ_RELATIONS_MODEL_DESC')));
				echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][relations]['.$i.'][tablename]', array('type' => 'dropdown', 'label' => l_('CF_DB_READ_RELATIONS_TABLENAME'), 'options' => $tables, 'sublabel' => l_('CF_DB_READ_RELATIONS_TABLENAME_DESC')));
				echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][relations]['.$i.'][type]', array('type' => 'dropdown', 'label' => l_('CF_DB_READ_RELATIONS_TYPE'), 'options' => array('hasOne' => l_('hasOne'), 'hasMany' => l_('hasMany'), 'belongsTo' => l_('belongsTo')), 'sublabel' => l_('CF_DB_READ_RELATIONS_TYPE_DESC')));
				echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][relations]['.$i.'][fkey]', array('type' => 'text', 'label' => l_('CF_DB_READ_RELATIONS_FKEY'), 'class' => 'M', 'sublabel' => l_('CF_DB_READ_RELATIONS_FKEY_DESC')));
				echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][relations]['.$i.'][join_conditions]', array('type' => 'textarea', 'rows' => 3, 'cols' => 70, 'label' => l_('CF_DB_READ_RELATIONS_JOIN_CONDITIONS'), 'sublabel' => l_('CF_DB_READ_RELATIONS_JOIN_CONDITIONS_DESC')));
				echo '</div></div>';
			}
			echo \GCore\Helpers\Html::formLine('process_relations', array('type' => 'multi', 'layout' => 'wide',
				'inputs' => array(
					array('type' => 'button', 'name' => 'add_relation', 'class' => 'btn btn-success', 'value' => l_('CF_DB_READ_ADD_RELATION'), 'id' => 'add_relation__XNX_', 'onclick' => 'addRelation(this, \'_XNX_\');'),
					array('type' => 'button', 'name' => 'remove_relation', 'class' => 'btn btn-danger', 'value' => l_('CF_DB_READ_REMOVE_RELATION'), 'id' => 'remove_relation__XNX_', 'onclick' => 'removeRelation(this, \'_XNX_\');'),
				)
			));
			
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
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][ndb_load_tables]', array('type' => 'button', 'value' => l_('CF_DB_SAVE_EXTERNAL_DB_LOAD_TABLES'), 'onclick' => 'db_read_ndb_load_tables(this, \'_XNX_\')', 'sublabel' => ''));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][ndb_table_name]', array('type' => 'dropdown', 'label' => l_('CF_DB_SAVE_EXTERNAL_DB_TABLE'), 'id' => 'db_read_ndb_table_name__XNX_', 'options' => $ndb_tables, 'sublabel' => l_('CF_DB_SAVE_EXTERNAL_DB_TABLE_DESC')));
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