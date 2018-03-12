<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

N2Loader::import('libraries.cache.image');

class N2Image extends N2CacheImage
{

    public function __construct($group) {
        parent::__construct($group, true);
    }

    public static function solidColor($group, $color) {
        $cache = new self($group);
        return N2Filesystem::pathToAbsoluteURL($cache->makeCache('png', array(
            $cache,
            '_solidColor'
        ), array($color)));
    }

    protected function _solidColor($filePath, $color) {
        $im    = imagecreatetruecolor(10, 10);
        $rgb   = N2Color::hex2rgb($color);
        $color = imagecolorallocate($im, $rgb[0], $rgb[1], $rgb[2]);
        imagefilledrectangle($im, 0, 0, 10, 10, $color);
        imagepng($im, $filePath);
        imagedestroy($im);
        return $filePath;
    }

    public static function resizeImage($group, $imageUrl, $targetWidth, $targetHeight, $mode = 'cover', $backgroundColor = false, $resizeRemote = false) {
        $originalImageUrl = $imageUrl;
        if ($targetWidth > 0 && $targetHeight > 0 && function_exists('exif_imagetype') && function_exists('imagecreatefrompng')) {

            if (substr($imageUrl, 0, 2) == '//') {
                $imageUrl = parse_url(N2Uri::getBaseuri(), PHP_URL_SCHEME) . ':' . $imageUrl;
            }

            $imageUrl  = N2Uri::relativetoabsolute($imageUrl);
            $imagePath = N2Filesystem::absoluteURLToPath($imageUrl);

            $cache = new self($group);

            if ($imagePath == $imageUrl) {
                // The image is not local
                if (!$resizeRemote) {
                    return $originalImageUrl;
                }

                $pathInfo  = pathinfo(parse_url($imageUrl, PHP_URL_PATH));
                $extension = self::validateExtension($pathInfo['extension']);
                if (!$extension) {
                    return $originalImageUrl;
                }
                return N2Filesystem::pathToAbsoluteURL($cache->makeCache($extension, array(
                    $cache,
                    '_resizeRemoteImage'
                ), array(
                    $extension,
                    $imageUrl,
                    $targetWidth,
                    $targetHeight,
                    $mode,
                    $backgroundColor
                )));

            } else {
                $extension = false;
                $imageType = @exif_imagetype($imagePath);
                switch ($imageType) {
                    case IMAGETYPE_JPEG:
                        $extension = 'jpg';
                        break;
                    case IMAGETYPE_PNG:
                        $extension = 'png';
                        break;
                }
                if (!$extension) {
                    throw new Exception('Filtype of the image is not supported: #' . $imageType . ' code  ' . $imagePath);
                }
                return N2Filesystem::pathToAbsoluteURL($cache->makeCache($extension, array(
                    $cache,
                    '_resizeImage'
                ), array(
                    $extension,
                    $imagePath,
                    $targetWidth,
                    $targetHeight,
                    $mode,
                    $backgroundColor,
                    filemtime($imagePath)
                )));
            }
        }
    }

    protected function _resizeRemoteImage($filePath, $extension, $imageUrl, $targetWidth, $targetHeight, $mode, $backgroundColor) {
        $this->_resizeImage($filePath, $extension, $imageUrl, $targetWidth, $targetHeight, $mode, $backgroundColor);
    }

    protected function _resizeImage($filePath, $extension, $imagePath, $targetWidth, $targetHeight, $mode, $backgroundColor, $originalFileModified = null) {
        if ($extension == 'png') {
            $image = @imagecreatefrompng($imagePath);
        } else if ($extension == 'jpg') {
            $image = @imagecreatefromjpeg($imagePath);
            if (function_exists("exif_read_data")) {
                $exif = exif_read_data($imagePath);

                $rotated = $this->getOrientation($exif, $image);
                if ($rotated) {
                    imagedestroy($image);
                    $image = $rotated;
                }
            }
        }

        if ($image) {
            $originalWidth  = imagesx($image);
            $originalHeight = imagesy($image);
            if ($rotated || $originalWidth != $targetWidth || $originalHeight != $targetHeight) {
                $newImage = imagecreatetruecolor($targetWidth, $targetHeight);
                if ($extension == 'png') {
                    imagesavealpha($newImage, true);
                    imagealphablending($newImage, false);
                    $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                    imagefilledrectangle($image, 0, 0, $targetWidth, $targetHeight, $transparent);
                } else if ($extension == 'jpg' && $backgroundColor) {
                    $rgb        = N2Color::hex2rgb($backgroundColor);
                    $background = imagecolorallocate($newImage, $rgb[0], $rgb[1], $rgb[2]);
                    imagefilledrectangle($newImage, 0, 0, $targetWidth, $targetHeight, $background);
                }

                list($dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h) = $this->imageMode($targetWidth, $targetHeight, $originalWidth, $originalHeight, $mode);
                imagecopyresampled($newImage, $image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
                imagedestroy($image);

            } else {
                $newImage = $image;
            }

            if ($extension == 'png') {
                imagepng($newImage, $filePath);
            } else if ($extension == 'jpg') {
                imagejpeg($newImage, $filePath, 100);
            }
            imagedestroy($newImage);
            return true;
        }

        throw new Exception('Unable to resize image: ' . $imagePath);
    }

    public static function scaleImage($group, $imageUrl, $scale = 1, $resizeRemote = false) {
        $originalImageUrl = $imageUrl;
        $imageUrl         = N2ImageHelper::fixed($imageUrl);
        if ($scale > 0 && function_exists('exif_imagetype') && function_exists('imagecreatefrompng')) {

            if (substr($imageUrl, 0, 2) == '//') {
                $imageUrl = parse_url(N2Uri::getBaseuri(), PHP_URL_SCHEME) . ':' . $imageUrl;
            }

            $imageUrl  = N2Uri::relativetoabsolute($imageUrl);
            $imagePath = N2Filesystem::absoluteURLToPath($imageUrl);

            $cache = new self($group);
            if ($imagePath == $imageUrl) {
                // The image is not local
                if (!$resizeRemote) {
                    return $originalImageUrl;
                }

                $pathInfo  = pathinfo(parse_url($imageUrl, PHP_URL_PATH));
                $extension = self::validateExtension($pathInfo['extension']);
                if (!$extension) {
                    return $originalImageUrl;
                }
                return N2ImageHelper::dynamic(N2Filesystem::pathToAbsoluteURL($cache->makeCache($extension, array(
                    $cache,
                    '_scaleRemoteImage'
                ), array(
                    $extension,
                    $imageUrl,
                    $scale
                ))));

            } else {
                $extension = false;
                $imageType = @exif_imagetype($imagePath);
                switch ($imageType) {
                    case IMAGETYPE_JPEG:
                        $extension = 'jpg';
                        break;
                    case IMAGETYPE_PNG:
                        $extension = 'png';
                        $fp        = fopen($imagePath, 'r');
                        fseek($fp, 25);
                        $data = fgets($fp, 2);
                        fclose($fp);
                        if (ord($data) == 3) {
                            // GD cannot resize palette PNG so we return the original image
                            return $originalImageUrl;
                        }
                        break;
                }
                if (!$extension) {
                    throw new Exception('Filtype of the image is not supported: #' . $imageType . ' code  ' . $imagePath);
                }
                return N2ImageHelper::dynamic(N2Filesystem::pathToAbsoluteURL($cache->makeCache($extension, array(
                    $cache,
                    '_scaleImage'
                ), array(
                    $extension,
                    $imagePath,
                    $scale,
                    filemtime($imagePath)
                ))));
            }
        }
    }

    protected function _scaleRemoteImage($filePath, $extension, $imageUrl, $scale) {
        $this->_scaleImage($filePath, $extension, $imageUrl, $scale);
    }

    protected function _scaleImage($filePath, $extension, $imagePath, $scale, $originalFileModified = null) {
        if ($extension == 'png') {
            $image = @imagecreatefrompng($imagePath);
        } else if ($extension == 'jpg') {
            $image = @imagecreatefromjpeg($imagePath);
            if (function_exists("exif_read_data")) {
                $exif = exif_read_data($imagePath);

                $rotated = $this->getOrientation($exif, $image);
                if ($rotated) {
                    imagedestroy($image);
                    $image = $rotated;
                }
            }
        }

        if ($image) {
            $originalWidth  = imagesx($image);
            $originalHeight = imagesy($image);
            $targetWidth    = $originalWidth * $scale;
            $targetHeight   = $originalHeight * $scale;
            if ((isset($rotated) && $rotated) || $originalWidth != $targetWidth || $originalHeight != $targetHeight) {
                $newImage = imagecreatetruecolor($targetWidth, $targetHeight);
                if ($extension == 'png') {
                    imagesavealpha($newImage, true);
                    imagealphablending($newImage, false);
                    $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                    imagefilledrectangle($image, 0, 0, $targetWidth, $targetHeight, $transparent);
                }

                list($dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h) = $this->imageMode($targetWidth, $targetHeight, $originalWidth, $originalHeight);
                imagecopyresampled($newImage, $image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
                imagedestroy($image);

            } else {
                $newImage = $image;
            }

            if ($extension == 'png') {
                imagepng($newImage, $filePath);
            } else if ($extension == 'jpg') {
                imagejpeg($newImage, $filePath, 100);
            }
            imagedestroy($newImage);
            return true;
        }

        throw new Exception('Unable to resize image: ' . $imagePath);
    }

    private function getOrientation($exif, $image) {
        if ($exif && !empty($exif['Orientation'])) {
            $rotated = false;
            switch ($exif['Orientation']) {
                case 3:
                    $rotated = imagerotate($image, 180, 0);
                    break;

                case 6:
                    $rotated = imagerotate($image, -90, 0);
                    break;

                case 8:
                    $rotated = imagerotate($image, 90, 0);
                    break;
            }
            return $rotated;
        }
        return false;
    }

    private function imageMode($width, $height, $originalWidth, $OriginalHeight, $mode = 'cover') {
        $dst_x           = 0;
        $dst_y           = 0;
        $src_x           = 0;
        $src_y           = 0;
        $dst_w           = $width;
        $dst_h           = $height;
        $src_w           = $originalWidth;
        $src_h           = $OriginalHeight;
        $horizontalRatio = $width / $originalWidth;
        $verticalRatio   = $height / $OriginalHeight;
        if ($mode == 'cover') {
            if ($horizontalRatio > $verticalRatio) {
                $new_h = $horizontalRatio * $OriginalHeight;
                $dst_y = ($height - $new_h) / 2;
                $dst_h = $new_h;
            } else {
                $new_w = $verticalRatio * $originalWidth;
                $dst_x = ($width - $new_w) / 2;
                $dst_w = $new_w;
            }
        } else if ($mode == 'contain') {
            if ($horizontalRatio < $verticalRatio) {
                $new_h = $horizontalRatio * $OriginalHeight;
                $dst_y = ($height - $new_h) / 2;
                $dst_h = $new_h;
            } else {
                $new_w = $verticalRatio * $originalWidth;
                $dst_x = ($width - $new_w) / 2;
                $dst_w = $new_w;
            }
        }
        return array(
            $dst_x,
            $dst_y,
            $src_x,
            $src_y,
            $dst_w,
            $dst_h,
            $src_w,
            $src_h
        );
    }

    private static function validateExtension($extension) {
        static $validExtensions = array(
            'png'  => 'png',
            'jpg'  => 'jpg',
            'jpeg' => 'jpg'
        );
        $extension = strtolower($extension);
        if (isset($validExtensions[$extension])) {
            return $validExtensions[$extension];
        }
        return false;
    }

    public static function base64Transparent() {
        return 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
    }

    public static function base64($imagePath, $image) {
        $pathInfo  = pathinfo(parse_url($imagePath, PHP_URL_PATH));
        $extension = self::validateExtension($pathInfo['extension']);
        if ($extension) {
            return 'data:image/' . $extension . ';base64,' . base64_encode(N2Filesystem::readFile($imagePath));
        }
        return N2ImageHelper::fixed($image);
    }
}

if (!function_exists('exif_imagetype')) {

    function exif_imagetype($filename) {
        if ((list($width, $height, $type, $attr) = getimagesize($filename)) !== false) {
            return $type;
        }
        return false;
    }
}