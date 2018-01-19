<?php

class paymentsResource extends XoopsObject
{
    public function __construct()
    {
        $this->XoopsObject();
        $this->initVar('id', XOBJ_DTYPE_INT);
        $this->initVar('torrent', XOBJ_DTYPE_INT);
        $this->initVar('passkey', XOBJ_DTYPE_INT);
        $this->initVar('userid', XOBJ_DTYPE_INT);
        $this->initVar('payment', XOBJ_DTYPE_INT);
        $this->initVar('business', XOBJ_DTYPE_TXTBOX);
        $this->initVar('txn_id', XOBJ_DTYPE_TXTBOX);
        $this->initVar('item_name', XOBJ_DTYPE_TXTBOX);
        $this->initVar('item_number', XOBJ_DTYPE_TXTBOX);
        $this->initVar('quantity', XOBJ_DTYPE_TXTBOX);
        $this->initVar('invoice', XOBJ_DTYPE_TXTBOX);
        $this->initVar('custom', XOBJ_DTYPE_TXTBOX);
        $this->initVar('tax', XOBJ_DTYPE_TXTBOX);
        $this->initVar('option_name1', XOBJ_DTYPE_TXTBOX);
        $this->initVar('option_selection1', XOBJ_DTYPE_TXTBOX);
        $this->initVar('option_name2', XOBJ_DTYPE_TXTBOX);
        $this->initVar('option_selection2', XOBJ_DTYPE_TXTBOX);
        $this->initVar('memo', XOBJ_DTYPE_TXTBOX);
        $this->initVar('payment_status', XOBJ_DTYPE_TXTBOX);
        $this->initVar('payment_date', XOBJ_DTYPE_INT);
        $this->initVar('txn_type', XOBJ_DTYPE_TXTBOX);
        $this->initVar('mc_gross', XOBJ_DTYPE_TXTBOX);
        $this->initVar('mc_fee', XOBJ_DTYPE_TXTBOX);
        $this->initVar('mc_currency', XOBJ_DTYPE_TXTBOX);
        $this->initVar('settle_amount', XOBJ_DTYPE_TXTBOX);
        $this->initVar('exchange_rate', XOBJ_DTYPE_TXTBOX);
        $this->initVar('first_name', XOBJ_DTYPE_TXTBOX);
        $this->initVar('last_name', XOBJ_DTYPE_TXTBOX);
        $this->initVar('address_street', XOBJ_DTYPE_TXTBOX);
        $this->initVar('address_city', XOBJ_DTYPE_TXTBOX);
        $this->initVar('address_state', XOBJ_DTYPE_TXTBOX);
        $this->initVar('address_zip', XOBJ_DTYPE_TXTBOX);
        $this->initVar('address_country', XOBJ_DTYPE_TXTBOX);
        $this->initVar('address_status', XOBJ_DTYPE_TXTBOX);
        $this->initVar('payer_email', XOBJ_DTYPE_TXTBOX);
        $this->initVar('payer_status', XOBJ_DTYPE_TXTBOX);
    }
}

class XtorrentPaymentsHandler extends XoopsObjectHandler
{
    public $db;
    public $db_table;
    public $perm_name = 'xtorrent_payments_';
    public $obj_class = 'paymentsResource';

    public function __construct($db)
    {
        if (!isset($db)&&!empty($db)) {
            $this->db = $db;
        } else {
            global $xoopsDB;
            $this->db = $xoopsDB;
        }
        $this->db_table = $this->db->prefix('xtorrent_payments');
        $this->perm_handler = xoops_gethandler('groupperm');
    }
    
    public function getInstance($db)
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new xtorrentpaymentsHandler($db);
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
            $payments = new $this->obj_class();
            $payments->assignVars($this->db->fetchArray($result));
            return $payments;
        }
        return false;
    }

    public function insert($payments, $force = false)
    {
        if (strtolower(get_class($payments)) != strtolower($this->obj_class)) {
            return false;
        }
        if (!$payments->isDirty()) {
            return true;
        }
        if (!$payments->cleanVars()) {
            return false;
        }
        foreach ($payments->cleanVars as $k=>$v) {
            ${$k} = $v;
        }
        $myts = MyTextSanitizer::getInstance();
        if ($payments->isNew() || empty($id)) {
            $id  = $this->db->genId($this->db_table . '_xt_payments_id_seq');
            $sql = sprintf(
                'INSERT INTO %s (
				`id`, `torrent`, `passkey`, `userid`, `payment`, `business`, `txn_id`, `item_name`, `item_number`, `quantity`, `invoice`, `custom`, `tax`, `option_name1`, `option_selection1`, `option_name2`, `option_selection2`, `memo`, `payment_status`, `payment_date`, 	`txn_type`, `mc_gross`, `mc_fee`, `mc_currency`, `settle_amount`, `exchange_rate`, `first_name`, `last_name`, `address_street`, `address_city`, `address_state`, `address_zip`, `address_country`, `address_status`, `payer_email`, `payer_status`
				) VALUES (
				%u, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s,
				%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s
				)',
                $this->db_table,
                $this->db->quoteString($id),
                $this->db->quoteString($torrent),
                $this->db->quoteString($passkey),
                $this->db->quoteString($userid),
                $this->db->quoteString($payment),
                $this->db->quoteString($business),
                $this->db->quoteString($txn_id),
                $this->db->quoteString($item_name),
                $this->db->quoteString($item_number),
                $this->db->quoteString($quantity),
                $this->db->quoteString($invoice),
                $this->db->quoteString($custom),
                $this->db->quoteString($tax),
                $this->db->quoteString($option_name1),
                $this->db->quoteString($option_selection1),
                $this->db->quoteString($option_name2),
                $this->db->quoteString($option_selection2),
                $this->db->quoteString($myts->addslashes($memo)),
                $this->db->quoteString($payment_status),
                $this->db->quoteString($payment_date),
                $this->db->quoteString($txn_type),
                $this->db->quoteString($mc_gross),
                $this->db->quoteString($mc_fee),
                $this->db->quoteString($mc_currency),
                $this->db->quoteString($settle_amount),
                $this->db->quoteString($exchange_rate),
                $this->db->quoteString($myts->addslashes($first_name)),
                $this->db->quoteString($myts->addslashes($last_name)),
                $this->db->quoteString($myts->addslashes($address_street)),
                $this->db->quoteString($myts->addslashes($address_city)),
                $this->db->quoteString($myts->addslashes($address_state)),
                $this->db->quoteString($myts->addslashes($address_zip)),
                $this->db->quoteString($myts->addslashes($address_country)),
                $this->db->quoteString($myts->addslashes($address_status)),
                $this->db->quoteString($myts->addslashes($payer_email)),
                $this->db->quoteString($myts->addslashes($payer_status))
            );
        } else {
            $sql = sprintf(
                'UPDATE %s SET
				`torrent` = %s,
				`passkey` = %s,
				`userid` = %s,
				`payment` = %s,
				`business` = %s,
				`txn_id` = %s,
				`item_name` = %s,
				`item_number` = %s,
				`quantity` = %s,
				`invoice` = %s,
				`custom` = %s,
				`tax` = %s,
				`option_name1` = %s,
				`option_selection1` = %s,
				`option_name2` = %s,
				`option_selection2` = %s,
				`memo` = %s,
				`payment_status` = %s,
				`payment_date` = %s,
				`txn_type` = %s,
				`mc_gross` = %s,
				`mc_fee` = %s,
				`mc_currency` = %s,
				`settle_amount` = %s,
				`exchange_rate` = %s,
				`first_name` = %s,
				`last_name` = %s,
				`address_street` = %s,
				`address_city` = %s,
				`address_state` = %s,
				`address_zip` = %s,
				`address_country` = %s,
				`address_status` = %s,
				`payer_email` = %s,
				`payer_status` = %s WHERE id = %s',
                $this->db_table,
                $this->db->quoteString($torrent),
                $this->db->quoteString($passkey),
                $this->db->quoteString($userid),
                $this->db->quoteString($payment),
                $this->db->quoteString($business),
                $this->db->quoteString($txn_id),
                $this->db->quoteString($item_name),
                $this->db->quoteString($item_number),
                $this->db->quoteString($quantity),
                $this->db->quoteString($invoice),
                $this->db->quoteString($custom),
                $this->db->quoteString($tax),
                $this->db->quoteString($option_name1),
                $this->db->quoteString($option_selection1),
                $this->db->quoteString($option_name2),
                $this->db->quoteString($option_selection2),
                $this->db->quoteString($myts->addslashes($memo)),
                $this->db->quoteString($payment_status),
                $this->db->quoteString($payment_date),
                $this->db->quoteString($txn_type),
                $this->db->quoteString($mc_gross),
                $this->db->quoteString($mc_fee),
                $this->db->quoteString($mc_currency),
                $this->db->quoteString($settle_amount),
                $this->db->quoteString($exchange_rate),
                $this->db->quoteString($myts->addslashes($first_name)),
                $this->db->quoteString($myts->addslashes($last_name)),
                $this->db->quoteString($myts->addslashes($address_street)),
                $this->db->quoteString($myts->addslashes($address_city)),
                $this->db->quoteString($myts->addslashes($address_state)),
                $this->db->quoteString($myts->addslashes($address_zip)),
                $this->db->quoteString($myts->addslashes($address_country)),
                $this->db->quoteString($myts->addslashes($address_status)),
                $this->db->quoteString($myts->addslashes($payer_email)),
                $this->db->quoteString($myts->addslashes($payer_status)),
                $this->db->quoteString($id)
            );
        }
        
        if (false != $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            $payments->setErrors('Could not store data in the database.<br>' . $this->db->error() . ' (' . $this->db->errno() . ')<br>' . $sql);
            return false;
        }
        if (empty($id)) {
            $id = $this->db->getInsertId();
        }
        $payments->assignVar('id', $id);
        return $id;
    }
    
    public function delete($criteria = null, $force = false)
    {
        if (strtolower(get_class($payments)) != strtolower($this->obj_class)) {
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
            $payments = new $this->obj_class();
            $payments->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] = $payments;
            } else {
                $ret[$myrow['id']] = $payments;
            }
            unset($payments);
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
    
    public function getPermittedTorrents($payments, $mode = 'view')
    {
        global $xoopsUser, $xoopsModule;
        $ret = false;
        if (isset($payments)) {
            $ret      = [];
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('gperm_itemid', $payments->getVar('id'), '='), 'AND');
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
            if ($payments = $this->getObjects($criteria, 'home_list')) {
                $ret = [];
                foreach ($payments as $f) {
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
