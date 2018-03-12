<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><h2>Cache</h2>
<?php
$buttons = array();

$buttons[] = array(
    'title'       => n2_('Clear sliders'),
    'htmlOptions' => array(
        'href' => $this->appType->router->createUrl(array(
                'settings/cache',
                array(
                    'refreshcache' => 1
                )
            ))
    ),
    'iconclass'   => 'nii nii-24x42 nii-global-action-icon nii-refresh'
);

$buttons[] = array(
    'title'       => n2_('Clear generators'),
    'htmlOptions' => array(
        'href' => $this->appType->router->createUrl(array(
                'settings/cache',
                array(
                    'refreshcache' => 2
                )
            ))
    ),
    'iconclass'   => 'nii nii-24x42 nii-global-action-icon nii-refresh'
);