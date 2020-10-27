<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Traits\ApiResponser;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    use ApiResponser;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (NotFoundHttpException $e, $request) {
            return $this->errorResponse('The specified URL cannot be found', 404);
        });

        $this->renderable(function (ValidationException $e, $request) {
            if($e->status == 422)
                return $this->errorResponse('You need to specify a different value to update', 404);
            else
                return $this->errorResponse($e, 404);
        });

        $this->renderable(function (MethodNotAllowedHttpException $e, $request) {
             //return $this->errorResponse('MethodNotAllowedHttpException', 404);
            return $this->errorResponse($e, 404);
        });

        // if (config('app.debug')) {
        //     return $this->errorResponse($e,500);          
        // }

        return $this->errorResponse('Unexpected Exception. Try later',500);
        //AuthenticationException
        //AuthorizationException
        //MethodNotAllowedHttpException
        //HttpException
        //ModelNotFoundException
        //QueryException

        //https://en.wikipedia.org/wiki/List_of_HTTP_status_codes

    }
}
