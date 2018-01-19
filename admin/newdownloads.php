<?php

require_once __DIR__ . '/admin_header.php';

if (isset($_POST)) {
    foreach ($_POST as $k => $v) {
        $$k = $v;
    }
}

if (isset($_GET)) {
    foreach ($_GET as $k => $v) {
        $$k = $v;
    }
}

if (!isset($_POST['op'])) {
    $op = isset($_GET['op']) ? $_GET['op'] : 'main';
} else {
    $op = $_POST['op'];
}

switch ($op) {
    case 'approve':

        global $xoopsModule;

        $lid                   = (int)$_GET['lid'];
        $result                = $xoopsDB->query('SELECT cid, title, notifypub FROM ' . $xoopsDB->prefix('xtorrent_downloads') . ' WHERE lid=' . $lid . '');
        list($cid, $title, $notifypub) = $xoopsDB->fetchRow($result);
        /**
         * Update the database
         */
        $time                  = time();
        $xoopsDB->queryF('UPDATE ' . $xoopsDB->prefix('xtorrent_downloads') . " SET published = '$time.', status = '1' WHERE lid = " . $lid . '');

        $tags                  = [];
        $tags['FILE_NAME']     = $title;
        $tags['FILE_URL']      = XOOPS_URL . '/modules/xtorrent/singlefile.php?cid=' . $cid . '&amp;lid=' . $lid;

        $sql                   = 'SELECT title FROM ' . $xoopsDB->prefix('xtorrent_cat') . ' WHERE cid=' . $cid;
        $result                = $xoopsDB->query($sql);

        $row                   = $xoopsDB->fetchArray($result);
        $tags['CATEGORY_NAME'] = $row['title'];
        $tags['CATEGORY_URL']  = XOOPS_URL . '/modules/xtorrent/viewcat.php?cid=' . $cid;
        $notification_handler  = xoops_gethandler('notification');
        $notification_handler->triggerEvent('global', 0, 'new_file', $tags);
        $notification_handler->triggerEvent('category', $cid, 'new_file', $tags);

        if ($notifypub) {
            $notification_handler->triggerEvent('file', $lid, 'approve', $tags);
        }
        redirect_header('newdownloads.php', 1, _AM_XTORRENT_SUB_NEXTILECREATED);
        break;

    // List downloads waiting for validation
    case 'main':
    default:

        include_once XOOPS_ROOT_PATH . '/class/pagenav.php';
        global $xoopsDB, $myts, $xoopsModuleConfig, $imagearray;

        $start           = isset($_GET['start']) ? (int)$_GET['start'] : 0;
        $sql             = 'SELECT * FROM ' . $xoopsDB->prefix('xtorrent_downloads') . ' WHERE published = 0 ORDER BY lid DESC';
        $new_array       = $xoopsDB->query($sql, $xoopsModuleConfig['admin_perpage'], $start);
        $new_array_count = $xoopsDB->getRowsNum($xoopsDB->query($sql));

        xoops_cp_header();
        $adminObject = \Xmf\Module\Admin::getInstance();
        $adminObject->displayNavigation(basename(__FILE__));

          echo "<fieldset><legend style='font-weight: bold; color: #900;'>" . _AM_XTORRENT_SUB_FILESWAITINGINFO . "</legend>
          		<div  style='padding:4px;'><b>" . _AM_XTORRENT_SUB_FILESWAITINGVALIDATION . '</b> ' . $new_array_count . "</div>
          		<div  style='padding:4px;'>
          		<li>" . $imagearray['approve'] . ' ' . _AM_XTORRENT_SUB_APPROVEWAITINGFILE . '
          		<li>' . $imagearray['editimg'] . ' ' . _AM_XTORRENT_SUB_EDITWAITINGFILE . '
          		<li>' . $imagearray['deleteimg'] . ' ' . _AM_XTORRENT_SUB_DELETEWAITINGFILE . "</div><br>
          		<table class='outer' style='width:100%;'>
          		<tr>
          		<th style='text-align:center;width:3%;'>" . _AM_XTORRENT_MINDEX_ID . "</th>
          		<th style='width:30%;'>" . _AM_XTORRENT_MINDEX_TITLE . "</th>
          		<th style='text-align:center;width:15%;'>" . _AM_XTORRENT_MINDEX_POSTER . "</th>
          		<th style='text-align:center;width:15%;'>" . _AM_XTORRENT_MINDEX_SUBMITTED . "</th>
          		<th style='text-align:center;width:7%;'>" . _AM_XTORRENT_MINDEX_ACTION . '</th>
          		</tr>';

        if ($new_array_count > 0) {
            while ($new = $xoopsDB->fetchArray($new_array)) {
                $rating    = number_format($new['rating'], 2);
                $title     = $myts->htmlSpecialChars($new['title']);
                $url       = $myts->htmlSpecialChars($new['url']);
                $url       = urldecode($url);
                $homepage  = $myts->htmlSpecialChars($new['homepage']);
                $version   = $myts->htmlSpecialChars($new['version']);
                $size      = $myts->htmlSpecialChars($new['size']);
                $platform  = $myts->htmlSpecialChars($new['platform']);
                $logourl   = $myts->htmlSpecialChars($new['screenshot']);
                $submitter = xoops_getLinkedUnameFromId($new['submitter']);
                $datetime  = formatTimestamp($new['date'], $xoopsModuleConfig['dateformat']);
                $status    = $new['published'] ? $approved : "<a href='newdownloads.php?op=approve&amp;lid=" . $new['lid'] . "'>" . $imagearray['approve'] . '</a>';
                $modify    = "<a href='index.php?op=Download&amp;lid=" . $new['lid'] . "'>" . $imagearray['editimg'] . '</a>';
                $delete    = "<a href='index.php?op=delDownload&amp;lid=" . $new['lid'] . "'>" . $imagearray['deleteimg'] . '</a>';

                echo "<tr>
                  		<td class='head' style='text-align:center;'>" . $new['lid'] . "</td>
                  		<td class='even' style='white-space:nowrap;'><a href='newdownloads.php?op=edit&lid=" . $new['lid'] . "'>" . $title . "</a></td>
                  		<td class='even' style='text-align:center; white-space:nowrap;'>$submitter</td>
                  		<td class='even' style='text-align:center;'>" . $datetime . "</td>
                  		<td class='even' style='text-align:center; white-space:nowrap;'>" . $status . ' ' . $modify . ' ' . $delete . '</td>
                  		</tr>';
            }
        } else {
            echo "<tr ><td class='head' colspan='6' style='text-align:center;'>" . _AM_XTORRENT_SUB_NOFILESWAITING . '</td></tr>';
        }
        echo '</table></fieldset>';

        include_once XOOPS_ROOT_PATH . '/class/pagenav.php';
        $page    = ($new_array_count > $xoopsModuleConfig['admin_perpage']) ? _AM_XTORRENT_MINDEX_PAGE : '';
        $pagenav = new XoopsPageNav($new_array_count, $xoopsModuleConfig['admin_perpage'], $start, 'start');
        echo "<div style='padding:8px;float:right;'>" . $page . '' . $pagenav -> renderNav() . '</div>';
        require_once __DIR__ . '/admin_footer.php';
        break;
}
