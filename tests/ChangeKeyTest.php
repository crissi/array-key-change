<?php
namespace Crissi\ArrayKeyChange;

use PHPUnit\Framework\TestCase;
use Crissi\ArrayKeyChange\ArrayKeyChange;
use InvalidArgumentException;

class ChangeKeyTest extends TestCase
{
    public function testStarOperatorForLists()
    {
        $source = [
            'alpha' => '1337',
            'omega' => [44,45, 45],
            'charlie' => [
                ['foo' => '222'],
                ['foo' => '44444'],
                ['foo' => '444'],
                [
                    'foo' => 'ggfhf',
                    'lol' => '3434'
                ],
            ],
        ];

        $expected = [
            'alpha' => '1337',
            'omega' => [44,45, 45],
            'charlie' => [
                ['bar' => '222'],
                ['bar' => '44444'],
                ['bar' => '444'],
                [
                    'bar' => 'ggfhf',
                    'lol' => '3434'
                ],
            ],
        ];
        
        $this->assertEquals($expected, ArrayKeyChange::in($source)->modify(['charlie.*.foo' => 'bar']));
    }

    public function testMultipleStarOperators()
    {
        $source = [
            'alpha' => '1337',
            'omega' => [44,45, 45],
            'list1' => [
                [
                    'foo' => [
                        'list2' => [
                            [
                                'take' => 2,
                                'hol' => 5,
                            ],
                            [
                                'hol' => 5,
                            ],
                            [
                                'hol' => 5,
                            ]
                        ]
                    ],
                ],
                [
                    'foo' => [
                        'list2' => [
                            [
                                'take' => 2,
                                'hol' => 5,
                            ],
                            [
                                'hol' => 5,
                            ],
                            [
                                'hol' => 5,
                            ]
                        ]
                    ],
                ]
            ]
        ];
        
        $expected = [
            'alpha' => '1337',
            'omega' => [44,45, 45],
            'list1' => [
                [
                    'foo' => [
                        'list2' => [
                            [
                                'take' => 2,
                                'rofl' => 5,
                            ],
                            [
                                'rofl' => 5,
                            ],
                            [
                                'rofl' => 5,
                            ]
                        ]
                    ],
                ],
                [
                    'foo' => [
                        'list2' => [
                            [
                                'take' => 2,
                                'rofl' => 5,
                            ],
                            [
                                'rofl' => 5,
                            ],
                            [
                                'rofl' => 5,
                            ]
                        ]
                    ],
                ]
            ]
        ];

        $this->assertEquals($expected, ArrayKeyChange::in($source)->modify(['list1.*.foo.list2.*.hol' => 'rofl']));
    }
    
    public function testStarOperatorForListsKeysMissing()
    {
        $source = [
            'alpha' => '1337',
            'omega' => [44,45, 45],
            'charlie' => [
                ['foo' => '222'],
                ['foo' => '44444'],
                [],
                [
                    'foo' => 'ggfhf',
                    'lol' => '3434'
                ],
            ],
        ];

        $expected = [
            'alpha' => '1337',
            'omega' => [44,45, 45],
            'charlie' => [
                ['bar' => '222'],
                ['bar' => '44444'],
                [],
                [
                    'bar' => 'ggfhf',
                    'lol' => '3434'
                ],
            ],
        ];
        
        $this->assertEquals($expected, ArrayKeyChange::in($source)->skipMissingPaths()->modify(['charlie.*.foo' => 'bar']));
    }


    public function testItDoesNotModifyTheSource()
    {
        $source = [
            'alpha' => '1337',
            'omega' => [44,45, 45],
            'charlie' => [
                ['foo' => '222'],
                ['foo' => '44444'],
                ['foo' => '444'],
                [
                    'foo' => 'ggfhf',
                    'lol' => '3434'
                ],
            ],
        ];

        $sourceCopy = $source;

        ArrayKeyChange::in($source)->modify(['charlie.*.foo' => 'bar']);
        $this->assertEquals($sourceCopy, $source);
    }
    public function testSimple()
    {
        $source = [
            'alpha' => '1337',
            'omega' => [44,45, 45],
            'charlie' => [
                ['foo' => '222'],
                ['foo' => '44444']
            ]
        ];

        $expected = [
            'beta' => '1337',
            'omega' => [44,45, 45],
            'charlie' => [
                ['foo' => '222'],
                ['foo' => '44444']
            ]
        ];
        $this->assertEquals($expected, ArrayKeyChange::in($source)->modify(['alpha' => 'beta']));
    }

    public function testSimpleWithMissingKeyShouldStayTheSame()
    {
        $source = [
            'alpha' => '1337',
            'omega' => [44,45, 45],
            'charlie' => [
                ['foo' => '222'],
                ['foo' => '44444'],
            ],
        ];

        $expected = [
            'alpha' => '1337',
            'omega' => [44,45, 45],
            'charlie' => [
                ['foo' => '222'],
                ['foo' => '44444']
            ],
        ];

        $this->assertEquals($expected, ArrayKeyChange::in($source)->skipMissingPaths()->modify(['bravo' => 'beta']));
    }

    public function testNestedWithMissingKeyInStrictMode()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Path 'alpha.bravo1.nested' does not exists");

        $source = [
            'alpha' => [
                'bravo' => [
                    'nested' => [1, 2, 3]
                ]
            ],
            'omega' => [44,45, 45],
        ];

        ArrayKeyChange::in($source)->modify(['alpha.bravo1.nested' => 'beta']);
    }

    public function testSimpleWithMissingKeyInStrictMode()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Path 'alpha.bravo1' does not exists");
        $source = [
            'alpha' => [
                'bravo' => [
                    'nested' => [1, 2, 3]
                ]
            ],
            'omega' => [44,45, 45],
        ];

        ArrayKeyChange::in($source)->modify(['alpha.bravo1' => 'beta']);
    }


    public function testNested()
    {
        $source = [
            'alpha' => [
                'bravo' => [
                    'nested' => [1, 2, 3]
                ]
            ],
            'omega' => [44,45, 45],
            'charlie' => [
                ['foo' => '222'],
                ['foo' => '44444'],
                ['foo' => '444'],
                [
                    'foo' => 'ggfhf',
                    'lol' => '3434'
                ],
            ],
        ];

        $expected = [
            'alpha' => [
                'bravo' => [
                    'newkey' => [1, 2, 3]
                ]
            ],
            'omega' => [44,45, 45],
            'charlie' => [
                ['foo' => '222'],
                ['foo' => '44444'],
                ['foo' => '444'],
                [
                    'foo' => 'ggfhf',
                    'lol' => '3434'
                ],
            ],
        ];

        $this->assertEquals($expected, ArrayKeyChange::in($source)->modify(['alpha.bravo.nested' => 'newkey']));
    }


    public function testChangingMultiplePathsAtATime()
    {
        $source = [
            'alpha' => [
                'bravo' => [
                    'nested' => [1, 2, 3]
                ]
            ],
            'whiskey' => [44,45, 45],
            'charlie' => [
                ['foo' => '222'],
                ['foo' => '44444'],
                ['foo' => '444'],
                [
                    'foo' => 'text',
                    'lol' => '3434'
                ],
            ],
        ];

        $expected = [
            'foxtrot' => [
                'echo' => [
                    'delta' => [1, 2, 3]
                ]
            ],
            'whiskey' => [44,45, 45],
            'omega' => [
                ['foo' => '222'],
                ['foo' => '44444'],
                ['foo' => '444'],
                [
                    'foo' => 'text',
                    'rofl' => '3434'
                ],
            ],
        ];

        $this->assertEquals($expected, ArrayKeyChange::in($source)->skipMissingPaths()->modify([
            'alpha.bravo.nested' => 'delta',
            'alpha' => 'foxtrot',
            'alpha.bravo' => 'echo',
            'charlie.*.lol' => 'rofl',
            'charlie' => 'omega',
        ]));
    }


    public function testParentAndChildPathsAreDoneInTheCorrectOrder()
    {
        $source = [
            'alpha' => [
                'bravo' => [
                    'nested' => [1, 2, 3]
                ]
            ]
        ];

        $expected = [
            'alpha' => [
                'echo' => [
                    'delta' => [1, 2, 3]
                ]
            ]
        ];

        $this->assertEquals($expected, ArrayKeyChange::in($source)->modify([
            'alpha.bravo.nested' => 'delta',
            'alpha.bravo' => 'echo',
        ]));

        $this->assertEquals($expected, ArrayKeyChange::in($source)->modify([
            'alpha.bravo' => 'echo',
            'alpha.bravo.nested' => 'delta',
        ]));
    }
}
