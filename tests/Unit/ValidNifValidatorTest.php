<?php

declare(strict_types=1);

namespace Yivoff\NifCheck\Test\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Yivoff\NifCheck\NifChecker;
use Yivoff\NifCheck\Validator\ValidNif;
use Yivoff\NifCheck\Validator\ValidNifValidator;

/**
 * @covers \Yivoff\NifCheck\Validator\ValidNifValidator
 *
 * @internal
 */
class ValidNifValidatorTest extends TestCase
{
    public function testInvalidConstraint(): void
    {
        $someConstraint = new NotBlank();
        $validator      = new ValidNifValidator(new NifChecker());
        $fakeNif        = 'whatever';

        $this->expectException(UnexpectedTypeException::class);
        $validator->validate($fakeNif, $someConstraint);
    }

    public function testIgnoresBlanksOrNulls(): void
    {
        $validator = $this->createPassingValidator();
        $validator->validate(null, $this->createMock(ValidNif::class));
    }

    public function testValidationOnlyWithStrings(): void
    {
        $nifConstraint = $this->createMock(ValidNif::class);
        $validator     = $this->createPassingValidator();

        $this->expectException(UnexpectedValueException::class);
        $validator->validate(true, $nifConstraint);

        $this->expectException(UnexpectedValueException::class);
        $validator->validate(9091928, $nifConstraint);
    }

    public function testInvalidStringBuildsViolation(): void
    {
        $validator = $this->createFailingValidator();
        $validator->validate('INVALID_NIF', $this->createMock(ValidNif::class));
    }

    public function testValidNifDoesNotBuildViolation(): void
    {
        $validator = $this->createPassingValidator();
        $validator->validate('VALID_NIF', $this->createMock(ValidNif::class));
    }

    private function createPassingValidator(): ConstraintValidator
    {
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        $executionContextMock
            ->expects($this->never())->method('buildViolation');

        $checkerMock = $this->createMock(NifChecker::class);
        $checkerMock->method('verify')->willReturn(true);

        $validator = new ValidNifValidator($checkerMock);
        $validator->initialize($executionContextMock);

        return $validator;
    }

    private function createFailingValidator(): ConstraintValidator
    {
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        $executionContextMock
            ->expects($this->once())->method('buildViolation');

        $checkerMock = $this->createMock(NifChecker::class);
        $checkerMock->method('verify')->willReturn(false);

        $validator = new ValidNifValidator($checkerMock);
        $validator->initialize($executionContextMock);

        return $validator;
    }
}
