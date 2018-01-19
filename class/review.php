<?php

class reviewResource extends XoopsObject
{
    public function __construct()
    {
        $this->XoopsObject();
        $this->initVar("reviewid", XOBJ_DTYPE_INT);
        $this->initVar("lid", XOBJ_DTYPE_INT);
        $this->initVar("title", XOBJ_DTYPE_TXTBOX);
        $this->initVar("review", XOBJ_DTYPE_TXTBOX);
        $this->initVar("submit", XOBJ_DTYPE_INT);
        $this->initVar("date", XOBJ_DTYPE_INT);
        $this->initVar("uid", XOBJ_DTYPE_INT);
        $this->initVar("rated", XOBJ_DTYPE_INT);
    }
}

class XtorrentReviewHandler extends XoopsObjectHandler
{
    public $db;
    public $db_table;
    public $perm_name = 'xtorrent_review_';
    public $obj_class = 'reviewResource';

    public function __construct($db)
    {
        if (!isset($db)&&!empty($db)) {
            $this->db = $db;
        } else {
            global $xoopsDB;
            $this->db = $xoopsDB;
        }
        $this->db_table = $this->db->prefix('xtorrent_reviews');
        $this->perm_handler = xoops_gethandler('groupperm');
    }
    
    public function getInstance($db)
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new xtorrentreviewHandler($db);
        }
        return $instance;
    }
    public function create()
    {
        return new $this->obj_class();
    }

    public function get($reviewid, $fields='*')
    {
        $reviewid = intval($reviewid);
        if ($reviewid > 0) {
            $sql = 'SELECT '.$fields.' FROM '.$this->db_table.' WHERE reviewid ='.$reviewid;
        } else {
            return false;
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        $numrows = $this->db->getRowsNum($result);
        if ($numrows == 1) {
            $review = new $this->obj_class();
            $review->assignVars($this->db->fetchArray($result));
            return $review;
        }
        return false;
    }

    public function insert($review, $force = false)
    {
        global $myts;
        if (strtolower(get_class($review)) != strtolower($this->obj_class)) {
            return false;
        }
        if (!$review->isDirty()) {
            return true;
        }
        if (!$review->cleanVars()) {
            return false;
        }
        foreach ($review->cleanVars as $k=>$v) {
            ${$k} = $v;
        }
        $myts = MyTextSanitizer::getInstance();
        if ($review->isNew() || empty($reviewid)) {
            $reviewid = $this->db->genId($this->db_table."_xt_review_id_seq");
            $sql = sprintf(
                "INSERT INTO %s (
				`reviewid`, `lid`, `title`, `review`, `submit`, `date`, `uid`, `rated`
				) VALUES (
				%u, %s, %s, %s, %s, %s, %s, %s
				)",
                $this->db_table,
                $this->db->quoteString($reviewid),
                $this->db->quoteString($lid),
                $this->db->quoteString($myts->addslashes($title)),
                $this->db->quoteString($myts->addslashes($review)),
                $this->db->quoteString($submit),
                $this->db->quoteString($date),
                $this->db->quoteString($uid),
                $this->db->quoteString($rated)
            );
        } else {
            $sql = sprintf(
                "UPDATE %s SET
				`lid` = %s,
				`title` = %s,
				`review` = %s,
				`submit` = %s,
				`date` = %s,
				`uid` = %s,
				`rated` = %s WHERE `reviewid` = %s",
                $this->db_table,
                $this->db->quoteString($lid),
                $this->db->quoteString($myts->addslashes($title)),
                $this->db->quoteString($myts->addslashes($review)),
                $this->db->quoteString($submit),
                $this->db->quoteString($date),
                $this->db->quoteString($uid),
                $this->db->quoteString($rated),
                $this->db->quoteString($reviewid)
            );
        }
        
        if (false != $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            $review->setErrors("Could not store data in the database.<br />".$this->db->error().' ('.$this->db->errno().')<br />'.$sql);
            return false;
        }
        if (empty($reviewid)) {
            $reviewid = $this->db->getInsertId();
        }
        $review->assignVar('id', $reviewid);
        return $reviewid;
    }
    
    public function delete($criteria = null, $force = false)
    {
        if (strtolower(get_class($review)) != strtolower($this->obj_class)) {
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

    public function getObjects($criteria = null, $fields='*', $reviewid_as_key = false)
    {
        $ret = [];
        $limit = $start = 0;
        $sql = 'SELECT '.$fields.' FROM '.$this->db_table;
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
            $review = new $this->obj_class();
            $review->assignVars($myrow);
            if (!$reviewid_as_key) {
                $ret[] = $review;
            } else {
                $ret[$myrow['id']] = $review;
            }
            unset($review);
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
    
    public function deleteTorrentPermissions($reviewid, $mode = "view")
    {
        global $xoopsModule;
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('gperm_itemid', $reviewid));
        $criteria->add(new Criteria('gperm_modid', $xoopsModule->getVar('mid')));
        $criteria->add(new Criteria('gperm_name', $this->perm_name.$mode));
        if ($old_perms = $this->perm_handler->getObjects($criteria)) {
            foreach ($old_perms as $p) {
                $this->perm_handler->delete($p);
            }
        }
        return true;
    }
    
    public function insertTorrentPermissions($reviewid, $group_ids, $mode = "view")
    {
        global $xoopsModule;
        foreach ($group_ids as $reviewid) {
            $perm =& $this->perm_handler->create();
            $perm->setVar('gperm_name', $this->perm_name.$mode);
            $perm->setVar('gperm_itemid', $reviewid);
            $perm->setVar('gperm_groupid', $reviewid);
            $perm->setVar('gperm_modid', $xoopsModule->getVar('mid'));
            $this->perm_handler->insert($perm);
            $ii++;
        }
        return "Permission ".$this->perm_name.$mode." set $ii times for "._C_ADMINTITLE." Record ID ".$reviewid;
    }
    
    public function getPermittedTorrents($review, $mode = "view")
    {
        global $xoopsUser, $xoopsModule;
        $ret=false;
        if (isset($review)) {
            $ret      = [];
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('gperm_itemid', $review->getVar('reviewid'), '='), 'AND');
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
            if ($review = $this->getObjects($criteria, 'home_list')) {
                $ret = [];
                foreach ($review as $f) {
                    if (false != $this->perm_handler->checkRight($this->perm_name.$mode, $f->getVar('reviewid'), $groups, $xoopsModule->getVar('mid'))) {
                        $ret[] = $f;
                        unset($f);
                    }
                }
            }
        }
        return ret;
    }
    
    public function getSingleTorrentPermission($reviewid, $mode = "view")
    {
        global $xoopsUser, $xoopsModule;
        $groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : 3;
        if (false != $this->perm_handler->checkRight($this->perm_name.$mode, $reviewid, $groups, $xoopsModule->getVar('mid'))) {
            return true;
        }
        return false;
    }
}
