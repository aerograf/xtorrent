<?php

include 'header.php';
include_once XOOPS_ROOT_PATH . '/class/xoopstree.php';

$lid   = intval($_GET['lid']);
$cid   = intval($_GET['cid']);
$title = xoops_sef($HTTP_GET_VARS['title'],'_');
$cat   = xoops_sef($HTTP_GET_VARS['cat'],'_');
global $xoopsModuleConfig;

if ($xoopsModuleConfig['htaccess']!=0)
{
	if ($title!=''&&$cat!=''){
		$ret = $xoopsDB->query("SELECT a.lid, a.cid FROM ".$xoopsDB->prefix("xtorrent_downloads")." a INNER JOIN ".$xoopsDB->prefix("xtorrent_cat")." b ON a.cid = b.cid WHERE a.title LIKE '$title' AND b.title LIKE '$cat'");
		list($lid, $cid) = $xoopsDB->fetchRow($ret);
	} else {
		$ret = $xoopsDB->query("SELECT a.title, b.title as cat_title FROM ".$xoopsDB->prefix("xtorrent_downloads")." a INNER JOIN ".$xoopsDB->prefix("xtorrent_cat")." b ON a.cid = b.cid WHERE a.lid = '$lid'");
		//echo "SELECT a.title, b.title as cat_title FROM ".$xoopsDB->prefix("mylinks_links")." a INNER JOIN ".$xoopsDB->prefix("mylinks_cat")." b ON a.cid = b.cid WHERE a.lid = '$lid'";
		list($title, $cat_title) = $xoopsDB->fetchRow($ret);
		if (strpos($_SERVER['REQUEST_URI'],'inglefile.php')>0)
		{
			header( "HTTP/1.1 301 Moved Permanently" ); 
			header( "Location: ".XOOPS_URL."/torrents/".xoops_sef($cat_title)."/".xoops_sef($title)."/".$lid.",".$cid);
			exit;
		}
	}
}
	
$xoopsOption['template_main'] = 'xtorrent_singlefile.tpl';

$sql      = "SELECT * FROM " . $xoopsDB->prefix('xtorrent_downloads') . " WHERE lid = $lid";
$result   = $xoopsDB->query($sql);
$down_arr = $xoopsDB->fetchArray($result);

if (!$down_arr) {
   redirect_header("index.php", 1, _MD_XTORRENT_NODOWNLOAD);
   exit(); 
}

include XOOPS_ROOT_PATH . '/header.php';

/**
 * Begin Main page Heading etc
 */
$down['imageheader'] = xtorrent_imageheader();
$down['id']          = intval($down_arr['lid']);
$down['cid']         = intval($down_arr['cid']);
/**
 * Breadcrumb
 */
$mytree       = new XoopsTree($xoopsDB->prefix('xtorrent_cat'), "cid", "pid");
$pathstring   = "<a href='index.php'>" . _MD_XTORRENT_MAIN . "</a>&nbsp;:&nbsp;";
$pathstring   .= $mytree->getNicePathFromId($cid, "title", "viewcat.php?op=");
$down['path'] = $pathstring;

include_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/include/downloadinfo.php';

$xoopsTpl->assign('show_screenshot', false);
if (isset($xoopsModuleConfig['screenshot']) && $xoopsModuleConfig['screenshot'] == 1)
{
    $xoopsTpl->assign('shots_dir', $xoopsModuleConfig['screenshots']);
    $xoopsTpl->assign('shotwidth', $xoopsModuleConfig['shotwidth']);
    $xoopsTpl->assign('shotheight', $xoopsModuleConfig['shotheight']);
    $xoopsTpl->assign('show_screenshot', true);
} 
$xoopsTpl->assign('navitem', 1);
/**
 * Show other author downloads
 */
$groups        = (is_object($xoopsUser)) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
$gperm_handler = xoops_gethandler('groupperm');

$sql = "SELECT lid, cid, title, published FROM " . $xoopsDB->prefix('xtorrent_downloads') . " 
      	WHERE submitter = " . $down_arr['submitter'] . " 
      	AND published > 0 AND published <= " . time() . " AND (expired = 0 OR expired > " . time() . ") 
      	AND offline = 0 ORDER by published DESC";
$result = $xoopsDB->query($sql, 20, 0);

while ($arr = $xoopsDB->fetchArray($result))
{
    if (!$gperm_handler->checkRight('xtorrentownFilePerm', $arr['lid'], $groups, $xoopsModule->getVar('mid')) || $arr['lid'] == $lid)
        continue;
 
    $downuid['title']     = $arr['title'];
    $downuid['lid']       = $arr['lid'];
    $downuid['cid']       = $arr['cid'];
    $downuid['published'] = formatTimestamp($arr['published'], $xoopsModuleConfig['dateformat']);;
    $xoopsTpl->append('down_uid', $downuid);
} 
/**
 * User reviews
 */
$sql_review = "SELECT * FROM " . $xoopsDB->prefix('xtorrent_reviews') . " 
              WHERE lid = " . $down_arr['lid'] . " AND submit = 1";
$result_review = $xoopsDB->query($sql_review);
$review_amount = $xoopsDB->getRowsNum($result_review);
if ($review_amount > 0)
{
    $user_reviews = "op=list&amp;cid=" . $down_arr['cid'] . "&amp;lid=" . $down_arr['lid'] . "\">" . _MD_XTORRENT_USERREVIEWS;
} 
else
{
    $user_reviews = "cid=" . $down_arr['cid'] . "&amp;lid=" . $down_arr['lid'] . "\">" . _MD_XTORRENT_NOUSERREVIEWS;
} 
$xoopsTpl->assign('lang_user_reviews', $xoopsConfig['sitename'] . " " . _MD_XTORRENT_USERREVIEWSTITLE);
$xoopsTpl->assign('lang_UserReviews', sprintf($user_reviews, $down_arr['title']));

if (isset($xoopsModuleConfig['copyright']) && $xoopsModuleConfig['copyright'] == 1)
{
    $xoopsTpl->assign('lang_copyright', "" . $down['title'] . " © " . _MD_XTORRENT_COPYRIGHT . " " . date("Y") . " " . XOOPS_URL);
} 

// GETS TORRENT DATA FROM DATABASE
$sql    =  [];
$sql[0] = "SELECT torrent, tracker FROM ".$xoopsDB->prefix('xtorrent_poll'). " WHERE lid = ".$down['id'];
$sql[1] = "SELECT seeds, leechers, tracker FROM ".$xoopsDB->prefix('xtorrent_tracker'). " WHERE lid = ".$down['id'];
$sql[2] = "SELECT seeds, leechers, totalsize, modifiedby, tname FROM ".$xoopsDB->prefix('xtorrent_torrent'). " WHERE lid = ".$down['id'];
$sql[3] = "SELECT file FROM ".$xoopsDB->prefix('xtorrent_files'). " WHERE lid = ".$down['id'];
//print_r($sql);
$ret    = [];
$ret[0] = $xoopsDB->query($sql[0]);
$ret[1] = $xoopsDB->query($sql[1]);
$ret[2] = $xoopsDB->query($sql[2]);
$ret[3] = $xoopsDB->query($sql[3]);

$poll    = $xoopsDB->fetchArray($ret[0]);
$torrent = $xoopsDB->fetchArray($ret[2]);

$trkcr = [];
while ($row = $xoopsDB->fetchArray($ret[1])){
	$trkcr[] = ["seeds" => $row['seeds'],
					 "leeches" => $row['leechers'],
					 "tracker" => $row['tracker']];
	$down['total_seeds']   = $down['total_seeds']+$row['seeds'];
	$down['total_leeches'] = $down['total_leeches']+$row['leechers'];

}

$files = [];
while ($row = $xoopsDB->fetchArray($ret[3])){
	$files[]  = ["file" => $row['file']];
}

$down['torrent_last_polled'] = date("H:i:s", $poll['torrent']);
$down['tracker_last_polled'] = date("H:i:s", $poll['tracker']);
$down['torrent']             = $torrent;
$down['total_seeds']         = $down['total_seeds']+$torrent['seeds'];
$down['total_leeches']       = $down['total_leeches']+$torrent['leechers'];
$down['tracker']             = $trkcr;
$down['files']               = $files;
//print_r($down);
$xoopsTpl->assign('down', $down);
$xoopsTpl->assign('xoops_pagetitle', $down['title']." | Torrents ");

include XOOPS_ROOT_PATH . '/include/comment_view.php';
include XOOPS_ROOT_PATH . '/footer.php';


// START TO CHECK FOR POLLING OF TORRENT

include "include/pollall.php";

//echo $poll['torrent']+($xoopsModuleConfig['poll_torrent_time']*60). "< time = ".time();

if ((time()>$poll['torrent']+($xoopsModuleConfig['poll_torrent_time']*60))&&$xoopsModuleConfig['poll_torrent']==1){
	$rt = poll_torrent($down['id']);
}

if ((time()>$poll['tracker']+($xoopsModuleConfig['poll_tracker_time']*60))&&$xoopsModuleConfig['poll_tracker']==1){
	$rt = poll_tracker($rt, $down['id'], $xoopsModuleConfig['poll_tracker_timeout']);
}
