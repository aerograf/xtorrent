<?php

require_once __DIR__ . '/admin_header.php';

//include '../functions.php';
include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
include_once XOOPS_ROOT_PATH . '/class/xoopstree.php';

$op     = strtolower($_REQUEST['op']);
$action = strtolower($_REQUEST['action']);
$server = isset($_REQUEST['srv']) ? $_REQUEST['srv'] : 0;

global $xoopsModule, $xoopsUser;
error_reporting(E_ALL);
$xoopsModule = XoopsModule::getByDirname('xsoap');
if (!$xoopsModule->mid()) {
    redirect_header(XOOPS_URL . '/', 3, 'X-SOAP Not Installed');
    exit();
} else {
    if (!file_exists(XOOPS_ROOT_PATH . '/class/soap/xoopssoap.php')) {
        foreach (get_loaded_extensions() as $ext) {
            if (strpos(' ' . $ext, 'soap') > 1) {
                $native = true;
            }
        }

        if (true != $native) {
            define('XOOPS_SOAP_LIB', 'NUSOAP');
            require_once XOOPS_ROOT_PATH . '/modules/xsoap/include/nusoap/nusoap.php';
        } else {
            define('XOOPS_SOAP_LIB', 'INHERIT');
        }
    } else {
        require_once XOOPS_ROOT_PATH . '/class/soap/xoopssoap.php';
    }
}

if (!$xoopsUser->isAdmin($xoopsModule->mid())) {
    redirect_header(XOOPS_URL . '/', 3, _NOPERM);
    exit();
} else {
    $servers = pharseSoapServer();
    if (empty($servers)) {
        redirect_header(XOOPS_URL . '/', 3, 'No SOAP Server Details Provided in Settings.');
        exit();
    }

    $sform = new XoopsThemeForm($heading, 'op', xoops_getenv('PHP_SELF') . "?op=$op&srv=$server");
    $sform->setExtra('enctype="multipart/form-data"');

    foreach ($servers as $key => $data) {
        $purl            = parse_url($data['uri']);
        $svr_array[$key] = $purl['host'];
    }

    $indeximage_select = new XoopsFormSelect('', 'server', $server);
    $indeximage_select->addOptionArray($svr_array);
    $indeximage_select->setExtra("onchange='location.href=\"" . XOOPS_URL . '/modules/xtorrent/admin/xsoap.php?op=' . $op . "&srv=\"+this.options[this.selectedIndex].value'");
    $indeximage_tray = new XoopsFormElementTray('Server', '&nbsp;');
    $indeximage_tray->addElement($indeximage_select);

    $sform->addElement($indeximage_tray);

    $client = new soapclient($servers[$server]['uri']);
    // Call the SOAP method
    $rnd    = rand(-100000, 100000000);
    $result = $client->call('xtorrent_key', [
                                              'username' => $servers[$server]['username'],
                                              'password' => $servers[$server]['password'],
                                              'passhash' => sha1((time() - $rnd) . $servers[$server]['username'] . $servers[$server]['password']),
                                              'rand'     => $rnd,
                                              'time'     => time()
                                          ]);

    $srv_key = new XoopsFormLabel('Server Key', $result['RESULT']['response_key']);
    $sform->addElement($srv_key);
    $srv_url = new XoopsFormLabel('Server URL', $result['xoops_url']);
    $sform->addElement($srv_url);
    $srv_sitename = new XoopsFormLabel('Server Sitename', $result['sitename']);
    $sform->addElement($srv_sitename);

    $site_url   = $result['xoops_url'];
    $site_name  = $result['sitename'];
    $server_key = $result['RESULT']['response_key'];

    switch ($op) {
        case 'category':
            switch ($_REQUEST['action']) {
                case 'scribe':
                    for ($r = 1; $r < $_REQUEST['total'] + 1; $r++) {
                        if (true != $_REQUEST['new'][$r]) {
                            $sql = 'UPDATE ' . $xoopsDB->prefix('xtorrent_soap_catmatch') . " SET cid = '" . $_REQUEST['catassign'][$r] . "', scid = '" . $_REQUEST['scid'][$r] . "', auto_approval = '" . $_REQUEST['auto_import'][$r] . "' WHERE id = '" . $_REQUEST['id'][$r] . "'";
                        } else {
                            $sql = 'INSERT INTO '
                                   . $xoopsDB->prefix('xtorrent_soap_catmatch')
                                   . " (cid, scid, stitle, sdescription, skey, auto_approval, server, username) VALUES ('"
                                   . $_REQUEST['catassign'][$r]
                                   . "', '"
                                   . $_REQUEST['scid'][$r]
                                   . "', '"
                                   . $_REQUEST['title'][$r]
                                   . "', '"
                                   . $_REQUEST['desc'][$r]
                                   . "', '"
                                   . $server_key
                                   . "', '"
                                   . $_REQUEST['auto_import'][$r]
                                   . "', '"
                                   . $servers[$server]['uri']
                                   . "', '"
                                   . $servers[$server]['username']
                                   . "')";
                        }
                        $ret = $xoopsDB->queryF($sql);
                    }

                    redirect_header(xoops_getenv('PHP_SELF') . "?op=$op&srv=$server", 3, 'Category Data Saved for Soap Transactions');
                    exit();
                    break;

                default:

                    xoops_cp_header();
                    $adminObject = \Xmf\Module\Admin::getInstance();
                    $adminObject->displayNavigation(basename(__FILE__));

                    $result = $client->call('xtorrent_categories', [
                                                                     'username' => $servers[$server]['username'],
                                                                     'password' => $servers[$server]['password'],
                                                                     'passhash' => sha1((time() - $rnd) . $servers[$server]['username'] . $servers[$server]['password']),
                                                                     'rand'     => $rnd,
                                                                     'time'     => time()
                                                                 ]);

                    $sql    = 'SELECT * FROM ' . $xoopsDB->prefix('xtorrent_soap_catmatch') . " WHERE skey = '$server_key'";
                    $ret    = $xoopsDB->queryF($sql);
                    $cordta = [];
                    while ($row = $xoopsDB->fetchArray($ret)) {
                        $cordta[] = $row;
                    }

                    $mytreechose = new XoopsTree($xoopsDB->prefix('xtorrent_cat'), 'cid', 'pid');

                    $iidx = [];
                    $dscx = [];
                    foreach ($cordta as $key => $row) {
                        $ii++;
                        /*
                        $dscx[$ii] = new XoopsFormLabel($row['stitle'], $row['sdescription']);
                        $sform -> addElement($dscx[$ii]);
                */
                        $iidx[$ii] = new XoopsFormElementTray($row['stitle'], '&nbsp;');

                        ob_start();
                        $mytreechose->makeMySelBox('title', 'title', $row['cid'], 1, "catassign[$ii]");
                        $iidx[$ii]->addElement(new XoopsFormLabel('Cat Assignment', ob_get_contents()));
                        ob_end_clean();
                        $iidx[$ii]->addElement(new XoopsFormRadioYN('Auto Import', "auto_import[$ii]", $row['auto_approval']));
                        $iidx[$ii]->addElement(new XoopsFormHidden("id[$ii]", $row['id']));
                        $iidx[$ii]->addElement(new XoopsFormHidden("desc[$ii]", $row['sdescription']));
                        $iidx[$ii]->addElement(new XoopsFormHidden("scid[$ii]", $row['scid']));
                        $iidx[$ii]->addElement(new XoopsFormHidden("new[$ii]", false));
                        $sform->addElement($iidx[$ii]);
                    }

                    foreach ($result['RESULT']['cats'] as $k => $r) {
                        $found = false;
                        foreach ($cordta as $ky => $rw) {
                            if ($rw['scid'] == $r['cid']) {
                                $found = true;
                            }
                        }

                        if (true != $found) {
                            $ii++;
                            /*
                              $dscx[$ii] = new XoopsFormLabel($r['title']." (new)", $r['description']);
                              $sform -> addElement($dscx[$ii]);
                  */
                            $iidx[$ii] = new XoopsFormElementTray($r['title'], '&nbsp;');

                            ob_start();
                            $mytreechose->makeMySelBox('title', 'title', 0, 1, "catassign[$ii]");
                            $iidx[$ii]->addElement(new XoopsFormLabel('Cat Assignment', ob_get_contents()));
                            ob_end_clean();
                            $iidx[$ii]->addElement(new XoopsFormRadioYN('Auto Import', "auto_import[$ii]", $r['auto_approval']));
                            $iidx[$ii]->addElement(new XoopsFormLabel('', '<strong>(new)</strong>'));
                            $iidx[$ii]->addElement(new XoopsFormHidden("desc[$ii]", $r['description']));
                            $iidx[$ii]->addElement(new XoopsFormHidden("title[$ii]", $r['title']));
                            $iidx[$ii]->addElement(new XoopsFormHidden("scid[$ii]", $r['cid']));
                            $iidx[$ii]->addElement(new XoopsFormHidden("new[$ii]", true));
                            $sform->addElement($iidx[$ii]);
                        }
                    }

                    $sform->addElement(new XoopsFormButton('', 'submit', 'Save Changes', 'submit'));
                    $sform->addElement(new XoopsFormHidden('action', 'scribe'));
                    $sform->addElement(new XoopsFormHidden('total', $ii));
            }
            break;
        case 'listing':
            xoops_cp_header();
            $adminObject = \Xmf\Module\Admin::getInstance();
            $adminObject->displayNavigation(basename(__FILE__));
            switch ($_REQUEST['action']) {
                case 'scribe':
                    $hmy = importtorrents($_REQUEST, $client, $servers, $server, $server_key, $site_name, $site_url);
                    if ($hmy > 0) {
                        redirect_header(xoops_getenv('PHP_SELF') . "?op=$op&srv=$server", 3, $hmy . ' Torrent(s) Imported');
                    } else {
                        redirect_header(xoops_getenv('PHP_SELF') . "?op=$op&srv=$server", 3, 'No Torrents Imported');
                    }
                    exit();
                    break;
                case 'search':
                    $toggles = xtorrent_getcookie('G', true);
                    ?>
                    <script src="/modules/xtorrent/assets/js/xtorrent_toggle.js" language="javascript"></script><?php
                    if (is_array($_REQUEST['category'])) {
                        $request['cid'] = $_REQUEST['category'];
                    }
                    if ($_REQUEST['from'] != $_REQUEST['to']) {
                        $request['from'] = strtotime($_REQUEST['from']);
                        $request['to']   = strtotime($_REQUEST['to']);
                    }
                    $request['datefield'] = $_REQUEST['datefield'];

                    $result = $client->call('xtorrent_listing', [
                                                                  'username' => $servers[$server]['username'],
                                                                  'password' => $servers[$server]['password'],
                                                                  'passhash' => sha1((time() - $rnd) . $servers[$server]['username'] . $servers[$server]['password']),
                                                                  'rand'     => $rnd,
                                                                  'time'     => time(),
                                                                  'request'  => $request
                                                              ]);

                    $sql = 'SELECT scid, slid, scrc FROM ' . $xoopsDB->prefix('xtorrent_soap_transactions') . ' WHERE ssitename = ' . $site_name . ' AND surl = ' . $site_url;
                    if (is_array($_REQUEST['category'])) {
                        $sql .= " AND scid in ('" . implode("','", $_REQUEST['category']) . "')";
                    }
                    $ret = $xoopsDB->queryF($sql);
                    while ($row = $xoopsDB->fetchArray($ret)) {
                        $cordta[$row['slid']] = ['scid' => $row['scid'], 'scrc' => $row['scrc']];
                    }
                    foreach ($result['RESULT']['data'] as $row) {
                        $uu = decodespecialchars($row);
                        $ii++;
                        if ($cordta[$uu['lid']]['scrc'] != $uu['crc']) {
                            $iidx[$ii] = new XoopsFormElementTray($uu['title'], '&nbsp;');
                            $objid     = 'toz_' . $uu['lid'] . $uu['cid'];
                            $objshow   = (count($toggles) > 0) ? (in_array($objid, $toggles) ? false : true) : true;

                            $display      = $objshow ? 'none;' : 'block;';
                            $display_text = $objshow ? 'Open' : 'Close';
                            $display_icon = $objshow ? XOOPS_URL . '/modules/xtorrent/images/down.gif' : XOOPS_URL . '/modules/xtorrent/images/up.gif';

                            $ti = '<img align="right" onclick="ToggleBlock2(\'' . 'toz_' . $uu['lid'] . $uu['cid'] . '\', this)" src="' . $display_icon . '" alt="' . $display_text . '" />';
                            $ti .= "<div id='toz_" . $uu['lid'] . $uu['cid'] . "' style=\"display: $display\">
                  	<table width=\"100%\" border=\"0\">
                  	  <tr>
                  		<td width='25%'>Title:</td>
                  		<td>" . $uu['title'] . '</td>
                  	  </tr>
                  	  <tr>
                  		<td>Paypal Email:</td>
                  		<td>' . $uu['paypalemail'] . '</td>
                  	  </tr>
                  	  <tr>
                  		<td>File CRC:</td>
                  		<td>' . $uu['crc'] . '</td>
                  	  </tr>
                  	  <tr>
                  		<td>Features:</td>
                  		<td> * ' . implode('<br> * ', explode('|', $uu['features'])) . '</td>
                  	  </tr>
                  	  <tr>
                  		<td>Requirements:</td>
                  		<td> * ' . implode('<br> * ', explode('|', $uu['requirements'])) . '</td>
                  	  </tr>
                  	  <tr>
                  		<td>Homepage:</td>
                  		<td><a href="' . $uu['homepage'] . '">' . $uu['homepagetitle'] . '</a></td>
                  	  </tr>
                  	  <tr>
                  		<td>Version:</td>
                  		<td>' . $uu['version'] . '</td>
                  	  </tr>
                  	  <tr>
                  		<td>Size:</td>
                  		<td>' . $uu['size'] . '</td>
                  	  </tr>
                  	  <tr>
                  		<td>Platform:</td>
                  		<td>' . $result['RESULT']['arrays']['platform'][$uu['platform']] . '</td>
                  	  </tr>
                  	  <tr>
                  		<td>Publisher</td>
                  		<td>' . $uu['publisher'] . '</td>
                  	  </tr>
                  	  <tr>
                  		<td>IP Address</td>
                  		<td>' . $uu['ipaddress'] . '</td>
                  	  </tr>
                  	</table></div>';
                            $iidx[$ii]->addElement(new XoopsFormLabel('', '<strong>Import: </strong><input type="checkbox" id="import[' . $ii . ']" name="import[' . $ii . ']" value="1">'));
                            $iidx[$ii]->addElement(new XoopsFormLabel('', '<strong>(new)</strong>'));
                            $iidx[$ii]->addElement(new XoopsFormLabel('', $ti));
                            $iidx[$ii]->addElement(new XoopsFormHidden("new[$ii]", true));
                            $iidx[$ii]->addElement(new XoopsFormHidden("lid[$ii]", $uu['lid']));
                            $sform->addElement($iidx[$ii]);
                        }
                    }
                    $sform->addElement(new XoopsFormLabel('Notify', '<input type="checkbox" id="notify" name="notify" value="1">&nbsp;<font size=-1>Notify Users of New Torrents</font>'));
                    $sform->addElement(new XoopsFormButton('', 'submit', 'Import Torrents', 'submit'));
                    $sform->addElement(new XoopsFormHidden('action', 'scribe'));
                    $sform->addElement(new XoopsFormHidden('total', $ii));

                    break;
                default:
                    $dscx = [];
                    $iidx = [];

                    $dscx[0]             = new XoopsFormSelect('Categories', 'category', 0, 4, true);
                    $dscx[1]             = new XoopsFormSelect('Search Field', 'datefield', 0, 1, false);
                    $cordtb['expired']   = 'Expiry Date';
                    $cordtb['date']      = 'Date of Ingestion';
                    $cordtb['published'] = 'Publishing Date';
                    $dscx[1]->addOptionArray($cordtb);
                    $sql    = 'SELECT scid, stitle FROM ' . $xoopsDB->prefix('xtorrent_soap_catmatch') . ' WHERE skey = ' . $server_key;
                    $ret    = $xoopsDB->queryF($sql);
                    $cordta = [];
                    //$cordta[0] = '--------------------------------';

                    while ($row = $xoopsDB->fetchArray($ret)) {
                        $cordta[$row['scid']] = $row['stitle'];
                    }
                    $dscx[0]->addOptionArray($cordta);
                    $sform->addElement($dscx[0]);
                    $sform->addElement(new XoopsFormDateTime('Search From', 'from'));
                    $sform->addElement(new XoopsFormDateTime('Search To', 'to'));
                    $sform->addElement($dscx[1]);
                    $sform->addElement(new XoopsFormButton('', 'submit', 'Search Listings', 'submit'));
                    $sform->addElement(new XoopsFormHidden('action', 'search'));
            }
            break;
        case 'retrieve':
        default:
            xoops_cp_header();
            $adminObject = \Xmf\Module\Admin::getInstance();
            $adminObject->displayNavigation(basename(__FILE__));
            break;
    }
    $sform->display();

    require_once __DIR__ . '/admin_footer.php';
}

function pharseSoapServer()
{
    global $xoopsModuleConfig;
    $svrinfo = [];
    $xms     = explode('|', $xoopsModuleConfig['xsoap_servers']);
    foreach ($xms as $key => $data) {
        $sep                       = explode('@', $data);
        $svrinfo[$key]['uri']      = $sep[1];
        $sepb                      = explode(':', $sep[0]);
        $svrinfo[$key]['username'] = $sepb[0];
        $svrinfo[$key]['password'] = $sepb[1];
    }
    return $svrinfo;
}

function decodespecialchars($rec)
{
    $res = [];
    foreach ($rec as $k => $l) {
        if ('crc' != $k) {
            $res[$k] = convert_uudecode($l);
        } else {
            $res[$k] = $l;
        }
    }
    return $res;
}

function importtorrents($req, $client, $servers, $server, $server_key, $site_name, $site_url)
{
    global $xoopsDB, $xoopsModuleConfig;

    foreach ($req['import'] as $k => $v) {
        if (0 != $v) {
            $request['lid'] = [$req['lid'][$k]];
            $result         = $client->call('xtorrent_send', [
                                                               'username' => $servers[$server]['username'],
                                                               'password' => $servers[$server]['password'],
                                                               'passhash' => sha1((time() - $rnd) . $servers[$server]['username'] . $servers[$server]['password']),
                                                               'rand'     => $rnd,
                                                               'time'     => time(),
                                                               'request'  => $request
                                                           ]);
            //print_r($result);
            $myts   = MyTextSanitizer::getInstance();
            $notify = 0 != $req['notify'] ? 1 : 0;
            $slid   = !empty($result['RESULT']['data'][0]['content']['lid']) ? (int)$result['RESULT']['data'][0]['content']['lid'] : 0;
            $scid   = !empty($result['RESULT']['data'][0]['content']['cid']) ? (int)$result['RESULT']['data'][0]['content']['cid'] : 0;
            $sql    = 'SELECT cid, auto_approval FROM ' . $xoopsDB->prefix('xtorrent_soap_catmatch') . ' WHERE scid = ' . $scid . ' AND skey = ' . $server_key;

            list($cid, $x_autoapprove) = $xoopsDB->fetchRow($xoopsDB->queryF($sql));

            if ($cid > 0) {
                $hmy++;
                $down = xtorrent_writefile(convert_uudecode($result['RESULT']['data'][0]['benc']), $xoopsModuleConfig['uploaddir'], XOOPS_URL, $result['RESULT']['data'][0]['content']['title'] . '.torrent');

                $url   = $down['url'];
                $size  = $down['size'];
                $title = $myts->addslashes(trim($result['RESULT']['data'][0]['content']['title']));

                $homepage      = '';
                $homepagetitle = '';
                if (!empty($result['RESULT']['data'][0]['content']['homepage']) || 'http://' != $result['RESULT']['data'][0]['content']['homepage']) {
                    $homepage      = $myts->addslashes(formatURL(trim($result['RESULT']['data'][0]['content']['homepage'])));
                    $homepagetitle = $myts->addslashes(trim($result['RESULT']['data'][0]['content']['homepagetitle']));
                }
                $version = $myts->addslashes($result['RESULT']['data'][0]['content']['version']);

                foreach ($xoopsModuleConfig['platform'] as $ky => $tp) {
                    if (false != strpos($result['RESULT']['arrays']['platform'][$result['RESULT']['data'][0]['content']['platform']], substr($tp, 1, strlen($tp) - 2))) {
                        $platform = $ky;
                    }
                }

                foreach ($xoopsModuleConfig['license'] as $ky => $tp) {
                    if (false != strpos($result['RESULT']['arrays']['license'][$result['RESULT']['data'][0]['content']['license']], substr($tp, 3, strlen($tp) - 6))) {
                        $license = $ky;
                    }
                }

                foreach ($xoopsModuleConfig['currencies'] as $ky => $tp) {
                    if (false != strpos($result['RESULT']['arrays']['currency'][$result['RESULT']['data'][0]['content']['currency']], $tp)) {
                        $currency = $ky;
                    }
                }
                global $xoopsUser;

                $description = $myts->addslashes($result['RESULT']['data'][0]['content']['description']);
                $submitter   = $myts->addslashes($xoopsUser->getVar('uid'));
                $publisher   = $myts->addslashes(trim($result['RESULT']['data'][0]['content']['publisher']));
                $price       = $myts->addslashes(trim($result['RESULT']['data'][0]['content']['price']));
                $mirror      = $myts->addslashes(formatURL(trim($result['RESULT']['data'][0]['content']['mirror'])));
                $paypalemail = $myts->addslashes(trim($result['RESULT']['data'][0]['content']['paypalemail']));
                //$currency     = $myts->addslashes(trim($result['RESULT']['data'][0]['content']["currency"]));
                $features     = $myts->addslashes(trim($result['RESULT']['data'][0]['content']['features']));
                $requirements = $myts->addslashes(trim($result['RESULT']['data'][0]['content']['requirements']));
                $limitations  = isset($result['RESULT']['data'][0]['content']['limitations']) ? $myts->addslashes($result['RESULT']['data'][0]['content']['limitations']) : '';
                $dhistory     = isset($result['RESULT']['data'][0]['content']['dhistory']) ? $myts->addslashes($result['RESULT']['data'][0]['content']['dhistory']) : '';
                $offline      = (isset($result['RESULT']['data'][0]['content']['offline']) && 1 == $result['RESULT']['data'][0]['content']['offline']) ? 1 : 0;
                $date         = isset($result['RESULT']['data'][0]['content']['date']) ? $myts->addslashes($result['RESULT']['data'][0]['content']['date']) : '';
                $publishdate  = isset($result['RESULT']['data'][0]['content']['publishdate']) ? $myts->addslashes($result['RESULT']['data'][0]['content']['publishdate']) : '';
                $notifypub    = (isset($result['RESULT']['data'][0]['content']['notifypub']) && 1 == $result['RESULT']['data'][0]['content']['notifypub']) ? 1 : 0;
                $scrc         = isset($result['RESULT']['data'][0]['content']['crc']) ? $result['RESULT']['data'][0]['content']['crc'] : '';
                $screenshot   = isset($result['RESULT']['data'][0]['content']['screenshot']) ? $myts->addslashes($result['RESULT']['data'][0]['content']['screenshot']) : '';
                $ipaddress    = isset($result['RESULT']['data'][0]['content']['ipaddress']) ? $myts->addslashes($result['RESULT']['data'][0]['content']['ipaddress']) : '';

                if (0 == $lid) {
                    if (1 == $x_autoapprove) {
                        $publishdate = time();
                        $status      = 1;
                    }
                    $status = (1 == $x_autoapprove) ? 1 : 0;
                    $query  = 'INSERT INTO ' . $xoopsDB->prefix('xtorrent_downloads') . ' 
						(lid, cid, title, url, homepage, version, size, platform, screenshot, submitter, publisher, status, date, hits, rating, votes, comments, price, mirror, license, paypalemail, features, requirements, homepagetitle, forumid, limitations, dhistory, published, expired,offline, description, ipaddress, notifypub, currency)';
                    $query  .= " VALUES 	('', $cid, '$title', '$url', '$homepage', '$version', '$size', '$platform', '$screenshot', '$submitter', '$publisher','$status', '$date', 0, 0, 0, 0, '$price', '$mirror', '$license', '$paypalemail', '$features', '$requirements', '$homepagetitle', '$forumid', '$limitations', '$dhistory', '$publishdate', 0, '$offline', '$description', '$ipaddress', '$notifypub', '$currency')";
                    $result = $xoopsDB->queryF($query);
                    $newid  = $xoopsDB->getInsertId();
                    if ($newid > 0) {
                        $groups = [1, 2];
                        xtorrent_save_Permissions($groups, $newid, 'xtorrentownFilePerm');

                        $sql    = 'INSERT INTO ' . $xoopsDB->prefix('xtorrent_soap_transactions') . " (lid, cid, slid, scid, scrc, retrieved, ssitename, surl) VALUES ('$newid','$cid', '$slid', '$scid', '$scrc', '" . time() . "', '$site_name', '$site_url')";
                        $result = $xoopsDB->queryF($sql);

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
                        if (1 == $x_autoapprove) {
                            $notification_handler->triggerEvent('global', 0, 'new_file', $tags);
                            $notification_handler->triggerEvent('category', $cid, 'new_file', $tags);
                        } else {
                            $tags['WAITINGFILES_URL'] = XOOPS_URL . '/modules/xtorrent/admin/newdownloads.php';
                            $notification_handler->triggerEvent('global', 0, 'file_submit', $tags);
                            $notification_handler->triggerEvent('category', $cid, 'file_submit', $tags);
                            if ($notify) {
                                include_once XOOPS_ROOT_PATH . '/include/notification_constants.php';
                                $notification_handler->subscribe('file', $newid, 'approve', XOOPS_NOTIFICATION_MODE_SENDONCETHENDELETE);
                            }
                        }
                    } else {
                        $hmy--;
                    }
                }
            }
        }
    }
    return $hmy;
}

function xtorrent_writefile($fdata, $write_dir, $xurl, $filename)
{
    $dest = XOOPS_ROOT_PATH . '/' . $write_dir . '/' . $filename;
    if (!empty($fdata)) {
        unlink($dest);
        $fout = fopen($dest, 'w');
        fwrite($fout, $fdata);
        fclose($fout);
        $down         = [];
        $down['url']  = $xurl . '/' . $write_dir . '/' . $filename;
        $down['size'] = strlen($fdata);
    }
    return $down;
}
