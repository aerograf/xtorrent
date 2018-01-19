<{if $catarray.imageheader != ""}>
    <br/>
    <div align="center"><{$catarray.imageheader}></div>
    <br/>
<{/if}>

<{if $brokenreport == true}>
    <div align="center">
        <h4><{$smarty.const._MD_XTORRENT_RESOURCEREPORTED}></h4>
        <div><{$smarty.const._MD_XTORRENT_RESOURCEREPORTED}></div>
        <br/>
        <div><b><{$smarty.const._MD_XTORRENT_FILETITLE}></b><{$broken.title}></div>
        <div><b><{$smarty.const._MD_XTORRENT_RESOURCEID}></b><{$broken.id}></div>
        <div><b><{$smarty.const._MD_XTORRENT_REPORTER}></b> <{$broken.reporter}></div>
        <div><b><{$smarty.const._MD_XTORRENT_DATEREPORTED}></b> <{$broken.date}></div>
        <br/>
        <div><b><{$smarty.const._MD_XTORRENT_WEBMASTERACKNOW}></b> <{$broken.acknowledged}></div>
        <div><b><{$smarty.const._MD_XTORRENT_WEBMASTERCONFIRM}></b> <{$broken.confirmed}></div>
    </div>
<{else}>
    <div align="center">
        <h4><{$smarty.const._MD_XTORRENT_BROKENREPORT}></h4>
        <div><{$smarty.const._MD_XTORRENT_THANKSFORHELP}></div>
        <div><{$smarty.const._MD_XTORRENT_FORSECURITY}></div>
        <br/>

        <div><{$smarty.const._MD_XTORRENT_BEFORESUBMIT}></div>
        <br/>
        <div><b><{$smarty.const._MD_XTORRENT_HOMEPAGEC}></b><{$down.homepage}></div>
        <br/>
        <div><b><{$smarty.const._MD_XTORRENT_FILETITLE}></b><{$down.title}></div>
        <div><b><{$smarty.const._MD_XTORRENT_PUBLISHER}><{$smarty.const._MD_XTORRENT_PUBLISHER}>:</b> <{$down.publisher}></div>
        <div><b><{$lang_subdate}>:</b> <{$down.updated}></div>
        <form action="brokenfile.php" method="POST">
            <input type="hidden" name="lid" value="<{$file_id}>"/><input type="submit" name="submit" value="<{$smarty.const._MD_XTORRENT_SUBMITBROKEN}>" alt="<{$smarty.const._MD_XTORRENT_SUBMITBROKEN}>"/>
            &nbsp;<input type="button" value="<{$smarty.const._MD_XTORRENT_CANCEL}>" alt="<{$smarty.const._MD_XTORRENT_CANCEL}>" onclick="javascript:history.go(-2)"/>
        </form>
    </div>
<{/if}>
