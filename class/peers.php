<?php

class peersResource extends XoopsObject
{
    public function __construct()
    {
        $this->XoopsObject();
        $this->initVar("id", XOBJ_DTYPE_INT);
        $this->initVar("torrent", XOBJ_DTYPE_INT);
        $this->initVar("peer_id", XOBJ_DTYPE_INT);
        $this->initVar("basename_net", XOBJ_DTYPE_TXTBOX);
        $this->initVar("xml", XOBJ_DTYPE_TXTBOX);
        $this->initVar("port", XOBJ_DTYPE_INT);
        $this->initVar("uploaded", XOBJ_DTYPE_INT);
        $this->initVar("downloaded", XOBJ_DTYPE_INT);
        $this->initVar("to_go", XOBJ_DTYPE_INT);
        $this->initVar("seeder", XOBJ_DTYPE_TXTBOX);
        $this->initVar("started", XOBJ_DTYPE_TXTBOX);
        $this->initVar("datetime", XOBJ_DTYPE_TXTBOX);
        $this->initVar("last_action", XOBJ_DTYPE_TXTBOX);
        $this->initVar("connectable", XOBJ_DTYPE_TXTBOX);
        $this->initVar("userid", XOBJ_DTYPE_INT);
        $this->initVar("agent", XOBJ_DTYPE_TXTBOX);
        $this->initVar("finishedat", XOBJ_DTYPE_INT);
        $this->initVar("downloadoffset", XOBJ_DTYPE_INT);
        $this->initVar("uploadoffset", XOBJ_DTYPE_INT);
        $this->initVar("passkey", XOBJ_DTYPE_TXTBOX);
    }
}

class XtorrentPeersHandler extends XoopsObjectHandler
{
    public $db;
    public $db_table;
    public $perm_name = 'xtorrent_peers_';
    public $obj_class = 'peersResource';

    public function __construct($db)
    {
        if (!isset($db)&&!empty($db)) {
            $this->db = $db;
        } else {
            global $xoopsDB;
            $this->db = $xoopsDB;
        }
        $this->db_table = $this->db->prefix('xtorrent_peers');
        $this->perm_handler = xoops_gethandler('groupperm');
    }
    
    public function getInstance($db)
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new xtorrentpeersHandler($db);
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
            $peers = new $this->obj_class();
            $peers->assignVars($this->db->fetchArray($result));
            return $peers;
        }
        return false;
    }

    public function insert($peers, $force = false)
    {
        if (strtolower(get_class($peers)) != strtolower($this->obj_class)) {
            return false;
        }
        if (!$peers->isDirty()) {
            return true;
        }
        if (!$peers->cleanVars()) {
            return false;
        }
        foreach ($peers->cleanVars as $k=>$v) {
            ${$k} = $v;
        }
        $myts = MyTextSanitizer::getInstance();
        if ($peers->isNew() || empty($id)) {
            $id  = $this->db->genId($this->db_table."_xt_peers_id_seq");
            $sql = sprintf(
                "INSERT INTO %s (
				`id`, `torrent`, `peer_id`, `ip`, `basename_net`, `port`, `uploaded`, `downloaded`, `to_go`, `seeder`, `started`, `last_action`, `connectable`, `userid`, `agent`, `finishedat`, `downloadoffset`, `uploadoffset`, `passkey`
				) VALUES (
				%u, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s
				)",
                $this->db_table,
                $this->db->quoteString($id),
                $this->db->quoteString($torrent),
                $this->db->quoteString($peer_id),
                $this->db->quoteString($ip),
                $this->db->quoteString($basename_net),
                $this->db->quoteString($port),
                $this->db->quoteString($uploaded),
                $this->db->quoteString($downloaded),
                $this->db->quoteString($to_go),
                $this->db->quoteString($seeder),
                $this->db->quoteString($started),
                $this->db->quoteString($last_action),
                $this->db->quoteString($connectable),
                $this->db->quoteString($userid),
                $this->db->quoteString($agent),
                $this->db->quoteString($finishedat),
                $this->db->quoteString($downloadoffset),
                $this->db->quoteString($uploadoffset),
                $this->db->quoteString($passkey)
            );
        } else {
            $sql = sprintf(
                "UPDATE %s SET
				`torrent` = %s,
				`peer_id` = %s,
				`ip` = %s,
				`basename_net` = %s,
				`port` = %s,
				`uploaded` = %s,
				`downloaded` = %s,
				`to_go` = %s,
				`seeder` = %s,
				`started` = %s,
				`last_action` = %s,
				`connectable` = %s,
				`userid` = %s,
				`agent` = %s,
				`finishedat` = %s,
				`downloadoffset` = %s,
				`uploadoffset` = %s,
				`passkey` = %s WHERE id = %s",
                $this->db_table,
                $this->db->quoteString($torrent),
                $this->db->quoteString($peer_id),
                $this->db->quoteString($ip),
                $this->db->quoteString($basename_net),
                $this->db->quoteString($port),
                $this->db->quoteString($uploaded),
                $this->db->quoteString($downloaded),
                $this->db->quoteString($to_go),
                $this->db->quoteString($seeder),
                $this->db->quoteString($started),
                $this->db->quoteString($last_action),
                $this->db->quoteString($connectable),
                $this->db->quoteString($userid),
                $this->db->quoteString($agent),
                $this->db->quoteString($finishedat),
                $this->db->quoteString($downloadoffset),
                $this->db->quoteString($uploadoffset),
                $this->db->quoteString($passkey),
                $this->db->quoteString($id)
            );
        }
        
        if (false != $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            $peers->setErrors("Could not store data in the database.<br />".$this->db->error().' ('.$this->db->errno().')<br />'.$sql);
            return false;
        }
        if (empty($id)) {
            $id = $this->db->getInsertId();
        }
        $peers->assignVar('id', $id);
        return $id;
    }
    
    public function delete($criteria = null, $force = false)
    {
        if (strtolower(get_class($peers)) != strtolower($this->obj_class)) {
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
            $peers = new $this->obj_class();
            $peers->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] = $peers;
            } else {
                $ret[$myrow['id']] = $peers;
            }
            unset($peers);
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
    
    public function getPermittedTorrents($peers, $mode = "view")
    {
        global $xoopsUser, $xoopsModule;
        $ret=false;
        if (isset($peers)) {
            $ret      = [];
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('gperm_itemid', $peers->getVar('id'), '='), 'AND');
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
            if ($peers = $this->getObjects($criteria, 'home_list')) {
                $ret = [];
                foreach ($peers as $f) {
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
