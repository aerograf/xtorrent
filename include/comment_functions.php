<?php

// comment callback functions

function xtorrent_com_update($download_id, $total_num){
	$db  = Database::getInstance();
	$sql = 'UPDATE ' . $db->prefix('xtorrent_downloads') . ' SET comments = ' . $total_num . ' WHERE lid = ' . $download_id;
	$db -> query($sql);
}

function xtorrent_com_approve(&$comment){
	// notification mail here
}
