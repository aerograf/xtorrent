<?php 

ob_start('ob_gzhandler');
require_once('../../mainfile.php');
require_once('include/functions.php');
require_once('include/bittorrent.php');
require_once('include/benc.php');

global $xoopsDB, $xoopsConfig, $xoopsModuleConfig;
$filename = XOOPS_ROOT_PATH.'/uploads/test.txt';

// Let's make sure the file exists and is writable first.
function debugtxt($str, $filename)
{
    if (is_writable($filename)) {
    
        // In our example we're opening $filename in append mode.
        // The file pointer is at the bottom of the file hence
        // that's where $somecontent will go when we fwrite() it.
        if (!$handle = fopen($filename, 'a')) {
        }
        if (fwrite($handle, $str.chr(13).chr(10)) === false) {
        }
        fclose($handle);
    }
}
//hit_start();

//$rt=debugtxt("Start", $filename);

function err($msg)
{
    benc_resp(['failure reason' => [type => 'string', value => $msg]]);
    hit_end();
    exit();
}

function benc_resp($d)
{
    benc_resp_raw(benc([type => 'dictionary', value => $d]));
}

function benc_resp_raw($x)
{
    header('Content-Type: text/plain');
    header('Pragma: no-cache');
    print($x);
}
/*
function hex2bin($h)
  {
  if (!is_string($h)) return null;
  $r = '';
  for ($a=0; $a<strlen($h); $a+=2) { $r.=chr(hexdec($h{$a} . $h{($a+1)})); }
  return $r;
  }
*/
if (!function_exists('bin2hex')) {
    function bin2hex($str)
    {
        $strl = strlen($str);
        $fin = '';
        for ($i =0; $i < $strl; $i++) {
            $fin .= dechex(ord($str[$i]));
        }
        return $fin;
    }
}

foreach ($_GET as $x => $k) {
    if (isset($_GET["$x"])) {
        ${$x} = '' . $k;
    }
    //$rt=debugtxt("$x = '".$k, $filename);
//echo ."'<br>";
}
$info_hash = md5($info_hash);
foreach (['port', 'downloaded', 'uploaded', 'left'] as $x) {
    ${$x} = 0 + $_GET[$x];
}

if (strpos($passkey, '?')) {
    $tmp               = substr($passkey, strpos($passkey, '?'));
    $passkey           = substr($passkey, 0, strpos($passkey, '?'));
    $tmpname           = substr($tmp, 1, strpos($tmp, '=') - 1);
    $tmpvalue          = substr($tmp, strpos($tmp, '=') + 1);
    $GLOBALS[$tmpname] = $tmpvalue;
}

if (strlen($peer_id) == 20) {
    $peer_id = bin2hex($peer_id);
}

/* if (strlen($info_hash) == 20) {
    $info_hash = bin2hex($info_hash);
} */


foreach (['passkey', 'info_hash', 'peer_id', 'port', 'downloaded', 'uploaded', 'left'] as $x) {
    if (!isset($x)) {
        err("Missing key: $x");
    }
}

//if (empty($ip) || !preg_match('/^(d{1,3}.){3}d{1,3}$/s', $ip))

$ip = getip();

$rsize = 50;
foreach (['num want', 'numwant', 'num_want'] as $k) {
    if (isset($_GET[$k])) {
        $rsize = 0 + $_GET[$k];
        break;
    }
}

$agent = $_SERVER['HTTP_USER_AGENT'];

// Deny access made with a browser...
if (preg_match(explode('|', $xoopsModuleConfig['agents_disallowed']), $agent)) {
    err('torrent not registered with this tracker');
}

/*if (!$port || $port > 0xffff)
    err("invalid port");*/

if (!isset($event)) {
    $event = '';
}

$seeder = ($left == 0) ? 'yes' : 'no';



if (isset($_SERVER['PATH_INFO'])) {
    if (substr($_SERVER['PATH_INFO'], -7) == '/scrape') {
        /*
         * Was an individual hash requested?
         */
        if (isset($_GET['info_hash'])) {
            if (get_magic_quotes_gpc()) {
                $info_hash = stripslashes($_GET['info_hash']);
            } else {
                $info_hash = $_GET['info_hash'];
            }

            if (strlen($info_hash) == 20) {
            } elseif (strlen($info_hash) == 40) {
            } else {
                err('Invalid info hash value.');
            }

            $usehash = true;
        }

        /*
         * Get requested info
         */
        if ($usehash) {
            $query = $xoopsDB->query('SELECT a.info_hash, a.seeds, a.finished, a.leechers, b.filename FROM ' . $xoopsDB->prefix('xtorrent_torrent') . ' a LEFT JOIN ' . $xoopsDB->prefix('xtorrent_downloads') . " b ON a.lid = b.lid WHERE a.info_hash=\"$info_hash\"") or err('Database error. Cannot complete request.');
        } else {
            $query = $xoopsDB->query('SELECT a.info_hash, a.seeds, a.finished, a.leechers, b.filename FROM ' . $xoopsDB->prefix('xtorrent_torrent') . ' a LEFT JOIN ' . $xoopsDB->prefix('xtorrent_downloads') . ' b ON a.lid = b.lid WHERE 1=1') or err('Database error. Cannot complete request.');
        }

        echo 'd5:filesd';
        while ($row = mysqli_fetch_row($query)) {
            if ($row[1] < 0 || $row[3] < 0) {
                $row[1] = 0;
                $row[3] = 0;
            }

            $hash = hex2bin($row[0]);
            echo '20:' . $hash . 'd';
            echo '8:completei' . $row[1] . 'e';
            echo '10:downloadedi' . $row[2] . 'e';
            echo '10:incompletei' . $row[3] . 'e';
            echo 'e';
        }
        echo 'e5:flagsd20:min_request_intervali' . ($xoopsModuleConfig['announce_interval'] * 60) . 'eee';
        exit;
    }
}


$res = $xoopsDB->queryF('DELETE FROM ' . $xoopsDB->prefix('xtorrent_users') . " WHERE `last_action` < '" . date('Y-m-d H:i:s', time() - 57254) . "'");
$res = $xoopsDB->queryF('DELETE FROM ' . $xoopsDB->prefix('xtorrent_peers') . " WHERE `last_access` < '" . date('Y-m-d H:i:s', time() - 57254) . "'");
$res = $xoopsDB->queryF('DELETE FROM ' . $xoopsDB->prefix('xtorrent_users') . " WHERE `last_action` = '' OR `last_action` = NULL");
$res = $xoopsDB->queryF('DELETE FROM ' . $xoopsDB->prefix('xtorrent_peers') . " WHERE `last_access` = '' OR `last_access` = NULL");

if (strpos($_SERVER['REQUEST_URI'], '?=') == 0) {
    $sql = 'SELECT lid, id FROM ' . $xoopsDB->prefix('xtorrent_users') . ' WHERE passkey=' . sqlesc($passkey) . ' and secret = ' . sqlesc(sha1(xtorrent_get_base_domain(gethostbyaddr($_SERVER['REMOTE_ADDR']))));
} else {
    $sql = 'SELECT a.lid, a.id FROM ' . $xoopsDB->prefix('xtorrent_users') . ' a INNER JOIN ' . $xoopsDB->prefix('xtorrent_torrent') . ' b on a.lid = b.lid WHERE b.hashInfo=' . sqlesc($info_hash) . ' and a.secret = ' . sqlesc(sha1(xtorrent_get_base_domain(gethostbyaddr($_SERVER['REMOTE_ADDR']))));
}

$valid = @mysqli_fetch_row(@$xoopsDB->queryF($sql));
if ($valid[0] == 0||empty($valid)) {
    err('Invalid passkey or secret! Re-download the .torrent from ' . XOOPS_URL);
}
$res = $xoopsDB->queryF('UPDATE ' . $xoopsDB->prefix('xtorrent_users') . " set last_access = '" . date('Y-m-d H:i:s') . "' WHERE id = " . $valid[1]);

$res = $xoopsDB->queryF('DELETE ' . $xoopsDB->prefix('xtorrent_users') . " WHERE last_access < '" . date('Y-m-d H:i:s', time() - (($xoopsModuleConfig['access_clear'] + 1) * (((60 * 60) * 12) * 7))));

$res = $xoopsDB->queryF('SELECT lid, seeds + leechers AS numpeers, added AS ts FROM ' . $xoopsDB->prefix('xtorrent_torrent') . ' WHERE lid = ' . $valid[0]);

$torrent = $xoopsDB->fetchArray($res);
if (!$torrent) {
    err('torrent not registered with this tracker');
}

$torrentid = $torrent['lid'];

$fields = 'seeder, peer_id, ip, port, uploaded, downloaded, userid';

$numpeers = $torrent['numpeers'];
$limit    = '';
if ($numpeers > $rsize) {
    $limit = "ORDER BY RAND() LIMIT $rsize";
}
$res = $xoopsDB->queryF("SELECT DISTINCT $fields FROM ".$xoopsDB->prefix('xtorrent_peers')." WHERE torrent = $torrentid $limit");
unset($self);


    $resp .= 'd' . '8:intervali' . ($xoopsModuleConfig['announce_interval'] * 60) . 'e'
             . '12:min intervali' . ($xoopsModuleConfig['announce_interval'] * 30) . 'e'
             . '5:peers';

    $seeds = 'd' . '8:intervali' . ($xoopsModuleConfig['announce_interval'] * 60) . 'e'
             . '12:min intervali' . ($xoopsModuleConfig['announce_interval'] * 30) . 'e'
             . '5:seeds';
        
    if (isset($_GET['compact']) && $_GET['compact'] == '1') {
        $p = '';
        $s = '';
        while ($row = $xoopsDB->fetchArray($res)) {
            if ($row['peer_id'] === $peer_id) {
                $userid = $row['userid'];
                $self = $row;
                continue;
            }
            
            $p .= pack('Nn', hex2bin($row['ip']), $row['port']);
            if ($row['seeder']='yes') {
                $s .= pack('Nn', hex2bin($row['ip']), $row['port']);
            }
        }
        $resp .= strlen($p).':'.$p;
        if (strlen($s)) {
            $seeds .= strlen($s).':'.$s;
        }
    } else {
        // no_peer_id or no feature supported
        $resp .='l';
        $seeds .='l';
        while ($row = $xoopsDB->fetchArray($res)) {
            if ($row['peer_id'] === $peer_id) {
                $userid = $row['userid'];
                $self = $row;
                continue;
            }
            
            $ips = ($row['ip']);
            
            if ($row['seeder']='yes') {
                $seeds .= 'd2:ip' . strlen($ips) . ':' . $ips;
                if (isset($row['peer_id'])) {
                    $seeds .= '7:peer id' . strlen(hex2bin($row['peer_id'])) . ':' . hex2bin($row['peer_id']);
                }
                $seeds .= '4:porti' . $row['port'] . 'ee';
            }
            

            $resp .= 'd2:ip' . strlen($ips) . ':' . $ips;
            if (isset($row['peer_id'])) {
                $resp .= '7:peer id' . strlen(hex2bin($row['peer_id'])) . ':' . hex2bin($row['peer_id']);
            }
            $resp .= '4:porti' . $row['port'] . 'ee';
        }
        $seeds .= 'e';
        $resp .= 'e';
    }
    
    if (isset($xoopsModuleConfig['response_key'])) {
        $resp .= '10:tracker id' . strlen($xoopsModuleConfig['response_key']) . ':' . $xoopsModuleConfig['response_key'];
    }
    
    $resp .= 'e';
    $resp .= $seeds . 'e';
        
$selfwhere = "torrent = $torrentid AND " . hash_where('peer_id', $peer_id);

if (!isset($self)) {
    //$rt=debugtxt("start self", $filename);
    $sql = "SELECT $fields FROM ".$xoopsDB->prefix('xtorrent_peers')." WHERE $selfwhere";
    //$rt=debugtxt($sql, $filename);
    $res = $xoopsDB->queryF($sql);// or err('Self Location Failed');
    $row = $xoopsDB->fetchArray($res);
    if ($row) {
        $userid = $row['userid'];
        $self = $row;
    }
}


//// Up/down stats ////////////////////////////////////////////////////////////
if (!isset($self)) {
    //$rt=debugtxt("start self 2", $filename);

    $valid = @$xoopsDB->fetchRow(@$xoopsDB->queryF('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('xtorrent_peers') . " WHERE torrent=$torrentid AND passkey=" . sqlesc($passkey))) or err('mistake');
    //if ($xoopsModuleConfig['numleechers'] != 0 && $valid[0] >= $xoopsModuleConfig['numleechers'] && $seeder == 'no') err("Connection limit exceeded! You may only leech from one location at a time.");
    //if ($xoopsModuleConfig['numseeds'] != 0 && $valid[0] >= $xoopsModuleConfig['numseeds'] && $seeder == 'yes') err("Connection limit exceeded!");

    $rz = $xoopsDB->queryF('SELECT id, uploaded, downloaded FROM '
                           . $xoopsDB->prefix('xtorrent_users') . ' WHERE passkey='
                           . sqlesc($passkey) . " AND enabled = 'yes' and secret = " . sqlesc(sha1(xtorrent_get_base_domain(gethostbyaddr($_SERVER['REMOTE_ADDR'])))) . ' ORDER BY last_access DESC LIMIT 1') or err('Tracker error 2');

    $az = $xoopsDB->fetchArray($rz);
    $userid = ($az['id']);

    if ($xoopsDB->getRowsNum($rz) == 0) {
        err('Unknown passkey or secret. Please redownload the torrent from ' . XOOPS_URL);
    }
} else {
    $rz = $xoopsDB->queryF('SELECT id, uploaded, downloaded FROM ' . $xoopsDB->prefix('xtorrent_users') . ' WHERE passkey=' . sqlesc($passkey) . " AND enabled = 'yes' ORDER BY last_access DESC LIMIT 1") or err('Tracker error 2');

    $az = $xoopsDB->fetchArray($rz);
    $userid = ($az['id']);

    $upthis   = max(0, $uploaded - $self['uploaded']);
    $downthis = max(0, $downloaded - $self['downloaded']);

    if (($upthis > 0 || $downthis > 0) && $userid<>0) {
        $rt=$xoopsDB->queryF('UPDATE ' . $xoopsDB->prefix('xtorrent_users') . " SET uploaded = uploaded + $upthis, downloaded = downloaded + $downthis WHERE id=$userid") or err('Tracker error 3');
    }
}

///////////////////////////////////////////////////////////////////////////////

function portblacklisted($port)
{
    // direct connect
    if ($port >= 411 && $port <= 413) {
        return true;
    }

    // bittorrent
    if ($port >= 6881 && $port <= 6889) {
        return true;
    }

    // kazaa
    if ($port == 1214) {
        return true;
    }

    // MITtella
    if ($port >= 6346 && $port <= 6347) {
        return true;
    }

    // emule
    if ($port == 4662) {
        return true;
    }

    // winmx
    if ($port == 6699) {
        return true;
    }

    return false;
}

$updateset = [];

if ($event == 'stopped') {
    //$rt=debugtxt("start stopped 1", $filename);

    if (isset($self)) {
        $xoopsDB->queryF('DELETE FROM ' . $xoopsDB->prefix('xtorrent_peers') . " WHERE $selfwhere");
        if ($xoopsDB->getAffectedRows()) {
            if ($self['seeder'] == 'yes') {
                $updateset[] = 'seeds = seeds - 1';
            } else {
                $updateset[] = 'leechers = leechers - 1';
            }
        }
    }
} else {
    if ($event == 'completed') {
        $updateset[] = 'finished = finished + 1';
    }

    if (isset($self)) {
        $xoopsDB->queryF('UPDATE ' . $xoopsDB->prefix('xtorrent_peers') . " SET uploaded = $uploaded, ip='$ip', downloaded = $downloaded, to_go = $left, last_action = NOW(), seeder = '$seeder'"
                         . ($seeder == 'yes' && $self['seeder'] != $seeder ? ', finishedat = ' . time() : '') . " WHERE $selfwhere");

            
        if ($xoopsDB->getAffectedRows() && $self['seeder'] != $seeder) {
            if ($seeder == 'yes') {
                $updateset[] = 'seeds = seeds + 1';
                $updateset[] = 'leechers = leechers - 1';
            } else {
                $updateset[] = 'seeds = seeds - 1';
                $updateset[] = 'leechers = leechers + 1';
            }
        }
    } else {
        if (portblacklisted($port)) {
            err("Port $port is blacklisted.");
        } else {
            /*$sockres = @fsockopen($ip, $port, $errno, $errstr, 5);
            if (!$sockres)
                $connectable = "no";
            else
            {*/
            $connectable = 'yes';
            //	@fclose($sockres);
            //}
        }

        $sql = 'SELECT id FROM ' . $xoopsDB->prefix('xtorrent_peers') . " WHERE torrent = '$torrentid', peer_id = " . sqlesc($peer_id) . ',  agent = ' . sqlesc(addslashes($agent)) . '';
        $rt = $xoopsDB->queryF($sql);
        if ($xoopsDB->getRowsNum($rt)) {
            list($pid) = $xoopsDB->fetchRow($rt) or err('Could not fetch peer record');
            $sql = 'UPDATE '
                   . $xoopsDB->prefix('xtorrent_peers') . " SET connectable = '$connectable', torrent = '$torrentid', peer_id = " . sqlesc($peer_id) . ', ip = '
                   . sqlesc($ip) . ", port = '$port', uploaded = '$uploaded', downloaded = '$downloaded', to_go = '$left', started = NOW(), last_action = NOW(), seeder = '$seeder', userid = '$userid', agent = " . sqlesc(addslashes($agent)) . ", uploadoffset = '$uploaded', downloadoffset = '$downloaded', passkey = " . sqlesc($passkey) . ") WHERE id = $pid";
        } else {
            $sql = 'INSERT INTO '
                   . $xoopsDB->prefix('xtorrent_peers') . " (connectable, torrent, peer_id, ip, port, uploaded, downloaded, to_go, started, last_action, seeder, userid, agent, uploadoffset, downloadoffset, passkey) VALUES ('$connectable', '$torrentid', " . sqlesc(addslashes($peer_id)) . ', '
                   . sqlesc($ip) . ", '$port', '$uploaded', '$downloaded', '$left', NOW(), NOW(), '$seeder', '$userid', " . sqlesc(addslashes($agent)) . ", '$uploaded', '$downloaded', " . sqlesc($passkey) . ')';
        }
        $ret = $xoopsDB->queryF($sql) or err('Tracker error - Update Peer Failed');
         
        if ($ret) {
            if ($seeder == 'yes') {
                $updateset[] = 'seeds = seeds + 1';
            } else {
                $updateset[] = 'leechers = leechers + 1';
            }
        }
    }
}

if ($seeder == 'yes') {
    /*if ($torrent["banned"] != "yes")
        $updateset[] = "visible = 'yes'";*/
    $updateset[] = 'last_action = NOW()';
}

if (count($updateset)) {
    $xoopsDB->queryF('UPDATE ' . $xoopsDB->prefix('xtorrent_torrent') . ' SET ' . join(',', $updateset) . " WHERE lid = $torrentid");
}

    //$rt=debugtxt("start resp:".$resp."", $filename);

benc_resp_raw($resp);

//hit_end();
