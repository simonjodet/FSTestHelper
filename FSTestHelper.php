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
     * @var string The system temp folder
     */
    private $systemTempFolder;
    /**
     * @var string Object unique temporary path
     */
    private $temporaryPath;

    /**
     * Constructor - Get the system temp folder
     */
    public function __construct()
    {
        $this->systemTempFolder = realpath(sys_get_temp_dir());
    }

    /**
     * Destructor - Deletes all temporary items
     */
    public function __destruct()
    {
        if (!is_null($this->temporaryPath))
        {
            $this->deleteFolder($this->temporaryPath);
        }
    }

    /**
     * Create a file and folder tree
     *
     * Example:
     * $FSTestHelper = new \FSTestHelper\FSTestHelper();
     * $FSTestHelper->createTree(
     *   array(
     *     'folders' => array(
     *       'some_folder',
     *       'other_folder'
     *     ),
     *     'files' => array(
     *       array(
     *         'path' => 'some_file',
     *         'content' => 'content'
     *       ),
     *       array(
     *         'path' => 'test/other_file',
     *         'content' => 'content'
     *       )
     *     )
     *   )
     * );
     *
     * @param $tree array describing the file tree- Check the example above
     *
     * @throws Exception
     */
    public function createTree($tree)
    {
        if (!is_array($tree) || !isset($tree['folders']) || !is_array($tree['folders']) || !isset($tree['files']) || !is_array($tree['files']))
        {
            throw new Exception('Malformed array passed to FSTestHelper::createTree()');
        }

        foreach ($tree['folders'] as $folder)
        {
            $this->createFolder($folder);
        }

        foreach ($tree['files'] as $file)
        {
            $this->createFile($file);
        }
    }

    /**
     * @return string The generated unique temporary path
     */
    public function generateUniqueTemporaryPath()
    {
        if (is_null($this->temporaryPath))
        {
            $this->temporaryPath = $this->generateRandomPath();
            while (file_exists($this->temporaryPath))
            {
                $this->temporaryPath = $this->generateRandomPath();
            }
            mkdir($this->temporaryPath);
        }
        return $this->temporaryPath;
    }

    /**
     * Create a folder
     *
     * @param string $folder
     *
     * @return string
     */
    public function createFolder($folder)
    {
        $path = $this->generateUniqueTemporaryPath() . '/' . $folder;
        mkdir($path, 0777, true);
        return $path;
    }

    /**
     * Create a file
     *
     * Example:
     * $FSTestHelper = new \FSTestHelper\FSTestHelper();
     * $FSTestHelper->createFile(
     *   array(
     *     'path'=>'file_path',
     *     'content'=>'file_content'
     *   )
     * );
     *
     * @param array $file
     *
     * @return string
     * @throws Exception
     */
    public function createFile($file)
    {
        if (!is_array($file) || !isset($file['path']) || !isset($file['content']))
        {
            throw new Exception('Malformed file description passed to FSTestHelper::createFile()');
        }
        $file_path_info = pathinfo($file['path']);
        $path = $this->generateUniqueTemporaryPath() . '/' . $file_path_info['dirname'];
        if (!file_exists($path))
        {
            mkdir($path, 0777, true);
        }

        $path = $path . '/' . $file_path_info['basename'];
        file_put_contents($path, $file['content']);
        return $path;
    }

    /**
     * @return string Get the generated temporary path
     */
    public function getTemporaryPath()
    {
        return $this->temporaryPath;
    }

    /**
     * @return string
     */
    private function generateRandomPath()
    {
        return $this->systemTempFolder . '/test_' . rand(0, 1000000);
    }

    /**
     * @param $path
     */
    private function deleteFolder($path)
    {
        foreach (glob($path . '/*') as $file)
        {
            if (is_dir($file))
            {
                $this->deleteFolder($file);
            }
            else
            {
                unlink($file);
            }
        }
        rmdir($path);
    }

}

/**
 * FSTestHelper Exception class
 */
class Exception extends \Exception
{

}