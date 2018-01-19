<?php

class pollResource extends XoopsObject
{
    public function __construct()
    {
        $this->XoopsObject();
        $this->initVar('lid', XOBJ_DTYPE_INT);
        $this->initVar('torrent', XOBJ_DTYPE_INT);
        $this->initVar('tracker', XOBJ_DTYPE_INT);
    }
}

class XtorrentPollHandler extends XoopsObjectHandler
{
    public $db;
    public $db_table;
    public $perm_name = 'xtorrent_poll_';
    public $obj_class = 'pollResource';

    public function __construct($db)
    {
        if (!isset($db)&&!empty($db)) {
            $this->db = $db;
        } else {
            global $xoopsDB;
            $this->db = $xoopsDB;
        }
        $this->db_table = $this->db->prefix('xtorrent_poll');
        $this->perm_handler = xoops_gethandler('groupperm');
    }
    
    public function getInstance($db)
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new xtorrentpollHandler($db);
        }
        return $instance;
    }
    public function create()
    {
        return new $this->obj_class();
    }

    public function get($id, $fields='*')
    {
        $id = intval($id);
        if ($id > 0) {
            $sql = 'SELECT '.$fields.' FROM '.$this->db_table.' WHERE id='.$id;
        } else {
            return false;
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        $numrows = $this->db->getRowsNum($result);
        if ($numrows == 1) {
            $poll = new $this->obj_class();
            $poll->assignVars($this->db->fetchArray($result));
            return $poll;
        }
        return false;
    }

    public function insert($poll, $force = false)
    {
        if (strtolower(get_class($poll)) != strtolower($this->obj_class)) {
            return false;
        }
        if (!$poll->isDirty()) {
            return true;
        }
        if (!$poll->cleanVars()) {
            return false;
        }
        foreach ($poll->cleanVars as $k=>$v) {
            ${$k} = $v;
        }
        $myts = MyTextSanitizer::getInstance();
        if ($poll->isNew() || empty($id)) {
            $id  = $this->db->genId($this->db_table . '_xt_poll_id_seq');
            $sql = sprintf(
                'INSERT INTO %s (
				`lid`, `torrent`, `tracker`
				) VALUES (
				%u, %s, %s
				)',
                $this->db_table,
                $this->db->quoteString($lid),
                $this->db->quoteString($torrent),
                $this->db->quoteString($tracker)
            );
        } else {
            $sql = sprintf(
                'UPDATE %s SET
				`torrent` = %s,
				`tracker` = %s,
				 WHERE lid = %s',
                $this->db_table,
                $this->db->quoteString($torrent),
                $this->db->quoteString($tracker),
                $this->db->quoteString($lid)
            );
        }
        
        if (false != $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            $poll->setErrors('Could not store data in the database.<br>' . $this->db->error() . ' (' . $this->db->errno() . ')<br>' . $sql);
            return false;
        }
        if (empty($id)) {
            $id = $this->db->getInsertId();
        }
        $poll->assignVar('id', $id);
        return $id;
    }
    
    public function delete($criteria = null, $force = false)
    {
        if (strtolower(get_class($poll)) != strtolower($this->obj_class)) {
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

    public function getObjects($criteria = null, $fields='*', $id_as_key = false)
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
            $poll = new $this->obj_class();
            $poll->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] = $poll;
            } else {
                $ret[$myrow['id']] = $poll;
            }
            unset($poll);
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
    
    public function deleteTorrentPermissions($id, $mode = 'view')
    {
        global $xoopsModule;
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('gperm_itemid', $id));
        $criteria->add(new Criteria('gperm_modid', $xoopsModule->getVar('mid')));
        $criteria->add(new Criteria('gperm_name', $this->perm_name.$mode));
        if ($old_perms = $this->perm_handler->getObjects($criteria)) {
            foreach ($old_perms as $p) {
                $this->perm_handler->delete($p);
            }
        }
        return true;
    }
    
    public function insertTorrentPermissions($id, $group_ids, $mode = 'view')
    {
        global $xoopsModule;
        foreach ($group_ids as $id) {
            $perm = $this->perm_handler->create();
            $perm->setVar('gperm_name', $this->perm_name.$mode);
            $perm->setVar('gperm_itemid', $id);
            $perm->setVar('gperm_groupid', $id);
            $perm->setVar('gperm_modid', $xoopsModule->getVar('mid'));
            $this->perm_handler->insert($perm);
            $ii++;
        }
        return 'Permission ' . $this->perm_name . $mode . " set $ii times for " . _C_ADMINTITLE . ' Record ID ' . $id;
    }
    
    public function getPermittedTorrents($poll, $mode = 'view')
    {
        global $xoopsUser, $xoopsModule;
        $ret=false;
        if (isset($poll)) {
            $ret      = [];
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('gperm_itemid', $poll->getVar('lid'), '='), 'AND');
            $criteria->add(new Criteria('gperm_modid', $xoopsModule->getVar('mid'), '='), 'AND');
            $criteria->add(new Criteria('gperm_name', $this->perm_name.$mode, '='), 'AND');

            $gtObjperm = $this->perm_handler->getObjects($criteria);
            $groups    = [];
            
            foreach ($gtObjperm as $v) {
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
            if ($poll = $this->getObjects($criteria, 'home_list')) {
                $ret = [];
                foreach ($poll as $f) {
                    if (false != $this->perm_handler->checkRight($this->perm_name.$mode, $f->getVar('lid'), $groups, $xoopsModule->getVar('mid'))) {
                        $ret[] = $f;
                        unset($f);
                    }
                }
            }
        }
        return ret;
    }
    
    public function getSingleTorrentPermission($id, $mode = 'view')
    {
        global $xoopsUser, $xoopsModule;
        $groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : 3;
        if (false != $this->perm_handler->checkRight($this->perm_name.$mode, $id, $groups, $xoopsModule->getVar('mid'))) {
            return true;
        }
        return false;
    }
}
