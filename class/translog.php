<?php

class translogResource extends XoopsObject {
	function __construct(){
		$this->XoopsObject();
		$this->initVar("id", XOBJ_DTYPE_INT);
		$this->initVar("log_date", XOBJ_DTYPE_TXTBOX);
		$this->initVar("payment_date", XOBJ_DTYPE_TXTBOX);
		$this->initVar("logentry", XOBJ_DTYPE_TXTBOX);
	}
}

class XtorrentTranslogHandler extends XoopsObjectHandler {
	var $db;
	var $db_table;
	var $perm_name = 'xtorrent_translog_';
	var $obj_class = 'translogResource';

	function __construct($db){
		if (!isset($db)&&!empty($db))
		{
			$this->db = $db;
		} else {
			global $xoopsDB;
			$this->db = $xoopsDB;
		}
		$this->db_table = $this->db->prefix('xtorrent_translog');
		$this->perm_handler = xoops_gethandler('groupperm');
	}
	
	function getInstance(&$db){
		static $instance;
		if( !isset($instance) ){
			$instance = new xtorrenttranslogHandler($db);
		}
		return $instance;
	}
	function create(){
		return new $this->obj_class();
	}

	function get($id, $fields='*'){
		$id = intval($id);
		if( $id > 0 ){
			$sql = 'SELECT '.$fields.' FROM '.$this->db_table.' WHERE id='.$id;
		} else {
			return false;
		}
		if( !$result = $this->db->query($sql) ){
			return false;
		}
		$numrows = $this->db->getRowsNum($result);
		if( $numrows == 1 ){
			$translog = new $this->obj_class();
			$translog->assignVars($this->db->fetchArray($result));
			return $translog;
		}
		return false;
	}

	function insert($translog, $force = false){
        if( strtolower(get_class($translog)) != strtolower($this->obj_class)){
            return false;
        }
        if( !$translog->isDirty() ){
            return true;
        }
        if( !$translog->cleanVars() ){
            return false;
        }
		foreach( $translog->cleanVars as $k=>$v ){
			${$k} = $v;
		}
		$myts = MyTextSanitizer::getInstance();
		if( $translog->isNew() || empty($id) ){
			$id = $this->db->genId($this->db_table."_xt_translog_id_seq");
			$sql = sprintf("INSERT INTO %s (
				`id`, `log_date`, `payment_date`, `logentry`,
				) VALUES (
				%u, %s, %s, %s
				)",
				$this->db_table,
				$this->db->quoteString($id),
				$this->db->quoteString($log_date),
				$this->db->quoteString($payment_date),
				$this->db->quoteString($logentry)
			);
		}else{
			$sql = sprintf("UPDATE %s SET
				`log_date` = %s,
				`payment_date` = %s,
				`logentry` = %s WHERE id = %s",
				$this->db_table,
				$this->db->quoteString($log_date),
				$this->db->quoteString($payment_date),
				$this->db->quoteString($logentry),
				$this->db->quoteString($id)
			);
		}
		
		if( false != $force ){
            $result = $this->db->queryF($sql);
        }else{
            $result = $this->db->query($sql);
        }
		if( !$result ){
			$translog->setErrors("Could not store data in the database.<br />".$this->db->error().' ('.$this->db->errno().')<br />'.$sql);
			return false;
		}
		if( empty($id) ){
			$id = $this->db->getInsertId();
		}
        $translog->assignVar('id', $id);
		return $id;
	}
	
	function delete($criteria = null, $force = false){
		if( strtolower(get_class($translog)) != strtolower($this->obj_class) ){
			return false;
		}
		if( isset($criteria) && is_subclass_of($criteria, 'criteriaelement') ){
			$sql = "DELETE FROM ".$this->db_table." ".$criteria->renderWhere()."";
		}
        if( false != $force ){
            $result = $this->db->queryF($sql);
        }else{
            $result = $this->db->query($sql);
        }
		return true;
	}
	
	function delete_scalar($limit = 20, $sort = "id DESC")
	{
		if ($this->getCount>$limit)
		{
			$sql = "SELECT id as lowid FROM ".$this->db->prefix("xtorrent_translog")." ORDER BY $sort LIMIT $limit";
			$result = $this->db->queryF($sql);
			while(list($lowid) = $this->db->fetchRow($result))
			{
					$sql =  "DELETE FROM ".$this->db->prefix("xtorrent_translog")." WHERE id < '" . $lowid . "'";
					$result = $this->db->queryF($sql);
			}
		}
	}

	function getObjects($criteria = null, $fields='*', $id_as_key = false){
		$ret   = [];
		$limit = $start = 0;
		$sql   = 'SELECT '.$fields.' FROM '.$this->db_table;
		if( isset($criteria) && is_subclass_of($criteria, 'criteriaelement') ){
			$sql .= ' '.$criteria->renderWhere();
			if( $criteria->getSort() != '' ){
				$sql .= ' ORDER BY '.$criteria->getSort().' '.$criteria->getOrder();
			}
			$limit = $criteria->getLimit();
			$start = $criteria->getStart();
		}
		$result = $this->db->query($sql, $limit, $start);
		if( !$result )
			return false;
		while( $myrow = $this->db->fetchArray($result) ){
			$translog = new $this->obj_class();
			$translog->assignVars($myrow);
			if( !$id_as_key ){
				$ret[] = $translog;
			}else{
				$ret[$myrow['id']] = $translog;
			}
			unset($translog);
		}
		return count($ret) > 0 ? $ret : false;
	}
	
    function getCount($criteria = null){
		$sql = 'SELECT COUNT(*) FROM '.$this->db_table;
		if( isset($criteria) && is_subclass_of($criteria, 'criteriaelement') ){
			$sql .= ' '.$criteria->renderWhere();
		}
		$result = $this->db->query($sql);
		if( !$result ){
			return 0;
		}
		list($count) = $this->db->fetchRow($result);
		return $count;
	}
    
    function deleteAll($criteria = null){
		$sql = 'DELETE FROM '.$this->db_table;
		if( isset($criteria) && is_subclass_of($criteria, 'criteriaelement') ){
			$sql .= ' '.$criteria->renderWhere();
		}
		if( !$result = $this->db->query($sql) ){
			return false;
		}
		return true;
	}
	
	function deleteTorrentPermissions($id, $mode = "view"){
		global $xoopsModule;
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('gperm_itemid', $id)); 
		$criteria->add(new Criteria('gperm_modid', $xoopsModule->getVar('mid')));
		$criteria->add(new Criteria('gperm_name', $this->perm_name.$mode)); 
		if( $old_perms =& $this->perm_handler->getObjects($criteria) ){
			foreach( $old_perms as $p ){
				$this->perm_handler->delete($p);
			}
		}
		return true;
	}
	
	function insertTorrentPermissions($id, $group_ids, $mode = "view"){
		global $xoopsModule;
		foreach( $group_ids as $id ){
			$perm = $this->perm_handler->create();
			$perm->setVar('gperm_name', $this->perm_name.$mode);
			$perm->setVar('gperm_itemid', $id);
			$perm->setVar('gperm_groupid', $id);
			$perm->setVar('gperm_modid', $xoopsModule->getVar('mid'));
			$this->perm_handler->insert($perm);
			$ii++;
		}
		return "Permission ".$this->perm_name.$mode." set $ii times for "._C_ADMINTITLE." Record ID ".$id;
	}
	
	function getPermittedTorrents($translog, $mode = "view"){
		global $xoopsUser, $xoopsModule;
		$ret=false;
		if (isset($translog))
		{
			$ret      = [];
			$criteria = new CriteriaCompo();
			$criteria->add(new Criteria('gperm_itemid', $translog->getVar('id'), '='), 'AND');
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
			$ret    = [];
			$groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : 3;
			$criteria = new CriteriaCompo();
			$criteria->add(new Criteria('Torrent_order', 1, '>='), 'OR');
			$criteria->setSort('Torrent_order');
			$criteria->setOrder('ASC');
			if( $translog = $this->getObjects($criteria, 'home_list') ){
				$ret = [];
				foreach( $translog as $f ){
					if( false != $this->perm_handler->checkRight($this->perm_name.$mode, $f->getVar('id'), $groups, $xoopsModule->getVar('mid')) ){
						$ret[] = $f;
						unset($f);
					}
				}
			}
		}
		return ret;
	}
	
	function getSingleTorrentPermission($id, $mode = "view"){
		global $xoopsUser, $xoopsModule;
		$groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : 3;
		if( false != $this->perm_handler->checkRight($this->perm_name.$mode, $id, $groups, $xoopsModule->getVar('mid')) ){
			return true;
		}
		return false;
	}
	
}
