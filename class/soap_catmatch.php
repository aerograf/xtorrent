<?php

class soap_catmatchResource extends XoopsObject
{
    public function __construct()
    {
        $this->XoopsObject();
        $this->initVar('id', XOBJ_DTYPE_INT);
        $this->initVar('cid', XOBJ_DTYPE_INT);
        $this->initVar('scid', XOBJ_DTYPE_INT);
        $this->initVar('stitle', XOBJ_DTYPE_TXTBOX);
        $this->initVar('sdescription', XOBJ_DTYPE_TXTBOX);
        $this->initVar('skey', XOBJ_DTYPE_TXTBOX);
        $this->initVar('lastimport', XOBJ_DTYPE_INT);
        $this->initVar('auto_approval', XOBJ_DTYPE_INT);
        $this->initVar('server', XOBJ_DTYPE_TXTBOX);
        $this->initVar('username', XOBJ_DTYPE_TXTBOX);
    }
}

class XtorrentSoap_catmatchHandler extends XoopsObjectHandler
{
    public $db;
    public $db_table;
    public $perm_name = 'xtorrent_soap_catmatch_';
    public $obj_class = 'soap_catmatchResource';

    public function __construct($db)
    {
        if (!isset($db)&&!empty($db)) {
            $this->db = $db;
        } else {
            global $xoopsDB;
            $this->db = $xoopsDB;
        }
        $this->db_table = $this->db->prefix('xtorrent_soap_catmatch');
        $this->perm_handler = xoops_gethandler('groupperm');
    }
    
    public function getInstance($db)
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new xtorrentsoap_catmatchHandler($db);
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
        if (1 == $numrows) {
            $soap_catmatch = new $this->obj_class();
            $soap_catmatch->assignVars($this->db->fetchArray($result));
            return $soap_catmatch;
        }
        return false;
    }

    public function insert($soap_catmatch, $force = false)
    {
        if (strtolower(get_class($soap_catmatch)) != strtolower($this->obj_class)) {
            return false;
        }
        if (!$soap_catmatch->isDirty()) {
            return true;
        }
        if (!$soap_catmatch->cleanVars()) {
            return false;
        }
        foreach ($soap_catmatch->cleanVars as $k=>$v) {
            ${$k} = $v;
        }
        $myts = MyTextSanitizer::getInstance();
        if ($soap_catmatch->isNew() || empty($id)) {
            $id = $this->db->genId($this->db_table . '_xt_soap_catmatch_id_seq');
            $sql = sprintf(
                'INSERT INTO %s (
				`id`, `cid`, `scid`, `stitle`, `sdescription`, `skey`, `lastimport`, `server`, `username`
				) VALUES (
				%u, %s, %s, %s, %s, %s, %s, %s, %s
				)',
                $this->db_table,
                $this->db->quoteString($id),
                $this->db->quoteString($cid),
                $this->db->quoteString($scid),
                $this->db->quoteString($myts->addslashes($stitle)),
                $this->db->quoteString($myts->addslashes($sdecription)),
                $this->db->quoteString($skey),
                $this->db->quoteString($lastimport),
                $this->db->quoteString($server),
                $this->db->quoteString($username)
            );
        } else {
            $sql = sprintf(
                'UPDATE %s SET
				`cid` = %s,
				`scid` = %s,
				`stitle` = %s,
				`sdecription` = %s,
				`skey` = %s,
				`lastimport` = %s,
				`server` = %s,
				`username` = %s WHERE id = %s',
                $this->db_table,
                $this->db->quoteString($cid),
                $this->db->quoteString($scid),
                $this->db->quoteString($myts->addslashes($stitle)),
                $this->db->quoteString($myts->addslashes($sdecription)),
                $this->db->quoteString($skey),
                $this->db->quoteString($lastimport),
                $this->db->quoteString($id)
            );
        }
        
        if (false != $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            $soap_catmatch->setErrors('Could not store data in the database.<br />' . $this->db->error() . ' (' . $this->db->errno() . ')<br />' . $sql);
            return false;
        }
        if (empty($id)) {
            $id = $this->db->getInsertId();
        }
        $soap_catmatch->assignVar('id', $id);
        return $id;
    }
    
    public function delete($criteria = null, $force = false)
    {
        if (strtolower(get_class($soap_catmatch)) != strtolower($this->obj_class)) {
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
            $soap_catmatch = new $this->obj_class();
            $soap_catmatch->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] = $soap_catmatch;
            } else {
                $ret[$myrow['id']] = $soap_catmatch;
            }
            unset($soap_catmatch);
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
    
    public function getPermittedTorrents($soap_catmatch, $mode = 'view')
    {
        global $xoopsUser, $xoopsModule;
        $ret = false;
        if (isset($soap_catmatch)) {
            $ret      = [];
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('gperm_itemid', $soap_catmatch->getVar('id'), '='), 'AND');
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
            if ($soap_catmatch = $this->getObjects($criteria, 'home_list')) {
                $ret = [];
                foreach ($soap_catmatch as $f) {
                    if (false != $this->perm_handler->checkRight($this->perm_name.$mode, $f->getVar('id'), $groups, $xoopsModule->getVar('mid'))) {
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
