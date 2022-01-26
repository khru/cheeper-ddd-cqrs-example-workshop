<?php

declare(strict_types=1);

namespace Cheeper\Chapter7\Infrastructure\Application\Event\Author;

use Cheeper\Chapter7\Application\Event\Author\AuthorFollowedEventHandler;
use Cheeper\Chapter7\DomainModel\Follow\AuthorFollowed;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

//snippet symfony-author-followed-event-handler
final class SymfonyAuthorFollowedHandler implements MessageSubscriberInterface
{
    public function __construct(
        private AuthorFollowedEventHandler $eventHandler
    ) {
    }

    public function handle(AuthorFollowed $event): void
    {
        $this->eventHandler->handle($event);
    }

    public static function getHandledMessages(): iterable
    {
        yield AuthorFollowed::class => [
            'method' => 'handle',
            'from_transport' => 'chapter7_events',
        ];
    }
}
//end-snippet
