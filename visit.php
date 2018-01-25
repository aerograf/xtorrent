<?php

include __DIR__ . '/header.php';

global $xoopsUser, $xoopsModuleConfig, $myts;

$agreed = isset($_GET['agree']) ? $_GET['agree'] : 0;

$lid = (int)$_GET['lid'];
$cid = (int)$_GET['cid'];

function reportBroken($lid)
{
    global $xoopsModule;
    echo '<h4>' . _MD_XTORRENT_BROKENFILE . '</h4>
		      <div>' . _MD_XTORRENT_PLEASEREPORT . "
      		<a href='" . XOOPS_URL . "/modules/xtorrent/brokenfile.php?lid=" . $lid . "'> " . _MD_XTORRENT_CLICKHERE . '</a>
      		</div>';
} 

if (0 == $agreed)
{
    if ($xoopsModuleConfig['check_host'])
    {
        $goodhost     = 0;
        $referer      = parse_url(xoops_getenv('HTTP_REFERER'));
        $referer_host = $referer['host'];
        foreach ($xoopsModuleConfig['referers'] as $ref)
        {
            if (!empty($ref) && preg_match('/' . $ref . '/i', $referer_host))
            {
                $goodhost = '1';
                break;
            } 
        } 
        if (!$goodhost)
        {
            redirect_header(XOOPS_URL . '/modules/xtorrent/singlefile.php?cid=' . $cid . '&amp;lid=' . $lid, 20, _MD_XTORRENT_NOPERMISETOLINK);
            exit();
        } 
    } 
} 

if ($xoopsModuleConfig['showDowndisclaimer'] && $agreed == 0)
{
    include XOOPS_ROOT_PATH . '/header.php';
    echo "<div style='text-align:center;'>" . xtorrent_imageheader() . '</div>
      		<h4>' . _MD_XTORRENT_DISCLAIMERAGREEMENT . '</h4>
      		<div>' . $myts -> displayTarea($xoopsModuleConfig['downdisclaimer'], 0, 1, 1, 1, 1) . "</div><br>
      		<form action='visit.php' method='post'>
      		<div style='text-align:center;'><b>" . _MD_XTORRENT_DOYOUAGREE . "</b><br><br>
      		<input type='button' onclick='location=\"visit.php?agree=1&amp;lid=" . $lid . "&amp;cid=" . $cid . "\"' class='formButton' value='" . _MD_XTORRENT_AGREE . "' alt='" . _MD_XTORRENT_AGREE . "'>
      		&nbsp;
      		<input type='button' onclick='location=\"index.php\"' class='formButton' value='" . _CANCEL . "' alt='" . _CANCEL . "'>
      		<input type='hidden' name='lid' value='1'>
      		<input type='hidden' name='cid' value='1'>
      		</div></form>";
    include XOOPS_ROOT_PATH . '/footer.php';
    exit();
} 
else
{
    $isadmin = (!empty($xoopsUser) && $xoopsUser -> isAdmin($xoopsModule -> mid())) ? true : false;
    if ($isadmin == false)
    {
        $sql = sprintf('UPDATE ' . $xoopsDB -> prefix('xtorrent_downloads') . ' SET hits = hits+1 WHERE lid=' . $lid);
        $xoopsDB -> queryF($sql);
    } 
    $result    = $xoopsDB -> query('SELECT url FROM ' . $xoopsDB -> prefix('xtorrent_downloads') . ' WHERE lid=' . $lid);
    list($url) = $xoopsDB -> fetchRow($result);

 // include XOOPS_ROOT_PATH . '/header.php';
 // echo "<br /><div align='center'>" . xtorrent_imageheader() . "</div>";
 // $url = $myts -> htmlSpecialChars(preg_replace('/javascript:/si' , 'java script:', $url), ENT_QUOTES);

    if (!empty($url))
    {
        if (!headers_sent())
        {
        	if (!empty($url))
            {

				ini_set('allow_url_fopen',true);
				global $xoopsUser, $xoopsDB;
				if (1 == $xoopsModuleConfig['opentracker'])
        {
					if (!empty($xoopsUser))
          {
						$sql = 'SELECT id FROM ' . $xoopsDB->prefix('xtorrent_users') . " WHERE username='" . $xoopsUser->getVar('uname') . "', uid='" . $xoopsUser->getVar('uid') . "'";
						$rt  = $xoopsDB->queryF($sql);				
						if (!$xoopsDB->getRowsNum($rt))
            {
							$sql = 'INSERT into ' . $xoopsDB->prefix('xtorrent_users') . " (username, uid, old_password, secret, lid) VALUES ('" . $xoopsUser->getVar('uname') . "', " . $xoopsUser->getVar('uid') . ", '" . $xoopsUser->getVar('pass') . "', '" . $_SERVER['REMOTE_ADDR'] . ':' . $_SERVER['REMOTE_PORT'] . "','" . $lid . "')";
							$rt  = $xoopsDB->queryF($sql);
						}
            else
            {
							$sql = 'DELETE FROM ' . $xoopsDB->prefix('xtorrent_users') . ' WHERE uid=' . $xoopsUser->getVar('uid') . ' and lid = ' . $lid . " and secret = '" . $_SERVER['REMOTE_ADDR'] . ':' . $_SERVER['REMOTE_PORT'] . "'";
							$rt  = $xoopsDB->queryF($sql);
							$sql = 'INSERT into ' . $xoopsDB->prefix('xtorrent_users') . " (username, uid, old_password, secret, lid) VALUES ('" . $xoopsUser->getVar('uname') . "', " . $xoopsUser->getVar('uid') . ", '" . $xoopsUser->getVar('pass') . "', '" . $_SERVER['REMOTE_ADDR'] . ':' . $_SERVER['REMOTE_PORT'] . "','" . $lid . "')";
							$rt  = $xoopsDB->queryF($sql);
						}
					}
          else
          {
						$sql = 'SELECT id FROM ' . $xoopsDB->prefix('xtorrent_users') . " WHERE username='guest', uid=0, old_password = md5('guest'), secret = '" . $_SERVER['REMOTE_ADDR'] . ':' . $_SERVER['REMOTE_PORT'] . "'";
						$rt  = $xoopsDB->queryF($sql);
						if (!$xoopsDB->getRowsNum($rt))
            {
							$sql = 'INSERT into ' . $xoopsDB->prefix('xtorrent_users') . " (username, uid, old_password, secret, lid) VALUES ('guest', 0, md5('guest'), '" . $_SERVER['REMOTE_ADDR'] . ':' . $_SERVER['REMOTE_PORT'] . "','" . $lid . "')";
							$rt  = $xoopsDB->queryF($sql);
						}
            else
            {
							$sql = "DELETE FROM " . $xoopsDB->prefix('xtorrent_users') . " where username='guest' and uid=0 and old_password = md5('guest') and lid = " . $lid . " and secret = '" . $_SERVER['REMOTE_ADDR'] . ':' . $_SERVER['REMOTE_PORT'] . "'";
							$rt  = $xoopsDB->queryF($sql);
							$sql = "INSERT into " . $xoopsDB->prefix('xtorrent_users') . " (username, uid, old_password, secret, lid) VALUES ('guest', 0, md5('guest'), '" . $_SERVER['REMOTE_ADDR'] . ":" . $_SERVER['REMOTE_PORT'] . "','" . $lid . "')";
							$rt  = $xoopsDB->queryF($sql);
						}
					}

					require_once __DIR__ . '/include/bittorrent.php';

					if($rt)
          {
						$kid     = $xoopsDB->getInsertId();
						$sql     = 'UPDATE ' . $xoopsDB->prefix('xtorrent_users') . ' set passhash = md5(concat(secret, old_password, secret)) where id = ' . $kid ;
						$rt      = $xoopsDB->queryF($sql);
						$sql     = 'SELECT * FROM ' . $xoopsDB->prefix('xtorrent_users') . ' WHERE id = ' . $kid ;
						$rt      = $xoopsDB->queryF($sql);
						$row     = $xoopsDB->fetchArray($rt); 
						$passkey = md5($row['username'] . get_date_time() . $row['passhash']);
						$sql     = 'UPDATE ' . $xoopsDB->prefix('xtorrent_users') . " set passkey = '" . $passkey . "' WHERE id = " . $kid ;
						$rt      = $xoopsDB->queryF($sql);
					}
				}			
				// Begin Download

				$fn = str_replace(XOOPS_URL, XOOPS_ROOT_PATH, $url);

				require_once __DIR__ . '/include/benc.php';
				$dict = bdec_file($fn, 1024*1024);

				if (empty($dict['value']['announce']))
        {
					$dict['value']['announce']['type'] = 'string';
					if (1 == $xoopsModuleConfig['opentracker'])
          {
						$dict['value']['announce']['value'] = $xoopsModuleConfig['announce_url'] . '?passkey=' . $passkey;
					}
          else
          {
						$dict['value']['announce']['value'] = $xoopsModuleConfig['announce_url'];
					}
					$dict['value']['announce']['string'] = strlen($dict['value']['announce']['value']) . ':' . $dict['value']['announce']['value'];
					$dict['value']['announce']['strlen'] = strlen($dict['value']['announce']['string']);
				}
        else
        {
					$tracker         = [];
					$buffer          = [];					
					$tracker['type'] = 'list';
					$buffer['type']  = 'string';
					if (1 == $xoopsModuleConfig['opentracker'])
          {
						$buffer['value'] = $xoopsModuleConfig['announce_url'] . '?passkey=' . $passkey;
					} else {
						$buffer['value'] = $xoopsModuleConfig['announce_url'];
					}
					if (!empty($dict['value']['announce-list']))
          {
						$buffer['string']  = strlen($buffer['value']) . ':' . $buffer['value'];
						$buffer['strlen']  = strlen($buffer['string']);
						$tracker['value']  = [$buffer];
						$tracker['string'] = 'l' . $buffer['string'] . 'e';
						$tracker['strlen'] = strlen($tracker['string']);
						$dict['value']['announce-list']['value'][count($dict['value']['announce-list']['value'])] = $tracker;
						$dict['value']['announce-list']['string'] = substr($dict['value']['announce-list']['string'], 0, strlen($dict['value']['announce-list']['string'])-2) . 'l' . $buffer['string'] . 'ee';
						$dict['value']['announce-list']['strlen'] = strlen($dict['value']['announce-list']['string']);
					}
          else
          {
						$dict['value']['announce-list']['type'] = 'list';
						$buffer2           = [];

						$buffer2['type']   = 'string';
						$buffer2['string'] = strlen($dict['value']['announce']['value']) . ':' . $dict['value']['announce']['value'];
						$buffer2['value']  = $dict['value']['announce']['value'];
						$buffer2['strlen'] = strlen($buffer2['string']);
						$tracker['value']  = [$buffer2];
						$tracker['string'] = 'l' . $buffer2['string'] . 'e';
						$tracker['strlen'] = strlen($tracker['string']);
						$dict['value']['announce-list']['value'][count($dict['value']['announce-list']['value'])] = $tracker;

						$buffer['string']  = strlen($buffer['value']) . ':' . $buffer['value'];
						$buffer['strlen']  = strlen($buffer['string']);
						$tracker['value']  = [$buffer];
						$tracker['string'] = 'l' . $buffer['string'] . 'e';
						$tracker['strlen'] = strlen($tracker['string']);

						$dict['value']['announce-list']['value'][count($dict['value']['announce-list']['value'])] = $tracker;
						$dict['value']['announce-list']['string'] = 'll' . $buffer2['string'] . '' . $buffer['string'] . 'ee';
						$dict['value']['announce-list']['strlen'] = strlen($dict['value']['announce-list']['string']);
					}									
					header('Content-Disposition: attachment; filename=' . basename($url));
					header('Content-Type: application/x-bittorrent');
					//print_r($dict);
					print(benc($dict));
					exit();
				}				
			} 
			else
			{
				include XOOPS_ROOT_PATH . '/header.php';
				echo "<br><div align='center'>" . xtorrent_imageheader() . '</div>';
				reportBroken($lid);
			} 
     }
     else
     {
			die('Headers already sent'); 
		}
    } 
    else
    {
      reportBroken($lid);
	    include XOOPS_ROOT_PATH . '/footer.php';
    } 

} 
