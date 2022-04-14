<?php

namespace App\Form;

use Symfony\Component\Validator\Context\ExecutionContextInterface;

class PhotosPathValidator
{
    public static function validate($object, ExecutionContextInterface $context, $payload)
    {
        if (!is_dir($object)) {
            $context->buildViolation('This folder does not exist!')
                ->atPath('photosPath')
                ->addViolation()
            ;
        }
    }
}
