<?php

namespace App\Support;

class Flash
{
    private ?string $title;

    private ?string $message;

    private string $type;

    private bool $autoClose;

    private int $seconds;

    public function __construct(
        ?string $title = null,
        ?string $message = null,
        string $type = 'success',
        bool $autoClose = true,
        int $seconds = 10
    ) {
        $this->autoClose = $autoClose;
        $this->message = $message;
        $this->seconds = $seconds;
        $this->title = $title;
        $this->type = $type;
    }

    public function index(): void
    {
        session()->flash('flash', [
            'message' => $this->message ?? $this->fetchMessage($this->type),
            'title' => $this->title ?? $this->fetchTitle($this->type),
            'autoClose' => $this->autoClose,
            'seconds' => $this->seconds,
            'type' => $this->type,
        ]);
    }

    private function fetchMessage(string $type): string
    {
        return match ($type) {
            'error' => 'We are working to fix it as soon as possible.',
            default => 'Task completed successfully. Well done!',
            'info' => '',
        };
    }

    private function fetchTitle(string $type): string
    {
        return match ($type) {
            default => 'Operation Successful!',
            'error' => 'Something Went Wrong!',
            'info' => 'Heads Up!',
        };
    }
}
