<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponser;
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        /*
         * ModelNotFoundException은 기본적으로 $internalDontReport에 있지만
         * render 함수를 오버라이딩하여 처리할 수 있다.
         */
        if($e instanceof ModelNotFoundException) {
            $model = strtolower(class_basename($e->getModel()));
            return $this->errorResponse("{$model}가 존재하지 않습니다.", Response::HTTP_NOT_FOUND);
        }

        if($e instanceof ValidationException) {
            return $this->errorResponse($e->validator->errors()->getMessages(),Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if($e instanceof AuthenticationException) {
            return $this->errorResponse("인증이 안되었습니다.", Response::HTTP_UNAUTHORIZED);
        }

        if($e instanceof UnauthorizedException) {
            return $this->errorResponse($e->getMessages(),Response::HTTP_FORBIDDEN);
        }

        if($e instanceof NotFoundHttpException) {
            return $this->errorResponse('URL이 없습니다', Response::HTTP_NOT_FOUND);
        }

        if($e instanceof MethodNotAllowedHttpException) {
            return $this->errorResponse('요청에 대한 메소드를 처리할 수 없습니다.', Response::HTTP_METHOD_NOT_ALLOWED);
        }

        if($e instanceof HttpException) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());
        }

        if($e instanceof QueryException) {
            $code = $e->errorInfo[1];
            if($code == 1451) {
                return $this->errorResponse("다른 리소스와 연관되어 있어 지울 수 없습니다.", Response::HTTP_CONFLICT);
            }
        }

        if(config('app.debug')) {
            return parent::render($request, $e);
        }

        return $this->errorResponse('예기치 않은 예외입니다', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
