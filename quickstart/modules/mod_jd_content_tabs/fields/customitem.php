<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.form.formfield');
 JHTML::_( 'behavior.modal' ); 
 $doc=JFactory::getDocument ();
 //$doc->addScript('//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js');
 JHtml::_('jquery.ui', array('core', 'sortable'));
 class JFormFieldCustomitem extends JFormField {
	 protected $type = 'customitem';
	 public function getLabel()
		{
			return '';
		}
		
        public function getInput() {
			$this->init();
			$return = '';
			$return .= '<input type="hidden" value="'.$this->value.'" name="'.$this->name.'" id="'.$this->id.'" />';
			
			$return .= $this->addItem();
			$return .= '<ul id="updateItemForms">';
			
			if($this->value!='')
			{
				$items = explode(',',$this->value);
				if(count($items)>0)
				{
					foreach($items as $item)
					{
						$return .= $this->displayItem($item);
					}
				}
			}
			
			$return .= '</ul>';
			$return .= '<button type="button" id="addItem" class="itemInputButton">Add Item</button>
			<script>
			(function($){
				$( "#updateItemForms" ).sortable({ placeholder: "form-placeholder",
				start: function( event, ui ) {
					$(".form-placeholder").css("height",$(".updateItemForm").height());
				},
				update: function()
				{
					var order = $("#updateItemForms").sortable("toArray");
					
					var order = order.join();
					
					$("#'.$this->id.'").val(order);
				},
				handle: ".form-sort-handle"
				});
			})(jQuery)
			</script>
			';
			return $return;
		}
				public function init()
		{
			$document = JFactory::getDocument();
			$style = "<style>
			#addItemForm
			{
				width:100%;
				background:#F4F4F4;
				display: block;
				float: left;
				border: 1px #BFC2CD solid;
			}
			.updateItemForm
			{
				width:100%;
				background:#F4F4F4;
				display: block;
				float: left;
				border: 1px #A9D96C solid; 
				margin-top: 10px !important;
			}
			.form-placeholder
			{
				width:100%;
				background:#e0f2d0;
				display: block;
				float: left;
				border: 1px #A9D96C dashed;
				margin-top: 10px !important;
			}
			#addItemForm h2.itemFormTitle
			{
				margin: 0px;
				background: #BFC2CD;
				color: #fff;
				font-weight: lighter;
				text-transform: uppercase;
				padding: 10px;
				margin-bottom: 10px;
			}
			#addItemForm .itemFormBody
			{
				margin: 0px;
				display: block;
				float: left;
				width: 100%;
			}
			.updateItemForm h2.itemFormTitle
			{
				margin: 0px;
				background: #A9D96C;
				color: #fff;
				font-weight: lighter;
				text-transform: uppercase;
				padding: 10px;
				margin-bottom: 10px;
			}
			.updateItemForm .itemFormBody
			{
				margin: 0px;
				display: block;
				float: left;
				width: 100%;
			}
			.addItemRow
			{
				width: 100%;
				float: left;
				clear: both;
				margin-bottom: 10px;
			}
			.itemInputLabel
			{
				float: left;
				line-height: 30px;
				width: 100px;
				text-align: right;
				padding: 0px 15px;
			}
			.itemInput
			{	
				float: left;
				margin: 0px;
				height: 25px;
				width: 130px;
				padding: 0px 10px;
			}
			.itemInputText
			{	
				float: left;
				margin: 0px;
				height: 75px;
				width: 130px;
				padding: 0px 10px;
			}
			.itemInputButton
			{
				width: 70px;
				background:#BFC2CD;
				float: left;
				border:none;
				color:#fff;
				height:30px;
				line-height:25px;
			}
			.itemInputButton.modal
			{
				background:#BFC2CD !important;
				float: left !important;
				border:none !important;
				color:#fff !important;
				width: 35px !important;
				height: 27px !important;
				line-height: 28px !important;
				text-align: center !important;
				text-transform: uppercase !important;
				font-size: 9px !important;
				margin-left: 10px;
				padding: 0px 6px;
				text-decoration: none;
			}
			.itemInputButton.updatebtn
			{
				background:#A9D96C;
			}
			.itemInputButton.updatebtn:hover
			{
				background:#A9D96C;
			}
			.itemInputButton.deletebtn
			{
				background:#FF6C60;
			}
			.itemInputButton.deletebtn:hover
			{
				background:#FF6C60;
			}
			.itemInputButton:hover
			{
				width: 70px;
				background:#BFC2CD;
				float: left;
				border:none;
				color:#fff;
				height:30px;
				line-height:25px;
			}
			.editor {
				width:450px;
				height:300px;
			}
			.form-sort-handle
			{
				width: 20px;
				height: 20px;
				display: block;
				float: right;
				position: relative;
				cursor: move;
			}
			.form-sort-handle img
			{
				width: 20px;
				height: 20px;
				margin: 0px;
			}
			#addItemFormData
			{
				margin:0px;
				padding:0px;
				background:transparent;
				background-color:transparent;
			}
			.updateItemFormData
			{
				margin:0px;
				padding:0px 0px 10px 0px !important;
				background:transparent;
				background-color:transparent;
			}
			</style>";
			$script = '	
			<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
			

<script>
jQuery(function($){
	$("#addItem").click(function(){
	 
		$.ajax({
			type: "POST",
			dataType: "json",
			data: $("#addItemFormData").serialize(),
			url: "'.JURI::root().'modules/mod_jd_content_tabs/fields/ajax.php",
			success: function(response){
			   
				var preval = $("#'.$this->id.'").val();
				if(preval=="")
				{
					$("#'.$this->id.'").val(response.id);
				}
				else
				{
					$("#'.$this->id.'").val(preval+","+response.id);
				}
				Joomla.submitbutton("module.apply");
			}
		})
		
	});
})
function updateItemData(id)
{
	var imgloc = "'.JURI::root().'modules/mod_jd_content_tabs/fields/loading.gif";
	var nimg = "<img src = " + imgloc + ">";
	jQuery("#loading"+id).html(nimg);
	jQuery("#loading"+id).show(); 
	setTimeout("timeupdate("+id+")", 2000);
	
}
function timeupdate(id)
{
	
var loc = "'.JURI::root().'modules/mod_jd_content_tabs/fields/check.png";
	var a = "<img src = " + loc + ">";

	
	jQuery.ajax({
			type: "POST",
			dataType: "json",
			data: jQuery("#updateItemFormData-"+id).serialize(),
			url: "'.JURI::root().'modules/mod_jd_content_tabs/fields/ajax.php",
			success: function(response){
			}
	})
		jQuery("#loading"+id).html(a);
			 return false;	
}
function deleteItem(id)
{
	var c = confirm("Are you sure?");
	if(c)
	{
	jQuery.ajax({
			type: "POST",
			dataType: "json",
			data: {deleteItem:1,id:id},
			url: "'.JURI::root().'modules/mod_jd_content_tabs/fields/ajax.php",
			success: function(response){
				var preval = jQuery("#'.$this->id.'").val();
				var order = new Array();
				order = preval.split(",");
				order.splice( order.indexOf(response.id),1);
				order = order.join();
				jQuery("#'.$this->id.'").val(order);
				Joomla.submitbutton("module.apply");
			}
		})
	}
	else
	{
		return false;
	}
}
function jInsertFieldValue(value, id) {
var old_value = document.id(id).value;
	if (old_value != value) {
		var elem = document.id(id);
		elem.value = value;
		elem.fireEvent("change");
		if (typeof(elem.onchange) === "function") {
			elem.onchange();
		}
		SqueezeBox.close();
	}
}
</script>

';

			$document->addCustomTag($style);
			$document->addCustomTag($script);
		}
		
		public function addItem()
		{
			$moduleid = JRequest::getVar('id');
			$return = '';
			$return = '
			<div style="clear:both"></div>
			<br/>
			<div id="addItemForm">
				<fieldset id="addItemFormData">
				
				<input type="hidden" name="addItem" value="1" />
				<input class="itemInput" type="hidden" name="item[title]" />
				<input class="itemInput" name="item[thumbnail]" type="hidden" id="innerboxthumbnail" >
				<input class="itemInput" name="item[description]" type="hidden" id="innerboxthumbnail" >
				
				
				
				</fieldset>
			</div>
			';
			
			return $return;
		}
		public function displayItem($id)
		{
			$db = JFactory::getDbo();
			$query = "SELECT * FROM `#__bds` WHERE `id`='$id'";
			$db->setQuery($query);
			$b = "content";
			$params = $db->loadObject()->params;
			$params = json_decode($params);
			$editor = JFactory::getEditor();
			$return = '';
			$return = '
			<li id="'.$id.'" class="updateItemForm">
				<fieldset class="updateItemFormData" id="updateItemFormData-'.$id.'">
				<input type="hidden" name="updateItem" value="1" />
				<input type="hidden" name="itemid" value="'.$id.'" />
				<h2 class="itemFormTitle">Item #'.$id.'<span class="form-sort-handle"><img src="'.JURI::root().'modules/mod_jd_content_tabs/fields/handle.svg" /></span></h2><div class="itemFormBody">
					<div class="addItemRow">
						<span class="itemInputLabel">Title</span><input class="itemInput" type="text" name="item[title]" value="'.$params->title.'" />
					</div>
					<div class="addItemRow">
						<span class="itemInputLabel">Slide Image</span><input class="itemInput" name="item[thumbnail]" value="'.$params->thumbnail.'" type="text" id="innerboxthumbnail'.$id.'" >
	
								<a class="modal itemInputButton" title="Select" href="index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;asset=974&amp;author=&amp;fieldid=innerboxthumbnail'.$id.'" rel="{handler: \'iframe\', size: {x: 800, y: 500}}">
						Select</a>
						
					</div>
					<div class="addItemRow">
						<span class="itemInputLabel">Description</span><textarea class="itemInput"  name="item[description]" >'.$params->description.'</textarea>
						<input type="hidden" id="newone" value="'.$editor->save($b ).'" />
					</div>
				
					<div class="addItemRow">
						<span class="itemInputLabel">&nbsp;</span>
						<button onclick="updateItemData('.$id.')" id="update'.$id.'" type="button" class="itemInputButton updatebtn">Update</button>&nbsp;<button onclick="deleteItem('.$id.')" type="button" class="itemInputButton deletebtn">Delete</button>
						<span id="loading'.$id.'" style="display:none;"><img  src="'.JURI::root().'modules/mod_jd_content_tabs/fields/loading.gif"/><?span>
					</div>
				</div>
				</fieldset>
			</li>
				
		
				  
			';
			
			return $return;
		}
 }
?>