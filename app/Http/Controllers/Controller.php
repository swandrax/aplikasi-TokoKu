<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function modalAlert(string $type, string $title, string $text): array
    {
        return [
            'type' => $type,
            'title' => $title,
            'text' => $text,
        ];
    }
}
