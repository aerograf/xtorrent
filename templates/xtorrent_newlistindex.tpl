<{if $imageheader != ""}>
    <br/>
    <div align="center"><{$imageheader}></div>
    <br/>
<{/if}>
<div align="center">
    <h4><{$smarty.const._MD_XTORRENT_NEWDOWNLOADS}></h4>
</div>
<center><b><{$smarty.const._MD_XTORRENT_TOTALNEWDOWNLOADS}>:</b>
    <{$smarty.const._MD_XTORRENT_LASTWEEK}> - <{$allweekdownloads}> \ <{$smarty.const._MD_XTORRENT_LAST30DAYS}> - <{$allmonthdownloads}><br/>
    <br/><{$smarty.const._MD_XTORRENT_SHOW}>: <a href="<{$xoops_url}>/modules/xtorrent/newlist.php?newdownloadshowdays=7"><{$smarty.const._MD_XTORRENT_1WEEK}></a>
    - <a href="<{$xoops_url}>/modules/xtorrent/newlist.php?newdownloadshowdays=14"><{$smarty.const._MD_XTORRENT_2WEEKS}></a>
    - <a href="<{$xoops_url}>/modules/xtorrent/newlist.php?newdownloadshowdays=30"><{$smarty.const._MD_XTORRENT_30DAYS}></a>
</center>
<p>
<div align="center"><b><{$smarty.const._MD_XTORRENT_DTOTALFORLAST}> <{$newdownloadshowdays}> <{$smarty.const._MD_XTORRENT_DAYS}></b></div></p>

<{if count($dailydownloads) gt 0}>
    <table border="0" cellspacing="1" cellpadding="2" align="center">
        <!-- Start category loop -->
        <{foreach item=dailydownloads from=$dailydownloads}>
            <tr>
                <td valign="top" width="40%" align="center"><strong><big>&middot;</big></strong>
                    <a href="<{$xoops_url}>/modules/xtorrent/viewcat.php?selectdate=<{$dailydownloads.newdownloaddayRaw}>"><{$dailydownloads.newdownloadView}></a>&nbsp;(<{$dailydownloads.totaldownloads}>)
                </td>
            </tr>
        <{/foreach}>
        <!-- End category loop -->

    </table>
    <h4><{$smarty.const._MD_XTORRENT_LATESTLIST}></h4>
    <table width="100%" cellspacing="0" cellpadding="10" border="0">
        <tr>
            <td width="100%">
                <!-- Start link loop -->
                <{section name=i loop=$file}>
                    <{include file="db:xtorrent_download.tpl" down=$file[i]}>
                <{/section}>
                <!-- End link loop -->
            </td>
        </tr>
    </table>
<{/if}> 
