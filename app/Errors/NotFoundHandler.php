<?php

namespace Point\Errors;

use Slim\Views\Twig;
use Slim\Handlers\NotFound;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class NotFoundHandler extends NotFound
{
    protected $twig;
    protected $template;

    public function __construct(Twig $twig, $template)
    {
        $this->twig = $twig;
        $this->template = $template;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        parent::__invoke($request, $response);

		$this->twig->render($response, $this->template);

		return $response->withStatus(404);
    }
}