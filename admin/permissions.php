<?php

require_once __DIR__ . '/admin_header.php';
include_once XOOPS_ROOT_PATH . '/class/xoopsform/grouppermform.php';

  xoops_cp_header();
  $adminObject = \Xmf\Module\Admin::getInstance();
  $adminObject->displayNavigation(basename(__FILE__));

	echo "<fieldset><legend style='font-weight:bold;color:#900;'>" . _AM_XTORRENT_PERM_CPERMISSIONS . "</legend>
		    <div style='padding: 2px;'>";

  $cat_form = new XoopsGroupPermForm('', $xoopsModule->getVar('mid'), 'xtorrentownCatPerm', _AM_XTORRENT_PERM_CSELECTPERMISSIONS );
  $result = $xoopsDB->query("SELECT cid, pid, title FROM " . $xoopsDB->prefix("xtorrent_cat"));
  if ($xoopsDB->getRowsNum($result))
  {
      while ($cat_row = $xoopsDB->fetcharray($result))
      {
              $cat_form->addItem($cat_row['cid'], $cat_row['title'], $cat_row['pid']);
      } 
      echo $cat_form->render();
  } 
  else
  {
      echo "<div><b>" . _AM_XTORRENT_PERM_CNOCATEGORY . "</b></div>";
  } 
  echo "</div></fieldset><br>";
  unset ($cat_form);
  
  /*
  * File permission form
  */ 
	echo "<fieldset><legend style='font-weight:bold;color:#900;'>" . _AM_XTORRENT_PERM_FPERMISSIONS . "</legend>
		    <div style='padding: 2px;'>";

  $file_form = new XoopsGroupPermForm('', $xoopsModule->getVar('mid'), 'xtorrentownFilePerm', _AM_XTORRENT_PERM_FSELECTPERMISSIONS);
  $result2   = $xoopsDB->query("SELECT lid, title FROM " . $xoopsDB->prefix('xtorrent_downloads'));
  if ($xoopsDB->getRowsNum($result2))
  {
      while ($file_row = $xoopsDB->fetcharray($result2))
      {
  	    $file_form->addItem($file_row['lid'], $file_row['title'], 0);
      } 
      echo $file_form->render();
  } 
  else
  {
      echo "<div><b>" . _AM_XTORRENT_PERM_FNOFILES . "</b></div>";
  } 
  echo "</div></fieldset><br>";
  unset ($file_form);
  echo "<fieldset><legend style='font-weight:bold;color:#900;'>" . _AM_XTORRENT_NOTE . "</legend>";
  echo _AM_XTORRENT_PERM_PERMSNOTE;
  echo "</fieldset><br>";

  require_once __DIR__ . '/admin_footer.php';
