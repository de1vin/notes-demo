<?php
namespace App\EventListener;

use App\Exception\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Class ExceptionListener
 */
class ExceptionListener
{
    /**
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request   = $event->getRequest();

        if ($request->headers->get('Content-Type') === 'application/json')  {
            $responseData = [
                'message' => $exception->getMessage(),
                'errors' => []
            ];

            $response = new JsonResponse($responseData);

            if ($exception instanceof HttpExceptionInterface) {
                $response->setStatusCode($exception->getStatusCode());
            } else if ($exception instanceof ValidationException) {
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                $responseData['errors'] = $exception->getErrors();
                $response->setData($responseData);
            } else {
                $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $event->setResponse($response);
        }
    }
}
