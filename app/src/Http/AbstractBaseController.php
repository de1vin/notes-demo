<?php

namespace App\Http;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class AbstractBaseController
 */
class AbstractBaseController extends AbstractController
{
    /**
     * @inheritDoc
     */
    public function json(mixed $data, int $status = 200, array $headers = [], array $context = []): JsonResponse
    {
        $context = array_merge([
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ], $context);

        return parent::json($data, $status, $headers, $context);
    }
}
