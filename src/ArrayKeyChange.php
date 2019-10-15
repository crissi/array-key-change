<?php
namespace Crissi\Helpers;

use InvalidArgumentException;

class ArrayKeyChange
{
    private $skipMissingPaths = false;

    private $array;

    private $currentFullPath;

    private function __construct(array $array)
    {
        $this->array = $array;
    }

    public static function in(array $array): self
    {
        return new self($array);
    }

    public function skipMissingPaths(): self
    {
        $this->skipMissingPaths = true;
        return $this;
    }

    public function modify(array $pathKeyMappings): array
    {
        $pathKeyMappings = $this->orderPaths($pathKeyMappings);

        foreach ($pathKeyMappings as $path => $replaceKey) {
            $this->currentFullPath = $path;
            $this->array = $this->changeKey($this->array, explode('.', $path), $replaceKey);
        }
        return $this->array;
    }

    private function orderPaths(array $paths): array
    {
        uksort($paths, function (string $a, string $b) {
            return $this->pathLevels($b) <=> $this->pathLevels($a);
        });
        return $paths;
    }

    private function pathLevels(string $path): int
    {
        return substr_count($path, '.');
    }

    private function changeKey(array &$target, array $segments, string $replaceKey): array
    {
        $segment = array_shift($segments);

        if (empty($segments)) {
            if (isset($target[$segment])) {
                $target[$replaceKey] = $target[$segment];
                unset($target[$segment]);
            } elseif ($this->skipMissingPaths === false) {
                $this->throwError();
            }
            return $target;
        }

        if ($segment === '*') {
            foreach ($target as &$inner) {
                $this->changeKey($inner, $segments, $replaceKey);
            }
            return $target;
        }

        if ($this->skipMissingPaths === false && !isset($target[$segment])) {
            $this->throwError();
        }

        $this->changeKey($target[$segment], $segments, $replaceKey);
        
        return $target;
    }

    private function throwError(): void
    {
        throw new InvalidArgumentException("Path '{$this->currentFullPath}' does not exists.");
    }
}
