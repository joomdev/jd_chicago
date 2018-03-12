<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><div class="n2-heading-bar">
    <div class="n2-h1 n2-heading"><?php n2_e('Dashboard'); ?></div>
</div>

<?php
foreach (N2Base::getApplications() AS $info):
    if (!$info->isPublic() || !N2Acl::canDo($info->getName(), $info)) {
        continue;
    }
    $info->getInstance();
    ?>
    <div class="n2-form-tab">
        <div class="n2-h2 n2-content-box-title-bg"><?php echo $info->getLabel(); ?></div>

        <div class="n2-description">
            <a href="<?php echo $info->getUrl(); ?>"
               class="n2-button n2-button-big n2-button-green"><?php n2_e('Go to'); ?> <?php echo $info->getLabel(); ?></a>
        </div>
    </div>
<?php
endforeach;
?>