<?php
/**
 * @version     2.0.0
 * @package     com_keenitportfolio
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Abdur Rashid <rashid.cse.05@gmail.com> - http://www.keenitsolution.com
 */
// no direct access
defined('_JEXEC') or die;
JHtml::_('bootstrap.tooltip');
$document = JFactory::getDocument();
$document->addStylesheet('components/com_keenitportfolio/assets/css/list.css');
$app = JFactory::getApplication();
$params = $app->getParams();
$title				= $params->get('detail_title', 1);
$can_title			= $params->get('can_title', 1);
$client_name		= $params->get('client_name', 1);
$project_date		= $params->get('project_date', 1);
$project_url		= $params->get('project_url', 1);
$project_details	= $params->get('project_details', 1);
?>

<?php if ($this->item) : ?>

<div class="portfolio_detail width100">
  <div class="port-width40"> <img class="port-img" src="<?php echo JURI::root(); ?>images/portfolio/<?php echo $this->item->image; ?>" alt=""> </div>
  <div class="port-width60">
  
    <?php if($title): ?>
    <h2><?php echo $this->item->project_name; ?></h2>
    <?php else: ?>
    <?php endif ?>
    
    <?php if($can_title): ?>
    <p>
      <label><?php echo JText::_('COM_KEENITPORTFOLIO_FORM_LBL_PORTFOLIO_CATEGORY');?>:</label>
      <?php echo $this->item->category_title;; ?>
    </p>
    <?php else: ?>
    <?php endif ?>
    
    <?php if($client_name): ?>
    <?php if(!empty($this->item->client_name)): ?>
    <p>
      <label><?php echo JText::_('COM_KEENITPORTFOLIO_FORM_LBL_PORTFOLIO_CLIENT_NAME');?>:</label>
      <?php echo $this->item->client_name; ?>
    </p>
	<?php endif; ?>
    <?php else: ?>
    <?php endif ?>
    
    <?php if($project_date): ?>
    <?php if(!empty($this->item->final_date) or  ($this->item->final_date=='0000-00-00')): ?>
    <p>
      <label><?php echo JText::_('COM_KEENITPORTFOLIO_FORM_LBL_PORTFOLIO_FINAL_DATE');?>:</label>
      <?php echo $this->item->final_date; ?>
    </p>
    <?php endif; ?>
    <?php else: ?>
    <?php endif ?>
    
    <?php if($project_url): ?>
    <?php if(!empty($this->item->project_url)): ?>
    <p>
      <label><?php echo JText::_('COM_KEENITPORTFOLIO_FORM_LBL_PORTFOLIO_PROJECT_URL');?>:</label>
      <a href="<?php echo $this->item->project_url; ?>" target="_blank">View Project</a>
    </p>
    <?php endif; ?>
    <?php else: ?>
    <?php endif ?>
    
    <?php if($project_details): ?>
    <?php if(!empty($this->item->desc)): ?>
    <p>
      <label>Description:</label>
      <br />
      <?php echo $this->item->desc; ?>
    </p>
    <?php endif; ?>
    
    <?php else: ?>
    <?php endif ?>
  </div>
</div>
<?php
else:
    echo JText::_('COM_KEENITPORTFOLIO_ITEM_NOT_LOADED');
endif;
?>
