<?php
/**
 * Copyright (c) Andreas Heigl<andreas@heigl.org>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author    Andreas Heigl<andreas@heigl.org>
 * @copyright Andreas Heigl
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @since     01.03.2017
 * @link      http://github.com/heiglandreas/org.heigl.WebHookHandler
 */

namespace Org_Heigl\WebHookHandler;

use Http\Client\HttpAsyncClient;
use Http\Message\RequestFactory;
use Monolog\Handler\AbstractProcessingHandler;
use Psr\Http\Message\UriInterface;

class WebHookHandler extends AbstractProcessingHandler
{
    /** @var RequestFactory  */
    private $requestFactory;

    /** @var UriInterface  */
    private $uri;

    /** @var HttpAsyncClient  */
    private $asyncClient;

    /** @var string */
    private $from = '';

    /**
     * WebHookHandler constructor.
     *
     * @param \Psr\Http\Message\UriInterface $uri
     * @param int                            $level
     * @param \Http\Client\HttpAsyncClient   $asyncClient
     * @param \Http\Message\RequestFactory   $requestFactory
     */
    public function __construct(
        UriInterface $uri,
        int $level,
        HttpAsyncClient $asyncClient,
        RequestFactory $requestFactory
    ) {
        $this->requestFactory = $requestFactory;
        $this->asyncClient = $asyncClient;
        $this->uri = $uri;
        $this->level = $level;
    }

    public function setFrom(string $from)
    {
        $this->from = $from;
    }
    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param  array $record
     *
     * @return void
     */
    protected function write(array $record)
    {
        $body = json_encode(array_merge(
            ['from' => $this->from],
            $record
        ));

        $request = $this->requestFactory->createRequest(
            'POST',
            (string) $this->uri,
            ['Content-Type' => 'application/json'],
            $body
        );

        $promise = $this->asyncClient->sendAsyncRequest($request);
        // As sending asynchronous doesn't really work we send it synchronously…
        $promise->wait();
    }
}
