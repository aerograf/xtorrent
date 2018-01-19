<?php

include 'header.php';
global $xoopsModuleConfig, $xoopsModule, $xoopsUser;

if (0 != $xoopsModuleConfig['htaccess']) {
    if (strpos($_SERVER['REQUEST_URI'], 'odules/')>0||strpos($_SERVER['REQUEST_URI'], 'ndex.php')>0) {
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . XOOPS_URL . '/torrents/');
        exit;
    }
}

include_once XOOPS_ROOT_PATH . '/class/xoopstree.php';
error_reporting(E_ALL);
$mytree = new XoopsTree($xoopsDB->prefix('xtorrent_cat'), 'cid', 'pid');

$xoopsOption['template_main'] = 'xtorrent_index.tpl';
include XOOPS_ROOT_PATH . '/header.php';
error_reporting(E_ALL);

/**
 * Begin Main page Heading etc
 */
$sql                          = 'SELECT * FROM ' . $xoopsDB->prefix('xtorrent_indexpage') . ' ';
$head_arr                     = $xoopsDB->fetchArray($xoopsDB->query($sql));
$catarray['imageheader']      = xtorrent_imageheader();
$catarray['indexheading']     = $myts->displayTarea($head_arr['indexheading']);
$catarray['indexheaderalign'] = $head_arr['indexheaderalign'];
$catarray['indexfooteralign'] = $head_arr['indexfooteralign'];

$html   = $head_arr['nohtml'] ? 0 : 1;
$smiley = $head_arr['nosmiley'] ? 0 : 1;
$xcodes = $head_arr['noxcodes'] ? 0 : 1;
$images = $head_arr['noimages'] ? 0 : 1;
$breaks = $head_arr['nobreak'] ? 1 : 0;

$catarray['indexheader'] = $myts->displayTarea($head_arr['indexheader'], $html, $smiley, $xcodes, $images, $breaks);
$catarray['indexfooter'] = $myts->displayTarea($head_arr['indexfooter'], $html, $smiley, $xcodes, $images, $breaks);
$catarray['letters']     = xtorrent_letters();
$catarray['toolbar']     = xtorrent_toolbar();
$xoopsTpl->assign('catarray', $catarray);
/**
 * End main page Headers
 */

$count   = 1;
$chcount = 0;
$countin = 0;

$groups        = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
$module_id     = $xoopsModule->getVar('mid');
$gperm_handler = xoops_gethandler('groupperm');

/**
 * Begin Main page download info
 */
$listings = xtorrent_getTotalItems();
/*
* get total amount of categories
*/
$total_cat = xtorrent_totalcategory();

$result = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('xtorrent_cat') . ' WHERE pid = 0 ORDER BY weight');
while ($myrow = $xoopsDB->fetchArray($result)) {
    $countin++;
    $subtotaldownload = 0;
    $totaldownload    = xtorrent_getTotalItems($myrow['cid'], 1);
    //$subtotaldownload = xtorrent_getTotalItems($myrow['cid'], 1);
    $indicator        = xtorrent_isnewimage($totaldownload['published']);

    if ($gperm_handler->checkRight('xtorrentownCatPerm', $myrow['cid'], $groups, $module_id)) {
        $title   = $myts->htmlSpecialChars($myrow['title']);
        $summary = $myts->displayTarea($myrow['summary']);
        /**
         * get child category objects
         */
        $arr           = [];
        $mytree        = new XoopsTree($xoopsDB->prefix('xtorrent_cat'), 'cid', 'pid');
        $arr           = $mytree->getFirstChild($myrow['cid'], 'title');
        $space         = 0;
        $chcount       = 0;
        $subcategories = '';

        foreach ($arr as $ele) {
            if ($gperm_handler->checkRight('xtorrentownCatPerm', $ele['cid'], $groups, $xoopsModule->getVar('mid'))) {
                if (1 == $xoopsModuleConfig['subcats']) {
                    $chtitle = $myts->htmlSpecialChars($ele['title']);
                    if ($chcount > 5) {
                        $subcategories .= '...';
                        break;
                    }
                    if ($space > 0) {
                        $subcategories .= '<br>';
                    }
                    $subcategories .= "<a href='" . XOOPS_URL . '/modules/xtorrent/viewcat.php?cid=' . $ele['cid'] . "'>" . $chtitle . '</a>';
                    $space++;
                    $chcount++;
                }
            }
        }

        if (is_file(XOOPS_ROOT_PATH . '/' . $xoopsModuleConfig['catimage'] . '/' . $myts->htmlSpecialChars($myrow['imgurl'])) && !empty($myrow['imgurl'])) {
            if ($xoopsModuleConfig['usethumbs'] && function_exists('gd_info')) {
                $imgurl = down_createthumb(
                    $myts->htmlSpecialChars($myrow['imgurl']),
                    $xoopsModuleConfig['catimage'], 'thumbs',
                    $xoopsModuleConfig['shotwidth'],
                    $xoopsModuleConfig['shotheight'],
                                    $xoopsModuleConfig['imagequality'],
                    $xoopsModuleConfig['updatethumbs'],
                    $xoopsModuleConfig['keepaspect']
                );
            } else {
                $imgurl = XOOPS_URL . '/' . $xoopsModuleConfig['catimage'] . '/' . $myts->htmlSpecialChars($myrow['imgurl']);
            }
        } else {
            $imgurl = $indicator['image'];
        }
        $xoopsTpl->append('categories', ['image' => $imgurl, 'id' => $myrow['cid'], 'title' => $title,
                  'summary' => $summary, 'subcategories' => $subcategories, 'totaldownloads' => $totaldownload['count'],
                  'count' => $count, 'alttext' => $indicator['alttext']]);
        $count++;
    }
}
switch ($total_cat) {
    case '1':
        $lang_ThereAre = _MD_XTORRENT_THEREIS;
        break;
    default:
        $lang_ThereAre = _MD_XTORRENT_THEREARE;
        break;
}

$xoopsTpl->assign('htaccess', $xoopsModuleConfig['htaccess']);
$xoopsTpl->assign('lang_thereare', sprintf($lang_ThereAre, $total_cat, $listings['count']));
$xoopsTpl->assign('navitem', 1);
include 'footer.php';
