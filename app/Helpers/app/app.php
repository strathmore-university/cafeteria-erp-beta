<?php

if ( ! function_exists('fire')) {
    /**
     * @throws Throwable
     */
    function fire(bool $condition, string $message): void
    {
        throw_if($condition, new Exception($message));
    }
}
