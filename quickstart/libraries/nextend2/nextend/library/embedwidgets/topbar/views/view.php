<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
if ($fixTo) {
    ?>
    <script type="text/javascript">
        nextend.ready(
            function ($) {
                var topOffset = $('#wpadminbar, .navbar-fixed-top').height(),
                    topOffsetLightbox = 0;
                $('.<?php echo $snapClass; ?>').each(function () {
                    var bar = $(this);
                    bar.fixTo(bar.parent(), {
                        top: (bar.closest('.n2-lightbox').length > 0 ? topOffsetLightbox : topOffset)
                    });
                });
            }
        );
    </script>
<?php
}
?>

<div class="n2-top-bar n2-sidebar-list-bg <?php echo $snapClass; ?>">

    <?php
    if ($hideSidebar):
        ?>
        <div class="n2-hide-sidebar n2-sidebar-tab-bg n2-left">
            <i class="n2-i n2-it n2-i-s-close"></i>
        </div>
    <?php endif; ?>
    <?php
    if ($back):
        ?>
        <div class="n2-back n2-top-bar-menu n2-sidebar-tab-bg n2-left">
            <?php echo $back; ?>
        </div>
    <?php endif; ?>

    <?php
    if (isset($menu) && count($menu)):
        ?>
        <div class="n2-left n2-top-bar-menu">
            <?php
            foreach ($menu AS $m):
                echo $m;
            endforeach;
            ?>
        </div>
    <?php
    endif;
    ?>

    <?php

    if ($notification) {
        array_unshift($actions, NHtml::tag('a', array(
            'class' => 'n2-button n2-button-grey n2-button-big n2-notification-button',
            'href'  => '#'
        ), NHtml::tag('i', array('class' => 'n2-i n2-it n2-i-32 n2-i-notification'))));
    }

    if ($expert) {
        array_unshift($actions, NHtml::tag('a', array(
            'class' => 'n2-expert-switch',
            'href'  => '#'
        ), NHtml::tag('span', array(
                'class' => 'n2-expert-expert n2-uc n2-h5'
            ), n2_('Expert')) . '<br>' . NHtml::tag('span', array(
                'class' => 'n2-expert-simple n2-uc n2-h5'
            ), n2_('Simple')) . NHtml::tag('div', array(
                'class' => 'n2-expert-bar'
            ), NHtml::tag('div', array(
                'class' => 'n2-expert-dot'
            ), ''))));
    }


    if (count($actions)):
        ?>
        <div class="n2-right n2-top-bar-actions">
            <?php
            foreach ($actions AS $action):
                echo $action;
            endforeach;
            ?>
        </div>
    <?php
    endif;
    ?>
</div>