<?php

include 'header.php';
include_once XOOPS_ROOT_PATH . '/class/xoopstree.php';

$xoopsOption['template_main'] = 'xtorrent_newlistindex.tpl';
include XOOPS_ROOT_PATH . '/header.php';

global $xoopsDB, $xoopsModule, $xoopsUser, $xoopsModuleConfig;

$groups        = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
$module_id     = $xoopsModule->getVar('mid');
$gperm_handler = xoops_gethandler('groupperm');

$imageheader = xtorrent_imageheader();
$xoopsTpl->assign('imageheader', $imageheader);

$counter          = 0;
$allweekdownloads = 0;

while ($counter <= 7 - 1) {
    $newdownloaddayRaw = (time() - (86400 * $counter));
    $newdownloadday    = date('d-M-Y', $newdownloaddayRaw);
    $newdownloadView   = date('F d, Y', $newdownloaddayRaw);
    $newdownloadDB     = formatTimestamp($newdownloaddayRaw, 's');
    $totaldownloads    = 0;
    $result            = $xoopsDB->query('SELECT lid, cid, published, updated FROM ' . $xoopsDB->prefix('xtorrent_downloads') . ' WHERE published > 0 AND published <= ' . time() . ' AND (expired = 0 OR expired > ' . time() . ') AND offline = 0');
    while ($myrow = $xoopsDB->fetcharray($result)) {
        $published = ($myrow['updated'] > 0) ? $myrow['updated'] : $myrow['published'];
        if ($gperm_handler->checkRight('xtorrentownCatPerm', $myrow['cid'], $groups, $module_id)) {
            if ($gperm_handler->checkRight('xtorrentownFilePerm', $myrow['lid'], $groups, $module_id)) {
                if (formatTimestamp($published, 's') == $newdownloadDB) {
                    $totaldownloads++;
                }
            }
        }
    }
    $counter++;
    $allweekdownloads = $allweekdownloads + $totaldownloads;
}

$counter = 0;
while ($counter <= 30 - 1) {
    $newdownloaddayRaw = (time() - (86400 * $counter));
    $newdownloadDB     = formatTimestamp($newdownloaddayRaw, 's');
    $totaldownloads    = 0;
    $result            = $xoopsDB->query('SELECT lid, cid, published, updated FROM ' . $xoopsDB->prefix('xtorrent_downloads') . ' WHERE published > 0 AND published <= ' . time() . ' AND (expired = 0 OR expired > ' . time() . ') AND offline = 0');
    while ($myrow = $xoopsDB->fetcharray($result)) {
        $published = ($myrow['updated'] > 0) ? $myrow['updated'] : $myrow['published'];
        if ($gperm_handler->checkRight('xtorrentownCatPerm', $myrow['cid'], $groups, $module_id)) {
            if ($gperm_handler->checkRight('xtorrentownFilePerm', $myrow['lid'], $groups, $module_id)) {
                if (formatTimestamp($published, 's') == $newdownloadDB) {
                    $totaldownloads++;
                }
            }
        }
    }
    if (!isset($allmonthdownloads)) {
        $allmonthdownloads = 0;
    };
    $allmonthdownloads = $allmonthdownloads + $totaldownloads;
    $counter++;
}
$xoopsTpl->assign('allweekdownloads', $allweekdownloads);
$xoopsTpl->assign('allmonthdownloads', $allmonthdownloads);
$xoopsTpl->assign('navitem', 1);
/**
 * List Last VARIABLE Days of Downloads
 */
$newdownloadshowdays = !isset($_GET['newdownloadshowdays']) ? 7 : $_GET['newdownloadshowdays'];
$xoopsTpl->assign('newdownloadshowdays', $newdownloadshowdays);

$counter          = 0;
$allweekdownloads = 0;
while ($counter <= $newdownloadshowdays - 1) {
    $newdownloaddayRaw = (time() - (86400 * $counter));
    $newdownloadday    = formatTimestamp($newdownloaddayRaw, 'd-M-Y');
    $newdownloadView   = formatTimestamp($newdownloaddayRaw, 'F d, Y');
    $newdownloadDB     = formatTimestamp($newdownloaddayRaw, 's');
    $totaldownloads    = 0;

    $result = $xoopsDB->query('SELECT lid, cid, published, updated FROM ' . $xoopsDB->prefix('xtorrent_downloads') . ' 
		WHERE published > 0 AND published <= ' . time() . ' 
		AND (expired = 0 OR expired > ' . time() . ') 
		AND offline = 0');
    while ($myrow = $xoopsDB->fetcharray($result)) {
        $published = ($myrow['updated'] > 0) ? $myrow['updated'] : $myrow['published'];

        if ($gperm_handler->checkRight('xtorrentownCatPerm', $myrow['cid'], $groups, $module_id)) {
            if ($gperm_handler->checkRight('xtorrentownFilePerm', $myrow['lid'], $groups, $module_id)) {
                if (formatTimestamp($myrow['published'], 's') == $newdownloadDB) {
                    $totaldownloads++;
                }
            }
        }
    }
    $counter++;
    $allweekdownloads                    = $allweekdownloads + $totaldownloads;
    $dailydownloads['newdownloadday']    = $dailydownloads['newdownloadView'] = $newdownloadView;
    $dailydownloads['newdownloaddayRaw'] = $newdownloaddayRaw;
    $dailydownloads['totaldownloads']    = $totaldownloads;
    $xoopsTpl->append('dailydownloads', $dailydownloads);
}
$counter           = 0;
$allmonthdownloads = 0;

$mytree = new XoopsTree($xoopsDB->prefix('xtorrent_cat'), 'cid', 'pid');
$sql    = 'SELECT * FROM ' . $xoopsDB->prefix('xtorrent_downloads') . ' ';
$sql    .= 'WHERE published > 0 AND published <= ' . time() . ' 
		AND (expired = 0 OR expired > ' . time() . ') AND offline = 0 
		ORDER BY ' . $xoopsModuleConfig['filexorder'];

$result = $xoopsDB->query($sql, $xoopsModuleConfig['perpage'], 0);
while ($down_arr = $xoopsDB->fetchArray($result)) {
    if ($gperm_handler->checkRight('xtorrentownFilePerm', $down_arr['lid'], $groups, $xoopsModule->getVar('mid'))) {
        include XOOPS_ROOT_PATH . '/modules/xtorrent/include/downloadinfo.php';
    }
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
include 'footer.php';
