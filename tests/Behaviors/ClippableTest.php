<?php

use Mockery as m;

use Belt\Core\Testing\BeltTestCase;
use Belt\Clip\Behaviors\Clippable;
use Belt\Clip\Attachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Relation;

class ClippableTest extends BeltTestCase
{

    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \Belt\Clip\Behaviors\Clippable::attachment
     * @covers \Belt\Clip\Behaviors\Clippable::attachments
     * @covers \Belt\Clip\Behaviors\Clippable::getResizePresets
     * @covers \Belt\Clip\Behaviors\Clippable::purgeAttachments
     * @covers \Belt\Clip\Behaviors\Clippable::getImageAttribute
     */
    public function test()
    {
        # attachments
        $morphMany = m::mock(Relation::class);
        $morphMany->shouldReceive('orderBy')->withArgs(['position']);
        $pageMock = m::mock(ClippableTestStub::class . '[morphMany]');
        $pageMock->shouldReceive('morphMany')->withArgs([Attachment::class, 'clippable'])->andReturn($morphMany);
        $pageMock->shouldReceive('attachments');
        $pageMock->attachments();

        # getResizePresets
        $this->assertNotEmpty(ClippableTestStub::getResizePresets());
        $this->assertEmpty(ClippableTestStub3::getResizePresets());
        app()['config']->set('belt.clip.resize.models.' . ClippableTestStub3::class, [
            [100, 100, 'fit'],
            [800, 800, 'fit'],
        ]);
        $this->assertNotEmpty(ClippableTestStub3::getResizePresets());

        # purgeAttachments
        $clippable = new ClippableTestStub();
        $clippable->id = 1;
        DB::shouldReceive('connection')->once()->andReturnSelf();
        DB::shouldReceive('table')->once()->with('clippables')->andReturnSelf();
        DB::shouldReceive('where')->once()->with('clippable_id', 1)->andReturnSelf();
        DB::shouldReceive('where')->once()->with('clippable_type', 'clippableTestStub')->andReturnSelf();
        DB::shouldReceive('delete')->once()->andReturnSelf();
        $clippable->purgeAttachments();

        # attachment
        $this->assertInstanceOf(BelongsTo::class, $clippable->attachment());

        # getImageAttribute
        Attachment::unguard();
        $clippable = new ClippableTestStub2();
        $clippable->attachments = new \Illuminate\Support\Collection();
        $this->assertInstanceOf(Attachment::class, $clippable->getImageAttribute());
        $clippable->attachments->push(new Attachment(['mimetype' => 'application/pdf']));
        $this->assertNull($clippable->getImageAttribute()->id);
        $clippable->attachments->push(new Attachment(['mimetype' => 'image/gif', 'id' => 1]));
        $this->assertEquals(1, $clippable->getImageAttribute()->id);


    }

}

class ClippableTestStub extends Model
{
    use Belt\Core\Behaviors\HasSortableTrait;
    use Clippable;

    public static $presets = [
        100,
        100
    ];

    public function getMorphClass()
    {
        return 'clippableTestStub';
    }
}

class ClippableTestStub2
{
    use Clippable;

    public static $presets = [
        100,
        100
    ];

    public function getMorphClass()
    {
        return 'clippableTestStub';
    }
}

class ClippableTestStub3
{
    use Clippable;

    public function getMorphClass()
    {
        return 'clippableTestStub';
    }
}