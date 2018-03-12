<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
if (!class_exists('PclZip')) {
    N2Loader::import('libraries.zip.pclzip');
}

class N2Zip
{

    /**
     * array (size=23)
     * 0 =>
     * array (size=11)
     * 'filename' => string 'akismet/' (length=8)
     * 'stored_filename' => string 'akismet/' (length=8)
     * 'size' => int 0
     * 'compressed_size' => int 0
     * 'mtime' => int 1443138698
     * 'comment' => string '' (length=0)
     * 'folder' => boolean true
     * 'index' => int 0
     * 'status' => string 'ok' (length=2)
     * 'crc' => int 0
     * 'content' => string '' (length=0)
     * 1 =>
     * array (size=11)
     * 'filename' => string 'akismet/akismet.php' (length=19)
     * 'stored_filename' => string 'akismet/akismet.php' (length=19)
     * 'size' => int 2438
     * 'compressed_size' => int 1184
     * 'mtime' => int 1443138698
     * 'comment' => string '' (length=0)
     * 'folder' => boolean false
     * 'index' => int 1
     * 'status' => string 'ok' (length=2)
     * 'crc' => int 1640791883
     * 'content' => string ''
     */
    public static function read($filePath) {

        if (function_exists('mbstring_binary_safe_encoding')) {
            mbstring_binary_safe_encoding();
        }

        $archive = new PclZip($filePath);

        $archive_files = $archive->extract(PCLZIP_OPT_EXTRACT_AS_STRING);

        if (function_exists('reset_mbstring_encoding')) {
            reset_mbstring_encoding();
        }
        return $archive_files;
    }
}