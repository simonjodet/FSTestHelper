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

    public function __toString()
    {
        return $this->path;
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
                $list = $this->itemize($path);
                $list[] = $path;
                rsort($list);
                foreach ($list as $item)
                {
                    if (is_dir($item))
                    {
                        rmdir($item);
                    }
                    else
                    {
                        unlink($item);
                    }
                }
            }
            else
            {
                unlink($path);
            }
        }
    }

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

    private function generateTemporaryPath($i)
    {
        $this->path = realpath(sys_get_temp_dir()) . '/test_' . $i;
    }
}