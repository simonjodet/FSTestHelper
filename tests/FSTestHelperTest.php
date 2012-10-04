<?php

require_once __DIR__ . '/../FSTestHelper/FSTestHelper.php';


/**
 * TODO: Add a method that recursively create a path
 * TODO: Add a method that recursively lists folders and files
 * TODO: Add a method that creates folders and files based on an array or JSON string to a specified location
 */
class FSTestHelperTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructor_returns_a_temporary_folder_in_the_system_temporary_folder()
    {
        $systemTempFolder = realpath(sys_get_temp_dir());
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $this->assertStringStartsWith($systemTempFolder . '/', $FSTestHelper->getPath());
    }

    public function test_constructor_returns_a_unique_temporary_folder()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $path1 = $FSTestHelper->getPath();
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $path2 = $FSTestHelper->getPath();

        $this->assertNotEquals($path1, $path2);
    }

    public function test_delete_deletes_files()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $path = $FSTestHelper->getPath();
        touch($path . '/file1');
        touch($path . '/file2');

        $FSTestHelper->delete('/file1');
        $FSTestHelper->delete('/file2');

        $this->assertFileNotExists($path . '/file1');
        $this->assertFileNotExists($path . '/file2');
    }

    public function test_delete_deletes_folders()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $path = $FSTestHelper->getPath();
        mkdir($path . '/folder1');
        mkdir($path . '/folder2');

        $FSTestHelper->delete('/folder1');
        $FSTestHelper->delete('/folder2');

        $this->assertFileNotExists($path . '/folder1');
        $this->assertFileNotExists($path . '/folder2');
    }

    public function test_delete_is_silent_if_given_path_does_not_exist()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $path = $FSTestHelper->getPath();

        $FSTestHelper->delete('/folder1');
        $FSTestHelper->delete('/file1');

        $this->assertFileNotExists($path . '/folder1');
        $this->assertFileNotExists($path . '/file1');
    }

    public function test_delete_empties_folders_before_deleting_them()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $path = $FSTestHelper->getPath();
        mkdir($path . '/folder1');
        mkdir($path . '/folder1/folder2');
        touch($path . '/folder1/file1');
        touch($path . '/folder1/folder2/file2');

        $FSTestHelper->delete('/folder1');

        $this->assertFileNotExists($path . '/folder1');
    }

    public function test_destructor_deletes_the_temporary_test_path()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $path = $FSTestHelper->getPath();
        mkdir($path . '/folder1');
        mkdir($path . '/folder1/folder2');
        touch($path . '/folder1/file1');
        touch($path . '/folder1/folder2/file2');

        unset($FSTestHelper);

        $this->assertFileNotExists($path);
    }
}

