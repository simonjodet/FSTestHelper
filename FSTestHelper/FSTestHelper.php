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
        $this->generateTemporaryPath($i);
        while (file_exists($this->path))
        {
            $i++;
            $this->generateTemporaryPath($i);
        }
        mkdir($this->path);
    }

    public function getPath()
    {
        return $this->path;
    }

    public function delete($path)
    {
        $path = $this->path . $path;
        if (file_exists($path))
        {
            if (is_dir($path))
            {
                rmdir($path);
            }
            else
            {
                unlink($path);
            }
        }
    }

    private function generateTemporaryPath($i)
    {
        $this->path = realpath(sys_get_temp_dir()) . '/test_' . $i;
    }
}

/**
 * FSTestHelper Exception class
 */
class Exception extends \Exception
{

}