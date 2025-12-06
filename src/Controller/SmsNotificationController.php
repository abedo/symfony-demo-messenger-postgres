<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Message\SmsNotification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

final class SmsNotificationController extends AbstractController
{
    #[Route('/sms/notification', name: 'app_sms_notification')]
    public function index(MessageBusInterface $bus): Response
    {
        // will cause the SmsNotificationHandler to be called
        $bus->dispatch(new SmsNotification('Look! I created a message!'));

        // Zwrócenie Response 200 OK z pustą treścią
        return new Response('', Response::HTTP_OK);
    }
}
