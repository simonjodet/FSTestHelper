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

    public function __destruct()
    {
        $this->delete($this->path);
    }

    public function getPath()
    {
        return $this->path;
    }

    public function itemize($path)
    {
        $items = array();
        foreach (glob($path . '/*') as $item)
        {
            $items[] = $item;
            if (is_dir($item))
            {
                $items = array_merge($items, $this->itemize($item));
            }
        }
        sort($items);
        return $items;
    }

    public function delete($path)
    {
        if (strpos(realpath($path), realpath($this->path)) !== 0)
        {
            $path = $this->path . '/' . $path;
        }
        if (file_exists($path))
        {
            if (is_dir($path))
            {
                foreach (glob($path . '/*') as $item)
                {
                    if (is_dir($item))
                    {
                        $this->delete($item);
                    }
                    else
                    {
                        unlink($item);
                    }
                }
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