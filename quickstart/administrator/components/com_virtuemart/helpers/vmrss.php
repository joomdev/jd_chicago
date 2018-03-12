<?php
/**
 * RSS helper class
 *
 * @package	VirtueMart
 * @subpackage Helpers
 * @author Valerie Isaksen
 * @copyright Copyright (c) 2014 VirtueMart Team and author. All rights reserved.
 */
defined('_JEXEC') or die('Restricted access');
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
//defined('_JEXEC') or define('_JEXEC', 1);


class vmRSS{

	/**
	 * Get cached feed
	 * @author valerie isaksen
	 * @param $rssUrl
	 * @param $max
	 * @param $cache_time in minutes
	 * @return mixed
	 */
	static public function getCPsRssFeed($rssUrl,$max, $cache_time=2880) {  // 2880 = 2days

		$cache = JFactory::getCache ('com_virtuemart_rss');

		$cache->setLifeTime($cache_time);
		$cache->setCaching (1);
		$feeds = $cache->call (array('vmRSS', 'getRssFeed'), $rssUrl, $max, $cache_time);

		return $feeds;
	}

	/**
	 * @author Valerie Isaksen
	 * Returns the RSS feed from Extensions.virtuemart.net
	 * @return mixed
	 */
	public static $extFeeds = false;
	static public function getExtensionsRssFeed($items =15, $cache_time = 2880) {
		if (empty(self::$extFeeds)) {
			try {
				self::$extFeeds = self::getCPsRssFeed( "http://extensions.virtuemart.net/?format=feed&type=rss", $items,$cache_time );
				//self::$extFeeds =  self::getRssFeed("http://extensions.virtuemart.net/?format=feed&type=rss", 15);
			} catch (Exception $e) {
				echo 'Where not able to parse extension feed';
			}
		}
		return self::$extFeeds;
	}

	/**
	 * @author Valerie Isaksen
	 * Returns the RSS feed from virtuemart.net
	 * @return mixed
	 */
	public static $vmFeeds = false;
	static public function getVirtueMartRssFeed() {
 		if (empty(self::$vmFeeds)) {
			try {
				self::$vmFeeds =  self::getCPsRssFeed("http://virtuemart.net/news/list-all-news?format=feed&type=rss", 5, 240);
			} catch (Exception $e) {
				echo 'Where not able to parse news feed';
			}
		}
		return self::$vmFeeds;
	}

	/**
	 * @param $rssURL
	 * @param $max
	 * @return array|bool
	 */
	static public function getRssFeed($rssURL, $max, $cache_time) {

		//if (JVM_VERSION < 3){
			$erRep = VmConfig::setErrorReporting(false,true);
			jimport('simplepie.simplepie');
			$rssFeed = new SimplePie($rssURL);

			$feeds = array();
			$count = $rssFeed->get_item_quantity();
			$limit=min($max,$count);
			for ($i = 0; $i < $limit; $i++) {
				$feed = new StdClass();
				$item = $rssFeed->get_item($i);
				$feed->link = $item->get_link();
				$feed->title = $item->get_title();
				$feed->description = $item->get_description();
				$feeds[] = $feed;
			}

			if($erRep[0]) ini_set('display_errors', $erRep[0]);
			if($erRep[1]) error_reporting($erRep[1]);
			return $feeds;

		/*} else {
			jimport('joomla.feed.factory');
			$feed = new JFeedFactory;
			$rssFeed = $feed->getFeed($rssURL,$cache_time);

			if (empty($rssFeed) or !is_object($rssFeed)) return false;

			for ($i = 0; $i < $max; $i++) {
				if (!$rssFeed->offsetExists($i)) {
					break;
				}
				$feed = new StdClass();
				$uri = (!empty($rssFeed[$i]->uri) || !is_null($rssFeed[$i]->uri)) ? $rssFeed[$i]->uri : $rssFeed[$i]->guid;
				$text = !empty($rssFeed[$i]->content) || !is_null($rssFeed[$i]->content) ? $rssFeed[$i]->content : $rssFeed[$i]->description;
				$feed->link = $uri;
				$feed->title = $rssFeed[$i]->title;
				$feed->description = $text;
				$feeds[] = $feed;
			}
			return $feeds;
		}*/

	}
}


// pure php no closing tag
