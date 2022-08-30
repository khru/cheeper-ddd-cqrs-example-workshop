<?php

declare(strict_types=1);

namespace Cheeper\DomainModel\Follow;

use Cheeper\DomainModel\Author\AuthorId;

/** @final */
class Follow
{
    /**
     * @psalm-param non-empty-string $followId
     * @psalm-param non-empty-string $fromAuthorId
     * @psalm-param non-empty-string $toAuthorId
     */
    private function __construct(
        private string $followId,
        private string $fromAuthorId,
        private string $toAuthorId,
    ) {
    }

    public static function fromAuthorToAuthor(
        FollowId $followId,
        AuthorId $fromAuthorId,
        AuthorId $toAuthorId,
    ): self {
        return new self(
            followId: $followId->id,
            fromAuthorId: $fromAuthorId->id,
            toAuthorId: $toAuthorId->id,
        );
    }

    public function fromAuthorId(): AuthorId
    {
        return AuthorId::fromString($this->fromAuthorId);
    }

    public function toAuthorId(): AuthorId
    {
        return AuthorId::fromString($this->toAuthorId);
    }

    public function followId(): FollowId
    {
        return FollowId::fromString($this->followId);
    }
}
