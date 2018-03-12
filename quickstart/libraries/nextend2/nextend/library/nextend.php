<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2
{

    public static $version = '2.0.18';
    public static $api = 'http://secure.nextendweb.com/api/api.php';

    public static function api($posts) {
        if (class_exists('JHttp')) {
            $posts_default = array(
                'platform' => N2Platform::getPlatform()
            );

            $client   = new JHttp();
            $response = $client->post(self::$api, $posts + $posts_default, array('Content-Type' => 'multipart/form-data'));
            if ($response->code != '200') {
                N2Message::error(n2_('Unable to contact with the licensing server, please try again later!'));
                return array(
                    'status' => 'ERROR_HANDLED'
                );
            }

            if (isset($response->headers['Content-Type'])) {
                $contentType = $response->headers['Content-Type'];
            }
            $data = $response->body;
        }
    

        if (!isset($data)) {
            if (function_exists('curl_init')) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, self::$api);

                $posts_default = array(
                    'platform' => N2Platform::getPlatform()
                );
                curl_setopt($ch, CURLOPT_POSTFIELDS, $posts + $posts_default);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $data            = curl_exec($ch);
                $contentType     = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
                $error           = curl_error($ch);
                $curlErrorNumber = curl_errno($ch);
                curl_close($ch);

                if ($curlErrorNumber) {
                    N2Message::error($curlErrorNumber . $error);
                    return array(
                        'status' => 'ERROR_HANDLED'
                    );
                }
            } else {
                $posts_default = array(
                    'platform' => N2Platform::getPlatform()
                );

                $opts    = array(
                    'http' => array(
                        'method'  => 'POST',
                        'header'  => 'Content-type: application/x-www-form-urlencoded',
                        'content' => http_build_query($posts + $posts_default)
                    )
                );
                $context = stream_context_create($opts);
                $data    = file_get_contents(self::$api, false, $context);
                if ($data === false) {
                    N2Message::error(n2_('CURL disabled in your php.ini configuration. Please enable it!'));
                    return array(
                        'status' => 'ERROR_HANDLED'
                    );
                }
                $headers = self::parseHeaders($http_response_header);
                if ($headers['status'] != '200') {
                    N2Message::error(n2_('Unable to contact with the licensing server, please try again later!'));
                    return array(
                        'status' => 'ERROR_HANDLED'
                    );
                }
                if (isset($headers['content-type'])) {
                    $contentType = $headers['content-type'];
                }
            }
        }

        switch ($contentType) {
            case 'application/json':
                return json_decode($data, true);
        }
        return $data;
    }

    private static function parseHeaders(array $headers, $header = null) {
        $output = array();
        if ('HTTP' === substr($headers[0], 0, 4)) {
            list(, $output['status'], $output['status_text']) = explode(' ', $headers[0]);
            unset($headers[0]);
        }
        foreach ($headers as $v) {
            $h = preg_split('/:\s*/', $v);
            if (count($h) >= 2) {
                $output[strtolower($h[0])] = $h[1];
            }
        }
        if (null !== $header) {
            if (isset($output[strtolower($header)])) {
                return $output[strtolower($header)];
            }
            return;
        }
        return $output;
    }
}