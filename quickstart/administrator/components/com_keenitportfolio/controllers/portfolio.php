<?php
/**
 * @version     2.0.0
 * @package     com_keenitportfolio
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Abdur Rashid <rashid.cse.05@gmail.com> - http://www.keenitsolution.com
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Portfolio controller class.
 */
class KeenitportfolioControllerPortfolio extends JControllerForm
{

    function __construct() {
        $this->view_list = 'portfolios';
        parent::__construct();
    }

}