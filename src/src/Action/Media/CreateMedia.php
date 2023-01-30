<?php

declare(strict_types=1);

namespace App\Action\Media;

use App\Entity\Media;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsController]
final class CreateMedia extends AbstractController
{
    public function __invoke(Request $request): Media
    {
        $uploadedFile = $request->files->get('formFile');

        if (null === $uploadedFile) {
            throw new BadRequestHttpException('"file" is required');
        }

        $media = new Media();
        $media->setFormFile($uploadedFile);

        /** @var User */
        $user = $this->getUser();
        $media->setStudent($user->getStudent());

        $code = $request->request->get('code');
        if (null !== $code) {
            $media->setCode(strval($request->request->get('code')));
        }

        $type = $request->request->get('type');
        if (null !== $type) {
            $media->setType(strval($request->request->get('type')));
        }

        return $media;
    }
}
