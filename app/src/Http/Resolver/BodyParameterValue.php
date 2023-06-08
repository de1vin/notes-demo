<?php
namespace App\Http\Resolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class BodyParameterValue
 */
readonly class BodyParameterValue implements ValueResolverInterface
{
    public function __construct(
        private DenormalizerInterface $denormalizer
    ) {}

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
        $attr = $argument->getAttributes(BodyValue::class);

        if (empty($attr)) {
            return [];
        }

        $content = $request->getContent();

        if (empty($content)) {
            throw new HttpException(400, 'Invalid JSON body');
        }

        $type = $argument->getType();
        $data = json_decode($content);

        return [$this->denormalizer->denormalize($data, $type)];
    }
}
