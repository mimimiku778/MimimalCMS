<?php

class NotFoundException extends RuntimeException
{
}

class ThrottleRequestsException extends RuntimeException
{
}

class BadRequestException extends RuntimeException
{
}

class MethodNotAllowedException extends RuntimeException
{
}

class ValidationException extends RuntimeException
{
}

class InvalidInputException extends RuntimeException
{
}

class DataIntegrityViolationException extends RuntimeException
{
}

class SessionTimeoutException extends RuntimeException
{
}

class TestException extends LogicException
{
}

class UnauthorizedException extends RuntimeException
{
}