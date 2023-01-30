<?php

declare(strict_types=1);

namespace App\Service\Bloc;

use App\Entity\Bloc\Bloc;
use App\Entity\ProgramChannel;
use App\Exception\Bloc\BlocNotFoundException;
use App\Exception\Parameter\ParameterNotFoundException;
use App\Manager\ParameterManager;
use App\Repository\BlocRepository;

/**
 * Replace argument in the content and the label of bloc
 */
class BlocRewriter
{
    private const VAR_DELIMITER = '%';
    private const DATE_FORMAT = 'd MMMM y à HH:mm';
    private const UNDEFINED_PARAMETER = 'undefined';
    private const PREFIX_PARAMETER = 'parameter';
    private const PREFIX_DEFAULT = 'default';

    public function __construct(
        private ParameterManager $parameterManager,
        private BlocRepository $blocRepository,
    ) {
    }

    public function rewriteBloc(Bloc|string $bloc, ?ProgramChannel $programChannel = null, array $params = []): Bloc
    {
        if(is_string($bloc)) {
            $bloc = $this->getBloc($bloc, $programChannel);
        }
        
        $rewriterBloc = clone $bloc;

        if (null !== $rewriterBloc->getLabel()) {
            $rewriterBloc->setLabel($this->rewriteString($bloc->getLabel(), $programChannel, $params));
        }
        if (!empty($rewriterBloc->getContent())) {
            $rewriterBloc->setContent($this->rewriteString($bloc->getContent(), $programChannel, $params));
        }

        return $rewriterBloc;
    }

    private function getBloc(string $key, ?ProgramChannel $programChannel): Bloc
    {
        $bloc = null;
        if(null !== $programChannel) {
            $bloc = $this->blocRepository->findActiveByKeyAndProgramChannel($key, $programChannel);
        } else {
            $bloc = $this->blocRepository->findOneBy(['key' => $key]);
        }
        
        if(null === $bloc) {
            throw new BlocNotFoundException($key);
        }

        return $bloc;
    }

    private function rewriteString(string $string, ?ProgramChannel $programChannel, array $params): string
    {
        $arguments = [self::PREFIX_DEFAULT => $params];
        $arguments[self::PREFIX_PARAMETER] = $this->getParametersFromKeys($this->extractVarsFromTextByType($string), $programChannel);

        return $this->replaceArgumentsInText($string, $arguments);
    }

    private function getParametersFromKeys(array $parameterKeys, ?ProgramChannel $programChannel): array
    {
        $parameters = [];
        foreach ($parameterKeys as $key) {
            
            try {
                $parameter = $this->parameterManager->getParameter($key, $programChannel)->getValue();
            } catch(ParameterNotFoundException $e) {
                $parameter = self::UNDEFINED_PARAMETER;
            }

            if ($parameter instanceof \DateTime) {
                $parameter = \IntlDateFormatter::formatObject(
                    $parameter,
                    self::DATE_FORMAT,
                );
            }

            $parameters[$key] = $parameter;
        }

        return $parameters;
    }

    /**
     * Recherche des arguments dans une chaine de caractères.
     *
     * @param string $text chaine en entrée ex: Bonjour %parameter.dateFinCv% %parameter.dateClotureInscriptions%
     * @param string $type type d'argument à récupérer ex: parameter
     * @return array liste des paramètres récupérés ex: [prenom, nom]
     */
    private function extractVarsFromTextByType(string $text, string $type = self::PREFIX_PARAMETER): array
    {
        $expressions = [];

        preg_match_all(
            sprintf("((?<=%s%s\.)(\w)+(?=%s))", self::VAR_DELIMITER, $type, self::VAR_DELIMITER),
            $text,
            $expressions,
        );

        return $expressions[0];
    }

    private function replaceArgumentsInText(string $text, array $arguments): string
    {
        foreach ($arguments as $type => $category) {
            foreach ($category as $key => $argument) {
                $text = str_replace(
                    $this->getArgumentWithDelimiter($type, $key),
                    $argument,
                    $text
                );
            }
        }

        return $text;
    }

    private function getArgumentWithDelimiter(string $type, string $key): string
    {
        return sprintf(
            "%s%s.%s%s",
            self::VAR_DELIMITER,
            $type,
            $key,
            self::VAR_DELIMITER,
        );
    }
}
