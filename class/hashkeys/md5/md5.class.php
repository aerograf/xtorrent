<?php
// $Id: md5.class.php 1.1.0 - md5 2009-08-15 9:22:20 wishcraft $
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
//  ----+--------[  Dedication Peter & Carola Muynck   ]-----=-->----- //
//  ----------------------------------------------------------------+------- //
//  -------+-----(.)-(.)- Happy Wedding - 03/10-2009  ---------------------- //
//  ---+------+------------------------------------------------------------- //
//  ------B===>-----+----------+------------<{:']-- Wishcraft & Purrrrr ---- //
//  -----+-----------+------------------------------------------------------ //

if (!class_exists('md5')) {
    error_reporting(0);

    class md5
    {
        public $base;
        public $enum;
        public $seed;
        public $crc;

        public function __construct($data, $seed, $len = 32)
        {
            $this->seed   = $seed;
            $this->length = $len;

            if (!empty($data)) {
                $this->crc = sha1($data);
            }
        }

        public function calc($data)
        {
            return md5($data);
        }
    }
}
