<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Bloc\Bloc;
use Doctrine\ORM\EntityManagerInterface;

class Utils
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Get the content of error bloc and adds the params
     *
     * The params in bloc content have to be on the format "Hello {name-of-parameter} world"
     *
     * @param string $key name of the bloc
     * @param array $params parameter to inject in the content of the bloc [name => value, ...]
     * @return string|null
     */
    public function getMessageByKey(string $key, array $params = []): ?string
    {
        $bloc = $this->em->getRepository(Bloc::class)->findOneBy(['key' => $key]);

        if (null === $bloc) {
            return null;
        }

        $content = $bloc->getContent();

        if (!empty($content)) {
            foreach ($params as $name => $value) {
                $content = str_replace(sprintf('{%s}', $name), $value, $content);
            }
        }

        return $content;
    }
}
