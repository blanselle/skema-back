<?php

namespace App\Form\Admin\User\AdministrativeRecord;

use App\Constants\Media\MediaWorflowStateConstants;
use App\Entity\Media;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\AbstractType;

class AbstractFunctionsType extends AbstractType
{
    protected function isMediaValid(Collection $medias, int $count): bool
    {
        $flg = 0;
        /** @var Media $media */
        foreach ($medias as $media) {
            if ($media->getState() === MediaWorflowStateConstants::STATE_ACCEPTED) {
                $flg++;
            }
        }
        if ($flg === $count) {
            return true;
        }

        return false;
    }
}