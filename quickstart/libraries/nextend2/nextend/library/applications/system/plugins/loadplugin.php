<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
defined('N2LIBRARY') or die();

$mdir = dirname(__FILE__).DIRECTORY_SEPARATOR;
foreach(N2Filesystem::folders($mdir) AS $mfolder){
    $mfile = $mdir.$mfolder.DIRECTORY_SEPARATOR.'loadplugin.php';
    if(N2Filesystem::fileexists($mfile)){
        require_once($mfile);
    }
}
