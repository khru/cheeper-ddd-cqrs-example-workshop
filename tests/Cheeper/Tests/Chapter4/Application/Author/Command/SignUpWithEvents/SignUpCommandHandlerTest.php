<?php

declare(strict_types=1);

namespace Cheeper\Tests\Chapter4\Application\Author\Command\SignUpWithEvents;

use Cheeper\AllChapters\DomainModel\Author\AuthorAlreadyExists;
use Cheeper\AllChapters\DomainModel\Author\UserName;
use Cheeper\AllChapters\DomainModel\Clock;
use Cheeper\AllChapters\DomainModel\Clock\DateCollectionClockStrategy;
use Cheeper\Chapter4\Application\Author\Command\SignUpWithEvents\SignUpCommandHandler;
use Cheeper\Chapter4\Application\Author\Command\SignUpWithoutEvents\SignUpCommand;
use Cheeper\Chapter4\DomainModel\Author\NewAuthorSigned;
use Cheeper\Chapter4\Infrastructure\Application\InMemoryEventBus;
use Cheeper\Chapter4\Infrastructure\DomainModel\Author\InMemoryAuthorRepository;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use function Functional\first;

final class SignUpCommandHandlerTest extends TestCase
{
    private InMemoryAuthorRepository $authorRepository;
    private InMemoryEventBus $eventBus;
    private DateTimeImmutable $today;

    protected function setUp(): void
    {
        $this->authorRepository = new InMemoryAuthorRepository();
        $this->eventBus = new InMemoryEventBus();
        $this->today = $this->getToday();
        Clock::instance()->changeStrategy(
            new DateCollectionClockStrategy([$this->today])
        );
    }

    protected function tearDown(): void
    {
        Clock::instance()->changeStrategy(
            new Clock\DefaultClockStrategy()
        );
    }

    /** @test */
    public function givenAUserNameThatAlreadyBelongsToAnExistingUserWhenSignUpThenAnExceptionShouldBeThrown(): void
    {
        $this->expectException(AuthorAlreadyExists::class);
        $this->expectExceptionMessage('Author with name "johndoe" already exists');

        $eventBus = new InMemoryEventBus();

        $signUpHandler = new SignUpCommandHandler(
            $this->authorRepository,
            $this->eventBus
        );

        $signUpHandler(
            new SignUpCommand(
                Uuid::uuid4()->toString(),
                'johndoe',
                'johndoe@example.com',
                'John Doe',
                'The usual profile example',
                'Madrid',
                'https://example.com/',
                (new \DateTimeImmutable())->format('Y-m-d')
            )
        );

        $eventBus->reset();

        $signUpHandler(
            new SignUpCommand(
                Uuid::uuid4()->toString(),
                'johndoe',
                'johndoe@example.com',
                'John Doe',
                'The usual profile example',
                'Madrid',
                'https://example.com/',
                (new \DateTimeImmutable())->format('Y-m-d')
            )
        );
    }

    /** @test */
    public function givenValidUserDataWhenSignUpWithOnlyMandatoryFieldsThenAValidUserShouldBeCreated(): void
    {
        $signUpHandler = new SignUpCommandHandler(
            $this->authorRepository,
            $this->eventBus
        );

        $userName = 'johndoe';
        $email = 'johndoe@example.com';
        $signUpHandler(
            new SignUpCommand(
                Uuid::uuid4()->toString(),
                $userName,
                $email,
            )
        );

        $actualAuthor = $this->authorRepository->ofUserName(UserName::pick($userName));
        $this->assertNotNull($actualAuthor);
        $this->assertSame($userName, $actualAuthor->userName()->userName());
        $this->assertSame($email, $actualAuthor->email()->value());
        $this->assertNull($actualAuthor->name());
        $this->assertNull($actualAuthor->biography());
        $this->assertNull($actualAuthor->location());
        $this->assertNull($actualAuthor->website());
        $this->assertNull($actualAuthor->birthDate());

        $events = $this->eventBus->events();

        /** @var NewAuthorSigned $firstEvent */
        $firstEvent = first($events);
        $this->assertCount(1, $events);
        $this->assertSame(NewAuthorSigned::class, $firstEvent::class);
        $this->assertSame($actualAuthor->authorId()->toString(), $firstEvent->authorId());
        $this->assertSame($this->today, $firstEvent->occurredOn());
    }

    /** @test */
    public function givenValidUserDataWhenSignUpWithAllFieldsThenAValidUserShouldBeCreated(): void
    {
        $signUpHandler = new SignUpCommandHandler(
            $this->authorRepository,
            $this->eventBus
        );

        $userName = 'johndoe';
        $email = 'johndoe@example.com';
        $name = 'John Doe';
        $biography = 'The usual profile example';
        $location = 'Madrid';
        $website = 'https://example.com/';
        $birthDate = (new \DateTimeImmutable())->format('Y-m-d');

        $signUpHandler(
            new SignUpCommand(
                Uuid::uuid4()->toString(),
                $userName,
                $email,
                $name,
                $biography,
                $location,
                $website,
                $birthDate
            )
        );

        $actualAuthor = $this->authorRepository->ofUserName(UserName::pick($userName));
        $this->assertNotNull($actualAuthor);
        $this->assertSame($userName, $actualAuthor->userName()->userName());
        $this->assertSame($email, $actualAuthor->email()->value());
        $this->assertSame($name, $actualAuthor->name());
        $this->assertSame($biography, $actualAuthor->biography());
        $this->assertSame($location, $actualAuthor->location());
        $this->assertSame($website, $actualAuthor->website()->toString());
        $this->assertSame($birthDate, $actualAuthor->birthDate()->date()->format('Y-m-d'));

        $events = $this->eventBus->events();
        $this->assertCount(1, $events);
        $this->assertSame(NewAuthorSigned::class, $events[0]::class);
    }

    /** @test */
    public function givenInvalidEmailUserDataWhenSignUpThenAnExceptionShouldBeThrown(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email not-a-valid-email');

        $signUpHandler = new SignUpCommandHandler(
            $this->authorRepository,
            $this->eventBus
        );

        $userName = 'johndoe';
        $email = 'not-a-valid-email';
        $name = 'John Doe';
        $biography = 'The usual profile example';
        $location = 'Madrid';
        $website = 'https://example.com/';
        $birthDate = (new \DateTimeImmutable())->format('Y-m-d');

        $signUpHandler(
            new SignUpCommand(
                Uuid::uuid4()->toString(),
                $userName,
                $email,
                $name,
                $biography,
                $location,
                $website,
                $birthDate
            )
        );
    }

    /** @test */
    public function givenInvalidWebsiteUserDataWhenSignUpThenAnExceptionShouldBeThrown(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid URL given');

        $signUpHandler = new SignUpCommandHandler(
            $this->authorRepository,
            $this->eventBus
        );

        $userName = 'johndoe';
        $email = 'carlos.buenosvinos@gmail.com';
        $name = 'John Doe';
        $biography = 'The usual profile example';
        $location = 'Madrid';
        $website = 'not-a-valid-website';
        $birthDate = (new \DateTimeImmutable())->format('Y-m-d');

        $signUpHandler(
            new SignUpCommand(
                Uuid::uuid4()->toString(),
                $userName,
                $email,
                $name,
                $biography,
                $location,
                $website,
                $birthDate
            )
        );
    }

    /** @test */
    public function givenAnAuthorIdThatAlreadyBelongsToAnExistingUserWhenSignUpThenAnExceptionShouldBeThrown(): void
    {
        $this->expectException(AuthorAlreadyExists::class);
        $this->expectExceptionMessage('Author with id "0c57e704-3982-4c90-9f3f-e00d5ea546ac" already exists');

        $signUpHandler = new SignUpCommandHandler(
            $this->authorRepository,
            $this->eventBus
        );

        $authorId = '0c57e704-3982-4c90-9f3f-e00d5ea546ac';
        $signUpHandler(
            new SignUpCommand(
                $authorId,
                'johndoe',
                'johndoe@example.com',
                'John Doe',
                'The usual profile example',
                'Madrid',
                'https://example.com/',
                (new DateTimeImmutable())->format('Y-m-d')
            )
        );

        $signUpHandler(
            new SignUpCommand(
                $authorId,
                'new_johndoe',
                'johndoe@example.com',
                'John Doe',
                'The usual profile example',
                'Madrid',
                'https://example.com/',
                (new DateTimeImmutable())->format('Y-m-d')
            )
        );
    }

    protected function getToday(): DateTimeImmutable
    {
        return new DateTimeImmutable(
            'now', new \DateTimeZone('UTC')
        );
    }
}
