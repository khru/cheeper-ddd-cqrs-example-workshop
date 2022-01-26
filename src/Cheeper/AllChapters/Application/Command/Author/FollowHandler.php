<?php

declare(strict_types=1);

namespace Cheeper\AllChapters\Application\Command\Author;

use Cheeper\AllChapters\DomainModel\Author\AuthorDoesNotExist;
use Cheeper\AllChapters\DomainModel\Author\Authors;
use Cheeper\AllChapters\DomainModel\Author\UserName;
use Cheeper\AllChapters\DomainModel\Follow\Follow as FollowAggregate;
use Cheeper\AllChapters\DomainModel\Follow\FollowId;
use Cheeper\AllChapters\DomainModel\Follow\Follows;

final class FollowHandler
{
    public function __construct(
        private Authors $authors,
        private Follows $follows,
    ) {
    }

    public function __invoke(Follow $command): void
    {
        $followeeUserName = UserName::pick($command->followeeUsername());
        $followedUserName = UserName::pick($command->followedUsername());

        $author = $this->authors->ofUserName($followeeUserName);

        if (null === $author) {
            throw AuthorDoesNotExist::withUserNameOf($followeeUserName);
        }

        $followed = $this->authors->ofUserName($followedUserName);

        if (null === $followed) {
            throw AuthorDoesNotExist::withUserNameOf($followedUserName);
        }

        $follow = FollowAggregate::fromAuthorToAuthor(
            FollowId::nextIdentity(),
            $author->authorId(),
            $followed->authorId()
        );

        $this->follows->add($follow);
    }
}
