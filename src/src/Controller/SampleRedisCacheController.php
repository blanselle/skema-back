<?php

namespace App\Controller;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_ADMIN')]
class SampleRedisCacheController extends AbstractController
{
    #[Route('/admin/redis/cache', name: 'app_redis_cache')]
    public function index(CacheInterface $cache): Response
    {
    
        $time = $cache->get('time',
            function(){
                return 17;
            }
        );
        return $this->json(['time' => $time], 200);
    }
}
