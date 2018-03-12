<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><div class="n2-sidebar-row n2-sidebar-header-bg n2-form-dark n2-sets-header">
    <div class="n2-table">
        <div class="n2-tr">
            <div class="n2-td">
                <div class="n2-h3 n2-uc"><?php n2_e('Set'); ?></div>
            </div>
            <div class="n2-td n2-manage-set">
                <?php
                $model->renderSetsForm();
                ?>
                <div id="layoutset-manage"
                     class="n2-button n2-button-medium n2-button-grey n2-h5 n2-uc"><?php n2_e('Manage'); ?></div>
            </div>
        </div>
    </div>
</div>
<?php
$ul = array();

$ul[] = array(
    'class'    => 'n2-button-container n2-save-as-new-container',
    'contents' => NHtml::tag('a', array(
        'class' => 'n2-button n2-button-big n2-button-green n2-uc n2-layout-save-as-new',
        'href'  => '#'
    ), n2_('Save as new layout')),
);

$this->widget->init("listn", array(
    "ul" => $ul
));
?>
<div class="n2-lightbox-sidebar-list">

</div>

<?php

$sets  = $model->getSets();
$setId = $sets[0]['id'];

$layouts         = array();
$layouts[$setId] = $model->getVisuals($setId);

N2JS::addFirstCode("
    new NextendLayoutManager({
        setsIdentifier: '" . $model->type . "set',
        sets: " . json_encode($sets) . ",
        visuals: " . json_encode($layouts) . ",
        ajaxUrl: '" . $this->appType->router->createAjaxUrl(array('layout/index')) . "'
    });
");

?>
<div class="n2-sidebar-pane-sizer">
    <i class="n2-i n2-it n2-i-drag"></i>
</div>

<div class="n2-sidebar-row n2-sidebar-header-bg n2-form-dark n2-sets-header">
    <div class="n2-table">
        <div class="n2-tr">
            <div class="n2-td">
                <div class="n2-h3 n2-uc"><?php n2_e('Local history'); ?></div>
            </div>
        </div>
    </div>
</div>
<div class="n2-ss-history-list">
    <ul class="n2-list n2-h4">
    </ul>
</div>