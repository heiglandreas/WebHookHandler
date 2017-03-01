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
use Http\Discovery\HttpAsyncClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Discovery\StreamFactoryDiscovery;
use Http\Message\RequestFactory;
use Http\Message\StreamFactory;
use Monolog\Handler\AbstractProcessingHandler;
use Psr\Http\Message\UriInterface;

class WebHookHandler extends AbstractProcessingHandler
{
    private $requestFactory;

    private $streamFactory;

    /** @var UriInterface  */
    private $uri;

    private $asyncClient;

    /**
     * WebHookHandler constructor.
     *
     * @param \Psr\Http\Message\UriInterface    $uri
     * @param int                               $level
     * @param \Http\Client\HttpAsyncClient|null $asyncClient
     * @param \Http\Message\RequestFactory|null $requestFactory
     * @param \Http\Message\StreamFactory|null  $streamFactory
     */
    public function __construct(
        UriInterface $uri,
        int $level,
        HttpAsyncClient $asyncClient = null,
        RequestFactory $requestFactory = null,
        StreamFactory $streamFactory = null
    ) {
        $this->requestFactory = $requestFactory ?: MessageFactoryDiscovery::find();
        $this->streamFactory = $streamFactory ?: StreamFactoryDiscovery::find();
        $this->asyncClient = $asyncClient ?: HttpAsyncClientDiscovery::find();
        $this->uri = $uri;
        $this->level = $level;
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
        $request = $this->requestFactory->createRequest('POST', (string) $this->uri);
        $stream = $this->streamFactory->createStream(json_encode(array_merge(
            ['message' => $record['formatted']],
            $record
        )));

        $request = $request->withBody($stream);

        $this->asyncClient->sendAsyncRequest($request);
    }
}
