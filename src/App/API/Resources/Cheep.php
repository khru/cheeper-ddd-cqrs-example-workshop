<?php

declare(strict_types=1);

namespace App\API\Resources;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Ramsey\Uuid\UuidInterface;

//snippet cheep-input-resource
#[ApiResource(collectionOperations: ['post'], itemOperations: ['get'])]
final class Cheep
{
    #[ApiProperty(identifier: true)]
    public ?UuidInterface $id = null;

    /** @var string  */
    public string $authorId;

    /** @var string */
    public string $message;
}
//end-snippet
