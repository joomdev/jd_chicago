<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2Platform
{

    public static $isAdmin = false;

    public static $hasPosts = true, $isJoomla = false, $isWordpress = false, $isMagento = false, $isNative = false;

    public static $name;

    public static function init() {
        self::$isJoomla = JVERSION;
        if (JFactory::getApplication()
                    ->isAdmin()
        ) {
            self::$isAdmin = true;
        }
    }

    public static function getPlatform() {
        return 'joomla';
    }

    public static function getPlatformName() {
        return 'Joomla';
    }

    public static function getDate() {
        return JFactory::getDate()
                       ->toSql();
    }

    public static function getTime() {
        return JFactory::getDate()
                       ->toUnix();
    }

    public static function getPublicDir() {
        if(defined('JPATH_MEDIA')){
            return JPATH_SITE . JPATH_MEDIA;
        }
        return JPATH_SITE . '/media';
    }

    public static function adminHideCSS() {
        echo '
            /*
            Joomla 3
            */

            .navbar{
                display: none;
            }

            .container-fluid{
                padding: 0;
            }

            .admin #content{
                margin: 0;
            }

            /**
            Joomla 2.5
            */
            body,
            #element-box,
            div#element-box div.m{
              margin: 0;
              padding: 0;
            }
            #border-top,
            #header-box{
                display: none;
            }

            #content-box{
              border: 0;
              width: 100%;
            }

            #element-box div.m{
                border: 0;
                background: transparent;
            }
        ';
    }

    public static function updateFromZip($fileRaw, $updateInfo) {
        N2Loader::import('libraries.zip.zip_read');

        $tmpHandle = tmpfile();
        fwrite($tmpHandle, $fileRaw);
        $metaData    = stream_get_meta_data($tmpHandle);
        $tmpFilename = $metaData['uri'];
        $zip         = new N2ZipRead();
        $files       = $zip->read_zip($tmpFilename);

        $updateFolder = N2Filesystem::getNotWebCachePath() . '/update/';
        $zip->recursive_extract($files, $updateFolder);
        fclose($tmpHandle);

        $installer = JInstaller::getInstance();
        $installer->setOverwrite(true);
        if (!$installer->install($updateFolder)) {
            N2Filesystem::deleteFolder($updateFolder);
            return false;
        }
        N2Filesystem::deleteFolder($updateFolder);
        return true;
    }

}

N2Platform::init();
