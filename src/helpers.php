<?php

declare(strict_types=1);

use Extended\API\Request;
use Extended\API\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

if (!function_exists('abort')) {
    /**
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    function abort($code, $message = '', array $headers = [])
    {
        if ($code == 404) {
            throw new NotFoundHttpException(message: $message, headers: $headers);
        }

        throw new HttpException(statusCode: $code, message: $message, headers: $headers);
    }
}

if (!function_exists('register_extended_rest_route')) {
    function register_extended_rest_route(string $namespace, string $route, array $args = [], bool $override = false)
    {
        $callback = $args['callback'];

        $args['callback'] = function (WP_REST_Request $request) use ($callback) {
            try {
                return $callback(
                    Request::fromWordPressRestRequest($request)
                );
            } catch (ValidationException $exception) {
                return response($exception);
            } catch (HttpException $exception) {
                return response($exception);
            }
        };

        register_rest_route($namespace, $route, $args, $override);
    }
}

if (!function_exists('response')) {
    function response(
        mixed $data = [],
        int $status = 200,
        array $headers = []
    ): Response {
        if ($data instanceof ValidationException) {
            $firstMessage = $data->validator->errors()->first();

            return new Response(
                ['message' => $firstMessage],
                $data->status,
            );
        }

        if ($data instanceof HttpException) {
            return new Response(
                ['message' => $data->getMessage()],
                $data->getStatusCode(),
                $data->getHeaders()
            );
        }

        return new Response($data, $status, $headers);
    }
}
