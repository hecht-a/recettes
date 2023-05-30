<?php

namespace App\Http\Twig;

use Knp\Bundle\PaginatorBundle\Helper\Processor;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigPaginatorExtension extends AbstractExtension
{
    public function __construct(private readonly Processor $processor)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('paginate', $this->paginate(...), ['is_safe' => ['html'], 'needs_environment' => true]),
        ];
    }

    /**
     * @param mixed[] $queryParams
     * @param mixed[] $viewParams
     */
    public function paginate(
        Environment $env,
        SlidingPagination $pagination,
        array $queryParams = [],
        array $viewParams = []
    ): string {
        return $env->render(
            $pagination->getTemplate() ?: 'partials/pagination.html.twig',
            $this->processor->render($pagination, $queryParams, $viewParams)
        );
    }
}
