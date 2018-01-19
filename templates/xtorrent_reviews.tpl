<{if $catarray.imageheader != ""}>
    <br/>
    <div align="center"><{$catarray.imageheader}></div>
    <br/>
<{/if}>

<div align="center" class="itemPermaLink"><{$catarray.letters}></div>
<div align="center"><{$catarray.toolbar}></div><br/>

<h4><img src="<{$xoops_url}>/modules/xtorrent/images/icon/reviews.gif" width="32" height="32" align="middle"/><{$smarty.const._MD_XTORRENT_TITLE}></h4>

<div><b><{$review.title}></b></div>
<div><b><{$smarty.const._MD_XTORRENT_TITLE}>:</b> <{$down_arr.title}></div>
<div style="margin-left: 38px; padding-bottom: 4px;"><{$down_arr.description}></div><br/>

<div><{$lang_review_found}></div>
<div><a href="<{$xoops_url}>/modules/xtorrent/review.php?cid=<{$down_arr.cid}>&amp;lid=<{$down_arr.lid}>"><{$smarty.const._MD_XTORRENT_REVIEWTITLE}></a></div>
<br/>
<{if $navbar.navbar }>
    <div class="odd"><b><{$smarty.const._MD_XTORRENT_PAGES}></b>: <{$navbar.navbar}></div>
    <br/>
<{/if}>

<!-- Start ranking loop -->
<{foreach item=review from=$down_review}>
    <table width="100%" cellpadding="1" cellspacing="0" border="0">
        <tr>
            <td style="padding: 2px;"><b><{$smarty.const._MD_XTORRENT_REVIEWER}> : <{$review.submitter}></b></td>
            <td align="right" style="padding: 2px;"><b><{$review.date}></b></td>
        </tr>
        <tr>
            <td bgcolor="#000000" colspan="2"><img src="/images/blank.png" alt="" height="1" width="1"/></td>
        </tr>
    </table>
    <table cellpadding="1" cellspacing="0" border="0">
        <tr valign="top">
            <td width="20%">
                <div style="margin-left: 10px; margin-right: 10px; padding: 4px;"><b><{$smarty.const._MD_XTORRENT_RATEDRESOURCE}></b><br/><img src="<{$xoops_url}>/modules/xtorrent/images/icon/<{$review.rated_img}>" alt="" align="middle"/></div>

            </td>
            <td width="10">&nbsp;&nbsp;</td>
            <td style="margin-left: 5px; margin-right: 5px; padding: 4px;"><font color="#0000CC"><b>"<{$review.title}>"</b></font><br/>
                <{$review.review}>
            </td>
        </tr>
    </table>
    <br/>
<{/foreach}>
<br/>
<{if $navbar.navbar }>
    <div style="text-align: right;" class="odd"><b><{$smarty.const._MD_XTORRENT_PAGES}></b>: <{$navbar.navbar}></div>
    <br/>
<{/if}>
<!-- End ranking loop --> 
