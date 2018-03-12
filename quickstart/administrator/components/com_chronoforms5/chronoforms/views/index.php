<?php
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
?>
<div class="chrono-page-container">
<div class="container" style="width:100%;">
<?php
	$doc = \GCore\Libs\Document::getInstance();
	$doc->_('datatable');
	$doc->_('jquery');
	//$doc->_('jquery-ui');
	$doc->_('bootstrap');
	$doc->addCssCode('
		.gbs3 .panel-body{padding:5px;}
		.gbs3 .panel{margin-bottom:5px;}
		.gbs3 .alert{
			margin:3px 0px;
			padding:5px;
		}
	');

	//$this->Toolbar->setTitle(l_('FORMS_MANAGER'));
	$this->Toolbar->addButton('add', r_('index.php?ext=chronoforms&act=edit'), l_('CF_NEW'), $this->Assets->image('add', 'toolbar/'), 'link');
	$this->Toolbar->addButton('add_simple', r_('index.php?ext=chronoforms&act=edit&setup=simple'), l_('CF_NEW_SIMPLE'), $this->Assets->image('add', 'toolbar/'), 'link');
	$this->Toolbar->addButton('remove', r_('index.php?ext=chronoforms&act=delete'), l_('CF_DELETE'), $this->Assets->image('remove', 'toolbar/'), 'submit_selectors');
	$this->Toolbar->addButton('copy', r_('index.php?ext=chronoforms&act=copy'), l_('CF_COPY'), \GCore\C::get('GCORE_ADMIN_URL').'extensions/chronoforms/assets/images/copy.png', 'submit_selectors');
	$this->Toolbar->addButton('create_table', r_('index.php?ext=chronoforms&act=create_table'), l_('CF_CREATE_TABLE'), \GCore\C::get('GCORE_ADMIN_URL').'extensions/chronoforms/assets/images/database_table.png', 'submit_selectors');
	$this->Toolbar->addButton('backup', r_('index.php?ext=chronoforms&act=backup'), l_('CF_BACKUP'), \GCore\C::get('GCORE_ADMIN_URL').'extensions/chronoforms/assets/images/backup.png', 'submit_selectors');
	$this->Toolbar->addButton('restore', r_('index.php?ext=chronoforms&act=restore'), l_('CF_RESTORE'), \GCore\C::get('GCORE_ADMIN_URL').'extensions/chronoforms/assets/images/restore.png');
	$this->Toolbar->addButton('action', r_('index.php?ext=chronoforms&act=install_action'), l_('CF_INSTALL_ACTION'), \GCore\C::get('GCORE_ADMIN_URL').'extensions/chronoforms/assets/images/action.png');
	$this->Toolbar->addButton('locale', r_('index.php?ext=chronoforms&act=install_locale'), l_('CF_INSTALL_LOCALE'), \GCore\C::get('GCORE_ADMIN_URL').'extensions/chronoforms/assets/images/locale.png');
	$this->Toolbar->addButton('demos', r_('index.php?ext=chronoforms&act=demos'), l_('CF_DEMOS'), \GCore\C::get('GCORE_ADMIN_URL').'extensions/chronoforms/assets/images/demos.png');
	$this->Toolbar->addButton('delete_cache', r_('index.php?ext=chronoforms&act=delete_cache'), l_('CF_DELETE_CACHE'), \GCore\C::get('GCORE_ADMIN_URL').'extensions/chronoforms/assets/images/delete_cache.png', 'link');
	$this->Toolbar->addButton('settings', r_('index.php?ext=chronoforms&act=settings'), l_('CF_SETTINGS'), \GCore\C::get('GCORE_ADMIN_URL').'extensions/chronoforms/assets/images/settings.png', 'link');
	$this->Toolbar->addButton('validate', r_('index.php?ext=chronoforms&act=validateinstall'), l_('VALIDATE_INSTALL'), \GCore\C::get('GCORE_ADMIN_URL').'extensions/chronoforms/assets/images/validate.png', 'link');

	//$doc->addJsFile(\GCore\C::get('GCORE_FRONT_URL').'assets/js/jquery/jquery.dropdown.js');
	//$doc->addCssFile(\GCore\C::get('GCORE_FRONT_URL').'assets/css/jquery/jquery.dropdown.css');
?>
<div class="row">
	<form action="<?php echo r_('index.php?ext=chronoforms'); ?>" method="post" name="admin_form" id="admin_form">
		<?php
			echo $this->DataTable->headerPanel($this->DataTable->_l('<h4>'.l_('FORMS_MANAGER').'</h4>').$this->DataTable->_r($this->Toolbar->renderBar()));
			echo $this->DataTable->headerPanel($this->DataTable->_l($this->Html->input('srch', array('type' => 'text', 'placeholder' => l_('CF_SEARCH_FORMS')))).$this->DataTable->_r($this->Paginator->getNav()));
			$this->DataTable->create();
			$columns_list = array(
				'CHECK' => $this->Toolbar->selectAll(),
				'Form.title' => $this->Sorter->link(l_('CF_FORM_NAME'), 'Form.title'),
				'Form.diagnostics' => l_('CF_DIAGNOSTICS'),
				'Form.view' => l_('CF_FRONT_VIEW'),
				'Form.tables' => l_('CF_CONNECTED_TABLES'),
				'Form.app' => $this->Sorter->link(l_('CF_FORM_APP'), 'Form.app'),
				'Form.published' => l_('CF_PUBLISHED'),
				'Form.id' => $this->Sorter->link(l_('CF_FORM_ID'), 'Form.id')
			);
			if(!$chronoforms_settings->get('wizard.display_diagnostics', 1)){
				unset($columns_list['Form.diagnostics']);
			}
			$this->DataTable->header($columns_list);

			foreach($forms as $k => $form){
				$tables = array();
				if(!empty($forms[$k]['Form']['extras']['actions_config'])){
					foreach($forms[$k]['Form']['extras']['actions_config'] as $i => $action_config){
						if(!empty($action_config['tablename'])){
							$tables[] = $action_config['tablename'];
						}
					}
				}
				$tables = array_unique($tables);
				$list = '';
				foreach($tables as $table){
					$list .= '<li><a href="'.r_('index.php?ext=chronoforms&form_id={Form.id}&act=list_data&table='.$table).'">'.$table.'</a></li>';
				}
				if(!empty($tables)){
					$forms[$k]['Form']['tables'] = '
					<div class="btn-group">
						<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-g-toggle="dropdown">
						<span class="caret"></span>
						</button>
						<ul class="dropdown-menu gdropdown" role="menu">
							'.$list.'
						</ul>
					</div>
					';
				}
				
				$form_errors = 0;
				$form_errors_list = array();
				$form_warnings = 0;
				$form_warnings_list = array();
				$actions = !empty($forms[$k]['Form']['extras']['DNA']) ? $this->DnaBuilder->get_actions($forms[$k]['Form']['extras']['DNA']) : array();
				foreach($actions as $id => $action){
					$id = str_replace('_', '', $id);
					$action_class = '\GCore\Admin\Extensions\Chronoforms\Actions\\'.\GCore\Libs\Str::camilize($action).'\\'.\GCore\Libs\Str::camilize($action);
					if(class_exists($action_class) AND isset($action_class::$title)){
						$action_class = new $action_class();
						if(method_exists($action_class, 'config_check')){
							$check_result = $action_class::config_check(!empty($forms[$k]['Form']['extras']['actions_config'][$id]) ? $forms[$k]['Form']['extras']['actions_config'][$id] : array());
							foreach($check_result as $text => $bool){
								if($bool === false){
									$class = 'label-danger';
									$icon_class = 'fa-times';
									$form_errors++;
									$form_errors_list[] = $text;
								}else if($bool === -1){
									$class = 'label-warning';
									$icon_class = 'fa-exclamation';
									$form_warnings++;
									$form_warnings_list[] = $text;
								}
							}
							
						}
					}
				}
				$forms[$k]['Form']['diagnostics'] = $form_errors > 0 ? '<span class="label label-danger">'.$form_errors.' '.l_('CF_ISSUES').'&nbsp;<i class="fa fa-times fa-lg"></i></span>' : '<span class="label label-success">'.$form_errors.' '.l_('CF_ISSUES').'&nbsp;<i class="fa fa-check fa-lg"></i></span>';
				$forms[$k]['Form']['diagnostics'] .= '&nbsp;';
				$forms[$k]['Form']['diagnostics'] .= $form_warnings > 0 ? '<span class="label label-warning">'.$form_warnings.' '.l_('CF_NOTICES').'&nbsp;<i class="fa fa-exclamation fa-lg"></i></span>' : '<span class="label label-success">'.$form_warnings.' '.l_('CF_NOTICES').'&nbsp;<i class="fa fa-check fa-lg"></i></span>';
				//$forms[$k]['Form']['diagnostics'] .= '<div class="alert alert-danger"><ol><li>'.implode('</li><li>', $form_errors_list).'</li></ol></div>';
				
				$field_name_errors = false;
				$field_name_warnings = false;
				$invalid_names_j = array('event', 'option');
				$invalid_names_wp = array('event', 'name');
				//check invalid fields names
				if(!empty($forms[$k]['Form']['extras']['fields'])){
					foreach($forms[$k]['Form']['extras']['fields'] as $field){
						if(isset($field['name'])){
							if(strpos($field['name'], ' ') !== false){// OR strpos($field['name'], '-') !== false){
								$field_name_errors = true;
							}
							if(preg_match("/^\d/", $field['name']) === 1){
								$field_name_errors = true;
							}
							
							if(\GCore\C::get('GSITE_PLATFORM') == 'wordpress'){
								if(in_array($field['name'], $invalid_names_wp)){
									$field_name_warnings = true;
								}
							}else{
								if(in_array($field['name'], $invalid_names_j)){
									$field_name_warnings = true;
								}
							}
						}
					}
				}
				if($field_name_errors){
					$forms[$k]['Form']['diagnostics'] .= '<div class="alert alert-danger">'.l_('CF_FIELD_NAME_ERRORS').'</div>';
				}
				if($field_name_warnings){
					if(\GCore\C::get('GSITE_PLATFORM') == 'wordpress'){
						$forms[$k]['Form']['diagnostics'] .= '<div class="alert alert-warning">'.sprintf(l_('CF_FIELD_NAME_WARNINGS'), implode(", ", $invalid_names_wp), "Wordpress").'</div>';
					}else{
						$forms[$k]['Form']['diagnostics'] .= '<div class="alert alert-warning">'.sprintf(l_('CF_FIELD_NAME_WARNINGS'), implode(", ", $invalid_names_j), "Joomla").'</div>';
					}
				}
			}

			$title_width_desc = function($cell, $row){
				$out = '<a href="'.r_('index.php?ext=chronoforms&act=edit&id={Form.id}').'">{Form.title}</a>';
				if(!empty($row['Form']['params']['description'])){
					$out .= '<br><small>{Form.params.description}</small>';
				}
				return $out;
			};

			$this->DataTable->cells($forms, array(
				'CHECK' => array(
					'style' => array('width' => '5%'),
					'html' => $this->Toolbar->selector('{Form.id}')
				),
				'Form.title' => array(
					//'link' => r_('index.php?ext=chronoforms&act=edit&id={Form.id}'),
					'function' => $title_width_desc,
					'style' => array('text-align' => 'left', 'width' => '20%')
				),
				'Form.view' => array(
					'style' => array('width' => '10%'),
					'html' => '<a href="'.r_(\GCore\C::get('GCORE_ROOT_URL').'index.php?ext=chronoforms&chronoform={Form.title}').'" target="_blank">'.l_('CF_VIEW_FORM').'</a>',
				),
				/*'Form.tables' => array(
					'html' => '
						<input type="button" value="'.l_('CF_TABLES_LIST').'" data-dropdown="#dropdown-{Form.id}" />
						<div id="dropdown-{Form.id}" class="dropdown dropdown-tip">
							<ul class="dropdown-menu">
								<li><a href="#1">Item 1</a></li>
								<li><a href="#2">Item 2</a></li>
								<li><a href="#3">Item 3</a></li>
							</ul>
						</div>
						',
				),*/
				'Form.diagnostics' => array(
					'style' => array('width' => '25%'),
				),
				'Form.tables' => array(
					'style' => array('width' => '10%'),
				),
				'Form.app' => array(
					'style' => array('width' => '10%'),
				),
				'Form.published' => array(
					'link' => array(r_('index.php?ext=chronoforms&act=toggle&gcb={Form.id}&val=1&fld=published'), r_('index.php?ext=chronoforms&act=toggle&gcb={Form.id}&val=0&fld=published')),
					'image' => array($this->Assets->image('disabled.png'), $this->Assets->image('enabled.png')),
					'style' => array('width' => '5%'),
				),
				'Form.id' => array(
					'style' => array('width' => '5%'),
				)
			));
			echo $this->DataTable->build();
			echo $this->DataTable->footerPanel($this->DataTable->_l($this->Paginator->getInfo()).$this->DataTable->_r($this->Paginator->getNav()));
			echo $this->DataTable->footerPanel($this->DataTable->_r($this->Paginator->getList()));
		?>
	</form>
</div>
</div>
</div>