<?php

class financialResource extends XoopsObject {
	function __construct(){
		$this->XoopsObject();
		$this->initVar("id", XOBJ_DTYPE_INT);
		$this->initVar("date", XOBJ_DTYPE_TXTBOX);
		$this->initVar("num", XOBJ_DTYPE_TXTBOX);
		$this->initVar("name", XOBJ_DTYPE_TXTBOX);
		$this->initVar("descr", XOBJ_DTYPE_TXTBOX);
		$this->initVar("amount", XOBJ_DTYPE_TXTBOX);						
	}
}

class XtorrentFinancialHandler extends XoopsObjectHandler {
	var $db;
	var $db_table;
	var $perm_name = 'xtorrent_financial_';
	var $obj_class = 'financialResource';

	function __construct($db){
		if (!isset($db)&&!empty($db))
		{
			$this->db = $db;
		} else {
			global $xoopsDB;
			$this->db = $xoopsDB;
		}
		$this->db_table = $this->db->prefix('xtorrent_financial');
		$this->perm_handler = xoops_gethandler('groupperm');
	}
	
	function getInstance($db){
		static $instance;
		if( !isset($instance) ){
			$instance = new xtorrentfinancialHandler($db);
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
			$financial = new $this->obj_class();
			$financial->assignVars($this->db->fetchArray($result));
			return $financial;
		}
		return false;
	}

	function insert($financial, $force = false){
        if( strtolower(get_class($financial)) != strtolower($this->obj_class)){
            return false;
        }
        if( !$financial->isDirty() ){
            return true;
        }
        if( !$financial->cleanVars() ){
            return false;
        }
		foreach( $financial->cleanVars as $k=>$v ){
			${$k} = $v;
		}
		$myts = MyTextSanitizer::getInstance();
		if( $financial->isNew() || empty($id) ){
			$id  = $this->db->genId($this->db_table."_xt_financial_id_seq");
			$sql = sprintf("INSERT INTO %s (
				`id`, `date`, `num`, `name`, `descr`, `amount`
				) VALUES (
				%u, %s, %s, %s, %s
				)",
				$this->db_table,
				$this->db->quoteString($id),
				$this->db->quoteString($date),
				$this->db->quoteString($num),
				$this->db->quoteString($name),
				$this->db->quoteString($descr),
				$this->db->quoteString($amount)
			);
		}else{
			$sql = sprintf("UPDATE %s SET
				`date` = %s,
				`num` = %s,
				`name` = %s,
				`descr` = %s,
				`amount` = %s WHERE id = %s",
				$this->db_table,
				$this->db->quoteString($date),
				$this->db->quoteString($num),
				$this->db->quoteString($name),
				$this->db->quoteString($descr),
				$this->db->quoteString($amount),
				$this->db->quoteString($id)
			);
		}
		
		if( false != $force ){
            $result = $this->db->queryF($sql);
        }else{
            $result = $this->db->query($sql);
        }
		if( !$result ){
			$financial->setErrors("Could not store data in the database.<br />".$this->db->error().' ('.$this->db->errno().')<br />'.$sql);
			return false;
		}
		if( empty($id) ){
			$id = $this->db->getInsertId();
		}
        $financial->assignVar('id', $id);
		return $id;
	}
	
	function delete($criteria = null, $force = false){
		if( strtolower(get_class($financial)) != strtolower($this->obj_class) ){
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
			$financial = new $this->obj_class();
			$financial->assignVars($myrow);
			if( !$id_as_key ){
				$ret[] = $financial;
			}else{
				$ret[$myrow['id']] = $financial;
			}
			unset($financial);
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
		if( $old_perms = $this->perm_handler->getObjects($criteria) ){
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
	
	function getPermittedTorrents($financial, $mode = "view"){
		global $xoopsUser, $xoopsModule;
		$ret=false;
		if (isset($financial))
		{
			$ret      = [];
			$criteria = new CriteriaCompo();
			$criteria->add(new Criteria('gperm_itemid', $financial->getVar('id'), '='), 'AND');
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
			if( $financial = $this->getObjects($criteria, 'home_list') ){
				$ret = [];
				foreach( $financial as $f ){
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
