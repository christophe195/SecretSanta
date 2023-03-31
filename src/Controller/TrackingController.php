<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Participant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class TrackingController extends AbstractController
{
    /**
     * @Route("email/{participantUrl}.gif", name="mailopen_tracker", methods={"GET"})
     */
    public function trackEmailAction(EntityManagerInterface $em, EventDispatcherInterface $dispatcher, $participantUrl)
    {
        $dispatcher->addListener(KernelEvents::TERMINATE,
            function (KernelEvent $event) use ($em, $participantUrl) {
                /** @var Participant $participant */
                $participant = $em->getRepository(Participant::class)->findOneByUrl($participantUrl);
                if ($participant != null) {
                    $participant->setOpenEmailDate(new \DateTime());
                    $em->persist($participant);
                    $em->flush();
                }
            }
        );

        $response = Response::create();
        $response->setContent(base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw=='));
        $response->headers->set('Content-Type', 'image/gif');
        $response->headers->addCacheControlDirective('no-cache', true);
        $response->headers->addCacheControlDirective('must-revalidate', true);
     //   $response->setPrivate();

        return $response;
    }
}
