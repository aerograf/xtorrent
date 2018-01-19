<?php

use XoopsModules\Xtorrent;

include __DIR__ . '/../preloads/autoloader.php';

require_once __DIR__ . '/../../../include/cp_header.php';
require_once __DIR__ . '../include/functions.php';

include_once XOOPS_ROOT_PATH . '/class/xoopstree.php';
include_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
    
if (is_object($xoopsUser)) {
    $xoopsModule = XoopsModule::getByDirname('xtorrent');
    if (!$xoopsUser->isAdmin($xoopsModule->mid())) {
        redirect_header(XOOPS_URL . '/', 3, _NOPERM);
        exit();
    }
} else {
    redirect_header(XOOPS_URL . '/', 1, _NOPERM);
    exit();
}

$moduleDirName = basename(dirname(__DIR__));

$helper = Xtorrent\Helper::getInstance();
$adminObject   = \Xmf\Module\Admin::getInstance();
$pathIcon32    = \Xmf\Module\Admin::menuIconPath('');
$pathModIcon32 = $helper->getModule()->getInfo('modicons32');

$helper->loadLanguage('admin');
$helper->loadLanguage('modinfo');

$myts = MyTextSanitizer::getInstance();

$imagearray = [
    'editimg'     => "<img src='../" . $pathModIcon32 . "/edit.gif' alt='" . _AM_XTORRENT_ICO_EDIT . "' align='middle'>",
  'deleteimg'   => "<img src='../" . $pathModIcon32 . "/delete.gif' alt='" . _AM_XTORRENT_ICO_DELETE . "' align='middle'>",
  'online'      => "<img src='../" . $pathModIcon32 . "/on.gif' alt='" . _AM_XTORRENT_ICO_ONLINE . "' align='middle'>",
  'offline'     => "<img src='../" . $pathModIcon32 . "/off.gif' alt='" . _AM_XTORRENT_ICO_OFFLINE . "' align='middle'>",
  'approved'    => "<img src='../" . $pathModIcon32 . "/on.gif' alt=''" . _AM_XTORRENT_ICO_APPROVED . "' align='middle'>",
  'notapproved' => "<img src='../" . $pathModIcon32 . "/off.gif' alt='" . _AM_XTORRENT_ICO_NOTAPPROVED . "' align='middle'>",
  'relatedfaq'  => "<img src='../" . $pathModIcon32 . "/link.gif' alt='" . _AM_XTORRENT_ICO_LINK . "' align='absmiddle'>",
  'relatedurl'  => "<img src='../" . $pathModIcon32 . "/urllink.gif' alt='" . _AM_XTORRENT_ICO_URL . "' align='middle'>",
  'addfaq'      => "<img src='../" . $pathModIcon32 . "/add.gif' alt='" . _AM_XTORRENT_ICO_ADD . "' align='middle'>",
  'approve'     => "<img src='../" . $pathModIcon32 . "/approve.gif' alt='" . _AM_XTORRENT_ICO_APPROVE . "' align='middle'>",
  'statsimg'    => "<img src='../" . $pathModIcon32 . "/stats.gif' alt='" . _AM_XTORRENT_ICO_STATS . "' align='middle'>",
    'ignore'      => "<img src='../" . $pathModIcon32 . "/ignore.gif' alt='" . _AM_XTORRENT_ICO_IGNORE . "' align='middle'>",
  'ack_yes'     => "<img src='../" . $pathModIcon32 . "/on.gif' alt='" . _AM_XTORRENT_ICO_ACK . "' align='middle'>",
    'ack_no'      => "<img src='../" . $pathModIcon32 . "/off.gif' alt='" . _AM_XTORRENT_ICO_REPORT . "' align='middle'>",
  'con_yes'     => "<img src='../" . $pathModIcon32 . "/on.gif' alt='" . _AM_XTORRENT_ICO_CONFIRM . "' align='middle'>",
    'con_no'      => "<img src='../" . $pathModIcon32 . "/off.gif' alt='" . _AM_XTORRENT_ICO_CONBROKEN . "' align='middle'>"
    ];
