<?php

namespace App\Helper;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CalendrierGouvHelper
{
    private const PATH = 'metropole/{year}.json';
    public function __construct(
        private HttpClientInterface $apiCalendrierGouvClient,
        private LoggerInterface $logger
    ) {}

    /**
     * @description Get public holidays
     *  "{\"2022-01-01\":\"1erjanvier\",\"2022-04-18\":\"LundidePâques\",\"2022-05-01\":\"1ermai\",\"2022-05-08\":\"8mai\",\"2022-05-26\":\"Ascension\",\"2022-06-06\":\"LundidePentecôte\",\"2022-07-14\":\"14juillet\",\"2022-08-15\":\"Assomption\",\"2022-11-01\":\"Toussaint\",\"2022-11-11\":\"11novembre\",\"2022-12-25\":\"JourdeNoël\"}"
     */
    public function getPublicHolidays(string $year): array
    {
        try {
            $response = $this->apiCalendrierGouvClient->request('GET', str_replace('{year}', $year, self::PATH), [
                'headers' => [
                    'Content-Type: application/json',
                    'Accept: application/json',
                ],
            ]);

            return $response->toArray();
        } catch (ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $e) {
            $this->logger->error(message: sprintf('Cannot get public holidays for year %s', $year));
        }

        return [];
    }
}