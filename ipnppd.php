<?php

include '../../mainfile.php';

global $xoopsModuleConfig, $xoopsDB;

/*include_once 'includes/common.php';
include_once 'includes/functions.php';
include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";
$xoopsModuleConfig = configInfo();
*/

$ERR    = 0;
$log    = '';
$loglvl = $xoopsModuleConfig['ipn_dbg_lvl'];
define('_ERR', 1);
define('_INF', 2);

if (isset($_GET['dbg'])) {
    $dbg = 1;
} else {
    $dbg = 0;
}

if ($dbg) {
    dprt(_XT_DEBUGACTIVE, _INF);
    echo _XT_DEBUGHEADER;
    $receiver_email = $paypalemail;
}

// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';

foreach ($_REQUEST as $key => $value) {
    $value = urlencode(stripslashes($value));
    $req   .= "$key = $value";
}

// post back to PayPal system to validate
$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= 'Content-Length: ' . strlen($req) . "\r\n\r\n";
$fp     = fsockopen('www.paypal.com', 80, $errno, $errstr, 30);

// For Debug Purposes ONLY
//$fp = fsockopen('www.eliteweaver.co.uk', 80, $errno, $errstr, 30);

dprt(_XT_OPENCONN, _INF);

if (!$fp) {
    // HTTP ERROR
    dprt(_XT_CONNFAIL, _ERR);
    die('Failed to post back.');
}

dprt('OK!', _INF);

// assign posted variables to local variables
$item_name        = $_REQUEST['item_name'];
$item_number      = $_REQUEST['item_number'];
$payment_status   = $_REQUEST['payment_status'];
$payment_amount   = $_REQUEST['mc_gross'];
$payment_currency = $_REQUEST['mc_currency'];
$txn_id           = $_REQUEST['txn_id'];
$txn_type         = $_REQUEST['txn_type'];
$receiver_email   = $_REQUEST['receiver_email'];
$payer_email      = $_REQUEST['payer_email'];

$iscrape = explode(' : ', $_REQUEST['item_name']);

$sql  = 'SELECT b.paypalemail FROM ' . $xoopsDB->prefix('xtorrent_users') . ' a INNER JOIN ' . $xoopsDB->prefix('xtorrent_downloads') . " b ON a.lid = b.lid WHERE a.lid = '" . $_REQUEST['item_number'] . "' AND a.passkey =  '" . $_REQUEST['custom'] . "'";
$rset = $xoopsDB->query($sql);
list($paypalemail) = $xoopsDB->fetchRow($rset);

fwrite($fp, $header . $req);

/*
// Perform PayPal email account verification
if( !$dbg && strcasecmp( $_REQUEST['business'], $paypalemail) != 0)
{
        dprt(sprintf(_XT_RCVINVALID,$receiver_email), _ERR) ;
        $ERR = 1;
}*/

$insertSQL = '';
// Look for duplicate txn_id's
if ($txn_id) {
    $sql            = 'SELECT * FROM ' . $xoopsDB->prefix('xtorrent_payments') . " WHERE txn_id = '$txn_id'";
    $Recordset1     = $xoopsDB->query($sql);
    $row_Recordset1 = $xoopsDB->fetchArray($Recordset1);
    $NumDups        = $xoopsDB->getRowsNum($Recordset1);
}

while (!$dbg && !$ERR && !feof($fp)) {
    $res = fgets($fp, 1024);
    if (0 == strcmp($res, 'VERIFIED')) {
        dprt(_XT_VERIFIED, _INF);
        // Ok, PayPal has told us we have a valid IPN here

        // Check for a reversal for a refund
        if (0 == strcmp($payment_status, 'Refunded')) {
            // Verify the reversal
            dprt(_XT_REFUND, _INF);
            if ((0 == $NumDups) || strcmp($row_Recordset1['payment_status'], 'Completed')
                || (0 != strcmp($row_Recordset1['txn_type'], 'web_accept') && 0 != strcmp($row_Recordset1['txn_type'], 'send_money'))) {
                // This is an error.  A reversal implies a pre-existing completed transaction
                dprt(_XT_TRANSMISSING, _ERR);
                foreach ($_REQUEST as $key => $val) {
                    dprt("$key => $val", _ERR);
                }
                break;
            }
            if (1 != $NumDups) {
                dprt(_XT_MULTITXNS, _ERR);
                foreach ($_REQUEST as $key => $val) {
                    dprt("$key => $val", _ERR);
                }
                break;
            }

            // We flip the sign of these amount so refunds can be handled correctly
            $mc_gross  = -$_REQUEST['mc_gross'];
            $mc_fee    = -$_REQUEST['mc_fee'];
            $insertSQL = 'INSERT INTO '
                         . $xoopsDB->prefix('xtorrent_payments')
                         . " (`txn_id`,`business`,`item_name`, `item_number`, `quantity`, `invoice`, `custom`, `memo`, `tax`, `option_name1`, `option_selection1`, `option_name2`, `option_selection2`, `payment_status`, `payment_date`, `txn_type`, `mc_gross`, `mc_fee`, `mc_currency`, `settle_amount`, `exchange_rate`, `first_name`, `last_name`, `address_street`, `address_city`, `address_state`, `address_zip`, `address_country`, `address_status`, `payer_email`, `payer_status`, `torrent`, `passkey`, `userid`) VALUES ('"
                         . $_REQUEST['txn_id']
                         . "','"
                         . $_REQUEST['business']
                         . "','"
                         . $_REQUEST['item_name']
                         . "','"
                         . $_REQUEST['item_number']
                         . "','"
                         . $_REQUEST['quantity']
                         . "','"
                         . $_REQUEST['invoice']
                         . "','"
                         . $_REQUEST['custom']
                         . "','"
                         . $_REQUEST['memo']
                         . "','"
                         . $_REQUEST['tax']
                         . "','"
                         . $_REQUEST['option_name1']
                         . "','"
                         . $_REQUEST['option_selection1']
                         . "','"
                         . $_REQUEST['option_name2']
                         . "','"
                         . $_REQUEST['option_selection2']
                         . "','"
                         . $_REQUEST['payment_status']
                         . "','"
                         . strftime('%Y-%m-%d %H:%M:%S', strtotime($_REQUEST['payment_date']))
                         . "','"
                         . $_REQUEST['txn_type']
                         . "','$mc_gross','$mc_fee','"
                         . $_REQUEST['mc_currency']
                         . "','"
                         . $_REQUEST['settle_amount']
                         . "','"
                         . $_REQUEST['exchange_rate']
                         . "','"
                         . $_REQUEST['first_name']
                         . "','"
                         . $_REQUEST['last_name']
                         . "','"
                         . $_REQUEST['address_street']
                         . "','"
                         . $_REQUEST['address_city']
                         . "','"
                         . $_REQUEST['address_state']
                         . "','"
                         . $_REQUEST['address_zip']
                         . "','"
                         . $_REQUEST['address_country']
                         . "','"
                         . $_REQUEST['address_status']
                         . "','"
                         . $_REQUEST['payer_email']
                         . "','"
                         . $_REQUEST['payer_status']
                         . "','"
                         . $_REQUEST['item_number']
                         . "','"
                         . $_REQUEST['custom']
                         . "','"
                         . $iscrape[0]
                         . "')";

            // We're cleared to add this record
            dprt($insertSQL, _INF);
            $Result1 = $xoopsDB->queryF($insertSQL);
            dprt('SQL result = ' . $Result1, _INF);

            $updateSQL = 'UPDATE ' . $xoopsDB->prefix('xtorrent_users') . " set enabled = 'yes' WHERE lid = '" . $_REQUEST['item_number'] . "' and passkey = '" . $_REQUEST['custom'] . "'";
            $Result1   = $xoopsDB->queryF($updateSQL);

            break;
        } elseif // Look for anormal payment
        ((0 == strcmp($payment_status, 'Completed')) && ((0 == strcmp($txn_type, 'web_accept')) || (0 == strcmp($txn_type, 'send_money')))) {
            dprt('Normal transaction', _INF);
            if ($lp) {
                fwrite($lp, $payer_email . ' ' . $payment_status . ' ' . $_REQUEST['payment_date'] . "\n");
            }

            // Check for a duplicate txn_id
            if (0 != $NumDups) {
                dprt(_XT_DUPLICATETXN, _ERR);
                foreach ($_REQUEST as $key => $val) {
                    dprt("$key => $val", _ERR);
                }
                break;
            }

            $insertSQL = 'INSERT INTO '
                         . $xoopsDB->prefix('xtorrent_payments')
                         . " (`txn_id`,`business`,`item_name`, `item_number`, `quantity`, `invoice`, `custom`, `memo`, `tax`, `option_name1`, `option_selection1`, `option_name2`, `option_selection2`, `payment_status`, `payment_date`, `txn_type`, `mc_gross`, `mc_fee`, `mc_currency`, `settle_amount`, `exchange_rate`, `first_name`, `last_name`, `address_street`, `address_city`, `address_state`, `address_zip`, `address_country`, `address_status`, `payer_email`, `payer_status`, `torrent`, `passkey`, `userid`) VALUES ('"
                         . $_REQUEST['txn_id']
                         . "', '"
                         . $_REQUEST['business']
                         . "', '"
                         . $_REQUEST['item_name']
                         . "', '"
                         . $_REQUEST['item_number']
                         . "', '"
                         . $_REQUEST['quantity']
                         . "', '"
                         . $_REQUEST['invoice']
                         . "', '"
                         . $_REQUEST['custom']
                         . "', '"
                         . $_REQUEST['memo']
                         . "', '"
                         . $_REQUEST['tax']
                         . "', '"
                         . $_REQUEST['option_name1']
                         . "', '"
                         . $_REQUEST['option_selection1']
                         . "', '"
                         . $_REQUEST['option_name2']
                         . "', '"
                         . $_REQUEST['option_selection2']
                         . "', '"
                         . $_REQUEST['payment_status']
                         . "', '"
                         . strftime('%Y-%m-%d %H:%M:%S', strtotime($_REQUEST['payment_date']))
                         . "', '"
                         . $_REQUEST['txn_type']
                         . "', '"
                         . $_REQUEST['mc_gross']
                         . "', '"
                         . $_REQUEST['mc_fee']
                         . "', '"
                         . $_REQUEST['mc_currency']
                         . "', '"
                         . $_REQUEST['settle_amount']
                         . "', '"
                         . $_REQUEST['exchange_rate']
                         . "', '"
                         . $_REQUEST['first_name']
                         . "', '"
                         . $_REQUEST['last_name']
                         . "', '"
                         . $_REQUEST['address_street']
                         . "', '"
                         . $_REQUEST['address_city']
                         . "', '"
                         . $_REQUEST['address_state']
                         . "', '"
                         . $_REQUEST['address_zip']
                         . "', '"
                         . $_REQUEST['address_country']
                         . "', '"
                         . $_REQUEST['address_status']
                         . "', '"
                         . $_REQUEST['payer_email']
                         . "', '"
                         . $_REQUEST['payer_status']
                         . "','"
                         . $_REQUEST['item_number']
                         . "','"
                         . $_REQUEST['custom']
                         . "','"
                         . $iscrape[0]
                         . "')";

            // We're cleared to add this record
            dprt($insertSQL, _INF);
            $Result1 = $xoopsDB->queryF($insertSQL);
            dprt('SQL result = ' . $Result1, _INF);

            $updateSQL = 'UPDATE ' . $xoopsDB->prefix('xtorrent_users') . " set enabled = 'yes' WHERE lid = '" . $_REQUEST['item_number'] . "' and passkey = '" . $_REQUEST['custom'] . "'";
            $Result1   = $xoopsDB->queryF($updateSQL);

            break;
        } else { // We're not interested in this transaction, so we're done
            dprt(_XT_NOTINTERESTED, _ERR);
            foreach ($_REQUEST as $key => $val) {
                dprt("$key => $val", _ERR);
            }
            break;
        }
    } elseif (0 == strcmp($res, 'INVALID')) {
        // log for manual investigation
        dprt(_XT_INVALIDIPN, _ERR);
        foreach ($_REQUEST as $key => $val) {
            dprt("$key => $val", _ERR);
        }
        break;
    }
}

if ($dbg) {
    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('xtorrent_payments') . ' LIMIT 10';
    echo 'Executing test query...';
    $Result1 = $xoopsDB->query($sql);
    if ($Result1) {
        echo _XT_DEBUGPASS . '<br>';
    } else {
        echo '<b>' . _XT_DEBUGFAIL . '</b><br>';
    }
    echo sprintf(_XT_RCVEMAIL, $paypalemail);
}

if ($log) {
    dprt('<br>' . _XT_LOGBEGIN . "<br>\n", _INF);
    // Insert the log entry
    $sql     = 'INSERT INTO ' . $xoopsDB->prefix('xtorrent_translog') . " VALUES (NULL,'" . strftime('%Y-%m-%d %H:%M:%S', mktime()) . "', '" . strftime('%Y-%m-%d %H:%M:%S', strtotime($_REQUEST['payment_date'])) . "','" . addslashes($log) . "')";
    $Result1 = $xoopsDB->queryF($sql);

    // Clear out old log entries
    $sql     = 'SELECT id AS lowid FROM ' . $xoopsDB->prefix('xtorrent_translog') . ' ORDER BY id DESC LIMIT ' . $xoopsModuleConfig['ipn_log_entries'];
    $Result1 = $xoopsDB->query($sql);
    while (list($lowid) = $xoopsDB->fetchRow($Result1)) {
        $sql     = 'DELETE FROM ' . $xoopsDB->prefix('xtorrent_translog') . " WHERE id < '" . $lowid . "'";
        $Result1 = $xoopsDB->queryF($sql);
    }
}

fclose($fp);
if ($lp) {
    fwrite($lp, "Exiting\n");
}
if ($lp) {
    fclose($lp);
}

if ($dbg) {
    echo '<hr>';
    echo _XT_IFNOERROR . '<br>';
}

function dprt($str, $clvl)
{
    global $dbg, $xoopsDB, $lp, $log, $loglvl;

    if ($lp) {
        fwrite($lp, $str . "\n");
    }
    if ($dbg) {
        echo $str . '<br>';
    }
    if ($clvl <= $loglvl) {
        $log .= $str . "\n";
    }
}
