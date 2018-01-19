<?php

include 'header.php';
require XOOPS_ROOT_PATH . '/modules/xtorrent/class/qcp71.class.php';

global $xoopsUser, $xoopsModuleConfig, $myts, $xoopsModule;

$agreed = isset($_GET['agree']) ? $_GET['agree'] : 0;

$lid = (int)$_GET['lid'];
$cid = (int)$_GET['cid'];

function passkey_paypal($lid, $made)
{
	global $xoopsUser, $xoopsDB, $xoopsModuleConfig, $myts;

	$sql    = 'SELECT cid, price, paypalemail, currency, title, description, ipaddress FROM ' . $xoopsDB->prefix('xtorrent_downloads') . " where lid = $lid";
	$result = $xoopsDB->queryF($sql);
	list($cid, $price, $paypalemail, $currency, $title, $description, $ipaddress) = $xoopsDB->fetchRow($result);

	if (!empty($xoopsUser)){
		$uname = $xoopsUser->getVar('uname');
		$uid   = $xoopsUser->getVar('uid');
		$pass  = $xoopsUser->getVar('pass');
	} else {
		$uname = 'guest';
		$uid   = 0;
		$pass  = md5($uname.$uid.$lid);
	}

	if (!empty($price)&&(float)$price>0&&!empty($paypalemail)&&$ipaddress!=$_SERVER['REMOTE_ADDR'])
	{
		$sql = 'select id, passkey from ' . $xoopsDB->prefix('xtorrent_users') . " where username='" . $uname . "' and uid='" . $uid . "' and lid = $lid and secret = sha1('" . xtorrent_get_base_domain(gethostbyaddr($_SERVER['REMOTE_ADDR'])) . "') and enabled = 'yes' order by last_access, id";
		$rt  = $xoopsDB->queryF($sql);				
		if ($xoopsDB->getRowsNum($rt)){			
			$sql = 'select id, passkey from ' . $xoopsDB->prefix('xtorrent_users') . " where lid = $lid and secret = sha1('" . xtorrent_get_base_domain(gethostbyaddr($_SERVER['REMOTE_ADDR'])) . "') and enabled = 'yes' order by last_access, id";
			$rt  = $xoopsDB->queryF($sql);				
		}
		$rt = $xoopsDB->queryF($sql);				
		if ($xoopsDB->getRowsNum($rt)){
			if ('yes' == $made)
			{
				list($id, $passkey) = $xoopsDB->fetchRow($rt);
				$sql = 'UDPATE ' . $xoopsDB->prefix('xtorrent_users') . " SET enabled = 'yes', last_access = '" . date('Y-m-d H:i:s') . "' WHERE id = '" . $id . "'";
				$rt  = $xoopsDB->queryF($sql);				
				$payment_made = true;
			} else {
				list($id, $passkey) = $xoopsDB->fetchRow($rt);
				$sql = 'SELECT id FROM ' . $xoopsDB->prefix('xtorrent_payments') . " WHERE custom = '" . $passkey . "'";
				$rt  = $xoopsDB->queryF($sql);				
				if ($xoopsDB->getRowsNum($rt)){
					$sql = 'UDPATE ' . $xoopsDB->prefix('xtorrent_users') . " SET enabled = 'yes', last_access = '" . date('Y-m-d H:i:s') . "' WHERE id = '" . $id . "'";
					$rt  = $xoopsDB->queryF($sql);
					$payment_made = true;
				}				
			}
		} else {
			$sql = "select id, $passkey from ".$xoopsDB->prefix('xtorrent_users'). " where username='".$uname."' and uid='".$uid."' and lid = $lid and secret = sha1('".xtorrent_get_base_domain(gethostbyaddr($_SERVER['REMOTE_ADDR']))."') order by last_access, id";
			$rt  = $xoopsDB->queryF($sql);				
			if (!$xoopsDB->getRowsNum($rt)){			
				$sql = "select id, $passkey from ".$xoopsDB->prefix('xtorrent_users'). " where lid = $lid and secret = sha1('".xtorrent_get_base_domain(gethostbyaddr($_SERVER['REMOTE_ADDR']))."') order by last_access, id";
				$rt  = $xoopsDB->queryF($sql);				
			}
			if (!$xoopsDB->getRowsNum($rt)){
				$sql = 'delete from ' . $xoopsDB->prefix('xtorrent_users') . ' where uid=' . $uid . ' and username=' . $uname . " and lid = $lid and secret = sha1('" . xtorrent_get_base_domain(gethostbyaddr($_SERVER['REMOTE_ADDR'])) . "') and enabled = 'no'";
				$rt  = $xoopsDB->queryF($sql);
				$sql = 'insert into ' . $xoopsDB->prefix('xtorrent_users') . " (username, uid, old_password, secret, lid, enabled) VALUES ('" . $uname . "', " . $uid . ", '" . $pass . "', sha1('" . xtorrent_get_base_domain(gethostbyaddr($_SERVER['REMOTE_ADDR'])) . "'),'$lid', 'no')";
				$rt  = $xoopsDB->queryF($sql);
			} else {
				$sql = 'delete from ' . $xoopsDB->prefix('xtorrent_users') . ' where uid=' . $uid . ' and username=' . $uname . " and lid = $lid and secret = sha1('" . xtorrent_get_base_domain(gethostbyaddr($_SERVER['REMOTE_ADDR'])) . "') and enabled = 'no'";
				$rt  = $xoopsDB->queryF($sql);
				$sql = 'insert into ' . $xoopsDB->prefix('xtorrent_users') . " (username, uid, old_password, secret, lid, enabled) VALUES ('" . $uname . "', " . $uid . ", '" . $pass . "', sha1('" . xtorrent_get_base_domain(gethostbyaddr($_SERVER['REMOTE_ADDR'])) . "'),'$lid', 'no')";
				$rt  = $xoopsDB->queryF($sql);
			}
			if($rt){
				$kid = $xoopsDB->getInsertId();
				$sql = 'update ' . $xoopsDB->prefix('xtorrent_users') . " set passhash = md5(concat(secret, old_password, secret, '" . gethostbyaddr($_SERVER['REMOTE_ADDR']) . "')), last_access = '" . date('Y-m-d H:i:s') . "' where id = " . $kid ;
				$rt  = $xoopsDB->queryF($sql);
				$sql = 'select * from ' . $xoopsDB->prefix('xtorrent_users') . ' where id = ' . $kid ;
				$rt  = $xoopsDB->queryF($sql);
				$row = $xoopsDB->fetchArray($rt); 
				$crc = new qcp71($lid.$kid.$row['username'].get_date_time().$row['passhash'], mt_rand(17,245), mt_rand(31,121));
				$passkey = $crc->crc;
				$sql = 'update ' . $xoopsDB->prefix('xtorrent_users') . " set passkey = '" . $passkey . "', last_access = '" . date('Y-m-d H:i:s') . "' where id = " . $kid ;
				$rt  = $xoopsDB->queryF($sql);
			}

			$payment_made = false;
		}

	} else {	
		$sql = 'select id, passkey from ' . $xoopsDB->prefix('xtorrent_users') . " where username='" . $uname . "' and uid='" . $uid . "' and lid = $lid and secret = sha1('" . xtorrent_get_base_domain(gethostbyaddr($_SERVER['REMOTE_ADDR'])) . "') and enabled = 'yes'";
		$rt  = $xoopsDB->queryF($sql);				
		if (!$xoopsDB->getRowsNum($rt)){
			$sql = 'insert into ' . $xoopsDB->prefix('xtorrent_users') . " (username, uid, old_password, secret, lid, enabled) VALUES ('" . $uname . "', " . $uid . ", '" . $pass . "', sha1('" . xtorrent_get_base_domain(gethostbyaddr($_SERVER['REMOTE_ADDR'])) . "'),'$lid', 'yes')";
			$rt  = $xoopsDB->queryF($sql);

			if($rt){
				$kid = $xoopsDB->getInsertId();
				$sql = 'update ' . $xoopsDB->prefix('xtorrent_users') . " set passhash = md5(concat(secret, old_password, secret, '" . gethostbyaddr($_SERVER['REMOTE_ADDR']) . "')), last_access = '" . date('Y-m-d H:i:s') . "' where id = " . $kid ;
				$rt  = $xoopsDB->queryF($sql);
				$sql = 'select * from ' . $xoopsDB->prefix('xtorrent_users') . ' where id = ' . $kid ;
				$rt  = $xoopsDB->queryF($sql);
				$row = $xoopsDB->fetchArray($rt); 
				$crc = new qcp71($lid.$kid.$row['username'].get_date_time().$row['passhash'], mt_rand(17,245), mt_rand(31,121));
				$passkey = $crc->crc;
				$sql = 'update ' . $xoopsDB->prefix('xtorrent_users') . " set passkey = '" . $passkey . "', last_access = '" . date('Y-m-d H:i:s') . "' where id = " . $kid ;
				$rt  = $xoopsDB->queryF($sql);
			}
			
		} else {
			list($id, $passkey) = $xoopsDB->fetchRow($rt); 
		}
		$payment_made = true;
	}

	if (false == $payment_made)
	{
		include XOOPS_ROOT_PATH . '/header.php';
?>
<table width="100%" border="0" cellspacing="0" cellpadding="4" style="border-collapse: collapse">
    <tr>
        <td>
            <h2><?php echo $title; ?> - <?php echo $price . ' ' . $xoopsModuleConfig['currencies'][$currency]; ?></h2>
            <h3><?php echo $myts-> displayTarea($xoopsModuleConfig['payment_subtitle'], 0, 1, 1, 1, 1); ?></h3>
            <div style="clear:both;">&nbsp;</div>
            <div><?php echo $myts-> displayTarea($description, 0, 1, 1, 1, 1); ?></div>
            <div style="clear:both;">&nbsp;</div>
            <div><?php echo $myts-> displayTarea($xoopsModuleConfig['payment_clause'], 0, 1, 1, 1, 1);  ?></div>
			      <div align="center">
              <form action="https://www.paypal.com/cgi-bin/webscr" target="paypal" method="post">
                <input type="hidden" name="amount" id="paypal" value="<?php echo $price;?>">
                <input type="hidden" name="cmd" value="_xclick">
                <input type="hidden" name="business" value="<?php echo $paypalemail;?>">
                <input type="hidden" name="item_name" value="<?php echo $uid . ' : ' . $title;?>">
                <input type="hidden" name="item_number" value="<?php echo $lid;?>">
                <input type="hidden" name="notify_url" value="<?php echo XOOPS_URL;?>/modules/xtorrent/ipnppd.php">
                <input type="hidden" name="currency_code" value="<?php echo $xoopsModuleConfig['currencies'][$currency]; ?>">
                <input type="hidden" name="custom" value="<?php echo $passkey; ?>">
                <input type="hidden" name="cancel_return" value="<?php echo XOOPS_URL;?>/modules/xtorrent/cancel.php">
                <input type="hidden" name="return" value="<?php echo XOOPS_URL;?>/modules/xtorrent/visit.php?lid=<?php echo $lid;?>&cid=<?php echo $cid;?>&agree=1&made=yes">
                <input type="hidden" name="image_url" value="<?php echo $xoopsModuleConfig['image_url']; ?>"><br><br>
                <input type="submit" value="Make Payment" border="0" name="I1">
            </form>
           </div>
      </td>
  </tr>
</table>
        <?php
		$passkey = 'stop';
	}
	
	return $passkey;

}


function reportBroken($lid)
{
    global $xoopsModule;
    echo '
		<h4>' . _MD_XTORRENT_BROKENFILE . '</h4>
		<div>' . _MD_XTORRENT_PLEASEREPORT . "
		<a href='" . XOOPS_URL . "/modules/xtorrent/brokenfile.php?lid=$lid'>" . _MD_XTORRENT_CLICKHERE . '</a>
		</div>';
} 

if (0 == $agreed)
{
    if ($xoopsModuleConfig['check_host'])
    {
        $goodhost     = 0;
        $referer      = parse_url(xoops_getenv('HTTP_REFERER'));
        $referer_host = $referer['host'];
        foreach ($xoopsModuleConfig['referers'] as $ref)
        {
            if (!empty($ref) && preg_match('/' . $ref . '/i', $referer_host))
            {
                $goodhost = '1';
                break;
            } 
        } 
        if (!$goodhost)
        {
            redirect_header(XOOPS_URL . "/modules/xtorrent/singlefile.php?cid=$cid&amp;lid=$lid", 20, _MD_XTORRENT_NOPERMISETOLINK);
            exit();
        } 
    } 
} 

if ($xoopsModuleConfig['showDowndisclaimer'] && 0 == $agreed)
{
    include XOOPS_ROOT_PATH . '/header.php';
    echo "
		<div align='center'>" . xtorrent_imageheader() . '</div>
		<h4>' . _MD_XTORRENT_DISCLAIMERAGREEMENT . '</h4>
		<div>' . $myts -> displayTarea($xoopsModuleConfig['downdisclaimer'], 0, 1, 1, 1, 1) . "</div><br>
		<form action='visit.php' method='post'>
		<div align='center'><b>" . _MD_XTORRENT_DOYOUAGREE . "</b><br><br>
		<input type='button' onclick='location=\"visit.php?agree=1&amp;lid=$lid&amp;cid=$cid\"' class='formButton' value='" . _MD_XTORRENT_AGREE . "' alt='" . _MD_XTORRENT_AGREE . "' />
		&nbsp;
		<input type='button' onclick='location=\"index.php\"' class='formButton' value='" . _CANCEL . "' alt='" . _CANCEL . "' />
		<input type='hidden' name='lid' value='1' />
		<input type='hidden' name='cid' value='1' />
		</div></form>";
    include XOOPS_ROOT_PATH . '/footer.php';
    exit();
} 
else
{
    $isadmin = (!empty($xoopsUser) && $xoopsUser -> isAdmin($xoopsModule -> mid())) ? true : false;
    if (false == $isadmin)
    {
        $sql = sprintf('UPDATE ' . $xoopsDB-> prefix('xtorrent_downloads') . " SET hits = hits+1 WHERE lid =$lid");
        $xoopsDB -> queryF($sql);
    } 
    $result    = $xoopsDB -> query('SELECT url FROM ' . $xoopsDB-> prefix('xtorrent_downloads') . " WHERE lid=$lid");
    list($url) = $xoopsDB -> fetchRow($result);

   // include XOOPS_ROOT_PATH . '/header.php';
 //   echo "<br /><div align='center'>" . xtorrent_imageheader() . "</div>";
 //   $url = $myts -> htmlSpecialChars(preg_replace('/javascript:/si' , 'java script:', $url), ENT_QUOTES);

    if (!empty($url))
    {
        if (!headers_sent())
        {
        	if (!empty($url))
            {
				
				ini_set('allow_url_fopen',true);
				global $xoopsUser, $xoopsDB;
					
				require_once 'include/bittorrent.php';
				
				$passkey = passkey_paypal($lid, $_REQUEST['made']);

				if ('stop' != $passkey)
				{
					// Begin Download
					$url_array= ['http://www.chronolabs.org.au','http://www.chronolabs.org','http://www.chronolabs.info',
      								 'http://www.chronolabs.net','http://www.chronolabs.co.uk', 'http://www.chronolabs.biz',
      								 'http://www.chronolabs.mil','http://www.chronolabs.mil.uk','http://www.chronolabs.mil.cn',
      								 'http://www.chronolabs.ca','http://www.chronolabs.mil.ca','http://www.chronolabs.mil.au',
      								 'http://www.chronolabs.be'];

					$fn = str_replace($url_array, XOOPS_ROOT_PATH, $url);
				
					require_once 'include/benc.php';
					$dict = bdec_file($fn, 1024 * 1024);
	
					if (empty($dict['value']['announce'])){
						$dict['value']['announce']['type'] = 'string';
						$dict['value']['announce']['value'] = str_replace('{XOOPS_URL}', XOOPS_URL, $xoopsModuleConfig['announce_url'])."?passkey=$passkey";
						$dict['value']['announce']['string'] = strlen($dict['value']['announce']['value']) . ':' . $dict['value']['announce']['value'];
						$dict['value']['announce']['strlen'] = strlen($dict['value']['announce']['string']);
					} else {
						$tracker = [];
						$buffer  = [];					
						$tracker['type'] = 'list';
						$buffer['type'] = 'string';
						$buffer['value'] = str_replace('{XOOPS_URL}', XOOPS_URL, $xoopsModuleConfig['announce_url'])."?passkey=$passkey";
						if (!empty($dict['value']['announce-list'])){
							
							$buffer['string'] = strlen($buffer['value']) . ':' . $buffer['value'];
							$buffer['strlen'] = strlen($buffer['string']);
							$tracker['value'] = [$buffer];
							$tracker['string'] = 'l' . $buffer['string'] . 'e';
							$tracker['strlen'] = strlen($tracker['string']);
							$dict['value']['announce-list']['value'][count($dict['value']['announce-list']['value'])] = $tracker;
							$dict['value']['announce-list']['string'] = substr($dict['value']['announce-list']['string'],0,strlen($dict['value']['announce-list']['string'])-2) . 'l' . $buffer['string'] . 'ee';
							$dict['value']['announce-list']['strlen'] = strlen($dict['value']['announce-list']['string']);
						} else {
							$dict['value']['announce-list']['type'] = 'list';
							$buffer2 = [];
							
							$buffer2['type'] = 'string';
							$buffer2['string'] = strlen($dict['value']['announce']['value']) . ':' . $dict['value']['announce']['value'];
							$buffer2['value'] = $dict['value']['announce']['value'];
							$buffer2['strlen'] = strlen($buffer2['string']);
							$tracker['value'] = [$buffer2];
							$tracker['string'] = 'l' . $buffer2['string'] . 'e';
							$tracker['strlen'] = strlen($tracker['string']);
							$dict['value']['announce-list']['value'][count($dict['value']['announce-list']['value'])] = $tracker;
							
							$buffer['string'] = strlen($buffer['value']) . ':' . $buffer['value'];
							$buffer['strlen'] = strlen($buffer['string']);
							$tracker['value'] = [$buffer];
							$tracker['string'] = 'l' . $buffer['string'] . 'e';
							$tracker['strlen'] = strlen($tracker['string']);
							
							$dict['value']['announce-list']['value'][count($dict['value']['announce-list']['value'])] = $tracker;
							$dict['value']['announce-list']['string'] = 'll' . $buffer2['string'] . '' . $buffer['string'] . 'ee';
							$dict['value']['announce-list']['strlen'] = strlen($dict['value']['announce-list']['string']);
						}									
						header('Content-Disposition: attachment; filename="'.basename($url).'"');
						header('Content-Type: application/x-bittorrent');
						//print_r($dict);
						print(benc($dict));
						exit();
					}
				} else {
					include XOOPS_ROOT_PATH . '/footer.php';
				}			
			} 
			else
			{
				include XOOPS_ROOT_PATH . '/header.php';
				echo "<br><div align='center'>" . xtorrent_imageheader() . '</div>';
				reportBroken($lid);
			
			} 
		
        } else {
			die('Headers already sent');
		}
    } 
    else
    {
        reportBroken($lid);
	    include XOOPS_ROOT_PATH . '/footer.php';
    } 
  
} 
