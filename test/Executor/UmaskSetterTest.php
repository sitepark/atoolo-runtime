<?php

declare(strict_types=1);

namespace Atoolo\Runtime\Test\Executor;

use Atoolo\Runtime\Executor\UmaskSetter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[CoversClass(UmaskSetter::class)]
class UmaskSetterTest extends TestCase
{
    private int $originUmask;

    public function setUp(): void
    {
        $this->originUmask = umask();
    }

    public function tearDown(): void
    {
        umask($this->originUmask);
    }

    public function testSetUmask(): void
    {
        $umaskSetter = new UmaskSetter();
        $umaskSetter->execute('', [
            'package1' => [
                'umask' => '0123'
            ]
        ]);
        $this->assertEquals(
            123,
            umask(),
            'The umask should have been set'
        );
    }

    public function testSetUmaskWithoutUmask(): void
    {
        $umaskSetter = new UmaskSetter();
        $this->expectNotToPerformAssertions();
        $umaskSetter->execute('', [
            'package1' => [
            ]
        ]);
    }

    public function testSetUmaskTwiceWithSameValue(): void
    {
        $umaskSetter = new UmaskSetter();
        $umaskSetter->execute('', [
            'package1' => [
                'umask' => '0123'
            ],
            'package2' => [
                'umask' => '0123'
            ]
        ]);
        $this->assertEquals(
            123,
            umask(),
            'The umask should have been set'
        );
    }

    public function testSetUmaskTwiceWithDifferentValues(): void
    {
        $umaskSetter = new UmaskSetter();
        $this->expectException(RuntimeException::class);
        $umaskSetter->execute('', [
            'package1' => [
                'umask' => '0123'
            ],
            'package2' => [
                'umask' => '0456'
            ]
        ]);
    }

    public function testSetUmaskWithNonNumericValue(): void
    {
        $umaskSetter = new UmaskSetter();
        $this->expectException(RuntimeException::class);
        $umaskSetter->execute('', [
            'package1' => [
                'umask' => 'abc'
            ],
        ]);
    }
}
