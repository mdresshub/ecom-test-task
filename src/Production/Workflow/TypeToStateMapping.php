<?php

declare(strict_types=1);

namespace Shop\Production\Workflow;

use Shop\Production\Article;
use Shop\Production\State\Framed;
use Shop\Production\State\GiftWrapped;
use Shop\Production\State\Initiated;
use Shop\Production\State\Ordered;
use Shop\Production\State\Printed;
use Shop\Production\State\Shipped;
use Shop\Production\State\Sliced;
use Shop\Production\State\StateInterface;

class TypeToStateMapping
{
    private const STATES_POSTER_FRAMED = [
        Initiated::class,
        Ordered::class,
        Printed::class,
        Sliced::class,
        Framed::class,
        GiftWrapped::class,
        Shipped::class,
    ];

    private const STATES_PRINTED_GLASS = [
        Initiated::class,
        Ordered::class,
        Printed::class,
        GiftWrapped::class,
        Shipped::class,
    ];

    private const ARTICLE_TYPES = [
        Article::TYPE_POSTER_FRAMED => self::STATES_POSTER_FRAMED,
        Article::TYPE_PRINTED_GLASS => self::STATES_PRINTED_GLASS,
    ];

    /**
     * @return array<int, class-string<StateInterface>>
     */
    public static function getStatesByType(string $articleType): array
    {
        return self::ARTICLE_TYPES[$articleType] ?? [];
    }
}
