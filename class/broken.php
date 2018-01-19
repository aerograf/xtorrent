<?php

class brokenResource extends XoopsObject
{
    public function __construct()
    {
        $this->XoopsObject();
        $this->initVar('reportid', XOBJ_DTYPE_INT);
        $this->initVar('lid', XOBJ_DTYPE_INT);
        $this->initVar('sender', XOBJ_DTYPE_INT);
        $this->initVar('ip', XOBJ_DTYPE_TXTBOX);
        $this->initVar('date', XOBJ_DTYPE_TXTBOX);
        $this->initVar('confirm', XOBJ_DTYPE_TXTBOX);
        $this->initVar('acknowledged', XOBJ_DTYPE_TXTBOX);
    }
}

class XtorrentBrokenHandler extends XoopsObjectHandler
{
    public $db;
    public $db_table;
    public $perm_name = 'xtorrent_broken_';
    public $obj_class = 'brokenResource';

    public function __construct($db)
    {
        if (!isset($db)&&!empty($db)) {
            $this->db = $db;
        } else {
            global $xoopsDB;
            $this->db = $xoopsDB;
        }
        $this->db_table = $this->db->prefix('xtorrent_broken');
        $this->perm_handler = xoops_gethandler('groupperm');
    }
    
    public function getInstance($db)
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new xtorrentbrokenHandler($db);
        }
        return $instance;
    }
    public function create()
    {
        return new $this->obj_class();
    }

    public function get($reportid, $fields='*')
    {
        $reportid = (int)$reportid;
        if ($reportid > 0) {
            $sql = 'SELECT ' . $fields . ' FROM ' . $this->db_table . ' WHERE reportid =' . $reportid;
        } else {
            return false;
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        $numrows = $this->db->getRowsNum($result);
        if (1 == $numrows) {
            $broken = new $this->obj_class();
            $broken->assignVars($this->db->fetchArray($result));
            return $broken;
        }
        return false;
    }

    public function insert(&$broken, $force = false)
    {
        if (strtolower(get_class($broken)) != strtolower($this->obj_class)) {
            return false;
        }
        if (!$broken->isDirty()) {
            return true;
        }
        if (!$broken->cleanVars()) {
            return false;
        }
        foreach ($broken->cleanVars as $k=>$v) {
            ${$k} = $v;
        }
        $myts = MyTextSanitizer::getInstance();
        if ($broken->isNew() || empty($reportid)) {
            $reportid = $this->db->genId($this->db_table . '_xt_broken_id_seq');
            $sql      = sprintf(
                'INSERT INTO %s (
				`reportid`, `lid`, `sender`, `ip`, `date`, `confirmed`, `acknowledged`
				) VALUES (
				%u, %s, %s, %s, %s, %s, %s
				)',
                $this->db_table,
                $this->db->quoteString($reportid),
                $this->db->quoteString($lid),
                $this->db->quoteString($sender),
                $this->db->quoteString($ip),
                $this->db->quoteString($date),
                $this->db->quoteString($confirmed),
                $this->db->quoteString($acknowledged)
            );
        } else {
            $sql = sprintf(
                'UPDATE %s SET
				`lid` = %s,
				`sender` = %s,
				`ip` = %s,
				`date` = %s,
				`confirmed` = %s,
				`acknowledged` = %s WHERE `reportid` = %s',
                $this->db_table,
                $this->db->quoteString($lid),
                $this->db->quoteString($sender),
                $this->db->quoteString($ip),
                $this->db->quoteString($date),
                $this->db->quoteString($confirmed),
                $this->db->quoteString($acknowledged),
                $this->db->quoteString($reportid)
            );
        }
        
        if (false != $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            $broken->setErrors('Could not store data in the database.<br />' . $this->db->error() . ' (' . $this->db->errno() . ')<br />' . $sql);
            return false;
        }
        if (empty($reportid)) {
            $reportid = $this->db->getInsertId();
        }
        $broken->assignVar('id', $reportid);
        return $reportid;
    }
    
    public function delete($criteria = null, $force = false)
    {
        if (strtolower(get_class($broken)) != strtolower($this->obj_class)) {
            return false;
        }
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql = 'DELETE FROM ' . $this->db_table . ' ' . $criteria->renderWhere() . '';
        }
        if (false != $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        return true;
    }

    public function getObjects($criteria = null, $fields='*', $reportid_as_key = false)
    {
        $ret   = [];
        $limit = $start = 0;
        $sql   = 'SELECT '.$fields.' FROM '.$this->db_table;
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' '.$criteria->renderWhere();
            if ('' != $criteria->getSort()) {
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
            $broken = new $this->obj_class();
            $broken->assignVars($myrow);
            if (!$reportid_as_key) {
                $ret[] = $broken;
            } else {
                $ret[$myrow['id']] = $broken;
            }
            unset($broken);
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
    
    public function deleteTorrentPermissions($reportid, $mode = 'view')
    {
        global $xoopsModule;
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('gperm_itemid', $reportid));
        $criteria->add(new Criteria('gperm_modid', $xoopsModule->getVar('mid')));
        $criteria->add(new Criteria('gperm_name', $this->perm_name.$mode));
        if ($old_perms = $this->perm_handler->getObjects($criteria)) {
            foreach ($old_perms as $p) {
                $this->perm_handler->delete($p);
            }
        }
        return true;
    }
    
    public function insertTorrentPermissions($reportid, $group_ids, $mode = 'view')
    {
        global $xoopsModule;
        foreach ($group_ids as $reportid) {
            $perm = $this->perm_handler->create();
            $perm->setVar('gperm_name', $this->perm_name.$mode);
            $perm->setVar('gperm_itemid', $reportid);
            $perm->setVar('gperm_groupid', $reportid);
            $perm->setVar('gperm_modid', $xoopsModule->getVar('mid'));
            $this->perm_handler->insert($perm);
            $ii++;
        }
        return 'Permission ' . $this->perm_name . $mode . " set $ii times for " . _C_ADMINTITLE . ' Record ID ' . $reportid;
    }
    
    public function getPermittedTorrents($broken, $mode = 'view')
    {
        global $xoopsUser, $xoopsModule;
        $ret=false;
        if (isset($broken)) {
            $ret      = [];
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('gperm_itemid', $broken->getVar('reportid'), '='), 'AND');
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
            if ($broken = $this->getObjects($criteria, 'home_list')) {
                $ret = [];
                foreach ($broken as $f) {
                    if (false != $this->perm_handler->checkRight($this->perm_name.$mode, $f->getVar('reportid'), $groups, $xoopsModule->getVar('mid'))) {
                        $ret[] = $f;
                        unset($f);
                    }
                }
            }
        }
        return ret;
    }
    
    public function getSingleTorrentPermission($reportid, $mode = 'view')
    {
        global $xoopsUser, $xoopsModule;
        $groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : 3;
        if (false != $this->perm_handler->checkRight($this->perm_name.$mode, $reportid, $groups, $xoopsModule->getVar('mid'))) {
            return true;
        }
        return false;
    }
}
