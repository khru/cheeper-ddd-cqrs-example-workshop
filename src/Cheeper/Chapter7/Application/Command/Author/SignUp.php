<?php

declare(strict_types=1);

namespace Cheeper\Chapter7\Application\Command\Author;

use Cheeper\Chapter7\Application\MessageTrait;

//snippet sign-up
final class SignUp
{
    //ignore
    use MessageTrait;
    //end-ignore

    public function __construct(
        private string $authorId,
        private string $userName,
        private string $email,
        private ?string $name = null,
        private ?string $biography = null,
        private ?string $location = null,
        private ?string $website = null,
        private ?string $birthDate = null,
    ) {
        $this->stampAsNewMessage();
    }

    //ignore
    public function authorId(): string
    {
        return $this->authorId;
    }

    public function userName(): string
    {
        return $this->userName;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function name(): ?string
    {
        return $this->name;
    }

    public function biography(): ?string
    {
        return $this->biography;
    }

    public function location(): ?string
    {
        return $this->location;
    }

    public function website(): ?string
    {
        return $this->website;
    }

    public function birthDate(): ?string
    {
        return $this->birthDate;
    }

    public static function fromArray(array $array): self
    {
        return new self(
            $array['author_id'] ?? '',
            $array['username'] ?? '',
            $array['email'] ?? '',
        );
    }
    //end-ignore
}
//end-snippet
