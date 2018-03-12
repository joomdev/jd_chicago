<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php echo NHtml::openTag('div', $attributes); ?>
<?php if ($image[0] == '<'): ?>
    <?php echo $image; ?>
<?php else: ?>
    <img src="<?php echo N2ImageHelper::fixed($image); ?>"/>
<?php endif; ?>


<?php
if ($lt) {
    echo NHtml::tag('div', $ltAttributes, $lt);
}
if ($rt) {
    echo NHtml::tag('div', $rtAttributes, $rt);
}
if ($lb) {
    echo NHtml::tag('div', $lbAttributes, $lb);
}
if ($rb) {
    echo NHtml::tag('div', $rbAttributes, $rb);
}
if ($center) {
    echo NHtml::tag('div', $centerAttributes, $center);
}
if ($overlay) {
    echo NHtml::tag('div', array(
        'class' => 'n2-box-overlay n2-on-hover'
    ), $rb);
}
?>

<?php
if ($firstCol):
    ?>
    <div class="n2-box-placeholder">
        <?php echo $placeholderContent; ?>
        <?php
        if ($secondCol):
            ?>
            <table>
                <tr>
                    <td class="n2-box-button"><?php echo $firstCol; ?></td>
                    <td class="n2-box-button"><?php echo $secondCol; ?></td>
                </tr>
            </table>
        <?php
        else:
            ?>
            <table>
                <tr>
                    <td class="n2-box-button"><?php echo $firstCol; ?></td>
                </tr>
            </table>
        <?php
        endif;
        ?>
    </div>
<?php
endif;
?>
</div>