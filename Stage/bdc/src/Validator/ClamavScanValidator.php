<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ClamavScanValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ClamavScan) {
            throw new UnexpectedTypeException($constraint, ClamavScan::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value || '' === $value) {
            return;
        }

        // On règle les pbs de permissions
        $file = $value->getRealPath();
        $perm = fileperms($file) | 0644;
        chmod($file, $perm);

        // Scan du fichier
        $socket = (new \Socket\Raw\Factory())->createClient('unix:///var/run/clamav/clamd.ctl');
        $quahog = new \Xenolope\Quahog\Client($socket);
        $result = $quahog->scanFile($file);

        if ($result->hasFailed()) {
            throw new \RuntimeException($result->getReason());
        } else if (!$result->isFound()) {
            return;
        }

        // the argument must be a string or an object implementing __toString()
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ string }}', $value)
            ->addViolation();
    }
}