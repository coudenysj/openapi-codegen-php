<?php
/**
 * This file is part of the Elastic OpenAPI PHP code generator.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swiftype\Connection\Handler;

use Swiftype\Exception\ConnectionException;
use Swiftype\Exception\CouldNotConnectToHostException;
use Swiftype\Exception\CouldNotResolveHostException;
use Swiftype\Exception\OperationTimeoutException;

/**
 * This handler manage connections errors and throw comprehensive exceptions to the user.
 *
 * @package Swiftype\Connection\Handler
 * @author  Aurélien FOUCRET <aurelien.foucret@elastic.co>
 */
class ConnectionErrorHandler
{
    /**
     * @var callable
     */
    private $handler;


    /**
     * @var \GuzzleHttp\Ring\Core
     */
    private $ringUtils;

    /**
     * Constructor.
     *
     * @param callable $handler original handler
     */
    public function __construct(callable $handler)
    {
        $this->handler   = $handler;
        $this->ringUtils = new \GuzzleHttp\Ring\Core();
    }

    /**
     * Proxy the response and throw an exception if a connection error is detected.
     *
     * @param array $request request
     *
     * @return array
     */
    public function __invoke($request)
    {
        $handler = $this->handler;
        $response = $this->ringUtils->proxy($handler($request), function ($response) {
            if (true === isset($response['error'])) {
                throw $this->getConnectionErrorException($response);
            }

            return $response;
        });

        return $response;
    }

    /**
     * Process error to raised a more comprehensive exception.
     *
     * @param array $request  request
     * @param array $response response
     *
     * @return ConnectionException
     */
    private function getConnectionErrorException($response)
    {
        $exception = null;
        $message = $response['error']->getMessage();

        $exception = new ConnectionException($message);

        if (isset($response['curl']['errno'])) {
            switch ($response['curl']['errno']) {
                case CURLE_COULDNT_RESOLVE_HOST:
                    $exception = new CouldNotResolveHostException($message, null, $response['error']);
                    break;
                case CURLE_COULDNT_CONNECT:
                    $exception = new CouldNotConnectToHostException($message, null, $response['error']);
                    break;
                case CURLE_OPERATION_TIMEOUTED:
                    $exception = new OperationTimeoutException($message, null, $response['error']);
                    break;
            }
        }

        return $exception;
    }
}
