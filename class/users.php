<?php

class usersResource extends XoopsObject
{
    public function __construct()
    {
        $this->XoopsObject();
        $this->initVar('id', XOBJ_DTYPE_INT);
        $this->initVar('uid', XOBJ_DTYPE_INT);
        $this->initVar('lid', XOBJ_DTYPE_INT);
        $this->initVar('username', XOBJ_DTYPE_TXTBOX);
        $this->initVar('old_password', XOBJ_DTYPE_TXTBOX);
        $this->initVar('passhash', XOBJ_DTYPE_TXTBOX);
        $this->initVar('secret', XOBJ_DTYPE_TXTBOX);
        $this->initVar('uploaded', XOBJ_DTYPE_INT);
        $this->initVar('downloaded', XOBJ_DTYPE_INT);
        $this->initVar('enabled', XOBJ_DTYPE_TXTBOX);
        $this->initVar('last_access', XOBJ_DTYPE_INT);
        $this->initVar('passkey', XOBJ_DTYPE_TXTBOX);
    }
}

class XtorrentUsersHandler extends XoopsObjectHandler
{
    public $db;
    public $db_table;
    public $perm_name = 'xtorrent_users_';
    public $obj_class = 'usersResource';

    public function __construct($db)
    {
        if (!isset($db) && !empty($db)) {
            $this->db = $db;
        } else {
            global $xoopsDB;
            $this->db = $xoopsDB;
        }
        $this->db_table    = $this->db->prefix('xtorrent_users');
        $this->permHandler = xoops_getHandler('groupperm');
    }

    public function getInstance($db)
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new xtorrentusersHandler($db);
        }
        return $instance;
    }

    public function create()
    {
        return new $this->obj_class();
    }

    public function get($id, $fields = '*')
    {
        $id = (int)$id;
        if ($id > 0) {
            $sql = 'SELECT ' . $fields . ' FROM ' . $this->db_table . ' WHERE id=' . $id;
        } else {
            return false;
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        $numrows = $this->db->getRowsNum($result);
        if (1 == $numrows) {
            $users = new $this->obj_class();
            $users->assignVars($this->db->fetchArray($result));
            return $users;
        }
        return false;
    }

    public function insert($users, $force = false)
    {
        if (strtolower(get_class($users)) != strtolower($this->obj_class)) {
            return false;
        }
        if (!$users->isDirty()) {
            return true;
        }
        if (!$users->cleanVars()) {
            return false;
        }
        foreach ($users->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        $myts = MyTextSanitizer::getInstance();
        if ($users->isNew() || empty($id)) {
            $id  = $this->db->genId($this->db_table . '_xt_users_id_seq');
            $sql = sprintf('INSERT INTO %s (
				`id`, `uid`, `lid`, `username`, `old_password`, `passhash`, `secret`, `uploaded`, `downloaded`, `enabled`, `last_access`, `passkey`
				) VALUES (
				%u, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s,
				)', $this->db_table, $this->db->quoteString($id), $this->db->quoteString($uid), $this->db->quoteString($lid), $this->db->quoteString($username), $this->db->quoteString($old_password), $this->db->quoteString($passhash), $this->db->quoteString($secret),
                           $this->db->quoteString($uploaded), $this->db->quoteString($downloaded), $this->db->quoteString($last_access), $this->db->quoteString($passkey));
        } else {
            $sql = sprintf('UPDATE %s SET
				`uid` = %s,
				`lid` = %s,
				`username` = %s,
				`old_password` = %s,
				`passhash` = %s,
				`secret` = %s,
				`uploaded` = %s,
				`downloaded` = %s,
				`enabled` = %s,
				`last_access` = %s,
				`passhash` = %s,
				WHERE id = %s', $this->db_table, $this->db->quoteString($uid), $this->db->quoteString($lid), $this->db->quoteString($username), $this->db->quoteString($old_password), $this->db->quoteString($passhash), $this->db->quoteString($secret), $this->db->quoteString($uploaded),
                           $this->db->quoteString($downloaded), $this->db->quoteString($last_access), $this->db->quoteString($passkey), $this->db->quoteString($id));
        }

        if (false !== $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            $users->setErrors('Could not store data in the database.<br />' . $this->db->error() . ' (' . $this->db->errno() . ')<br />' . $sql);
            return false;
        }
        if (empty($id)) {
            $id = $this->db->getInsertId();
        }
        $users->assignVar('id', $id);
        return $id;
    }

    public function delete($criteria = null, $force = false)
    {
        if (strtolower(get_class($users)) != strtolower($this->obj_class)) {
            return false;
        }
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql = 'DELETE FROM ' . $this->db_table . ' ' . $criteria->renderWhere() . '';
        }
        if (false !== $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        return true;
    }

    public function getObjects($criteria = null, $fields = '*', $id_as_key = false)
    {
        $ret   = [];
        $limit = $start = 0;
        $sql   = 'SELECT ' . $fields . ' FROM ' . $this->db_table;
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
            if ('' != $criteria->getSort()) {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return false;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $users = new $this->obj_class();
            $users->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] = $users;
            } else {
                $ret[$myrow['id']] = $users;
            }
            unset($users);
        }
        return count($ret) > 0 ? $ret : false;
    }

    public function getCount($criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->db_table;
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
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
        $sql = 'DELETE FROM ' . $this->db_table;
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
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
        $criteria->add(new Criteria('gperm_name', $this->perm_name . $mode));
        if ($old_perms = $this->permHandler->getObjects($criteria)) {
            foreach ($old_perms as $p) {
                $this->permHandler->delete($p);
            }
        }
        return true;
    }

    public function insertTorrentPermissions($id, $group_ids, $mode = 'view')
    {
        global $xoopsModule;
        foreach ($group_ids as $id) {
            $perm = $this->permHandler->create();
            $perm->setVar('gperm_name', $this->perm_name . $mode);
            $perm->setVar('gperm_itemid', $id);
            $perm->setVar('gperm_groupid', $id);
            $perm->setVar('gperm_modid', $xoopsModule->getVar('mid'));
            $this->permHandler->insert($perm);
            $ii++;
        }
        return 'Permission ' . $this->perm_name . $mode . " set $ii times for " . _C_ADMINTITLE . ' Record ID ' . $id;
    }

    public function getPermittedTorrents($users, $mode = 'view')
    {
        global $xoopsUser, $xoopsModule;
        $ret = false;
        if (isset($users)) {
            $ret      = [];
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('gperm_itemid', $users->getVar('id'), '='), 'AND');
            $criteria->add(new Criteria('gperm_modid', $xoopsModule->getVar('mid'), '='), 'AND');
            $criteria->add(new Criteria('gperm_name', $this->perm_name . $mode, '='), 'AND');

            $gtObjperm = $this->permHandler->getObjects($criteria);
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
            if ($users = $this->getObjects($criteria, 'home_list')) {
                $ret = [];
                foreach ($users as $f) {
                    if (false !== $this->permHandler->checkRight($this->perm_name . $mode, $f->getVar('id'), $groups, $xoopsModule->getVar('mid'))) {
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
        if (false !== $this->permHandler->checkRight($this->perm_name . $mode, $id, $groups, $xoopsModule->getVar('mid'))) {
            return true;
        }
        return false;
    }
}
