<?php

declare(strict_types=1);

namespace Atoolo\Runtime\Test\Executor;

use Atoolo\Runtime\Executor\IniSetter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[CoversClass(IniSetter::class)]
class IniSetterTest extends TestCase
{
    public function testSetIni(): void
    {
        $iniSetter = new IniSetter();
        $iniSetter->execute('', [
            'package1' => [
                'ini' => [
                    'set' => [
                        'user_agent' => 'Test'
                    ]
                ]
            ]
        ]);
        $this->assertEquals(
            ini_get('user_agent'),
            'Test',
            'The user_agent should have been set'
        );
    }

    public function testSetIniTwiceWithSameValueAgain(): void
    {
        $iniSetter = new IniSetter();
        $iniSetter->execute('', [
            'package1' => [
                'ini' => [
                    'set' => [
                        'user_agent' => 'Test'
                    ]
                ]
            ],
            'package2' => [
                'ini' => [
                    'set' => [
                        'user_agent' => 'Test'
                    ]
                ]
            ]
        ]);
        $this->assertEquals(
            ini_get('user_agent'),
            'Test',
            'The user_agent should have been set'
        );
    }

    public function testSetIniTwiceWithDifferentValues(): void
    {
        $iniSetter = new IniSetter();
        $this->expectException(RuntimeException::class);
        $iniSetter->execute('', [
            'package1' => [
                'ini' => [
                    'set' => [
                        'user_agent' => 'Test'
                    ]
                ]
            ],
            'package2' => [
                'ini' => [
                    'set' => [
                        'user_agent' => 'Test2'
                    ]
                ]
            ]
        ]);
    }

    public function testSetIniSystemDirective(): void
    {
        $iniSetter = new IniSetter();
        $this->expectException(RuntimeException::class);
        $iniSetter->execute('', [
            'package1' => [
                'ini' => [
                    'set' => [
                        'allow_url_fopen' => 0
                    ]
                ]
            ]
        ]);
    }

    public function testSetIniWithNonScalar(): void
    {
        $iniSetter = new IniSetter();
        $this->expectException(RuntimeException::class);
        $iniSetter->execute('', [
            'package1' => [
                'ini' => [
                    'set' => [
                        'user_agent' => ['non' => 'scalar']
                    ]
                ]
            ]
        ]);
    }

    public function testSetIniWithNull(): void
    {
        $iniSetter = new IniSetter();
        $this->expectNotToPerformAssertions();
        $iniSetter->execute('', [
            'package1' => [
                'ini' => [
                    'set' => [
                        'user_agent' => null
                    ]
                ]
            ]
        ]);
    }
}
