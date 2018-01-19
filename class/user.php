<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
/**
 * Class for users
 * @author Kazumi Ono <onokazu@xoops.org>
 * @copyright copyright (c) 2000-2003 XOOPS.org
 * @package kernel
 */
class Xtorrent_XoopsUser extends XoopsUser
{

    /**
     * Array of groups that user belongs to
     * @var array
     * @access private
     */
    public $_xt_groups   = [];
    /**
     * @var bool is the user admin?
     * @access private
     */
    public $_xt_isAdmin  = null;
    /**
     * @var string user's rank
     * @access private
     */
    public $_xt_rank     = null;
    /**
     * @var bool is the user online?
     * @access private
     */
    public $_xt_isOnline = null;

    /**
     * constructor
     * @param array $id Array of key-value-pairs to be assigned to the user. (for backward compatibility only)
     * @param int $id ID of the user to be loaded from the database.
     */
    public function __construct($id = null)
    {
        $this->initVar('passcrc', XOBJ_DTYPE_INT);
        $this->initVar('uname', XOBJ_DTYPE_TXTBOX, null, true, 25);
        
        $id = $this->getVar("id");
        
        // for backward compatibility
        if (isset($id)) {
            if (is_array($id)) {
                $this->assignVars($id);
            } else {
                $member_handler = xoops_gethandler('member');
                $user           = $member_handler->getUser($id);
                foreach ($user->vars as $k => $v) {
                    $this->assignVar($k, $v['value']);
                }
            }
        }
    }

    /**
     * function for getuname_fromhash
     * @author Simon Roberts <simon@chronolabs.org>
     * @copyright copyright (c) 2009 chronolabs.org.au
     * @package xtorrent
     */

    public function getuname_fromhash($user_hashinfo)
    {
        global $xoopsDB;
        $sql     = "SELECT (1, uname) FROM ".$xoopsDB->prefix('users')." WHERE sha1(concat('uname','id')) = "."'$user_hashinfo'";
        $request = $xoopsDB->prefix($sql);
        if (!empty($request)) {
            list($passcrc, $uname)   = $xoopsDB->fetchRow($request);
            $this->setVar("passcrc") = $passcrc;
            return $uname;
        }
        return false;
    }
    
    /**
     * function for xoops_check_userhashinfo
     * @author Simon Roberts <simon@chronolabs.org>
     * @copyright copyright (c) 2009 chronolabs.org.au
     * @package xtorrent
     */
     
    public function xoops_check_userhashinfo($hashinfo)
    {
        $result = $this->getuname_fromhash($hashinfo);
        return $result;
    }
}
