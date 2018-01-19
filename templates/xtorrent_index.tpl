<{if $catarray.imageheader != ""}>
    <div style="clear:both;text-align:center;"><{$catarray.imageheader}></div>
<{/if}>

<{if $catarray.indexheading != ""}>
    <div><h4><{$catarray.indexheading}></h4></div>
<{/if}>

<div style="clear:both;text-align:<{$catarray.indexheaderalign}>;margin-bottom:10px;margin-top:10px;"><{$catarray.indexheader}></div>
<div class="itemPermaLink" style="clear:both;text-align:center;"><{$catarray.letters}></div>
<div style="clear:both;text-align:center;"><{$catarray.toolbar}></div>

<{if count($categories) gt 0}>
    <div class="even" style="margin-top:10px;margin-bottom:10px;"><b><{$smarty.const._MD_XTORRENT_MAINLISTING}></b></div>
    <div style="margin-top:10px;margin-bottom:10px;text-align:center;">
        <div style="width:100%">
            <!-- Start category loop -->
            <{foreach item=category from=$categories}>
            <div style="vertical-align:top;width:100px;display:inline-block;text-align:center;">
                <a href="<{$xoops_url}>/modules/xtorrent/viewcat.php?cid=<{$category.id}>">
                    <img src="<{$category.image}>" alt="<{$category.alttext}>" style="vertical-align:middle;max-width:100px;max-height:100px;"></a>
            </div>
            <div style="vertical-align:middle;width:35%;display:inline-block;text-align:left;">
                <a href="<{$xoops_url}>/modules/xtorrent/viewcat.php?cid=<{$category.id}>"><b><{$category.title}></b></a>&nbsp;(<{$category.totaldownloads}>)
                <div style="margin-bottom: 3px; margin-left: 10px;"><{$category.summary}></div>
                <{if $category.subcategories}>
                    <div style="margin-bottom: 3px; margin-left: 16px;">
                        <small><{$category.subcategories}></small>
                    </div>
                <{/if}>
            </div>
            <{if $category.count is div by 2}>
        </div>
        <{/if}>
        <{/foreach}>
        <!-- End category loop -->
    </div>
    <div class="odd" style="line-height:8px;margin-bottom:10px;">
        <small><{$lang_thereare}></small>
    </div>
    <div style="margin-bottom:1px;float:right;">
        <small>
            <img src="<{$xoops_url}>/modules/xtorrent/assets/images/icons/32/download1.gif" alt=""
                 style="vertical-align:middle;">&nbsp;<{$smarty.const._MD_XTORRENT_LEGENDTEXTNEW}>
            <img src="<{$xoops_url}>/modules/xtorrent/assets/images/icons/32/download2.gif" alt=""
                 style="vertical-align:middle;">&nbsp;<{$smarty.const._MD_XTORRENT_LEGENDTEXTNEWTHREE}>
            <img src="<{$xoops_url}>/modules/xtorrent/assets/images/icons/32/download3.gif" alt=""
                 style="vertical-align:middle;">&nbsp;<{$smarty.const._MD_XTORRENT_LEGENDTEXTTHISWEEK}>
            <img src="<{$xoops_url}>/modules/xtorrent/assets/images/icons/32/download4.gif" alt=""
                 style="vertical-align:middle;">&nbsp;<{$smarty.const._MD_XTORRENT_LEGENDTEXTNEWLAST}>
        </small>
    </div>
<{/if}>
<div style="clear:both;text-align:<{$catarray.indexfooteralign}>;margin-bottom:10px;margin-top:10px;"><{$catarray.indexfooter}></div>
<div style="float:right"><a href="<{$xoops_url}>/modules/xtorrent/rss/<{if $htaccess == 0}>feed.php<{/if}>"><img src="<{$xoops_url}>/modules/xtorrent/assets/images/rss.gif"></a></div>
<{include file="db:system_notification_select.tpl"}>
