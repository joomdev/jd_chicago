<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
N2Loader::import("libraries.mvc.db");

class N2Model
{

    /**
     * @var N2DBConnectorAbstract
     */
    public $db;

    public function __construct($tableName = null) {

        if (is_null($tableName)) {
            $tableName = get_class();
        }
        $this->db = new N2DBConnector($tableName);

    }

}