<?php
namespace App\Http\Resolver;

use Attribute;

/**
 * Class QueryParameter
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class QueryParameter
{
    public function __construct(
        public bool $required = false,
        public string|null $type = null
    ) {}
}
