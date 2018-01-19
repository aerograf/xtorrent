<?php

require_once "bencode.reader.php";

class Torrent
{
    public $announce;
    public $announceList;
    public $createdBy;
    public $creationDate;
    public $encoding;
    public $name;
    public $length;
    public $files;
    public $pieceLength;
    public $pieces;
    public $comment;
    public $private;
    public $md5sum;
    public $filename;
    public $infoHash;
    public $totalSize;
    public $modifiedBy;
    public $error = false;

    public function __construct($filename)
    {
        // Keep this info for reference later
        $this->filename = $filename;
        
        // The entire contents of a torrent file should form into a dictionary object, which will be used to get all our info.
        $reader = new BEncodeReader($filename);
        $torrentInfo = $reader->readNext();
        
        // In the case of an invalid torrent file the result of the readNext call will be "false".
        if ($torrentInfo === false) {
            $this->error = true;
            trigger_error("The torrent file is invalid", E_USER_WARNING);
        }
        
        // Based on the information we've read in, we can now set up the contents of this class
        $this->announce     = $torrentInfo['announce'];
        $this->announceList = $torrentInfo['announce-list'];
        $this->createdBy    = $torrentInfo['created by'];
        $this->creationDate = $torrentInfo['creation date'];
        $this->comment      = $torrentInfo['comment'];
        $this->modifiedBy   = $torrentInfo['modified-by'];
        $this->pieceLength  = $torrentInfo['info']['piece length'];
        $this->pieces       = $torrentInfo['info']['pieces'];
        $this->private      = ($torrentInfo['info']['private'] == 1);
        $this->name         = $torrentInfo['info']['name'];
        $this->encoding     = $torrentInfo['encoding'];
        $this->infoHash     = $torrentInfo['info']['hash'];
        
        // Files gets a bit tricky. If it isn't defined then this is a single file torrent, which has only the info
        // about one file. Otherwise we have a list of files and path info for each.
        if (!isset($torrentInfo['info']['files'])) {
            $this->length = $torrentInfo['info']['length'];
            $this->md5sum = $torrentInfo['info']['md5sum'];
            $this->totalSize = $this->length;
        } else {
            $this->files = [];
            $this->totalSize = 0;
            foreach ($torrentInfo['info']['files'] as $key=>$fileInfo) {
                $torrentFile = new TorrentFile();
                $torrentFile->md5sum = $fileInfo['md5sum'];
                $torrentFile->length = $fileInfo['length'];
                $torrentFile->name = implode("/", $fileInfo['path']);
                $this->files[$key] = $torrentFile;
                $this->totalSize += $torrentFile->length;
            }
        }
    }
}

// Class representing a file within a torrent
class TorrentFile
{
    public $md5sum;
    public $name;
    public $length;
}
