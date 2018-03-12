<?php
/**
* CHRONOFORMS version 4.0
* Copyright (c) 2006 - 2011 Chrono_Man, ChronoEngine.com. All rights reserved.
* Author: Chrono_Man (ChronoEngine.com)
* @license		GNU/GPL
* Visit http://www.ChronoEngine.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoforms\Actions\CsvExport;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
Class CsvExport extends \GCore\Admin\Extensions\Chronoforms\Action{
	static $title = 'CSV Export';
	static $group = array('data_management' => 'Data Management');
	//var $events = array('success' => 0, 'fail' => 0);
	var $delimiter = ',';

	var $defaults = array(
		'tablename' => '',
		'include' => '',
		'exclude' => '',
		'save_path' => '',
		'file_name' => '',
		'delimiter' => '',
		'enclosure' => '',
		'download_mime_type' => '',
		'download_export' => '',
		'download_nosave' => '',
		'where' => '',
		'data_path' => '',
		'excluded_columns' => '',
		'post_file_name' => '',
		'order_by' => '',
		'columns' => '',
		'delimiter' => ',',
	);

	function execute(&$form, $action_id){
		$config =  $form->actions_config[$action_id];
		$config = new \GCore\Libs\Parameter($config);

		if(!$config->get('enabled')){
			return;
		}
		$tablename = $config->get('tablename', '');
		$titles = array();
		$this->delimiter = $config->get('delimiter', ',');
		
		if($config->get('columns', '')){
			$columns = \GCore\Libs\Str::list_to_array($config->get('columns', ''));
			$titles = $columns;
			$columns = array_keys($columns);
		}
		
		if(!empty($tablename)){
			\GCore\Libs\Model::generateModel('ListData', array('tablename' => $tablename));
			$list_model = '\GCore\Models\ListData';
			
			if(!$config->get('columns', '')){
				$columns = $list_model::getInstance()->dbo->getTableColumns($tablename);
				$titles = array_combine($columns, $columns);
			}

			if($config->get('order_by', '')){
				$order_by = array_map('trim', explode(',', $config->get('order_by', '')));
			}else{
				$order_by = $list_model::getInstance()->pkey;
			}

			$file_name = 'csv_export_'.$tablename.'_'.date('YmdHi').'.csv';
			$rows = $list_model::getInstance()->find('all', array('fields' => $columns, 'order' => $order_by));
		}else{
			if(!$config->get('data_path', '')){
				return;
			}
			$rows = \GCore\Libs\Arr::getVal($form->data, explode('.', $config->get('data_path', '')), array());
			$rows = array_values($rows);
			
			if(!$config->get('columns', '')){
				$columns = array_keys($rows[0]);
				$titles = array_combine($columns, $columns);
			}
			$file_name = 'csv_export_'.date('YmdHis').'.csv';
		}
		
		if($config->get('excluded_columns', '')){
			$excluded_columns = array_map('trim', explode("\n", $config->get('excluded_columns', '')));
			$columns = array_diff($columns, $excluded_columns);
		}else{
			$excluded_columns = array();//$columns;
		}

		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename='.$file_name);
		header('Pragma: no-cache');
		header('Expires: 0');

		//$data = array($titles);
		foreach($titles as $k => $v){
			if(in_array($k, $columns)){
				$data[0][$k] = $v;
			}
		}

		if(!empty($rows)){
			foreach($rows as $row){
				$csv_data_row = array();
				
				if(!empty($tablename)){
					$row_path = $row['ListData'];
				}else{
					$row_path = $row;
				}
				
				foreach($row_path as $k => $v){
					if(in_array($k, $columns)){
						$csv_data_row[$k] = $v;
					}
				}
				$data[] = $csv_data_row;
			}
		}
		@ob_end_clean();
		$this->outputCSV($data);
		exit;
	}

	public function outputCSV($data){
		$outstream = fopen('php://output', 'w');
		array_walk($data, array($this, 'insert_data'), $outstream);
		fclose($outstream);
	}

	public function insert_data(&$vals, $key, $filehandler){
		fputcsv($filehandler, $vals, $this->delimiter); // add parameters if you want
	}

	public static function config(){
		$tables = \GCore\Libs\Database::getInstance()->getTablesList();
		array_unshift($tables, '');
		$tables = array_combine($tables, $tables);

		echo \GCore\Helpers\Html::formStart('action_config csv_export_action_config', 'csv_export_action_config__XNX_');
		echo \GCore\Helpers\Html::formSecStart();
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][enabled]', array('type' => 'dropdown', 'label' => l_('CF_ENABLED'), 'options' => array(0 => l_('NO'), 1 => l_('YES'))));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][tablename]', array('type' => 'dropdown', 'label' => l_('CF_CSV_TABLENAME'), 'options' => $tables, 'sublabel' => l_('CF_CSV_TABLENAME_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][data_path]', array('type' => 'text', 'class' => 'M', 'label' => l_('CF_CSV_DATA_PATH'), 'sublabel' => l_('CF_CSV_DATA_PATH_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][columns]', array('type' => 'textarea', 'class' => 'XL', 'rows' => 7, 'cols' => 60, 'label' => l_('CF_CSV_COLUMNS'), 'sublabel' => l_('CF_CSV_COLUMNS_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][excluded_columns]', array('type' => 'textarea', 'class' => 'XL', 'rows' => 7, 'cols' => 60, 'label' => l_('CF_CSV_EXCLUDED_COLUMNS'), 'sublabel' => l_('CF_CSV_EXCLUDED_COLUMNS_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][order_by]', array('type' => 'text', 'class' => 'XL', 'label' => l_('CF_CSV_ORDER_BY'), 'sublabel' => l_('CF_CSV_ORDER_BY_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][delimiter]', array('type' => 'text', 'class' => 'SS', 'label' => l_('CF_CSV_DELIMITER'), 'sublabel' => l_('CF_CSV_DELIMITER_DESC')));
		echo \GCore\Helpers\Html::formSecEnd();
		echo \GCore\Helpers\Html::formEnd();
	}

}
?>