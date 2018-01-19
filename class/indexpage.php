<?php

class indexpageResource extends XoopsObject
{
    public function __construct()
    {
        $this->XoopsObject();
        $this->initVar("id", XOBJ_DTYPE_INT);
        $this->initVar("lid", XOBJ_DTYPE_INT);
        $this->initVar("cid", XOBJ_DTYPE_INT);
        $this->initVar("indeximage", XOBJ_DTYPE_TXTBOX);
        $this->initVar("indexheading", XOBJ_DTYPE_TXTBOX);
        $this->initVar("indexheader", XOBJ_DTYPE_TXTBOX);
        $this->initVar("indexfooter", XOBJ_DTYPE_TXTBOX);
        $this->initVar("nohtml", XOBJ_DTYPE_INT);
        $this->initVar("nosmiley", XOBJ_DTYPE_INT);
        $this->initVar("noxcodes", XOBJ_DTYPE_INT);
        $this->initVar("noimages", XOBJ_DTYPE_INT);
        $this->initVar("nobreak", XOBJ_DTYPE_INT);
        $this->initVar("indexheaderalign", XOBJ_DTYPE_TXTBOX);
        $this->initVar("indexfooteralign", XOBJ_DTYPE_TXTBOX);
    }
}

class XtorrentIndexpageHandler extends XoopsObjectHandler
{
    public $db;
    public $db_table;
    public $perm_name = 'xtorrent_indexpage_';
    public $obj_class = 'indexpageResource';

    public function __construct($db)
    {
        if (!isset($db)&&!empty($db)) {
            $this->db = $db;
        } else {
            global $xoopsDB;
            $this->db = $xoopsDB;
        }
        $this->db_table = $this->db->prefix('xtorrent_indexpage');
        $this->perm_handler = xoops_gethandler('groupperm');
    }
    
    public function getInstance($db)
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new xtorrentindexpageHandler($db);
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
            $indexpage = new $this->obj_class();
            $indexpage->assignVars($this->db->fetchArray($result));
            return $indexpage;
        }
        return false;
    }

    public function insert($indexpage, $force = false)
    {
        if (strtolower(get_class($indexpage)) != strtolower($this->obj_class)) {
            return false;
        }
        if (!$indexpage->isDirty()) {
            return true;
        }
        if (!$indexpage->cleanVars()) {
            return false;
        }
        foreach ($indexpage->cleanVars as $k=>$v) {
            ${$k} = $v;
        }
        $myts = MyTextSanitizer::getInstance();
        if ($indexpage->isNew() || empty($id)) {
            $id  = $this->db->genId($this->db_table."_xt_indexpage_id_seq");
            $sql = sprintf(
                "INSERT INTO %s (
				`id`, `lid`, `cid`, `indeximage`, `indexheading`, `indexheader`, `indexfooter`, `nohtml`, `nosmiley`, `noxcodes`, `noimages`, `nobreak`, `indexheaderalign`, `indexfooteralign`
				) VALUES (
				%u, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s
				)",
                $this->db_table,
                $this->db->quoteString($id),
                $this->db->quoteString($lid),
                $this->db->quoteString($cid),
                $this->db->quoteString($myts->addslashes($indeximage)),
                $this->db->quoteString($myts->addslashes($indexheading)),
                $this->db->quoteString($myts->addslashes($indexheader)),
                $this->db->quoteString($myts->addslashes($indexfooter)),
                $this->db->quoteString($downloaded),
                $this->db->quoteString($nohtml),
                $this->db->quoteString($nosmiley),
                $this->db->quoteString($noxcodes),
                $this->db->quoteString($noimages),
                $this->db->quoteString($nobreak),
                $this->db->quoteString($indexheaderalign),
                $this->db->quoteString($indexfooteralign)
            );
        } else {
            $sql = sprintf(
                "UPDATE %s SET
					`lid` = %s,
					`cid` = %s,
					`indeximage` = %s,
					`indexheading` = %s,
					`indexheader` = %s,
					`indexfooter` = %s,
					`nohtml` = %s,
					`nosmiley` = %s,
					`noxcodes` = %s,
					`noimages` = %s,
					`nobreak` = %s,
					`indexheaderalign` = %s,
					`indexfooteralign` = %s WHERE id = %s",
                $this->db_table,
                $this->db->quoteString($lid),
                $this->db->quoteString($cid),
                $this->db->quoteString($myts->addslashes($indeximage)),
                $this->db->quoteString($myts->addslashes($indexheading)),
                $this->db->quoteString($myts->addslashes($indexheader)),
                $this->db->quoteString($myts->addslashes($indexfooter)),
                $this->db->quoteString($downloaded),
                $this->db->quoteString($nohtml),
                $this->db->quoteString($nosmiley),
                $this->db->quoteString($noxcodes),
                $this->db->quoteString($noimages),
                $this->db->quoteString($nobreak),
                $this->db->quoteString($indexheaderalign),
                $this->db->quoteString($indexfooteralign),
                $this->db->quoteString($id)
            );
        }
        
        if (false != $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            $indexpage->setErrors("Could not store data in the database.<br>".$this->db->error().' ('.$this->db->errno().')<br>'.$sql);
            return false;
        }
        if (empty($id)) {
            $id = $this->db->getInsertId();
        }
        $indexpage->assignVar('id', $id);
        return $id;
    }
    
    public function delete($criteria = null, $force = false)
    {
        if (strtolower(get_class($indexpage)) != strtolower($this->obj_class)) {
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
            $indexpage = new $this->obj_class();
            $indexpage->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] = $indexpage;
            } else {
                $ret[$myrow['id']] = $indexpage;
            }
            unset($indexpage);
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
    
    public function deleteTorrentPermissions($id, $mode = "view")
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
    
    public function insertTorrentPermissions($id, $group_ids, $mode = "view")
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
        return "Permission ".$this->perm_name.$mode." set $ii times for "._C_ADMINTITLE." Record ID ".$id;
    }
    
    public function getPermittedTorrents($indexpage, $mode = "view")
    {
        global $xoopsUser, $xoopsModule;
        $ret = false;
        if (isset($indexpage)) {
            $ret      = [];
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('gperm_itemid', $indexpage->getVar('id'), '='), 'AND');
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
            if ($indexpage = $this->getObjects($criteria, 'home_list')) {
                $ret = [];
                foreach ($indexpage as $f) {
                    if (false != $this->perm_handler->checkRight($this->perm_name.$mode, $f->getVar('id'), $groups, $xoopsModule->getVar('mid'))) {
                        $ret[] = $f;
                        unset($f);
                    }
                }
            }
        }
        return ret;
    }
    
    public function getSingleTorrentPermission($id, $mode = "view")
    {
        global $xoopsUser, $xoopsModule;
        $groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : 3;
        if (false != $this->perm_handler->checkRight($this->perm_name.$mode, $id, $groups, $xoopsModule->getVar('mid'))) {
            return true;
        }
        return false;
    }
}
