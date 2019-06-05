<?php
/**
 * This file is part of the Elastic OpenAPI PHP code generator.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swiftype\Exception;

/**
 * Exception raised when the client can not connect to the host specified.
 *
 * @package Swiftype\Exception
 * @author  Aurélien FOUCRET <aurelien.foucret@elastic.co>
 */
class CouldNotConnectToHostException extends ConnectionException implements SwiftypeException
{
}
