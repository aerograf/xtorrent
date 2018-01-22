<?

include("../../../mainfile.php");

$source   = $_GET['source'];
$numitems = isset($_GET['numitems']) ? $_GET['numitems'] : 15;
header('Content-type: text/xml');

include_once XOOPS_ROOT_PATH . '/class/xoopsform/grouppermform.php';

function rss_data($cid, $numitems)
{
    global $xoopsDB, $xoopsModule, $xoopsUser;

    $block = [];
    $myts  = MyTextSanitizer::getInstance();

    $modhandler        = xoops_gethandler('module');
    $xoopsModule       = $modhandler->getByDirname("xtorrent");
    $config_handler    = xoops_gethandler('config');
    $xoopsModuleConfig = $config_handler->getConfigsByCat(0, $xoopsModule->getVar('mid'));

    $groups        = (is_object($xoopsUser)) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
    $gperm_handler = xoops_gethandler('groupperm'); 

	if (!empty($cid)||$cid>0){
		$pif = " WHERE cid = $cid";
	}
	$rss    = [];
  $result = $xoopsDB->query("SELECT lid, cid, title, date, description, hits FROM " . $xoopsDB->prefix('xtorrent_downloads') . " $pif ORDER BY published DESC ", $numitems, 0);
	$rep    = ["<br>","<br/>","<br />"];
    while($myrow = $xoopsDB->fetchArray($result))
    {
       
            $download                = [];
            $download['title']       = strip_tags($myts->displayTarea($myrow["title"], 0, 0, 1));
            $download['description'] = strip_tags($myts->displayTarea($myrow["description"], 0, 0, 1));			
            $download['url']         = XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/visit.php?lid=' . $myrow['lid'];
			      $download['dossier_url'] = XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/singlefile.php?lid=' . $myrow['lid'];
            $download['date']        = formatTimestamp($myrow['date'], 'D, d-m-y H:i:s e');
            $download['hits']        = $myrow['hits'];
			      $rss[$j++]               = $download;
    }
    return $rss;
}

$myts = MyTextSanitizer::getInstance();
	
if (!function_exists('xoops_sef')){
	function xoops_sef($datab, $char ='-')
	{
		$replacement_chars = [];
		$accepted          = [
                          "a",
                          "b",
                          "c",
                          "d",
                          "e",
                          "f",
                          "g",
                          "h",
                          "i",
                          "j",
                          "k",
                          "l",
                          "m",
                          "n",
                          "m",
                          "o",
                          "p",
                          "q",
                          "r",
                          "s",
                          "t",
                          "u",
                          "v",
                          "w",
                          "x",
                          "y",
                          "z",
                          "0",
                          "9",
                          "8",
                          "7",
                          "6",
                          "5",
                          "4",
                          "3",
                          "2",
                          "1"
                          ];
		for($i=0; $i<256; $i++){
			if (!in_array(strtolower(chr($i)), $accepted))
				$replacement_chars[] = chr($i);
		}
		$return_data = (str_replace($replacement_chars, $char,$datab));
		#print $return_data . "<br><br>";
		return($return_data);
	
	}
}

global $xoopsDB;

if (!empty($source)){
	$sql             = "SELECT cid, title FROM " . $xoopsDB->prefix('xtorrent_cat') . " FROM title LIKE '" . xoops_sef($source,"_") . "'";
	list($cid, $cat) = $xoopsDB->fetchRow($xoopsDB->query($sql));
	
	if (is_numeric($source)){

		$sql             = "SELECT cid, title FROM " . $xoopsDB->prefix('xtorrent_cat') . " FROM cid = '" . xoops_sef($source,"_") . "'";
		list($cid, $cat) = $xoopsDB->fetchRow($xoopsDB->query($sql));

	}
}	

if ($cat=="")
	$cat = "Latest Torrents";

$rssfeed_data = rss_data($cid, $numitems);

header("Content-type: text/xml; charset=UTF-8");  
?><?php echo '<?xml version="1.0" encoding="iso-8859-1"?>'.chr(10).chr(13); ?>
<rss version="2.0"> 

<channel>

 <description><? echo (htmlspecialchars($xoopsConfig['slogan']));?></description>
 <lastBuildDate><? echo date('D, d-m-y H:i:s e',time());?></lastBuildDate>
 <docs>http://backend.userland.com/rss/</docs>
 <generator><? echo (htmlspecialchars($xoopsConfig['sitename']));?></generator>
 <category><? echo ucfirst($cat); ?></category>
 <managingEditor><? echo $xoopsConfig['adminmail'];?></managingEditor>
 <webMaster><? echo $xoopsConfig['adminmail'];?></webMaster>
 <language>en</language>
 <image>
      <title><? echo (htmlspecialchars($xoopsConfig['sitename']));?></title>
      <url><? echo XOOPS_URL; ?>/images/logo.png</url>
      <link><? echo XOOPS_URL; ?>/</link>
  	  <width>230</width>
	  <height>170</height>
  </image>
 <title>RSS Feed | <? echo htmlspecialchars($xoopsConfig['sitename']).' | '.ucfirst($cat);?> </title> 
 <link><? echo XOOPS_URL; ?></link>
<?
foreach ($rssfeed_data as $item) {
?>
 <item>
 <title><? echo htmlspecialchars(($item['title']));?></title> 
 <link><? echo $item['url']; ?></link>
 <description><? echo $item['description']; ?></description> 
 <pubDate><? echo $item['date'];?></pubDate>
 </item>
<?
}?>
 </channel>
 </rss>
