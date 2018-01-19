<?php

include_once '../../../include/cp_header.php';
include_once 'mygrouppermform.php';
include_once XOOPS_ROOT_PATH . '/class/xoopsblock.php';
include_once XOOPS_ROOT_PATH . '/modules/xtorrent/include/functions.php';

$xoops_system_url  = XOOPS_URL . '/modules/system';
$xoops_system_path = XOOPS_ROOT_PATH . '/modules/system';
// language files
$language = $xoopsConfig['language'];
if (! file_exists("$xoops_system_path/language/$language/admin/blocksadmin.php")) {
    $language = 'english';
}

include_once "$xoops_system_path/language/$language/admin.php";
include_once "$xoops_system_path/language/$language/admin/blocksadmin.php";
$group_defs = file("$xoops_system_path/language/$language/admin/groups.php");
foreach ($group_defs as $def) {
    if (strstr($def, '_AM_XTORRENT_ACCESSRIGHTS') || strstr($def, '_AM_XTORRENT_ACTIVERIGHTS')) {
        eval($def);
    }
}
// check $xoopsModule
if (! is_object($xoopsModule)) {
    redirect_header(XOOPS_URL . '/user.php', 1, _NOPERM);
}
// get blocks owned by the module
$block_arr = XoopsBlock::getByModule($xoopsModule->mid());

function list_blocks()
{
    global $xoopsUser , $xoopsConfig , $xoopsDB;
    global $block_arr , $xoops_system_url;
    require_once __DIR__ . '/admin_header.php';
    
    $side_descs = [0 => _AM_XTORRENT_SBLEFT, 1 => _AM_XTORRENT_SBRIGHT, 3 => _AM_XTORRENT_CBLEFT, 4 => _AM_XTORRENT_CBRIGHT, 5 => _AM_XTORRENT_CBCENTER];
    // displaying TH
    echo "<table width='100%' class='outer' cellpadding='4' cellspacing='1'>
	        <tr valign='middle'>";
    $headingarray = [_AM_XTORRENT_BLKDESC, _AM_XTORRENT_TITLE, _AM_XTORRENT_SIDE, _AM_XTORRENT_WEIGHT, _AM_XTORRENT_VISIBLE, _AM_XTORRENT_ACTION];
    for ($i = 0; $i <= count($headingarray)-1; $i++) {
        $align = 'center';
        echo "<th style='text-align:" . $align . ";'><b>" . $headingarray[$i] . '</th>';
    }
    echo '</tr>';
    // blocks displaying loop
    $class = 'even';
    foreach (array_keys($block_arr) as $i) {
        $visible   = ($block_arr[$i]->getVar('visible') == 1) ? _YES : _NO;
        $weight    = $block_arr[$i]->getVar('weight');
        $side_desc = $side_descs[$block_arr[$i]->getVar('side')];
        $title     = $block_arr[$i]->getVar('title');
        if ($title == '') {
            $title = '&nbsp;';
        }
        $name      = $block_arr[$i]->getVar('name');
        $bid       = $block_arr[$i]->getVar('bid');

        echo "<tr>
              <td class='" . $class . "'>" . $name . "</td>
              <td class='" . $class . "'>" . $title . "</td>
              <td class='" . $class . "' style='text-align:center;'>" . $side_desc . "</td>
              <td class='" . $class . "' style='text-align:center;'>" . $weight . "</td>
              <td class='" . $class . "' style='white-space:nowrap;text-align:center;'>" . $visible . "</td>
              <td class='" . $class . "' style='text-align:center;'>
              <a href='" . $xoops_system_url . '/admin.php?fct=blocksadmin&amp;op=edit&amp;bid=' . $bid . "' target='_blank'>" . $imagearray['editimg'] . '</a></td>
              </tr>';
        $class = ($class == 'even') ? 'odd' : 'even';
    }
    echo "<tr><th colspan='7'></th></tr></table>";
}

function list_groups()
{
    global $xoopsUser , $xoopsConfig , $xoopsDB;
    global $xoopsModule , $block_arr , $xoops_system_url;

    foreach (array_keys($block_arr) as $i) {
        $item_list[ $block_arr[$i]->getVar('bid') ] = $block_arr[$i]->getVar('title');
    }
    $form = new MyXoopsGroupPermForm('', 1, 'block_read', _AM_SYSTEM_ADGS);
    $form->addAppendix('module_admin', $xoopsModule->mid(), $xoopsModule->name() . ' ' . _AM_XTORRENT_ACTIVERIGHTS);
    $form->addAppendix('module_read', $xoopsModule->mid(), $xoopsModule->name() . ' ' . _AM_XTORRENT_ACCESSRIGHTS);
    foreach ($item_list as $item_id => $item_name) {
        $form->addItem($item_id, $item_name);
    }
    echo $form->render();
}

  if (! empty($_POST['submit'])) {
      include 'mygroupperm.php';
      redirect_header(XOOPS_URL . '/modules/xtorrent/admin/myblocksadmin.php', 1, _AM_SYSTEM_DBUPDATED);
  }

  xoops_cp_header();
  $adminObject = \Xmf\Module\Admin::getInstance();
  $adminObject->displayNavigation(basename(__FILE__));

  list_blocks();
  list_groups();
  require_once __DIR__ . '/admin_footer.php';
