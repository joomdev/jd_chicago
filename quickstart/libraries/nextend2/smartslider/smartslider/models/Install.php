<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php


class N2SmartsliderInstallModel extends N2Model
{

    private static $sql = array(
        "CREATE TABLE IF NOT EXISTS `#__nextend2_smartslider3_generators` (
  `id`     INT(11)      NOT NULL AUTO_INCREMENT,
  `group`  VARCHAR(254) NOT NULL,
  `type`   VARCHAR(254) NOT NULL,
  `params` TEXT         NOT NULL,
  PRIMARY KEY (`id`)
)
  DEFAULT CHARSET = utf8;",
        "CREATE TABLE IF NOT EXISTS `#__nextend2_smartslider3_sliders` (
  `id`     INT(11)      NOT NULL AUTO_INCREMENT,
  `title`  VARCHAR(100) NOT NULL,
  `type`   VARCHAR(30)  NOT NULL,
  `params` MEDIUMTEXT   NOT NULL,
  `time`   DATETIME     NOT NULL,
  PRIMARY KEY (`id`)
)
  DEFAULT CHARSET = utf8;",
        "CREATE TABLE IF NOT EXISTS `#__nextend2_smartslider3_slides` (
  `id`           INT(11)      NOT NULL AUTO_INCREMENT,
  `title`        VARCHAR(200) NOT NULL,
  `slider`       INT(11)      NOT NULL,
  `publish_up`   DATETIME     NOT NULL,
  `publish_down` DATETIME     NOT NULL,
  `published`    TINYINT(1)   NOT NULL,
  `first`        INT(11)      NOT NULL,
  `slide`        LONGTEXT,
  `description`  TEXT         NOT NULL,
  `thumbnail`    VARCHAR(255) NOT NULL,
  `params`       TEXT         NOT NULL,
  `ordering`     INT(11)      NOT NULL,
  `generator_id` INT(11)      NOT NULL,
  PRIMARY KEY (`id`)
)
  DEFAULT CHARSET = utf8;"

    );

    public function install() {
        foreach (self::$sql AS $query) {
            $this->db->query($this->db->parsePrefix($query));
        }
        /*
        $storageDefaults = <<<EODEOD;
        $this->db->query($this->db->parsePrefix($storageDefaults));
        */
        N2Loader::import('install', 'smartslider.platform');
    }
}