<?php

class votedataResource extends XoopsObject
{
    public function __construct()
    {
        $this->XoopsObject();
        $this->initVar("ratingid", XOBJ_DTYPE_INT);
        $this->initVar("lid", XOBJ_DTYPE_INT);
        $this->initVar("ratinguser", XOBJ_DTYPE_INT);
        $this->initVar("rating", XOBJ_DTYPE_INT);
        $this->initVar("ratinghostname", XOBJ_DTYPE_TXTBOX);
        $this->initVar("ratingtimestamp", XOBJ_DTYPE_INT);
    }
}

class XtorrentVotedataHandler extends XoopsObjectHandler
{
    public $db;
    public $db_table;
    public $perm_name = 'xtorrent_votedata_';
    public $obj_class = 'votedataResource';

    public function __construct($db)
    {
        if (!isset($db)&&!empty($db)) {
            $this->db = $db;
        } else {
            global $xoopsDB;
            $this->db = $xoopsDB;
        }
        $this->db_table = $this->db->prefix('xtorrent_votedata');
        $this->perm_handler = xoops_gethandler('groupperm');
    }
    
    public function getInstance($db)
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new xtorrentvotedataHandler($db);
        }
        return $instance;
    }
    public function &create()
    {
        return new $this->obj_class();
    }

    public function get($ratingid, $fields='*')
    {
        $ratingid = intval($ratingid);
        if ($ratingid > 0) {
            $sql = 'SELECT '.$fields.' FROM '.$this->db_table.' WHERE ratingid ='.$ratingid;
        } else {
            return false;
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        $numrows = $this->db->getRowsNum($result);
        if ($numrows == 1) {
            $votedata = new $this->obj_class();
            $votedata->assignVars($this->db->fetchArray($result));
            return $votedata;
        }
        return false;
    }

    public function insert($votedata, $force = false)
    {
        if (strtolower(get_class($votedata)) != strtolower($this->obj_class)) {
            return false;
        }
        if (!$votedata->isDirty()) {
            return true;
        }
        if (!$votedata->cleanVars()) {
            return false;
        }
        foreach ($votedata->cleanVars as $k=>$v) {
            ${$k} = $v;
        }
        $myts = MyTextSanitizer::getInstance();
        if ($votedata->isNew() || empty($ratingid)) {
            $ratingid = $this->db->genId($this->db_table."_xt_votedata_id_seq");
            $sql = sprintf(
                "INSERT INTO %s (
				`ratingid`, `lid`, `ratinguser`, `rating`, `ratinghostname`, `ratingtimestamp`
				) VALUES (
				%u, %s, %s, %s, %s, %s
				)",
                $this->db_table,
                $this->db->quoteString($ratingid),
                $this->db->quoteString($lid),
                $this->db->quoteString($ratinguser),
                $this->db->quoteString($rating),
                $this->db->quoteString($ratinghostname),
                $this->db->quoteString($ratingtimestamp)
            );
        } else {
            $sql = sprintf(
                "UPDATE %s SET
				`lid` = %s,
				`ratinguser` = %s,
				`rating` = %s,
				`ratinghostname` = %s,
				`ratingtimestamp` = %s WHERE `ratingid` = %s",
                $this->db_table,
                $this->db->quoteString($lid),
                $this->db->quoteString($ratinguser),
                $this->db->quoteString($rating),
                $this->db->quoteString($ratinghostname),
                $this->db->quoteString($ratingtimestamp),
                $this->db->quoteString($ratingid)
            );
        }
        
        if (false != $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            $votedata->setErrors("Could not store data in the database.<br />".$this->db->error().' ('.$this->db->errno().')<br />'.$sql);
            return false;
        }
        if (empty($ratingid)) {
            $ratingid = $this->db->getInsertId();
        }
        $votedata->assignVar('id', $ratingid);
        return $ratingid;
    }
    
    public function delete($criteria = null, $force = false)
    {
        if (strtolower(get_class($votedata)) != strtolower($this->obj_class)) {
            return false;
        }
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql = "DELETE FROM ".$this->db_table." ".$criteria->renderWhere()."";
        }
        if (false != $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        return true;
    }

    public function &getObjects($criteria = null, $fields='*', $ratingid_as_key = false)
    {
        $ret   = [];
        $limit = $start = 0;
        $sql   = 'SELECT '.$fields.' FROM '.$this->db_table;
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' '.$criteria->renderWhere();
            if ($criteria->getSort() != '') {
                $sql .= ' ORDER BY '.$criteria->getSort().' '.$criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return false;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $votedata = new $this->obj_class();
            $votedata->assignVars($myrow);
            if (!$ratingid_as_key) {
                $ret[] = $votedata;
            } else {
                $ret[$myrow['id']] = $votedata;
            }
            unset($votedata);
        }
        return count($ret) > 0 ? $ret : false;
    }
    
    public function getCount($criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM '.$this->db_table;
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' '.$criteria->renderWhere();
        }
        $result = $this->db->query($sql);
        if (!$result) {
            return 0;
        }
        list($count) = $this->db->fetchRow($result);
        return $count;
    }
    
    public function deleteAll($criteria = null)
    {
        $sql = 'DELETE FROM '.$this->db_table;
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' '.$criteria->renderWhere();
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        return true;
    }
    
    public function deleteTorrentPermissions($ratingid, $mode = "view")
    {
        global $xoopsModule;
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('gperm_itemid', $ratingid));
        $criteria->add(new Criteria('gperm_modid', $xoopsModule->getVar('mid')));
        $criteria->add(new Criteria('gperm_name', $this->perm_name.$mode));
        if ($old_perms = $this->perm_handler->getObjects($criteria)) {
            foreach ($old_perms as $p) {
                $this->perm_handler->delete($p);
            }
        }
        return true;
    }
    
    public function insertTorrentPermissions($ratingid, $group_ids, $mode = "view")
    {
        global $xoopsModule;
        foreach ($group_ids as $ratingid) {
            $perm = $this->perm_handler->create();
            $perm->setVar('gperm_name', $this->perm_name.$mode);
            $perm->setVar('gperm_itemid', $ratingid);
            $perm->setVar('gperm_groupid', $ratingid);
            $perm->setVar('gperm_modid', $xoopsModule->getVar('mid'));
            $this->perm_handler->insert($perm);
            $ii++;
        }
        return "Permission ".$this->perm_name.$mode." set $ii times for "._C_ADMINTITLE." Record ID ".$ratingid;
    }
    
    public function &getPermittedTorrents($votedata, $mode = "view")
    {
        global $xoopsUser, $xoopsModule;
        $ret = false;
        if (isset($votedata)) {
            $ret      = [];
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('gperm_itemid', $votedata->getVar('ratingid'), '='), 'AND');
            $criteria->add(new Criteria('gperm_modid', $xoopsModule->getVar('mid'), '='), 'AND');
            $criteria->add(new Criteria('gperm_name', $this->perm_name.$mode, '='), 'AND');

            $gtObjperm = $this->perm_handler->getObjects($criteria);
            $groups    = [];
            
            foreach ($gtObjperm as $v) {
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
            if ($votedata = $this->getObjects($criteria, 'home_list')) {
                $ret = [];
                foreach ($votedata as $f) {
                    if (false != $this->perm_handler->checkRight($this->perm_name.$mode, $f->getVar('ratingid'), $groups, $xoopsModule->getVar('mid'))) {
                        $ret[] = $f;
                        unset($f);
                    }
                }
            }
        }
        return ret;
    }
    
    public function getSingleTorrentPermission($ratingid, $mode = "view")
    {
        global $xoopsUser, $xoopsModule;
        $groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : 3;
        if (false != $this->perm_handler->checkRight($this->perm_name.$mode, $ratingid, $groups, $xoopsModule->getVar('mid'))) {
            return true;
        }
        return false;
    }
}
