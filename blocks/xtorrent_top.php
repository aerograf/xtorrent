<?php

include_once XOOPS_ROOT_PATH . '/class/xoopsform/grouppermform.php';

function b_XTORRENT_top_show($options)
{
    global $xoopsDB, $xoopsModule, $xoopsUser;

    $block             = [];
    $myts              = MyTextSanitizer::getInstance();
    $modhandler        = xoops_gethandler('module');
    $xoopsModule       = $modhandler->getByDirname('xtorrent');
    $config_handler    = xoops_gethandler('config');
    $xoopsModuleConfig = $config_handler->getConfigsByCat(0, $xoopsModule->getVar('mid'));
    $groups            = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
    $gperm_handler     = xoops_gethandler('groupperm');
    $result            = $xoopsDB->query('SELECT lid, cid, title, date, hits FROM ' . $xoopsDB->prefix('xtorrent_downloads') . ' WHERE status > 0 AND offline = 0 ORDER BY ' . $options[0] . ' DESC', $options[1], 0);
    while ($myrow = $xoopsDB->fetchArray($result)) {
        if ($gperm_handler->checkRight('xtorrentownFilePerm', $myrow['lid'], $groups, $xoopsModule->getVar('mid'))) {
            $download = [];
            $title    = $myts->htmlSpecialChars($myrow['title']);
            if (!XOOPS_USE_MULTIBYTES) {
                if (strlen($myrow['title']) >= $options[2]) {
                    $title = $myts->htmlSpecialChars(substr($myrow['title'], 0, $options[2] - 1)) . '...';
                }
            }
            $download['id']    = $myrow['lid'];
            $download['cid']   = $myrow['cid'];
            $download['title'] = $title;
            if ('date' == $options[0]) {
                $download['date'] = formatTimestamp($myrow['date'], $xoopsModuleConfig['dateformat']);
            } elseif ('hits' == $options[0]) {
                $download['hits'] = $myrow['hits'];
            }
            $download['dirname']  = $xoopsModule->dirname();
            $block['downloads'][] = $download;
        }
    }
    return $block;
}

function b_XTORRENT_top_edit($options)
{
    $form = '' . _MB_XTORRENT_DISP . '&nbsp;';
    $form .= "<input type='hidden' name='options[]' value='";
    if ('date' == $options[0]) {
        $form .= "date'";
    } else {
        $form .= "hits'";
    }
    $form .= ' />';
    $form .= "<input type='text' name='options[]' value='" . $options[1] . "' />&nbsp;" . _MB_XTORRENT_FILES . '';
    $form .= '&nbsp;<br>' . _MB_XTORRENT_CHARS . "&nbsp;<input type='text' name='options[]' value='" . $options[2] . "' />&nbsp;" . _MB_XTORRENT_LENGTH . '';
    return $form;
}
