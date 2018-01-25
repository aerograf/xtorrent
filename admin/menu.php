<?php

$moduleDirName = basename(dirname(__DIR__));

$helper = \XoopsModules\Xtorrent\Helper::getInstance();
$pathIcon32    = \Xmf\Module\Admin::menuIconPath('');
$pathModIcon32 = $helper->getModule()->getInfo('modicons32');

$helper->loadLanguage('admin');
$helper->loadLanguage('modinfo');

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
/*    [
     'title'   =>  _MI_TORRENT_BLOCKADMIN,
     'link'    =>  'admin/myblocksadmin.php',
     'desc'    =>  '',
     'icon'    =>  $pathIcon32 . '/block.png'
    ],*/
    [
     'title'   =>  _MI_TORRENT_ADMENU3,
     'link'    =>  'admin/about.php',
     'desc'    =>  '',
     'icon'    => $pathIcon32 . '/about.png'
    ]
]; 
