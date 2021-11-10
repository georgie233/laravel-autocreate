<?php

namespace Georgie\AutoCreate\Exceptions;

use Throwable;

class AutoCreateException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render($request)
    {
        if (request()->expectsJson()) {
            return ['code' => 401, 'message' => $this->message];
        }
        session()->flash('danger', $this->getMessage());

        return redirect()->back();
    }
}
