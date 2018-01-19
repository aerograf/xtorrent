<?php

/*
    bencode.reader.php
    Contains classes and functions to assist in the reading and processing of bencoded files and strings.
    This is a component of a Bitorrent tracker/torrent library.
    This library is free to redistribute, but I'd appreciate if you left the credit in if you use it.
    Written by Greg Poole | m4dm4n@gmail.com | http://m4dm4n.homelinux.net:8086
*/

class BEncodeReader
{
    public $data;
    
    public $pointer = 0;
    
    public $error = false;
    
    public function __construct($filename = null)
    {
        ini_set('allow_url_fopen', true);
        
        if (is_null($filename)) {
            return;
        }
    
        if (!is_file($filename)) {
            trigger_error("Could not create BEncodeReader for {$filename}: it does not exist", E_USER_WARNING);
            return;
        }

        $h = @fopen($filename, 'rb');
        if ($h === false) {
            trigger_error("Could not create BEncodeReader for {$filename}: failed to open for reading", E_USER_WARNING);
            return;
        }

        $filesize = @filesize($filename);
        if ($filesize === false) {
            trigger_error("Could not create BEncodeReader for {$filename}: the file is empty", E_USER_WARNING);
            return;
        }

        $this->data = @fread($h, $filesize);

        if ($this->data === false) {
            trigger_error("Error creating BEncodeReader for {$filename}: error reading from file", E_USER_WARNING);
        }

        @fclose($h);
    }

    // Read the next part in the current file
    public function readNext()
    {
        if (!isset($this->data)) {
            return false;
        }

        if ($this->data[$this->pointer] == 'e') {
            // Except in the case of an error or malformed data string, the letter e will mark the end of anything we've been reading, so we move
            // the pointer and retreat.
            $this->pointer++;
            return false;
        }
        if ($this->data[$this->pointer] == 'd') {
            // d marks the start of a dictionary, which is essentially an associative array
            $start = $this->pointer;
            $this->pointer++;
            $dictionary = [];
            $current    = false;
            while (($value = $this->readNext()) !== false) {
                if ($current === false) {
                    $current = $value;
                } else {
                    $dictionary[$current] = $value;
                    $current = false;
                }
            }

            if (count($dictionary) == 0 || $this->error) {
                $this->error = true;
                return false;
            }

            $end = $this->pointer;
            $dictionary['hash'] = pack('H*', sha1(substr($this->data, $start, $end - $start)));
            return $dictionary;
        } elseif ($this->data[$this->pointer] == 'l') {
            // An l indicates the start of a list, which is essentially an array, so we will read it as such
            $this->pointer++;
            $list = [];
            for ($i=0;($value = $this->readNext()) !== false;$i++) {
                $list[$i] = $value;
            }

            if (count($list) == 0 || $this->error) {
                return false;
            }

            return $list;
        } elseif ($this->data[$this->pointer] == 'i') {
            // The following data is an integer, so it will be read until the next "e", after which it will be returned
            $this->pointer++;

            $endPosition = strpos($this->data, 'e', $this->pointer);

            // A failure to find an endpoint within a resonable distance means that this is not a valid integer definition.
            if ($endPosition === false || ($endPosition - $this->pointer) > 10) {
                $this->error = true;
                return false;
            }

            $readLength = ($endPosition - $this->pointer);
            $int = substr($this->data, $this->pointer, $readLength);
            $this->pointer += $readLength + 1;
            return $int;
        } else {
            $nextColon = strpos($this->data, ':', $this->pointer);

            // The only thing possible here is a string definition, which is ###:string. Without a colon,
            // this is not a string definition and we can assume the file is broken. I'm also including
            // a check to make sure that colons over 5 characters away will be ignored to prevent a
            // broken file from locking up the system.
            if ($nextColon === false || ($nextColon - $this->pointer) > 5) {
                $this->error = true;
                return false;
            }

            $length = substr($this->data, $this->pointer, $nextColon);
            $readLength = ($nextColon - $this->pointer);
            $this->pointer += $readLength + 1;
            $string = substr($this->data, $this->pointer, $length);
            $this->pointer += strlen($string);
            return $string;
        }
    }
}
