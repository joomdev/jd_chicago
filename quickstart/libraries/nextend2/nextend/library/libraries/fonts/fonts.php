<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2Fonts
{

    private static $config;

    public static function loadSettings() {
        static $inited;
        if (!$inited) {
            $inited       = true;
            self::$config = array(
                'default-family'  => n2_x('Montserrat,Arial', 'Default font'),
                'preset-families' => n2_x("'Montserrat',Arial\n'Pacifico',Arial\n'Open Sans',Arial\n'Lato',Arial\n'Bevan',Arial\n'Oxygen',Arial\n'Pt Sans',Arial\n'Average',Arial\n'Roboto',Arial\n'Roboto Slab',Arial\n'Oswald',Arial\n'Droid Sans',Arial\n'Raleway',Arial\n'Lobster',Arial\n'Titillium Web',Arial\n'Cabin',Arial\n'Varela Round',Arial\n'Vollkorn',Arial\n'Quicksand',Arial\n'Source Sans Pro',Arial\n'Asap',Arial\n'Merriweather',Arial", 'Default font family list'),
                'plugins'         => array()
            );
            foreach (N2StorageSectionAdmin::getAll('system', 'fonts') AS $data) {
                self::$config[$data['referencekey']] = $data['value'];
            }
            self::$config['plugins'] = new N2Data(self::$config['plugins'], true);
        }
        return self::$config;
    }

    public static function storeSettings($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (isset(self::$config[$key])) {
                    self::$config[$key] = $value;
                    N2StorageSectionAdmin::set('system', 'fonts', $key, $value, 1, 1);
                    unset($data[$key]);
                }
            }
            if (count($data)) {
                self::$config['plugins'] = new N2Data($data);
                N2StorageSectionAdmin::set('system', 'fonts', 'plugins', self::$config['plugins']->toJSON(), 1, 1);

            }
            return true;
        }
        return false;
    }

}

if (class_exists('N2FontRenderer', false)) {
    $fontSettings                = N2Fonts::loadSettings();
    N2FontRenderer::$defaultFont = $fontSettings['default-family'];
}