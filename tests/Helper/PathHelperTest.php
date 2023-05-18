<?php

namespace App\Tests\Helper;

use App\Helper\PathHelper;
use PHPUnit\Framework\TestCase;

class PathHelperTest extends TestCase
{
    public static function getPaths(): iterable
    {
        yield [['path1', 'path2', 'path3'], 'path1/path2/path3'];
        yield [['/path1', 'path2/', 'path3'], '/path1/path2/path3'];
        yield [['/path1/aze/a', 'path2/', 'path3'], '/path1/aze/a/path2/path3'];
    }

    /**
     * @dataProvider getPaths
     */
    public function testPathGeneration(array $parts, string $expected): void
    {
        $path = PathHelper::join(...$parts);
        $this->assertEquals($expected, $path);
    }
}
