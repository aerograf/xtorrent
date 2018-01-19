<?php

require_once __DIR__ . '/admin_header.php';
require_once XOOPS_ROOT_PATH . '/modules/news/class/class.newsstory.php';

$story = new NewsStory();
$story -> setUid($xoopsUser -> uid());
$story -> setPublished(time());
$story -> setExpired(0);
$story -> setType("admin");
$story -> setHostname(getenv("REMOTE_ADDR"));
$story -> setApproved(1);
$topicid = $_POST["newstopicid"];
$story -> setTopicId($topicid);
$story -> setTitle($title);

$_fileid = (isset($lid) && $lid > 0) ? $lid : $newid;
$_link = $_POST["description"]."<br><div><a href=" . XOOPS_URL . "/modules/xtorrent/singlefile.php?cid=" . $cid . "&amp;lid=" . $_fileid . ">" . $title . "</a></div>";

$description = $myts->addslashes(trim($_link));
$story -> setHometext($description);
$story -> setBodytext('');
$nohtml = (empty($nohtml)) ? 0 : 1;
$nosmiley = (empty($nosmiley)) ? 0 : 1;
$story -> setNohtml($nohtml);
$story -> setNosmiley($nosmiley);
$story -> store();
$notification_handler = xoops_gethandler('notification');
$tags = [];
$tags['STORY_NAME'] = $story -> title();

$modhandler = xoops_gethandler('module');
$newsModule = $modhandler -> getByDirname("news");

$tags['STORY_URL'] = XOOPS_URL . '/modules/news/article.php?storyid=' . $story -> storyid();
if (!empty($isnew))
{
    $notification_handler -> triggerEvent('story', $story -> storyid(), 'approve', $tags);
} 
$notification_handler -> triggerEvent('global', 0, 'new_story', $tags);
unset($xoopsModule);
