<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class NextendImageFallBack
{

    static public function findImage($s) {
        preg_match_all('/(<img.*?src=[\'"](.*?)[\'"][^>]*>)|(background(-image)??\s*?:.*?url\((["|\']?)?(.+?)(["|\']?)?\))/i', $s, $r);
        if (isset($r[2]) && !empty($r[2][0])) {
            $s = $r[2][0];
        } else if (isset($r[6]) && !empty($r[6][0])) {
            $s = trim($r[6][0], "'\" \t\n\r\0\x0B");
        } else {
            $s = '';
        }
        return $s;
    }

    static public function fallback($root, $imageVars, $textVars = array()) {
        $return = '';
        if (is_array($imageVars)) {
            foreach ($imageVars as $image) {
                if (!empty($image)) {
                    $return = N2ImageHelper::dynamic($root . $image);
                    break;
                }
            }
            if ($return == '' && !empty($textVars)) {
                foreach ($textVars as $text) {
                    $imageInText = self::findImage($text);
                    if (!empty($imageInText)) {
                        $return = N2ImageHelper::dynamic($root . $imageInText);
                        if ($return != '$/') {
                            break;
                        } else {
                            $return = '';
                        }
                    }
                }
            }
            if ($return != '') {
                if (strpos($return, '$/http:') !== false || strpos($return, '$/https:') !== false) {
                    $return = substr($return, 2);
                } else if (strpos($return, '$http:') !== false || strpos($return, '$https:') !== false || strpos($return, '$//') !== false) {
                    $return = substr($return, 1);
                }
            }
        }
        return $return;
    }
}