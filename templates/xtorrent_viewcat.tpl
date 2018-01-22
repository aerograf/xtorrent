<{if $catarray.imageheader != ""}>
	<div style="clear:both;text-align:center;"><{$catarray.imageheader}></div>
<{/if}>

<div><{$description}></div><br />
<div style="clear:both">&nbsp;</div>
<div style="float:left"><a href="<{$xoops_url}>/modules/xtorrent/rss/<{if $htaccess == 0}>feed.php?source=<{/if}><{$category_id}>"><img src="<{$xoops_url}>/modules/xtorrent/assets/images/rss.gif"></a></div>
<div style="clear:both">&nbsp;</div>
<{if $subcategories}>
<fieldset><legend style="font-weight: bold; color: #639ACE;"><{$smarty.const._MD_XTORRENT_SUBCATLISTING}></legend>
<div style="padding: 2px;">
<div align= "left" style="margin-left: 5px; padding: 0px;">
 <table width="80%">
  <tr>
   <{foreach item=subcat from=$subcategories}>
    <td><a href="viewcat.php?cid=<{$subcat.id}>"><{$subcat.title}></a>&nbsp;(<{$subcat.totallinks}>)<br />
	 <{if $subcat.infercategories}>
	  &nbsp;&nbsp;<{$subcat.infercategories}>
      <{/if}>
	 </td>
	 <{if $subcat.count is div by 2}>
      </tr><tr>
     <{/if}>
   <{/foreach}>
   </tr>
 </table>    
</div></fieldset>
<br />
<{/if}>

<div align="center" class="itemPermaLink"><{$catarray.letters}></div><br />
<div align="center"><{$catarray.toolbar}></div><br />
<div><b><{$category_path}></b></div><br />

<{if $show_links == true}> 
<div align="center"><small>
<b><{$smarty.const._MD_XTORRENT_SORTBY}></b>&nbsp;<{$smarty.const._MD_XTORRENT_TITLE}> (
<a href="viewcat.php?cid=<{$category_id}>&amp;orderby=titleA">
<img src="assets/images/up.gif" align="middle" alt="" /></a>
<a href="viewcat.php?cid=<{$category_id}>&amp;orderby=titleD">
<img src="assets/images/down.gif" align="middle" alt="" /></a>
)
&nbsp;
<{$smarty.const._MD_XTORRENT_DATE}> (
<a href="viewcat.php?cid=<{$category_id}>&amp;orderby=dateA">
<img src="assets/images/up.gif" align="middle" alt="" /></a>
<a href="viewcat.php?cid=<{$category_id}>&amp;orderby=dateD">
<img src="assets/images/down.gif" align="middle" alt="" /></a>
)
&nbsp;
<{$smarty.const._MD_XTORRENT_RATING}> (
<a href="viewcat.php?cid=<{$category_id}>&amp;orderby=ratingA">
<img src="assets/images/up.gif" align="middle" alt="" /></a>
<a href="viewcat.php?cid=<{$category_id}>&amp;orderby=ratingD">
<img src="assets/images/down.gif" align="middle" alt="" /></a>
)
&nbsp;
<{$smarty.const._MD_XTORRENT_POPULARITY}> (
<a href="viewcat.php?cid=<{$category_id}>&amp;orderby=hitsA">
<img src="assets/images/up.gif" align="middle" alt="" />
</a><a href="viewcat.php?cid=<{$category_id}>&amp;orderby=hitsD">
<img src="assets/images/down.gif" align="middle" alt="" /></a>
)
<br />
<b><{$lang_cursortedby}></b>
</small></div>
<br />
<{/if}>

<{if $page_nav == true}>
<div><{$smarty.const._MD_XTORRENT_PAGES}>: <{$pagenav}></div><br />
<{/if}>

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

<{if $page_nav == true}>
<div align="right"><{$smarty.const._MD_XTORRENT_PAGES}>: <{$pagenav}></div>
<{/if}>

<{include file="db:system_notification_select.tpl"}> </div>
<div style="float:right"><a href="<{$xoops_url}>/modules/xtorrent/rss/<{if $htaccess == 0}>feed.php?source=<{$category_id}><{/if}><{$rss_source}>"><img src="<{$xoops_url}>/modules/xtorrent/assets/images/rss.gif"></a></div>