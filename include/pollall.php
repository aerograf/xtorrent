<?

error_reporting(E_ALL);

include __DIR__ . '/torrent.php';
include __DIR__ . '/tracker.php';

function returnfile($id)
{
	global $xoopsDB;
	$sql = 'SELECT url FROM ' . $xoopsDB->prefix('xtorrent_downloads') . ' WHERE lid = ' . $id;
	$rti = $xoopsDB->query($sql);
	$rt  = $xoopsDB->fetchArray($rti);
	return str_replace(XOOPS_URL,XOOPS_ROOT_PATH,$rt['url']);
}

function poll_torrent($id)
{
	global $xoopsDB;
	
	$torrent = new Torrent(returnfile($id));
	if(!$torrent->error) {
		$sql[0]  = 'DELETE from ' . $xoopsDB->prefix('xtorrent_torrent') . ' WHERE lid = ' . $id;
		$sql[1]  = 'INSERT INTO ' . $xoopsDB->prefix('xtorrent_torrent') . ' (lid, seeds, leechers, totalsize, modifiedby, tname, infoHash, announce, md5sum, added) ';
		$sql[1] .= "VALUES ('" . $id
                           . "', '"
                           . $summary['seeds']
                           . "', '"
                           . $summary['leeches']
                           . "', '"
                           . ($torrent->totalSize / 1073741824)
                           . "', '"
                           . addslashes($torrent->modifiedBy)
                           . "', '"
                           . addslashes($torrent->name)
                           . "', '"
                           . addslashes($torrent->infoHash)
                           . "', '"
                           . addslashes($torrent->announce)
                           . "', '"
                           . addslashes($torrent->md5sum)
                           . "', '"
                           . time()
                           . "')";
		$sql[2] = 'DELETE from ' . $xoopsDB->prefix('xtorrent_files') . ' WHERE lid = ' . $id;
		
		$rt = $xoopsDB->queryF($sql[0]);
		$rt = $xoopsDB->queryF($sql[1]);
		$rt = $xoopsDB->queryF($sql[2]);
	
		foreach($torrent->files as $file)
    { 
			$rt = $xoopsDB->queryF('INSERT INTO ' . $xoopsDB->prefix('xtorrent_files') . " (lid,`file`) VALUES ('" . $id . "', '" . addslashes($file->name) . "')");
		}
	}

  $sql[3] = 'SELECT * FROM ' . $xoopsDB->prefix('xtorrent_poll') . ' WHERE lid = ' . $id;
  $rt     = $xoopsDB->queryF($sql[3]);
  if ($xoopsDB->getRowsNum($rt))
  {
     $sql[4] = 'UPDATE ' . $xoopsDB->prefix('xtorrent_poll') . ' SET torrent = ' . time() . ' WHERE lid = ' . $id;
  }
  else
  {
     $sql[4] = 'INSERT INTO ' . $xoopsDB->prefix('xtorrent_poll') . ' (torrent, lid) VALUES (' . time() . ', ' . $id . ')';
  }

  $rt = $xoopsDB->queryF($sql[4]);

	return $torrent;
}

function poll_tracker($torrent, $id, $timeout){

	global $xoopsDB;
	
	if (!isset($torrent))
  {
		$torrent = new Torrent(returnfile($id));
  }
			
	if(!$torrent->error)
  {
		$scrape_result = @tracker_scrape_all($torrent, $timeout);
		$summary       = tracker_scrape_summarise($scrape_result);
	
		$sql[0] = 'DELETE from ' . $xoopsDB->prefix('xtorrent_tracker') . ' WHERE lid = ' . $id;	
		$rt     = $xoopsDB->queryF($sql[0]);
		
		foreach($scrape_result as $tracker => $result)
    {
			if (isset($result))
      {
				$rt = $xoopsDB->queryF('INSERT INTO '
                                . $xoopsDB->prefix('xtorrent_tracker')
                                . " (lid, seeds, leechers, tracker) VALUES ('"
                                . $id
                                . "', '"
                                . $result['seeds']
                                . "', '"
                                . $result['leeches']
                                . "', '"
                                . $tracker
                                . "')");	
			}
      else
      {
				$rt = $xoopsDB->queryF('INSERT INTO '
                                . $xoopsDB->prefix('xtorrent_tracker')
                                . " (lid, seeds, leechers, tracker) VALUES ('"
                                . $id
                                . "','0','0','"
                                . $tracker
                                . "')");			
			}
		}
	}
  $sql[3] = 'SELECT * FROM ' . $xoopsDB->prefix('xtorrent_poll') . ' WHERE lid = ' . $id;
  $rt     = $xoopsDB->queryF($sql[3]);
  if ($xoopsDB->getRowsNum($rt))
  {
        $sql[4] = 'UPDATE ' . $xoopsDB->prefix('xtorrent_poll') . ' SET tracker = ' . time() . ' WHERE lid = ' . $id;
    }
    else
    {
        $sql[4] = 'INSERT INTO ' . $xoopsDB->prefix('xtorrent_poll') . ' (tracker, lid) VALUES (' . time() . ', ' . $id . ')';
    }

    $rt = $xoopsDB->queryF($sql[4]);
}
