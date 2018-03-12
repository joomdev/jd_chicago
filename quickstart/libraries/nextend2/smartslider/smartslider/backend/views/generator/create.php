<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
/**
 * @var $_class N2SmartsliderBackendGeneratorView
 */
N2SmartsliderBackendGeneratorView::loadSources();
?>

    <div id="n2-tab-slide" class="n2-form-tab ">
        <div class="n2-h2 n2-content-box-title-bg"><?php n2_e('Choose dynamic source'); ?></div>
        <?php
        $_class->_renderSourceList(N2SmartsliderBackendGeneratorView::$sources['available']);
        ?>
        <div class="n2-clear"></div>
    </div>

<?php if (count(N2SmartsliderBackendGeneratorView::$sources['notavailable'])): ?>
    <div id="n2-tab-slide" class="n2-form-tab ">
        <div class="n2-h2 n2-content-box-title-bg"><?php n2_e('Not installed'); ?></div>
        <?php
        $_class->_renderSourceList(N2SmartsliderBackendGeneratorView::$sources['notavailable']);
        ?>
        <div class="n2-clear"></div>
    </div>
<?php endif; ?>

<?php
?>

<div id="n2-tab-slide" class="n2-form-tab ">
    <div class="n2-h2 n2-content-box-title-bg"><?php n2_e('Pro sources'); ?></div>

    <div class="n2-description">
        <?php
        ?>
        <?php
        ?>
        <ul>
            <li>Images from folder</li>
            <li>Facebook</li>
            <li>Twitter</li>
            <li>Instagram</li>
        </ul>
    <?php
    
        ?>
        <?php
        ?>
        <?php
        ?>
    </div>
</div>
<?php

?>

<?php
?>