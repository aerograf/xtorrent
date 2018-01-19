<?php

class torrentResource extends XoopsObject
{
    public function __construct()
    {
        $this->XoopsObject();
        $this->initVar('lid', XOBJ_DTYPE_INT);
        $this->initVar('seeds', XOBJ_DTYPE_INT);
        $this->initVar('leechers', XOBJ_DTYPE_INT);
        $this->initVar('totalsize', XOBJ_DTYPE_FLOAT);
        $this->initVar('modifiedby', XOBJ_DTYPE_TXTBOX);
        $this->initVar('tname', XOBJ_DTYPE_TXTBOX);
        $this->initVar('infoHash', XOBJ_DTYPE_TXTBOX);
        $this->initVar('announce', XOBJ_DTYPE_TXTBOX);
        $this->initVar('md5sum', XOBJ_DTYPE_TXTBOX);
        $this->initVar('added', XOBJ_DTYPE_INT);
        $this->initVar('announce');
        $this->initVar('announceList');
        $this->initVar('createdBy');
        $this->initVar('creationDate');
        $this->initVar('encoding');
        $this->initVar('name');
        $this->initVar('length');
        $this->initVar('files');
        $this->initVar('pieceLength');
        $this->initVar('pieces');
        $this->initVar('comment');
        $this->initVar('private');
        $this->initVar('filename');
        $this->initVar('benc');
    }

    public function getArray()
    {
        $ret = [];
        foreach ($this->cleanVars as $k => $v) {
            $ret[$k] = $v;
        }
        return $ret;
    }
}

class XtorrentTorrentHandler extends XoopsObjectHandler
{
    public $db;
    public $db_table;
    public $perm_name = 'xtorrent_torrent_';
    public $obj_class = 'torrentResource';

    public function __construct($db)
    {
        if (!isset($db) && !empty($db)) {
            $this->db = $db;
        } else {
            global $xoopsDB;
            $this->db = $xoopsDB;
        }
        $this->db_table    = $this->db->prefix('xtorrent_torrent');
        $this->permHandler = xoops_getHandler('groupperm');
    }

    public function getInstance($db)
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new xtorrenttorrentHandler($db);
        }
        return $instance;
    }

    public function create()
    {
        return new $this->obj_class();
    }

    public function poll($download)
    {
        $torrent = $this->retrieve($download);

        if (!$torrent->getVar('error')) {
            $xthdlr_torrent = xoops_load('torrent', 'xtorrent');

            $criteria_y = new CriteriaCompo(new Criteria('lid', $lid));
            $xthdlr_torrent->delete($criteria_y);

            $torrent = $xthdlr_torrent->create();

            $torrent->setVar('seeds', $summary['seeds']);
            $torrent->setVar('leechers', $summary['leechers']);
            $torrent->setVar('totalsize', $torrent->totalSize);
            $torrent->setVar('modifiedby', $torrent->modifiedBy);
            $torrent->setVar('tname', $torrent->name);
            $torrent->setVar('infoHash', $torrent->infoHash);
            $torrent->setVar('announce', $torrent->announce);
            $torrent->setVar('md5sum', $torrent->md5sum);
            $torrent->setVar('added', time());

            $xthdlr_torrent->insert($torrent);

            $xthdlr_files = xoops_load('files', 'xtorrent');
            $xthdlr_files->delete($criteria_y);

            foreach ($torrent->files as $file) {
                $file = $xthdlr_files->create();
                $file->setVar('lid', $lid);
                $file->setVar('file', $file->name);
                $xthdlr_files->insert($file);
            }
        }
        return $torrent;
    }

    public function retrieve($torrent)
    {
        $xthdlr_downloads = xoops_load('downloads', 'xtorrent');

        if (is_object($torrent)) {
            $download = $xthdlr_downloads->get($torrent->getVar('lid'));
        } elseif (is_numeric($torrent)) {
            $download = $xthdlr_downloads->get($torrent);
        } else {
            return false;
        }

        // Create and Decompile Benc Object
        $xthdlr_benc  = xoops_load('benc', 'xtorrent');
        $benc_torrent = $xthdlr_benc->create();
        $benc_torrent->setVar('filename', $download->getVar('filename'));
        $benc_torrent = $xthdlr_benc->decompile($benc_torrent);

        $torrentInfo = $benc_torrent->getVar('object');

        if (false === $torrentInfo) {
            $this->error = true;
            trigger_error('The torrent file is invalid', E_USER_WARNING);
        }

        $torrent->setVar('benc', $benc_torrent);
        $torrent->setVar('announce', $torrentInfo['announce']);
        $torrent->setVar('announceList', $torrentInfo['announce-list']);
        $torrent->setVar('createdBy', $torrentInfo['created by']);
        $torrent->setVar('creationDate', $torrentInfo['creation date']);
        $torrent->setVar('comment', $torrentInfo['comment']);
        $torrent->setVar('modifiedBy', $torrentInfo['modified-by']);
        $torrent->setVar('pieceLength', $torrentInfo['info']['piece length']);
        $torrent->setVar('pieces', $torrentInfo['info']['pieces']);
        $torrent->setVar('private', 1 == $torrentInfo['info']['private']);
        $torrent->setVar('tname', $torrentInfo['info']['name']);
        $torrent->setVar('encoding', $torrentInfo['encoding']);
        $torrent->setVar('infoHash', $torrentInfo['info']['hash']);

        if (!isset($torrentInfo['info']['files'])) {
            $torrent->setVar('length', $torrentInfo['info']['length']);
            $torrent->setVar('md5sum', $torrentInfo['info']['md5sum']);
            $torrent->setVar('totalSize', $torrentInfo['info']['length']);
        } else {
            $files     = [];
            $totalSize = 0;
            foreach ($torrentInfo['info']['files'] as $key => $fileInfo) {
                $torrentFile         = new TorrentFile();
                $torrentFile->md5sum = $fileInfo['md5sum'];
                $torrentFile->length = $fileInfo['length'];
                $torrentFile->name   = implode('/', $fileInfo['path']);
                $md5key              = md5($fileInfo['md5sum'] . $md5key);
                $files[$key]         = $torrentFile;
                $totalSize           += $torrentFile->length;
            }
            $torrent->setVar('length', 0);
            $torrent->setVar('files', $files);
            $torrent->setVar('md5sum', $md5key);
            $torrent->setVar('totalSize', $totalSize);
        }
        return $torrent;
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
            $torrent = new $this->obj_class();
            $torrent->assignVars($this->db->fetchArray($result));
            return $torrent;
        }
        return false;
    }

    public function insert($torrent, $force = false)
    {
        if (strtolower(get_class($torrent)) != strtolower($this->obj_class)) {
            return false;
        }
        if (!$torrent->isDirty()) {
            return true;
        }
        if (!$torrent->cleanVars()) {
            return false;
        }
        foreach ($torrent->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        $myts = MyTextSanitizer::getInstance();
        if ($torrent->isNew() || empty($id)) {
            $id  = $this->db->genId($this->db_table . '_xt_torrent_id_seq');
            $sql = sprintf(
                'INSERT INTO %s (
				`lid`, `seeds`, `leeches`, `totalsize`, `modifiedby`, `tname`, `infoHash`, `announce`, `md5sum`, `added`
				) VALUES (
				%u, %s, %s, %s, %s, %s, %s, %s, %s, %s
				)',
                $this->db_table,
                $this->db->quoteString($lid),
                $this->db->quoteString($seeds),
                $this->db->quoteString($leeches),
                $this->db->quoteString($totalsize),
                $this->db->quoteString($modifiedby),
                $this->db->quoteString($tname),
                $this->db->quoteString($infoHash),
                           $this->db->quoteString($announce),
                $this->db->quoteString($md5sum),
                $this->db->quoteString($added)
            );
        } else {
            $sql = sprintf(
                'UPDATE %s SET
				`seeds` = %s,
				`leeches` = %s,
				`totalsize` = %s,
				`modifiedby` = %s,
				`tname` = %s,
				`infoHash` = %s,
				`announce` = %s,
				`md5sum` = %s,
				`added` = %s WHERE lid = %s',
                $this->db_table,
                $this->db->quoteString($seeds),
                $this->db->quoteString($leeches),
                $this->db->quoteString($totalsize),
                $this->db->quoteString($modifiedby),
                $this->db->quoteString($tname),
                $this->db->quoteString($infoHash),
                           $this->db->quoteString($announce),
                $this->db->quoteString($md5sum),
                $this->db->quoteString($added),
                $this->db->quoteString($lid)
            );
        }

        if (false !== $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            $torrent->setErrors('Could not store data in the database.<br>' . $this->db->error() . ' (' . $this->db->errno() . ')<br>' . $sql);
            return false;
        }
        if (empty($id)) {
            $id = $this->db->getInsertId();
        }
        $torrent->assignVar('id', $id);
        return $id;
    }

    public function delete($criteria = null, $force = false)
    {
        if (strtolower(get_class($torrent)) != strtolower($this->obj_class)) {
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
            $torrent = new $this->obj_class();
            $torrent->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] = $torrent;
            } else {
                $ret[$myrow['id']] = $torrent;
            }
            unset($torrent);
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
        if ($old_perms =& $this->permHandler->getObjects($criteria)) {
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

    public function getPermittedTorrents($torrent, $mode = 'view')
    {
        global $xoopsUser, $xoopsModule;
        $ret = false;
        if (isset($torrent)) {
            $ret      = [];
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('gperm_itemid', $torrent->getVar('lid'), '='), 'AND');
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
            if ($torrent = $this->getObjects($criteria, 'home_list')) {
                $ret = [];
                foreach ($torrent as $f) {
                    if (false !== $this->permHandler->checkRight($this->perm_name . $mode, $f->getVar('lid'), $groups, $xoopsModule->getVar('mid'))) {
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

// Class representing a file within a torrent
class TorrentFile
{
    public $md5sum;
    public $name;
    public $length;
}
