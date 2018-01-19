<?php

// Module Info
// The name of this module
//define('_MI_XTORRENT_POLL_TORRENT', 'Poll Torrent Trackers');
//define('_MI_XTORRENT_POLL_TORRENTDSC', 'Enable this if you want to poll the trackers.');
//define('_MI_XTORRENT_POLL_TORRENTTIME', 'Polling Intervals');
//define('_MI_XTORRENT_POLL_TORRENTTIMEDSC', 'The interval you want to wait between polls.');

define('_MI_XTORRENT_HASHKEYS', 'Hashkeys');
define('_MI_XTORRENT_HASHKEYSDSC', 'Set the type of checksum you would like to use in this module.');

define('_MI_XTORRENT_XSOAP_CATEGORY', 'SOAP Categories');
define('_MI_XTORRENT_XSOAP_LISTING', 'SOAP Listings');
define('_MI_XTORRENT_XSOAP_RETRIEVE', 'SOAP Retrieve');

define('_MI_XSOAP_SERVERS', 'X-SOAP Servers');
define('_MI_XSOAP_SERVERSDESC', 'Put the X-Soap Services you wish to exchange with in the following format <br><em>username:password@uri|username:password@uri</em><br/><font size="-2"><strong>ie. admin:adminpass@http://www.example.com/modules/xsoap/|...|...</strong></font>');
define('_MI_XSOAP_SERVERSSUBMIT', 'Send Sydnication');
define('_MI_XSOAP_SERVERSSUBMITDESC', 'This enables the ability for your X-Torrent to send Torrents via server pathways.');
define('_MI_XSOAP_SERVERSRECIEVE', 'Recieve Sydnication');
define('_MI_XSOAP_SERVERSRECIEVEDESC', 'This enables the ability for your x-soap to recieve Torrents via server pathways.');
define('_MI_XSOAP_SERVERSEXCHANGE', 'Server Exchange');
define('_MI_XSOAP_SERVERSEXCHANGEDESC', 'This enables the ability for your X-Torrent data off other Torrent servers with data exchange.');
define('_MI_XSOAP_SERVERKEY', 'Server Key');
define('_MI_XSOAP_SERVERKEYDESC', 'This is the Torrent server key this is used to identify it on other X-Torrent servers.');

define('_MI_ACCESS_CLEAR', 'Access Cache Clear');
define('_MI_ACCESS_CLEARDESC', 'This is the number of weeks you would like access permit to exist.');

define('_MI_XTORRENT_NAME', 'X-Torrents');

// A brief description of this module
define('_MI_XTORRENT_DESC', 'Creates a Torrents section where users can Torrent/submit/rate various Torrents.');

// Names of blocks for this module (Not all module has blocks)
define('_MI_XTORRENT_BNAME1', 'Recent Torrents');
define('_MI_XTORRENT_BNAME2', 'Top Torrents');

// Sub menu titles
define('_MI_XTORRENT_SMNAME1', 'Submit');
define('_MI_XTORRENT_SMNAME2', 'Popular');
define('_MI_XTORRENT_SMNAME3', 'Top Rated');

// EXTRA OPTIONS
define('_MI_XTORRENT_CURRENCIES', 'Currency');
define('_MI_XTORRENT_CURRENCIESDESC', 'Paypal currencies for Torrent charge');

define('_MI_IPN_DEBUG_LEVEL', 'PayPal IPN Debug Level');
define('_MI_IPN_DEBUG_LEVELDESC', 'Set the debug level you want for the IPN (0 = Off)');

define('_MI_IPN_LOG_ENTRIES', 'PayPal Log Clearance');
define('_MI_IPN_LOG_ENTRIESDESC', 'Set the amount of IPN Logs to Clear.');

define('_MI_PAYPAL_PAYSUBTITLE', 'Payment Screen Sub-title');
define('_MI_PAYPAL_PAYSUBTITLEDESC', 'This is the subtitle under the header of the payment screen');

define('_MI_PAYPAL_PAYCLAUSE', 'Payment Clause');
define('_MI_PAYPAL_PAYCLAUSEDESC', 'This is the clause or disclaimer for making a payment');

//define('_MI_ACCESS_CLEAR','User Table clearance');
//define('_MI_ACCESS_CLEARDESC','Set this to the number of weeks you want the Torrent to remain active');

define('_MI_PAYPAL_IMAGE', 'PayPal Image');
define('_MI_PAYPAL_IMAGEDESC', 'Set the url for the image you want displayed in the paypal checkout.');

define('_MI_XTORRENT_AGENTSDISALLOWED', 'Agents Disallowed');
define('_MI_XTORRENT_AGENTSDISALLOWEDDESC', 'Agents disallowed to access the tracker.<br>(ereg support, seperate expressions with <strong>|</strong>)');
define('_MI_XTORRENT_SEO_HTACCESS', '.htaccess');
define('_MI_XTORRENT_SEO_HTACCESSDESC', 'Allow .htaccess SEO');
define('_MI_XTORRENT_THROTTLE', 'Allow Throttle');
define('_MI_XTORRENT_THROTTLEDSC', 'This allow\'s bandwidth throttle (experimental)');
define('_MI_XTORRENT_OPEN', 'Closed Tracker');
define('_MI_XTORRENT_OPENDSC', 'This allows for users of the website only');
define('_MI_XTORRENT_ANNOUNCEINTERVAL', 'Number of minutes between announces');
define('_MI_XTORRENT_ANNOUNCEINTERVALDSC', 'Select the number of minutes per announce.');
define('_MI_XTORRENT_NUMLEECHERS', 'Allowed Leeches');
define('_MI_XTORRENT_NUMLEECHERSDSC', 'This allow\'s number of leeches');
define('_MI_XTORRENT_NUMSEEDS', 'Allow Seeds');
define('_MI_XTORRENT_NUMSEEDSDSC', 'This allow\'s number of leeches');
define('_MI_XTORRENT_ANNOUNCEURL', 'URL for Announce');
define('_MI_XTORRENT_ANNOUNCEURLDSC', 'If you want to SEO the announce. file with a .htaccess here is how to redirect it.');

// Names of admin menu items
define('_MI_XTORRENT_BINDEX', 'Main Index');
define('_MI_XTORRENT_INDEXPAGE', 'Index Page Management');
define('_MI_XTORRENT_MCATEGORY', 'Category Management');
define('_MI_XTORRENT_MDOWNLOADS', 'File Management');
define('_MI_XTORRENT_MUPLOADS', 'Image Upload');
define('_MI_XTORRENT_MMIMETYPES', 'Mimetypes Management');
define('_MI_XTORRENT_PERMISSIONS', 'Permission Settings');
define('_MI_XTORRENT_BLOCKADMIN', 'Block Settings');
define('_MI_XTORRENT_MVOTEDATA', 'Votes');

// Title of config items
define('_MI_XTORRENT_POPULAR', 'Torrent Popular Count');
define('_MI_XTORRENT_POPULARDSC', 'The number of hits before a Torrent status will be considered as popular.');

//Display Icons
define('_MI_XTORRENT_ICONDISPLAY', 'Torrents Popular and New:');
define('_MI_XTORRENT_DISPLAYICONDSC', 'Select how to display the popular and new icons in Torrent listing.');
define('_MI_XTORRENT_DISPLAYICON1', 'Display As Icons');
define('_MI_XTORRENT_DISPLAYICON2', 'Display As Text');
define('_MI_XTORRENT_DISPLAYICON3', 'Do Not Display');

define('_MI_XTORRENT_DAYSNEW', 'Torrents Days New:');
define('_MI_XTORRENT_DAYSNEWDSC', 'The number of days a Torrent status will be considered as new.');
define('_MI_XTORRENT_DAYSUPDATED', 'Torrents Days Updated:');
define('_MI_XTORRENT_DAYSUPDATEDDSC', 'The amount of days a Torrent status will be considered as updated.');

define('_MI_XTORRENT_PERPAGE', 'Torrent Listing Count');
define('_MI_XTORRENT_PERPAGEDSC', 'Number of Torrents to display in each category listing.');

define('_MI_XTORRENT_USESHOTS', 'Display Screenshot Images?');
define('_MI_XTORRENT_USESHOTSDSC', 'Select yes to display screenshot images for each Torrent item');
define('_MI_XTORRENT_SHOTWIDTH', 'Image Display Width');
define('_MI_XTORRENT_SHOTWIDTHDSC', 'Display width for screenshot image');
define('_MI_XTORRENT_SHOTHEIGHT', 'Image Display Height');
define('_MI_XTORRENT_SHOTHEIGHTDSC', 'Display height for screenshot image');
define('_MI_XTORRENT_CHECKHOST', 'Disallow direct Torrent linking? (leeching)');
define('_MI_XTORRENT_REFERERS', 'These sites can directly link to your files <br>Separate with');
define('_MI_XTORRENT_ANONPOST', 'Anonymous User Submission:');
define('_MI_XTORRENT_ANONPOSTDSC', 'Allow Anonymous users to submit or upload to your website?');
define('_MI_XTORRENT_AUTOAPPROVE', 'Auto Approve Submitted Torrents');
define('_MI_XTORRENT_AUTOAPPROVEDSC', 'Select to approve submitted Torrents without moderation.');

define('_MI_XTORRENT_MAXFILESIZE', 'Upload Size (KB)');
define('_MI_XTORRENT_MAXFILESIZEDSC', 'Maximum file size permitted with file uploads.');
define('_MI_XTORRENT_IMGWIDTH', 'Upload Image width');
define('_MI_XTORRENT_IMGWIDTHDSC', 'Maximum image width permitted when uploading image files');
define('_MI_XTORRENT_IMGHEIGHT', 'Upload Image height');
define('_MI_XTORRENT_IMGHEIGHTDSC', 'Maximum image height permitted when uploading image files');

define('_MI_XTORRENT_UPLOADDIR', 'Upload Directory (No trailing slash)');
define('_MI_XTORRENT_ALLOWSUBMISS', 'User Submissions:');
define('_MI_XTORRENT_ALLOWSUBMISSDSC', 'Allow Users to Submit new Torrents');
define('_MI_XTORRENT_ALLOWUPLOADS', 'User Uploads:');
define('_MI_XTORRENT_ALLOWUPLOADSDSC', 'Allow Users to upload files directly to your website');
define('_MI_XTORRENT_SCREENSHOTS', 'Screenshots Upload Directory');
define('_MI_XTORRENT_CATEGORYIMG', 'Category Image Upload Directory');
define('_MI_XTORRENT_MAINIMGDIR', 'Main Image Directory');
define('_MI_XTORRENT_USETHUMBS', 'Use Thumb Nails:');
define('_MI_XTORRENT_USETHUMBSDSC', 'Supported file types: JPG, GIF, PNG.<div style="padding-top: 8px;">XT-Section will use thumb nails for images. Set to "No" to use orginal image if the server does not support this option.</div>');
define('_MI_XTORRENT_DATEFORMAT', 'Timestamp:');
define('_MI_XTORRENT_DATEFORMATDSC', 'Default Timestamp for X-Torrents:');
define('_MI_XTORRENT_SHOWDISCLAIMER', 'Show Disclaimer before User Submission?');
define('_MI_XTORRENT_SHOWDOWNDISCL', 'Show Disclaimer before User Torrent?');
define('_MI_XTORRENT_DISCLAIMER', 'Enter Submission Disclaimer Text:');
define('_MI_XTORRENT_DOWNDISCLAIMER', 'Enter Torrent Disclaimer Text:');
define('_MI_XTORRENT_PLATFORM', 'Enter Platforms:');
define('_MI_XTORRENT_SUBCATS', 'Sub-Categories:');
define('_MI_XTORRENT_VERSIONTYPES', 'Version Status:');
define('_MI_XTORRENT_LICENSE', 'Enter License:');
define('_MI_XTORRENT_LIMITS', 'Enter File Limitations:');

define('_MI_XTORRENT_SUBMITART', 'Torrent Submission:');
define('_MI_XTORRENT_SUBMITARTDSC', 'Select groups that can submit new Torrents.');

define('_MI_XTORRENT_IMGUPDATE', 'Update Thumbnails?');
define('_MI_XTORRENT_IMGUPDATEDSC', 'If selected Thumbnail images will be updated at each page render, otherwise the first thumbnail image will be used regardless. <br><br>');
define('_MI_XTORRENT_QUALITY', 'Thumb Nail Quality:');
define('_MI_XTORRENT_QUALITYDSC', 'Quality Lowest: 0 Highest: 100');
define('_MI_XTORRENT_KEEPASPECT', 'Keep Image Aspect Ratio?');
define('_MI_XTORRENT_KEEPASPECTDSC', '');
define('_MI_XTORRENT_ADMINPAGE', 'Admin Index Files Count:');
define('_MI_XTORRENT_AMDMINPAGEDSC', 'Number of new files to display in module admin area.');
define('_MI_XTORRENT_ARTICLESSORT', 'Default Torrent Order:');
define('_MI_XTORRENT_ARTICLESSORTDSC', 'Select the default order for the Torrent listings.');
define('_MI_XTORRENT_TITLE', 'Title');
define('_MI_XTORRENT_RATING', 'Rating');
define('_MI_XTORRENT_WEIGHT', 'Weight');
define('_MI_XTORRENT_POPULARITY', 'Popularity');
define('_MI_XTORRENT_SUBMITTED2', 'Submission Date');
define('_MI_XTORRENT_COPYRIGHT', 'Copyright Notice:');
define('_MI_XTORRENT_COPYRIGHTDSC', 'Select to display a copyright notice on Torrent page.');

define('_MI_XTORRENT_POLL_TORRENT', 'Poll Torrent:');
define('_MI_XTORRENT_POLL_TORRENTDSC', 'Select to poll a Torrent.');
define('_MI_XTORRENT_POLL_TORRENTTIME', 'Torrent Poll Refresh Every:');
define('_MI_XTORRENT_POLL_TORRENTTIMEDSC', 'Number of minutes to wait before refreshing a poll.');

define('_MI_XTORRENT_POLL_TRACKER', 'Poll Tracker:');
define('_MI_XTORRENT_POLL_TRACKERDSC', 'Select to poll a tracker.');
define('_MI_XTORRENT_POLL_TRACKERTIME', 'Tracker Poll Refresh Every:');
define('_MI_XTORRENT_POLL_TRACKERTIMEDSC', 'Number of minutes to wait before refreshing a poll.');
define('_MI_XTORRENT_POLL_TRACKERTIMEOUT', 'Tracker Poll Timeout:');
define('_MI_XTORRENT_POLL_TRACKERTIMEOUTDSC', 'Number of seconds to wait before timing out a poll.');

// Description of each config items
define('_MI_XTORRENT_PLATFORMDSC', 'List of platforms to enter <br>Separate withIMPORTANT: Do not change this once the site is Live, Add new to the end of the list!');
define('_MI_XTORRENT_SUBCATSDSC', 'SELECT Yes TO display sub-categories. Selecting NO will hide sub-categories FROM the listings');
define('_MI_XTORRENT_LICENSEDSC', 'List of platforms to enter <br>Separate with');

// Text for notifications
define('_MI_XTORRENT_GLOBAL_NOTIFY', 'Global');
define('_MI_XTORRENT_GLOBAL_NOTIFYDSC', 'Global Torrents notification options.');

define('_MI_XTORRENT_CATEGORY_NOTIFY', 'Category');
define('_MI_XTORRENT_CATEGORY_NOTIFYDSC', 'Notification options that apply to the current file category.');

define('_MI_XTORRENT_FILE_NOTIFY', 'File');
define('_MI_XTORRENT_FILE_NOTIFYDSC', 'Notification options that apply to the current file.');

define('_MI_XTORRENT_GLOBAL_NEWCATEGORY_NOTIFY', 'New Category');
define('_MI_XTORRENT_GLOBAL_NEWCATEGORY_NOTIFYCAP', 'Notify me when a new file category is created.');
define('_MI_XTORRENT_GLOBAL_NEWCATEGORY_NOTIFYDSC', 'Receive notification when a new file category is created.');
define('_MI_XTORRENT_GLOBAL_NEWCATEGORY_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New file category');

define('_MI_XTORRENT_GLOBAL_FILEMODIFY_NOTIFY', 'Modify File Requested');
define('_MI_XTORRENT_GLOBAL_FILEMODIFY_NOTIFYCAP', 'Notify me of any file modification request.');
define('_MI_XTORRENT_GLOBAL_FILEMODIFY_NOTIFYDSC', 'Receive notification when any file modification request is submitted.');
define('_MI_XTORRENT_GLOBAL_FILEMODIFY_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : File Modification Requested');

define('_MI_XTORRENT_GLOBAL_FILEBROKEN_NOTIFY', 'Broken File Submitted');
define('_MI_XTORRENT_GLOBAL_FILEBROKEN_NOTIFYCAP', 'Notify me of any broken file report.');
define('_MI_XTORRENT_GLOBAL_FILEBROKEN_NOTIFYDSC', 'Receive notification when any broken file report is submitted.');
define('_MI_XTORRENT_GLOBAL_FILEBROKEN_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : Broken File Reported');

define('_MI_XTORRENT_GLOBAL_FILESUBMIT_NOTIFY', 'File Submitted');
define('_MI_XTORRENT_GLOBAL_FILESUBMIT_NOTIFYCAP', 'Notify me when any new file is submitted (awaiting approval).');
define('_MI_XTORRENT_GLOBAL_FILESUBMIT_NOTIFYDSC', 'Receive notification when any new file is submitted (awaiting approval).');
define('_MI_XTORRENT_GLOBAL_FILESUBMIT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New file submitted');

define('_MI_XTORRENT_GLOBAL_NEXTILE_NOTIFY', 'New File');
define('_MI_XTORRENT_GLOBAL_NEXTILE_NOTIFYCAP', 'Notify me when any new file is posted.');
define('_MI_XTORRENT_GLOBAL_NEXTILE_NOTIFYDSC', 'Receive notification when any new file is posted.');
define('_MI_XTORRENT_GLOBAL_NEXTILE_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New file');

define('_MI_XTORRENT_CATEGORY_FILESUBMIT_NOTIFY', 'File Submitted');
define('_MI_XTORRENT_CATEGORY_FILESUBMIT_NOTIFYCAP', 'Notify me when a new file is submitted (awaiting approval);to the current category.');
define('_MI_XTORRENT_CATEGORY_FILESUBMIT_NOTIFYDSC', 'Receive notification when a new file is submitted (awaiting approval);to the current category.');
define('_MI_XTORRENT_CATEGORY_FILESUBMIT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New file submitted in category');

define('_MI_XTORRENT_CATEGORY_NEXTILE_NOTIFY', 'New File');
define('_MI_XTORRENT_CATEGORY_NEXTILE_NOTIFYCAP', 'Notify me when a new file is posted to the current category.');
define('_MI_XTORRENT_CATEGORY_NEXTILE_NOTIFYDSC', 'Receive notification when a new file is posted to the current category.');
define('_MI_XTORRENT_CATEGORY_NEXTILE_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New file in category');

define('_MI_XTORRENT_FILE_APPROVE_NOTIFY', 'File Approved');
define('_MI_XTORRENT_FILE_APPROVE_NOTIFYCAP', 'Notify me when this file is approved.');
define('_MI_XTORRENT_FILE_APPROVE_NOTIFYDSC', 'Receive notification when this file is approved.');
define('_MI_XTORRENT_FILE_APPROVE_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : File Approved');

define('_MI_XTORRENT_AUTHOR_INFO', 'Developer Information');
define('_MI_XTORRENT_AUTHOR_NAME', 'Developer');
define('_MI_XTORRENT_AUTHOR_DEVTEAM', 'Development Team');
define('_MI_XTORRENT_AUTHOR_WEBSITE', 'Developer website');
define('_MI_XTORRENT_AUTHOR_EMAIL', 'Developer email');
define('_MI_XTORRENT_AUTHOR_CREDITS', 'Credits');
define('_MI_XTORRENT_MODULE_INFO', 'Module Development Information');
define('_MI_XTORRENT_MODULE_STATUS', 'Development Status');
define('_MI_XTORRENT_MODULE_DEMO', 'Demo Site');
define('_MI_XTORRENT_MODULE_SUPPORT', 'Official support site');
define('_MI_XTORRENT_MODULE_BUG', 'Report a bug for this module');
define('_MI_XTORRENT_MODULE_FEATURE', 'Suggest a new feature for this module');
define('_MI_XTORRENT_MODULE_DISCLAIMER', 'Disclaimer');
define('_MI_XTORRENT_RELEASE', 'Release Date:');

define('_MI_XTORRENT_MODULE_MAILLIST', 'XT-Section Mailing Lists');

define('_MI_XTORRENT_MODULE_MAILANNOUNCEMENTS', 'Announcements Mailing List');
define('_MI_XTORRENT_MODULE_MAILBUGS', 'Bug Mailing List');
define('_MI_XTORRENT_MODULE_MAILFEATURES', 'Features Mailing List');

define('_MI_XTORRENT_MODULE_MAILANNOUNCEMENTSDSC', 'Get the latest announcements from XT-Section.');
define('_MI_XTORRENT_MODULE_MAILBUGSDSC', 'Bug Tracking and submission mailing list');
define('_MI_XTORRENT_MODULE_MAILFEATURESDSC', 'Request New Features mailing list.');

define('_MI_XTORRENT_WARNINGTEXT', 'THE SOFTWARE IS PROVIDED BY X-Torrent "AS IS" AND "WITH ALL FAULTS".
X-Torrent MAKES NO REPRESENTATIONS OR WARRANTIES OF ANY KIND CONCERNING
THE QUALITY, SAFETY OR SUITABILITY OF THE SOFTWARE, EITHER EXPRESS OR
IMPLIED, INCLUDING WITHOUT LIMITATION ANY IMPLIED WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, OR NON-INFRINGEMENT.
FURTHER, CHRONOLABS MAKES NO REPRESENTATIONS OR WARRANTIES AS TO THE TRUTH,
ACCURACY OR COMPLETENESS OF ANY STATEMENTS, INFORMATION OR MATERIALS
CONCERNING THE SOFTWARE THAT IS CONTAINED IN X-Torrent WEBSITE. IN NO
EVENT WILL CHRONOLABS BE LIABLE FOR ANY INDIRECT, PUNITIVE, SPECIAL,
INCIDENTAL OR CONSEQUENTIAL DAMAGES HOWEVER THEY MAY ARISE AND EVEN IF
X-Torrent HAS BEEN PREVIOUSLY ADVISED OF THE POSSIBILITY OF SUCH DAMAGES..');

define('_MI_XTORRENT_AUTHOR_CREDITSTEXT', 'The X-Torrent Team would like to thank the following people for their help and support during the development phase of this module:<br><br>
wishcraft, lordpeter in association with tom, mking, paco1969, mharoun, Talis, m0nty, steenlnielsen, Clubby, Geronimo, bd_csmc, herko, LANG, Stewdio, tedsmith, veggieryan, carnuke, MadFish.
<br><br>And on a personal note, I would like to thank the special girl in my life who I love dearly and who gives me the strength and support to do all of this.');
define('_MI_XTORRENT_AUTHOR_BUGFIXES', 'Bug Fix History');

define('_MI_XTORRENT_COPYRIGHTIMAGE', 'Images copyright X-Torrent and may only be used with permission');

define('_MI_XTORRENT_ADMENU3', 'About');

define('_MI_XTORRENT_PAYMENTS', 'Payment Register');
define('_MI_XTORRENT_PCONSOLID', 'Consolidate Payments');


//Help
define('_MI_XTORRENT_DIRNAME', basename(dirname(dirname(__DIR__))));
define('_MI_XTORRENT_HELP_HEADER', __DIR__.'/help/helpheader.tpl');
define('_MI_XTORRENT_BACK_2_ADMIN', 'Back to Administration of ');
define('_MI_XTORRENT_OVERVIEW', 'Overview');

//define('_MI_XTORRENT_HELP_DIR', __DIR__);

//help multi-page
define('_MI_XTORRENT_HELP_DISCLAIMER', 'Disclaimer');
define('_MI_XTORRENT_HELP_LICENSE', 'License');
define('_MI_XTORRENT_HELP_SUPPORT', 'Support');
