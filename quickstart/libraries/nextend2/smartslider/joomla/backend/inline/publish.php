<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><div class="n2-table n2-table-fixed n2-ss-slider-publish">
    <div class="n2-tr">
        <div class="n2-td n2-first">
            <div class="n2-h2">Module</div>

            <div class="n2-h4">Create a module to display the slider in template module position:</div>
            <br />
            <br />
            <?php
            echo NHtml::link("Create module", 'index.php?option=com_modules&view=select', array(
                'class'  => 'n2-button n2-button-big n2-button-green n2-h3',
                'target' => '_blank'
            ));
            ?>
        </div>
        <div class="n2-td">
            <div class="n2-h2">Articles</div>

            <div class="n2-h4">Paste the code into article:</div>
            <code><div onclick="return selectText(this);">smartslider3[<?php echo $sliderid; ?>]</div></code>
        </div>
        <div class="n2-td n2-last">
            <div class="n2-h2">PHP code</div>

            <div class="n2-h4">Paste the PHP code into source code:</div>
            <code><div onclick="return selectText(this);">
                &lt;?php <br />
                echo nextend_smartslider3(<?php echo $sliderid; ?>);<br />
                ?&gt;</div></code>
        </div>
    </div>
</div>