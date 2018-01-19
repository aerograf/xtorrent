<?php

require_once __DIR__ . '/admin_header.php';

$op = '';

if (!isset($_POST['op'])) {
    $op = isset($_GET['op']) ? $_GET['op'] : 'listBrokenDownloads';
} else {
    $op = $_POST['op'];
}

$lid = (isset($_GET['lid'])) ? intval($_GET['lid']) : 0;

switch ($op) {
    case "updateNotice":
        global $xoopsDB;
        if (isset($_GET['ack'])) {
            $acknowledged = (isset($_GET['ack']) && $_GET['ack'] == 0) ? 1 : 0;
            $xoopsDB->queryF("UPDATE " . $xoopsDB->prefix("xtorrent_broken") . " SET acknowledged = " . $acknowledged . " WHERE lid= " . $lid);
            $update_mess = _AM_XTORRENT_BROKEN_NOWACK;
        }
        if (isset($_GET['con'])) {
            $confirmed = (isset($_GET['con']) && $_GET['con'] == 0) ? 1 : 0;
            $xoopsDB->queryF("UPDATE " . $xoopsDB->prefix("xtorrent_broken") . " SET confirmed = " . $confirmed . " WHERE lid = " . $lid);
            $update_mess = _AM_XTORRENT_BROKEN_NOWCON;
        }
        redirect_header("brokendown.php?op=default", 1, $update_mess);
        break;

    case "delBrokenDownloads":
        global $xoopsDB;
        $xoopsDB->queryF("DELETE FROM " . $xoopsDB->prefix("xtorrent_broken") . " WHERE lid = " . $lid);
        $xoopsDB->queryF("DELETE FROM " . $xoopsDB->prefix("xtorrent_downloads") . " WHERE lid = " . $lid);
        redirect_header("brokendown.php?op=default", 1, _AM_XTORRENT_BROKENFILEDELETED);
        exit();
        break;

    case "ignoreBrokenDownloads":
        global $xoopsDB;
        $xoopsDB->queryF("DELETE FROM " . $xoopsDB->prefix("xtorrent_broken") . " WHERE lid = " . $lid);
        redirect_header("brokendown.php?op=default", 1, _AM_XTORRENT_BROKEN_FILEIGNORED);
        break;

    case "listBrokenDownloads":
    case "default":

        global $xoopsDB, $imagearray, $xoopsModule;
        $result               = $xoopsDB->query("SELECT * FROM " . $xoopsDB->prefix("xtorrent_broken") . " ORDER BY reportid");
        $totalbrokendownloads = $xoopsDB->getRowsNum($result);

        xoops_cp_header();
        $adminObject = \Xmf\Module\Admin::getInstance();
        $adminObject->displayNavigation(basename(__FILE__));

          echo "<fieldset><legend style='font-weight:bold;color:#900;'>" . _AM_XTORRENT_BROKEN_REPORTINFO . "</legend>
          		<div style='padding:4px;'>" . _AM_XTORRENT_BROKEN_REPORTSNO . "&nbsp;<b>$totalbrokendownloads</b><div>
          		<div style='padding:4px;'>
          		<ul>
              <li>" . $imagearray['ignore'] . " " . _AM_XTORRENT_BROKEN_IGNOREDESC . "</li>
          		<li>" . $imagearray['editimg'] . " " . _AM_XTORRENT_BROKEN_EDITDESC . "</li>
          		<li>" . $imagearray['deleteimg'] . " " . _AM_XTORRENT_BROKEN_DELETEDESC . "</li>
          		<li>" . $imagearray['ack_yes'] . " " . _AM_XTORRENT_BROKEN_ACKDESC . "</li>
          		<li>" . $imagearray['con_yes'] . " " . _AM_XTORRENT_BROKEN_CONFIRMDESC . "</li>
          		</ul></div><br>
          		<table class='outer' style='width:100%;'>
          		<tr style='text-align:center;'>
          		<th style='text-align:center;width:3%;'>" . _AM_XTORRENT_BROKEN_ID . "</th>
          		<th style='text-align:center;width:35%;'>" . _AM_XTORRENT_BROKEN_TITLE . "</th>
          		<th>" . _AM_XTORRENT_BROKEN_REPORTER . "</th>
          		<th>" . _AM_XTORRENT_BROKEN_FILESUBMITTER . "</th>
          		<th>" . _AM_XTORRENT_BROKEN_DATESUBMITTED . "</th>
          		<th style='text-align:center;'>" . _AM_XTORRENT_BROKEN_ACTION . "</th></tr>";

        if ($totalbrokendownloads == 0) {
            echo "<tr style='text-align:center;'><td class='head' colspan = '6' style='text-align:center;'>" . _AM_XTORRENT_BROKEN_NOFILEMATCH . "</td></tr>";
        } else {
            while (list($reportid, $lid, $sender, $ip, $date, $confirmed, $acknowledged) = $xoopsDB->fetchRow($result)) {
                $result2 = $xoopsDB->query("SELECT cid, title, url, submitter FROM " . $xoopsDB->prefix("xtorrent_downloads") . " WHERE lid=$lid");
                list($cid, $fileshowname, $url, $submitter) = $xoopsDB->fetchRow($result2);

                if ($sender != 0) {
                    $result3 = $xoopsDB->query("SELECT uname, email FROM " . $xoopsDB->prefix("users") . " WHERE uid=" . $sender . "");
                    list($sendername, $email) = $xoopsDB->fetchRow($result3);
                }

                $result4 = $xoopsDB->query("SELECT uname, email FROM " . $xoopsDB->prefix("users") . " WHERE uid=" . $sender . "");
                list($ownername, $owneremail) = $xoopsDB->fetchRow($result4);

                echo "<tr style='text-align:center;'>
                  		<td class='head'>" . $reportid . "</td>
                  		<td class='even' style='text-align:left;'>
                      <a href='" . XOOPS_URL . "/modules/xtorrent/singlefile.php?cid=" . $cid . "&amp;lid=" . $lid . "' target='_blank'>" . $fileshowname . "</a></td>";

                if ($email == "") {
                    echo "<td class='even'>" . $sendername . " (" . $ip . ")";
                } else {
                    echo "<td class='even'><a href='mailto:" . $email . "'>" . $sendername . "</a> (" . $ip . ")";
                }
                if ($owneremail == '') {
                    echo "<td class='even'>" . $ownername;
                } else {
                    echo "<td class='even'><a href='mailto:" . $owneremail . "'>" . $ownername. "</a>";
                }
                echo "</td>
                  		<td class='even' style='text-align:center;'>" . formatTimestamp($date, $xoopsModuleConfig['dateformat']) . "</td>
                  		<td class='even' style='text-align:center;white-space:nowrap;'>
                  		<a href='brokendown.php?op=ignoreBrokenDownloads&amp;lid=" . $lid . "'>" . $imagearray['ignore'] . "</a>
                  		<a href='index.php?op=Download&amp;lid=" . $lid . "'> " . $imagearray['editimg'] . " </a>
                  		<a href='brokendown.php?op=delBrokenDownloads&amp;lid=" . $lid . "'>" . $imagearray['deleteimg'] . "</a>";
                $ack_image = ($acknowledged) ? $imagearray['ack_yes'] : $imagearray['ack_no'];
                echo "<a href='brokendown.php?op=updateNotice&amp;lid=$lid&ack=$acknowledged'>" . $ack_image . " </a>";
                $con_image = ($confirmed) ? $imagearray['con_yes'] : $imagearray['con_no'];
                echo "<a href='brokendown.php?op=updateNotice&amp;lid=$lid&amp;con=$confirmed'>" . $con_image . " </a></td></tr>";
            }
        }
        echo"</table></fieldset>";
}
require_once __DIR__ . '/admin_footer.php';
