*Easily change keys in a array of any depth*

Example:
```
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

$result = ArrayKeyChange::in($source)
    ->skipMissingPaths()
    ->modify([
        'alpha.bravo.nested' => 'delta',
        'alpha' => 'foxtrot',
        'alpha.bravo' => 'echo',
        'charlie.*.lol' => 'rofl',
        'charlie' => 'omega'
    ]);
```
Result:
```
[
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
```
> Use skipMissingPaths() to avoid errors on missing keys

> Use the star-operator (*) to traverse sequential arrays

Changes
-------
### 1.0
*	Initial public release
