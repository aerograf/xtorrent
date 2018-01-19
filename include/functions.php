<?php

/*
function urlExists($url)
{
    $file = '';
    $url = htmlentities($url);

    $url = ereg_replace("http://", "", $url);
    list($domain, $file) = explode("/", $url, 2);
    if ($file)
    {
        $fid = fsockopen($domain, 80, $errno ='', $errstr ='', 30);
        fputs($fid, "GET /$file HTTP/1.0\r\nHost: $domain\r\n\r\n");
        $gets = fgets($fid, 1024);
        fclose($fid);
        if (ereg("HTTP/1.1 200 OK", $gets)) return true;
        else return false;
    }
    else
    {
        return false;
    }
}
*/
/**
 * save_Permissions()
 *
 * @param $groups
 * @param $id
 * @param $perm_name
 * @return
 **/
function xtorrent_save_Permissions($groups, $id, $perm_name)
{
    $result         = true;
    $hModule        = xoops_gethandler('module');
    $xtorrentModule = $hModule -> getByDirname('xtorrent');

    $module_id      = $xtorrentModule -> getVar('mid');
    $gperm_handler  = xoops_gethandler('groupperm');

    /*
    * First, if the permissions are already there, delete them
    */
    $gperm_handler -> deleteByModule($module_id, $perm_name, $id);
    /*
    *  Save the new permissions
    */
    if (is_array($groups)) {
        foreach ($groups as $group_id) {
            $gperm_handler -> addRight($perm_name, $id, $group_id, $module_id);
        }
    }
    return $result;
}

/**
 * toolbar()
 *
 * @return
 **/
function xtorrent_toolbar()
{
    global $xoopsModuleConfig, $xoopsUser;
    $submissions = $xoopsModuleConfig['submissions'] ? 1 : 0;
    if (!is_object($xoopsUser)) {
        $submissions = $xoopsModuleConfig['anonpost'] ? 1 : 0;
    }
    $toolbar = '[ ';
    if (1 == $submissions) {
        $toolbar .= "<a href='submit.php'>" . _MD_XTORRENT_SUBMITDOWNLOAD . '</a> | ';
    }
    $toolbar .= "<a href='newlist.php'>" . _MD_XTORRENT_LATESTLIST . "</a> | <a href='topten.php?list=hit'>" . _MD_XTORRENT_POPULARITY . "</a> | <a href='topten.php?list=rate'>" . _MD_XTORRENT_TOPRATED . '</a> ]';
    return $toolbar;
}

/**
 * xtorrent_serverstats()
 *
 * @return
 **/
/*
function xtorrent_serverstats()
{
    echo "
        <fieldset><legend style='font-weight: bold; color: #900;'>" . _AM_XTORRENT_DOWN_IMAGEINFO . "</legend>\n
        <div style='padding: 8px;'>\n
        <div>" . _AM_XTORRENT_DOWN_SPHPINI . "</div>\n
        ";

    $safemode        = (ini_get('safe_mode')) ? _AM_XTORRENT_DOWN_ON . _AM_XTORRENT_DOWN_SAFEMODEPROBLEMS : _AM_XTORRENT_DOWN_OFF;
    $registerglobals = (ini_get('register_globals') == '') ? _AM_XTORRENT_DOWN_OFF : _AM_XTORRENT_DOWN_ON;
    $downloads       = (ini_get('file_uploads')) ? _AM_XTORRENT_DOWN_ON : _AM_XTORRENT_DOWN_OFF;
    $gdlib           = (function_exists('gd_info')) ? _AM_XTORRENT_DOWN_GDON : _AM_XTORRENT_DOWN_GDOFF;
    echo "<li>" . _AM_XTORRENT_DOWN_GDLIBSTATUS . $gdlib;
    if (function_exists('gd_info'))
    {
        if (true == $gdlib = gd_info())
        {
            echo "<li>" . _AM_XTORRENT_DOWN_GDLIBVERSION . "<b>" . $gdlib['GD Version'] . "</b>";
        }
    }
    echo "<br><br>\n\n";
    echo "<li>" . _AM_XTORRENT_DOWN_SAFEMODESTATUS . $safemode;
  echo "<li>" . _AM_XTORRENT_DOWN_REGISTERGLOBALS . $registerglobals;
    echo "<li>" . _AM_XTORRENT_DOWN_SERVERUPLOADSTATUS . $downloads;
  echo "<li>" . _AM_XTORRENT_DOWN_MAXUPLOADSIZE . " <b>" . ini_get('upload_max_filesize') . "</b>\n";
  echo "<li>" . _AM_XTORRENT_DOWN_MAXPOSTSIZE . " <b>" . ini_get('post_max_size') . "</b>\n";
    echo "</div>";
    echo "</fieldset><br>";
}
*/
/**
 * displayicons()
 *
 * @param  $time
 * @param integer $status
 * @param integer $counter
 * @return
 */
function xtorrent_displayicons($time, $status = 0, $counter = 0)
{
    global $xoopsModuleConfig;

    $new = '';
    $pop = '';

    $newdate = (time() - (86400 * (int)$xoopsModuleConfig['daysnew']));
    $popdate = (time() - (86400 * (int)$xoopsModuleConfig['daysupdated'])) ;

    if (3 != $xoopsModuleConfig['displayicons']) {
        if ($newdate < $time) {
            if ((int)$status > 1) {
                if (1 == $xoopsModuleConfig['displayicons']) {
                    $new = '&nbsp;<img src=' . XOOPS_URL . "/modules/xtorrent/images/icon/update.gif alt='' align ='absmiddle'>";
                }
                if (2 == $xoopsModuleConfig['displayicons']) {
                    $new = '<i>Updated!</i>';
                }
            } else {
                if (1 == $xoopsModuleConfig['displayicons']) {
                    $new = '&nbsp;<img src=' . XOOPS_URL . "/modules/xtorrent/images/icon/newred.gif alt='' align ='absmiddle'>";
                }
                if (2 == $xoopsModuleConfig['displayicons']) {
                    $new = '<i>New!</i>';
                }
            }
        }
        if ($popdate < $time) {
            if ($counter >= $xoopsModuleConfig['popular']) {
                if (1 == $xoopsModuleConfig['displayicons']) {
                    $pop = '&nbsp;<img src =' . XOOPS_URL . "/modules/xtorrent/images/icon/pop.gif alt='' align ='absmiddle'>";
                }
                if (2 == $xoopsModuleConfig['displayicons']) {
                    $pop = '<i>Popular</i>';
                }
            }
        }
    }
    $icons = $new . ' ' . $pop;
    return $icons;
}

if (!function_exists('convertorderbyin')) {
    // Reusable Link Sorting Functions
    /**
     * convertorderbyin()
     *
     * @param $orderby
     * @return
     **/
    function convertorderbyin($orderby)
    {
        switch (trim($orderby)) {
            case 'titleA':
                $orderby = 'title ASC';
                break;
            case 'dateA':
                $orderby = 'published ASC';
                break;
            case 'hitsA':
                $orderby = 'hits ASC';
                break;
            case 'ratingA':
                $orderby = 'rating ASC';
                break;
            case 'titleD':
                $orderby = 'title DESC';
                break;
            case 'hitsD':
                $orderby = 'hits DESC';
                break;
            case 'ratingD':
                $orderby = 'rating DESC';
                break;
            case'dateD':
            default:
                $orderby = 'published DESC';
                break;
        }
        return $orderby;
    }
}
if (!function_exists('convertorderbytrans')) {
    function convertorderbytrans($orderby)
    {
        if ('hits ASC' == $orderby) {
            $orderbyTrans = _MD_XTORRENT_POPULARITYLTOM;
        }
        if ('hits DESC' == $orderby) {
            $orderbyTrans = _MD_XTORRENT_POPULARITYMTOL;
        }
        if ('title ASC' == $orderby) {
            $orderbyTrans = _MD_XTORRENT_TITLEATOZ;
        }
        if ('title DESC' == $orderby) {
            $orderbyTrans = _MD_XTORRENT_TITLEZTOA;
        }
        if ('published ASC' == $orderby) {
            $orderbyTrans = _MD_XTORRENT_DATEOLD;
        }
        if ('published DESC' == $orderby) {
            $orderbyTrans = _MD_XTORRENT_DATENEW;
        }
        if ('rating ASC' == $orderby) {
            $orderbyTrans = _MD_XTORRENT_RATINGLTOH;
        }
        if ('rating DESC' == $orderby) {
            $orderbyTrans = _MD_XTORRENT_RATINGHTOL;
        }
        return $orderbyTrans;
    }
}
if (!function_exists('convertorderbyout')) {
    function convertorderbyout($orderby)
    {
        if ('title ASC' == $orderby) {
            $orderby = 'titleA';
        }
        if ('published ASC' == $orderby) {
            $orderby = 'dateA';
        }
        if ('hits ASC' == $orderby) {
            $orderby = 'hitsA';
        }
        if ('rating ASC' == $orderby) {
            $orderby = 'ratingA';
        }
        if ('title DESC' == $orderby) {
            $orderby = 'titleD';
        }
        if ('published DESC' == $orderby) {
            $orderby = 'dateD';
        }
        if ('hits DESC' == $orderby) {
            $orderby = 'hitsD';
        }
        if ('rating DESC' == $orderby) {
            $orderby = 'ratingD';
        }
        return $orderby;
    }
}

/**
 * PrettySize()
 *
 * @param $size
 * @return
 **/
function xtorrent_PrettySize($size)
{
    $mb = 1024 * 1024;
    if ($size > $mb) {
        $mysize = sprintf('%01.2f', $size / $mb) . ' MB';
    } elseif ($size >= 1024) {
        $mysize = sprintf('%01.2f', $size / 1024) . ' KB';
    } else {
        $mysize = sprintf(_MD_XTORRENT_NUMBYTES, $size);
    }
    return $mysize;
}

/**
 * updaterating()
 *
 * @param $sel_id
 * @return updates rating data in itemtable for a given item
 **/
function xtorrent_updaterating($sel_id)
{
    global $xoopsDB;
    $query       = 'select rating FROM ' . $xoopsDB-> prefix('xtorrent_votedata') . ' WHERE lid = ' . $sel_id . '';
    $voteresult  = $xoopsDB -> query($query);
    $votesDB     = $xoopsDB -> getRowsNum($voteresult);
    $totalrating = 0;
    while (list($rating) = $xoopsDB -> fetchRow($voteresult)) {
        $totalrating += $rating;
    }
    $finalrating = $totalrating / $votesDB;
    $finalrating = number_format($finalrating, 4);
    $sql = sprintf('UPDATE %s SET rating = %u, votes = %u WHERE lid = %u', $xoopsDB-> prefix('xtorrent_downloads'), $finalrating, $votesDB, $sel_id);
    $xoopsDB -> query($sql);
}


/**
 * totalcategory()
 *
 * @param integer $pid
 * @return
 **/
function xtorrent_totalcategory($pid = 0)
{
    global $xoopsDB, $xoopsModule, $xoopsUser;

    $groups        = is_object($xoopsUser) ? $xoopsUser-> getGroups() : XOOPS_GROUP_ANONYMOUS;
    $gperm_handler = xoops_gethandler('groupperm');

    $sql = 'SELECT cid FROM ' . $xoopsDB-> prefix('xtorrent_cat') . ' ';
    if ($pid > 0) {
        $sql .= 'WHERE pid = 0';
    }
    $result           = $xoopsDB -> query($sql);
    $catlisting       = 0;
    while (list($cid) = $xoopsDB -> fetchRow($result)) {
        if ($gperm_handler -> checkRight('xtorrentownCatPerm', $cid, $groups, $xoopsModule -> getVar('mid'))) {
            $catlisting++;
        }
    }
    return $catlisting;
}

/**
 * getTotalItems()
 *
 * @param integer $sel_id
 * @param integer $get_child
 * @return the total number of items in items table that are accociated with a given table $table id
 **/
function xtorrent_getTotalItems($sel_id = 0, $get_child = 0)
{
    global $xoopsDB, $mytree, $xoopsModule, $xoopsUser;

    $groups         = is_object($xoopsUser) ? $xoopsUser-> getGroups() : XOOPS_GROUP_ANONYMOUS;
    $gperm_handler  = xoops_gethandler('groupperm');

    $count          = 0;
    $published_date = 0;

    $arr = [];
    $query = 'select lid, published from ' . $xoopsDB-> prefix('xtorrent_downloads') . ' 
		WHERE offline = 0 AND published > 0 AND published <= ' . time() . ' AND (expired = 0 OR expired > ' . time() . ')';
    if ($sel_id) {
        $query .= ' AND cid=' . $sel_id . '';
    }
    $result = $xoopsDB -> query($query);
    while (list($lid, $published) = $xoopsDB -> fetchRow($result)) {
        if ($gperm_handler -> checkRight('xtorrentownFilePerm', $lid, $groups, $xoopsModule -> getVar('mid'))) {
            $count++;
            $published_date = ($published > $published_date) ? $published : $published_date;
        }
    }
    $thing = 0;
    if (1 == $get_child) {
        $arr  = $mytree -> getAllChildId($sel_id);
        $size = count($arr);
        for ($i = 0, $iMax = count($arr); $i < $iMax; $i++) {
            $query2  = 'select lid, published from ' . $xoopsDB-> prefix('xtorrent_downloads') . ' WHERE status > 0 AND offline = 0 AND published > 0 AND published <= ' . time() . ' AND (expired = 0 OR expired > ' . time() . ') AND cid=' . $arr[$i] . '';
            $result2 = $xoopsDB -> query($query2);
            while (list($lid, $published) = $xoopsDB -> fetchRow($result2)) {
                if ($gperm_handler -> checkRight('xtorrentownFilePerm', $lid, $groups, $xoopsModule -> getVar('mid'))) {
                    $published_date = ($published > $published_date) ? $published : $published_date;
                    $thing++;
                }
            }
        }
    }
    $info['count']     = $count + $thing;
    $info['published'] = $published_date;
    return $info;
}

function xtorrent_imageheader()
{
    global $xoopsDB, $xoopsModule, $xoopsModuleConfig;

    $image  = '';
    $result = $xoopsDB -> query('SELECT indeximage, indexheading FROM ' . $xoopsDB-> prefix('xtorrent_indexpage ') . ' ');
    list($indeximage, $indexheading) = $xoopsDB -> fetchrow($result);
    if (!empty($indeximage)) {
        $image = xtorrent_displayimage($indeximage, "'index.php'", $xoopsModuleConfig['mainimagedir'], $indexheading);
    }
    return $image;
}

function xtorrent_displayimage($image = '', $path = '', $imgsource = '', $alttext = '')
{
    global $xoopsConfig, $xoopsUser, $xoopsModule;

    $showimage = '';

    /**
     * Check to see if link is given
     */
    if ($path) {
        $showimage = '<a href=' . $path . '>';
    }

    /**
     * checks to see if the file is valid else displays default blank image
     */

    if (!is_dir(XOOPS_ROOT_PATH . '/' . $imgsource . '/' . $image) && file_exists(XOOPS_ROOT_PATH . '/' . $imgsource . '/' . $image)) {
        $showimage .= "<img src='" . XOOPS_URL . '/' . $imgsource . '/' . $image . "' border='0' alt='" . $alttext . "' ></a>";
    } else {
        if ($xoopsUser && $xoopsUser -> isAdmin($xoopsModule -> mid())) {
            $showimage .= "<img src='" . XOOPS_URL . "/modules/xtorrent/images/brokenimg.png' alt='" . _MD_XTORRENT_ISADMINNOTICE . "' ></a>";
        } else {
            $showimage .= "<img src='" . XOOPS_URL . "/modules/xtorrent/images/blank.png' alt=" . $alttext . ' ></a>';
        }
    }
    clearstatcache();
    return $showimage;
}

/**
 * down_createthumb()
 *
 * @param $img_name
 * @param $img_path
 * @param $img_savepath
 * @param integer $img_w
 * @param integer $img_h
 * @param integer $quality
 * @param integer $update
 * @param integer $aspect
 * @return
 **/
function down_createthumb($img_name, $img_path, $img_savepath, $img_w = 100, $img_h = 100, $quality = 100, $update = 0, $aspect = 1)
{
    global $xoopsModuleConfig, $xoopsConfig;
    // paths
    if (0 == $xoopsModuleConfig['usethumbs']) {
        $image_path = XOOPS_URL . "/{$img_path}/{$img_name}";
        return $image_path;
    }
    $image_path = XOOPS_ROOT_PATH . "/{$img_path}/{$img_name}";

    $savefile = $img_path . '/' . $img_savepath . '/' . $img_w . 'x' . $img_h . '_' . $img_name;
    $savepath = XOOPS_ROOT_PATH . '/' . $savefile;
    // Return the image if no update and image exists
    if (0 == $update && file_exists($savepath)) {
        return XOOPS_URL . '/' . $savefile;
    }

    list($width, $height, $type, $attr) = getimagesize($image_path, $info);

    switch ($type) {
        case 1:
            # GIF image
            if (function_exists('imagecreatefromgif')) {
                $img = @imagecreatefromgif($image_path);
            } else {
                $img = @imagecreatefrompng($image_path);
            }
            break;
        case 2:
            # JPEG image
            $img = @imagecreatefromjpeg($image_path);
            break;
        case 3:
            # PNG image
            $img = @imagecreatefrompng($image_path);
            break;
        default:
            return $image_path;
            break;
    }

    if (!empty($img)) {
        /**
         * Get image size and scale ratio
         */
        $scale = min($img_w / $width, $img_h / $height);
        /**
         * If the image is larger than the max shrink it
         */
        if ($scale < 1 && 1 == $aspect) {
            $img_w = floor($scale * $width);
            $img_h = floor($scale * $height);
        }
        /**
         * Create a new temporary image
         */
        if (function_exists('imagecreatetruecolor')) {
            $tmp_img = imagecreatetruecolor($img_w, $img_h);
        } else {
            $tmp_img = imagecreate($img_w, $img_h);
        }
        /**
         * Copy and resize old image into new image
         */
        imagecopyresampled($tmp_img, $img, 0, 0, 0, 0, $img_w, $img_h, $width, $height);
        imagedestroy($img);
        flush();
        $img = $tmp_img;
    }

    switch ($type) {
        case 1:
        default:
            # GIF image
            if (function_exists('imagegif')) {
                imagegif($img, $savepath);
            } else {
                imagepng($img, $savepath);
            }
            break;
        case 2:
            # JPEG image
            imagejpeg($img, $savepath, $quality);
            break;
        case 3:
            # PNG image
            imagepng($img, $savepath);
            break;
    }
    imagedestroy($img);
    flush();
    return XOOPS_URL . '/' . $savefile;
}

function xtorrent_letters()
{
    global $xoopsModule;

    $letterchoice  = '<div>' . _MD_XTORRENT_BROWSETOTOPIC . '</div>';
    $letterchoice .= '[  ';
    $alphabet      = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
    $num           = count($alphabet) - 1;
    $counter       = 0;
    while (list(, $ltr) = each($alphabet)) {
        $letterchoice .= "<a href='" . XOOPS_URL . "/modules/xtorrent/viewcat.php?list=$ltr'>$ltr</a>";
        if ($counter == round($num / 2)) {
            $letterchoice .= ' ]<br>[ ';
        } elseif ($counter != $num) {
            $letterchoice .= '&nbsp;|&nbsp;';
        }
        $counter++;
    }
    $letterchoice .= ' ]';
    return $letterchoice;
}

function xtorrent_isnewimage($published)
{
    global $xoopsDB;

    $oneday    = (time() - (86400 * 1));
    $threedays = (time() - (86400 * 3));
    $week      = (time() - (86400 * 7));

    if ($published > 0 && $published < $week) {
        $indicator['image']   = 'images/icon/download4.gif';
        $indicator['alttext'] = _MD_XTORRENT_NEWLAST;
    } elseif ($published >= $week && $published < $threedays) {
        $indicator['image']   = 'images/icon/download3.gif';
        $indicator['alttext'] = _MD_XTORRENT_NEWTHIS;
    } elseif ($published >= $threedays && $published < $oneday) {
        $indicator['image']   = 'images/icon/download2.gif';
        $indicator['alttext'] = _MD_XTORRENT_THREE;
    } elseif ($published >= $oneday) {
        $indicator['image']   = 'images/icon/download1.gif';
        $indicator['alttext'] = _MD_XTORRENT_TODAY;
    } else {
        $indicator['image']   = 'images/icon/download.gif';
        $indicator['alttext'] = _MD_XTORRENT_NO_FILES;
    }
    return $indicator;
}
// GetDownloadTime()
// This function is used to show some different download times
// BCMATH-Support in PHP needed!
// (c)02.06.04 by St@neCold, stonecold@csgui.de, http://www.csgui.de
function xtorrent_GetDownloadTime($size = 0, $gmodem = 1, $gisdn = 1, $gdsl = 1, $gslan = 0, $gflan = 0)
{
    $aflag  = [];
    $amtime = [];
    $artime = [];
    $ahtime = [];
    $asout  = [];
    $aflag  = [$gmodem, $gisdn, $gdsl, $gslan, $gflan];
    $amtime = [$size / 6300 / 60, $size / 7200 / 60, $size / 86400 / 60, $size / 1125000 / 60, $size / 11250000 / 60];
    $amname = ['Modem(56k)', 'ISDN(64k)', 'DSL(768k)', 'LAN(10M)', 'LAN(100M'];
    for ($i = 0;$i < 5;$i++) {
        $artime[$i] = ($amtime[$i] % 60);
    }
    for ($i = 0;$i < 5;$i++) {
        $ahtime[$i] = sprintf(' %2.0f', $amtime[$i] / 60);
    }
    if ($size <= 0) {
        $dltime = 'N/A';
    } else {
        for ($i = 0;$i < 5;$i++) {
            if (!$aflag[$i]) {
                $asout[$i] = '';
            } else {
                if (($amtime[$i] * 60) < 1) {
                    $asout[$i] = sprintf(' : %4.2fs', $amtime[$i] * 60);
                } else {
                    if ($amtime[$i] < 1) {
                        $asout[$i] = sprintf(' : %2.0fs', round($amtime[$i] * 60));
                    } else {
                        if (0 == $ahtime[$i]) {
                            $asout[$i] = sprintf(' : %5.1fmin', $amtime[$i]);
                        } else {
                            $asout[$i] = sprintf(' : %2.0fh%2.0fmin', $ahtime[$i], $artime[$i]);
                        }
                    }
                }
                $asout[$i] = '<b>' . $amname[$i] . '</b>' . $asout[$i];
                if ($i < 4) {
                    $asout[$i] = $asout[$i] . ' | ';
                }
            }
        }
        $dltime = '';
        for ($i = 0;$i < 5;$i++) {
            $dltime = $dltime . $asout[$i];
        }
    }
    return $dltime;
}

function xtorrent_strrrchr($haystack, $needle)
{
    return substr($haystack, 0, strpos($haystack, $needle) + 1);
}

function xtorrent_retmime($filename, $usertype = 1)
{
    global $xoopsDB;

    $ext = ltrim(strrchr($filename, '.'), '.');
    $sql = 'SELECT mime_types, mime_ext FROM ' . $xoopsDB-> prefix('xtorrent_mimetypes') . " WHERE mime_ext = '" . strtolower($ext) . "'";
    if (1 == $usertype) {
        $sql .= ' AND mime_admin = 1';
    } else {
        $sql .= ' AND mime_user = 1';
    }
    $result = $xoopsDB -> query($sql);
    list($mime_types, $mime_ext) = $xoopsDB -> fetchrow($result);
    $mimtypes = explode(' ', trim($mime_types));
    return $mimtypes;
}

function xtorrent_adminmenu($header = '', $menu = '', $extra = '', $scount = 4)
{
    global $xoopsConfig, $xoopsModule;
    
    if (!is_object($xoopsModule)) {
        $module_handler = xoops_gethandler('module');
        $xoopsModule = $module_handler->getByDirname('xtorrent');
    }

    if (isset($_SERVER['PHP_SELF'])) {
        $thispage = basename($_SERVER['PHP_SELF']);
    }
    $op = isset($_GET['op']) ? $op = '?op=' . $_GET['op'] : '';

    echo "
		<table class='outer' style='width:100%;'>
		<tr>
		<td style='font-size:10px;text-align:left;color:#2F5376;padding:2px 6px;line-height:18px;'>
		<a href='../../system/admin.php?fct=preferences&amp;op=showmod&amp;mod=" . $xoopsModule -> getVar('mid') . "'>" . _AM_XTORRENT_PREFS . "</a> |
		<a href='../admin/index.php'>" . _AM_XTORRENT_BINDEX . "</a> |
		<a href='../admin/permissions.php'>" . _AM_XTORRENT_PERMISSIONS . "</a> |
		<a href='../admin/myblocksadmin.php'>" . _AM_XTORRENT_BLOCKADMIN . "</a> |
		<a href='../index.php'>" . _AM_XTORRENT_GOMODULE . "</a> |
		<a href='../admin/index.php?op=payment'>" . _AM_XTORRENT_PAYMENTS . "</a> |
		<a href='../admin/index.php?op=ipnrec'>" . _AM_XTORRENT_PCONSOLID . "</a> |
		<a href='about.php'>" . _AM_XTORRENT_ABOUT . "</a>\n
		</td>
		</tr>
		</table><br>";

    if (empty($menu)) {
        /**
         * You can change this part to suit your own module. Defining this here will save you form having to do this each time.
         */
        $menu = [
            // _AM_GENERALSET => "" . XOOPS_URL . "/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod=" . $xoopsModule->getVar('mid') . "",
            _AM_XTORRENT_INDEXPAGE  => 'indexpage.php',
            _AM_XTORRENT_MCATEGORY  => 'category.php',
            _AM_XTORRENT_MDOWNLOADS => 'index.php?op=Download',
            _AM_XTORRENT_MUPLOADS   => 'upload.php',
            _AM_XTORRENT_MMIMETYPES => 'mimetypes.php',
            _AM_XTS_MVOTEDATA       => 'votedata.php',
            _AM_XTORRENT_MCOMMENTS  => '../../system/admin.php?module=' . $xoopsModule-> mid() . '&amp;status=0&amp;limit=100&amp;fct=comments&amp;selsubmit=Go',
            ];
    }

    if (!is_array($menu)) {
        echo "
		<table class='outer' style='width:100%;'>
		<tr><td class ='even' align ='center'><b>" . _AM_XTORRENT_NOMENUITEMS . '</b></td></tr></table><br>';
        return false;
    }

    $oddnum   = [1 => '1', 3 => '3', 5 => '5', 7 => '7', 9 => '9', 11 => '11', 13 => '13'];
    // number of rows per menu
    $menurows = count($menu) / $scount;
    // total amount of rows to complete menu
    $menurow  = ceil($menurows) * $scount;
    // actual number of menuitems per row
    $rowcount = $menurow / ceil($menurows);
    $count    = 0;
    for ($i = count($menu); $i < $menurow; $i++) {
        $tempArray = [1 => null];
        $menu      = array_merge($menu, $tempArray);
        $count++;
    }

    /**
     * Sets up the width of each menu cell
     */
    $width = 100 / $scount;
    $width = ceil($width);

    $menucount = 0;
    $count     = 0;
    /**
     * Menu table output
     */
    echo "<table width = '100%' cellpadding= '2' cellspacing= '1' class='outer'><tr>";
    /**
     * Check to see if $menu is and array
     */
    if (is_array($menu)) {
        $classcounts = 0;
        $classcol[0] = 'even';

        for ($i = 1; $i < $menurow; $i++) {
            $classcounts++;
            if ($classcounts >= $scount) {
                if ('odd' == $classcol[$i - 1]) {
                    $classcol[$i] = ('odd' == $classcol[$i - 1] && in_array($classcounts, $oddnum)) ? 'even' : 'odd';
                } else {
                    $classcol[$i] = ('even' == $classcol[$i - 1] && in_array($classcounts, $oddnum)) ? 'odd' : 'even';
                }
                $classcounts = 0;
            } else {
                $classcol[$i] = ('even' == $classcol[$i - 1]) ? 'odd' : 'even';
            }
        }
        unset($classcounts);

        foreach ($menu as $menutitle => $menulink) {
            if ($thispage . $op == $menulink) {
                $classcol[$count] = 'outer';
            }
            echo "<td class='" . $classcol[$count] . "' align='center' valign='middle' width='$width%'>";
            if (is_string($menulink)) {
                echo "<a href='" . $menulink . "'><small>" . $menutitle . '</small></a></td>';
            } else {
                echo '&nbsp;</td>';
            }
            $menucount++;
            $count++;
            /**
             * Break menu cells to start a new row if $count > $scount
             */
            if ($menucount >= $scount) {
                echo '</tr>';
                $menucount = 0;
            }
        }
        echo '</table><br>';
        unset($count);
        unset($menucount);
    }
    echo "<h3 style='color: #2F5376;'>" . $header . '</h3>';
    if ($extra) {
        echo "<div>$extra</div>";
    }
}

function xtorrent_getDirSelectOption($selected, $dirarray, $namearray)
{
    echo "<select size='1' name='workd' onchange='location.href=\"upload.php?rootpath=\"+this.options[this.selectedIndex].value'>";
    echo "<option value=''>--------------------------------------</option>";
    foreach ($namearray as $namearray => $workd) {
        if ($workd === $selected) {
            $opt_selected = 'selected';
        } else {
            $opt_selected = '';
        }
        echo "<option value='" . htmlspecialchars($namearray, ENT_QUOTES) . "' $opt_selected>" . $workd . '</option>';
    }
    echo '</select>';
}
/*
function filesarray($filearray)
{
    $files = array();
    $dir = opendir($filearray);

    while (($file = readdir($dir)) !== false)
    {
        if ((!preg_match("/^[.]{1,2}$/", $file) && preg_match("/[.htm|.html|.xhtml]$/i", $file) && !is_dir($file)))
        {
            if (strtolower($file) != 'cvs' && !is_dir($file))
            {
                $files[$file] = $file;
            }
        }
    }
    closedir($dir);
    asort($files);
    reset($files);
    return $files;
}
*/
function xtorrent_uploading($_GLOBALS, $uploaddir = 'uploads', $allowed_mimetypes = '', $redirecturl = 'index.php', $num = 0, $redirect = 0, $usertype = 1)
{
    global $_GLOBALS, $xoopsConfig, $xoopsModuleConfig, $_POST, $xoopsModule;

    $down = [];
    
    include_once XOOPS_ROOT_PATH . '/modules/xtorrent/class/uploader.php';

    if (empty($allowed_mimetypes)) {
        $allowed_mimetypes = xtorrent_retmime($_FILES['userfile']['name'], $usertype);
    }
    $upload_dir = XOOPS_ROOT_PATH . '/' . $uploaddir . '/';

    $maxfilesize   = $xoopsModuleConfig['maxfilesize'];
    $maxfilewidth  = $xoopsModuleConfig['maximgwidth'];
    $maxfileheight = $xoopsModuleConfig['maximgheight'];

    $uploader = new XoopsMediaUploader($upload_dir, $allowed_mimetypes, $maxfilesize, $maxfilewidth, $maxfileheight);
    $uploader -> noAdminSizeCheck(1);

    if ($uploader -> fetchMedia($_POST['xoops_upload_file'][0])) {
        if (!$uploader -> upload()) {
            $errors = $uploader -> getErrors();
            redirect_header($redirecturl, 2, $errors);
        } else {
            if ($redirect) {
                redirect_header($redirecturl, 1, _AM_XTORRENT_UPLOADFILE);
            } else {
                if (is_file($uploader->savedDestination)) {
                    $down['url'] = XOOPS_URL . '/' . $uploaddir . '/' . strtolower($uploader->savedFileName);
                    $down['size'] = filesize(XOOPS_ROOT_PATH . '/' . $uploaddir . '/' . strtolower($uploader->savedFileName));
                }
                return $down;
            }
        }
    } else {
        $errors = $uploader -> getErrors();
        redirect_header($redirecturl, 1, $errors);
    }
}

function xtorrent_getforum($forumid)
{
    global $xoopsDB, $xoopsConfig;

    echo "<select name='forumid'>";
    echo "<option value='0'>----------------------</option>";
    $result = $xoopsDB -> query('SELECT forum_name, forum_id FROM ' . $xoopsDB-> prefix('bb_forums') . ' ORDER BY forum_id');
    if (!$result) {
        $result = $xoopsDB -> query('SELECT forum_name, forum_id FROM ' . $xoopsDB-> prefix('xf_forums') . ' ORDER BY forum_id');
    }
    while (list($forum_name, $forum_id) = $xoopsDB -> fetchRow($result)) {
        if ($forum_id == $forumid) {
            $opt_selected = "selected='selected'";
        } else {
            $opt_selected = '';
        }
        echo "<option value='" . $forum_id . "' $opt_selected>" . $forum_name . '</option>';
    }
    echo '</select></div>';
    return $forumid;
}

function xtorrent_downlistheader($heading)
{
    echo "<fieldset><legend style='font-weight:bold;color:#900;'>" . $heading . "</legend><br>
    		<table class = 'outer' style='width:100%;'>
    		<tr>
    		<th style='text-align:center;'>" . _AM_XTORRENT_MINDEX_ID . '</th>
    		<th>' . _AM_XTORRENT_MINDEX_TITLE . "</th>
    		<th style='text-align:center;'>" . _AM_XTORRENT_MINDEX_POSTER . "</th>
    		<th style='text-align:center;'>" . _AM_XTORRENT_MINDEX_SUBMITTED . "</th>
    		<th style='text-align:center;'>" . _AM_XTORRENT_MINDEX_ONLINESTATUS . "</th>
    		<th style='text-align:center;'>" . _AM_XTORRENT_MINDEX_PUBLISHED . "</th>
    		<th style='text-align:center;'>" . _AM_XTORRENT_MINDEX_ACTION . '</th>
    		</tr>';
}

function xtorrent_downlistbody($published)
{
    global $myts, $imagearray;

    $lid       = $published['lid'];
    $cid       = $published['cid'];
    $title     = "<a href='../singlefile.php?cid=" . $published['cid'] . '&amp;lid=' . $published['lid'] . "'>" . $myts-> htmlSpecialChars(trim($published['title'])) . '</a>';
    ;
    $submitter = xoops_getLinkedUnameFromId((int)$published['submitter']);
    $publish   = formatTimestamp($published['published'], 's');
    $status    = ($published['published'] > 0) ? $imagearray['online'] : "<a href='newdownloads.php'>" . $imagearray['offline'] . '</a>';
    $offline   = (0 == $published['offline']) ? $imagearray['online'] : $imagearray['offline'];
    $modify    = "<a href='index.php?op=Download&amp;lid=" . $lid . "'>" . $imagearray['editimg'] . '</a>';
    $delete    = "<a href='index.php?op=delDownload&amp;lid=" . $lid . "'>" . $imagearray['deleteimg'] . '</a>';

    echo "<tr>
    		<td class='head' style='text-align:center;'>" . $lid . "</td>
    		<td class='even'>" . $title . "</td>\n
    		<td class='even' style='text-align:center;'>" . $submitter . "</td>
    		<td class='even' style='text-align:center;'>" . $publish . "</td>
    		<td class='even' style='text-align:center;'>" . $offline . "</td>
    		<td class='even' style='text-align:center;'>" . $status . "</td>
    		<td class='even' style='text-align:center;width:10%;white-space:nowrap;'>" . $modify . '' . $delete . '</td>
    		</tr>';
    unset($published);
}

function xtorrent_downlistfooter()
{
    echo "<tr>
    		<td class='head' colspan= '7' style='text-align:center;'>" . _AM_XTORRENT_MINDEX_NODOWNLOADSFOUND . '</td>
    		</tr>';
}

function xtorrent_setcookie($name, $string = '', $expire = 0)
{
    global $xtorrentCookie;
    if (is_array($string)) {
        $value = [];
        foreach ($string as $key => $val) {
            $value[]= $key . '|' . $val;
        }
        $string = implode(',', $value);
    }
    setcookie($xtorrentCookie['prefix'].$name, $string, (int)$expire, $xtorrentCookie['path'], $xtorrentCookie['domain'], $xtorrentCookie['secure']);
}

function xtorrent_getcookie($name, $isArray = false)
{
    global $xtorrentCookie;
    $value = !empty($_COOKIE[$xtorrentCookie['prefix'].$name]) ? $_COOKIE[$xtorrentCookie['prefix'].$name] : null;
    if ($isArray) {
        $_value = $value ?explode(',', $value):[];
        $value = [];
        if (count($_value)>0) {
            foreach ($_value as $string) {
                $sep = strpos($string, '|');
                if (false === $sep) {
                    $value[]=$string;
                } else {
                    $key = substr($string, 0, $sep);
                    $val = substr($string, $sep + 1);
                    $value[$key] = $val;
                }
            }
        }
        unset($_value);
    }
    return $value;
}

function xtorrent_downlistpagenav($pubrowamount, $start, $art = 'art')
{
    global $xoopsModuleConfig;

    echo '</table>';
    // Display Page Nav if published is > total display pages amount.
    $page    = ($pubrowamount > $xoopsModuleConfig['admin_perpage']) ? _AM_XTORRENT_MINDEX_PAGE : '';
    $pagenav = new XoopsPageNav($pubrowamount, $xoopsModuleConfig['admin_perpage'], $start, 'st' . $art);
    echo '<div style="padding:8px;float:right;">' . $page . '' . $pagenav -> renderNav() . '</div>';
    echo '</fieldset><br>';
}

function xtorrent_get_base_domain($url)
{
    static $sdomain;
    
    if (!strpos($url, '://')) {
        $url = 'tcp://' . $url;
    }
    
    $parsed_url = parse_url($url);
    
    if (strpos(' ' . $parsed_url['host'], $sdomain)) {
        return $sdomain;
    }
            
    // break up domain, reverse
    $domain = explode('.', $parsed_url['host']);
    $domain = array_reverse($domain);
    
    // first check for ip address
    if (4 == count($domain) && is_numeric($domain[0]) && is_numeric($domain[3])) {
        return implode('.', $domain);
    }
    
    // if only 2 domain parts, that must be our domain
    if (count($domain) <= 2) {
        $sdomain = strtolower($parsed_url['host']);
        return $sdomain;
    }
    
    $OEG_TLD = [
                    'biz','com','edu','gov','info','int','mil','name','net','org',
                    'aero','asia','cat','coop','jobs','mobi','museum','pro','tel','travel',
                    'arpa','root','berlin','bzh','cym','gal','geo','kid','kids','lat','mail',
                    'nyc','post','sco','web','xxx','nato', 'example','invalid','localhost','test',
                    'bitnet','csnet','ip','localhost','onion','uucp','geek','co','go','spy','travel','int','asia'
                  ];
  
    // country tlds (source: http://en.wikipedia.org/wiki/Country_code_top-level_domain)
    $OE_TLD = [
      // active
                  'ac','ad','ae','af','ag','ai','al','am','an','ao','aq','ar','as','at','au','aw','ax','az',
                  'ba','bb','bd','be','bf','bg','bh','bi','bj','bm','bn','bo','br','bs','bt','bw','by','bz',
                  'ca','cc','cd','cf','cg','ch','ci','ck','cl','cm','cn','co','cr','cu','cv','cx','cy','cz',
                  'de','dj','dk','dm','do','dz','ec','ee','eg','er','es','et','eu','fi','fj','fk','fm','fo',
                  'fr','ga','gd','ge','gf','gg','gh','gi','gl','gm','gn','gp','gq','gr','gs','gt','gu','gw',
                  'gy','hk','hm','hn','hr','ht','hu','id','ie','il','im','in','io','iq','ir','is','it','je',
                  'jm','jo','jp','ke','kg','kh','ki','km','kn','kr','kw','ky','kz','la','lb','lc','li','lk',
                  'lr','ls','lt','lu','lv','ly','ma','mc','md','mg','mh','mk','ml','mm','mn','mo','mp','mq',
                  'mr','ms','mt','mu','mv','mw','mx','my','mz','na','nc','ne','nf','ng','ni','nl','no','np',
                  'nr','nu','nz','om','pa','pe','pf','pg','ph','pk','pl','pn','pr','ps','pt','pw','py','qa',
                  're','ro','ru','rw','sa','sb','sc','sd','se','sg','sh','si','sk','sl','sm','sn','sr','st',
                  'sv','sy','sz','tc','td','tf','tg','th','tj','tk','tl','tm','tn','to','tr','tt','tv','tw',
                  'tz','ua','ug','uk','us','uy','uz','va','vc','ve','vg','vi','vn','vu','wf','ws','ye','yu',
                  'za','zm','zw','io','eh','kp','me','rs','um','bv','gb','pm','sj','so','yt','su','tp',
                  'bu','cs','dd','zr'
              ];
    
    if (in_array($domain[0], $OE_TLD) && in_array($domain[1], $OEG_TLD) && 'www' != $domain[2]) {
        $sdomain = strtolower($domain[2] . '.' . $domain[1] . '.' . $domain[0]);
        return $sdomain;
    } elseif (in_array($domain[0], $OE_TLD) && in_array($domain[1], $OEG_TLD)) {
        $sdomain = strtolower($domain[2] . '.' .$domain[1] . '.' . $domain[0]);
        return $sdomain;
    } elseif (in_array($domain[0], $OEG_TLD)) {
        $sdomain = strtolower($domain[1] . '.' . $domain[0]);
        return $sdomain;
    } elseif (in_array($domain[0], $OE_TLD)) {
        $sdomain = strtolower($domain[1] . '.' . $domain[0]);
        return $sdomain;
    }

    
    $A_TLD = [];
    $first = 'a';
    for ($i=0;$i<26;$i++) {
        $second = 'a';
        for ($j=0;$j<26;$j++) {
            $A_TLD[] = $first.$second;
            if (strlen('us')>2) {
                $third = 'a';
                for ($c=0;$c<26;$c++) {
                    $A_TLD[] = $first.$second.$third;
                    if (strlen('mil')>3) {
                        $forth = 'a';
                        for ($l=0;$l<26;$l++) {
                            $A_TLD[] = $first.$second.$third.forth;
                            $forth++;
                        }
                    }
                    $third++;
                }
            }
            $second++;
        }
        $first++;
    }
    
    $first = 'a';
    for ($i=0;$i<26;$i++) {
        if (strlen('localhost')>1) {
            $second = 'a';
            for ($j=0;$j<26;$j++) {
                $C_TLD[] = $first.$second;
                if (strlen('localhost')>2) {
                    $third = 'a';
                    for ($c=0;$c<26;$c++) {
                        $C_TLD[] = $first.$second.$third;
                        if (strlen('localhost')>3) {
                            $forth = 'a';
                            for ($v=0;$v<26;$v++) {
                                $C_TLD[] = $first.$second.$third.$forth;
                                if (strlen('localhost')>4) {
                                    $fifth = 'a';
                                    for ($x=0;$x<26;$x++) {
                                        $C_TLD[] = $first.$second.$third.$forth.$fifth;
                                        if (strlen('localhost')>5) {
                                            $sixth = 'a';
                                            for ($x=0;$x<26;$x++) {
                                                $C_TLD[] = $first.$second.$third.$forth.$fifth.$sixth;
                                                if (strlen('localhost')>6) {
                                                    $seventh = 'a';
                                                    for ($x=0;$x<26;$x++) {
                                                        $C_TLD[] = $first.$second.$third.$forth.$fifth.$sixth.$seventh;
                                                        if (strlen('localhost')>7) {
                                                            $eigth = 'a';
                                                            for ($x=0;$x<26;$x++) {
                                                                $C_TLD[] = $first.$second.$third.$forth.$fifth.$sixth.$seventh.$eigth;
                                                                if (strlen('localhost')>8) {
                                                                    $ninth = 'a';
                                                                    for ($x=0;$x<26;$x++) {
                                                                        $C_TLD[] = $first.$second.$third.$forth.$fifth.$sixth.$seventh.$eigth.$ninth;
                                                                        if (strlen('localhost')>9) {
                                                                            $tenth = 'a';
                                                                            for ($x=0;$x<26;$x++) {
                                                                                $C_TLD[] = $first.$second.$third.$forth.$fifth.$sixth.$seventh.$eigth.$ninth.$tenth;
                                                                                $tenth++;
                                                                            }
                                                                        }
                                                                        $ninth++;
                                                                    }
                                                                }
                                                                $eigth++;
                                                            }
                                                        }
                                                        $seventh++;
                                                    }
                                                }
                                                $sixth++;
                                            }
                                        }
                                        $fifth++;
                                    }
                                }
                                $forth++;
                            }
                        }
                        $third++;
                    }
                }
                $second++;
            }
                
            if (in_array($domain[0], $A_TLD) && in_array($domain[1], $C_TLD) && 'www' != $domain[2]) {
                $sdomain = strtolower($domain[2] . '.' . $domain[1] . '.' . $domain[0]);
                return $sdomain;
            } elseif (in_array($domain[0], $A_TLD) && in_array($domain[1], $C_TLD)) {
                $sdomain = strtolower($domain[2] . '.' . $domain[1] . '.' . $domain[0]);
                return $sdomain;
            } elseif (in_array($domain[0], $C_TLD)) {
                $sdomain = strtolower($domain[1] . '.' . $domain[0]);
                return $sdomain;
            } elseif (in_array($domain[0], $A_TLD)) {
                $sdomain = strtolower($domain[1] . '.' . $domain[0]);
                return $sdomain;
            }

            unset($C_TLD);
            $C_TLD = [];
        }
        $first++;
    }

    $sdomain = strtolower(str_replace('www.', '', $parsed_url['host']));
    return $sdomain;
}
