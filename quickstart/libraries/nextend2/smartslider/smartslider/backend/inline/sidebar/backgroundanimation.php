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
            </div>
        </div>
    </div>
</div>
<div class="n2-lightbox-sidebar-list">

</div>