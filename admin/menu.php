<?php

if (!isset($moduleDirName)) {
    $moduleDirName = basename(dirname(__DIR__));
}
if (false !== ($moduleHelper = Xmf\Module\Helper::getHelper($moduleDirName))) {
} else {
    $moduleHelper = Xmf\Module\Helper::getHelper('system');
}
$adminObject   = \Xmf\Module\Admin::getInstance();
$pathIcon32    = \Xmf\Module\Admin::menuIconPath('');
$pathModIcon32 = $moduleHelper->getModule()->getInfo('modicons32');

$adminmenu = [
    [
     'title'   =>  _MI_TORRENT_BINDEX,
     'link'    =>  'admin/index.php',
     'desc'    =>  '',
     'icon'    =>  $pathIcon32 . 'home.png'
    ],
    [
     'title'   =>  _MI_TORRENT_MDOWNLOADS,
     'link'    =>  'admin/index.php?op=Download',
     'desc'    =>  '',
     'icon'    =>  $pathIcon32 . '/add.png'
    ],
    [
     'title'   =>  _MI_TORRENT_INDEXPAGE,
     'link'    =>  'admin/indexpage.php',
     'desc'    =>  '',
     'icon'    =>  $pathIcon32 . '/manage.png'
    ],
    [
     'title'   =>  _MI_TORRENT_MCATEGORY,
     'link'    =>  'admin/category.php',
     'desc'    =>  '',
     'icon'    =>  $pathIcon32 . '/category.png'
    ],
    [
     'title'   =>  _MI_TORRENT_MUPLOADS,
     'link'    =>  'admin/upload.php',
     'desc'    =>  '',
     'icon'    =>  $pathIcon32 . '/compfile.png'
    ],
    [
     'title'   =>  _MI_TORRENT_MMIMETYPES,
     'link'    =>  'admin/mimetypes.php',
     'desc'    =>  '',
     'icon'    =>  $pathIcon32 . '/type.png'
    ],
    [
     'title'   =>  _MI_TORRENT_MVOTEDATA,
     'link'    =>  'admin/votedata.php',
     'desc'    =>  '',
     'icon'    =>  $pathIcon32 . '/event.png'
    ],
    [
     'title'   =>  _MI_TORRENT_PERMISSIONS,
     'link'    =>  'admin/permissions.php',
     'desc'    =>  '',
     'icon'    =>  $pathIcon32 . '/permissions.png'
    ],
    [
     'title'   =>  _MI_TORRENT_BLOCKADMIN,
     'link'    =>  'admin/myblocksadmin.php',
     'desc'    =>  '',
     'icon'    =>  $pathIcon32 . '/block.png'
    ],
    [
     'title'   =>  _MI_XTORRENT_PAYMENTS,
     'link'    =>  'admin/index.php?op=payment',
     'desc'    =>  '',
     'icon'    =>  $pathIcon32 . '/cash_stack.png'
    ],
    [
     'title'   =>  _MI_XTORRENT_PCONSOLID,
     'link'    =>  'admin/index.php?op=ipnrec',
     'desc'    =>  '',
     'icon'    =>  $pathIcon32 . '/cash_stack.png'
    ],
    [
     'title'   =>  _MI_TORRENT_ADMENU3,
     'link'    =>  'admin/about.php',
     'desc'    =>  '',
     'icon'    => $pathIcon32 . '/about.png'
    ]
]; 
 
/*
global $xoopsModule, $xoopsUser;
$xoopsModule = XoopsModule::getByDirname("xsoap");
if (!empty($xoopsModule)&&isset($xoopsUser))
if ($xoopsUser->isAdmin($xoopsModule->mid())) {
	$server = isset($_REQUEST['srv'])?$_REQUEST['srv']:0;
$adminmenu = [
    [
     'title'   =>  _MI_TORRENT_XSOAP_CATEGORY,
     'link'    =>  'admin/xsoap.php?op=category&srv=' . $server,
     'desc'    =>  '',
     //'icon'    =>    $pathModIcon32 . '/slogo.png'
    ],
    [
     'title'   =>  _MI_TORRENT_XSOAP_LISTING,
     'link'    =>  'admin/xsoap.php?op=listing&srv=' . $server,
     'desc'    =>  '',
     //'icon'    =>    $pathModIcon32 . '/slogo.png'
    ], 
    [
     'title'   =>  _MI_TORRENT_XSOAP_RETRIEVE,
     'link'    =>  'admin/xsoap.php?op=retrieve&srv=' . $server,
     'desc'    =>  '',
     //'icon'    =>    $pathModIcon32 . '/slogo.png'
    ]
];
}
*/