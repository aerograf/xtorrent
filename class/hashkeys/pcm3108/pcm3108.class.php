<?php
// $Id: pcm3108.class.php 1.1.0 - pcm3108 2009-08-15 9:22:20 wishcraft $
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
//  ---------------------------------------------------------(.)-(.)-------- //
   //  ----+----^---[  Dedication Peter & Carola Muynck   ]-------------- //
//  ----------------------------------------------------------------+------- //
//  -------+-----(.)-(.)- Happy Wedding - 03/10-2009  ---------------------- //
//  ---+------+------------------------------------------------------------- //
//  ------B===>-----+----------+------------<{:']-- Wishcraft & Purrrrr ---- //
//  -----+-----------+------------------------------------------------------ //

if (!class_exists('pcm3108')) {
    error_reporting(0);
    
    class pcm3108
    {
        public $base;
        public $enum;
        public $seed;
        public $crc;
            
        public function __construct($data, $seed, $len=29)
        {
            $this->seed = $seed;
            $this->length = $len;
            $this->base = new pcm3108_base((int)$seed);
            $this->enum = new pcm3108_enumerator($this->base);
            
            if (!empty($data)) {
                for ($i=1; $i<strlen($data); $i++) {
                    $enum_calc = $this->enum->enum_calc(substr($data, $i, 1), $enum_calc);
                }
                $pcm3108_crc = new pcm3108_leaver($enum_calc, $this->base, $this->length);
                $this->crc = $pcm3108_crc->crc;
            }
        }
            
        public function calc($data)
        {
            for ($i=1; $i<strlen($data); $i++) {
                $enum_calc = $this->enum->enum_calc(substr($data, $i, 1), $enum_calc);
            }
            $pcm3108_crc = new pcm3108_leaver($enum_calc, $this->base, $this->length);
            return $pcm3108_crc->crc;
        }
    }
}

require 'pcm3108.base.php';
require 'pcm3108.enumerator.php';
require 'pcm3108.leaver.php';
