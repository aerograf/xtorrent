<?php

use XoopsModules\Xtorrent;

$moduleDirName = basename(dirname(__DIR__));

$helper = Xtorrent\Helper::getInstance();
$helper->loadLanguage('admin');
$helper->loadLanguage('modinfo');

$modversion     = [
    'version'             => 4.00,
    'module_status'       => 'Alpha 1',
    'release_date'        => '2018/01/17',
    'name'                => '_MI_TORRENT_NAME',
    'description'         => '_MI_TORRENT_DESC',
    'dirname'             => $moduleDirName,
    'help'                => 'xtorrent.tpl',
    'author'              => 'Wishcraft, LordPeter, Eparcyl, Aerograf',
    'credits'             => 'X-Torrent extrapolated from WF-Downloads',
    'license'             => 'GNU GPL 2.0',
    'license_url'         => 'www.gnu.org/licenses/gpl-2.0.html/',
    'image'               => 'assets/images/logoModule_b.png',
    'module_website_url'  => 'www.xoops.org/',
    'module_website_name' => 'XOOPS',
    'demo_site_url'       => 'https://xoops.org/newbb/',
    'demo_site_name'      => 'XOOPS Project',
    'support_site_url'    => 'https://xoops.org/newbb/',
    'support_site_name'   => 'XOOPS Project',
    'min_php'             => '5.5',
    'min_xoops'           => '2.5.9',
    'min_admin'           => '1.2',
    'min_db'              => ['mysql' => '5.5'],
    'modicons16'          => 'assets/images/icons/16',
    'modicons32'          => 'assets/images/icons/32',
    'sqlfile'             => ['mysql' => 'sql/xtorrent.sql'],
    'tables'              => [
    $moduleDirName . '_' . 'broken',
    $moduleDirName . '_' . 'cat',
    $moduleDirName . '_' . 'downloads',
    $moduleDirName . '_' . 'mod',
    $moduleDirName . '_' . 'votedata',
    $moduleDirName . '_' . 'indexpage',
    $moduleDirName . '_' . 'reviews',
    $moduleDirName . '_' . 'mimetypes',
    $moduleDirName . '_' . 'tracker',
    $moduleDirName . '_' . 'torrent',
    $moduleDirName . '_' . 'poll',
    $moduleDirName . '_' . 'files',
    $moduleDirName . '_' . 'peers',
    $moduleDirName . '_' . 'users',
    $moduleDirName . '_' . 'soap_transactions',
    $moduleDirName . '_' . 'soap_catmatch',
    $moduleDirName . '_' . 'financial',
    $moduleDirName . '_' . 'payments',
    $moduleDirName . '_' . 'translog'
    ],
    'system_menu'         => 1,
    'hasAdmin'            => 1,
    'adminindex'          => 'admin/index.php',
    'adminmenu'           => 'admin/menu.php',
    'hasMain'             => 1,
    'hasSearch'           => 1,
    'search'              => [
        'file' => 'include/search.inc.php',
        'func' => 'newbb_search',
    ],
    'use_smarty'          => 1
];
/*
* added by Liquid. Based on code by Marcan
*/
/* 
$modversion['author_realname'] = "Simon Roberts";
$modversion['author_website_url'] = "http://www.chronolabs.org.au";
$modversion['author_website_name'] = "Chronolabs Australia";
$modversion['author_email'] = "simon@chronolabs.org.au";
$modversion['demo_site_url'] = "Chronolabs International";
$modversion['demo_site_name'] = "http://www.chronolabs.org.au/modules/xtorrent/";
$modversion['support_site_url'] = "http://www.chronolabs.org.au/modules/newbb/viewforum.php?forum=7";
$modversion['support_site_name'] = "x-Torrent";
$modversion['submit_bug'] = "http://www.chronolabs.org.au/modules/newbb/viewforum.php?forum=7";
$modversion['submit_feature'] = "http://www.chronolabs.org.au/modules/newbb/viewforum.php?forum=7";
$modversion['usenet_group'] = "sci.chronolabs";
$modversion['maillist_announcements'] = "";
$modversion['maillist_bugs'] = "";
$modversion['maillist_features'] = "";

$modversion['warning'] = _MI_TORRENT_WARNINGTEXT;
$modversion['author_credits'] = _MI_TORRENT_AUTHOR_CREDITSTEXT;
*/

// Blocks
$modversion['blocks'] = [];
$modversion['blocks'][] = [
         'file'         => 'xtorrent_top.php',
         'name'         => '_MI_TORRENT_BNAME1',
         'description'  => 'Shows recently added donwload files',
         'show_func'    => 'b_XTORRENT_top_show',
         'edit_func'    => 'b_XTORRENT_top_edit',
         'options'      => 'date|10|19',
         'template'     => 'xtorrent_block_new.tpl'
];
$modversion['blocks'][] = [
         'file'         => 'xtorrent_top.php',
         'name'         => _MI_TORRENT_BNAME2,
         'description'  => 'Shows most downloaded files',
         'show_func'    => 'b_XTORRENT_top_show',
         'edit_func'    => 'b_XTORRENT_top_edit',
         'options'      => 'hits|10|19',
         'template'     => 'xtorrent_block_top.tpl'
];

global $xoopsModuleConfig, $xoopsUser, $xoopsDLModule;

$submissions = 0;

if (is_object($xoopsUser) && isset($xoopsModuleConfig['submissions']))
{
	$groups = $xoopsUser->getGroups();
	if (array_intersect($xoopsModuleConfig['submitarts'], $groups))
	{
		$submissions = 1;
	}
}
else
{
	if (isset($xoopsModuleConfig['anonpost']) && $xoopsModuleConfig['anonpost'] == 1)
	{
		$submissions = 1;
	}
}


$i = 0;
if ($submissions)
{
    $i++;
    $modversion['sub'][$i]['name'] = _MI_TORRENT_SMNAME1;
    $modversion['sub'][$i]['url'] = "submit.php";
}
$i++;
$modversion['sub'][$i]['name'] = _MI_TORRENT_SMNAME2;
$modversion['sub'][$i]['url'] = "topten.php?list=hit";
$i++;
$modversion['sub'][$i]['name'] = _MI_TORRENT_SMNAME3;
$modversion['sub'][$i]['url'] = "topten.php?list=rate";
unset($i);
// Comments
$modversion['hasComments'] = 1;
$modversion['comments']['itemName'] = 'lid';
$modversion['comments']['pageName'] = 'singlefile.php';
$modversion['comments']['extraParams'] = ['cid'];
// Comment callback functions
$modversion['comments']['callbackFile'] = 'include/comment_functions.php';
$modversion['comments']['callback']['approve'] = 'xtorrent_com_approve';
$modversion['comments']['callback']['update'] = 'xtorrent_com_update';
// Templates
$modversion['templates'] = [
    ['file' => 'xtorrent_brokenfile.tpl', 'description' => ''],
    ['file' => 'xtorrent_download.tpl', 'description' => ''],
    ['file' => 'xtorrent_index.tpl', 'description' => ''],
    ['file' => 'xtorrent_modfile.tpl', 'description' => ''],
    ['file' => 'xtorrent_ratefile.tpl', 'description' => ''],
    ['file' => 'xtorrent_singlefile.tpl', 'description' => ''],
    ['file' => 'xtorrent_submit.tpl', 'description' => ''],
    ['file' => 'xtorrent_topten.tpl', 'description' => ''],
    ['file' => 'xtorrent_viewcat.tpl', 'description' => ''],
    ['file' => 'xtorrent_newlistindex.tpl', 'description' => ''],
    ['file' => 'xtorrent_viewlist.tpl', 'description' => ''],
    ['file' => 'xtorrent_reviews.tpl', 'description' => '']
];

//Module config setting
// new $modversion['config'] = [];
// new $modversion['config'][] = [];
$modversion['config'][1]['name'] = 'popular';
$modversion['config'][1]['title'] = '_MI_TORRENT_POPULAR';
$modversion['config'][1]['description'] = '_MI_TORRENT_POPULARDSC';
$modversion['config'][1]['formtype'] = 'select';
$modversion['config'][1]['valuetype'] = 'int';
$modversion['config'][1]['default'] = 100;
$modversion['config'][1]['options'] = ['5' => 5, '10' => 10, '50' => 50, '100' => 100, '200' => 200, '500' => 500, '1000' => 1000];

$modversion['config'][2]['name'] = 'displayicons';
$modversion['config'][2]['title'] = '_MI_TORRENT_ICONDISPLAY';
$modversion['config'][2]['description'] = '_MI_TORRENT_DISPLAYICONDSC';
$modversion['config'][2]['formtype'] = 'select';
$modversion['config'][2]['valuetype'] = 'int';
$modversion['config'][2]['default'] = 1;
$modversion['config'][2]['options'] = ['_MI_TORRENT_DISPLAYICON1' => 1, '_MI_TORRENT_DISPLAYICON2' => 2, '_MI_TORRENT_DISPLAYICON3' => 3];


$modversion['config'][3]['name'] = 'perpage';
$modversion['config'][3]['title'] = '_MI_TORRENT_PERPAGE';
$modversion['config'][3]['description'] = '_MI_TORRENT_PERPAGEDSC';
$modversion['config'][3]['formtype'] = 'select';
$modversion['config'][3]['valuetype'] = 'int';
$modversion['config'][3]['default'] = 10;
$modversion['config'][3]['options'] = ['5' => 5, '10' => 10, '15' => 15, '20' => 20, '25' => 25, '30' => 30, '50' => 50];

$modversion['config'][35]['name'] = 'admin_perpage';
$modversion['config'][35]['title'] = '_MI_TORRENT_ADMINPAGE';
$modversion['config'][35]['description'] = '_MI_TORRENT_AMDMINPAGEDSC';
$modversion['config'][35]['formtype'] = 'select';
$modversion['config'][35]['valuetype'] = 'int';
$modversion['config'][35]['default'] = 10;
$modversion['config'][35]['options'] = ['5' => 5, '10' => 10, '15' => 15, '20' => 20, '25' => 25, '30' => 30, '50' => 50];


$qa = ' (A)';
$qd = ' (D)';

$modversion['config'][39]['name'] = 'filexorder';
$modversion['config'][39]['title'] = '_MI_TORRENT_ARTICLESSORT';
$modversion['config'][39]['description'] = '_MI_TORRENT_ARTICLESSORTDSC';
$modversion['config'][39]['formtype'] = 'select';
$modversion['config'][39]['valuetype'] = 'text';
$modversion['config'][39]['default'] = 'title ASC';
$modversion['config'][39]['options'] = [
    _MI_TORRENT_TITLE . $qa => 'title ASC',
    _MI_TORRENT_TITLE . $qd => 'title DESC',
    _MI_TORRENT_SUBMITTED2 . $qa => 'published ASC' ,
    _MI_TORRENT_SUBMITTED2 . $qd => 'published DESC',
    _MI_TORRENT_RATING . $qa => 'rating ASC',
    _MI_TORRENT_RATING . $qd => 'rating DESC',
    _MI_TORRENT_POPULARITY . $qa => 'hits ASC',
    _MI_TORRENT_POPULARITY . $qd => 'hits DESC',
    _MI_TORRENT_WEIGHT => 'weight'];

$modversion['config'][32]['name'] = 'subcats';
$modversion['config'][32]['title'] = '_MI_TORRENT_SUBCATS';
$modversion['config'][32]['description'] = '_MI_TORRENT_SUBCATSDSC';
$modversion['config'][32]['formtype'] = 'yesno';
$modversion['config'][32]['valuetype'] = 'int';
$modversion['config'][32]['default'] = 0;

$modversion['config'][41]['name'] = 'daysnew';
$modversion['config'][41]['title'] = '_MI_TORRENT_DAYSNEW';
$modversion['config'][41]['description'] = '_MI_TORRENT_DAYSNEWDSC';
$modversion['config'][41]['formtype'] = 'textbox';
$modversion['config'][41]['valuetype'] = 'int';
$modversion['config'][41]['default'] = 10;

$modversion['config'][42]['name'] = 'daysupdated';
$modversion['config'][42]['title'] = '_MI_TORRENT_DAYSUPDATED';
$modversion['config'][42]['description'] = '_MI_TORRENT_DAYSUPDATEDDSC';
$modversion['config'][42]['formtype'] = 'textbox';
$modversion['config'][42]['valuetype'] = 'int';
$modversion['config'][42]['default'] = 10;

$modversion['config'][6]['name'] = 'screenshot';
$modversion['config'][6]['title'] = '_MI_TORRENT_USESHOTS';
$modversion['config'][6]['description'] = '_MI_TORRENT_USESHOTSDSC';
$modversion['config'][6]['formtype'] = 'yesno';
$modversion['config'][6]['valuetype'] = 'int';
$modversion['config'][6]['default'] = 0;

$modversion['config'][21]['name'] = 'usethumbs';
$modversion['config'][21]['title'] = '_MI_TORRENT_USETHUMBS';
$modversion['config'][21]['description'] = '_MI_TORRENT_USETHUMBSDSC';
$modversion['config'][21]['formtype'] = 'yesno';
$modversion['config'][21]['valuetype'] = 'int';
$modversion['config'][21]['default'] = 0;

$modversion['config'][36]['name'] = 'updatethumbs';
$modversion['config'][36]['title'] = '_MI_TORRENT_IMGUPDATE';
$modversion['config'][36]['description'] = '_MI_TORRENT_IMGUPDATEDSC';
$modversion['config'][36]['formtype'] = 'yesno';
$modversion['config'][36]['valuetype'] = 'int';
$modversion['config'][36]['default'] = 1;

$modversion['config'][37]['name'] = 'imagequality';
$modversion['config'][37]['title'] = '_MI_TORRENT_QUALITY';
$modversion['config'][37]['description'] = '_MI_TORRENT_QUALITYDSC';
$modversion['config'][37]['formtype'] = 'textbox';
$modversion['config'][37]['valuetype'] = 'int';
$modversion['config'][37]['default'] = 100;

$modversion['config'][38]['name'] = 'keepaspect';
$modversion['config'][38]['title'] = '_MI_TORRENT_KEEPASPECT';
$modversion['config'][38]['description'] = '_MI_TORRENT_KEEPASPECTDSC';
$modversion['config'][38]['formtype'] = 'yesno';
$modversion['config'][38]['valuetype'] = 'int';
$modversion['config'][38]['default'] = 0;

$modversion['config'][7]['name'] = 'shotwidth';
$modversion['config'][7]['title'] = '_MI_TORRENT_SHOTWIDTH';
$modversion['config'][7]['description'] = '_MI_TORRENT_SHOTWIDTHDSC';
$modversion['config'][7]['formtype'] = 'textbox';
$modversion['config'][7]['valuetype'] = 'int';
$modversion['config'][7]['default'] = 140;

$modversion['config'][20]['name'] = 'shotheight';
$modversion['config'][20]['title'] = '_MI_TORRENT_SHOTHEIGHT';
$modversion['config'][20]['description'] = '_MI_TORRENT_SHOTHEIGHTDSC';
$modversion['config'][20]['formtype'] = 'textbox';
$modversion['config'][20]['valuetype'] = 'int';
$modversion['config'][20]['default'] = 79;

$modversion['config'][8]['name'] = 'check_host';
$modversion['config'][8]['title'] = '_MI_TORRENT_CHECKHOST';
$modversion['config'][8]['description'] = '';
$modversion['config'][8]['formtype'] = 'yesno';
$modversion['config'][8]['valuetype'] = 'int';
$modversion['config'][8]['default'] = 0;

//$xoops_url = parse_url(XOOPS_URL);
$modversion['config'][9]['name'] = 'referers';
$modversion['config'][9]['title'] = '_MI_TORRENT_REFERERS';
$modversion['config'][9]['description'] = '_MI_TORRENT_REFERERSDSC';
$modversion['config'][9]['formtype'] = 'textarea';
$modversion['config'][9]['valuetype'] = 'array';
//$modversion['config'][9]['default'] = array($xoops_url['host']);

$modversion['config'][15]['name'] = 'submissions';
$modversion['config'][15]['title'] = '_MI_TORRENT_ALLOWSUBMISS';
$modversion['config'][15]['description'] = '_MI_TORRENT_ALLOWSUBMISSDSC';
$modversion['config'][15]['formtype'] = 'yesno';
$modversion['config'][15]['valuetype'] = 'int';
$modversion['config'][15]['default'] = 1;

$modversion['config'][34]['name'] = 'submitarts';
$modversion['config'][34]['title'] = '_MI_TORRENT_SUBMITART';
$modversion['config'][34]['description'] = '_MI_TORRENT_SUBMITARTDSC';
$modversion['config'][34]['formtype'] = 'group_multi';
$modversion['config'][34]['valuetype'] = 'array';
$modversion['config'][34]['default'] = '1';

$modversion['config'][4]['name'] = 'anonpost';
$modversion['config'][4]['title'] = '_MI_TORRENT_ANONPOST';
$modversion['config'][4]['description'] = '_MI_TORRENT_ANONPOSTDSC';
$modversion['config'][4]['formtype'] = 'yesno';
$modversion['config'][4]['valuetype'] = 'int';
$modversion['config'][4]['default'] = 0;

$modversion['config'][5]['name'] = 'autoapprove';
$modversion['config'][5]['title'] = '_MI_TORRENT_AUTOAPPROVE';
$modversion['config'][5]['description'] = '_MI_TORRENT_AUTOAPPROVEDSC';
$modversion['config'][5]['formtype'] = 'yesno';
$modversion['config'][5]['valuetype'] = 'int';
$modversion['config'][5]['default'] = 0;


$modversion['config'][16]['name'] = 'useruploads';
$modversion['config'][16]['title'] = '_MI_TORRENT_ALLOWUPLOADS';
$modversion['config'][16]['description'] = '_MI_TORRENT_ALLOWUPLOADSDSC';
$modversion['config'][16]['formtype'] = 'yesno';
$modversion['config'][16]['valuetype'] = 'int';
$modversion['config'][16]['default'] = 1;

$modversion['config'][10]['name'] = 'maxfilesize';
$modversion['config'][10]['title'] = '_MI_TORRENT_MAXFILESIZE';
$modversion['config'][10]['description'] = '_MI_TORRENT_MAXFILESIZEDSC';
$modversion['config'][10]['formtype'] = 'textbox';
$modversion['config'][10]['valuetype'] = 'int';
$modversion['config'][10]['default'] = 200000;

$modversion['config'][11]['name'] = 'maximgwidth';
$modversion['config'][11]['title'] = '_MI_TORRENT_IMGWIDTH';
$modversion['config'][11]['description'] = '_MI_TORRENT_IMGWIDTHDSC';
$modversion['config'][11]['formtype'] = 'textbox';
$modversion['config'][11]['valuetype'] = 'int';
$modversion['config'][11]['default'] = 600;

$modversion['config'][12]['name'] = 'maximgheight';
$modversion['config'][12]['title'] = '_MI_TORRENT_IMGHEIGHT';
$modversion['config'][12]['description'] = '_MI_TORRENT_IMGHEIGHTDSC';
$modversion['config'][12]['formtype'] = 'textbox';
$modversion['config'][12]['valuetype'] = 'int';
$modversion['config'][12]['default'] = 600;

$modversion['config'][13]['name'] = 'uploaddir';
$modversion['config'][13]['title'] = '_MI_TORRENT_UPLOADDIR';
$modversion['config'][13]['description'] = '_MI_TORRENT_UPLOADDIRDSC';
$modversion['config'][13]['formtype'] = 'textbox';
$modversion['config'][13]['valuetype'] = 'text';
$modversion['config'][13]['default'] = 'uploads';

$modversion['config'][19]['name'] = 'mainimagedir';
$modversion['config'][19]['title'] = '_MI_TORRENT_MAINIMGDIR';
$modversion['config'][19]['description'] = '_MI_TORRENT_MAINIMGDIRDSC';
$modversion['config'][19]['formtype'] = 'textbox';
$modversion['config'][19]['valuetype'] = 'text';
$modversion['config'][19]['default'] = 'modules/xtorrent/assets/images';

$modversion['config'][17]['name'] = 'screenshots';
$modversion['config'][17]['title'] = '_MI_TORRENT_SCREENSHOTS';
$modversion['config'][17]['description'] = '_MI_TORRENT_SCREENSHOTSDSC';
$modversion['config'][17]['formtype'] = 'textbox';
$modversion['config'][17]['valuetype'] = 'text';
$modversion['config'][17]['default'] = 'modules/xtorrent/assets/images/screenshots';

$modversion['config'][18]['name'] = 'catimage';
$modversion['config'][18]['title'] = '_MI_TORRENT_CATEGORYIMG';
$modversion['config'][18]['description'] = '_MI_TORRENT_CATEGORYIMGDSC';
$modversion['config'][18]['formtype'] = 'textbox';
$modversion['config'][18]['valuetype'] = 'text';
$modversion['config'][18]['default'] = 'modules/xtorrent/assets/images/category';

$modversion['config'][26]['name'] = 'platform';
$modversion['config'][26]['title'] = '_MI_TORRENT_PLATFORM';
$modversion['config'][26]['description'] = '_MI_TORRENT_PLATFORMDSC';
$modversion['config'][26]['formtype'] = 'textarea';
$modversion['config'][26]['valuetype'] = 'array';
$modversion['config'][26]['default'] = 'None|Windows|Unix|Mac|Other';

$license = 'Apache License (v. 1.0)|
Apache License (v. 1.1) |
Attribution Assurance License |
Berkeley Database License |
4.4 BSD Copyright (Original BSD-Lizenz) |
FreeBSD Copyright (Modifizierte BSD-Lizenz) |
BSD License (Original)|
Christian Software Public License|
Cryptix General License| 
Eiffel Forum License (v. 1.0)|
Eiffel Forum License (v. 2.0)|
Free Fuzzy Logic Library Open Source License| 
Intel Open Source License for CDSA/CSSM Implementation| 
Lua Copyright notice| 
GNU License|
Mozart License|
OpenLDAP Public License (v. 2.3)| 
OpenLDAP Public License (v. 2.5)|
OpenLDAP Public License (v. 2.7)|
Open Media Group Open Source License|
Pangeia Informatica Copyright (v. 1.2)|
Phorum License (v. 2.0)|
PHP License (v. 3.0)|
PLT License|
Python Copyright|
skyBuilders Open Source License|
Sleepycat Software Product License|
SpectSoft General Open Source License (SGOSL)|
4Suite License (v. 1.1)|
Tea Software License|
UdanaxTM Open-Source License|
Vovida Software License|
W3C Software Notice and License|
Wide Open License (WOL)|
X.Net License|
X Window System License|
The license of ZLib|
Zope Public License (v. 2.0)| 
Academic Free License (AFL) (v. 1.1)|
Academic Free License (AFL) (v. 1.2)| 
CNRI Open Source License Agreement (bis Python 1.6)|
Galen Open Source License (GOSL)|
Globus Toolkit Public License|
Open Group Test Suite License|
PSF License Agreement (Python)| 
Ruby License|
Sendmail License|
SFL License Agreement| 
Standard ML of New Jersey Copyright Notice|
Suneido Free Software License|
Tcl/Tk License Terms|
xinetd License|
Zope Public License (v. 1.0)|
Affero General Public License|
Alternate Route Open Source License (v. 2.0)|
GNU Emacs General Public License|
GPL General Public License (GPL) (v. 1.0)|
GPL General Public License (GPL) (v. 2.0)|
Open RTLinux Patent License (v. 2.0)|
Apple Public Source License (v. 2.0)|
Arphic Public License|
Common Public License|
IBM Public License|
Jabber Open Source License|
Nethack General Public License|
Open Group Public License|
Open Software License (OSL) (v. 1.0)|
Open Software License (OSL) (v. 1.1)|
Open Software License (OSL) (v 2.0)|
RedHat eCos Public License (v. 1.1)| 
Salutation Public License|
Software AG License Terms (Quip License) (v. 1.3)|
Vim License|
Erlang Public License (v. 1.1)|
ICS Open Source Public License|
Interbase Public License|
Mozilla Public License (v. 1.0)|
Mozilla Public License (v. 1.1)|
Netizen Open Source License (NOSL)|
Nokia Open Source License|
Open Telecom Public License|
Ricoh Source Code Public License|
Sun Public License|
Sun Industry Standards Source License (v. 1.1)|
Zenplex Public License|
Cougaar Open Source License|
Hi-Potent Open Source License | 
GNU Library General Public License (LGPL) (v. 2.0)| 
GNU Lesser General Public License (LGPL) (v. 2.1)| 
Motosoto Open Source License (v. 0.9.1)|
Open Watcom Public License|
Artistic License (v. 1.0)| 
Artistic License (v. 2.0)| 
Clarified Artistic License| 
Keith Devens Open Source License|
LaTeX Project Public License (LPPL) (v. 1.2)| 
Physnet Package License|
SGI Free Software License B (v. 1.1)| 
Apple Public Source License (v. 1.2)| 
Macromedia Open Source License Agreement (v. 1.0)|
Netscape Public License (NPL) (v. 1.0)|
Netscape Public License (NPL) (v. 1.1)|
OCLC Research Public License (v. 1.0)|
OCLC Research Public License (v. 2.0)|
Open Map Software License Agreement| 
Q Public License (QPL)|
Aladdin Free Public License (v. 8.0)|
Aladdin Free Public License (v. 9.0)| 
AT&T Source Code Agreement (v. 1.2D)| 
CenterPoint Public License|
CrossPoint Quelltext Lizenz|
Hacktivismo Enhanced-Source Software License Agreement (v. 0.1)|
Jahia Community Source License (JSCL) (v.012)|
Microsoft Shared Source License| 
Open Public License|
Open RTLinux Patent License (v. 1.0)|
Plan 9 Open Source License Agreement| 
PLS Free Software License Agreement (v. 1.0)|
Real Networks Community Source License (v. 1.2)| 
Red Hat eCos Public License (v. 1.1)| 
Scilab license| 
SGI Free Software License B (v. 1.0)|  
ShippySoft Source Available License (SSSAL)| 
Squeak License (Apple)| 
Sun Community Source License| 
Sun Solaris Source Code (Foundation Release) License (v. 1.1)| 
University of Utah Public License| 
YaST and SuSE Linux license terms| 
YaST und SuSE Linux Lizenzbestimmungen| 
ZIB Academic License| 
Apples Common Documentation License (v. 1.0)| 
Creative Commons Licenses| 
Design Science License (DSL)| 
EFF Audio Public License (v. 1.0.1) | 
electrohippies Ethical Open License (v. 2.0)| 
electronic Music Public License| 
Ethymonics Free Music License| 
Free Art License| 
Lizenz (Freie Kunst)|
FreeBSD Documentation License| 
Free Music Public License (FMPL) (v. 0.7)| 
GNU Free Documentation License (FDL) (v. 1.1)| 
GNU Free Documentation License (FDL) (v. 1.2)| 
Guy Hoffmans license| 
Linux Documentation Project Copying License| 
Lizenz f�r Freie Inhalte|
Lizenz f�r die freie Nutzung unver�nderter Inhalte| 
October Open Game License (OOGL)| 
The Open Book Project| 
Open Content License (OPL)| 
Open Directory Project License| 
Open Game License| 
The green Open Music Licence| 
The yellow Open Music License| 
The red Open Music License| 
The rainbow Open Music License| 
Open Publication License (v. 1.0)| 
Open Source Music License (OSML)| 
OR Magazine License| 
Public Documentation License (PDL)'; 

$modversion['config'][27]['name'] = 'license';
$modversion['config'][27]['title'] = '_MI_TORRENT_LICENSE';
$modversion['config'][27]['description'] = '_MI_TORRENT_LICENSEDSC';
$modversion['config'][27]['formtype'] = 'textarea';
$modversion['config'][27]['valuetype'] = 'array';
$modversion['config'][27]['default'] = $license;

$modversion['config'][33]['name'] = 'versiontypes';
$modversion['config'][33]['title'] = '_MI_TORRENT_VERSIONTYPES';
$modversion['config'][33]['description'] = '_MI_TORRENT_PLATFORMDSC';
$modversion['config'][33]['formtype'] = 'textarea';
$modversion['config'][33]['valuetype'] = 'array';
$modversion['config'][33]['default'] = 'None|Alpha|Beta|RC|FULL';

$modversion['config'][31]['name'] = 'limitations';
$modversion['config'][31]['title'] = '_MI_TORRENT_LIMITS';
$modversion['config'][31]['description'] = '_MI_TORRENT_LIMITSDSC';
$modversion['config'][31]['formtype'] = 'textarea';
$modversion['config'][31]['valuetype'] = 'array';
$modversion['config'][31]['default'] = 'None|Trial|14 day limitation|None Save';

$modversion['config'][23]['name'] = 'dateformat';
$modversion['config'][23]['title'] = '_MI_TORRENT_DATEFORMAT';
$modversion['config'][23]['description'] = '_MI_TORRENT_DATEFORMATDSC';
$modversion['config'][23]['formtype'] = 'textbox';
$modversion['config'][23]['valuetype'] = 'text';
$modversion['config'][23]['default'] = 'D, d-M-Y';

$modversion['config'][24]['name'] = 'showdisclaimer';
$modversion['config'][24]['title'] = '_MI_TORRENT_SHOWDISCLAIMER';
$modversion['config'][24]['description'] = '_MI_TORRENT_SHOWDISCLAIMERDSC';
$modversion['config'][24]['formtype'] = 'yesno';
$modversion['config'][24]['valuetype'] = 'int';
$modversion['config'][24]['default'] = 0;

$modversion['config'][25]['name'] = 'disclaimer';
$modversion['config'][25]['title'] = '_MI_TORRENT_DISCLAIMER';
$modversion['config'][25]['description'] = '_MI_TORRENT_DISCLAIMERDSC';
$modversion['config'][25]['formtype'] = 'textarea';
$modversion['config'][25]['valuetype'] = 'text';
$modversion['config'][25]['default'] = 'We have the right, but not the obligation to monitor and review submissions submitted by users, in the forums. We shall not be responsible for any of the content of these messages. We further reserve the right, to delete, move or edit submissions that the we, in its exclusive discretion, deems abusive, defamatory, obscene or in violation of any Copyright or Trademark laws or otherwise objectionable.';

$modversion['config'][29]['name'] = 'showDowndisclaimer';
$modversion['config'][29]['title'] = '_MI_TORRENT_SHOWDOWNDISCL';
$modversion['config'][29]['description'] = '_MI_TORRENT_SHOWDOWNDISCLDSC';
$modversion['config'][29]['formtype'] = 'yesno';
$modversion['config'][29]['valuetype'] = 'int';
$modversion['config'][29]['default'] = 0;

$modversion['config'][30]['name'] = 'downdisclaimer';
$modversion['config'][30]['title'] = '_MI_TORRENT_DOWNDISCLAIMER';
$modversion['config'][30]['description'] = '_MI_TORRENT_DOWNDISCLAIMERDSC';
$modversion['config'][30]['formtype'] = 'textarea';
$modversion['config'][30]['valuetype'] = 'text';
$modversion['config'][30]['default'] = 'The file downloads on this site are provided as is without warranty either expressed or implied. Downloaded files should be checked for possible virus infection using the most up-to-date detection and security packages. If you have a question concerning a particular piece of software, feel free to contact the developer. We refuse liability for any damage or loss resulting from the use or misuse of any software offered from this site for downloading. If you have any doubt at all about the safety and operation of software made available to you on this site, do not download it. Contact us if you have questions concerning this disclaimer.';

$modversion['config'][40]['name'] = 'copyright';
$modversion['config'][40]['title'] = '_MI_TORRENT_COPYRIGHT';
$modversion['config'][40]['description'] = '_MI_TORRENT_COPYRIGHTDSC';
$modversion['config'][40]['formtype'] = 'yesno';
$modversion['config'][40]['valuetype'] = 'int';
$modversion['config'][40]['default'] = 1;

$modversion['config'][43]['name'] = 'poll_torrent';
$modversion['config'][43]['title'] = '_MI_TORRENT_POLL_TORRENT';
$modversion['config'][43]['description'] = '_MI_TORRENT_POLL_TORRENTDSC';
$modversion['config'][43]['formtype'] = 'yesno';
$modversion['config'][43]['valuetype'] = 'int';
$modversion['config'][43]['default'] = 1;

$modversion['config'][44]['name'] = 'poll_torrent_time';
$modversion['config'][44]['title'] = '_MI_TORRENT_POLL_TORRENTTIME';
$modversion['config'][44]['description'] = '_MI_TORRENT_POLL_TORRENTTIMEDSC';
$modversion['config'][44]['formtype'] = 'select';
$modversion['config'][44]['valuetype'] = 'int';
$modversion['config'][44]['default'] = 5;
$modversion['config'][44]['options'] = ['1' => 1,'5' => 5, '10' => 10, '15' => 15, '20' => 20, '25' => 25, '30' => 30, '50' => 50, '75' => 75, '120' => 120];

$modversion['config'][45]['name'] = 'poll_tracker';
$modversion['config'][45]['title'] = '_MI_TORRENT_POLL_TRACKER';
$modversion['config'][45]['description'] = '_MI_TORRENT_POLL_TRACKERDSC';
$modversion['config'][45]['formtype'] = 'yesno';
$modversion['config'][45]['valuetype'] = 'int';
$modversion['config'][45]['default'] = 0;

$modversion['config'][46]['name'] = 'poll_tracker_time';
$modversion['config'][46]['title'] = '_MI_TORRENT_POLL_TRACKERTIME';
$modversion['config'][46]['description'] = '_MI_TORRENT_POLL_TRACKERTIMEDSC';
$modversion['config'][46]['formtype'] = 'select';
$modversion['config'][46]['valuetype'] = 'int';
$modversion['config'][46]['default'] = 10;
$modversion['config'][46]['options'] = ['1' => 1,'5' => 5, '10' => 10, '15' => 15, '20' => 20, '25' => 25, '30' => 30, '50' => 50, '75' => 75, '120' => 120];

$modversion['config'][47]['name'] = 'poll_tracker_timeout';
$modversion['config'][47]['title'] = '_MI_TORRENT_POLL_TRACKERTIMEOUT';
$modversion['config'][47]['description'] = '_MI_TORRENT_POLL_TRACKERTIMEOUTDSC';
$modversion['config'][47]['formtype'] = 'select';
$modversion['config'][47]['valuetype'] = 'int';
$modversion['config'][47]['default'] = 5;
$modversion['config'][47]['options'] = ['1' => 1,'5' => 5, '10' => 10, '15' => 15, '20' => 20, '25' => 25, '30' => 30, '50' => 50, '75' => 75, '120' => 120];

$modversion['config'][48]['name'] = 'announce_interval';
$modversion['config'][48]['title'] = '_MI_TORRENT_ANNOUNCEINTERVAL';
$modversion['config'][48]['description'] = '_MI_TORRENT_ANNOUNCEINTERVALDSC';
$modversion['config'][48]['formtype'] = 'select';
$modversion['config'][48]['valuetype'] = 'int';
$modversion['config'][48]['default'] = 10;
$modversion['config'][48]['options'] = ['5' => 5, '10' => 10, '15' => 15, '20' => 20, '25' => 25, '30' => 30, '50' => 50];

$modversion['config'][49]['name'] = 'numleechers';
$modversion['config'][49]['title'] = '_MI_TORRENT_NUMLEECHERS';
$modversion['config'][49]['description'] = '_MI_TORRENT_NUMLEECHERSDSC';
$modversion['config'][49]['formtype'] = 'select';
$modversion['config'][49]['valuetype'] = 'int';
$modversion['config'][49]['default'] = 100;
$modversion['config'][49]['options'] = ['5' => 5, '10' => 10, '15' => 15, '20' => 20, '25' => 25, '50' => 50, '100' => 100, '125' => 125, '150' => 150, '200' => 200, '300' => 300, '400' => 400, '500' => 500, '750' => 750, '100' => 1000, '2000' => 2000, '3000' => 3000, 'Unlimited' => 0];

$modversion['config'][50]['name'] = 'numseeds';
$modversion['config'][50]['title'] = '_MI_TORRENT_NUMSEEDS';
$modversion['config'][50]['description'] = '_MI_TORRENT_NUMSEEDSDSC';
$modversion['config'][50]['formtype'] = 'select';
$modversion['config'][50]['valuetype'] = 'int';
$modversion['config'][50]['default'] = 25;
$modversion['config'][50]['options'] = ['5' => 5, '10' => 10, '15' => 15, '20' => 20, '25' => 25, '30' => 30, '50' => 50, '100' => 100, '125' => 125, '150' => 150, '200' => 200, '300' => 300, '400' => 400, '500' => 500, 'Unlimited' => 0];

$modversion['config'][51]['name'] = 'announce_url';
$modversion['config'][51]['title'] = '_MI_TORRENT_ANNOUNCEURL';
$modversion['config'][51]['description'] = '_MI_TORRENT_ANNOUNCEURLDSC';
$modversion['config'][51]['formtype'] = 'textarea';
$modversion['config'][51]['valuetype'] = 'text';
$modversion['config'][51]['default'] = XOOPS_URL . '/modules/' . $modversion['dirname'] . '/announce.php';

$modversion['config'][52]['name'] = 'agents_disallowed';
$modversion['config'][52]['title'] = '_MI_TORRENT_AGENTSDISALLOWED';
$modversion['config'][52]['description'] = '_MI_TORRENT_AGENTSDISALLOWEDDESC';
$modversion['config'][52]['formtype'] = 'textarea';
$modversion['config'][52]['valuetype'] = 'text';
$modversion['config'][52]['default'] = '^Mozilla\\\\/|^Opera\\\\/|^Links |^Lynx\\\\/';

$modversion['config'][53]['name'] = 'ipn_dbg_lvl';
$modversion['config'][53]['title'] = '_MI_IPN_DEBUG_LEVEL';
$modversion['config'][53]['description'] = '_MI_IPN_DEBUG_LEVELDESC';
$modversion['config'][53]['formtype'] = 'text';
$modversion['config'][53]['valuetype'] = 'int';
$modversion['config'][53]['default'] = 0;

$modversion['config'][54]['name'] = 'ipn_log_entries';
$modversion['config'][54]['title'] = '_MI_IPN_LOG_ENTRIES';
$modversion['config'][54]['description'] = '_MI_IPN_LOG_ENTRIESDESC';
$modversion['config'][54]['formtype'] = 'select';
$modversion['config'][54]['valuetype'] = 'int';
$modversion['config'][54]['default'] = 10;
$modversion['config'][54]['options'] = ['5' => 5, '10' => 10, '15' => 15, '20' => 20, '25' => 25, '30' => 30, '50' => 50];

$modversion['config'][55]['name'] = 'currencies';
$modversion['config'][55]['title'] = '_MI_TORRENT_CURRENCIES';
$modversion['config'][55]['description'] = '_MI_TORRENT_CURRENCIESDESC';
$modversion['config'][55]['formtype'] = 'textarea';
$modversion['config'][55]['valuetype'] = 'array';
$modversion['config'][55]['default'] = 'CAD|EUR|GBP|USD|YEN|AUD|NZD|CHF|HKD|SGD|SEK|DKK|PLN|NOK|HUF|CZK|ILS|MXN';

$modversion['config'][56]['name'] = 'image_url';
$modversion['config'][56]['title'] = '_MI_PAYPAL_IMAGE';
$modversion['config'][56]['description'] = '_MI_PAYPAL_IMAGEDESC';
$modversion['config'][56]['formtype'] = 'textarea';
$modversion['config'][56]['valuetype'] = 'text';
$modversion['config'][56]['default'] = XOOPS_URL . '/images/xoops.gif';

$modversion['config'][56]['name'] = 'payment_subtitle';
$modversion['config'][56]['title'] = '_MI_PAYPAL_PAYSUBTITLE';
$modversion['config'][56]['description'] = '_MI_PAYPAL_PAYSUBTITLEDESC';
$modversion['config'][56]['formtype'] = 'textarea';
$modversion['config'][56]['valuetype'] = 'text';
$modversion['config'][56]['default'] = 'By paying now you will be provided with download file.';

$modversion['config'][57]['name'] = 'payment_subtitle';
$modversion['config'][57]['title'] = '_MI_PAYPAL_PAYSUBTITLE';
$modversion['config'][57]['description'] = '_MI_PAYPAL_PAYSUBTITLEDESC';
$modversion['config'][57]['formtype'] = 'textarea';
$modversion['config'][57]['valuetype'] = 'text';
$modversion['config'][57]['default'] = 'By paying now you will be provided with download file.';

$modversion['config'][58]['name'] = 'payment_clause';
$modversion['config'][58]['title'] = '_MI_PAYPAL_PAYCLAUSE';
$modversion['config'][58]['description'] = '_MI_PAYPAL_PAYCLAUSEDESC';
$modversion['config'][58]['formtype'] = 'textarea';
$modversion['config'][58]['valuetype'] = 'text';
$modversion['config'][58]['default'] = 'By paying now you agree that you are paying the owner of this torrent to download this file, all recourse and enquires should be directed to the payee not the host.';

$modversion['config'][59]['name'] = 'access_clear';
$modversion['config'][59]['title'] = '_MI_ACCESS_CLEAR';
$modversion['config'][59]['description'] = '_MI_ACCESS_CLEARDESC';
$modversion['config'][59]['formtype'] = 'select';
$modversion['config'][59]['valuetype'] = 'int';
$modversion['config'][59]['default'] = 10;
$modversion['config'][59]['options'] = ['5' => 5, '10' => 10, '15' => 15, '20' => 20, '25' => 25, '30' => 30, '50' => 50, '80' => 80, '110' => 110, '150' => 150, '190' => 190];

$modversion['config'][60]['name'] = 'htaccess';
$modversion['config'][60]['title'] = '_MI_TORRENT_SEO_HTACCESS';
$modversion['config'][60]['description'] = '_MI_TORRENT_SEO_HTACCESSDESC';
$modversion['config'][60]['formtype'] = 'yesno';
$modversion['config'][60]['valuetype'] = 'int';
$modversion['config'][60]['default'] = 0;

$modversion['config'][61]['name'] = 'xsoap_servers';
$modversion['config'][61]['title'] = '_MI_XSOAP_SERVERS';
$modversion['config'][61]['description'] = '_MI_XSOAP_SERVERSDESC';
$modversion['config'][61]['formtype'] = 'textarea';
$modversion['config'][61]['valuetype'] = 'text';
$modversion['config'][61]['default'] = '';

$modversion['config'][62]['name'] = 'xsoap_servers_send';
$modversion['config'][62]['title'] = '_MI_XSOAP_SERVERSSUBMIT';
$modversion['config'][62]['description'] = '_MI_XSOAP_SERVERSSUBMITDESC';
$modversion['config'][62]['formtype'] = 'yesno';
$modversion['config'][62]['valuetype'] = 'int';
$modversion['config'][62]['default'] = 0;

$modversion['config'][63]['name'] = 'xsoap_servers_receive';
$modversion['config'][63]['title'] = '_MI_XSOAP_SERVERSRECIEVE';
$modversion['config'][63]['description'] = '_MI_XSOAP_SERVERSRECIEVEDESC';
$modversion['config'][63]['formtype'] = 'yesno';
$modversion['config'][63]['valuetype'] = 'int';
$modversion['config'][63]['default'] = 0;

$modversion['config'][64]['name'] = 'xsoap_servers_exchange';
$modversion['config'][64]['title'] = '_MI_XSOAP_SERVERSEXCHANGE';
$modversion['config'][64]['description'] = '_MI_XSOAP_SERVERSEXCHANGEDESC';
$modversion['config'][64]['formtype'] = 'yesno';
$modversion['config'][64]['valuetype'] = 'int';
$modversion['config'][64]['default'] = 0;

$modversion['config'][65]['name'] = 'response_key';
$modversion['config'][65]['title'] = '_MI_XSOAP_SERVERKEY';
$modversion['config'][65]['description'] = '_MI_XSOAP_SERVERKEYDESC';
$modversion['config'][65]['formtype'] = 'textarea';
$modversion['config'][65]['valuetype'] = 'text';
$modversion['config'][65]['default'] = sha1($xoopsConfig['sitename'].XOOPS_URL.time().XOOPS_ROOT_PATH.$xoopsConfig['adminmail']).substr(sha1(XOOPS_URL.$xoopsConfig['adminmail'].time().$xoopsConfig['sitename'].XOOPS_ROOT_PATH),0, mt_rand(13, strlen(sha1(0))));

$modversion['config'][66]['name'] = 'hashkeys';
$modversion['config'][66]['title'] = '_MI_TORRENT_HASHKEYS';
$modversion['config'][66]['description'] = '_MI_TORRENT_HASHKEYSDSC';
$modversion['config'][66]['formtype'] = 'select';
$modversion['config'][66]['valuetype'] = 'text';
$modversion['config'][66]['default'] = 'qcp64';
$modversion['config'][66]['options'] = ['md5 keys' => 'md5', 'sha1 keys' => 'sha1', 'qcp64 keys' => 'qcp64', 'qcp71 keys' => 'qcp71', 'qcp135 keys' => 'qcp135'];

// Notification
$modversion['hasNotification'] = 1;
$modversion['notification']['lookup_file'] = 'include/notification.inc.php';
$modversion['notification']['lookup_func'] = 'xtorrent_notify_iteminfo';

$modversion['notification']['category'][1]['name'] = 'global';
$modversion['notification']['category'][1]['title'] = _MI_TORRENT_GLOBAL_NOTIFY;
$modversion['notification']['category'][1]['description'] = _MI_TORRENT_GLOBAL_NOTIFYDSC;
$modversion['notification']['category'][1]['subscribe_from'] = ['index.php', 'viewcat.php', 'singlefile.php'];

$modversion['notification']['category'][2]['name'] = 'category';
$modversion['notification']['category'][2]['title'] = _MI_TORRENT_CATEGORY_NOTIFY;
$modversion['notification']['category'][2]['description'] = _MI_TORRENT_CATEGORY_NOTIFYDSC;
$modversion['notification']['category'][2]['subscribe_from'] = ['viewcat.php', 'singlefile.php'];
$modversion['notification']['category'][2]['item_name'] = 'cid';
$modversion['notification']['category'][2]['allow_bookmark'] = 1;

$modversion['notification']['category'][3]['name'] = 'file';
$modversion['notification']['category'][3]['title'] = _MI_TORRENT_FILE_NOTIFY;
$modversion['notification']['category'][3]['description'] = _MI_TORRENT_FILE_NOTIFYDSC;
$modversion['notification']['category'][3]['subscribe_from'] = 'singlefile.php';
$modversion['notification']['category'][3]['item_name'] = 'lid';
$modversion['notification']['category'][3]['allow_bookmark'] = 1;

$modversion['notification']['event'][1]['name'] = 'new_category';
$modversion['notification']['event'][1]['category'] = 'global';
$modversion['notification']['event'][1]['title'] = _MI_TORRENT_GLOBAL_NEWCATEGORY_NOTIFY;
$modversion['notification']['event'][1]['caption'] = _MI_TORRENT_GLOBAL_NEWCATEGORY_NOTIFYCAP;
$modversion['notification']['event'][1]['description'] = _MI_TORRENT_GLOBAL_NEWCATEGORY_NOTIFYDSC;
$modversion['notification']['event'][1]['mail_template'] = 'global_newcategory_notify';
$modversion['notification']['event'][1]['mail_subject'] = _MI_TORRENT_GLOBAL_NEWCATEGORY_NOTIFYSBJ;

$modversion['notification']['event'][2]['name'] = 'file_modify';
$modversion['notification']['event'][2]['category'] = 'global';
$modversion['notification']['event'][2]['admin_only'] = 1;
$modversion['notification']['event'][2]['title'] = _MI_TORRENT_GLOBAL_FILEMODIFY_NOTIFY;
$modversion['notification']['event'][2]['caption'] = _MI_TORRENT_GLOBAL_FILEMODIFY_NOTIFYCAP;
$modversion['notification']['event'][2]['description'] = _MI_TORRENT_GLOBAL_FILEMODIFY_NOTIFYDSC;
$modversion['notification']['event'][2]['mail_template'] = 'global_filemodify_notify';
$modversion['notification']['event'][2]['mail_subject'] = _MI_TORRENT_GLOBAL_FILEMODIFY_NOTIFYSBJ;

$modversion['notification']['event'][3]['name'] = 'file_broken';
$modversion['notification']['event'][3]['category'] = 'global';
$modversion['notification']['event'][3]['admin_only'] = 1;
$modversion['notification']['event'][3]['title'] = _MI_TORRENT_GLOBAL_FILEBROKEN_NOTIFY;
$modversion['notification']['event'][3]['caption'] = _MI_TORRENT_GLOBAL_FILEBROKEN_NOTIFYCAP;
$modversion['notification']['event'][3]['description'] = _MI_TORRENT_GLOBAL_FILEBROKEN_NOTIFYDSC;
$modversion['notification']['event'][3]['mail_template'] = 'global_filebroken_notify';
$modversion['notification']['event'][3]['mail_subject'] = _MI_TORRENT_GLOBAL_FILEBROKEN_NOTIFYSBJ;

$modversion['notification']['event'][4]['name'] = 'file_submit';
$modversion['notification']['event'][4]['category'] = 'global';
$modversion['notification']['event'][4]['admin_only'] = 1;
$modversion['notification']['event'][4]['title'] = _MI_TORRENT_GLOBAL_FILESUBMIT_NOTIFY;
$modversion['notification']['event'][4]['caption'] = _MI_TORRENT_GLOBAL_FILESUBMIT_NOTIFYCAP;
$modversion['notification']['event'][4]['description'] = _MI_TORRENT_GLOBAL_FILESUBMIT_NOTIFYDSC;
$modversion['notification']['event'][4]['mail_template'] = 'global_filesubmit_notify';
$modversion['notification']['event'][4]['mail_subject'] = _MI_TORRENT_GLOBAL_FILESUBMIT_NOTIFYSBJ;

$modversion['notification']['event'][5]['name'] = 'new_file';
$modversion['notification']['event'][5]['category'] = 'global';
$modversion['notification']['event'][5]['title'] = _MI_TORRENT_GLOBAL_NEXTILE_NOTIFY;
$modversion['notification']['event'][5]['caption'] = _MI_TORRENT_GLOBAL_NEXTILE_NOTIFYCAP;
$modversion['notification']['event'][5]['description'] = _MI_TORRENT_GLOBAL_NEXTILE_NOTIFYDSC;
$modversion['notification']['event'][5]['mail_template'] = 'global_newfile_notify';
$modversion['notification']['event'][5]['mail_subject'] = _MI_TORRENT_GLOBAL_NEXTILE_NOTIFYSBJ;

$modversion['notification']['event'][6]['name'] = 'file_submit';
$modversion['notification']['event'][6]['category'] = 'category';
$modversion['notification']['event'][6]['admin_only'] = 1;
$modversion['notification']['event'][6]['title'] = _MI_TORRENT_CATEGORY_FILESUBMIT_NOTIFY;
$modversion['notification']['event'][6]['caption'] = _MI_TORRENT_CATEGORY_FILESUBMIT_NOTIFYCAP;
$modversion['notification']['event'][6]['description'] = _MI_TORRENT_CATEGORY_FILESUBMIT_NOTIFYDSC;
$modversion['notification']['event'][6]['mail_template'] = 'category_filesubmit_notify';
$modversion['notification']['event'][6]['mail_subject'] = _MI_TORRENT_CATEGORY_FILESUBMIT_NOTIFYSBJ;

$modversion['notification']['event'][7]['name'] = 'new_file';
$modversion['notification']['event'][7]['category'] = 'category';
$modversion['notification']['event'][7]['title'] = _MI_TORRENT_CATEGORY_NEXTILE_NOTIFY;
$modversion['notification']['event'][7]['caption'] = _MI_TORRENT_CATEGORY_NEXTILE_NOTIFYCAP;
$modversion['notification']['event'][7]['description'] = _MI_TORRENT_CATEGORY_NEXTILE_NOTIFYDSC;
$modversion['notification']['event'][7]['mail_template'] = 'category_newfile_notify';
$modversion['notification']['event'][7]['mail_subject'] = _MI_TORRENT_CATEGORY_NEXTILE_NOTIFYSBJ;

$modversion['notification']['event'][8]['name'] = 'approve';
$modversion['notification']['event'][8]['category'] = 'file';
$modversion['notification']['event'][8]['invisible'] = 1;
$modversion['notification']['event'][8]['title'] = _MI_TORRENT_FILE_APPROVE_NOTIFY;
$modversion['notification']['event'][8]['caption'] = _MI_TORRENT_FILE_APPROVE_NOTIFYCAP;
$modversion['notification']['event'][8]['description'] = _MI_TORRENT_FILE_APPROVE_NOTIFYDSC;
$modversion['notification']['event'][8]['mail_template'] = 'file_approve_notify';
$modversion['notification']['event'][8]['mail_subject'] = _MI_TORRENT_FILE_APPROVE_NOTIFYSBJ;

$modversion['notification']['event'][9]['name'] = 'completed';
$modversion['notification']['event'][9]['category'] = 'global';
$modversion['notification']['event'][9]['admin_only'] = 1;
$modversion['notification']['event'][9]['title'] = "_MI_TORRENT_COMPLETE_NOTIFY";
$modversion['notification']['event'][9]['caption'] = "_MI_TORRENT_COMPLETE_NOTIFYCAP";
$modversion['notification']['event'][9]['description'] = "_MI_TORRENT_COMPLETE_NOTIFYDSC";
$modversion['notification']['event'][9]['mail_template'] = 'completed_download_notify';
$modversion['notification']['event'][9]['mail_subject'] = "_MI_TORRENT_COMPLETE_NOTIFYSBJ";

$modversion['notification']['event'][10]['name'] = 'completed';
$modversion['notification']['event'][10]['category'] = 'announce';
$modversion['notification']['event'][10]['title'] = "_MI_TORRENT_COMPLETE_NOTIFY";
$modversion['notification']['event'][10]['caption'] = "_MI_TORRENT_COMPLETE_NOTIFYCAP";
$modversion['notification']['event'][10]['description'] = "_MI_TORRENT_COMPLETE_NOTIFYDSC";
$modversion['notification']['event'][10]['mail_template'] = 'completed_download_notify';
$modversion['notification']['event'][10]['mail_subject'] = "_MI_TORRENT_COMPLETE_NOTIFYSBJ";

$modversion['notification']['event'][11]['name'] = 'stopped';
$modversion['notification']['event'][11]['category'] = 'global';
$modversion['notification']['event'][11]['admin_only'] = 1;
$modversion['notification']['event'][11]['title'] = "_MI_TORRENT_STOPPED_NOTIFY";
$modversion['notification']['event'][11]['caption'] = "_MI_TORRENT_STOPPED_NOTIFYCAP";
$modversion['notification']['event'][11]['description'] = "_MI_TORRENT_STOPPED_NOTIFYDSC";
$modversion['notification']['event'][11]['mail_template'] = 'stopped_download_notify';
$modversion['notification']['event'][11]['mail_subject'] = "_MI_TORRENT_STOPPED_NOTIFYSBJ";

$modversion['notification']['event'][12]['name'] = 'stopped';
$modversion['notification']['event'][12]['category'] = 'announce';
$modversion['notification']['event'][12]['title'] = "_MI_TORRENT_STOPPED_NOTIFY";
$modversion['notification']['event'][12]['caption'] = "_MI_TORRENT_STOPPED_NOTIFYCAP";
$modversion['notification']['event'][12]['description'] = "_MI_TORRENT_STOPPED_NOTIFYDSC";
$modversion['notification']['event'][12]['mail_template'] = 'stopped_download_notify';
$modversion['notification']['event'][12]['mail_subject'] = "_MI_TORRENT_STOPPED_NOTIFYSBJ";
