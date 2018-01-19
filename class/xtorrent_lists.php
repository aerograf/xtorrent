<?php

class XtsLists
{
    public $value;
    public $selected;
    public $path = 'uploads';
    public $size;
    public $emptyselect;
    public $type;
    public $prefix;
    public $suffix;

    /**
     * $value:
     * Selection:
     * Path:
     * Size:
     * emptyselect:
     * $type: Filter which types of files should be returned
     * 		Html
     * 		Images
     * 		files
     * 		dir
     */

    public function __construct($path = 'uploads', $value = null, $selected = '', $size = 1, $emptyselect = 0, $type = 0, $prefix = '', $suffix = '')
    {
        $this->value = $value;
        $this->selection = $selected;
        $this->path = $path;
        $this->size = intval($size);
        $this->emptyselect = $emptyselect ? 0 : 1;
        $this->type = $type;
    }

    public static function getarray($this_array)
    {
        $ret = "<select size='" . $this->size() . "' name='$this->value()'>";
        if ($this->emptyselect) {
            $ret .= "<option value='" . $this->value() . "'>----------------------</option>";
        }
        foreach ($this_array as $content) {
            $opt_selected = '';

            if ($content[0] == $this->selected()) {
                $opt_selected = "selected='selected'";
            }
            $ret .= "<option value='" . $content . "' $opt_selected>" . $content . '</option>';
        }
        $ret .= '</select>';
        return $ret;
    }

    /**
     * Private to be called by other parts of the class
     */
    public static function getDirListAsArray($dirname)
    {
        $dirlist = [];
        if (is_dir($dirname) && $handle = opendir($dirname)) {
            while (false !== ($file = readdir($handle))) {
                if (!preg_match('/^[.]{1,2}$/', $file)) {
                    if ('cvs' != strtolower($file) && is_dir($dirname . $file)) {
                        $dirlist[$file] = $file;
                    }
                }
            }
            closedir($handle);

            reset($dirlist);
        }
        return $dirlist;
    }

    public static function getListTypeAsArray($dirname, $type = '', $prefix = '', $noselection = 1)
    {
        $filelist = [];
        switch (trim($type)) {
            case 'images':
                $types = '[.gif|.jpg|.png]';
                if ($noselection) {
                    $filelist[''] = 'Show No Image';
                }
                break;
            case 'html':
                $types = '[.htm|.html|.xhtml|.php|.php3|.phtml|.txt]';
                if ($noselection) {
                    $filelist[''] = 'No Selection';
                }
                break;
            default:
                $types = '';
                if ($noselection) {
                    $filelist[''] = 'No Selected File';
                }
                break;
        }

        if ('/' == substr($dirname, -1)) {
            $dirname = substr($dirname, 0, -1);
        }

        if (is_dir($dirname) && $handle = opendir($dirname)) {
            while (false !== ($file = readdir($handle))) {
                if (!preg_match('/^[.]{1,2}$/', $file) && preg_match("/$types$/i", $file) && is_file($dirname . '/' . $file)) {
                    if ('blank.png' == strtolower($file)) {
                        continue;
                    }
                    $file = $prefix . $file;
                    $filelist[$file] = $file;
                }
            }
            closedir($handle);
            asort($filelist);
            reset($filelist);
        }
        return $filelist;
    }

    public static function value()
    {
        return $this->value;
    }

    public static function selected()
    {
        return $this->selected;
    }

    public static function paths()
    {
        return $this->path;
    }

    public static function size()
    {
        return $this->size;
    }

    public static function emptyselect()
    {
        return $this->emptyselect;
    }

    public static function type()
    {
        return $this->type;
    }

    public static function prefix()
    {
        return $this->prefix;
    }

    public static function suffix()
    {
        return $this->suffix;
    }
}
