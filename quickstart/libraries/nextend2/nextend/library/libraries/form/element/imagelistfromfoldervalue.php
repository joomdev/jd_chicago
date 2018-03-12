<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
N2Loader::import('libraries.form.element.imagelistfromfolder');

class N2ElementImageListFromFolderValue extends N2ElementImageListFromFolder
{

    function generateOptions(&$xml) {
        $this->values = array();
        $html         = '';
        foreach ($xml->option AS $option) {
            $v     = N2XmlHelper::getAttribute($option, 'value');
            $image = N2Uri::pathToUri($v);

            $selected = $this->isSelected($this->parseValue($v));

            if ($v != -1) {
                $this->values[] = $this->parseValue($image);
                $html .= NHtml::openTag("div", array("class" => "n2-radio-option n2-imagelist-option" . ($selected ? ' n2-active' : '')));
                $html .= NHtml::image($image, (string)$option);
                $html .= NHtml::closeTag("div");
            } else {
                $this->values[] = -1;
                $html .= NHtml::tag("div", array("class" => "n2-radio-option" . ($selected ? ' n2-active' : '')), ((string)$option));
            }
        }

        return $html;
    }

    function parseValue($image) {
        return pathinfo($image, PATHINFO_FILENAME);
    }
}