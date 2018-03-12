<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
echo NHtml::openTag('dl', array('class' => $class . " n2-h3"));

if (isset($dl) && count($dl)) {

    foreach ($dl as $dlRow) {

        //BEGIN <DT>
        if (!isset($dlRow['options'])) $dlRow['options'] = array();
        echo NHtml::openTag('dt', $dlRow['options'] + array('class' => $dlRow['class']));
        if (isset($dlRow["linkOptions"])) {
            echo NHtml::tag('a', (isset($dlRow['linkOptions']) ? $dlRow['linkOptions'] : array()), $dlRow['title']);
        } elseif (isset($dlRow["link"])) {
            echo NHtml::tag('a', array('href' => $dlRow['link']), $dlRow['title']);
        } else {
            echo NHtml::tag('div', array(), $dlRow['title']);
        }

        if (!empty($dlRow['actions'])) {
            echo NHtml::tag('span', array('class' => 'n2-actions'), $dlRow['actions']);
        }

        if (!empty($dlRow['after'])) echo $dlRow['after'];
        echo NHtml::closeTag('dt');

        echo NHtml::openTag('dd', array('class' => $dlRow['class']));

        if (!empty($dlRow["preUl"])) {
            echo $dlRow["preUl"];
        }

        /**
         * @see Listn
         */
        if (!empty($dlRow["ul"])) {
            echo $this->widget->init('listn', array('ul' => $dlRow["ul"]));
        }
        echo NHtml::closeTag('dd');
    }

}
echo NHtml::closeTag('dl');
?>