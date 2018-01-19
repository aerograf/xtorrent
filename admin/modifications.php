<?php

require_once __DIR__ . '/admin_header.php';

if (!isset($_POST['op'])) {
    $op = isset($_GET['op']) ? $_GET['op'] : 'main';
} else {
    $op = $_POST['op'];
}

switch ($op) {
    case 'listModReqshow':

        include XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

        global $xoopsDB, $myts, $mytree, $xoopsModuleConfig, $xoopsUser;

        xoops_cp_header();
        $adminObject = \Xmf\Module\Admin::getInstance();
        $adminObject->displayNavigation(basename(__FILE__));

        $requestid = (int)$_GET['requestid'];

        $sql       = 'SELECT lid, title, url, mirror, homepage, homepagetitle, cid, version, submitter, size, platform,  
  			license, limitations, price, description, features, requirements, dhistory, screenshot, modifysubmitter, requestid
  			FROM ' . $xoopsDB->prefix('xtorrent_mod') . ' WHERE requestid=' . $_GET['requestid'];
        $mod_array = $xoopsDB->fetchArray($xoopsDB->query($sql));
        unset($sql);

        $sql        = 'SELECT lid, title, url, mirror, homepage, homepagetitle, cid, version, publisher, size, platform,  
  			license, limitations, price, description, features, requirements, dhistory, screenshot, submitter 
  			FROM ' . $xoopsDB->prefix('xtorrent_downloads') . ' WHERE lid=' . $mod_array['lid'];
        $orig_array = $xoopsDB->fetchArray($xoopsDB->query($sql));
        unset($sql);

        $orig_user      = new XoopsUser($orig_array['submitter']);
        $submittername  = XoopsUserUtility::getUnameFromId($orig_array['submitter']); // $orig_user->getvar("uname");
        $submitteremail = $orig_user->getUnameFromId('email');

        echo '<div><b>' . _AM_XTORRENT_MOD_MODPOSTER . '</b> ' . $submittername . '</div>';
        $not_allowed = ['lid', 'submitter', 'requestid', 'modifysubmitter'];
        $sform       = new XoopsThemeForm(_AM_XTORRENT_MOD_ORIGINAL, 'storyform', 'index.php');
        foreach ($orig_array as $key => $content) {
            if (in_array($key, $not_allowed)) {
                continue;
            }
            $lang_def = constant('_AM_XTORRENT_MOD_' . strtoupper($key));

            if ('platform' == $key || 'license' == $key || 'limitations' == $key) {
                $content = $xoopsModuleConfig[$key][$orig_array[$key]];
            }
            if ('cid' == $key) {
                $sql     = 'SELECT title FROM ' . $xoopsDB->prefix('xtorrent_cat') . ' WHERE cid=' . $content . '';
                $row     = $xoopsDB->fetchArray($xoopsDB->query($sql));
                $content = $row['title'];
            }
            if ('forumid' == $key) {
                $content          = '';
                $moduleHandler    = xoops_getHandler('module');
                $xoopsforumModule = $moduleHandler->getByDirname('newbb');
                $sql              = 'SELECT title FROM ' . $xoopsDB->prefix('bb_categories') . ' WHERE cid=' . $content . '';
                if ($xoopsforumModule && $content > 0) {
                    $content = "<a href='" . XOOPS_URL . '/modules/newbb/viewforum.php?forum=' . $content . "'>Forumid</a>";
                } else {
                    $content = '';
                }
            }
            if ('screenshot' == $key) {
                $content = '';
                if ($content > 0) {
                    $content = "<img src='" . XOOPS_URL . '/' . $xoopsModuleConfig['screenshots'] . '/' . $logourl . "' width='" . $xoopsModuleConfig['shotwidth'] . "' alt='' >";
                }
            }
            if ('features' == $key || 'requirements' == $key) {
                if ('' != $content) {
                    $downrequirements = explode('|', trim($content));
                    foreach ($downrequirements as $bi) {
                        $content = '<li>' . $bi;
                    }
                }
            }
            if ('dhistory' == $key) {
                $content = $myts->displayTarea($content, 1, 0, 0, 0, 1);;
            }
            $sform->addElement(new XoopsFormLabel($lang_def, $content));
        }
        $sform->display();

        $orig_user      = new XoopsUser($mod_array['modifysubmitter']);
        $submittername  = XoopsUserUtility::getUnameFromId($mod_array['modifysubmitter']);
        $submitteremail = $orig_user->getUnameFromId('email');

        echo '<div><b>' . _AM_XTORRENT_MOD_MODIFYSUBMITTER . '</b> ' . $submittername . '</div>';
        $sform = new XoopsThemeForm(_AM_XTORRENT_MOD_PROPOSED, 'storyform', 'modifications.php');
        foreach ($mod_array as $key => $content) {
            if (in_array($key, $not_allowed)) {
                continue;
            }
            $lang_def = constant('_AM_XTORRENT_MOD_' . strtoupper($key));

            if ('platform' == $key || 'license' == $key || 'limitations' == $key) {
                $content = $xoopsModuleConfig[$key][$orig_array[$key]];
            }
            if ('cid' == $key) {
                $sql     = 'SELECT title FROM ' . $xoopsDB->prefix('xtorrent_cat') . ' WHERE cid=' . $content . '';
                $row     = $xoopsDB->fetchArray($xoopsDB->query($sql));
                $content = $row['title'];
            }
            if ('forumid' == $key) {
                $content          = '';
                $moduleHandler    = xoops_getHandler('module');
                $xoopsforumModule = $moduleHandler->getByDirname('newbb');
                $sql              = 'SELECT title FROM ' . $xoopsDB->prefix('bb_categories') . ' WHERE cid=' . $content . '';
                $content          = '';
                if ($xoopsforumModule && $content > 0) {
                    $content = "<a href='" . XOOPS_URL . '/modules/newbb/viewforum.php?forum=' . $content . "'>Forumid</a>";
                }
            }
            if ('screenshot' == $key) {
                $content = '';
                if ($content > 0) {
                    $content = "<img src='" . XOOPS_URL . '/' . $xoopsModuleConfig['screenshots'] . '/' . $logourl . "' width='" . $xoopsModuleConfig['shotwidth'] . "' alt='' >";
                }
            }
            if ('features' == $key || 'requirements' == $key) {
                if ('' != $content) {
                    $downrequirements = explode('|', trim($content));
                    foreach ($downrequirements as $bi) {
                        $content = '<li>' . $bi;
                    }
                }
            }
            if ('dhistory' == $key) {
                $content = $myts->displayTarea($content, 1, 0, 0, 0, 1);;
            }
            $sform->addElement(new XoopsFormLabel($lang_def, $content));
        }

        $button_tray = new XoopsFormElementTray('', '');
        $button_tray->addElement(new XoopsFormHidden('requestid', $requestid));
        $button_tray->addElement(new XoopsFormHidden('lid', $mod_array['requestid']));
        $hidden = new XoopsFormHidden('op', 'changeModReq');
        $button_tray->addElement($hidden);
        if ($mod_array) {
            $butt_dup = new XoopsFormButton('', '', _AM_XTORRENT_BAPPROVE, 'submit');
            $butt_dup->setExtra('onclick="this.form.elements.op.value=\'changeModReq\'"');
            $button_tray->addElement($butt_dup);
        }
        $butt_dupct2 = new XoopsFormButton('', '', _AM_XTORRENT_BIGNORE, 'submit');
        $butt_dupct2->setExtra('onclick="this.form.elements.op.value=\'ignoreModReq\'"');
        $button_tray->addElement($butt_dupct2);
        $sform->addElement($button_tray);
        $sform->display();

        require_once __DIR__ . '/admin_footer.php';
        exit();
        break;

    case 'changeModReq':
        global $xoopsDB, $_POST, $eh, $myts;

        $sql        = 'SELECT * FROM ' . $xoopsDB->prefix('xtorrent_mod') . ' WHERE requestid=' . $_POST['requestid'] . '';
        $down_array = $xoopsDB->fetchArray($xoopsDB->query($sql));

        $lid           = $down_array['lid'];
        $cid           = $down_array['cid'];
        $title         = $down_array['title'];
        $url           = $down_array['url'];
        $homepage      = $down_array['homepage'];
        $homepagetitle = $down_array['homepagetitle'];
        $version       = $down_array['version'];
        $size          = $down_array['size'];
        $platform      = $down_array['platform'];
        $publisher     = $down_array['publisher'];
        $screenshot    = $down_array['screenshot'];
        $price         = $down_array['price'];
        $description   = $down_array['description'];
        $mirror        = $down_array['mirror'];
        $license       = $down_array['license'];
        $features      = $down_array['features'];
        $requirements  = $down_array['requirements'];
        $limitations   = $down_array['limitations'];
        $dhistory      = $down_array['dhistory'];
        $submitter     = $down_array['submitter'];
        $updated       = time();

        $xoopsDB->query('UPDATE ' . $xoopsDB->prefix('xtorrent_downloads') . " SET cid = $cid, title = '$title', 
  			url = '$url', mirror = '$mirror', license = '$license', features = '$features', homepage = '$homepage', version = '$version', size = $size, platform = '$platform',
  			screenshot = '$screenshot', publisher = '$publisher', status = '2', price = '$price', requirements = '$requirements', 
  			homepagetitle = '$homepagetitle', limitations = '$limitations', dhistory = '$dhistory', updated = '$updated', 
  			description = '$description' WHERE lid = $lid");
        $sql    = 'DELETE FROM ' . $xoopsDB->prefix('xtorrent_mod') . ' WHERE requestid = ' . $_POST['requestid'] . '';
        $result = $xoopsDB->query($sql);
        redirect_header('index.php', 1, _AM_XTORRENT_MOD_REQUPDATED);
        break;

    case 'ignoreModReq':
        global $xoopsDB, $_POST;
        $sql = sprintf('DELETE FROM ' . $xoopsDB->prefix('xtorrent_mod') . ' WHERE requestid = ' . $_POST['requestid'] . '');
        $xoopsDB->query($sql);
        redirect_header('index.php', 1, _AM_XTORRENT_MOD_REQDELETED);
        break;

    case 'main':
    default:

        include_once XOOPS_ROOT_PATH . '/class/xoopstree.php';

        global $xoopsModuleConfig;
        $start  = isset($_GET['start']) ? (int)$_GET['start'] : 0;
        $mytree = new XoopsTree($xoopsDB->prefix('xtorrent_mod'), 'requestid', 0);

        global $xoopsDB, $myts, $mytree, $xoopsModuleConfig;
        $sql              = 'SELECT * FROM ' . $xoopsDB->prefix('xtorrent_mod') . ' ORDER BY requestdate DESC';
        $result           = $xoopsDB->query($sql, $xoopsModuleConfig['admin_perpage'], $start);
        $totalmodrequests = $xoopsDB->getRowsNum($xoopsDB->query($sql));

        xoops_cp_header();
        $adminObject = \Xmf\Module\Admin::getInstance();
        $adminObject->displayNavigation(basename(__FILE__));

        echo "<fieldset><legend style='font-weight:bold;color:#900;'>" . _AM_XTORRENT_MOD_MODREQUESTSINFO . "</legend>
          		<div style='padding:4px;'><b>" . _AM_XTORRENT_MOD_TOTMODREQUESTS . '</b> ' . $totalmodrequests . "</div><br>
          		<table class='outer' style='width:100%;'>
          		<tr>
          		<th style='text-align:center;'>" . _AM_XTORRENT_MOD_MODID . '</th>
          		<th>' . _AM_XTORRENT_MOD_MODTITLE . "</th>
          		<th style='text-align:center;'>" . _AM_XTORRENT_MOD_MODIFYSUBMIT . "</th>
          		<th style='text-align:center;'>" . _AM_XTORRENT_MOD_DATE . "</th>
          		<th style='text-align:center;'>" . _AM_XTORRENT_MINDEX_ACTION . '</th>
          		</tr>';
        if ($totalmodrequests > 0) {
            while ($down_arr = $xoopsDB->fetchArray($result)) {
                $path      = $mytree->getNicePathFromId($down_arr['requestid'], 'title', 'modifications.php?op=listModReqshow&requestid');
                $path      = str_replace('/', '', $path);
                $path      = str_replace(':', '', trim($path));
                $title     = trim($path);
                $submitter = XoopsUserUtility::getUnameFromId($down_arr['modifysubmitter']);;
                $requestdate = formatTimestamp($down_arr['requestdate'], $xoopsModuleConfig['dateformat']);
                echo "<tr>
          		<td class='head' style='text-align:center;'>" . $down_arr['requestid'] . "</td>
          		<td class='even'>" . $title . "</td>
          		<td class='even' style='text-align:center;'>" . $submitter . "</td>
          		<td class='even' style='text-align:center;'>" . $requestdate . "</td>
          		<td class='even' style='text-align:center;'><a href='modifications.php?op=listModReqshow&amp;requestid=" . $down_arr['requestid'] . "'>" . _AM_XTORRENT_MOD_VIEW . '</a></td>
          		</tr>';
            }
        } else {
            echo "<tr><td class='head' colspan='7' style='text-align:center;'>" . _AM_XTORRENT_MOD_NOMODREQUEST . '</td></tr>';
        }
        echo '</table></fieldset>';

        include_once XOOPS_ROOT_PATH . '/class/pagenav.php';
        $page    = ($totalmodrequests > $xoopsModuleConfig['admin_perpage']) ? _AM_XTORRENT_MINDEX_PAGE : '';
        $pagenav = new XoopsPageNav($totalmodrequests, $xoopsModuleConfig['admin_perpage'], $start, 'start');
        echo "<div style='padding:8px;float:right;'>" . $page . '' . $pagenav->renderNav() . '</div>';
        require_once __DIR__ . '/admin_footer.php';
}
