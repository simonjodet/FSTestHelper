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

}

