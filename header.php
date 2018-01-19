<?php

include_once '../../mainfile.php';
include XOOPS_ROOT_PATH . '/modules/xtorrent/include/functions.php';

$xoopsModule       = $module_handler->getByDirname('xtorrent');
$xoopsModuleConfig = $config_handler->getConfigsByCat(0, $xoopsModule->getVar('mid'));

error_reporting(E_ALL);

if (!function_exists('xoops_sef')) {
    function xoops_sef($datab, $char = '-')
    {
        $replacement_chars = [];
        $accepted          = [
            'a',
            'b',
            'c',
            'd',
            'e',
            'f',
            'g',
            'h',
            'i',
            'j',
            'k',
            'l',
            'm',
            'n',
            'm',
            'o',
            'p',
            'q',
            'r',
            's',
            't',
            'u',
            'v',
            'w',
            'x',
            'y',
            'z',
            '0',
            '9',
            '8',
            '7',
            '6',
            '5',
            '4',
            '3',
            '2',
            '1'
        ];
        for ($i = 0; $i < 256; $i++) {
            if (!in_array(strtolower(chr($i)), $accepted)) {
                $replacement_chars[] = chr($i);
            }
        }
        $return_data = str_replace($replacement_chars, $char, $datab);
        #print $return_data . "<BR><BR>";
        return $return_data;
    }
}

$myts = MyTextSanitizer:: getInstance(); // MyTextSanitizer object
