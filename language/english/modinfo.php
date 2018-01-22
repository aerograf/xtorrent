<?php

// Module Info
// The name of this module
define("_MI_TORRENT_NAME","x-Torrents");

// A brief description of this module
define("_MI_TORRENT_DESC","Creates a torrents section where users can torrent/submit/rate various torrents.");

// Names of blocks for this module (Not all module has blocks)
define("_MI_TORRENT_BNAME1","Recent Torrents");
define("_MI_TORRENT_BNAME2","Top Torrents");

// Sub menu titles
define("_MI_TORRENT_SMNAME1","Submit");
define("_MI_TORRENT_SMNAME2","Popular");
define("_MI_TORRENT_SMNAME3","Top Rated");

// EXTRA OPTIONS

define("_MI_TORRENT_SEO_HTACCESS",".htaccess");
define("_MI_TORRENT_SEO_HTACCESSDESC","Allow .htaccess SEO");
define("_MI_TORRENT_THROTTLE","Allow Throttle");
define("_MI_TORRENT_THROTTLEDSC","This allow's bandwidth throttle (experimental)");
define("_MI_TORRENT_OPEN","Closed Tracker");
define("_MI_TORRENT_OPENDSC","This allows for users of the website only");
define("_MI_TORRENT_ANNOUNCEINTERVAL","Number of minutes between announces");
define("_MI_TORRENT_ANNOUNCEINTERVALDSC","Select the number of minutes per announce.");
define("_MI_TORRENT_NUMLEECHERS","Allowed Leeches");
define("_MI_TORRENT_NUMLEECHERSDSC","This allow's number of leeches");
define("_MI_TORRENT_NUMSEEDS","Allow Seeds");
define("_MI_TORRENT_NUMSEEDSDSC","This allow's number of leeches");
define("_MI_TORRENT_ANNOUNCEURL","URL for Announce");
define("_MI_TORRENT_ANNOUNCEURLDSC","If you want to SEO the announce.php file with a .htaccess here is how to redirect it.");




// Names of admin menu items
define("_MI_TORRENT_BINDEX","Main Index");
define("_MI_TORRENT_INDEXPAGE","Index Page Management");
define("_MI_TORRENT_MCATEGORY","Category Management");
define("_MI_TORRENT_MDOWNLOADS","File Management");
define("_MI_TORRENT_MUPLOADS","Image Upload");
define("_MI_TORRENT_MMIMETYPES","Mimetypes Management");
define("_MI_TORRENT_PERMISSIONS","Permission Settings");
define("_MI_TORRENT_BLOCKADMIN","Block Settings");
define("_MI_TORRENT_MVOTEDATA","Votes");

// Title of config items
define('_MI_TORRENT_POPULAR', 'Torrent Popular Count');
define('_MI_TORRENT_POPULARDSC', 'The number of hits before a torrent status will be considered as popular.');

//Display Icons
define("_MI_TORRENT_ICONDISPLAY","torrents Popular and New:");
define("_MI_TORRENT_DISPLAYICONDSC", "Select how to display the popular and new icons in torrent listing.");
define("_MI_TORRENT_DISPLAYICON1", "Display As Icons");
define("_MI_TORRENT_DISPLAYICON2", "Display As Text");
define("_MI_TORRENT_DISPLAYICON3", "Do Not Display");

define("_MI_TORRENT_DAYSNEW","torrents Days New:");
define("_MI_TORRENT_DAYSNEWDSC","The number of days a torrent status will be considered as new.");
define("_MI_TORRENT_DAYSUPDATED","Torrents Days Updated:");
define("_MI_TORRENT_DAYSUPDATEDDSC","The amount of days a torrent status will be considered as updated.");

define('_MI_TORRENT_PERPAGE', 'Torrent Listing Count');
define('_MI_TORRENT_PERPAGEDSC', 'Number of torrents to display in each category listing.');

define('_MI_TORRENT_USESHOTS', 'Display Screenshot Images?');
define('_MI_TORRENT_USESHOTSDSC', 'Select yes to display screenshot images for each torrent item');
define('_MI_TORRENT_SHOTWIDTH', 'Image Display Width');
define('_MI_TORRENT_SHOTWIDTHDSC', 'Display width for screenshot image');
define('_MI_TORRENT_SHOTHEIGHT', 'Image Display Height');
define('_MI_TORRENT_SHOTHEIGHTDSC', 'Display height for screenshot image');
define('_MI_TORRENT_CHECKHOST', 'Disallow direct torrent linking? (leeching)');
define('_MI_TORRENT_REFERERS', 'These sites can directly link to your files <br>Separate with | ');
define("_MI_TORRENT_ANONPOST","Anonymous User Submission:");
define("_MI_TORRENT_ANONPOSTDSC","Allow Anonymous users to submit or upload to your website?");
define('_MI_TORRENT_AUTOAPPROVE','Auto Approve Submitted torrents');
define('_MI_TORRENT_AUTOAPPROVEDSC','Select to approve submitted torrents without moderation.');

define('_MI_TORRENT_MAXFILESIZE','Upload Size (KB)');
define('_MI_TORRENT_MAXFILESIZEDSC','Maximum file size permitted with file uploads.');
define('_MI_TORRENT_IMGWIDTH','Upload Image width');
define('_MI_TORRENT_IMGWIDTHDSC','Maximum image width permitted when uploading image files');
define('_MI_TORRENT_IMGHEIGHT','Upload Image height');
define('_MI_TORRENT_IMGHEIGHTDSC','Maximum image height permitted when uploading image files');

define('_MI_TORRENT_UPLOADDIR','Upload Directory (No trailing slash)');
define('_MI_TORRENT_ALLOWSUBMISS','User Submissions:');
define('_MI_TORRENT_ALLOWSUBMISSDSC','Allow Users to Submit new torrents');
define('_MI_TORRENT_ALLOWUPLOADS','User Uploads:');
define('_MI_TORRENT_ALLOWUPLOADSDSC','Allow Users to upload files directly to your website');
define('_MI_TORRENT_SCREENSHOTS','Screenshots Upload Directory');
define('_MI_TORRENT_CATEGORYIMG','Category Image Upload Directory');
define('_MI_TORRENT_MAINIMGDIR','Main Image Directory');
define('_MI_TORRENT_USETHUMBS', 'Use Thumb Nails:');
define("_MI_TORRENT_USETHUMBSDSC", "Supported file types: JPG, GIF, PNG.<div style='padding-top:8px;'>WF-Section will use thumb nails for images. Set to 'No' to use orginal image if the server does not support this option.</div>");
define('_MI_TORRENT_DATEFORMAT', 'Timestamp:');
define('_MI_TORRENT_DATEFORMATDSC', 'Default Timestamp for WF-torrents:');
define('_MI_TORRENT_SHOWDISCLAIMER', 'Show Disclaimer before User Submission?');
define('_MI_TORRENT_SHOWDOWNDISCL', 'Show Disclaimer before User torrent?');
define('_MI_TORRENT_DISCLAIMER', 'Enter Submission Disclaimer Text:');
define('_MI_TORRENT_DOWNDISCLAIMER', 'Enter torrent Disclaimer Text:');
define('_MI_TORRENT_PLATFORM', 'Enter Platforms:');
define('_MI_TORRENT_SUBCATS', 'Sub-Categories:');
define('_MI_TORRENT_VERSIONTYPES', 'Version Status:');
define('_MI_TORRENT_LICENSE', 'Enter License:');
define('_MI_TORRENT_LIMITS', 'Enter File Limitations:');

define("_MI_TORRENT_SUBMITART", "torrent Submission:");
define("_MI_TORRENT_SUBMITARTDSC", "Select groups that can submit new torrents.");

define("_MI_TORRENT_IMGUPDATE", "Update Thumbnails?");
define("_MI_TORRENT_IMGUPDATEDSC", "If selected Thumbnail images will be updated at each page render, otherwise the first thumbnail image will be used regardless. <br /><br />");
define("_MI_TORRENT_QUALITY", "Thumb Nail Quality:");
define("_MI_TORRENT_QUALITYDSC", "Quality Lowest: 0 Highest: 100");
define("_MI_TORRENT_KEEPASPECT", "Keep Image Aspect Ratio?");
define("_MI_TORRENT_KEEPASPECTDSC", "");
define("_MI_TORRENT_ADMINPAGE", "Admin Index Files Count:");
define("_MI_TORRENT_AMDMINPAGEDSC", "Number of new files to display in module admin area.");
define("_MI_TORRENT_ARTICLESSORT", "Default torrent Order:");
define("_MI_TORRENT_ARTICLESSORTDSC", "Select the default order for the torrent listings.");
define("_MI_TORRENT_TITLE", "Title");
define("_MI_TORRENT_RATING", "Rating");
define("_MI_TORRENT_WEIGHT", "Weight");
define("_MI_TORRENT_POPULARITY", "Popularity");
define("_MI_TORRENT_SUBMITTED2", "Submission Date");
define('_MI_TORRENT_COPYRIGHT', 'Copyright Notice:');
define('_MI_TORRENT_COPYRIGHTDSC', 'Select to display a copyright notice on torrent page.');

define('_MI_TORRENT_POLL_TORRENT', 'Poll Torrent:');
define('_MI_TORRENT_POLL_TORRENTDSC', 'Select to poll a torrent.');
define('_MI_TORRENT_POLL_TORRENTTIME', 'Torrent Poll Refresh Every:');
define('_MI_TORRENT_POLL_TORRENTTIMEDSC', 'Number of minutes to wait before refreshing a poll.');

define('_MI_TORRENT_POLL_TRACKER', 'Poll Tracker:');
define('_MI_TORRENT_POLL_TRACKERDSC', 'Select to poll a tracker.');
define('_MI_TORRENT_POLL_TRACKERTIME', 'Tracker Poll Refresh Every:');
define('_MI_TORRENT_POLL_TRACKERTIMEDSC', 'Number of minutes to wait before refreshing a poll.');
define('_MI_TORRENT_POLL_TRACKERTIMEOUT', 'Tracker Poll Timeout:');
define('_MI_TORRENT_POLL_TRACKERTIMEOUTDSC', 'Number of seconds to wait before timing out a poll.');

// Description of each config items
define('_MI_TORRENT_PLATFORMDSC', 'List of platforms to enter <br />Separate with | IMPORTANT: Do not change this once the site is Live, Add new to the end of the list!');
define('_MI_TORRENT_SUBCATSDSC', 'Select Yes to display sub-categories. Selecting No will hide sub-categories from the listings');
define('_MI_TORRENT_LICENSEDSC', 'List of platforms to enter <br />Separate with |');

// Text for notifications
define('_MI_TORRENT_GLOBAL_NOTIFY', 'Global');
define('_MI_TORRENT_GLOBAL_NOTIFYDSC', 'Global torrents notification options.');

define('_MI_TORRENT_CATEGORY_NOTIFY', 'Category');
define('_MI_TORRENT_CATEGORY_NOTIFYDSC', 'Notification options that apply to the current file category.');

define('_MI_TORRENT_FILE_NOTIFY', 'File');
define('_MI_TORRENT_FILE_NOTIFYDSC', 'Notification options that apply to the current file.');

define('_MI_TORRENT_GLOBAL_NEWCATEGORY_NOTIFY', 'New Category');
define('_MI_TORRENT_GLOBAL_NEWCATEGORY_NOTIFYCAP', 'Notify me when a new file category is created.');
define('_MI_TORRENT_GLOBAL_NEWCATEGORY_NOTIFYDSC', 'Receive notification when a new file category is created.');
define('_MI_TORRENT_GLOBAL_NEWCATEGORY_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New file category');                              

define('_MI_TORRENT_GLOBAL_FILEMODIFY_NOTIFY', 'Modify File Requested');
define('_MI_TORRENT_GLOBAL_FILEMODIFY_NOTIFYCAP', 'Notify me of any file modification request.');
define('_MI_TORRENT_GLOBAL_FILEMODIFY_NOTIFYDSC', 'Receive notification when any file modification request is submitted.');
define('_MI_TORRENT_GLOBAL_FILEMODIFY_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : File Modification Requested');

define('_MI_TORRENT_GLOBAL_FILEBROKEN_NOTIFY', 'Broken File Submitted');
define('_MI_TORRENT_GLOBAL_FILEBROKEN_NOTIFYCAP', 'Notify me of any broken file report.');
define('_MI_TORRENT_GLOBAL_FILEBROKEN_NOTIFYDSC', 'Receive notification when any broken file report is submitted.');
define('_MI_TORRENT_GLOBAL_FILEBROKEN_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : Broken File Reported');

define('_MI_TORRENT_GLOBAL_FILESUBMIT_NOTIFY', 'File Submitted');
define('_MI_TORRENT_GLOBAL_FILESUBMIT_NOTIFYCAP', 'Notify me when any new file is submitted (awaiting approval).');
define('_MI_TORRENT_GLOBAL_FILESUBMIT_NOTIFYDSC', 'Receive notification when any new file is submitted (awaiting approval).');
define('_MI_TORRENT_GLOBAL_FILESUBMIT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New file submitted');

define('_MI_TORRENT_GLOBAL_NEWFILE_NOTIFY', 'New File');
define('_MI_TORRENT_GLOBAL_NEWFILE_NOTIFYCAP', 'Notify me when any new file is posted.');
define('_MI_TORRENT_GLOBAL_NEWFILE_NOTIFYDSC', 'Receive notification when any new file is posted.');
define('_MI_TORRENT_GLOBAL_NEWFILE_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New file');

define('_MI_TORRENT_CATEGORY_FILESUBMIT_NOTIFY', 'File Submitted');
define('_MI_TORRENT_CATEGORY_FILESUBMIT_NOTIFYCAP', 'Notify me when a new file is submitted (awaiting approval) to the current category.');   
define('_MI_TORRENT_CATEGORY_FILESUBMIT_NOTIFYDSC', 'Receive notification when a new file is submitted (awaiting approval) to the current category.');      
define('_MI_TORRENT_CATEGORY_FILESUBMIT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New file submitted in category'); 

define('_MI_TORRENT_CATEGORY_NEWFILE_NOTIFY', 'New File');
define('_MI_TORRENT_CATEGORY_NEWFILE_NOTIFYCAP', 'Notify me when a new file is posted to the current category.');   
define('_MI_TORRENT_CATEGORY_NEWFILE_NOTIFYDSC', 'Receive notification when a new file is posted to the current category.');      
define('_MI_TORRENT_CATEGORY_NEWFILE_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New file in category'); 

define('_MI_TORRENT_FILE_APPROVE_NOTIFY', 'File Approved');
define('_MI_TORRENT_FILE_APPROVE_NOTIFYCAP', 'Notify me when this file is approved.');
define('_MI_TORRENT_FILE_APPROVE_NOTIFYDSC', 'Receive notification when this file is approved.');
define('_MI_TORRENT_FILE_APPROVE_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : File Approved');

define('_MI_TORRENT_AUTHOR_INFO', "Developer Information");
define('_MI_TORRENT_AUTHOR_NAME', "Developer");
define('_MI_TORRENT_AUTHOR_DEVTEAM', "Development Team");
define('_MI_TORRENT_AUTHOR_WEBSITE', "Developer website");
define('_MI_TORRENT_AUTHOR_EMAIL', "Developer email");
define('_MI_TORRENT_AUTHOR_CREDITS', "Credits");
define('_MI_TORRENT_MODULE_INFO', "Module Development Information");
define('_MI_TORRENT_MODULE_STATUS', "Development Status");
define('_MI_TORRENT_MODULE_DEMO', "Demo Site");
define('_MI_TORRENT_MODULE_SUPPORT', "Official support site");
define('_MI_TORRENT_MODULE_BUG', "Report a bug for this module");
define('_MI_TORRENT_MODULE_FEATURE', "Suggest a new feature for this module");
define('_MI_TORRENT_MODULE_DISCLAIMER', "Disclaimer");
define('_MI_TORRENT_RELEASE', "Release Date: ");

define('_MI_TORRENT_MODULE_MAILLIST', "WF-Section Mailing Lists");

define('_MI_TORRENT_MODULE_MAILANNOUNCEMENTS', "Announcements Mailing List");
define('_MI_TORRENT_MODULE_MAILBUGS', "Bug Mailing List");
define('_MI_TORRENT_MODULE_MAILFEATURES', "Features Mailing List");

define('_MI_TORRENT_MODULE_MAILANNOUNCEMENTSDSC', "Get the latest announcements from WF-Section.");
define('_MI_TORRENT_MODULE_MAILBUGSDSC', "Bug Tracking and submission mailing list");
define('_MI_TORRENT_MODULE_MAILFEATURESDSC', "Request New Features mailing list.");

define('_MI_TORRENT_WARNINGTEXT', "THE SOFTWARE IS PROVIDED BY WF-SECTIONS \"AS IS\" AND \"WITH ALL FAULTS.\"
WF-SECTIONS MAKES NO REPRESENTATIONS OR WARRANTIES OF ANY KIND CONCERNING
THE QUALITY, SAFETY OR SUITABILITY OF THE SOFTWARE, EITHER EXPRESS OR
IMPLIED, INCLUDING WITHOUT LIMITATION ANY IMPLIED WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, OR NON-INFRINGEMENT.
FURTHER, ABLEMEDIA MAKES NO REPRESENTATIONS OR WARRANTIES AS TO THE TRUTH,
ACCURACY OR COMPLETENESS OF ANY STATEMENTS, INFORMATION OR MATERIALS
CONCERNING THE SOFTWARE THAT IS CONTAINED IN WF-SECTIONS WEBSITE. IN NO
EVENT WILL ABLEMEDIA BE LIABLE FOR ANY INDIRECT, PUNITIVE, SPECIAL,
INCIDENTAL OR CONSEQUENTIAL DAMAGES HOWEVER THEY MAY ARISE AND EVEN IF
WF-SECTIONS HAS BEEN PREVIOUSLY ADVISED OF THE POSSIBILITY OF SUCH DAMAGES..");

define('_MI_TORRENT_AUTHOR_CREDITSTEXT',"The WF-Sections Team would like to thank the following people for their help and support during the development phase of this module:<br /><br />
tom, mking, paco1969, mharoun, Talis, m0nty, steenlnielsen, Clubby, Geronimo, bd_csmc, herko, LANG, Stewdio, tedsmith, veggieryan, carnuke, MadFish.
<br /><br />And on a personal note, I would like to thank the special girl in my life who I love dearly and who gives me the strength and support to do all of this.
");
define('_MI_TORRENT_AUTHOR_BUGFIXES', "Bug Fix History");

define('_MI_TORRENT_COPYRIGHTIMAGE', "Images copyright WF-Project and may only be used with permission");

define('_MI_TORRENT_ADMENU3', 'About');
