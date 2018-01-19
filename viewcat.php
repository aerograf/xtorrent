<?php

include 'header.php';
include_once XOOPS_ROOT_PATH . '/class/xoopstree.php';

global $xoopsModuleConfig, $myts, $xoopsModules;

$start      = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
$orderby    = isset($_REQUEST['orderby']) ? $_REQUEST['orderby'] : 'dateD';
$cid        = isset($_REQUEST['cid']) && $_REQUEST['cid'] > 0 ? intval($_REQUEST['cid']) : 0;
$selectdate = isset($_REQUEST['selectdate'])?$_REQUEST['selectdate']: 0 ;
$list       = isset($_REQUEST['list'])?$_REQUEST['list']: 0 ;
$cat        = empty($_REQUEST['cat']) ? '' : xoops_sef($_REQUEST['cat'], '_');

if (0 != $xoopsModuleConfig['htaccess']) {
    if (0 != $cid) {
        global $xoopsDB;
        if ('' != $cat && 0 == $cid) {
            $sql = 'SELECT b.title, b.cid FROM ' . $xoopsDB->prefix('xtorrent_cat') . " b WHERE b.title LIKE '$cat'";
            $ret = $xoopsDB->query($sql);
            $rt = $xoopsDB->fetchArray($ret);
            $selectdate =str_replace(' ', '', $selectdate);
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: ' . XOOPS_URL . '/torrents/' . xoops_sef($rt['title']) . '/' . $rt['cid'] . ",$start,$selectdate,$list,$orderby");
            exit;
        }
        if ('' == $cat || strpos($_SERVER['REQUEST_URI'], 'iewcat.php') > 0) {
            $sql = 'SELECT b.title, b.cid FROM ' . $xoopsDB->prefix('xtorrent_cat') . " b WHERE b.cid=$cid";
            $ret = $xoopsDB->query($sql);
            $rt = $xoopsDB->fetchArray($ret);
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: ' . XOOPS_URL . '/torrents/' . xoops_sef($rt['title']) . '/' . $cid . ",$start,$selectdate,$list,$orderby");
            exit;
        }
    } else {
        if (strpos($_SERVER['REQUEST_URI'], 'iewcat.php')>0) {
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: ' . XOOPS_URL . "/torrents/search/$list,$orderby");
            exit;
        }
    }
}

$orderby = isset($_REQUEST['orderby']) ? convertorderbyin($_REQUEST['orderby']) : 'date DESC';
    
$xoopsOption['template_main'] = 'xtorrent_viewcat.tpl';
$groups        = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
$gperm_handler = xoops_gethandler('groupperm');

/**
 * Begin Main page Heading etc
 */
include XOOPS_ROOT_PATH . '/header.php';

$catarray['imageheader'] = xtorrent_imageheader();
$catarray['letters']     = xtorrent_letters();
$catarray['toolbar']     = xtorrent_toolbar();
$xoopsTpl->assign('catarray', $catarray);

/**
 * Breadcrumb
 */
$mytree      = new XoopsTree($xoopsDB->prefix('xtorrent_cat'), 'cid', 'pid');
$pathstring  = "<a href='index.php'>" . _MD_XTORRENT_MAIN . '</a>&nbsp;:&nbsp;';
$pathstring .= $mytree->getNicePathFromId($cid, 'title', 'viewcat.php?op=');
$child_array = $mytree->getFirstChild($myrow['cid'], 'title');
$xoopsTpl->assign('xoops_pagetitle', str_replace(['HOME | '], '', str_replace(['&nbsp;:&nbsp;', ':'], ' | ', strip_tags($pathstring))) . ' | Torrents ');
$xoopsTpl->assign('category_path', $pathstring);
$xoopsTpl->assign('category_id', $cid);
$xoopsTpl->assign('navitem', 1);
$arr = $mytree->getFirstChild($cid, 'weight');

/**
 * Display Sub-categories for selected Category
 */
if (is_array($arr) > 0 && !empty($list) && !empty($selectdate)) {
    $scount = 1;
    foreach ($arr as $ele) {
        if (!$gperm_handler->checkRight('xtorrentownCatPerm', $ele['cid'], $groups, $xoopsModule->getVar('mid'))) {
            continue;
        }

        $sub_arr         = [];
        $sub_arr         = $mytree->getFirstChild($ele['cid'], 'weight');
        $space           = 0;
        $chcount         = 0;
        $infercategories = '';

        foreach ($sub_arr as $sub_ele) {
            /**
             * Subitem file count
             */
            $hassubitems = xtorrent_REQUESTTotalItems($sub_ele['cid']);
            /**
             * Filter group permissions
             */
            if ($gperm_handler->checkRight('xtorrentownCatPerm', $sub_ele['cid'], $groups, $xoopsModule->getVar('mid'))) {
                /**
                 * If subcategory count > 5 then finish adding subcats to $infercategories and end
                 */
                if ($chcount > 5) {
                    $infercategories .= '...';
                    break;
                }
                if ($space > 0) {
                    $infercategories .= ', ';
                }
                $infercategories .= "<a href='" . XOOPS_URL . '/modules/xtorrent/viewcat.php?cid=' . $sub_ele['cid'] . "'>" . $myts->htmlSpecialChars($sub_ele['title']) . '</a> (' . $hassubitems['count'] . ')';
                $space++;
                $chcount++;
            }
        }
        $totallinks = xtorrent_REQUESTTotalItems($ele['cid']);
        $xoopsTpl->append('subcategories', [
            'title' => $myts->htmlSpecialChars($ele['title']),
            'id'    => $ele['cid'], 'infercategories' => $infercategories, 'totallinks' => $totallinks['count'],
            'count' => $scount
        ]);
        $scount++;
    }
}

/**
     * Show Description for Category listing
     */
    $sql         = 'SELECT description, nohtml, nosmiley, noxcodes, noimages, nobreak FROM ' . $xoopsDB->prefix('xtorrent_cat') . " WHERE cid = $cid";
    $head_arr    = $xoopsDB->fetchArray($xoopsDB->query($sql));
    $html        = $head_arr['nohtml'] ? 0 : 1;
    $smiley      = $head_arr['nosmiley'] ? 0 : 1;
    $xcodes      = $head_arr['noxcodes'] ? 0 : 1;
    $images      = $head_arr['noimages'] ? 0 : 1;
    $breaks      = $head_arr['nobreak'] ? 1 : 0;
    $description = $myts->displayTarea($head_arr['description'], $html, $smiley, $xcodes, $images, $breaks);
    $xoopsTpl->assign('description', $description);


/**
 * Extract Download information from database
 */
$xoopsTpl->assign('show_categort_title', true);
$sql = 'SELECT * FROM ' . $xoopsDB->prefix('xtorrent_downloads') . ' ';
if (!empty($selectdate)) {
    $sql .= 'WHERE TO_DAYS(FROM_UNIXTIME(published)) = TO_DAYS(FROM_UNIXTIME(' . $selectdate . ')) 
      			AND published > 0 AND published <= ' . time() . ' AND (expired = 0 OR expired > ' . time() . ') 
      			AND offline = 0 ORDER BY published DESC';
    $result = $xoopsDB->query($sql, $xoopsModuleConfig['perpage'], $start);
    $total_numrows['count'] = $xoopsDB->getRowsNum($xoopsDB->query($sql));
} elseif (!empty($list)) {
    $sql .= "WHERE title LIKE '" . strtoupper($list) . "%' OR title LIKE '" . strtolower($list) . "%' AND published > 0 AND 
      			published <= " . time() . ' AND (expired = 0 OR expired > ' . time() . ') AND offline = 0 
      			ORDER BY ' . $orderby;
    $result = $xoopsDB->query($sql, $xoopsModuleConfig['perpage'], $start);
    $xoopsTpl->assign('xoops_pagetitle', 'Search By ' . $list);
    $total_numrows['count'] = $xoopsDB->getRowsNum($xoopsDB->query($sql));
    echo $sql;
} else {
    $sql .= 'WHERE cid=' . $cid . ' AND published > 0 AND published <= ' . time() . ' 
      			AND (expired = 0 OR expired > ' . time() . ') AND offline = 0 
      			ORDER BY ' . $orderby;

    $result = $xoopsDB->query($sql, $xoopsModuleConfig['perpage'], $start);
    $xoopsTpl->assign('show_categort_title', false);
    $total_numrows = xtorrent_getTotalItems($cid);
}
/**
 * Show Downloads by file
 */
if ($total_numrows['count'] > 0) {
    while ($down_arr = $xoopsDB->fetchArray($result)) {
        if ($gperm_handler->checkRight('xtorrentownFilePerm', $down_arr['lid'], $groups, $xoopsModule->getVar('mid'))) {
            include XOOPS_ROOT_PATH . '/modules/xtorrent/include/downloadinfo.php';
        }
    }

    /**
     * Show order box
     */
    $xoopsTpl->assign('show_links', false);
    if ($total_numrows['count'] > 1 && 0 != $cid) {
        $xoopsTpl->assign('show_links', true);
        $orderbyTrans = convertorderbytrans($orderby);
        $xoopsTpl->assign('lang_cursortedby', sprintf(_MD_XTORRENT_CURSORTBY, convertorderbytrans($orderby)));
        $orderby = convertorderbyout($orderby);
    }
    /**
     * Screenshots display
     */
    $xoopsTpl->assign('show_screenshot', false);
    if (isset($xoopsModuleConfig['screenshot']) && 1 == $xoopsModuleConfig['screenshot']) {
        $xoopsTpl->assign('shots_dir', $xoopsModuleConfig['screenshots']);
        $xoopsTpl->assign('shotwidth', $xoopsModuleConfig['shotwidth']);
        $xoopsTpl->assign('shotheight', $xoopsModuleConfig['shotheight']);
        $xoopsTpl->assign('show_screenshot', true);
    }

    /**
     * Nav page render
     */
    include_once XOOPS_ROOT_PATH . '/class/pagenav.php';
    if (!empty($selectdate)) {
        $pagenav = new XoopsPageNav($total_numrows['count'], $xoopsModuleConfig['perpage'], $start, 'start', 'selectdate=' . $selectdate);
    } elseif (!empty($list)) {
        $pagenav = new XoopsPageNav($total_numrows['count'], $xoopsModuleConfig['perpage'], $start, 'start', 'list=' . $list);
    } else {
        $pagenav = new XoopsPageNav($total_numrows['count'], $xoopsModuleConfig['perpage'], $start, 'start', 'cid=' . $cid);
    }
    $page_nav = $pagenav->renderNav();
    $istrue   = (isset($page_nav) && !empty($page_nav)) ? true : false;
    $xoopsTpl->assign('rss_source', xoops_sef($child_array[sizeof($child_array)]));
    $xoopsTpl->assign('page_nav', $istrue);
    $xoopsTpl->assign('pagenav', $page_nav);
    $xoopsTpl->assign('htaccess', $xoopsModuleConfig['htaccess']);
}
include XOOPS_ROOT_PATH . '/footer.php';
