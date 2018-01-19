<?php

include '../../../mainfile.php';

$source   = $_GET['source'];
$numitems = isset($_GET['numitems']) ? $_GET['numitems'] : 15;
header('Content-type: text/xml');

include_once XOOPS_ROOT_PATH . '/class/xoopsform/grouppermform.php';

function rss_data($cid, $numitems)
{
    global $xoopsDB, $xoopsModule, $xoopsUser, $xoopsModuleConfig;

    $block = [];
    $myts  = MyTextSanitizer::getInstance();

    $modhandler        = xoops_gethandler('module');
    $xoopsModule       = $modhandler->getByDirname('xtorrent');
    $config_handler    = xoops_gethandler('config');
    $xoopsModuleConfig = $config_handler->getConfigsByCat(0, $xoopsModule->getVar('mid'));

    $groups        = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
    $gperm_handler = xoops_gethandler('groupperm');

    if (!empty($cid) || $cid > 0) {
        $pif = " WHERE b.cid = $cid";
    }
    $rss      = [];
    $result   = $xoopsDB->query('SELECT a.lid, a.cid, a.title, a.date, a.description, a.hits, a.version, a.platform, a.mirror, a.votes, a.publisher, a.updated, a.license, b.title as category FROM '
                                . $xoopsDB->prefix('xtorrent_downloads')
                                . ' a INNER JOIN '
                                . $xoopsDB->prefix('xtorrent_cat')
                                . " b ON a.cid = b.cid $pif ORDER BY published DESC ", $numitems, 0);
    $rep      = ['<br>', '<br/>', '<br />'];
    $category = [];
    while ($myrow = $xoopsDB->fetchArray($result)) {
        $download                = [];
        $download['title']       = strip_tags($myts->displayTarea($myrow['title'], 0, 0, 1));
        $download['description'] = htmlspecialchars(htmlspecialchars_decode($myts->displayTarea($myrow['description'], 1, 1, 1)));
        $download['url']         = XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/visit.php?agree=1&lid=' . $myrow['lid'] . '&cid=' . $myrow['cid'];
        $download['dossier_url'] = XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/singlefile.php?lid=' . $myrow['lid'] . '&cid=' . $myrow['cid'];
        $download['date']        = formatTimestamp($myrow['date'], 'D, d-m-y H:i:s e');
        $download['hits']        = $myrow['hits'];
        $download['episode']     = $myrow['version'];
        $download['series']      = $xoopsModuleConfig['platform'][$myrow['platform']];
        $download['mirror']      = $myrow['mirror'];
        $download['votes']       = $myrow['votes'];
        $download['publisher']   = $myrow['publisher'];
        $download['updated']     = formatTimestamp($myrow['updated'], 'D, d-m-y H:i:s e');
        $download['license']     = $xoopsModuleConfig['license'][$myrow['license']];
        $download['category']    = $myrow['category'];
        $category[]              = ucfirst($myrow['category']);
        $rss[$j++]               = $download;
    }
    $rss['category'] = array_unique($category);
    return $rss;
}

$myts = MyTextSanitizer::getInstance();

if (!function_exists('xoops_sef')) {
    function xoops_sef($datab, $char = '-')
    {
        $replacement_chars = [];
        $accepted          = [
            'a',
            'b',
            'c',
            'd',
            'e',
            'f',
            'g',
            'h',
            'i',
            'j',
            'k',
            'l',
            'm',
            'n',
            'm',
            'o',
            'p',
            'q',
            'r',
            's',
            't',
            'u',
            'v',
            'w',
            'x',
            'y',
            'z',
            '0',
            '9',
            '8',
            '7',
            '6',
            '5',
            '4',
            '3',
            '2',
            '1'
        ];
        for ($i = 0; $i < 256; $i++) {
            if (!in_array(strtolower(chr($i)), $accepted)) {
                $replacement_chars[] = chr($i);
            }
        }
        $return_data = str_replace($replacement_chars, $char, $datab);
        #print $return_data . "<BR><BR>";
        return $return_data;
    }
}

global $xoopsDB;

$sql = 'SELECT cid, title FROM ' . $xoopsDB->prefix('xtorrent_cat') . " FROM cid = '" . $_GET['source'] . "'";
list($cid, $cat) = $xoopsDB->fetchRow($xoopsDB->query($sql));

if (empty($cid) && empty($cat)) {
    $sql = 'SELECT cid, title FROM ' . $xoopsDB->prefix('xtorrent_cat') . " FROM title LIKE '" . xoops_sef($_GET['source'], '_') . "'";
    list($cid, $cat) = $xoopsDB->fetchRow($xoopsDB->query($sql));
}

if ('' == $cat) {
    $cat = 'Latest Torrents';
}

$rssfeed_data = rss_data($source, $numitems);

header('Content-type: text/xml; charset=UTF-8');
?><?php echo '<?xml version="1.0" encoding="iso-8859-1"?>' . chr(10) . chr(13); ?>
<rss version="2.0">

    <channel>

        <?php if (!isset($_REQUEST['ms'])) {
    ?>
            <description><?php echo htmlspecialchars($xoopsConfig['slogan']) . ' ' . htmlspecialchars(implode(', ', $rssfeed_data['category'])); ?></description>
            <lastBuildDate><?php echo date('D, d-m-y H:i:s e', time()); ?></lastBuildDate>
            <docs>http://backend.userland.com/rss/</docs>
            <generator><?php echo(htmlspecialchars($xoopsConfig['sitename'])); ?></generator>
            <category><?php echo implode(', ', $rssfeed_data['category']); ?></category>
            <managingEditor><?php echo $xoopsConfig['adminmail']; ?></managingEditor>
            <webMaster><?php echo $xoopsConfig['adminmail']; ?></webMaster>
            <?php
} ?>
        <language>en</language>
        <?php if (!isset($_REQUEST['ms'])) {
        ?>
            <image>
                <title><?php echo(htmlspecialchars($xoopsConfig['sitename'])); ?></title>
                <url><?php echo XOOPS_URL; ?>/images/logo.png</url>
                <link><?php echo XOOPS_URL; ?>/</link>
                <width>230</width>
                <height>170</height>
            </image>
            <title>RSS Feed | <?php echo htmlspecialchars($xoopsConfig['sitename']) . ' | ' . ucfirst($rssfeed_data['category'][0]); ?> </title>
            <link><?php echo XOOPS_URL; ?></link>
            <?php
    } ?>
        <?php
        foreach ($rssfeed_data as $item) {
            ?>
            <item>
                <title><?php echo htmlspecialchars($item['title']); ?></title>
                <link><?php echo htmlspecialchars($item['url']); ?></link>
                <hits><?php echo htmlspecialchars($item['hits']); ?></hits>
                <episode><?php echo htmlspecialchars($item['episode']); ?></episode>
                <series><?php echo htmlspecialchars($item['series']); ?></series>
                <mirror><?php echo htmlspecialchars($item['mirror']); ?></mirror>
                <votes><?php echo htmlspecialchars($item['votes']); ?></votes>
                <publisher><?php echo htmlspecialchars($item['publisher']); ?></publisher>
                <updated><?php echo htmlspecialchars($item['updated']); ?></updated>
                <license><?php echo htmlspecialchars($item['license']); ?></license>
                <description><?php echo $item['description']; ?></description>
                <?php if (!isset($_REQUEST['ms'])) {
                ?>
                    <guid><?php echo htmlspecialchars($item['dossier_url']); ?></guid>
                    <category><?php echo $item['category']; ?></category>
                    <?php
            } ?>
                <pubDate><?php echo $item['date']; ?></pubDate>
            </item>
            <?php
        } ?>
    </channel>
</rss>
