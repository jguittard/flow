<?php

declare(strict_types=1);

namespace Flow\ETL\Tests\Integration\Filesystem\FilesystemStreams\NotPartitioned;

use function Flow\ETL\DSL\ignore;
use Flow\ETL\Filesystem\FilesystemStreams;
use Flow\ETL\Tests\Integration\Filesystem\FilesystemStreams\FilesystemStreamsTestCase;

final class IgnoreModeTest extends FilesystemStreamsTestCase
{
    protected function tearDown() : void
    {
        parent::tearDown();
        $this->cleanFiles();
    }

    public function test_open_stream_for_existing_file() : void
    {
        $streams = $this->streams();
        $this->setupFiles([
            __FUNCTION__ => [
                'existing-file.txt' => 'some content',
            ],
        ]);
        $path = $this->getPath(__FUNCTION__ . '/existing-file.txt');

        $fileStream = $streams->writeTo($path);
        $fileStream->append('different content');
        $streams->closeStreams($path);

        self::assertFileExists($path->path());
        self::assertSame('some content', \file_get_contents($path->path()));
    }

    public function test_open_stream_for_non_existing_file() : void
    {
        $streams = $this->streams();
        $this->setupFiles([
            __FUNCTION__ => [],
        ]);
        $path = $this->getPath(__FUNCTION__ . '/non-existing-file.txt');

        $fileStream = $streams->writeTo($path);
        $fileStream->append('some content');
        $streams->closeStreams($path);

        self::assertFileExists($path->path());
        self::assertSame('some content', \file_get_contents($path->path()));
    }

    protected function streams() : FilesystemStreams
    {
        $streams = new FilesystemStreams($this->fstab());
        $streams->setSaveMode(ignore());

        return $streams;
    }
}
