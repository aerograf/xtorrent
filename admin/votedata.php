<?php

require_once __DIR__ . '/admin_header.php';

$op = "";
if (isset($_POST))
{
    foreach ($_POST as $k => $v)
    {
        ${$k} = $v;
    }
}

if (isset($_GET))
{
    foreach ($_GET as $k => $v)
    {
        ${$k} = $v;
    }
}

if (isset($_GET['op'])) {
    $op = $_GET['op'];
}
if (isset($_POST['op'])) {
    $op = $_POST['op'];
}

switch ($op)
{
    case 'delVote':
        global $xoopsDB, $_GET;
        $rid     = (int)$_GET['rid'];
        $lid     = (int)$_GET['lid'];
        $sql     = $xoopsDB->queryF('DELETE FROM ' . $xoopsDB->prefix('xtorrent_votedata') . ' WHERE ratingid = ' . $rid);
        $xoopsDB -> query($sql);
        xtorrent_updaterating($lid);
        redirect_header('votedata.php', 1, _AM_XTORRENT_VOTEDELETED);
        break;

    case 'main':
    default:
        global $xoopsDB, $imagearray;

		    $start         = isset($_GET['start']) ? (int)$_GET['start'] : 0;
        $useravgrating = '0';
        $uservotes     = '0';

		    $sql     = 'SELECT * FROM ' . $xoopsDB->prefix('xtorrent_votedata') . ' ORDER BY ratingtimestamp DESC';
        $results = $xoopsDB->query($sql, 20, $start);
		    $votes   = $xoopsDB->getRowsNum($results);
		
        $sql           = 'SELECT rating FROM ' . $xoopsDB->prefix('xtorrent_votedata') . '';
        $result2       = $xoopsDB->query($sql, 20, $start);
		    $uservotes     = $xoopsDB->getRowsNum($result2);
        $useravgrating = 0;

        while (list($rating2) = $xoopsDB->fetchRow($result2))
        {
            $useravgrating += $rating2;
        }
        if ($useravgrating > 0)
        {
           $useravgrating /= $uservotes;
           $useravgrating  = number_format($useravgrating, 2);
        }

        xoops_cp_header();
        $adminObject = \Xmf\Module\Admin::getInstance();
        $adminObject -> displayNavigation(basename(__FILE__)); 		

      	echo "<fieldset><legend style='font-weight:bold;color:#900;'>" . _AM_XTORRENT_VOTE_DISPLAYVOTES . "</legend>
          		<div style='padding:4px;'>
              <ul>
              <li style='padding:4px;'><b>" . _AM_XTORRENT_VOTE_USERAVG . ':</b> ' . $useravgrating . "</li>
          		<li style='padding:4px;'><b>" . _AM_XTORRENT_VOTE_TOTALRATE . ':</b> ' . $uservotes . "</li>
          		<li style='padding:4px;'>" . $imagearray['deleteimg'] . ' ' . _AM_XTORRENT_VOTE_DELETEDSC . "</li>
              </ul></div><br>
          		<table class='outer' style='width:100%;'><tr>";

        $headingarray = [
                         _AM_XTORRENT_VOTE_ID,
                         _AM_XTORRENT_VOTE_USER,
                         _AM_XTORRENT_VOTE_IP,
                         _AM_XTORRENT_VOTE_FILETITLE,
                         _AM_XTORRENT_VOTE_RATING,
                         _AM_XTORRENT_VOTE_DATE,
                         _AM_XTORRENT_MINDEX_ACTION
                         ];
        for($i = 0; $i <= count($headingarray)-1; $i++)
        {
            echo "<th style='text-align:center;'><b>" . $headingarray[$i] . '</th>';
        }
        echo '</tr>'; 

        if (0 == $votes)
        {
            echo "<tr><td colspan='7' class='head' style='text-align:center;'>" . _AM_XTORRENT_VOTE_NOVOTES . '</td></tr>';
        }
        while (list($ratingid, $lid, $ratinguser, $rating, $ratinghostname, $ratingtimestamp) = $xoopsDB->fetchRow($results))
        {
            $sql            = 'SELECT title FROM ' . $xoopsDB->prefix('xtorrent_downloads') . ' WHERE lid=' . $lid . '';
            $down_array     = $xoopsDB->fetchArray($xoopsDB->query($sql));
			
            $formatted_date = formatTimestamp($ratingtimestamp, $xoopsModuleConfig['dateformat']);
            $ratinguname    = XoopsUser::getUnameFromId($ratinguser);
      	echo "<tr>
          		<td class='head' style='text-align:center;'>" . $ratingid . "</td>
          		<td class='even' style='text-align:center;'>" . $ratinguname . "</td>
          		<td class='even' style='text-align:center;'>" . $ratinghostname . "</td>
          		<td class='even' style='text-align:center;'>" . $down_array['title'] . "</td>
          		<td class='even' style='text-align:center;'>" . $rating . "</td>
          		<td class='even' style='text-align:center;'>" . $formatted_date . "</td>
          		<td class='even' style='text-align:center;'>
              <b><a href='votedata.php?op=delVote&amp;lid=" . $lid . '&amp;rid=' . $ratingid . "'>" . $imagearray['deleteimg'] . '</a></b></td>
          		</tr>';
        }
        echo '</table></fieldset>';
        
    		//Include page navigation
    		include_once XOOPS_ROOT_PATH . '/class/pagenav.php';
        $page = ($votes > 20) ? _AM_XTORRENT_MINDEX_PAGE : '';
        $pagenav = new XoopsPageNav($page, 20, $start, 'start');
        echo "<div style='padding:8px;float:right;'>" . $page . '' . $pagenav -> renderNav() . '</div>';
        break;
}

require_once __DIR__ . '/admin_footer.php';
