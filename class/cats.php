<?php

class catResource extends XoopsObject
{
    public function __construct()
    {
        $this->XoopsObject();
        $this->initVar("cid", XOBJ_DTYPE_INT);
        $this->initVar("pid", XOBJ_DTYPE_INT);
        $this->initVar("title", XOBJ_DTYPE_TXTBOX);
        $this->initVar("imgurl", XOBJ_DTYPE_TXTBOX);
        $this->initVar("description", XOBJ_DTYPE_TXTBOX);
        $this->initVar("total", XOBJ_DTYPE_INT);
        $this->initVar("summary", XOBJ_DTYPE_TXTBOX);
        $this->initVar("spotlighttop", XOBJ_DTYPE_INT);
        $this->initVar("spotlighthis", XOBJ_DTYPE_INT);
        $this->initVar("nohtml", XOBJ_DTYPE_INT);
        $this->initVar("nosmiley", XOBJ_DTYPE_INT);
        $this->initVar("noxcodes", XOBJ_DTYPE_INT);
        $this->initVar("noimages", XOBJ_DTYPE_INT);
        $this->initVar("nobreak", XOBJ_DTYPE_INT);
        $this->initVar("weight", XOBJ_DTYPE_INT);
    }
}

class XtorrentCatHandler extends XoopsObjectHandler
{
    public $db;
    public $db_table;
    public $perm_name = 'xtorrent_cat_';
    public $obj_class = 'catResource';

    public function __construct($db)
    {
        if (!isset($db)&&!empty($db)) {
            $this->db = $db;
        } else {
            global $xoopsDB;
            $this->db = $xoopsDB;
        }
        $this->db_table = $this->db->prefix('xtorrent_cat');
        $this->perm_handler = xoops_gethandler('groupperm');
    }
    
    public function getInstance($db)
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new xtorrentcatHandler($db);
        }
        return $instance;
    }
    public function create()
    {
        return new $this->obj_class();
    }

    public function get($cid, $fields='*')
    {
        $cid = intval($cid);
        if ($cid > 0) {
            $sql = 'SELECT '.$fields.' FROM '.$this->db_table.' WHERE cid ='.$cid;
        } else {
            return false;
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        $numrows = $this->db->getRowsNum($result);
        if ($numrows == 1) {
            $cat = new $this->obj_class();
            $cat->assignVars($this->db->fetchArray($result));
            return $cat;
        }
        return false;
    }

    public function insert(&$cat, $force = false)
    {
        if (strtolower(get_class($cat)) != strtolower($this->obj_class)) {
            return false;
        }
        if (!$cat->isDirty()) {
            return true;
        }
        if (!$cat->cleanVars()) {
            return false;
        }
        foreach ($cat->cleanVars as $k=>$v) {
            ${$k} = $v;
        }
        $myts = MyTextSanitizer::getInstance();
        if ($cat->isNew() || empty($cid)) {
            $cid = $this->db->genId($this->db_table."_xt_cat_id_seq");
            $sql = sprintf(
                "INSERT INTO %s (
				`cid`, `pid`, `title`, `imgurl`, `description`, `total`, `summary`, `spotlighttop`, `spotlighthis`, `nohtml`, `nosmiley`, `noxcodes`, `noimages`, `nobreak`, `weight`
				) VALUES (
				%u, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s
				)",
                $this->db_table,
                $this->db->quoteString($cid),
                $this->db->quoteString($pid),
                $this->db->quoteString($myts->addslashes($title)),
                $this->db->quoteString($myts->addslashes($imgurl)),
                $this->db->quoteString($myts->addslashes($description)),
                $this->db->quoteString($total),
                $this->db->quoteString($myts->addslashes($summary)),
                $this->db->quoteString($spotlighttop),
                $this->db->quoteString($spotlighthis),
                $this->db->quoteString($nohtml),
                $this->db->quoteString($nosmiley),
                $this->db->quoteString($noxcodes),
                $this->db->quoteString($noimages),
                $this->db->quoteString($nobreak),
                $this->db->quoteString($weight)
            );
        } else {
            $sql = sprintf(
                "UPDATE %s SET
 				 `weight` = %s,
				 `pid` = %s,
				 `title` = %s,
				 `imgurl` = %s,
				 `description` = %s,
				 `total` = %s,
				 `summary` = %s,
				 `spotlighttop` = %s,
				 `spotlighthis` = %s,
				 `nohtml` = %s,
				 `nosmiley` = %s,
				 `noxcodes` = %s,
				 `noimages` = %s,
				 `nobreak` = %s,
				  WHERE `cid` = %s",
                $this->db_table,
                $this->db->quoteString($weight),
                $this->db->quoteString($pid),
                $this->db->quoteString($myts->addslashes($title)),
                $this->db->quoteString($myts->addslashes($imgurl)),
                $this->db->quoteString($myts->addslashes($description)),
                $this->db->quoteString($total),
                $this->db->quoteString($myts->addslashes($summary)),
                $this->db->quoteString($spotlighttop),
                $this->db->quoteString($spotlighthis),
                $this->db->quoteString($nohtml),
                $this->db->quoteString($nosmiley),
                $this->db->quoteString($noxcodes),
                $this->db->quoteString($noimages),
                $this->db->quoteString($nobreak),
                $this->db->quoteString($cid)
            );
        }
        
        if (false != $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            $cat->setErrors("Could not store data in the database.<br />".$this->db->error().' ('.$this->db->errno().')<br />'.$sql);
            return false;
        }
        if (empty($cid)) {
            $cid = $this->db->getInsertId();
        }
        $cat->assignVar('id', $cid);
        return $cid;
    }
    
    public function delete($criteria = null, $force = false)
    {
        if (strtolower(get_class($cat)) != strtolower($this->obj_class)) {
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

    public function getObjects($criteria = null, $fields='*', $cid_as_key = false)
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
            $cat = new $this->obj_class();
            $cat->assignVars($myrow);
            if (!$cid_as_key) {
                $ret[] = $cat;
            } else {
                $ret[$myrow['id']] = $cat;
            }
            unset($cat);
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
    
    public function deleteTorrentPermissions($cid, $mode = "view")
    {
        global $xoopsModule;
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('gperm_itemid', $cid));
        $criteria->add(new Criteria('gperm_modid', $xoopsModule->getVar('mid')));
        $criteria->add(new Criteria('gperm_name', $this->perm_name.$mode));
        if ($old_perms =& $this->perm_handler->getObjects($criteria)) {
            foreach ($old_perms as $p) {
                $this->perm_handler->delete($p);
            }
        }
        return true;
    }
    
    public function insertTorrentPermissions($cid, $group_ids, $mode = "view")
    {
        global $xoopsModule;
        foreach ($group_ids as $cid) {
            $perm = $this->perm_handler->create();
            $perm->setVar('gperm_name', $this->perm_name.$mode);
            $perm->setVar('gperm_itemid', $cid);
            $perm->setVar('gperm_groupid', $cid);
            $perm->setVar('gperm_modid', $xoopsModule->getVar('mid'));
            $this->perm_handler->insert($perm);
            $ii++;
        }
        return "Permission ".$this->perm_name.$mode." set $ii times for "._C_ADMINTITLE." Record ID ".$cid;
    }
    
    public function getPermittedTorrents($cat, $mode = "view")
    {
        global $xoopsUser, $xoopsModule;
        $ret=false;
        if (isset($cat)) {
            $ret      = [];
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('gperm_itemid', $cat->getVar('cid'), '='), 'AND');
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
            if ($cat = $this->getObjects($criteria, 'home_list')) {
                $ret   = [];
                foreach ($cat as $f) {
                    if (false != $this->perm_handler->checkRight($this->perm_name.$mode, $f->getVar('cid'), $groups, $xoopsModule->getVar('mid'))) {
                        $ret[] = $f;
                        unset($f);
                    }
                }
            }
        }
        return ret;
    }
    
    public function getSingleTorrentPermission($cid, $mode = "view")
    {
        global $xoopsUser, $xoopsModule;
        $groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : 3;
        if (false != $this->perm_handler->checkRight($this->perm_name.$mode, $cid, $groups, $xoopsModule->getVar('mid'))) {
            return true;
        }
        return false;
    }
}
