<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><script type="text/javascript">
    nextend.ready(
        function ($) {
            var n2 = $('#n2-admin'),
                hideSidebarButtons = $('.n2-hide-sidebar')
                    .on('click', function () {
                        if (n2.hasClass('n2-sidebar-hidden')) {
                            n2.removeClass('n2-sidebar-hidden');
                        } else {
                            n2.addClass('n2-sidebar-hidden');
                        }
                        $(window).trigger('resize');
                    });
        }
    );
</script>

<?php
echo NHtml::openTag('div', array('class' => 'n2-table n2-table-fixed'));
echo NHtml::openTag('div', array('class' => 'n2-tr'));

if (!empty($logoUrl)):

    echo NHtml::tag('div', array('class' => 'n2-td n2-blue-logo-bg n2-logo n2-border-radius-tl'), NHtml::tag('a', array('href' => $logoUrl), NHtml::image($logoImageUrl)));

endif;
?>
<div class="n2-td n2-blue-bg n2-header n2-border-radius-tr">
    <?php
    if (isset($actions) && count($actions)):
        ?>
        <div class="n2-header-action n2-left">
            <?php
            foreach ($actions AS $action):
                echo $action;
            endforeach;
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($views) && count($views)): ?>
        <div class="n2-header-menu">
            <?php
            foreach ($views AS $view) {
                echo $view;
            }
            ?>
        </div>
    <?php endif; ?>

</div>

<?php
echo NHtml::closeTag('div');
echo NHtml::closeTag('div');