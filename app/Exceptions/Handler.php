<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Mail,Auth;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\Debug\ExceptionHandler as SymfonyExceptionHandler;
use App\Mail\ExceptionOccured;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        // dd($exception);
        if ($this->shouldReport($exception)) {
            $requestedUri = request()->getUri();
            // sends an email
            $this->sendEmail($exception,$requestedUri);
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if("admin" == $request->path() || "admin/" == $request->path()){
            return redirect()->to('admin/login');
        }
        if($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException || $exception instanceof TokenMismatchException ) {
            return redirect()->to('/');
        }
        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest('login');
    }

    /**
     *  send email when exception occurs
     *  @param  \Exception  $exception
     */

    public function sendEmail(Exception $exception, $requestedUri)
    {

        try {
            $e = FlattenException::create($exception);

            $handler = new SymfonyExceptionHandler();

            $html['error'] = $handler->getHtml($e);
            $html['url'] = $requestedUri;

            Mail::to('vchipdesigng8@gmail.com')->send(new ExceptionOccured($html));
        } catch (Exception $ex) {
            return redirect()->to('/');
        }
    }
}