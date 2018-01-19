<?php

class ioResource extends XoopsObject {
	public function __construct(){
		$this->XoopsObject();
		$this->initVar('session', XOBJ_DTYPE_INT);
		$this->initVar('xoopsUser');
	}
}

class XtorrentIoHandler extends XoopsObjectHandler {
	public $db;
	public $db_table;
	public $perm_name = 'xtorrent_io_';
	public $obj_class = 'ioResource';

	public function __construct($db){
		if (!isset($db)&&!empty($db))
		{
			$this->db = $db;
		} else {
			global $xoopsDB;
			$this->db = $xoopsDB;
		}
		$this->db_table = $this->db->prefix('xtorrent_io');
		//$this->perm_handler =& xoops_gethandler('groupperm');
	}
	
	public function getInstance($db){
		static $instance;
		if( !isset($instance) ){
			$instance = new xtorrentioHandler($db);
		}
		return $instance;
	}
	
	public function err($msg)
	{
		benc_resp(['failure reason' => [type => 'string', value => $msg]]);
		hit_end();
		exit();
	}
	
	public function benc_resp($d)
	{
		$xthdlr_benc  = xoops_load('benc', 'xtorrent');
		$benc_torrent = $xthdlr_benc->create();				
		$benc_torrent->setVar('object', [type => 'dictionary', value => $d]);
		$benc_torrent = $xthdlr_benc->compile($benc_torrent);
		benc_resp_raw($benc_torrent->getVar('benc'));
	}
	
	public function benc_resp_raw($x)
	{
		header('Content-Type: text/plain');
		header('Pragma: no-cache');
		print($x);
	}

	
	public function strip_magic_quotes($arr)
	{
		foreach ($arr as $k => $v)
		{
			 if (is_array($v))
			  { $arr[$k] = strip_magic_quotes($v); }
			 else
			  { $arr[$k] = stripslashes($v); }
		}
		
		return $arr;
	}

	public function ipn_out($str, $clvl)
	{
		global $dbg, $lp, $log, $loglvl;
		if( $lp ) 
				fwrite($lp, $str . "\n");
		if( $dbg ) 
				echo $str . '<br>';
		if( $clvl <= $loglvl )
				$log .= $str . "\n";
	}
	
	public function validfilename($name) {
		return preg_match('/^[^\0-\x1f:\\\\\/?*\xff#<>|]+$/si', $name);
	}
	
	public function validemail($email) {
		return preg_match('/^[\w.-]+@([\w.-]+\.)+[a-z]{2,6}$/is', $email);
	}
	
	public function gmtime()
	{
		return strtotime(get_date_time());
	}

	public function hash_pad($hash) {
		return str_pad($hash, 20);
	}
	
	public function shhash($hash) {
		return preg_replace('/ *$/s', '', $hash);
	}
	
	public function getip() {
		if (isset($_SERVER)) {
			if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			  	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
			  	$ip = $_SERVER['HTTP_CLIENT_IP'];
			} else {
			  	$ip = $_SERVER['REMOTE_ADDR'];
			}
		} else {
			if (getenv('HTTP_X_FORWARDED_FOR')) {
			  	$ip = getenv('HTTP_X_FORWARDED_FOR');
			} elseif (getenv('HTTP_CLIENT_IP')) {
			  	$ip = getenv('HTTP_CLIENT_IP');
			} else {
			  	$ip = getenv('REMOTE_ADDR');
			}
		}
		return $ip;
	}

	public function unesc($x) {
		if (get_magic_quotes_gpc())
			return stripslashes($x);
		return $x;
	}
	
	public function portblacklisted($port)
	{
		global $xoopsModuleConfig;
		$ports = explode(',', str_replace(' ', '', $xoopsModuleConfig['ports_checked']));
		
		foreach ($ports as $k => $v)
		{
			if (strpos($v, '-'))
			{
				$range = explode('-', $v);
				if ((int)$port >= (int)$range[0] && (int)$port <= (int)$range[1]) return true;				
			} else {
				if ((int)$port == (int)$v) return true;		
			}
		
		}
	
		return false;
	}

	public function benc_str($s) {
		return strlen($s) . ":$s";
	}
	
	public function benc_int($i) {
		return 'i' . $i . 'e';
	}
	
	public function mksize($bytes)
	{
		if ($bytes < 1000 * 1024)
			return number_format($bytes / 1024, 2) . ' kB';
		elseif ($bytes < 1000 * 1048576)
			return number_format($bytes / 1048576, 2) . ' MB';
		elseif ($bytes < 1000 * 1073741824)
			return number_format($bytes / 1073741824, 2) . ' GB';
		else
			return number_format($bytes / 1099511627776, 2) . ' TB';
	}
	
	public function mksizeint($bytes)
	{
		$bytes = max(0, $bytes);
		if ($bytes < 1000)
			return floor($bytes) . ' B';
		elseif ($bytes < 1000 * 1024)
			return floor($bytes / 1024) . ' kB';
		elseif ($bytes < 1000 * 1048576)
			return floor($bytes / 1048576) . ' MB';
		elseif ($bytes < 1000 * 1073741824)
			return floor($bytes / 1073741824) . ' GB';
		else
			return floor($bytes / 1099511627776) . ' TB';
	}
	
	public function deadtime() {
		return time() - floor($this->->tracker_wait * 1.3);
	}
	
	public function mkprettytime($s) {
		if ($s < 0)
		$s = 0;
		$t = [];
		foreach (['60:sec', '60:min', '24:hour', '0:day'] as $x) {
			$y = explode(':', $x);
			if ($y[0] > 1) {
				$v = $s % $y[0];
				$s = floor($s / $y[0]);
			}
			else
			$v        = $s;
			$t[$y[1]] = $v;
		}
	
		if ($t['day'])
			return $t['day'] . 'd ' . sprintf('%02d:%02d:%02d', $t['hour'], $t['min'], $t['sec']);
		if ($t['hour'])
			return sprintf('%d:%02d:%02d', $t['hour'], $t['min'], $t['sec']);
	    if ($t['min'])
			return sprintf('%d:%02d', $t['min'], $t['sec']);
	    return $t['sec'] . ' secs';
	}

	
	public function validip($ip)
	{
		if (!empty($ip) && ip2long($ip)!=-1)
		{
			$reserved_ips = [
					['0.0.0.0','2.255.255.255'],
					['10.0.0.0','10.255.255.255'],
					['127.0.0.0','127.255.255.255'],
					['169.254.0.0','169.254.255.255'],
					['172.16.0.0','172.31.255.255'],
					['192.0.2.0','192.0.2.255'],
					['192.168.0.0','192.168.255.255'],
					['255.255.255.0','255.255.255.255']
			];
	
			foreach ($reserved_ips as $r)
			{
					$min = ip2long($r[0]);
					$max = ip2long($r[1]);
					if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
			}
			return true;
		}
		else return false;
	}

	public function agents_disallowed()
	{
		global $xoopsModuleConfig;
		return !isset($xoopsModuleConfig['agents_disallowed']) ?'':(string)$xoopsModuleConfig['agents_disallowed'];
	}

	public function numleechers()
	{
		global $xoopsModuleConfig;
		return !isset($xoopsModuleConfig['numleechers']) ?300:(int)$xoopsModuleConfig['numleechers'];
	}

	public function numseeds()
	{
		global $xoopsModuleConfig;
		return !isset($xoopsModuleConfig['numseeds']) ?1000:(int)$xoopsModuleConfig['numseeds'];
	}

	public function max_torrent_size()
	{
		global $xoopsModuleConfig;
		return !isset($xoopsModuleConfig['max_torrent_size']) ?1000000:(int)$xoopsModuleConfig['max_torrent_size'];
	}

	public function max_torrent_size()
	{
		global $xoopsModuleConfig;
		return !isset($xoopsModuleConfig['max_torrent_size']) ?1000000:(int)$xoopsModuleConfig['max_torrent_size'];
	}

	public function completed_notice()
	{
		global $xoopsConfig;
		return !isset($xoopsConfig['completed_notice']) ?TRUE:(int)$xoopsConfig['completed_notice'];
	}

	public function stopped_notice()
	{
		global $xoopsConfig;
		return !isset($xoopsConfig['stopped_notice']) ?FALSE:(int)$xoopsConfig['stopped_notice'];
	}

	public function sitename()
	{
		global $xoopsConfig;
		return !isset($xoopsConfig['sitename']) ?'X-Torrent Module Site':(string)$xoopsConfig['sitename'];
	}

	public function siteemail()
	{
		global $xoopsConfig;
		return !isset($xoopsConfig['adminemail']) ?'example555@hotmail.com':(string)$xoopsConfig['adminemail'];
	}

	public function peerlimit()
	{
		global $xoopsModuleConfig;
		return !isset($xoopsModuleConfig['peerlimit']) ?1:(int)$xoopsModuleConfig['peerlimit'];
	}
	
	public function baseurl()
	{
		return XOOPS_URL;
	}

	public function autoclean_interval()
	{
		global $xoopsModuleConfig;
		return !isset($xoopsModuleConfig['autoclean_interval']) ? 900 :(int)$xoopsModuleConfig['autoclean_interval'];
	}

	public function max_torrent_size()
	{
		global $xoopsModuleConfig;
		return !isset($xoopsModuleConfig['max_torrent_size']) ?1000000:(int)$xoopsModuleConfig['max_torrent_size'];
	}

	public function tracker_wait()
	{
		global $xoopsModuleConfig;
		return !isset($xoopsModuleConfig['tracker_wait']) ?(60 * 30):(int)$xoopsModuleConfig['tracker_wait'];
	}
	
	public function mit_timeout()
	{
		global $xoopsModuleConfig;
		return !isset($xoopsModuleConfig['mit_timeout']) ?(86400 * 3):(int)$xoopsModuleConfig['mit_timeout'];
	}	

	public function minvotes()
	{
		global $xoopsModuleConfig;
		return !isset($xoopsModuleConfig['minvotes']) ? 1 :(int)$xoopsModuleConfig['minvotes'];
	}	
	
	public function dead_torrent_time()
	{
		global $xoopsModuleConfig;
		return !isset($xoopsModuleConfig['dead_torrent_time']) ?(6 * 3600):(int)$xoopsModuleConfig['dead_torrent_time'];
	}	

	public function online()
	{
		global $xoopsModuleConfig;
		return !isset($xoopsModuleConfig['online']) ? TRUE :(int)$xoopsModuleConfig['online'];
	}	
	
	public function maxusers()
	{
		global $xoopsModuleConfig;
		return !isset($xoopsModuleConfig['maxusers']) ? 450009 :(int)$xoopsModuleConfig['maxusers'];
	}	
	
	public function announce_urls()
	{
		global $xoopsModuleConfig;
		$announce_urls   = [];
		$announce_urls[] = str_replace('{XOOPS_URL}', XOOPS_URL, $xoopsModuleConfig['announce_url']);
		return $announce_urls;
	}

	public function local_user()
	{
		global $HTTP_SERVER_VARS;
		return $HTTP_SERVER_VARS['SERVER_ADDR'] == $HTTP_SERVER_VARS['REMOTE_ADDR'];
	}
	
	public function create(){
		global $xoopsUser;
		
		$io_obj = new $this->obj_class();
		
		if (isset($xoopsUser))
			$io_obj->setVar('session',sha1($xoopsUser->getVar('uid').$xoopsUser->getVar('uname').$xoopsUser->getVar('name').$xoopsUser->getVar('last_login').get_date_time()));
		else 
			$io_obj->setVar('session',sha1($this->getip.$this->sitename.$this->siteemail.mt_rand(0,mt_rand(237,328683)).get_date_time()));
			
		$io_obj->setVar('xoopsUser', $xoopsUser);
		
		return $io_obj;
	}
	
}
