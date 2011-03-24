<?php
/**
 * Core Action
 *
 * LICENSE
 *
 * This file is intellectual property of Core Action, LLC and may not
 * be used without permission.
 *
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */

namespace Core\Engine\View\StreamWrapper;

use Core\Engine,
    Core\Engine\View;

/**
 * FilterWrapper
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class FilterWrapper
{
    /**
     * View object
     *
     * @var \Core\Engine\View\ViewAbstract
     */
    private static $view;

    /**
     * Current stream position.
     *
     * @var integer
     */
    protected $pos = 0;

    /**
     * Data for streaming.
     *
     * @var string
     */
    protected $data;

    /**
     * Stream stats.
     *
     * @var array
     */
    protected $stat = array();

    /**
     * File handle
     *
     * @var resource
     */
    protected $fileHandle;

    /**
     * File path
     *
     * @var string
     */
    protected $path;

    /**
     * Open
     *
     * @param string $path
     * @param string $mode
     * @param int $options
     * @param string $openedPath
     * @return boolean
     */
    public function stream_open($path, $mode, $options, &$openedPath)
    {
        // Get host part from path (scheme://host)
        preg_match('#(?:[\w\d]+://)?(.+)#', $path, $matches);
        $this->path = $matches[1];

        // Search include path for relative urls?
        $useIncludePath = $this->checkFlag($options, STREAM_USE_PATH);

        // Trigger errors?
        $triggerErrors  = $this->checkFlag($options, STREAM_REPORT_ERRORS);

        // Get contents
        $this->fileHandle = $triggerErrors ? fopen($this->path, $mode, $useIncludePath)
                                           : @fopen($this->path, $mode, $useIncludePath);

        $fileSize = @filesize($this->path);

        // If reading the file failed, update our local stat store
        // to reflect the real stat of the file, then return on failure
        if ($this->fileHandle === false) {
            return false;
        } else if ($fileSize <= 0) {
            $this->data = '';
        } else {
            $this->data = fread($this->fileHandle, $fileSize);
        }

        // Path opened
        if ($useIncludePath) {
            $openedPath = $this->path;
        }

        // Process data
        $this->data = $this->filter($this->data);

        // file_get_contents() won't update PHP's stat cache, so performing
        // another stat() on it will hit the filesystem again.  Since the file
        // has been successfully read, avoid this and just fake the stat
        // so include() is happy.
        $this->stat = array(
            'mode' => 0100777,
            'size' => strlen($this->data)
        );

        return true;
    }

    /**
     * Close file handle
     *
     */
    public function stream_close()
    {
        fclose($this->fileHandle);
    }

    /**
     * Read
     *
     * @param integer $count
     * @return string
     */
    public function stream_read($count)
    {
        $return = substr($this->data, $this->pos, $count);
        $this->pos += strlen($return);

        return $return;
    }

    /**
     * Write
     *
     * @param string $data
     * @return integer
     */
    public function stream_write($data)
    {
        return fwrite($this->fileHandle, $data);
    }

    /**
     * End of stream indicator
     *
     * @return boolean
     */
    public function stream_eof()
    {
        $isEof = ($this->pos >= strlen($this->data));
        return $isEof;
    }

    /**
     * Current position
     *
     * @return integer
     */
    public function stream_tell()
    {
        return $this->pos;
    }

    /**
     * Seek in stream
     *
     * @return boolean
     */
    public function stream_seek($offset, $whence)
    {
        switch ($whence) {
            case SEEK_SET:
                if ($offset < strlen($this->data) && $offset >= 0) {
                    $this->pos = $offset;
                    return true;
                } else {
                    return false;
                }
                break;

            case SEEK_CUR:
                if ($offset >= 0) {
                    $this->pos += $offset;
                    return true;
                } else {
                    return false;
                }
                break;

            case SEEK_END:
                if (strlen($this->data) + $offset >= 0) {
                    $this->pos = strlen($this->data) + $offset;
                    return true;
                } else {
                    return false;
                }
                break;

            default:
                return false;
        }
    }

    /**
     * Flush
     *
     * @return boolean
     */
    public function stream_flush()
    {
        return fflush($this->fileHandle);
    }

    /**
     * Stream statistics
     *
     * @return array
     */
    public function stream_stat()
    {
        return $this->stat;
    }

    /**
     * Stat url
     *
     * @param string $path
     * @param string $flags
     * @return array
     */
    public function url_stat($path, $flags = null)
    {
        // Get host part
        preg_match('#(?:[\w\d]+://)?(.+)#', $path, $matches);
        $host = $matches[1];

        // Trigger errors?
        $noErrors = $this->checkFlag($flags, STREAM_URL_STAT_QUIET);

        // Stat symlinks
        if ($this->checkFlag($flags, STREAM_URL_STAT_LINK)) {
            return $noErrors ? @lstat($host) : lstat($host);
        }

        return $noErrors ? @stat($host) : stat($host);
    }

    /**
     * Set View
     *
     * @param View\ViewAbstract $view
     */
    public static function setView(View\ViewAbstract $view)
    {
        self::$view = $view;
    }

    /**
     * Get view
     *
     * @return View\ViewAbstract
     */
    public static function getView()
    {
        return self::$view;
    }


    /**
     * Applies the filter callback to a buffer.
     *
     * @param string $buffer The buffer contents.
     * @return string The filtered buffer.
     */
    protected function filter($buffer)
    {
        $view          = self::getView();
        $streamFilters = $view->getStreamFilters();

        // Loop through each filter class
        foreach ($streamFilters as $name) {
            // Load and apply the filter class
            $filter = $view->getFilter($name);
            $buffer = call_user_func(array($filter, 'filter'), $buffer);
        }

        // Done!
        return $buffer;
    }

    /**
     * Validate bitwise flags
     *
     * @param integer $values
     * @param integer $flag
     * @return boolean
     */
    protected function checkFlag($values, $flag)
    {
         $flag   = (int) $flag;
         $values = (int) $values;

         return (($values & $flag) == $flag);
    }
}