<?php

namespace Md\Phunkie\Types;

use Error;
use Md\Phunkie\Cats\Show;
use function Md\Phunkie\Functions\functor\fmap;
use const Md\Phunkie\Functions\show\showValue;
use Md\Phunkie\Utils\Copiable;
use TypeError;

class Tuple implements Copiable
{
    use Show;
    private $values;

    final public function __construct(...$values)
    {
        $this->tupleIsSealed();
        $this->guardNumArgs(func_num_args());
        $this->values = $values;
    }

    public function __get($member)
    {
        $this->startsWithUnderscore($member);
        $this->followedByANumber($member);
        $this->includedInMembers($member);

        return $this->values[$this->keyFromMember($member)];
    }

    public function __set($arg, $value)
    {
        throw new \TypeError("Tuples are immutable");
    }

    public function copy(array $parameters): Tuple
    {
        $values = $this->values;

        foreach ($parameters as $parameter => $value) {
            $key = $this->keyFromMember($parameter);
            $this->validateKey($key, $parameter);
            $values[$key] = $value;
        }
        return Tuple(...$values);
    }

    public function toString(): string
    {

        return "(" . implode(", ", fmap(showValue, ImmList(...$this->values))->toArray()) . ")";
    }

    public function getArity(): int
    {
        return count($this->values);
    }

    private function guardNumArgs(int $numArgs)
    {
        if (get_class($this) === Unit::class && $numArgs > 0) {
            throw new \TypeError(sprintf("Unit does not take arguments %d given", $numArgs));
        }

        if (get_class($this) === Pair::class && $numArgs !== 2) {
            throw new \TypeError(sprintf("Pair must take exactly 2 arguments %d given", $numArgs));
        }
    }

    private function tupleIsSealed()
    {
        if (!in_array(get_class($this), [Tuple::class, Pair::class, Unit::class])) {
            throw new TypeError("Tuple is sealed. It cannot be extended outside Phunkie");
        }
    }

    private function validateKey($key, $parameter)
    {
        if (!array_key_exists($key, $this->values)) {
            throw new \InvalidArgumentException("$parameter is not a member of " . get_class($this) . ".");
        }
    }

    private function startsWithUnderscore($arg)
    {
        if (strpos($arg, "_") !== 0) {
            throw new Error("$arg is not a member of Tuple");
        }
    }

    private function followedByANumber($arg)
    {
        if (!is_numeric(substr($arg, 1))) {
            throw new Error("$arg is not a member of Tuple");
        }
    }

    private function includedInMembers($arg)
    {
        if (!array_key_exists($this->keyFromMember($arg), $this->values)) {
            throw new Error("$arg is not a member of Tuple");
        }
    }

    private function keyFromMember($arg): int
    {
        return ((integer)substr($arg, 1)) - 1;
    }
}