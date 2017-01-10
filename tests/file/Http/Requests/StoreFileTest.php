<?php

use Ohio\Storage\File\Http\Requests\StoreFile;

class StoreFileTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers \Ohio\Storage\File\Http\Requests\StoreFile::rules
     */
    public function test()
    {

        $request = new StoreFile();

        $this->assertNotEmpty($request->rules());
    }

}