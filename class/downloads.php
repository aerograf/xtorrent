<?php

class downloadsResource extends XoopsObject
{
    public function __construct()
    {
        $this->XoopsObject();
        $this->initVar('lid', XOBJ_DTYPE_INT);
        $this->initVar('cid', XOBJ_DTYPE_INT);
        $this->initVar('title', XOBJ_DTYPE_TXTBOX);
        $this->initVar('url', XOBJ_DTYPE_TXTBOX);
        $this->initVar('homepage', XOBJ_DTYPE_TXTBOX);
        $this->initVar('version', XOBJ_DTYPE_TXTBOX);
        $this->initVar('size', XOBJ_DTYPE_INT);
        $this->initVar('platform', XOBJ_DTYPE_TXTBOX);
        $this->initVar('screenshot', XOBJ_DTYPE_TXTBOX);
        $this->initVar('submitter', XOBJ_DTYPE_INT);
        $this->initVar('publisher', XOBJ_DTYPE_INT);
        $this->initVar('status', XOBJ_DTYPE_INT);
        $this->initVar('date', XOBJ_DTYPE_INT);
        $this->initVar('hits', XOBJ_DTYPE_INT);
        $this->initVar('rating', XOBJ_DTYPE_FLOAT);
        $this->initVar('votes', XOBJ_DTYPE_INT);
        $this->initVar('comments', XOBJ_DTYPE_INT);
        $this->initVar('license', XOBJ_DTYPE_TXTBOX);
        $this->initVar('mirror', XOBJ_DTYPE_TXTBOX);
        $this->initVar('price', XOBJ_DTYPE_TXTBOX);
        $this->initVar('paypalemail', XOBJ_DTYPE_TXTBOX);
        $this->initVar('features', XOBJ_DTYPE_TXTBOX);
        $this->initVar('requirements', XOBJ_DTYPE_TXTBOX);
        $this->initVar('homepagetitle', XOBJ_DTYPE_TXTBOX);
        $this->initVar('forumid', XOBJ_DTYPE_INT);
        $this->initVar('limitations', XOBJ_DTYPE_INT);
        $this->initVar('dhistory', XOBJ_DTYPE_TXTBOX);
        $this->initVar('published', XOBJ_DTYPE_INT);
        $this->initVar('expired', XOBJ_DTYPE_INT);
        $this->initVar('updated', XOBJ_DTYPE_INT);
        $this->initVar('offline', XOBJ_DTYPE_INT);
        $this->initVar('description', XOBJ_DTYPE_TXTBOX);
        $this->initVar('ipaddress', XOBJ_DTYPE_TXTBOX);
        $this->initVar('notifypub', XOBJ_DTYPE_INT);
        $this->initVar('currency', XOBJ_DTYPE_TXTBOX);
    }

    public function increaseHit($amount = 1)
    {
        $hits = $this->getVar('hits');
        $this->setVar('hits', $hits + $amount);
    }
}

class XtorrentDownloadsHandler extends XoopsObjectHandler
{
    public $db;
    public $db_table;
    public $perm_name = 'xtorrent_downloads_';
    public $obj_class = 'downloadsResource';

    public function __construct($db)
    {
        if (!isset($db) && !empty($db)) {
            $this->db = $db;
        } else {
            global $xoopsDB;
            $this->db = $xoopsDB;
        }
        $this->db_table     = $this->db->prefix('xtorrent_downloads');
        $this->perm_handler = xoops_gethandler('groupperm');
    }

    public function getInstance($db)
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new xtorrentdownloadsHandler($db);
        }
        return $instance;
    }

    public function create()
    {
        return new $this->obj_class();
    }

    public function get($lid, $fields = '*')
    {
        $lid = (int)$lid;
        if ($lid > 0) {
            $sql = 'SELECT ' . $fields . ' FROM ' . $this->db_table . ' WHERE lid =' . $lid;
        } else {
            return false;
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        $numrows = $this->db->getRowsNum($result);
        if (1 == $numrows) {
            $downloads = new $this->obj_class();
            $downloads->assignVars($this->db->fetchArray($result));
            return $downloads;
        }
        return false;
    }

    public function insert($mod, $force = false)
    {
        if (strtolower(get_class($mod)) != strtolower($this->obj_class)) {
            return false;
        }
        if (!$mod->isDirty()) {
            return true;
        }
        if (!$mod->cleanVars()) {
            return false;
        }
        foreach ($mod->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        $myts = MyTextSanitizer::getInstance();
        if ($mod->isNew() || empty($lid)) {
            $lid = $this->db->genId($this->db_table . '_xt_mod_id_seq');
            $sql = sprintf(
                'INSERT INTO %s (
				`lid`, `cid`, `title`, `url`, `homepage`,`version`,`size`,`platform`,`screenshot`,`submitter`,`publisher`,`status`,`date`,`hits`,`rating`,`votes`,`comments`,`license`,`mirror`,`price, `paypalemail`,`features`,`requirements`,`homepagetitle`,`forumid`,`limitations`,`dhistory`,`published`,`expired`,`updated`,`offline`,`description`,`ipaddress`,`notifypub`,`currency`
				) VALUES (
				%u, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, 
				%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s 
				)',
                $this->db_table,
                $this->db->quoteString($lid),
                $this->db->quoteString($cid),
                $this->db->quoteString($myts->addslashes($title)),
                $this->db->quoteString($myts->addslashes($url)),
                $this->db->quoteString($myts->addslashes($homepage)),
                $this->db->quoteString($version),
                           $this->db->quoteString($size),
                $this->db->quoteString($platform),
                $this->db->quoteString($myts->addslashes($screenshot)),
                $this->db->quoteString($submitter),
                $this->db->quoteString($publisher),
                $this->db->quoteString($status),
                $this->db->quoteString($date),
                           $this->db->quoteString($hits),
                $this->db->quoteString($rating),
                $this->db->quoteString($votes),
                $this->db->quoteString($comments),
                $this->db->quoteString($$myts->addslashes(license)),
                $this->db->quoteString($myts->addslashes($mirror)),
                $this->db->quoteString($price),
                           $this->db->quoteString($myts->addslashes($paypalemail)),
                $this->db->quoteString($myts->addslashes($features)),
                $this->db->quoteString($myts->addslashes($requirements)),
                $this->db->quoteString($myts->addslashes($homepagetitle)),
                $this->db->quoteString($forumid),
                           $this->db->quoteString($myts->addslashes($limitations)),
                $this->db->quoteString($myts->addslashes($dhistory)),
                $this->db->quoteString($published),
                $this->db->quoteString($expired),
                $this->db->quoteString($updated),
                $this->db->quoteString($offline),
                           $this->db->quoteString($myts->addslashes($description)),
                $this->db->quoteString($ipaddress),
                $this->db->quoteString($notifypub),
                $this->db->quoteString($currency)
            );
        } else {
            $sql = sprintf(
                'UPDATE %s SET
				`cid` = %s, `title` = %s, `url` = %s, `homepage` = %s, `version` = %s, `size` = %s, `platform` = %s, `screenshot` = %s, `submitter` = %s, `publisher` = %s, `status` = %s, `date` = %s, `hits` = %s, `rating` = %s, `votes` = %s, `comments` = %s, `license` = %s, `mirror` = %s, `price` = %s, `paypalemail` = %s, `features` = %s, `requirements` = %s, `homepagetitle` = %s, `forumid` = %s, `limitations` = %s, `dhistory` = %s, `published` = %s, `expired` = %s, `updated` = %s, `offline` = %s, `description` = %s, `ipaddress` = %s, `notifypub` = %s, `currency` = %s WHERE `lid` = %s',
                           $this->db_table,
                $this->db->quoteString($cid),
                $this->db->quoteString($myts->addslashes($title)),
                $this->db->quoteString($myts->addslashes($url)),
                $this->db->quoteString($myts->addslashes($homepage)),
                $this->db->quoteString($version),
                $this->db->quoteString($size),
                           $this->db->quoteString($platform),
                $this->db->quoteString($myts->addslashes($screenshot)),
                $this->db->quoteString($submitter),
                $this->db->quoteString($publisher),
                $this->db->quoteString($status),
                $this->db->quoteString($date),
                $this->db->quoteString($hits),
                           $this->db->quoteString($rating),
                $this->db->quoteString($votes),
                $this->db->quoteString($comments),
                $this->db->quoteString($$myts->addslashes(license)),
                $this->db->quoteString($myts->addslashes($mirror)),
                $this->db->quoteString($price),
                           $this->db->quoteString($myts->addslashes($paypalemail)),
                $this->db->quoteString($myts->addslashes($features)),
                $this->db->quoteString($myts->addslashes($requirements)),
                $this->db->quoteString($myts->addslashes($homepagetitle)),
                $this->db->quoteString($forumid),
                           $this->db->quoteString($limitations),
                $this->db->quoteString($myts->addslashes($dhistory)),
                $this->db->quoteString($published),
                $this->db->quoteString($expired),
                $this->db->quoteString($updated),
                $this->db->quoteString($offline),
                           $this->db->quoteString($myts->addslashes($description)),
                $this->db->quoteString($ipaddress),
                $this->db->quoteString($notifypub),
                $this->db->quoteString($currency),
                $this->db->quoteString($lid)
            );
        }

        if (false != $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            $downloads->setErrors('Could not store data in the database.<br />' . $this->db->error() . ' (' . $this->db->errno() . ')<br />' . $sql);
            return false;
        }
        if (empty($lid)) {
            $lid = $this->db->getInsertId();
        }
        $downloads->assignVar('lid', $lid);
        return $lid;
    }

    public function delete($criteria = null, $force = false)
    {
        if (strtolower(get_class($downloads)) != strtolower($this->obj_class)) {
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

    public function getObjects($criteria = null, $fields = '*', $lid_as_key = false)
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
            $downloads = new $this->obj_class();
            $downloads->assignVars($myrow);
            if (!$lid_as_key) {
                $ret[] = $downloads;
            } else {
                $ret[$myrow['lid']] =& $downloads;
            }
            unset($downloads);
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

    public function deleteTorrentPermissions($lid, $mode = 'view')
    {
        global $xoopsModule;
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('gperm_itemid', $lid));
        $criteria->add(new Criteria('gperm_modid', $xoopsModule->getVar('mid')));
        $criteria->add(new Criteria('gperm_name', $this->perm_name . $mode));
        if ($old_perms = $this->perm_handler->getObjects($criteria)) {
            foreach ($old_perms as $p) {
                $this->perm_handler->delete($p);
            }
        }
        return true;
    }

    public function insertTorrentPermissions($lid, $group_ids, $mode = 'view')
    {
        global $xoopsModule;
        foreach ($group_ids as $lid) {
            $perm = $this->perm_handler->create();
            $perm->setVar('gperm_name', $this->perm_name . $mode);
            $perm->setVar('gperm_itemid', $lid);
            $perm->setVar('gperm_groupid', $lid);
            $perm->setVar('gperm_modid', $xoopsModule->getVar('mid'));
            $this->perm_handler->insert($perm);
            $ii++;
        }
        return 'Permission ' . $this->perm_name . $mode . " set $ii times for " . _C_ADMINTITLE . ' Record ID ' . $lid;
    }

    public function getPermittedTorrents($downloads, $mode = 'view')
    {
        global $xoopsUser, $xoopsModule;
        $ret = false;
        if (isset($downloads)) {
            $ret      = [];
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('gperm_itemid', $downloads->getVar('lid'), '='), 'AND');
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
            if ($downloads = $this->getObjects($criteria, 'home_list')) {
                $ret = [];
                foreach ($downloads as $f) {
                    if (false != $this->perm_handler->checkRight($this->perm_name . $mode, $f->getVar('lid'), $groups, $xoopsModule->getVar('mid'))) {
                        $ret[] = $f;
                        unset($f);
                    }
                }
            }
        }
        return ret;
    }

    public function getSingleTorrentPermission($lid, $mode = 'view')
    {
        global $xoopsUser, $xoopsModule;
        $groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : 3;
        if (false != $this->perm_handler->checkRight($this->perm_name . $mode, $lid, $groups, $xoopsModule->getVar('mid'))) {
            return true;
        }
        return false;
    }
}
