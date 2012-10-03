<?php
/**
 * Helper for tests involving the file system
 * @package FSTestHelper
 */

namespace FSTestHelper;

/**
 * Helper for tests involving the file system
 */
class FSTestHelper
{
    private $path;

    public function __construct()
    {
        $i = 0;
        $this->path = realpath(sys_get_temp_dir()) . '/test_' . $i;
        while (file_exists($this->path))
        {
            $i++;
            $this->path = realpath(sys_get_temp_dir()) . '/test_' . $i;
        }
        mkdir($this->path);
    }

    public function getPath()
    {
        return $this->path;
    }
}

/**
 * FSTestHelper Exception class
 */
class Exception extends \Exception
{

}