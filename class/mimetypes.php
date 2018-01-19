<?php

class mimetypesResource extends XoopsObject
{
    public function __construct()
    {
        $this->XoopsObject();
        $this->initVar('mime_id', XOBJ_DTYPE_INT);
        $this->initVar('mime_ext', XOBJ_DTYPE_TXTBOX);
        $this->initVar('mime_types', XOBJ_DTYPE_TXTBOX);
        $this->initVar('mime_name', XOBJ_DTYPE_TXTBOX);
        $this->initVar('mime_admin', XOBJ_DTYPE_INT);
        $this->initVar('mime_user', XOBJ_DTYPE_INT);
    }
}

class XtorrentMimetypesHandler extends XoopsObjectHandler
{
    public $db;
    public $db_table;
    public $perm_name = 'xtorrent_mimetypes_';
    public $obj_class = 'mimetypesResource';

    public function __construct($db)
    {
        if (!isset($db) && !empty($db)) {
            $this->db = $db;
        } else {
            global $xoopsDB;
            $this->db = $xoopsDB;
        }
        $this->db_table    = $this->db->prefix('xtorrent_mimetypes');
        $this->permHandler = xoops_getHandler('groupperm');
    }

    public function getInstance($db)
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new xtorrentmimetypesHandler($db);
        }
        return $instance;
    }

    public function create()
    {
        return new $this->obj_class();
    }

    public function get($mime_id, $fields = '*')
    {
        $mime_id = (int)$mime_id;
        if ($mime_id > 0) {
            $sql = 'SELECT ' . $fields . ' FROM ' . $this->db_table . ' WHERE `mime_id` =' . $mime_id;
        } else {
            return false;
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        $numrows = $this->db->getRowsNum($result);
        if (1 == $numrows) {
            $mimetypes = new $this->obj_class();
            $mimetypes->assignVars($this->db->fetchArray($result));
            return $mimetypes;
        }
        return false;
    }

    public function insert($mimetypes, $force = false)
    {
        if (strtolower(get_class($mimetypes)) != strtolower($this->obj_class)) {
            return false;
        }
        if (!$mimetypes->isDirty()) {
            return true;
        }
        if (!$mimetypes->cleanVars()) {
            return false;
        }
        foreach ($mimetypes->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        $myts = MyTextSanitizer::getInstance();
        if ($mimetypes->isNew() || empty($mime_id)) {
            $mime_id = $this->db->genId($this->db_table . '_xt_mimetypes_id_seq');
            $sql     = sprintf('INSERT INTO %s (
				`mime_id`, `mime_ext`, `mime_types`, `mime_name`, `mime_admin`, `mime_user`
				) VALUES (
				%u, %s, %s, %s, %s, %s
				)', $this->db_table, $this->db->quoteString($mime_id), $this->db->quoteString($mime_ext), $this->db->quoteString($mime_types), $this->db->quoteString($mime_name), $this->db->quoteString($mime_admin), $this->db->quoteString($mime_user));
        } else {
            $sql = sprintf('UPDATE %s SET
				`mime_ext` = %s,
				`mime_types` = %s,
				`mime_name` = %s,
				`mime_admin` = %s,
				`mime_user` = %s WHERE mime_id = %s', $this->db_table, $this->db->quoteString($mime_ext), $this->db->quoteString($mime_types), $this->db->quoteString($mime_name), $this->db->quoteString($mime_admin), $this->db->quoteString($mime_user), $this->db->quoteString($mime_id));
        }

        if (false !== $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            $mimetypes->setErrors('Could not store data in the database.<br />' . $this->db->error() . ' (' . $this->db->errno() . ')<br />' . $sql);
            return false;
        }
        if (empty($mime_id)) {
            $mime_id = $this->db->getInsertId();
        }
        $mimetypes->assignVar('mime_id', $mime_id);
        return $mime_id;
    }

    public function delete($criteria = null, $force = false)
    {
        if (strtolower(get_class($mimetypes)) != strtolower($this->obj_class)) {
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

    public function getObjects($criteria = null, $fields = '*', $mime_id_as_key = false)
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
            $mimetypes = new $this->obj_class();
            $mimetypes->assignVars($myrow);
            if (!$mime_id_as_key) {
                $ret[] = $mimetypes;
            } else {
                $ret[$myrow['mime_id']] = $mimetypes;
            }
            unset($mimetypes);
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

    public function deleteTorrentPermissions($mime_id, $mode = 'view')
    {
        global $xoopsModule;
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('gperm_itemid', $mime_id));
        $criteria->add(new Criteria('gperm_modid', $xoopsModule->getVar('mid')));
        $criteria->add(new Criteria('gperm_name', $this->perm_name . $mode));
        if ($old_perms = $this->permHandler->getObjects($criteria)) {
            foreach ($old_perms as $p) {
                $this->permHandler->delete($p);
            }
        }
        return true;
    }

    public function insertTorrentPermissions($mime_id, $group_ids, $mode = 'view')
    {
        global $xoopsModule;
        foreach ($group_ids as $mime_id) {
            $perm = $this->permHandler->create();
            $perm->setVar('gperm_name', $this->perm_name . $mode);
            $perm->setVar('gperm_itemid', $mime_id);
            $perm->setVar('gperm_groupid', $mime_id);
            $perm->setVar('gperm_modid', $xoopsModule->getVar('mid'));
            $this->permHandler->insert($perm);
            $ii++;
        }
        return 'Permission ' . $this->perm_name . $mode . " set $ii times for " . _C_ADMINTITLE . ' Record ID ' . $mime_id;
    }

    public function &getPermittedTorrents($mimetypes, $mode = 'view')
    {
        global $xoopsUser, $xoopsModule;
        $ret = false;
        if (isset($mimetypes)) {
            $ret      = [];
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('gperm_itemid', $mimetypes->getVar('mime_id'), '='), 'AND');
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
            if ($mimetypes =& $this->getObjects($criteria, 'home_list')) {
                $ret = [];
                foreach ($mimetypes as $f) {
                    if (false !== $this->permHandler->checkRight($this->perm_name . $mode, $f->getVar('mime_id'), $groups, $xoopsModule->getVar('mid'))) {
                        $ret[] = $f;
                        unset($f);
                    }
                }
            }
        }
        return ret;
    }

    public function getSingleTorrentPermission($mime_id, $mode = 'view')
    {
        global $xoopsUser, $xoopsModule;
        $groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : 3;
        if (false !== $this->permHandler->checkRight($this->perm_name . $mode, $mime_id, $groups, $xoopsModule->getVar('mid'))) {
            return true;
        }
        return false;
    }
}
