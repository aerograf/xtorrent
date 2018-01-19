<{if $down.imageheader != ""}>
    <br/>
    <div align="center"><{$down.imageheader}></div>
    <br/>
<{/if}>

<div style="line-height: 12px;" class="even">
    <small><b><{$down.path}></b></small>
</div><br/>

<table width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td colspan="2">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td valign="top" style="line-height: 12px;">
                        <h3 style="color: #2F5376;"><{$down.title}></h3>
                        <div style="margin-left: 6px;">
                            <a href="<{$xoops_url}>/modules/xtorrent/visit.php?cid=<{$down.cid}>&amp;lid=<{$down.id}>">
                                <img src="<{$xoops_url}>/modules/xtorrent/images/icon/downloads.gif" align="absmiddle" alt=""/> <{$smarty.const._MD_XTORRENT_DOWNLOADNOW}>
                            </a> <{$down.adminlink}>
                            <{if $down.forumid > 0}>
                                <a href="<{$xoops_url}>/modules/newbb/viewforum.php?forum=<{$down.forumid}>">
                                    <img src="<{$xoops_url}>/modules/xtorrent/images/icon/forum.gif" align="absmiddle" alt="<{$smarty.const._MD_XTORRENT_INFORUM}>"/> <{$smarty.const._MD_XTORRENT_INFORUM}>
                                </a>
                            <{/if}>
                        </div>
                        <br/><br/>
                        <div align="justify"><b><{$smarty.const._MD_XTORRENT_DESCRIPTIONC}></b><br/>
                            <{$down.description}></div>
                        <br/>
                        <{if $down.features != ''}>
                            <div><b><{$smarty.const._MD_XTORRENT_FEATURES}></b></div>
                            <div>
                                <ul><{foreach item=features from=$down.features}>
                                        <li><{$features}></li>
                                    <{/foreach}></ul>
                            </div>
                            <br/>
                        <{/if}> <{if $down.requirements != ''}>
                            <div><b><{$smarty.const._MD_XTORRENT_REQUIREMENTS}></b></div>
                            <div>
                                <ul><{foreach item=requirements from=$down.requirements}>
                                        <li><{$requirements}></li>
                                    <{/foreach}></ul>
                            </div>
                            <br/>
                        <{/if}>
                        <{if $down.requirements != ''}>
                            <div align="justify"><b><{$smarty.const._MD_XTORRENT_HISTORY}></b><br/>
                                <{$down.history}></div>
                            <br/>
                        <{/if}></td>
                    <td width="35%" style="line-height: 12px;"><font color="#333333">
                            <div style="color:#000000; margin-left: 10px; margin-right: 10px; padding: 4px; background-color:#e6e6e6; border-color:#999999;" class="outer">
                                <small>
                                    <div><b><{$smarty.const._MD_XTORRENT_SUBMITTER}>:</b>&nbsp;<{$down.submitter}></div>
                                    <div><b><{$smarty.const._MD_XTORRENT_PUBLISHER}>:</b>&nbsp;<{$down.publisher}></div>
                                    <div><b><{$lang_subdate}>:</b>&nbsp;<{$down.updated}></div>
                                    <br/>
                                    <div><b><{$smarty.const._MD_XTORRENT_VERSION}>:</b>&nbsp;<{$down.version}></div>
                                    <div><b><{$smarty.const._MD_XTORRENT_DOWNLOADHITS}>:</b>&nbsp;<{$down.hits}></div>
                                    <div><b><{$smarty.const._MD_XTORRENT_FILESIZE}>:</b>&nbsp;<{$down.size}></div>
                                    <div><b><{$smarty.const._MD_XTORRENT_HOMEPAGE}>:</b>&nbsp;<span style="color:#003333"><{$down.homepage}></span></div>
                                    <div><b><{$smarty.const._MD_XTORRENT_MIRROR}>:</b>&nbsp;<span style="color:#003333"><{$down.mirror}></span></div>
                                    <br/>
                                    <div><b><{$smarty.const._MD_XTORRENT_TTLSEEDS}>:</b>&nbsp;<{$down.total_seeds}></div>
                                    <div><b><{$smarty.const._MD_XTORRENT_TTLLEECHES}>:</b>&nbsp;<{$down.total_leeches}></div>
                                    <div><b><{$smarty.const._MD_XTORRENT_TOTALSIZE}>:</b>&nbsp;<{$down.torrent.totalsize}> Gb</div>
                                    <div><b><{$smarty.const._MD_XTORRENT_TNAME}>:</b>&nbsp;<{$down.torrent.tname}></div>
                                    <div><b><{$smarty.const._MD_XTORRENT_POLLED}>:</b>&nbsp;<{$down.torrent_last_polled}></div>
                                    <div><b><{$smarty.const._MD_XTORRENT_TRACKERPOLLED}>:</b>&nbsp;<{$down.tracker_last_polled}></div>

                            </div>
                            </div>
                            <br/>
                            <div style="margin-left: 10px; margin-right: 10px; padding: 4px;" class="outer">
                                <small><b><{$smarty.const._MD_XTORRENT_RATINGC}></b>&nbsp;<img src="<{$xoops_url}>/modules/xtorrent/images/icon/<{$down.rateimg}>" alt="" align="absmiddle"/>&nbsp;(<{$down.votes}>)</small>
                            </div>
                            <br/>
                            <div style="margin-left: 10px; margin-right: 10px; padding: 4px;" class="outer">
                                <small><b><{$smarty.const._MD_XTORRENT_REVIEWS}></b>&nbsp;<img src="<{$xoops_url}>/modules/xtorrent/images/icon/<{$down.review_rateimg}>" alt="" align="absmiddle"/>&nbsp;(<{$down.reviews_num}>)</small>
                            </div>
                            <br/>
                            <div style="margin-left: 10px; margin-right: 10px; padding: 4px;" class="outer"><b><{$smarty.const._MD_XTORRENT_DOWNTIMES}></b>
                                <div style="margin-left: 4px;">
                                    <{$down.downtime}>
                                </div>
                            </div>
                            <{if $show_screenshot == true}>
                                <{if $down.screenshot_full}>
                                    <div style="margin-left: 10px; margin-center: 10px; padding: 4px;">
                                        <b><{$smarty.const._MD_XTORRENT_SCREENSHOT}></b>
                                        <div align="center"><a href="<{$xoops_url}>/<{$shots_dir}>/<{$down.screenshot_full}>" target="_blank">
                                                <img src="<{$down.screenshot_thumb}>" alt="<{$smarty.const._MD_XTORRENT_SCREENSHOTCLICK}>" width="<{$shotwidth}>" height="<{$shotheight}>" alt="" vspace="3" hspace="7" align="middle" style="border: 1px solid black"/></a></div>
                                        <div align="center"><a href="<{$down.screenshot_thumb}>" target="_blank"><{$lang_screenshot_click}></a>
                                        </div>
                                    </div>
                                    <br/>
                                <{/if}><{/if}></font>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="line-height: 12px;">&nbsp;</td>
                </tr>
                <{if $down.tagbar}>
                    <tr colspan="2">
                        <td>
                            <strong><{$down.tagbar.title}>:</strong> <{foreach item=tag from=$down.tagbar.tags}><{$down.tagbar.delimiter}> <{$tag}>&nbsp;&nbsp;<{/foreach}>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="line-height: 12px;">&nbsp;</td>
                    </tr>
                <{/if}>
                <tr>
                    <td colspan="2">
                        <div align="center" style="margin-bottom: 3px; ">
                            <small>
                                <b><{$smarty.const._MD_XTORRENT_PRICE}>:</b>&nbsp;<{$down.price}> |
                                <b><{$smarty.const._MD_XTORRENT_SUPPORTEDPLAT}>:</b>&nbsp;<{$down.platform}> |
                                <b><{$smarty.const._MD_XTORRENT_DOWNLICENSE}>:</b>&nbsp;<{$down.license}> |
                                <b><{$smarty.const._MD_XTORRENT_LIMITS}>:</b>&nbsp;<{$down.limitations}>
                            </small>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="even" style="line-height: 12px;">
                        <small>
                            <div align="center"><a href="<{$xoops_url}>/modules/xtorrent/review.php?cid=<{$down.cid}>&amp;lid=<{$down.id}>"><{$smarty.const._MD_XTORRENT_REVIEWTHISFILE}></a> | <a
                                        href="<{$xoops_url}>/modules/xtorrent/ratefile.php?cid=<{$down.cid}>&amp;lid=<{$down.id}>"><{$smarty.const._MD_XTORRENT_RATETHISFILE}></a> | <{if $down.useradminlink == true}>
                                    <a href="<{$xoops_url}>/modules/xtorrent/modfile.php?lid=<{$down.id}>"><{$smarty.const._MD_XTORRENT_MODIFY}></a>
                                    | <{/if}> <a href="<{$xoops_url}>/modules/xtorrent/brokenfile.php?lid=<{$down.id}>"><{$smarty.const._MD_XTORRENT_REPORTBROKEN}></a> | <a target="_top"
                                                                                                                                                                             href="mailto:?subject=<{$down.mail_subject}>&amp;body=<{$down.mail_body}>"><{$smarty.const._MD_XTORRENT_TELLAFRIEND}></a> | <a
                                        href="<{$xoops_url}>/modules/xtorrent/singlefile.php?cid=<{$down.cid}>&amp;lid=<{$down.id}>"><{$smarty.const._COMMENTS}> (<{$down.comments}>)</a></div>
                        </small>
                    </td>
                </tr>
            </table>
    </tr>
</table>
<table width="100%" border="1" cellspacing="2">
    <tr class="head">
        <td width="50%">Trackers</td>
        <td width="50%">Files</td>
    </tr>
    <tr>
        <td align="left" valign="top">
            <table width="100%" border="0" cellspacing="0">
                <tr class="head">
                    <td width="54%">Tracker</td>
                    <td width="22%" align="right">Seeds</td>
                    <td width="24%" align="right">Leeches</td>
                </tr>
                <{foreach item=tracker from=$down.tracker}>
                    <tr class="<{cycle values="odd,even"}>">
                        <td><{$tracker.tracker}></td>
                        <td align="right"><{$tracker.seeds}></td>
                        <td align="right"><{$tracker.leeches}></td>
                    </tr>
                <{/foreach}>
            </table>
        </td>
        <td align="left" valign="top">
            <table width="100%" border="0" cellspacing="0">
                <{foreach item=file from=$down.files}>
                    <tr class="<{cycle values="odd,even"}>">
                        <td><{$file.file}></td>
                    </tr>
                <{/foreach}>
            </table>
        </td>
    </tr>
</table>
<br/>
<div><b><{$lang_user_reviews}></b></div>
<div style="padding: 3px; margin:3px;">
    <img src="<{$xoops_url}>/modules/xtorrent/images/icon/reviews.gif" width="32" height="32" align="absmiddle"/>
    <a href="<{$xoops_url}>/modules/xtorrent/review.php?<{$lang_UserReviews}></a>
</div>
<br />

<div><b><{$smarty.const._MD_XTORRENT_OTHERBYUID}></b> <{$down.submitter}></div> 
<table width=" 100%" border="0" cellspacing="1" cellpadding="2">
    <tr>
        <{foreach item=down_user from=$down_uid}>
        <td>
            <div style="margin-left: 10px;">
                <a href="<{$xoops_url}>/modules/xtorrent/visit.php?cid=<{$down_user.cid}>&amp;lid=<{$down_user.lid}>"><{$down_user.title}></a>
                (<{$down_user.published}>)
            </div>
        </td>
        <{if 20 is div by 2}>
    </tr>
    <tr>
        <{/if}>
        <{/foreach}>
    </tr>
    </table><br/>

    <div align="center"><{$lang_copyright}></div>
    <br/>
    <div style="text-align: center; padding: 3px; margin:3px;">
        <{$commentsnav}>
        <{$lang_notice}>
    </div>

    <div style="margin:3px; padding: 3px;">
        <!-- start comments loop -->
        <{if $comment_mode == "flat"}>
            <{include file="db:system_comments_flat.tpl"}>
        <{elseif $comment_mode == "thread"}>
            <{include file="db:system_comments_thread.tpl"}>
        <{elseif $comment_mode == "nest"}>
            <{include file="db:system_comments_nest.tpl"}>
        <{/if}>
        <!-- end comments loop -->
    </div>
    <{include file="db:system_notification_select.tpl"}>
