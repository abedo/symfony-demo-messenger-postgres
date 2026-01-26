<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Webhook;

use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcher\IsJsonRequestMatcher;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Symfony\Component\Webhook\Client\AbstractRequestParser;
use Symfony\Component\Webhook\Exception\RejectWebhookException;

final class PleoRequestParser extends AbstractRequestParser
{
    protected function getRequestMatcher(): RequestMatcherInterface
    {
        return new IsJsonRequestMatcher();
    }

    /**
     * @throws JsonException
     */
    protected function doParse(Request $request, #[\SensitiveParameter] string $secret): ?RemoteEvent
    {

        // Tutaj weryfikujesz podpis (np. X-Hub-Signature) używając $secret
        // Jeśli coś jest nie tak: throw new RejectWebhookException(406, 'Błędny podpis');

        $payload = $request->toArray();

        // Tworzymy zdarzenie: nazwa, ID zdarzenia, dane
        return new RemoteEvent('v1.export-job.created', $payload['payload']['data']['id'], $payload);
    }
}
