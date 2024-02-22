<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Mail\ExceptionOccured;
use Illuminate\Support\Facades\Log;
use Mail;
use Symfony\Component\Debug\Exception\FlattenException as DebugFlattenException;
use Symfony\Component\Debug\ExceptionHandler as SymfonyExceptionHandler;
class Handler extends ExceptionHandler
{
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
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        $enableEmailExceptions = config('exceptions.emailExceptionEnabled');

        if ($enableEmailExceptions === '') {
            $enableEmailExceptions = config('exceptions.emailExceptionEnabledDefault');
        }

        if ($enableEmailExceptions && $this->shouldReport($exception)) {
            $this->sendEmail($exception);
        }

        parent::report($exception);
    }

    public function sendEmail(\Throwable $exception)
    {
        
        try {
            $e = DebugFlattenException::create($exception);
            $handler = new SymfonyExceptionHandler();
            $html = $handler->getHtml($e);
            Mail::send(new ExceptionOccured($html));
        } catch (\Throwable $exception) {
            Log::error($exception);
        }
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
        return parent::render($request, $exception);
    }
}
