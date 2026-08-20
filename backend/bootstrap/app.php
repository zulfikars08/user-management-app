<?php

use App\Http\Middleware\EnsureUserHasRole;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => EnsureUserHasRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $json = fn (Request $request): bool => $request->is('api/*');

        // Laravel evaluates render callbacks in reverse registration order.
        $exceptions->render(function (Throwable $exception, Request $request) use ($json) {
            if (! $json($request) || $exception instanceof ValidationException
                || $exception instanceof ModelNotFoundException
                || $exception instanceof NotFoundHttpException
                || $exception instanceof AuthenticationException
                || $exception instanceof AuthorizationException
                || $exception instanceof AccessDeniedHttpException) {
                return null;
            }

            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'errors' => null,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        });

        $exceptions->render(function (ValidationException $exception, Request $request) use ($json) {
            return $json($request) ? response()->json([
                'success' => false,
                'message' => 'The given data was invalid.',
                'errors' => $exception->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY) : null;
        });

        $exceptions->render(function (ModelNotFoundException $exception, Request $request) use ($json) {
            return $json($request) ? response()->json([
                'success' => false,
                'message' => 'Resource not found.',
                'errors' => null,
            ], Response::HTTP_NOT_FOUND) : null;
        });

        $exceptions->render(function (NotFoundHttpException $exception, Request $request) use ($json) {
            return $json($request) ? response()->json([
                'success' => false,
                'message' => 'Resource not found.',
                'errors' => null,
            ], Response::HTTP_NOT_FOUND) : null;
        });

        $exceptions->render(function (AuthenticationException $exception, Request $request) use ($json) {
            return $json($request) ? response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
                'errors' => null,
            ], Response::HTTP_UNAUTHORIZED) : null;
        });

        $exceptions->render(function (AuthorizationException $exception, Request $request) use ($json) {
            return $json($request) ? response()->json([
                'success' => false,
                'message' => 'Forbidden.',
                'errors' => null,
            ], Response::HTTP_FORBIDDEN) : null;
        });

        $exceptions->render(function (AccessDeniedHttpException $exception, Request $request) use ($json) {
            return $json($request) ? response()->json([
                'success' => false,
                'message' => 'Forbidden.',
                'errors' => null,
            ], Response::HTTP_FORBIDDEN) : null;
        });

    })->create();
