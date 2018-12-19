<?php

namespace Point\View;

use Slim\Views\Twig;

class Factory
{
    protected $view;

    public static function getEngine()
    {
        return new Twig(__DIR__ . '/../../resources/views', [
            // Enable cache on production
            // 'cache' => false,
            'cache' => __DIR__ . '/../../cache/views',
            'debug' => true,
        ]);
    }

    public function make($view, $data = [])
    {
        $this->view = static::getEngine()->fetch($view, $data);
        
        return $this;
    }

    public function render()
    {
        return $this->view;
    }
}