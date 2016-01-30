<?php
/**
 * OAuth 2.0 Invalid Client Reference Exception
 *
 * @package     
 * @author      
 * @copyright   
 * @license     http://mit-license.org/
 * @link        
 */

namespace App\Exceptions;

use League\OAuth2\Server\Exception\OAuthException;

/**
 * Exception class
 */
class InvalidClientReferenceException extends OAuthException
{
    /**
     * {@inheritdoc}
     */
    public $httpStatusCode = 401;

    /**
     * {@inheritdoc}
     */
    public $errorType = 'invalid_client';

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct('Client authentication failed. Reference URL not found');
    }
}
