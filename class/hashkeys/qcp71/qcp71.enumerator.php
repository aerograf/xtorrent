<?php
// $Id: qcp71.enumerator.php 1.1.0 - QCP71 2009-08-15 9:22:20 wishcraft $
//  ------------------------------------------------------------------------ //
//                    CLORA - Chronolabs Australia                           //
//                         Copyright (c) 2009                                //
//                   <http://www.chronolabs.org.au/>                         //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the SDPL Source Directive Public Licence           //
//  as published by Chronolabs Australia; either version 2 of the License,   //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Chronolab Australia        //
//  Chronolabs International PO BOX 699, DULWICH HILL, NSW, 2203, Australia  //
//  ------------------------------------------------------------------------ //

if (!class_exists('qcp71_enumerator')) {
    error_reporting(0);
    
    class qcp71_enumerator extends qcp71
    {
        public $elekey;
        public $base;
        public $len;
        
        public function __construct($base, $len)
        {
            @$this->len = $len;
            @$this->setElements($base);
        }
    
        private function setElements($base)
        {
            @$this->base = $base;
            @$this->elekey = [];

            foreach ($base->base as $key => $data) {
                if (1 == strlen((string)$data)) {
                    if (5 == strlen(bindec(ord($data)))) {
                        $offset = [
                            'ice' => (int)substr(decbin(ord($data)), 5, 1),
                            'icd' => (int)substr(decbin(ord($data)), 4, 1),
                            'icc' => (int)substr(decbin(ord($data)), 3, 1),
                            'icb' => (int)substr(decbin(ord($data)), 2, 1),
                            'ica' => (int)substr(decbin(ord($data)), 1, 1)];
                        if (1 == substr(decbin(ord($data)), 5, 1)) {
                            $offset['icf'] = 0;
                        } else {
                            $offset['icf'] = 1;
                        }
                    } elseif (6 == strlen(decbin(ord($data)))) {
                        $offset = [
                            'icf' => (int)substr(decbin(ord($data)), 6, 1),
                            'ice' => (int)substr(decbin(ord($data)), 5, 1),
                            'icd' => (int)substr(decbin(ord($data)), 4, 1),
                            'icc' => (int)substr(decbin(ord($data)), 3, 1),
                            'icb' => (int)substr(decbin(ord($data)), 2, 1),
                            'ica' => (int)substr(decbin(ord($data)), 1, 1)];
                    } elseif (7 == strlen(decbin(ord($data)))) {
                        $offset = [
                            'ica' => (int)substr(decbin(ord($data)), 6, 1),
                            'icb' => (int)substr(decbin(ord($data)), 5, 1),
                            'icc' => (int)substr(decbin(ord($data)), 4, 1),
                            'icd' => (int)substr(decbin(ord($data)), 3, 1),
                            'ice' => (int)substr(decbin(ord($data)), 2, 1),
                            'icf' => (int)substr(decbin(ord($data)), 1, 1)];
                    }
                } else {
                    $offset = [
                        'ica' => (int)substr(decbin(ord(substr($key, strlen($key) - 1, 1))), 6, 1),
                        'icb' => (int)substr(decbin(ord(substr($key, strlen($key) - 1, 1))), 5, 1),
                        'icc' => (int)substr(decbin(ord(substr($key, strlen($key) - 1, 1))), 4, 1),
                        'icd' => (int)substr(decbin(ord(substr($key, strlen($key) - 1, 1))), 2, 1),
                        'ice' => (int)substr(decbin(ord(substr($key, strlen($key) - 1, 1))), 1, 1),
                        'icf' => (int)substr(decbin(ord(substr($key, strlen($key) - 1, 1))), 0, 1)];
                }
                
                if (7 == strlen(decbin(ord($data)))) {
                    if (1 == strlen($data)) {
                        $cycle = ['icf', 'ice', 'icd', 'icc', 'icb', 'ica'];
                        foreach ($cycle as $element) {
                            if (false == $done) {
                                if ('0' == $offset[$element]) {
                                    if ('' != $prev_ele) {
                                        if ('1' == $offset[$prev_ele]) {
                                            $offset[$prev_ele] = '0';
                                        } else {
                                            $offset[$prev_ele] = '1';
                                        }
                                    }
                                    $offset[$element]= '1';
                                    $done=true;
                                }
                            }
                        }
                    } else {
                        $cycle = ['ica', 'icb', 'icc', 'icd', 'ice', 'icf'];
                        foreach ($cycle as $element) {
                            if (false == $done) {
                                if ('0' == $offset[$element]) {
                                    if ('' != $prev_ele) {
                                        if ('1' == $offset[$prev_ele]) {
                                            $offset[$prev_ele] = '0';
                                        } else {
                                            $offset[$prev_ele] = '1';
                                        }
                                    }
                                    $offset[$element]= '1';
                                    $done=true;
                                }
                            }
                        }
                    }
                }
                $done=false;
                if (1 == strlen($data)) {
                    @$this->elekey[$key] = [
                        'key'    => $data,
                        'bin'    => decbin(ord($data)),
                        'offset' => $offset,
                        'flip'   => 0];
                } else {
                    @$this->elekey[$key] = [
                        'key'    => $data,
                        'bin'    => decbin(ord($data)),
                        'offset' => $offset,
                        'flip'   => 1];
                }
            }
        }
    
        private function getBytePos($char)
        {
            return floor((ord($char)+1)/4);
        }
        
        public function enum_calc($char, $enum_calc, $debug=false)
        {
            static $flip;
            
            foreach ($enum_calc as $key => $value) {
                ${$key} = $value;
            }
            
            static $charnum;
            $charnum++;
            if ($charnum>3) {
                $charnum=1;
            }
            
            $nx_key.= $char;
            
            if ($this->len>15) {
                if (strlen($nx_key)>$this->len) {
                    $nx_key = substr($nx_key, strlen($nx_key)/($charnum+1), strlen($nx_key) - (strlen($nx_key)/($charnum+1))).substr($nx_key, 1, strlen($nx_key)-(strlen($nx_key) - (strlen($nx_key)/($charnum+1))));
                }
            } else {
                if (strlen($nx_key)>32) {
                    $nx_key = substr($nx_key, strlen($nx_key)/($charnum+1), strlen($nx_key) - (strlen($nx_key)/($charnum+1))).substr($nx_key, 1, strlen($nx_key)-(strlen($nx_key) - (strlen($nx_key)/($charnum+1))));
                }
            }
            
            if (0 == $this->elekey[$this->getBytePos($char)]['flip']) {
                $ica = $this->elekey[$this->getBytePos($char)]['offset']['ica'];
                $icb = $this->elekey[$this->getBytePos($char)]['offset']['icb'];
                $icc = $this->elekey[$this->getBytePos($char)]['offset']['icc'];
                $icd = $this->elekey[$this->getBytePos($char)]['offset']['icd'];
                $ice = $this->elekey[$this->getBytePos($char)]['offset']['ice'];
                $icf = $this->elekey[$this->getBytePos($char)]['offset']['icf'];
            } else {
                if (1 == $charnum) {
                    $icf = $this->elekey[$this->getBytePos($char)]['offset']['ica'];
                    $ice = $this->elekey[$this->getBytePos($char)]['offset']['icb'];
                    $icd = $this->elekey[$this->getBytePos($char)]['offset']['icc'];
                    $icc = $this->elekey[$this->getBytePos($char)]['offset']['icd'];
                    $icb = $this->elekey[$this->getBytePos($char)]['offset']['ice'];
                    $ica = $this->elekey[$this->getBytePos($char)]['offset']['icf'];
                } elseif (2 == $charnum) {
                    $icf = $this->elekey[$this->getBytePos($char)]['offset']['ica'];
                    $ice = $this->elekey[$this->getBytePos($char)]['offset']['icb'];
                    $icd = $this->elekey[$this->getBytePos($char)]['offset']['icc'];
                    $icc = $this->elekey[$this->getBytePos($char)]['offset']['icf'];
                    $icb = $this->elekey[$this->getBytePos($char)]['offset']['ice'];
                    $ica = $this->elekey[$this->getBytePos($char)]['offset']['icd'];
                } else {
                    $icf = $this->elekey[$this->getBytePos($char)]['offset']['icc'];
                    $ice = $this->elekey[$this->getBytePos($char)]['offset']['icb'];
                    $icd = $this->elekey[$this->getBytePos($char)]['offset']['ica'];
                    $icc = $this->elekey[$this->getBytePos($char)]['offset']['icd'];
                    $icb = $this->elekey[$this->getBytePos($char)]['offset']['ice'];
                    $ica = $this->elekey[$this->getBytePos($char)]['offset']['icf'];
                }
            }
            for ($icount=1; $icount<65; $icount++) {
                if ($this->elekey[$icount]['offset']['ica'] == $icb && $this->elekey[$icount]['offset']['icb'] == $icc && $this->elekey[$icount]['offset']['icc'] == $icd) {
                    $nuclear .=  '10';
                    if ($icb = $this->elekey[$icount]['flip']) {
                        $nuclear .=  '0';
                    } else {
                        $nuclear .=  '1';
                    }
                    if ($icc = $this->elekey[$icount]['flip']) {
                        $nuclear .=  '0';
                    } else {
                        $nuclear .=  '1';
                    }
                    if ($icd = $this->elekey[$icount]['flip']) {
                        $nuclear .=  '0';
                    } else {
                        $nuclear .=  '1';
                    }
                }
                
                if ($this->elekey[$icount]['offset']['ica'] == $icc && $this->elekey[$icount]['offset']['icb'] == $icd && $this->elekey[$icount]['offset']['icc'] == $ice) {
                    $nuclear .=  '01';
                    if ($icb = $this->elekey[$icount]['flip']) {
                        $nuclear .=  '0';
                    } else {
                        $nuclear .=  '1';
                    }
                    if ($icc = $this->elekey[$icount]['flip']) {
                        $nuclear .=  '0';
                    } else {
                        $nuclear .=  '1';
                    }
                    if ($icd = $this->elekey[$icount]['flip']) {
                        $nuclear .=  '0';
                    } else {
                        $nuclear .=  '1';
                    }
                }
            }

            // Change in version 1.6.4
            if (strlen($nuclear)>32768) {
                $nuclear = substr($nuclear, strlen($nuclear)-32768, 32768);
            }

            $result = $result + $ica;
            $prince = $prince + $icb;
            $karma = $karma + $icc;
            $motivation = $motivation + $icd;
            $official = $official + $ice;
            $outsidecause = $outsidecause + $icf;
            
            if ('0' == $ica) {
                $yang = $yang + 1;
            } else {
                $yin = $yin + 1;
            }
            if ('0' == $icb) {
                $yang = yang + 1;
            } else {
                $yin = $yin + 1;
            }
            if ('0' == $icc) {
                $yang = $yang+ 1;
            } else {
                $yin = $yin + 1;
            }
            if ('0' == $icd) {
                $yang = $yang + 1;
            } else {
                $yin = $yin + 1;
            }
            if ('0' == $ice) {
                $yang = yang + 1;
            } else {
                $yin = $yin + 1;
            }
            if ('0' == $icf) {
                $yang = $yang+ 1;
            } else {
                $yin = $yin + 1;
            }
            
            if (true == $debug) {
                $data[sizeof($data)+1] = [
                    'pos'      => $this->getBytePos($char),
                    'elements' => $this->elekey];
                                
                $result = [
                    'result'       => $result,
                    'prince'       => $prince,
                    'karma'        => $karma,
                    'motivation'   => $motivation,
                    'official'     => $official,
                    'outsidecause' => $outsidecause,
                    'nuclear'      => $nuclear,
                    'yin'          => $yin,
                    'yang'         => $yang,
                    'nx_key'       => $nx_key,
                    'data'         => $data];
            } else {
                $result = [
                    'result'       => $result,
                    'prince'       => $prince,
                    'karma'        => $karma,
                    'motivation'   => $motivation,
                    'official'     => $official,
                    'outsidecause' => $outsidecause,
                    'nuclear'      => $nuclear,
                    'yin'          => $yin,
                    'yang'         => $yang,
                    'nx_key'       => $nx_key];
            }
            
            return $result;
        }
    }
}
