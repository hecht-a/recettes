<?php

namespace App\Http\Twig\Components;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class Alert
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?string $message = null;

    #[LiveProp(writable: true)]
    public ?string $type = null;

    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    #[LiveListener('alert:message')]
    public function setMessage(#[LiveArg] string $type, #[LiveArg] string $message): void
    {
        $this->type = $type;
        $this->message = $message;
    }

    #[LiveAction]
    public function reset(): void
    {
        $this->type = null;
        $this->message = null;
    }
}
