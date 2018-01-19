<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

/**
 * Class for users
 * @author Simon Roberts <simon@chronolabs.org>
 * @copyright copyright (c) 2009 chronolabs.org.au
 * @package xtorrent
 */
class Xtorrent_XoopsAuth extends XoopsAuth
{
    public $passcrc;
    /**
     * function for getuname_fromhash
     * @author Simon Roberts <simon@chronolabs.org>
     * @copyright copyright (c) 2009 chronolabs.org.au
     * @package xtorrent
     */

    public function uid_from_userkey($user_userkey, $passkey, $return = '')
    {
        global $xoopsDB;
        $sql     = 'SELECT (1, uname, uid) FROM ' . $xoopsDB->prefix('users') . " WHERE sha1(concat('uname','uid','$passkey')) = '$user_userkey'";
        $request = $xoopsDB->prefix($sql);
        if (!empty($request)) {
            list($passcrc, $uname, $uid) = $xoopsDB->fetchRow($request);
            $this->passcrc = isset($passcrc)?true:false;
            return ${$return};
        }
        return false;
    }
    
    /**
     * function for xoops_check_useruserkey
     * @author Simon Roberts <simon@chronolabs.org>
     * @copyright copyright (c) 2009 chronolabs.org.au
     * @package xtorrent
     */
     
    public function xoops_check_userkey($userkey, $passkey)
    {
        $result = $this->getuname_fromhash($userkey, $passkey);
        return $this->passcrc;
    }
    
    public function authenticate_userkey($userkey, $passkey)
    {
        $member_handler = xoops_gethandler('member');
        $user       = $member_handler->getUser($this->uid_from_userkey($userkey, $passkey));
        if ($user == false) {
            $this->setErrors(1, _US_INCORRECTLOGIN);
        }
        return $user;
    }
}
