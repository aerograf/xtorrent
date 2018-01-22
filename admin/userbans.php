<?php

require_once __DIR__ . '/admin_header.php';

global $_GET, $_POST;

$g_id = 1;

xoops_cp_header();
$adminObject = \Xmf\Module\Admin::getInstance();
$adminObject -> displayNavigation(basename(__FILE__)); 

$member_handler     = xoops_gethandler('member');
$thisgroup          = $member_handler -> getGroup($g_id);
$name_value         = $thisgroup -> getVar("name", "E");
$desc_value         = $thisgroup -> getVar("description", "E");
$moduleperm_handler = xoops_gethandler('groupperm');

$usercount      = $member_handler -> getUserCount(new Criteria('level', 0, '>'));
$member_handler = xoops_gethandler('member');
$membercount    = $member_handler -> getUserCountByGroup($g_id);

$members        = $member_handler -> getUsersByGroup($g_id, true);
$mlist          = [];
$mcount         = count($members);
for ($i = 0; $i < $mcount; $i++)
{
    $mlist[$members[$i] -> getVar('uid')] = $members[$i] -> getVar('uname');
} 
$criteria  = new Criteria('level', 0, '>');
$criteria  -> setSort('uname');
$userslist = $member_handler -> getUserList($criteria);
$users     = array_diff($userslist, $mlist);

echo "<fieldset><legend style='font-weight:bold;color:#900;'>" . _AM_XTORRENT_EDITBANNED . "</legend>";
echo '<table class="outer" style="width:100%;">
		  <tr><th style="text-align:center;">' . _AM_XTORRENT_NONBANNED . '</th>';

echo '<th></th><th style="text-align:center;">' . _AM_XTORRENT_BANNED . '</th>';
echo '</tr>
  		<tr><td class="even">
  		<form action="admin.php" method="post">
  		<select name="uids[]" size="10" multiple="multiple">';
    foreach ($mlist as $m_id => $m_name)
    {
        echo '<option value="' . $m_id . '">' . $m_name . '</option>';
    } 

echo "</select>";
echo "</td><td align='center' class='odd'>
  		<input type='hidden' name='op' value='addUser' >
  		<input type='hidden' name='fct' value='groups' >
  		<input type='hidden' name='groupid' value='" . $thisgroup -> getVar("groupid") . "' >
  		<input type='submit' name='submit' value='" . _AM_XTORRENT_BADD . "' >
  		</form><br>
  		<form action='admin.php' method='post'>
  		<input type='hidden' name='op' value='delUser'>
  		<input type='hidden' name='fct' value='groups'>
  		<input type='hidden' name='groupid' value='" . $thisgroup -> getVar("groupid") . "' >
  		<input type='submit' name='submit' value='" . _AM_XTORRENT_BDELETE . "' >
  		</td>
  		<td class='even'>";
echo "<select name='uids[]' size='10' multiple='multiple'>";
foreach ($users as $u_id => $u_name)
{
    echo '<option value="' . $u_id . '">' . $u_name . '</option>';
} 
echo "</select>";
echo "</td></tr></form></table></fieldset>";
require_once __DIR__ . '/admin_footer.php';
