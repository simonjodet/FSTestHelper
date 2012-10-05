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
    /**
     * The test folder's path
     * @var string
     */
    private $path;

    /**
     * Constructor - Creates the unique test folder path
     */
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

    /**
     * Destructor - Deletes the test folder
     */
    public function __destruct()
    {
        $this->delete($this->path);
    }

    /**
     * String serializer
     * @return string The test folder's path
     */
    public function __toString()
    {
        return $this->path;
    }

    /**
     * Test folder's path property getter
     * @return string The test folder's path
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Recursively list a folder INSIDE or OUTSIDE the test folder
     *
     * @param string $path - The folder path you want to list
     *
     * @return array The sorted folders and files list
     */
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

    /**
     * Recursively list a folder INSIDE the test folder
     *
     * @param string $path - The path INSIDE the test folder you want to delete
     */
    public function delete($path)
    {
        if (strpos(realpath($path), realpath($this->path)) !== 0)
        {
            $path = $this->path . '/' . $path;
        }
        if (file_exists($path))
        {
            exec('rm -rf ' . $path);
        }
    }

    /**
     * Recursively copy a folder that is INSIDE or OUTSIDE the test folder INSIDE the test folder
     *
     * @param string $source - The path INSIDE or OUTSIDE the test folder you want to copy
     * @param string $destination - The path INSIDE the test folder you want to copy to
     */
    public function copy($source, $destination)
    {
        $list = $this->itemize($source);
        if (strpos(realpath($destination), realpath($this->path)) !== 0)
        {
            $destination = $this->path . '/' . $destination;
        }
        if (!file_exists($destination))
        {
            mkdir($destination);
        }
        foreach ($list as $item)
        {
            $relative_path = ltrim(str_replace(realpath($source), '', realpath($item)), '/');
            if (is_dir($item))
            {
                mkdir($destination . '/' . $relative_path);
            }
            else
            {
                if (realpath($item) != '')
                {
                    copy($item, $destination . '/' . $relative_path);
                }
            }
        }
    }

    /**
     * Recursively create a folder and file structure INSIDE the test folder
     *
     * @param array $items - The array describing the files and folders you want to create inside the test folder
     */
    public function create($items)
    {
        if (isset($items['files']))
        {
            foreach ($items['files'] as $file)
            {
                $pathinfo = pathinfo($file['path']);
                if ($pathinfo['dirname'] != '.' && !file_exists($this->path . '/' . $pathinfo['dirname']))
                {
                    mkdir($this->path . '/' . $pathinfo['dirname'], 0777, true);
                }
                file_put_contents($this->getPath() . '/' . $file['path'], $file['content']);
            }
        }
        if (isset($items['folders']))
        {
            foreach ($items['folders'] as $folder)
            {
                if (!file_exists($this->path . '/' . $folder))
                {
                    mkdir($this->path . '/' . $folder, 0777, true);
                }
            }
        }
    }

    /**
     * Generates a path inside the system's temporary folder
     *
     * @param int $i - Discriminating part of the path
     */
    private function generateTemporaryPath($i)
    {
        $this->path = realpath(sys_get_temp_dir()) . '/test_' . $i;
    }
}