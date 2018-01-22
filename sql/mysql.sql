#
# Table structure for table `xtorrent_users`
#

CREATE TABLE xtorrent_users (
  `id` int(10) unsigned NOT NULL auto_increment,                                     
  `uid` int(20) default NULL,                                                        
	`lid` int(20) default NULL,     
  `username` varchar(40) NOT NULL default '',                                        
  `old_password` varchar(40) NOT NULL default '',                                    
  `passhash` varchar(32) NOT NULL default '',                                        
  `secret` varchar(20) character set latin1 collate latin1_bin NOT NULL default '',  
  `uploaded` int(20) default NULL,                                                   
  `downloaded` int(20) default NULL,                                                 
  `enabled` enum('yes','no') default 'yes',                                          
	`last_access` datetime default NULL,                                               
	`passkey` varchar(128) default NULL,                                               
PRIMARY KEY  (`id`)              
) ENGINE=MyISAM;

#
# Table structure for table `xtorrent_peers`
#


CREATE TABLE xtorrent_peers (
  id int(10) unsigned NOT NULL auto_increment,
  torrent int(10) unsigned NOT NULL default '0',
  peer_id varchar(20) binary NOT NULL default '',
  ip varchar(64) NOT NULL default '',
  port smallint(5) unsigned NOT NULL default '0',
  uploaded bigint(20) unsigned NOT NULL default '0',
  downloaded bigint(20) unsigned NOT NULL default '0',
  to_go bigint(20) unsigned NOT NULL default '0',
  seeder enum('yes','no') NOT NULL default 'no',
  started datetime NOT NULL default '0000-00-00 00:00:00',
  last_action datetime NOT NULL default '0000-00-00 00:00:00',
  connectable enum('yes','no') NOT NULL default 'yes',
  userid int(10) unsigned NOT NULL default '0',
  agent varchar(60) NOT NULL default '',
  finishedat int(10) unsigned NOT NULL default '0',
  downloadoffset bigint(20) unsigned NOT NULL default '0',
  uploadoffset bigint(20) unsigned NOT NULL default '0',
  passkey varchar(32) NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY torrent_peer_id (torrent,peer_id),
  KEY torrent (torrent),
  KEY torrent_seeder (torrent,seeder),
  KEY last_action (last_action),
  KEY connectable (connectable),
  KEY userid (userid)
) ENGINE=MyISAM;

#
# Table structure for table `xtorrent_broken`
#

CREATE TABLE xtorrent_broken (
  reportid int(5) NOT NULL auto_increment,
  lid int(11) NOT NULL default '0',
  sender int(11) NOT NULL default '0',
  ip varchar(20) NOT NULL default '',
  date varchar(11) NOT NULL default '0',
  confirmed enum('0','1') NOT NULL default '0',
  acknowledged enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (reportid),
  KEY lid (lid),
  KEY sender (sender),
  KEY ip (ip)
) ENGINE=MyISAM;

#
# Dumping data for table `xtorrent_broken`
#


# --------------------------------------------------------

#
# Table structure for table `xtorrent_cat`
#

CREATE TABLE xtorrent_cat (
  cid int(5) unsigned NOT NULL auto_increment,
  pid int(5) unsigned NOT NULL default '0',
  title varchar(50) NOT NULL default '',
  imgurl varchar(150) NOT NULL default '',
  description varchar(255) NOT NULL default '',
  total int(11) NOT NULL default '0',
  summary text NOT NULL,
  spotlighttop int(11) NOT NULL default '0',
  spotlighthis int(11) NOT NULL default '0',
  nohtml int(1) NOT NULL default '0',
  nosmiley int(1) NOT NULL default '0',
  noxcodes int(1) NOT NULL default '0',
  noimages int(1) NOT NULL default '0',
  nobreak int(1) NOT NULL default '1',
  weight int(11) NOT NULL default '0',
  PRIMARY KEY  (cid),
  KEY pid (pid)
) ENGINE=MyISAM;

#
# Dumping data for table `xtorrent_cat`
#

# --------------------------------------------------------

#
# Table structure for table `xtorrent_downloads`
#

CREATE TABLE xtorrent_downloads (
  lid int(11) unsigned NOT NULL auto_increment,
  cid int(5) unsigned NOT NULL default '0',
  title varchar(100) NOT NULL default '',
  url varchar(255) NOT NULL default '',
  homepage varchar(100) NOT NULL default '',
  version varchar(20) NOT NULL default '',
  size int(8) NOT NULL default '0',
  platform varchar(50) NOT NULL default '',
  screenshot varchar(255) NOT NULL default '',
  submitter int(11) NOT NULL default '0',
  publisher varchar(255) NOT NULL default '',
  status tinyint(2) NOT NULL default '0',
  date int(10) NOT NULL default '0',
  hits int(11) unsigned NOT NULL default '0',
  rating double(6,4) NOT NULL default '0.0000',
  votes int(11) unsigned NOT NULL default '0',
  comments int(11) unsigned NOT NULL default '0',
  license varchar(255) NOT NULL default '',
  mirror varchar(255) NOT NULL default '',
  price varchar(10) NOT NULL default 'Free',
  paypalemail varchar(255) NOT NULL default '',
  features text NOT NULL,
  requirements text NOT NULL,
  homepagetitle varchar(255) NOT NULL default '',
  forumid int(11) NOT NULL default '0',
  limitations varchar(255) NOT NULL default '30 day trial',
  dhistory text NOT NULL,
  published int(11) NOT NULL default '1089662528',
  expired int(10) NOT NULL default '0',
  updated int(11) NOT NULL default '0',
  offline tinyint(1) NOT NULL default '0',
  description text NOT NULL,
  ipaddress varchar(120) NOT NULL default '0',
  notifypub int(1) NOT NULL default '0',
  PRIMARY KEY  (lid),
  KEY cid (cid),
  KEY status (status),
  KEY title (title(40))
) ENGINE=MyISAM;

#
# Dumping data for table `xtorrent_downloads`
#

# --------------------------------------------------------

#
# Table structure for table `xtorrent_files`
#

CREATE TABLE xtorrent_files (
  lid int(11) unsigned NOT NULL default '0',
  file varchar(255) NOT NULL default ''
) ENGINE=MyISAM;

#
# Dumping data for table `xtorrent_files`
#

#
# Table structure for table `xtorrent_torrent`
#

CREATE TABLE `xtorrent_torrent` (                  
`lid` int(11) unsigned NOT NULL default '0',              
`seeds` int(5) unsigned NOT NULL default '0',             
`leechers` int(5) unsigned NOT NULL default '0',          
`totalsize` float(5,2) unsigned NOT NULL default '0.00',  
`modifiedby` varchar(250) NOT NULL default '',            
`tname` varchar(255) NOT NULL default '',                 
`infoHash` varchar(128) default NULL,                     
`announce` varchar(255) default NULL,                     
`md5sum` varchar(32) default NULL,                        
`added` int(12) default NULL                              
) ENGINE=MyISAM;

#
# Dumping data for table `xtorrent_torrent`
#

# --------------------------------------------------------

#
# Table structure for table `xtorrent_tracker`
#

CREATE TABLE xtorrent_tracker (
  lid int(11) unsigned NOT NULL default '0',
  seeds int(5) unsigned NOT NULL default '0',
  leechers int(5) unsigned NOT NULL default '0',
  tracker varchar(250) NOT NULL default ''
) ENGINE=MyISAM;

#
# Dumping data for table `xtorrent_tracker`
#

# --------------------------------------------------------

#
# Table structure for table `xtorrent_poll`
#

CREATE TABLE xtorrent_poll (
  lid int(11) unsigned NOT NULL default '0',
  torrent int(11) unsigned NOT NULL default '0',
  tracker int(11) unsigned NOT NULL default '0'
) ENGINE=MyISAM;

#
# Dumping data for table `xtorrent_poll`
#

# --------------------------------------------------------


#
# Table structure for table `xtorrent_indexpage`
#

CREATE TABLE xtorrent_indexpage (
  indeximage varchar(255) NOT NULL default 'blank.png',
  indexheading varchar(255) NOT NULL default 'X-Torrent',
  indexheader text NOT NULL,
  indexfooter text NOT NULL,
  nohtml tinyint(8) NOT NULL default '1',
  nosmiley tinyint(8) NOT NULL default '1',
  noxcodes tinyint(8) NOT NULL default '1',
  noimages tinyint(8) NOT NULL default '1',
  nobreak tinyint(4) NOT NULL default '0',
  indexheaderalign varchar(25) NOT NULL default 'left',
  indexfooteralign varchar(25) NOT NULL default 'center',
  FULLTEXT KEY indexheading (indexheading),
  FULLTEXT KEY indexheader (indexheader),
  FULLTEXT KEY indexfooter (indexfooter)
) ENGINE=MyISAM;

#
# Dumping data for table `xtorrent_indexpage`
#

INSERT INTO xtorrent_indexpage VALUES ('logo-en.gif', 'X-Torrent', '<div><b>Welcome to the X-Torrent Section.</b></div>', 'X-Torrent', 0, 0, 0, 0, 1, 'left', 'Center');

# --------------------------------------------------------

#
# Table structure for table `xtorrent_mimetypes`
#

CREATE TABLE xtorrent_mimetypes (
  mime_id int(11) NOT NULL auto_increment,
  mime_ext varchar(60) NOT NULL default '',
  mime_types text NOT NULL,
  mime_name varchar(255) NOT NULL default '',
  mime_admin int(1) NOT NULL default '1',
  mime_user int(1) NOT NULL default '0',
  KEY mime_id (mime_id)
) ENGINE=MyISAM;

#
# Dumping data for table `xtorrent_mimetypes`
#

INSERT INTO xtorrent_mimetypes VALUES (1, 'torrent', 'application/x-bittorrent application/octet-stream', 'Binary Torrent File', 1, 1);

# --------------------------------------------------------

#
# Table structure for table `xtorrent_mod`
#

CREATE TABLE xtorrent_mod (
  requestid int(11) NOT NULL auto_increment,
  lid int(11) unsigned NOT NULL default '0',
  cid int(5) unsigned NOT NULL default '0',
  title varchar(255) NOT NULL default '',
  url varchar(255) NOT NULL default '',
  homepage varchar(255) NOT NULL default '',
  version varchar(20) NOT NULL default '',
  size int(8) NOT NULL default '0',
  platform varchar(50) NOT NULL default '',
  screenshot varchar(255) NOT NULL default '',
  submitter int(11) NOT NULL default '0',
  publisher text NOT NULL,
  status tinyint(2) NOT NULL default '0',
  date int(10) NOT NULL default '0',
  hits int(11) unsigned NOT NULL default '0',
  rating double(6,4) NOT NULL default '0.0000',
  votes int(11) unsigned NOT NULL default '0',
  comments int(11) unsigned NOT NULL default '0',
  license varchar(255) NOT NULL default '',
  mirror varchar(255) NOT NULL default '',
  price varchar(10) NOT NULL default 'Free',
  paypalemail varchar(255) NOT NULL default '',
  features text NOT NULL,
  requirements text NOT NULL,
  homepagetitle varchar(255) NOT NULL default '',
  forumid int(11) NOT NULL default '0',
  limitations varchar(255) NOT NULL default '30 day trial',
  dhistory text NOT NULL,
  published int(10) NOT NULL default '0',
  expired int(10) NOT NULL default '0',
  updated int(11) NOT NULL default '0',
  offline tinyint(1) NOT NULL default '0',
  description text NOT NULL,
  modifysubmitter int(11) NOT NULL default '0',
  requestdate int(11) NOT NULL default '0',
  PRIMARY KEY  (requestid)
) ENGINE=MyISAM;

#
# Dumping data for table `xtorrent_mod`
#

# --------------------------------------------------------

#
# Table structure for table `xtorrent_reviews`
#

CREATE TABLE xtorrent_reviews (
  review_id int(11) unsigned NOT NULL auto_increment,
  lid int(11) NOT NULL default '0',
  title varchar(255) default NULL,
  review text,
  submit int(11) NOT NULL default '0',
  date int(11) NOT NULL default '0',
  uid int(10) NOT NULL default '0',
  rated int(11) NOT NULL default '0',
  PRIMARY KEY  (review_id),
  KEY categoryid (lid)
) ENGINE=MyISAM;

#
# Dumping data for table `xtorrent_reviews`
#

# --------------------------------------------------------

#
# Table structure for table `xtorrent_votedata`
#

CREATE TABLE xtorrent_votedata (
  ratingid int(11) unsigned NOT NULL auto_increment,
  lid int(11) unsigned NOT NULL default '0',
  ratinguser int(11) NOT NULL default '0',
  rating tinyint(3) unsigned NOT NULL default '0',
  ratinghostname varchar(60) NOT NULL default '',
  ratingtimestamp int(10) NOT NULL default '0',
  PRIMARY KEY  (ratingid),
  KEY ratinguser (ratinguser),
  KEY ratinghostname (ratinghostname),
  KEY lid (lid)
) ENGINE=MyISAM;

#
# Dumping data for table `xtorrent_votedata`
#