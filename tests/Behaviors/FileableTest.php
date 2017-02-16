<?php

use Mockery as m;

use Belt\Core\Testing\BeltTestCase;
use Belt\Storage\Behaviors\Fileable;
use Belt\Storage\File;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class FileableTest extends BeltTestCase
{

    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \Belt\Storage\Behaviors\Fileable::files
     * @covers \Belt\Storage\Behaviors\Fileable::getResizePresets
     * @covers \Belt\Storage\Behaviors\Fileable::getBelongsToManyCaller
     */
    public function test()
    {
        # files
        $morphMany = m::mock(Relation::class);
        $morphMany->shouldReceive('orderBy')->withArgs(['position']);
        $pageMock = m::mock(FileableTestStub::class . '[morphMany]');
        $pageMock->shouldReceive('morphMany')->withArgs([File::class, 'fileable'])->andReturn($morphMany);
        $pageMock->shouldReceive('files');
        $pageMock->files();

        # getResizePresets
        $this->assertNotEmpty(FileableTestStub::getResizePresets());

        # getBelongsToManyCaller
        $this->assertNotEmpty((new FileableTestStub)->files());
    }

}

class FileableTestStub extends Model
{
    use Fileable;

    public static $presets = [
        100, 100
    ];
}