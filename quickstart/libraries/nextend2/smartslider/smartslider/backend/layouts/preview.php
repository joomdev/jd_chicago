<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
/* @var $this N2Layout */
?>
    <div id="n2-admin" class="n2 n2-border-radius">
        <?php
        $this->renderFragmentBlock('nextend_content');
        ?>
    </div>
<?php
N2JS::addInline("new NextendExpertMode('smartslider', " . N2SSPRO . ");");