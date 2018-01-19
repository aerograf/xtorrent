<?php

class transactionsResource extends XoopsObject
{
    public function __construct()
    {
        $this->XoopsObject();
        $this->initVar('xtid', XOBJ_DTYPE_INT);
        $this->initVar('lid', XOBJ_DTYPE_INT);
        $this->initVar('uid', XOBJ_DTYPE_INT);
        $this->initVar('key', XOBJ_DTYPE_TXTBOX);
        $this->initVar('hist_xml', XOBJ_DTYPE_TXTBOX);
        $this->initVar('time', XOBJ_DTYPE_INT);
        $this->initVar('microtime', XOBJ_DTYPE_INT);
        $this->initVar('ip', XOBJ_DTYPE_TXTBOX);
        $this->initVar('hostname', XOBJ_DTYPE_TXTBOX);
    }
}

class XtorrentTransactionsHandler extends XoopsObjectHandler
{
    public $db;
    public $db_table;
    public $perm_name = 'xtorrent_transactions_';
    public $obj_class = 'transactionsResource';

    public function __construct($db)
    {
        if (!isset($db) && !empty($db)) {
            $this->db = $db;
        } else {
            global $xoopsDB;
            $this->db = $xoopsDB;
        }
        $this->db_table     = $this->db->prefix('xtorrent_transactions');
        $this->perm_handler = xoops_gethandler('groupperm');
    }

    public function getInstance($db)
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new xtorrenttransactionsHandler($db);
        }
        return $instance;
    }

    public function history2xml($array)
    {
        return $this->array2xml($array);
    }

    private function array2xml($buffer, $doctype = 'tracker_history')
    {
        $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $xml .= "<$doctype version=\"1.0\">\n";
        $count++;
        foreach ($buffer as $key => $val) {
            $xml .= str_repeat("\t", $count) . "<$doctype>\n";
            $count++;
            $xml .= $this->array2xml_sub($val, $key, $count);
            $count--;
            $xml .= str_repeat("\t", $count) . "</$doctype>\n";
        }
        $count--;
        $xml .= "</$doctype>\n";
        return $xml;
    }

    private function array2xml_sub($val, $key, $count)
    {
        $count++;
        if (!is_array($val)) {
            $xml .= str_repeat("\t", $count) . "<$key>" . utf8_encode($val) . "</$key>\n";
        } else {
            $count++;
            foreach ($val as $key => $value) {
                if (is_array($value)) {

                    $xml .= str_repeat("\t", $count) . "<$key>\n"
					$count++;
					$xml .= $this->array2xml_sub($value, $key, $count);
					$count--;
					$xml .= str_repeat("\t", $count) . "</$key>\n"
				} else {
                    $xml .= str_repeat("\t", $count) . "<$key>" . utf8_encode($value) . "</$key>\n";
                }
            }
            $count--;
        }
        $count--;
        return $xml;
    }

    public function create()
    {
        return new $this->obj_class();
    }

    public function get($xtid, $fields = '*')
    {
        $xtid = (int)$xtid;
        if ($xtid > 0) {
            $sql = 'SELECT ' . $fields . ' FROM ' . $this->db_table . ' WHERE id=' . $xtid;
        } else {
            return false;
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        $numrows = $this->db->getRowsNum($result);
        if (1 == $numrows) {
            $transactions = new $this->obj_class();
            $transactions->assignVars($this->db->fetchArray($result));
            return $transactions;
        }
        return false;
    }

    public function insert($transactions, $force = false)
    {
        if (strtolower(get_class($transactions)) != strtolower($this->obj_class)) {
            return false;
        }
        if (!$transactions->isDirty()) {
            return true;
        }
        if (!$transactions->cleanVars()) {
            return false;
        }
        foreach ($transactions->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        $myts = MyTextSanitizer::getInstance();
        if ($transactions->isNew() || empty($xtid)) {
            $xtid = $this->db->genId($this->db_table . '_xt_transactions_id_seq');
            $sql  = sprintf('INSERT INTO %s (
				`xtid`, `lid`, `uid`, `key`, `hist_xml`, `time`, `microtime`, `ip`, `hostname`
				) VALUES (
				%u, %s, %s, %s, AES_CRYPT(COMPRESS(%s),%s), %s, %s, %s, %s
				)', $this->db_table, $this->db->quoteString($xtid), $this->db->quoteString($lid), $this->db->quoteString($uid), $this->db->quoteString($key), $this->db->quoteString($myts->addslashes($hist_xml)), $this->db->quoteString($key), $this->db->quoteString($time),
                            $this->db->quoteString($microtime), $this->db->quoteString($ip), $this->db->quoteString($hostname));
        } else {
            $sql = sprintf('UPDATE %s SET
				`lid` = %s,
				`uid` = %s,
				`key` = %s,
				`hist_xml` = AES_CRYPT(COMPRESS(%s),%s),
				`time` = %s,
				`microtime` = %s,
				`ip` = %s,
				`hostname` = %s WHERE `xtid` = %s', $this->db_table, $this->db->quoteString($lid), $this->db->quoteString($uid), $this->db->quoteString($key), $this->db->quoteString($myts->addslashes($hist_xml)), $this->db->quoteString($key), $this->db->quoteString($time),
                           $this->db->quoteString($microtime), $this->db->quoteString($ip), $this->db->quoteString($hostname), $this->db->quoteString($xtid));
        }

        if (false != $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            $transactions->setErrors('Could not store data in the database.<br>' . $this->db->error() . ' (' . $this->db->errno() . ')<br>' . $sql);
            return false;
        }
        if (empty($xtid)) {
            $xtid = $this->db->getInsertId();
        }
        $transactions->assignVar('xtid', $xtid);
        return $xtid;
    }

    public function delete($criteria = null, $force = false)
    {
        if (strtolower(get_class($transactions)) != strtolower($this->obj_class)) {
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

    public function delete_scalar($limit = 20, $sort = 'id DESC')
    {
        if ($this->getCount > $limit) {
            $sql    = 'SELECT xtid as lowid FROM ' . $this->db->prefix('xtorrent_transactions') . " ORDER BY $sort LIMIT $limit";
            $result = $this->db->queryF($sql);
            while (list($lowid) = $this->db->fetchRow($result)) {
                $sql    = 'DELETE FROM ' . $this->db->prefix('xtorrent_transactions') . " WHERE xtid < '" . $lowid . "'";
                $result = $this->db->queryF($sql);
            }
        }
    }

    public function getObjects($criteria = null, $fields = '*', $xtid_as_key = false)
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
            $transactions = new $this->obj_class();
            $transactions->assignVars($myrow);
            if (!$xtid_as_key) {
                $ret[] = $transactions;
            } else {
                $ret[$myrow['xtid']] = $transactions;
            }
            unset($transactions);
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

    public function deleteTorrentPermissions($xtid, $mode = 'view')
    {
        global $xoopsModule;
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('gperm_itemid', $xtid));
        $criteria->add(new Criteria('gperm_modid', $xoopsModule->getVar('mid')));
        $criteria->add(new Criteria('gperm_name', $this->perm_name . $mode));
        if ($old_perms = $this->perm_handler->getObjects($criteria)) {
            foreach ($old_perms as $p) {
                $this->perm_handler->delete($p);
            }
        }
        return true;
    }

    public function insertTorrentPermissions($xtid, $group_ids, $mode = 'view')
    {
        global $xoopsModule;
        foreach ($group_ids as $xtid) {
            $perm = $this->perm_handler->create();
            $perm->setVar('gperm_name', $this->perm_name . $mode);
            $perm->setVar('gperm_itemid', $xtid);
            $perm->setVar('gperm_groupid', $xtid);
            $perm->setVar('gperm_modid', $xoopsModule->getVar('mid'));
            $this->perm_handler->insert($perm);
            $ii++;
        }
        return 'Permission ' . $this->perm_name . $mode . " set $ii times for " . _C_ADMINTITLE . ' Record xtid ' . $xtid;
    }

    public function getPermittedTorrents($transactions, $mode = 'view')
    {
        global $xoopsUser, $xoopsModule;
        $ret = false;
        if (isset($transactions)) {
            $ret      = [];
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('gperm_itemid', $transactions->getVar('xtid'), '='), 'AND');
            $criteria->add(new Criteria('gperm_modid', $xoopsModule->getVar('mid'), '='), 'AND');
            $criteria->add(new Criteria('gperm_name', $this->perm_name . $mode, '='), 'AND');

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
            if ($transactions = $this->getObjects($criteria, 'home_list')) {
                $ret = [];
                foreach ($transactions as $f) {
                    if (false != $this->perm_handler->checkRight($this->perm_name . $mode, $f->getVar('xtid'), $groups, $xoopsModule->getVar('mid'))) {
                        $ret[] = $f;
                        unset($f);
                    }
                }
            }
        }
        return ret;
    }

    public function getSingleTorrentPermission($xtid, $mode = 'view')
    {
        global $xoopsUser, $xoopsModule;
        $groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : 3;
        if (false != $this->perm_handler->checkRight($this->perm_name . $mode, $xtid, $groups, $xoopsModule->getVar('mid'))) {
            return true;
        }
        return false;
    }
}
