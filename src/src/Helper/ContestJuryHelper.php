<?php

declare(strict_types=1);

namespace App\Helper;

use App\Exception\Sudoku\ContestJuryClientException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ContestJuryHelper
{
    private const PATH_PLANNING_INFO = '/admin/planning/info';

    public function __construct(private HttpClientInterface $contestJuryClient) {}

    public function getPlanningInfo(string $contestJuryWebsiteCode, \DateTimeImmutable $date): array
    {
        try {
            $response = $this->contestJuryClient->request('GET', self::PATH_PLANNING_INFO, [
                'query' => [
                    'centre' => $contestJuryWebsiteCode,
                    'date' => $date->format('Y-m-d'),
                ],
                'headers' => [
                    'Content-Type: application/json',
                    'Accept: application/json',
                ],
            ]);
        } catch (ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $e) {
            throw new ContestJuryClientException('Le service juryconcours ne réponds pas, le sudoku ne peut pas être initialisé', 500, $e);
        }

        return $response->toArray();
    }
}