<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2ZipRead
{

    /*
        zipfile class, for reading or writing .zip files
        See http://www.gamingg.net for more of my work
        Based on tutorial given by John Coggeshall at http://www.zend.com/zend/spotlight/creating-zip-files3.php
        Copyright (C) Joshua Townsend and licensed under the GPL
        Version 1.0
    */
    var $datasec = array(); // array to store compressed data
    var $files = array(); // array of uncompressed files
    var $dirs = array(); // array of directories that have been created already
    var $ctrl_dir = array(); // central directory
    var $eof_ctrl_dir = "\x50\x4b\x05\x06\x00\x00\x00\x00"; //end of Central directory record
    var $old_offset = 0;
    var $basedir = ".";

    function create_dir($name) // Adds a directory to the zip with the name $name
    {
        $name = str_replace("\\", "/", $name);

        $fr = "\x50\x4b\x03\x04";
        $fr .= "\x0a\x00"; // version needed to extract
        $fr .= "\x00\x00"; // general purpose bit flag
        $fr .= "\x00\x00"; // compression method
        $fr .= "\x00\x00\x00\x00"; // last mod time and date

        $fr .= pack("V", 0); // crc32
        $fr .= pack("V", 0); //compressed filesize
        $fr .= pack("V", 0); //uncompressed filesize
        $fr .= pack("v", strlen($name)); //length of pathname
        $fr .= pack("v", 0); //extra field length
        $fr .= $name;
        // end of "local file header" segment

        // no "file data" segment for path

        // "data descriptor" segment (optional but necessary if archive is not served as file)
        $fr .= pack("V", 0); //crc32
        $fr .= pack("V", 0); //compressed filesize
        $fr .= pack("V", 0); //uncompressed filesize

        // add this entry to array
        $this->datasec[] = $fr;

        $new_offset = strlen(implode("", $this->datasec));

        // ext. file attributes mirrors MS-DOS directory attr byte, detailed
        // at http://support.microsoft.com/support/kb/articles/Q125/0/19.asp

        // now add to central record
        $cdrec = "\x50\x4b\x01\x02";
        $cdrec .= "\x00\x00"; // version made by
        $cdrec .= "\x0a\x00"; // version needed to extract
        $cdrec .= "\x00\x00"; // general purpose bit flag
        $cdrec .= "\x00\x00"; // compression method
        $cdrec .= "\x00\x00\x00\x00"; // last mod time and date
        $cdrec .= pack("V", 0); // crc32
        $cdrec .= pack("V", 0); //compressed filesize
        $cdrec .= pack("V", 0); //uncompressed filesize
        $cdrec .= pack("v", strlen($name)); //length of filename
        $cdrec .= pack("v", 0); //extra field length
        $cdrec .= pack("v", 0); //file comment length
        $cdrec .= pack("v", 0); //disk number start
        $cdrec .= pack("v", 0); //internal file attributes
        $cdrec .= pack("V", 16); //external file attributes  - 'directory' bit set

        $cdrec .= pack("V", $this->old_offset); //relative offset of local header
        $this->old_offset = $new_offset;

        $cdrec .= $name;
        // optional extra field, file comment goes here
        // save to array
        $this->ctrl_dir[] = $cdrec;
        $this->dirs[]     = $name;
    }


    function create_file($data, $name) // Adds a file to the path specified by $name with the contents $data
    {
        $name = str_replace("\\", "/", $name);

        $fr = "\x50\x4b\x03\x04";
        $fr .= "\x14\x00"; // version needed to extract
        $fr .= "\x00\x00"; // general purpose bit flag
        $fr .= "\x08\x00"; // compression method
        $fr .= "\x00\x00\x00\x00"; // last mod time and date

        $unc_len = strlen($data);
        $crc     = crc32($data);
        $zdata   = gzcompress($data);
        $zdata   = substr($zdata, 2, -4); // fix crc bug
        $c_len   = strlen($zdata);
        $fr .= pack("V", $crc); // crc32
        $fr .= pack("V", $c_len); //compressed filesize
        $fr .= pack("V", $unc_len); //uncompressed filesize
        $fr .= pack("v", strlen($name)); //length of filename
        $fr .= pack("v", 0); //extra field length
        $fr .= $name;
        // end of "local file header" segment

        // "file data" segment
        $fr .= $zdata;

        // "data descriptor" segment (optional but necessary if archive is not served as file)
        $fr .= pack("V", $crc); // crc32
        $fr .= pack("V", $c_len); // compressed filesize
        $fr .= pack("V", $unc_len); // uncompressed filesize

        // add this entry to array
        $this->datasec[] = $fr;

        $new_offset = strlen(implode("", $this->datasec));

        // now add to central directory record
        $cdrec = "\x50\x4b\x01\x02";
        $cdrec .= "\x00\x00"; // version made by
        $cdrec .= "\x14\x00"; // version needed to extract
        $cdrec .= "\x00\x00"; // general purpose bit flag
        $cdrec .= "\x08\x00"; // compression method
        $cdrec .= "\x00\x00\x00\x00"; // last mod time & date
        $cdrec .= pack("V", $crc); // crc32
        $cdrec .= pack("V", $c_len); //compressed filesize
        $cdrec .= pack("V", $unc_len); //uncompressed filesize
        $cdrec .= pack("v", strlen($name)); //length of filename
        $cdrec .= pack("v", 0); //extra field length
        $cdrec .= pack("v", 0); //file comment length
        $cdrec .= pack("v", 0); //disk number start
        $cdrec .= pack("v", 0); //internal file attributes
        $cdrec .= pack("V", 32); //external file attributes - 'archive' bit set

        $cdrec .= pack("V", $this->old_offset); //relative offset of local header
        $this->old_offset = $new_offset;

        $cdrec .= $name;
        // optional extra field, file comment goes here
        // save to central directory
        $this->ctrl_dir[] = $cdrec;
    }

    function read_zip($name, $isFilePath = true) {
        // Clear current file
        $this->datasec = array();

        if($isFilePath) {
            // File information
            $this->name = $name;
            $this->size = filesize($name);

            // Read file
            $fh   = fopen($name, "rb");
            $data = fread($fh, $this->size);
            fclose($fh);
        }else{
            $data = $name;
        }

        // Break into sections
        $filesecta = explode("\x50\x4b\x05\x06", $data);

        // ZIP Comment
        $unpackeda     = unpack('x16/v1length', $filesecta[1]);
        $this->comment = substr($filesecta[1], 18, $unpackeda['length']);
        $this->comment = str_replace(array(
            "\r\n",
            "\r"
        ), "\n", $this->comment); // CR + LF and CR -> LF

        // Cut entries from the central directory
        $filesecta = explode("\x50\x4b\x01\x02", $data);
        $filesecta = explode("\x50\x4b\x03\x04", $filesecta[0]);
        array_shift($filesecta); // Removes empty entry/signature

        foreach ($filesecta as $data) {
            $unpackeda = unpack("v1version/v1general_purpose/v1compress_method/v1file_time/v1file_date/V1crc/V1size_compressed/V1size_uncompressed/v1filename_length", $data);

            // Check for encryption
            $isencrypted = (($unpackeda['general_purpose'] & 0x0001) ? true : false);

            // Check for value block after compressed data
            if ($unpackeda['general_purpose'] & 0x0008) {
                $unpackeda2 = unpack("V1crc/V1size_compressed/V1size_uncompressed", substr($data, -12));

                $unpackeda['crc']               = $unpackeda2['crc'];
                $unpackeda['size_compressed']   = $unpackeda2['size_uncompressed'];
                $unpackeda['size_uncompressed'] = $unpackeda2['size_uncompressed'];

                unset($unpackeda2);
            }

            $error = "";
            $name  = substr($data, 26, $unpackeda['filename_length']);

            if (substr($name, -1) == "/") // skip directories
            {
                continue;
            }

            $dir  = dirname($name);
            $dir  = ($dir == "." ? "" : $dir);
            $name = basename($name);

            $data = substr($data, 26 + $unpackeda['filename_length']);

            if (strlen($data) != $unpackeda['size_compressed']) {
                $error = "Compressed size is not equal to the value given in header.";
            }

            if ($isencrypted) {
                $error = "Encryption is not supported.";
            } else {
                switch ($unpackeda['compress_method']) {
                    case 0: // Stored
                        // Not compressed, continue
                        break;
                    case 8: // Deflated
                        $data = gzinflate($data);
                        break;
                    case 12: // BZIP2
                        if (!extension_loaded("bz2")) {
                            @dl((strtolower(substr(PHP_OS, 0, 3)) == "win") ? "php_bz2.dll" : "bz2.so");
                        }

                        if (extension_loaded("bz2")) {
                            $data = bzdecompress($data);
                        } else {
                            $error = "Required BZIP2 Extension not available.";
                        }
                        break;
                    default:
                        $error = "Compression method ({$unpackeda['compress_method']}) not supported.";
                }

                if (!$error) {
                    if ($data === false) {
                        $error = "Decompression failed.";
                    } elseif (strlen($data) != $unpackeda['size_uncompressed']) {
                        $error = "File size is not equal to the value given in header.";
                    } elseif (crc32($data) != $unpackeda['crc']) {
                        $error = "CRC32 checksum is not equal to the value given in header.";
                    }
                }
            }

            if (!empty($dir)) {
                if (!isset($this->files[$dir])) {
                    $this->files[$dir] = array();
                }
                $this->files[$dir][$name] = $data;
            } else {
                $this->files[$name] = $data;
            }
        }

        return $this->files;
    }

    function add_file($file, $dir = ".", $file_blacklist = array(), $ext_blacklist = array()) {
        $file = str_replace("\\", "/", $file);
        $dir  = str_replace("\\", "/", $dir);

        if (strpos($file, "/") !== false) {
            $dira = explode("/", "{$dir}/{$file}");
            $file = array_shift($dira);
            $dir  = implode("/", $dira);
            unset($dira);
        }

        while (substr($dir, 0, 2) == "./") {
            $dir = substr($dir, 2);
        }
        while (substr($file, 0, 2) == "./") {
            $file = substr($file, 2);
        }
        if (!in_array($dir, $this->dirs)) {
            if ($dir == ".") {
                $this->create_dir("./");
            }
            $this->dirs[] = $dir;
        }
        if (in_array($file, $file_blacklist)) {
            return true;
        }
        foreach ($ext_blacklist as $ext) {
            if (substr($file, -1 - strlen($ext)) == ".{$ext}") {
                return true;
            }
        }

        $filepath = (($dir && $dir != ".") ? "{$dir}/" : "") . $file;
        if (is_dir("{$this->basedir}/{$filepath}")) {
            $dh = opendir("{$this->basedir}/{$filepath}");
            while (($subfile = readdir($dh)) !== false) {
                if ($subfile != "." && $subfile != "..") {
                    $this->add_file($subfile, $filepath, $file_blacklist, $ext_blacklist);
                }
            }
            closedir($dh);
        } else {
            $this->create_file(implode("", file("{$this->basedir}/{$filepath}")), $filepath);
        }

        return true;
    }


    function zipped_file() // return zipped file contents
    {
        $data    = implode("", $this->datasec);
        $ctrldir = implode("", $this->ctrl_dir);

        return $data . $ctrldir . $this->eof_ctrl_dir . pack("v", sizeof($this->ctrl_dir)) . // total number of entries "on this disk"
        pack("v", sizeof($this->ctrl_dir)) . // total number of entries overall
        pack("V", strlen($ctrldir)) . // size of central dir
        pack("V", strlen($data)) . // offset to start of central dir
        "\x00\x00"; // .zip file comment length
    }

    function recursive_extract($files, $targetFolder) {
        foreach ($files AS $fileName => $file) {
            if (is_array($file)) {
                if (N2Filesystem::createFolder($targetFolder . $fileName . '/')) {
                    $this->recursive_extract($file, $targetFolder . $fileName . '/');
                } else {
                    return false;
                }
            } else {
                if (!N2Filesystem::createFile($targetFolder . $fileName, $file)) {
                    return false;
                }
            }
        }
        return true;
    }
}
