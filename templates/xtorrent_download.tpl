<{if $show_categort_title == true}>
    <div style="margin-bottom: 4px;"><b><{$smarty.const._MD_XTORRENT_CATEGORYC}></b><{$down.category}></div>
<{/if}>

<table width="100%" cellspacing="1" cellpadding="2">
    <tr>
        <td width="82%" align="left">
            <a href="<{$xoops_url}>/modules/xtorrent/visit.php?cid=<{$down.cid}>&amp;lid=<{$down.id}>"><span class="itemTitle"><{$down.title}> </span></a> <{$down.icons}>
        </td>
        <td width="18%" align="right" nowrap="nowrap">
            <a href="<{$xoops_url}>/modules/xtorrent/singlefile.php?cid=<{$down.cid}>&amp;lid=<{$down.id}>">
                <span class="itemTitle"><{$smarty.const._MD_XTORRENT_VIEWDETAILS}></span></a>
        </td>
    </tr>
    <tr>
        <td height="1" colspan="2" bgcolor="#000000"></td>
    </tr>
    <tr>
        <td colspan="2">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td colspan="2">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr class="even" style="line-height: 10px;">
                                <td width="50%">
                                    <small><strong><{$smarty.const._MD_XTORRENT_SUBMITTER}>:</strong> <{$down.submitter}>
                                        <{$down.adminlink}></small>
                                </td>
                                <td>
                                    <div align="right" style="margin-right: 2px;">
                                        <small><b><{$lang_subdate}>:</b>&nbsp;&nbsp;
                                            <{$down.updated}></small>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="line-height: 12px;">&nbsp;</td>
                </tr>
                <tr>
                    <td width="65%" height="133" valign="top">
                        <{if $show_screenshot == true}>
                            <{if $down.screenshot_full != ''}>
                                <div>
                                    <a href="<{$xoops_url}>/<{$shots_dir}>/<{$down.screenshot_full}>" target="_blank">
                                        <img src="<{$down.screenshot_thumb}>" width="<{$shotwidth}>" height="<{$shotheight}>" alt="" vspace="3" hspace="7" align="right" style='border: 1px solid black'/>
                                    </a>
                                </div>
                            <{/if}>
                        <{/if}>
                        <div style="margin-left: 6px;" align="justify"><a href="<{$xoops_url}>/modules/xtorrent/visit.php?cid=<{$down.cid}>&amp;lid=<{$down.id}>">
                                <img src="<{$xoops_url}>/modules/xtorrent/images/icon/downloads.gif" alt="" align="absmiddle"/>
                                <{$smarty.const._MD_XTORRENT_DOWNLOADNOW}></a></div>
                        <br/>
                        <div style="margin-left: 6px;" align="justify"><{$down.description}></div>
                    </td>
                    <td width="35%">
                        <div style="margin-left: 10px; color:#333333; margin-right: 10px; padding: 4px; background-color:#E6E6E6; border-color:#999999;" class="outer">
                            <small>
                                <div><b><{$smarty.const._MD_XTORRENT_VERSION}>:</b>&nbsp;<{$down.version}></div>
                                <div>
                                    <div><b><{$smarty.const._MD_XTORRENT_DOWNLOADHITS}></b>&nbsp;<{$down.hits}></div>
                                    <div><b><{$smarty.const._MD_XTORRENT_FILESIZE}>:</b>&nbsp;<{$down.size}></div>
                                    <div><b><{$smarty.const._MD_XTORRENT_HOMEPAGE}>:</b>&nbsp;<{$down.homepage|wordwrap:50:"\n":true}></div>
                                    <div><b><{$smarty.const._MD_XTORRENT_MIRROR}>:</b>&nbsp;<{$down.mirror|wordwrap:50:"\n":true}></div>
                                    <div><b><{$smarty.const._MD_XTORRENT_TTLSEEDS}>:</b>&nbsp;<{$down.total_seeds}></div>
                                    <div><b><{$smarty.const._MD_XTORRENT_TTLLEECHES}>:</b>&nbsp;<{$down.total_leeches}></div>
                                    <div><b><{$smarty.const._MD_XTORRENT_TOTALSIZE}>:</b>&nbsp;<{$down.torrent.totalsize}> Gb</div>
                                    <div><b><{$smarty.const._MD_XTORRENT_TNAME}>:</b>&nbsp;<{$down.torrent.tname}></div>
                                    <div><b><{$smarty.const._MD_XTORRENT_POLLED}>:</b>&nbsp;<{$down.torrent_last_polled}></div>
                                    <div><b><{$smarty.const._MD_XTORRENT_TRACKERPOLLED}>:</b>&nbsp;<{$down.tracker_last_polled}></div>
                                </div>
                            </small>
                        </div>
                        <br/>
                        <div style="margin-left: 10px; margin-right: 10px; padding: 4px;" class="outer">
                            <small><b><{$smarty.const._MD_XTORRENT_RATINGC}></b>&nbsp;<img src="<{$xoops_url}>/modules/xtorrent/images/icon/<{$down.rateimg}>" alt="" align="absmiddle"/>&nbsp;&nbsp;(<{$down.votes}>)</small>
                        </div>
                        <br/>
                        <div style="margin-left: 10px; margin-right: 10px; padding: 4px;" class="outer">
                            <small><b><{$smarty.const._MD_XTORRENT_REVIEWS}></b>&nbsp;<img src="<{$xoops_url}>/modules/xtorrent/images/icon/<{$down.review_rateimg}>" alt="" align="absmiddle"/>&nbsp;&nbsp;(<{$down.reviews_num}>)</small>
                        </div>

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
                                <b><{$smarty.const._MD_XTORRENT_LIMITS}>:</b>&nbsp;<{$down.limitations}></small>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="even" style="line-height: 12px;">

                        <div align="center">
                            <small>
                                <a href="<{$xoops_url}>/modules/xtorrent/review.php?cid=<{$down.cid}>&amp;lid=<{$down.id}>">
                                    <{$smarty.const._MD_XTORRENT_REVIEWTHISFILE}></a> |
                                <a href="<{$xoops_url}>/modules/xtorrent/ratefile.php?cid=<{$down.cid}>&amp;lid=<{$down.id}>">
                                    <{$smarty.const._MD_XTORRENT_RATETHISFILE}></a> |
                                <{if $down.useradminlink == true}>
                                    <a href="<{$xoops_url}>/modules/xtorrent/submit.php?lid=<{$down.id}>">
                                        <{$smarty.const._MD_XTORRENT_MODIFY}></a>
                                    |
                                <{/if}>
                                <a href="<{$xoops_url}>/modules/xtorrent/brokenfile.php?lid=<{$down.id}>">
                                    <{$smarty.const._MD_XTORRENT_REPORTBROKEN}></a> |
                                <a target="_top" href="mailto:?subject=<{$down.mail_subject}>&amp;body=<{$down.mail_body}>">
                                    <{$smarty.const._MD_XTORRENT_TELLAFRIEND}></a> |
                                <a href="<{$xoops_url}>/modules/xtorrent/singlefile.php?cid=<{$down.cid}>&amp;lid=<{$down.id}>">
                                    <{$smarty.const._COMMENTS}> (<{$down.comments}>)</a>
                            </small>
                        </div>

                    </td>
                </tr>
            </table>
    </tr>
</table>
<br/>

