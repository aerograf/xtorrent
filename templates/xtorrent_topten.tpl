<{if $catarray.imageheader != ""}>
    <br/>
    <div align="center"><{$catarray.imageheader}></div>
    <br/>
<{/if}>
<div align="center" class="itemPermaLink"><{$catarray.letters}></div><br/>
<div align="center"><{$catarray.toolbar}></div><br/>
<h5><{$smarty.const._MD_XTORRENT_DISPLAYING}><{$lang_sortby}></h5>
<!-- Start ranking loop -->
<{foreach item=ranking from=$rankings}>
    <div style="margin-bottom: 5px;"><b><{$smarty.const._MD_XTORRENT_CATEGORY}>: <{$ranking.title}></b></div>
    <table cellpadding="0" cellspacing="1" width="100%" class="outer">
        <tr>
            <th width="5%" align="center"><{$smarty.const._MD_XTORRENT_RANK}></th>
            <th width="29%"><{$smarty.const._MD_XTORRENT_TITLE}></th>
            <th width="27%"><{$smarty.const._MD_XTORRENT_CATEGORY}></th>
            <th width="10%" align="center"><{$smarty.const._MD_XTORRENT_HITS}></th>
            <th width="12%" align="center"><{$smarty.const._MD_XTORRENT_RATING}></th>
            <th width="11%" align="center"><{$smarty.const._MD_XTORRENT_VOTE}></th>
        </tr>
        <!-- Start files loop -->
        <{foreach item=file from=$ranking.file}>
            <tr>
                <td align="center" class="head"><b><{$file.rank}></b></td>
                <td class="even"><a href="singlefile.php?cid=<{$file.cid}>&amp;lid=<{$file.id}>"><{$file.title}></a></td>
                <td class="even"><a href="viewcat.php?cid=<{$file.cid}>"><{$file.category}></a></td>
                <td class="even" align="center"><{$file.hits}></td>
                <td class="even" align="center"><{$file.rating}></td>
                <td align="center" class="even"><{$file.votes}></td>
            </tr>
        <{/foreach}>
        <!-- End links loop-->
    </table>
    <br/>
<{/foreach}>
<!-- End ranking loop --> 
