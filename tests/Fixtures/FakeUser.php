<?php

declare(strict_types=1);

namespace Yivoff\NifCheck\Test\Fixtures;

use Symfony\Component\Validator\Constraints\NotBlank;
use Yivoff\NifCheck\Validator\ValidNif;

class FakeUser
{
    public function __construct(
        #[ValidNif]
        #[NotBlank]
        public string $nif
    ) {
    }
}
