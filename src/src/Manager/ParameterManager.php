<?php

declare(strict_types=1);

namespace App\Manager;

use App\Constants\Parameters\ParametersKeyTypeConstants;
use App\Entity\Parameter\Parameter;
use App\Entity\ProgramChannel;
use App\Exception\Parameter\ParameterNotFoundException;
use App\Repository\Parameter\ParameterRepository;

class ParameterManager
{
    public function __construct(private ParameterRepository $parameterRepository) {}

    public function getParameter(string $key, ?ProgramChannel $programChannel = null) : Parameter
    {
        $parameter = null;
        if(null === $programChannel) {
            $parameter = $this->parameterRepository->findOneParameterByKeyName($key);
        } else {
            $parameter = $this->parameterRepository->findOneParameterByKeyNameAndProgramChannel($key, $programChannel);
            $parameter = $this->rewriteValue($parameter);
        }

        if(null === $parameter) {
            throw new ParameterNotFoundException($key);
        }

        return $parameter;
    }

    private function rewriteValue(Parameter $parameter): Parameter
    {
        if ($parameter->getKey()->getType() === ParametersKeyTypeConstants::DATE) {
            $parameter->setValue($parameter->getValueDateTime());
        }

        if ($parameter->getKey()->getType() === ParametersKeyTypeConstants::TEXT) {
            $parameter->setValue($parameter->getValueString());
        }

        if ($parameter->getKey()->getType() === ParametersKeyTypeConstants::NUMBER) {
            $parameter->setValue($parameter->getValueNumber());
        }
        return $parameter;
    }
}
