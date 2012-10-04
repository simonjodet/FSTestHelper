<?php

require_once __DIR__ . '/../FSTestHelper/FSTestHelper.php';


/**
 * TODO: Add a method that recursively creates a path
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

    public function test_delete_only_deletes_items_that_are_inside_its_test_folder()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $path1 = $FSTestHelper->getPath();
        touch($path1 . '/file');
        $deletingFSTestHelper = new \FSTestHelper\FSTestHelper();
        $deletingFSTestHelper->delete($path1 . '/file');

        $this->assertFileExists($path1 . '/file');
    }

    public function test_itemize_returns_a_recursive_and_sorted_list_of_the_given_path_content()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $path = $FSTestHelper->getPath();
        mkdir($path . '/folder1');
        mkdir($path . '/folder1/folder2');
        touch($path . '/folder1/file1');
        touch($path . '/folder1/folder2/file2');

        $listingFSTestHelper = new \FSTestHelper\FSTestHelper();

        $this->assertEquals(array(
                $path . '/folder1',
                $path . '/folder1/file1',
                $path . '/folder1/folder2',
                $path . '/folder1/folder2/file2'
            ),
            $listingFSTestHelper->itemize($path));
    }

    public function test_copy_recursively_copies_a_folder_to_the_set_destination()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $source = $FSTestHelper->getPath();
        mkdir($source . '/folder1');
        mkdir($source . '/folder1/folder2');
        touch($source . '/folder1/file1');
        touch($source . '/folder1/folder2/file2');

        $copyingFSTestHelper = new \FSTestHelper\FSTestHelper();
        $copyingFSTestHelper->copy($source, '/target');
        $target = $copyingFSTestHelper->getPath() . '/target';

        $this->assertFileExists($target . '/folder1');
        $this->assertFileExists($target . '/folder1/file1');
        $this->assertFileExists($target . '/folder1/folder2');
        $this->assertFileExists($target . '/folder1/folder2/file2');
    }

    public function test_create_creates_files_at_the_correct_location_with_the_correct_content()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $FSTestHelper->create(
            array(
                'files' => array(
                    array(
                        'path' => 'some_file',
                        'content' => 'content'
                    ),
                    array(
                        'path' => 'folder1/folder2/some_other_file',
                        'content' => 'other_content'
                    )
                )
            )
        );
        $this->assertStringEqualsFile($FSTestHelper->getPath() . '/some_file', 'content');
        $this->assertStringEqualsFile($FSTestHelper->getPath() . '/folder1/folder2/some_other_file', 'other_content');
    }
}

