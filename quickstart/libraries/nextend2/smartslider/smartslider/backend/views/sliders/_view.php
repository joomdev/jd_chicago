<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php


class N2SmartsliderBackendSlidersView extends N2ViewBase
{

    public function renderImportByUploadForm() {

        N2SmartsliderSlidersModel::renderImportByUploadForm();
    }

    public function renderRestoreByUploadForm() {

        N2SmartsliderSlidersModel::renderRestoreByUploadForm();
    }

    public function renderImportFromServerForm() {

        N2SmartsliderSlidersModel::renderImportFromServerForm();
    }
} 