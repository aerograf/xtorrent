<?php

class bencResource extends XoopsObject {
	function __construct(){
		$this->XoopsObject();
		$this->initVar("benc");
		$this->initVar("object");
		$this->initVar("filename");
	}
}

class XtorrentBencHandler extends XoopsObjectHandler {
	var $db;
	var $db_table;
	var $perm_name = 'xtorrent_benc_';
	var $obj_class = 'bencResource';
	var $memref    = 0;
	
	function __construct($db){
		if (!isset($db)&&!empty($db))
		{
			$this->db = $db;
		} else {
			global $xoopsDB;
			$this->db = $xoopsDB;
		}
		$this->db_table = $this->db->prefix('xtorrent_benc');
		$this->perm_handler = xoops_gethandler('groupperm');
	}
	
	function getInstance($db){
		static $instance;
		if( !isset($instance) ){
			$instance = new xtorrentbencHandler($db);
		}
		return $instance;
	}
	
	private function decompile($file, $ms) {
		$benc = new $this->obj_class();
		$benc->setVar('filename', $file);
		$benc->setVar('object', xtorrent_bdec_file($file, $ms));
		
		$fp = fopen($file, "rb");
		if (!$fp)
			return;
		$e = fread($fp, $ms);
		fclose($fp);
		
		$benc->setVar('benc', $e);
		return $benc;
	}
	
	function compile($benc) 
	{
		$obj = $benc->getVar('object');
		
		if (!is_array($obj) || !isset($obj["type"]) || !isset($obj["value"]))
		{
			trigger_error("Could not identify benc object type", E_USER_WARNING);
			return false;		
		}

		$c = $obj["value"];
		switch ($obj["type"]) 
		{
			case "string":
				$benc = $this->xtorrent_benc_str($c);
			case "integer":
				$benc = $this->xtorrent_benc_int($c);
			case "list":
				$benc = $this->xtorrent_benc_list($c);
			case "dictionary":
				$benc = $this->xtorrent_benc_dict($c);
			default:
				trigger_error("Could not identify benc object type set as ".$obj["type"], E_USER_WARNING);
				return false;
		}
		
		$benc->setVar('benc', $benc);
		return $benc;
	}
	
	private function xtorrent_benc_str($s) {
		return strlen($s) . ":$s";
	}
	
	private function xtorrent_benc_int($i) {
		return "i" . $i . "e";
	}
	
	private function xtorrent_benc_list($a) {
		$s = "l";
		foreach ($a as $e) {
			$s .= $this->xtorrent_benc($e);
		}
		$s .= "e";
		return $s;
	}
	
	private function xtorrent_benc_dict($d) {
		$s = "d";
		$keys = array_keys($d);
		sort($keys);
		foreach ($keys as $k) {
			$v = $d[$k];
			$s .= $this->xtorrent_benc_str($k);
			$s .= $this->xtorrent_benc($v);
		}
		$s .= "e";
		return $s;
	}
	
	private function xtorrent_bdec_file($f, $ms) {
		$fp = fopen($f, "rb");
		if (!$fp)
			return;
		$e = fread($fp, $ms);
		fclose($fp);
		return $this->xtorrent_bdec($e);
	}
	
	private function xtorrent_bdec($s) {
		if (preg_match('/^(\d+):/', $s, $m)) 
		{
			$l  = $m[1];
			$pl = strlen($l) + 1;
			$v  = substr($s, $pl, $l);
			$ss = substr($s, 0, $pl + $l);
			if (strlen($v) != $l)
				return;
			return ['type' => "string", 'value' => $v, 'strlen' => strlen($ss), 'string' => $ss];
		}
		if (preg_match('/^i(\d+)e/', $s, $m)) 
		{
			$v  = $m[1];
			$ss = "i" . $v . "e";
	
			if ($v === "-0")
				return;
	
			if ($v[0] == "0" && strlen($v) != 1)
				return;
		
			return ['type' => "integer", 'value' => $v, 'strlen' => strlen($ss), 'string' => $ss];
		}
		switch ($s[0]) 
		{
			case "l":
				return $this->xtorrent_bdec_list($s);
			case "d":
				return $this->xtorrent_bdec_dict($s);
			default:
				return;
		}
	}
	
	private function xtorrent_bdec_list($s) 
	{
		if ($s[0] != "l")
			return;
		$sl = strlen($s);
		$i  = 1;
		$v  = [];
		$ss = "l";
		for (;;) {
			if ($i >= $sl)
				return;
			if ($s[$i] == "e")
				break;
			$ret = $this->xtorrent_bdec(substr($s, $i));
			if (!isset($ret) || !is_array($ret))
				return;
			$v[] = $ret;
			$i  += $ret["strlen"];
			$ss .= $ret["string"];
		}
		$ss .= "e";
		return ['type' => "list", 'value' => $v, 'strlen' => strlen($ss), 'string' => $ss];
	}
	//
	private function xtorrent_bdec_dict($s) {
		if ($s[0] != "d")
			return;
		
		$sl = strlen($s);
		$i  = 1;
		$v  = [];
		$ss = "d";
		for (;;) {
			if ($i >= $sl)
				return;
			if ($s[$i] == "e")
				break;
			$ret = $this->xtorrent_bdec(substr($s, $i));
			if (!isset($ret) || !is_array($ret) || $ret["type"] != "string")
				return;
			$k   = $ret["value"];
			$i  += $ret["strlen"];
			$ss .= $ret["string"];
			if ($i >= $sl)
				return;
			$ret = $this->xtorrent_bdec(substr($s, $i));
			if (!isset($ret) || !is_array($ret))
				return;
			$v[$k] = $ret;
			$i    += $ret["strlen"];
			$ss   .= $ret["string"];
		}
		$ss .= "e";
		return ['type' => "dictionary", 'value' => $v, 'strlen' => strlen($ss), 'string' => $ss];
	}
	
	function decompile($benc, $reimport = false) {

		if (!strlen($benc->getVar('benc')||$reimport!=FALSE))
		{
			$filename = $benc->getVar('filename');
			
			ini_set('allow_url_fopen',true);
			
			if(is_null($filename))
				return;
			
			$h = @fopen($filename, "rb");
			if($h === false) {
				trigger_error("Could not create class benc for {$filename}: failed to open for reading", E_USER_WARNING);
				return;
			}
			
			$filesize = @filesize($filename);
			if($filesize === false) {
				trigger_error("Could not create class benc for {$filename}: the file is empty", E_USER_WARNING);
				return;
			}
			
			$data = @fread($h, $filesize);
			
			if($data === false)
				trigger_error("Error creating class benc for {$filename}: error reading from file", E_USER_WARNING);
			
			@fclose($h);
		} else {
			$data = $benc->getVar('benc');
		}		
		
		$benc->setVar('benc', $data);
		$benc->setVar('object', $this->xtorrent_readbenc($data));
		$this->memref = FALSE;
		
		return $benc;
	}
	
	// Read the next part in the current file
	private function xtorrent_readbenc($data) {
	
		if(!isset($data)) {
			return false;
		}
	
		if($data[$this->memref] == 'e') {
			$this->memref++;
			return false;
		} if($data[$this->memref] == 'd') {
			$start = $this->memref;
			$this->memref++;
			$dictionary = [];
			$current    = false;
			while(($value = $this->xtorrent_readbenc()) !== false) {
				if($current === false) {
					$current = $value;
				} else {
					$dictionary[$current] = $value;
					$current = false;
				}
			}
			
			if(count($dictionary) == 0 || $this->error) {
				trigger_error("Zero Length Dictionary", E_USER_WARNING);
				return false;
			}
			
			$end = $this->memref;
			$dictionary['hash'] = pack("H*", sha1(substr($data,$start,$end - $start)));
			return $dictionary;
		} else if($data[$this->memref] == 'l') {
			
			$this->memref++;
			$list = [];
			for($i=0;($value = $this->xtorrent_readbenc()) !== false;$i++) {
				$list[$i] = $value;
			}
			
			if(count($list) == 0 || $this->error)
				return false;
			
			return $list;
		} else if($data[$this->memref] == 'i') {
			
			$this->memref++;
			
			$endPosition = strpos($data, 'e', $this->memref);
			
			if($endPosition === false || ($endPosition - $this->memref) > 10) {
				$this->error = true;
				return false;
			}
			$readLength = ($endPosition - $this->memref);
			$int        = substr($data, $this->memref, $readLength);
			$this->memref += $readLength + 1;
			return $int;
		} else {
			$nextColon = strpos($data, ':', $this->memref);		
			if($nextColon === false || ($nextColon - $this->memref) > 5) {
				$this->error = true;
				return false;
			}
			$length        = substr($data, $this->memref, $nextColon);
			$readLength    = ($nextColon - $this->memref);
			$this->memref += $readLength + 1;
			$string        = substr($data, $this->memref, $length);
			$this->memref += strlen($string);
			return $string;
		}
	}

	function create(){
		return new $this->obj_class();
	}
	
	function deleteTorrentPermissions($lid, $mode = "view"){
		global $xoopsModule;
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('gperm_itemid', $lid)); 
		$criteria->add(new Criteria('gperm_modid', $xoopsModule->getVar('mid')));
		$criteria->add(new Criteria('gperm_name', $this->perm_name.$mode)); 
		if( $old_perms = $this->perm_handler->getObjects($criteria) ){
			foreach( $old_perms as $p ){
				$this->perm_handler->delete($p);
			}
		}
		return true;
	}
	
	function insertTorrentPermissions($lid, $group_ids, $mode = "view"){
		global $xoopsModule;
		foreach( $group_ids as $lid ){
			$perm = $this->perm_handler->create();
			$perm->setVar('gperm_name', $this->perm_name.$mode);
			$perm->setVar('gperm_itemid', $lid);
			$perm->setVar('gperm_groupid', $lid);
			$perm->setVar('gperm_modid', $xoopsModule->getVar('mid'));
			$this->perm_handler->insert($perm);
			$ii++;
		}
		return "Permission ".$this->perm_name.$mode." set $ii times for "._C_ADMINTITLE." Record ID ".$lid;
	}
	
	function getPermittedTorrents($benc, $mode = "view"){
		global $xoopsUser, $xoopsModule;
		$ret=false;
		if (isset($benc))
		{
			$ret      = [];
			$criteria = new CriteriaCompo();
			$criteria->add(new Criteria('gperm_itemid', $benc->getVar('center_id'), '='), 'AND');
			$criteria->add(new Criteria('gperm_modid', $xoopsModule->getVar('mid'), '='), 'AND');
			$criteria->add(new Criteria('gperm_name', $this->perm_name.$mode, '='), 'AND');						

			$gtObjperm = $this->perm_handler->getObjects($criteria);
			$groups    = [];
			
			foreach ($gtObjperm as $v)
			{
				$ret[] = $v->getVar('gperm_groupid');
			}	
			return $ret;
			
		} else {
			$ret      = [];
			$groups   = is_object($xoopsUser) ? $xoopsUser->getGroups() : 3;
			$criteria = new CriteriaCompo();
			$criteria->add(new Criteria('Torrent_order', 1, '>='), 'OR');
			$criteria->setSort('Torrent_order');
			$criteria->setOrder('ASC');
			if( $benc = $this->getObjects($criteria, 'home_list') ){
				$ret    = [];
				foreach( $benc as $f ){
					if( false != $this->perm_handler->checkRight($this->perm_name.$mode, $f->getVar('center_id'), $groups, $xoopsModule->getVar('mid')) ){
						$ret[] = $f;
						unset($f);
					}
				}
			}
		}
		return ret;
	}
	
	function getSingleTorrentPermission($lid, $mode = "view"){
		global $xoopsUser, $xoopsModule;
		$groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : 3;
		if( false != $this->perm_handler->checkRight($this->perm_name.$mode, $lid, $groups, $xoopsModule->getVar('mid')) ){
			return true;
		}
		return false;
	}
	
}
