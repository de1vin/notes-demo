<?php
namespace App\Http\Resolver;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;


/**
 * Class QueryParameterResolver
 */
readonly class QueryParameterResolver implements ValueResolverInterface
{
    /**
     * @param DenormalizerInterface $denormalizer
     */
    public function __construct(
        private DenormalizerInterface $denormalizer
    )
    {}

    /**
     * @param Request          $request
     * @param ArgumentMetadata $argument
     *
     * @return iterable
     *
     * @throws
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $name = $argument->getName();
        $type = $argument->getType();
        $nullable = $argument->isNullable();
        $required = false; //TODO: Load value from attribute
        $values = $request->query->all();
        $value = $values[$name] ?? null;

        if (!$type && $argument->hasDefaultValue()) {
            $type = gettype($argument->getDefaultValue());
        }

        if (!$value && $argument->hasDefaultValue()) {
            $value = $argument->getDefaultValue();
        }

        if ($required && !$value) {
            throw new InvalidArgumentException("Request query parameter '" . $name . "' is required, but not set.");
        }

        if (class_exists($type)) {
            $data = is_array($value) ? $value : $values;
            $resultValue = $this->denormalizer->denormalize($data, $type);
        } else {
            $resultValue = match ($type) {
                'int', 'integer' => $value ? (int)$value : 0,
                'float' => $value ? (float)$value : .0,
                'bool' => (bool)$value,
                'string' => $value ? (string)$value : ($nullable ? null : ''),
                null => null
            };
        }

        yield $resultValue;
    }
}
