<?php 

include 'header.php';
include_once XOOPS_ROOT_PATH . '/class/xoopstree.php';
include_once XOOPS_ROOT_PATH . '/include/xoopscodes.php';

$myts   = MyTextSanitizer::getInstance(); // MyTextSanitizer object
$mytree = new XoopsTree($xoopsDB->prefix('xtorrent_cat'), 'cid', 'pid');

global $xoopsModuleConfig;

if (!is_object($xoopsUser) && !$xoopsModuleConfig['anonpost']) {
    redirect_header(XOOPS_URL . '/user.php', 1, _MD_XTORRENT_MUSTREGFIRST);
    exit();
}

if (!$xoopsModuleConfig['submissions']) {
    redirect_header('index.php', 1, _MD_XTORRENT_NOTALLOWESTOSUBMIT);
    exit();
}

if (isset($_POST['submit']) && !empty($_POST['submit'])) {
    $notify = !empty($_POST['notify']) ? 1 : 0;

    $lid = !empty($_POST['lid']) ? intval($_POST['lid']) : 0 ;
    $cid = !empty($_POST['cid']) ? intval($_POST['cid']) : 0 ;

    if (empty($_FILES['userfile']['name']) && $_POST['url'] && '' != $_POST['url'] && 'https://' != $_POST['url']) {
        $url   = ('https://' != $_POST['url']) ? $myts->addslashes($_POST['url']) : '';
        $size  = empty($size) || !is_numeric($size) ? $myts->addslashes($_POST['size']) : 0;
        $title = $myts->addslashes(trim($_POST['title']));
    } else {
        global $_FILES;

        $down  = xtorrent_uploading($_FILES, $xoopsModuleConfig['uploaddir'], '', 'index.php', 0, 0, 0);
        $url   = $down['url'];
        $size  = $down['size'];
        $title = $_FILES['userfile']['name'];
        $title = rtrim(xtorrent_strrrchr($title, '.'), '.');
        $title = (isset($_POST['title_checkbox']) && 1 == $_POST['title_checkbox']) ? $title : $myts->addslashes(trim($_POST['title']));
    }

    $homepage      = '';
    $homepagetitle = '';
    if (!empty($_POST['homepage']) || 'http://' != $_POST['homepage']) {
        $homepage      = $myts->addslashes(formatURL(trim($_POST['homepage'])));
        $homepagetitle = $myts->addslashes(trim($_POST['homepagetitle']));
    }
    $version         = $myts->addslashes($_POST['version']);
    $platform        = $myts->addslashes($_POST['platform']);
    $description     = $myts->addslashes($_POST['description']);
    $submitter       = !empty($xoopsUser) ? $xoopsUser->getVar('uid') : 0;
    $publisher       = $myts->addslashes(trim($_POST['publisher']));
    $price           = $myts->addslashes(trim($_POST['price']));
    $mirror          = $myts->addslashes(formatURL(trim($_POST['mirror'])));
    $license         = $myts->addslashes(trim($_POST['license']));
    $paypalemail     = $myts->addslashes(trim($_POST['paypalemail']));
    $currency        = $myts->addslashes(trim($_POST['currency']));
    $features        = $myts->addslashes(trim($_POST['features']));
    $requirements    = $myts->addslashes(trim($_POST['requirements']));
    $forumid         = (isset($_POST['forumid']) && $_POST['forumid'] > 0) ? intval($_POST['forumid']) : 0;
    $limitations     = isset($_POST['limitations']) ? $myts->addslashes($_POST['limitations']) : '';
    $dhistory        = isset($_POST['dhistory']) ? $myts->addslashes($_POST['dhistory']) : '';
    $dhistoryhistory = isset($_POST['dhistoryaddedd']) ? $myts->addslashes($_POST['dhistoryaddedd']) : '';
    if ($lid > 0 && !empty($dhistoryhistory)) {
        $dhistory = $dhistory . "\n\n";
        $time     = time();
        $dhistory .= '<b>' . formatTimestamp($time, $xoopsModuleConfig['dateformat']) . '</b>';
        $dhistory .= $dhistoryhistory;
    }
    $offline = (isset($_POST['offline']) && 1 == $_POST['offline']) ? 1 : 0;
    $date = time();
    $publishdate = 0;
    $notifypub = (isset($_POST['notifypub']) && 1 == $_POST['notifypub']) ? 1 : 0;
    
    $screenshot = '';
    if (isset($_FILES['screenshot']['name']) && !empty($_FILES['screenshot']['name'])) {
        $allowed_mimetypes = $allowed_mimetypes = ['image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png'];
        $maxfilesize       = $xoopsModuleConfig['maxfilesize'];
        $maxfilewidth      = $xoopsModuleConfig['maximgwidth'];
        $maxfileheight     = $xoopsModuleConfig['maximgheight'];
        $uploaddir         = XOOPS_ROOT_PATH . '/' . $xoopsModuleConfig['screenshots'] . '/';
        $screenshot        = strtolower($_FILES['screenshot']['name']);

        include_once XOOPS_ROOT_PATH . '/modules/xtorrent/class/uploader.php';
        $uploader = new XoopsMediaUploader($uploaddir, $allowed_mimetypes, $maxfilesize, $maxfilewidth, $maxfileheight);

        if ($uploader->fetchMedia($_POST['xoops_upload_file'][1])) {
            if (!$uploader->upload()) {
                $errors = $uploader->getErrors();
                redirect_header('index.php?op=downloadsConfigMenu', 1, $errors);
            } else {
            }
        } else {
            $errors = $uploader->getErrors();
            redirect_header('index.php?op=downloadsConfigMenu', 1, $errors);
        }
    }

    $ipaddress = $_SERVER['REMOTE_ADDR'];
    if (0 == $lid) {
        if (1 == $xoopsModuleConfig['autoapprove']) {
            $publishdate = time();
            $status = 1;
        }
        $status = (1 == $xoopsModuleConfig['autoapprove']) ? 1 : 0 ;
        $query = 'INSERT INTO ' . $xoopsDB->prefix('xtorrent_downloads') . ' 
            			(lid, cid, title, url, homepage, version, size, platform, screenshot, submitter, publisher, status, 
            			date, hits, rating, votes, comments, price, mirror, license, paypalemail, features, requirements, 
            			homepagetitle, forumid, limitations, dhistory, published, expired,offline, description, ipaddress, notifypub, currency)';
        $query .= " VALUES 	('', $cid, '$title', '$url', '$homepage', '$version', $size, '$platform', '$screenshot', 
            			'$submitter', '$publisher','$status', '$date', 0, 0, 0, 0, '$price', '$mirror', '$license', '$paypalemail', 
            			'$features', '$requirements', '$homepagetitle', '$forumid', '$limitations', '$dhistory', '$publishdate', 
            			0, '$offline', '$description', '$ipaddress', '$notifypub', '$currency')";
        $result = $xoopsDB->queryF($query);
        $error  = _MD_XTORRENT_INFONOSAVEDB;
        $error .= $query;
        if (!$result) {
            trigger_error($error, E_USER_ERROR);
        }
        $newid = $xoopsDB->getInsertId();
        $groups = [1, 2];
        xtorrent_save_Permissions($groups, $newid, 'xtorrentownFilePerm');
        // START TO CHECK FOR POLLING OF TORRENT
        //echo "Please wait a moment while we poll the torrent...";
        error_reporting(E_ALL);
        include 'include/pollall.php';
        
        if (1 == $xoopsModuleConfig['poll_torrent']) {
            $rt = poll_torrent($newid);
        }
        
        if (1 == $xoopsModuleConfig['poll_tracker']) {
            $rt = poll_tracker($rt, $newid, $xoopsModuleConfig['poll_tracker_timeout']);
        }

        /*
        *  Notify of new link (anywhere) and new link in category
        */
        $notification_handler  = xoops_gethandler('notification');
        $tags                  = [];
        $tags['FILE_NAME']     = $title;
        $tags['FILE_URL']      = XOOPS_URL . '/modules/xtorrent/singlefile.php?cid=' . $cid . '&amp;lid=' . $newid;
        $sql                   = 'SELECT title FROM ' . $xoopsDB->prefix('xtorrent_cat') . ' WHERE cid=' . $cid;
        $result                = $xoopsDB->query($sql);
        $row                   = $xoopsDB->fetchArray($result);
        $tags['CATEGORY_NAME'] = $row['title'];
        $tags['CATEGORY_URL']  = XOOPS_URL . '/modules/xtorrent/viewcat.php?cid=' . $cid;
        if (1 == $xoopsModuleConfig['autoapprove']) {
            $notification_handler->triggerEvent('global', 0, 'new_file', $tags);
            $notification_handler->triggerEvent('category', $cid, 'new_file', $tags);
            redirect_header('index.php', 2, _MD_XTORRENT_ISAPPROVED . '');
        } else {
            $tags['WAITINGFILES_URL'] = XOOPS_URL . '/modules/xtorrent/admin/newdownloads.php';
            $notification_handler->triggerEvent('global', 0, 'file_submit', $tags);
            $notification_handler->triggerEvent('category', $cid, 'file_submit', $tags);
            if ($notify) {
                include_once XOOPS_ROOT_PATH . '/include/notification_constants.php';
                $notification_handler->subscribe('file', $newid, 'approve', XOOPS_NOTIFICATION_MODE_SENDONCETHENDELETE);
            }
            redirect_header('index.php', 2, _MD_XTORRENT_THANKSFORINFO);
        }
        exit();
    } else {
        $updated = (isset($_POST['up_dated']) && 0 == $_POST['up_dated']) ? 0 : time();
        
        if (1 == $xoopsModuleConfig['autoapprove']) {
            $updated               = time();
            $xoopsDB->query('UPDATE '
                            . $xoopsDB->prefix('xtorrent_downloads') . " SET cid = $cid, title = '$title', url = '$url', mirror = '$mirror', license = '$license', features = '$features', homepage = '$homepage', version = '$version', size = $size, platform = '$platform', screenshot = '$screenshot', publisher = '$publisher', price = '$price', requirements = '$requirements', homepagetitle = '$homepagetitle', limitations = '$limitations', dhistory = '$dhistory', updated = '$updated', offline = '$offline', description = '$description', ipaddress = '$ipaddress', notifypub = '$notifypub', paypalemail = '$paypalemail', currency = '$currency' WHERE lid = $lid");
            $notification_handler  = xoops_gethandler('notification');
            $tags                  = [];
            $tags['FILE_NAME']     = $title;
            $tags['FILE_URL']      = XOOPS_URL . '/modules/xtorrent/singlefile.php?cid=' . $cid . '&amp;lid=' . $lid;
            $sql                   = 'SELECT title FROM ' . $xoopsDB->prefix('xtorrent_cat') . ' WHERE cid=' . $cid;
            $result                = $xoopsDB->query($sql);
            $row                   = $xoopsDB->fetchArray($result);
            $tags['CATEGORY_NAME'] = $row['title'];
            $tags['CATEGORY_URL']  = XOOPS_URL . '/modules/xtorrent/viewcat.php?cid=' . $cid;
        } else {
            $modifysubmitter = $xoopsUser->uid();
            $requestdate = time();
            $sql = 'INSERT INTO ' . $xoopsDB->prefix('xtorrent_mod') . ' 
          				(requestid, lid, cid, title, url, homepage, version, size, platform, screenshot, publisher, price, mirror, license, paypalemail, features, requirements, homepagetitle, forumid, limitations, dhistory, description, modifysubmitter, requestdate, currency)';
            $sql .= " VALUES 	('', $lid, $cid, '$title', '$url', '$homepage', '$version', $size, '$platform', 
          				'$screenshot', '$publisher', '$price', '$mirror', '$license', '$paypalemail', '$features', 
          				'$requirements', '$homepagetitle', '$forumid', '$limitations', '$dhistory', '$description', 
          				'$modifysubmitter', '$requestdate', '$currency')";
            $result = $xoopsDB->query($sql);
            $error  = '' . _MD_XTORRENT_ERROR . ': <br><br>' . $sql;
            if (!$result) {
                trigger_error($error, E_USER_ERROR);
            }
            $tags                      = [];
            $tags['MODIFYREPORTS_URL'] = XOOPS_URL . '/modules/xtorrent/admin/index.php?op=listModReq';
            $notification_handler      = xoops_gethandler('notification');
            $notification_handler->triggerEvent('global', 0, 'file_modify', $tags);
        }

        if (1 == $xoopsModuleConfig['autoapprove']) {
            $notification_handler->triggerEvent('global', 0, 'new_file', $tags);
            $notification_handler->triggerEvent('category', $cid, 'new_file', $tags);
            redirect_header('index.php', 2, _MD_XTORRENT_ISAPPROVED . '');
        } else {
            $tags['WAITINGFILES_URL'] = XOOPS_URL . '/modules/xtorrent/admin/index.php?op=listNewDownloads';
            $notification_handler->triggerEvent('global', 0, 'file_submit', $tags);
            $notification_handler->triggerEvent('category', $cid, 'file_submit', $tags);
            if ($notify) {
                include_once XOOPS_ROOT_PATH . '/include/notification_constants.php';
                $notification_handler->subscribe('file', $newid, 'approve', XOOPS_NOTIFICATION_MODE_SENDONCETHENDELETE);
            }
            redirect_header('index.php', 2, _MD_XTORRENT_THANKSFORINFO);
            exit();
        }
    }
} else {
    include XOOPS_ROOT_PATH . '/header.php';
    include_once XOOPS_ROOT_PATH . '/class/xoopstree.php';
    include XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

    global $_FILES, $xoopsModuleConfig, $xoopsConfig;

    if ($xoopsModuleConfig['showdisclaimer'] && !isset($_GET['agree'])) {
        echo "<p><div align = 'center'>" . xtorrent_imageheader() . '</div></p>
    		<h4>' . _MD_XTORRENT_DISCLAIMERAGREEMENT . '</h4>
    		<p><div>' . $myts->displayTarea($xoopsModuleConfig['disclaimer'], 0, 1, 1, 1, 1) . "</div></p>
    		<form action='submit.php' method='post'>
    		<div align='center'><b>" . _MD_XTORRENT_DOYOUAGREE . "</b><br><br>
    		<input type = 'button' onclick = 'location=\"submit.php?agree=1\"' class='formButton' value='" . _MD_XTORRENT_AGREE . "' alt='" . _MD_XTORRENT_AGREE . "' />
    		&nbsp;
    		<input type='button' onclick = 'location=\"index.php\"' class='formButton' value='" . _CANCEL . "' alt='" . _CANCEL . "' />
    		</div></form>";
        include XOOPS_ROOT_PATH . '/footer.php';
        exit();
    }

    $lid           = 0;
    $cid           = 0;
    $title         = '';
    $url           = 'https://';
    $homepage      = 'https://';
    $homepagetitle = '';
    $version       = '';
    $size          = 0;
    $platform      = '';
    $screenshot    = '';
    $price         = 0;
    $currency      = 'USD';
    $description   = '';
    $mirror        = 'https://';
    $license       = '';
    $paypalemail   = '';
    $features      = '';
    $requirements  = '';
    $forumid       = 0;
    $limitations   = '';
    $dhistory      = '';
    $status        = 0;
    $is_updated    = 0;
    $offline       = 0;
    $published     = 0;
    $expired       = 0;
    $updated       = 0;
    $versiontypes  = '';
    $publisher     = '';

    if (isset($_POST['lid'])) {
        $lid = intval($_POST['lid']);
    } elseif (isset($_GET['lid'])) {
        $lid = intval($_GET['lid']);
    } else {
        $lid = 0;
    }

    echo "
		<p><div align = 'center'>" . xtorrent_imageheader() . '</div></p>
		<div>' . _MD_XTORRENT_SUB_SNEWMNAMEDESC . '</div>';
    if ($lid) {
        $sql = 'SELECT * FROM ' . $xoopsDB->prefix('xtorrent_downloads') . ' WHERE lid=' . $lid . '';
        $down_array = $xoopsDB->fetchArray($xoopsDB->query($sql));

        $lid           = $myts->htmlSpecialChars($down_array['lid']);
        $cid           = $myts->htmlSpecialChars($down_array['cid']);
        $title         = $myts->htmlSpecialChars($down_array['title']);
        $url           = $myts->htmlSpecialChars($down_array['url']);
        $homepage      = $myts->htmlSpecialChars($down_array['homepage']);
        $homepagetitle = $myts->htmlSpecialChars($down_array['homepagetitle']);
        $version       = $myts->htmlSpecialChars($down_array['version']);
        $size          = $myts->htmlSpecialChars($down_array['size']);
        $platform      = $myts->htmlSpecialChars($down_array['platform']);
        $paypalemail   = $myts->htmlSpecialChars($down_array['paypalemail']);
        $currency      = $myts->htmlSpecialChars($down_array['currency']);
        $publisher     = $myts->htmlSpecialChars($down_array['publisher']);
        $screenshot    = $myts->htmlSpecialChars($down_array['screenshot']);
        $price         = $myts->htmlSpecialChars($down_array['price']);
        $description   = $myts->htmlSpecialChars($down_array['description']);
        $mirror        = $myts->htmlSpecialChars($down_array['mirror']);
        $license       = $myts->htmlSpecialChars($down_array['license']);
        $features      = $myts->htmlSpecialChars($down_array['features']);
        $requirements  = $myts->htmlSpecialChars($down_array['requirements']);
        $limitations   = $myts->htmlSpecialChars($down_array['limitations']);
        $dhistory      = $myts->htmlSpecialChars($down_array['dhistory']);
        $published     = $myts->htmlSpecialChars($down_array['published']);
        $expired       = $myts->htmlSpecialChars($down_array['expired']);
        $updated       = $myts->htmlSpecialChars($down_array['updated']);
        $offline       = $myts->htmlSpecialChars($down_array['offline']);
        $forumid       = $myts->htmlSpecialChars($down_array['forumid']);
    }
    $sform = new XoopsThemeForm(_MD_XTORRENT_SUBMITCATHEAD, 'storyform', xoops_getenv('PHP_SELF'));
    $sform->setExtra('enctype="multipart/form-data"');

    $sform->addElement(new XoopsFormText(_MD_XTORRENT_FILETITLE, 'title', 50, 255, $title), true);
    $sform->addElement(new XoopsFormText(_MD_XTORRENT_DLURL, 'url', 50, 255, $url), false);
    if ($xoopsModuleConfig['useruploads']) {
        $sform->addElement(new XoopsFormFile(_MD_XTORRENT_UPLOAD_FILEC, 'userfile', 0), false);
    }
    $sform->addElement(new XoopsFormText(_MD_XTORRENT_MIRROR, 'mirror', 50, 255, $mirror), false);

    $mytree = new XoopsTree($xoopsDB->prefix('xtorrent_cat'), 'cid', 'pid');
    ob_start();
    $mytree->makeMySelBox('title', 'cid', $cid, 0);
    $sform->addElement(new XoopsFormLabel(_MD_XTORRENT_CATEGORYC, ob_get_contents()));
    ob_end_clean();

    $sform->addElement(new XoopsFormText(_MD_XTORRENT_HOMEPAGETITLEC, 'homepagetitle', 50, 255, $homepagetitle), false);
    $sform->addElement(new XoopsFormText(_MD_XTORRENT_HOMEPAGEC, 'homepage', 50, 255, $homepage), false);
    $sform->addElement(new XoopsFormText(_MD_XTORRENT_VERSIONC, 'version', 10, 20, $version), false);
    $sform->addElement(new XoopsFormText(_MD_XTORRENT_PUBLISHERC, 'publisher', 50, 255, $publisher), false);
    $sform->addElement(new XoopsFormText(_MD_XTORRENT_FILESIZEC, 'size', 10, 20, $size), false);

    $platform_array  = $xoopsModuleConfig['platform'];
    $platform_select = new XoopsFormSelect('', 'platform', $platform, '', '', 0);
    $platform_select->addOptionArray($platform_array);
    $platform_tray = new XoopsFormElementTray(_MD_XTORRENT_PLATFORMC, '&nbsp;');
    $platform_tray->addElement($platform_select);
    $sform->addElement($platform_tray);

    $license_array  = $xoopsModuleConfig['license'];
    $license_select = new XoopsFormSelect('', 'license', $license, '', '', 0);
    $license_select->addOptionArray($license_array);
    $license_tray = new XoopsFormElementTray(_MD_XTORRENT_LICENCEC, '&nbsp;');
    $license_tray->addElement($license_select);
    $sform->addElement($license_tray);

    $limitations_array  = $xoopsModuleConfig['limitations'];
    $limitations_select = new XoopsFormSelect('', 'limitations', $limitations, '', '', 0);
    $limitations_select->addOptionArray($limitations_array);
    $limitations_tray = new XoopsFormElementTray(_MD_XTORRENT_LIMITATIONS, '&nbsp;');
    $limitations_tray->addElement($limitations_select);

    $sform->addElement($limitations_tray);

    $price_array  = $xoopsModuleConfig['currencies'];
    $price_select = new XoopsFormSelect('', 'currency', $currency, '', '', 0);
    $price_select->addOptionArray($price_array);
    $price_tray = new XoopsFormElementTray(_MD_XTORRENT_PRICEC, '&nbsp;');
    $price_tray->addElement(new XoopsFormText('', 'price', 10, 20, $price), false);
    $price_tray->addElement($price_select);
    $sform->addElement($price_tray);
    
    $sform->addElement(new XoopsFormText(_MD_XTORRENT_PAYPAL, 'paypalemail', 50, 250, $paypalemail), false);
    $sform->addElement(new XoopsFormDhtmlTextArea(_MD_XTORRENT_DESCRIPTION, 'description', $description, 15, 60), true);
    $sform->addElement(new XoopsFormTextArea(_MD_XTORRENT_KEYFEATURESC, 'features', $features, 7, 60), false);
    $sform->addElement(new XoopsFormTextArea(_MD_XTORRENT_REQUIREMENTSC, 'requirements', $requirements, 7, 60), false);
    $sform->addElement(new XoopsFormTextArea(_MD_XTORRENT_HISTORYC, 'dhistory', $dhistory, 7, 60), false);
    if ($lid && !empty($dhistory)) {
        $sform->addElement(new XoopsFormTextArea(_MD_XTORRENT_HISTORYD, 'dhistoryaddedd', '', 7, 60), false);
    }
    $sform->addElement(new XoopsFormFile(_MD_XTORRENT_DUPLOADSCRSHOT, 'screenshot', 0), false);

    $option_tray     = new XoopsFormElementTray(_MD_XTORRENT_OPTIONS, '<br />');
    $notify_checkbox = new XoopsFormCheckBox('', 'notifypub');
    $notify_checkbox->addOption(1, _MD_XTORRENT_NOTIFYAPPROVE);
    $option_tray->addElement($notify_checkbox);
    $sform->addElement($option_tray);
    $button_tray = new XoopsFormElementTray('', '');

    $button_tray->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));
    $button_tray->addElement(new XoopsFormHidden('lid', $lid));
    $sform->addElement($button_tray);
    $sform->display();
    include XOOPS_ROOT_PATH . '/footer.php';
}
