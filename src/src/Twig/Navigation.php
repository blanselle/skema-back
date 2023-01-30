<?php

declare(strict_types=1);

namespace App\Twig;

use App\Constants\Navigation\NavigationConstants;
use App\Entity\User;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Yaml\Parser;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Navigation extends AbstractExtension
{
    public function __construct(
        private Security $security,
        private RequestStack $requestStack
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('navigation', [$this, 'navigation']),
        ];
    }

    public function navigation(): array
    {
        $navigation = $this->getConfigFile();

        return $this->rewriteNavigation($navigation);
    }

    public function rewriteNavigation(array $navigation): array
    {
        $currentRoute = $this->requestStack->getCurrentRequest()->get('_route');
        foreach ($navigation['categories'] as $categoryName => $category) {
            $navigation['categories'][$categoryName]['active'] = false;

            // Restriction de la category
            if (!$this->testCurrentUserIsGranted($category['role'] ?? null)) {
                unset($navigation['categories'][$categoryName]);
                continue;
            }

            foreach ($category['pages'] as $route => $page) {
                $navigation['categories'][$categoryName]['pages'][$route]['active'] = false;

                // Restriction de la page
                if (!$this->testCurrentUserIsGranted($page['role'] ?? null)) {
                    unset($navigation['categories'][$categoryName]['pages'][$route]);
                    continue;
                }

                // Gestion des pages et category active
                if ($currentRoute == $route) {
                    $navigation['categories'][$categoryName]['pages'][$route]['active'] = true;
                    $navigation['categories'][$categoryName]['active'] = true;
                }
            }
        }

        return $navigation;
    }

    private function testCurrentUserIsGranted(array|string|null $role): bool
    {
        if (is_null($role)) { // Pas de role
            return true;
        }

        /** @var User|null */
        $user = $this->security->getUser();

        if (null === $user) {
            return false;
        }

        return $this->security->isGranted($role, $user);
    }

    /**
     * @codeCoverageIgnore
     */
    private function getConfigFile(): array
    {
        $content = file_get_contents(NavigationConstants::CONFIG_FILE);
        if (false === $content) {
            throw new FileNotFoundException();
        }

        return (new Parser())->parse($content);
    }
}
