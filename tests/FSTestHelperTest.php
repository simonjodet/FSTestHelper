<?php

require_once __DIR__ . '/../FSTestHelper/FSTestHelper.php';

class FSTestHelperTest extends \PHPUnit_Framework_TestCase
{
    public function test_createTree_requires_an_array()
    {
        $this->setExpectedException('\FSTestHelper\Exception', 'Malformed array passed to FSTestHelper::createTree()');
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $FSTestHelper->createTree('not_an_array');
    }

    public function test_createTree_requires_an_array_with_an_array_of_folders()
    {
        $this->setExpectedException('\FSTestHelper\Exception', 'Malformed array passed to FSTestHelper::createTree()');
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $FSTestHelper->createTree(array(
            'folders' => 'not an array'
        ));
    }

    public function test_createTree_requires_an_array_with_an_array_of_files()
    {
        $this->setExpectedException('\FSTestHelper\Exception', 'Malformed array passed to FSTestHelper::createTree()');
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $FSTestHelper->createTree(
            array('folders' => array())
        );
    }

    public function test_createTree_creates_the_tree_correctly()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $FSTestHelper->createTree(
            array(
                'folders' => array(
                    'some_folder',
                    'other_folder'
                ),
                'files' => array(
                    array(
                        'path' => 'some_file',
                        'content' => 'content'
                    ),
                    array(
                        'path' => 'test/other_file',
                        'content' => 'content'
                    )
                )
            )
        );
        $temporaryPath = $FSTestHelper->getTemporaryPath();

        $this->assertFileExists($temporaryPath . '/some_folder');
        $this->assertFileExists($temporaryPath . '/other_folder');
        $this->assertStringEqualsFile($temporaryPath . '/some_file', 'content');
        $this->assertStringEqualsFile($temporaryPath . '/test/other_file', 'content');
    }


    public function test_generateUniqueTemporaryPath_always_returns_the_same_path()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $path1 = $FSTestHelper->generateUniqueTemporaryPath(false);

        $this->assertEquals($path1, $FSTestHelper->generateUniqueTemporaryPath());
    }

    public function test_generateUniqueTemporaryPath_returns_folder_with_expected_name()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $path = $FSTestHelper->generateUniqueTemporaryPath();

        $this->assertRegExp('#^(.*)/test_\d*$#', $path);
    }

    public function test_createFolder_creates_the_passed_folder()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $testFolderPath = $FSTestHelper->createFolder('test_folder');
        $this->assertFileExists($testFolderPath);
    }

    public function test_createFolder_creates_the_passed_folder_recursively()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $testFolderPath = $FSTestHelper->createFolder('test_folder/test_folder');
        $this->assertFileExists($testFolderPath);
    }

    public function test_createFile_throws_exception_if_file_is_not_an_array()
    {
        $this->setExpectedException('\FSTestHelper\Exception', 'Malformed file description passed to FSTestHelper::createFile()');
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $FSTestHelper->createFile('not_an_array');
    }

    public function test_createFile_throws_exception_if_file_does_not_have_path()
    {
        $this->setExpectedException('\FSTestHelper\Exception', 'Malformed file description passed to FSTestHelper::createFile()');
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $FSTestHelper->createFile(array());
    }

    public function test_createFile_throws_exception_if_file_does_not_have_content()
    {
        $this->setExpectedException('\FSTestHelper\Exception', 'Malformed file description passed to FSTestHelper::createFile()');
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $FSTestHelper->createFile(
            array(
                'path' => 'some name'
            )
        );
    }

    public function test_createFile_creates_file_with_correct_content_at_correct_path()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $path = $FSTestHelper->createFile(
            array(
                'path' => 'some_name',
                'content' => 'some_content'
            )
        );
        $this->assertStringEqualsFile($path, 'some_content');

        $path = $FSTestHelper->createFile(
            array(
                'path' => 'path/some_name',
                'content' => 'some_content'
            )
        );
        $this->assertStringEqualsFile($path, 'some_content');
    }

    public function test___destruct_deletes_all_files_and_folders()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $path1 = $FSTestHelper->createFile(
            array(
                'path' => 'some_name',
                'content' => 'some_content'
            )
        );
        $path2 = $FSTestHelper->createFile(
            array(
                'path' => 'path/some_name',
                'content' => 'some_content'
            )
        );

        unset($FSTestHelper);

        $this->assertFileNotExists($path1);
        $this->assertFileNotExists($path2);
    }

    public function test___toString_method_returns_the_temporary_path()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();

        $this->assertEquals($FSTestHelper->getTemporaryPath(), strval($FSTestHelper));
    }

    public function test_createTreeFromJson_creates_the_tree_correctly()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $FSTestHelper->createTreeFromJson('
        {
            "folders":[
                "some_folder",
                "other_folder"
            ],
            "files":[
                {
                    "path":"some_file",
                    "content":"content"
                },
                {
                    "path":"test/other_file",
                    "content":"content"
                }
            ]
        }
        ');
        $temporaryPath = $FSTestHelper->getTemporaryPath();

        $this->assertFileExists($temporaryPath . '/some_folder');
        $this->assertFileExists($temporaryPath . '/other_folder');
        $this->assertStringEqualsFile($temporaryPath . '/some_file', 'content');
        $this->assertStringEqualsFile($temporaryPath . '/test/other_file', 'content');
    }

    public function test_createTreeFromJson_throws_an_exception_if_passed_invalid_JSON()
    {
        $this->setExpectedException(
            'FSTestHelper\Exception', 'Invalid JSON'
        );
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $FSTestHelper->createTreeFromJson('invalid json');
    }

    public function test_importFolderTree_returns_the_correct_JSON_object()
    {
        $FSTestHelper = new \FSTestHelper\FSTestHelper();
        $tree = array(
            'folders' => array(
                'other_folder',
                'some_folder'
            ),
            'files' => array(
                array(
                    'path' => 'some_file',
                    'content' => 'content'
                ),
                array(
                    'path' => 'test/other_file',
                    'content' => 'content'
                )
            )
        );
        $FSTestHelper->createTree($tree);
        $temporaryPath = $FSTestHelper->getTemporaryPath();

        $this->assertEquals($tree, json_decode($FSTestHelper->importFolderTree($temporaryPath), true));
    }

}

