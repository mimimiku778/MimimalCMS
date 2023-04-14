<?php

/**
 * Exception thrown when a requested resource is not found.
 */
class NotFoundException extends RuntimeException
{
}

/**
 * Exception thrown when a client has made too many requests within a certain amount of time.
 */
class ThrottleRequestsException extends RuntimeException
{
}

/**
 * Exception thrown when a request is malformed or invalid.
 */
class BadRequestException extends RuntimeException
{
}

/**
 * Exception thrown when a requested HTTP method is not allowed for the requested resource.
 */
class MethodNotAllowedException extends RuntimeException
{
}

/**
 * Exception thrown when input validation fails.
 */
class ValidationException extends RuntimeException
{
}

/**
 * Exception thrown when the input data is invalid or does not match the expected format.
 */
class InvalidInputException extends RuntimeException
{
}

/**
 * Exception thrown when there is an error in the integrity of data, such as violating a unique constraint or foreign key constraint.
 */
class DataIntegrityViolationException extends RuntimeException
{
}

/**
 * Exception thrown when a session has timed out or is no longer valid.
 */
class SessionTimeoutException extends RuntimeException
{
}

/**
 * Exception used for testing purposes only.
 */
class TestException extends LogicException
{
}

/**
 * Exception thrown when authentication credentials are missing or invalid.
 */
class UnauthorizedException extends RuntimeException
{
}
