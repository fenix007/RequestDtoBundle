<?php

declare(strict_types=1);

namespace Nelexa\RequestDtoBundle\Normalizer;

use Nelexa\RequestDtoBundle\Exception\RequestDtoValidationException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ConstraintViolationListNormalizer;
use Symfony\Component\Serializer\Normalizer\ProblemNormalizer;
use Symfony\Component\Validator\ConstraintViolation;

class RequestDtoExceptionNormalizer extends ProblemNormalizer
{
    private ConstraintViolationListNormalizer $normalizer;

    private bool $debug;

    public function __construct(
        ConstraintViolationListNormalizer $normalizer,
        bool $debug = false,
        array $defaultContext = []
    ) {
        parent::__construct($debug, $defaultContext);
        $this->normalizer = $normalizer;
        $this->debug = $debug;
    }

    /**
     * @param RequestDtoValidationException $exception
     *
     * @throws ExceptionInterface
     *
     * @return array|\ArrayObject|bool|float|int|string|null
     */
    public function normalize($exception, ?string $format = null, array $context = [])
    {
        $data   = [];
        $data['status'] = $exception->getStatusCode();

        $errors = $exception->getErrors();

        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $data['errors'][$error->getPropertyPath()] = $error->getMessage();
        }

        return $data;
    }

    public function supportsNormalization($data, ?string $format = null): bool
    {
        return $data instanceof RequestDtoValidationException;
    }
}
