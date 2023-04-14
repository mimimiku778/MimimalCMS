<?php

namespace Shadow\Config;

class ExceptionHandlerConfig
{
    // Display exceptions
    const EXCEPTION_HANDLER_DISPLAY_ERROR_TRACE_DETAILS = true;
    const EXCEPTION_HANDLER_DISPLAY_BEFORE_OB_CLEAN = true;

    // Exceptions Log directory.
    const EXCEPTION_LOG_DIRECTORY = __DIR__ . '/../shared/exception.log';

    /**
     * The path to hide from exception error trace.
     * This constant is used to remove the unnecessary path from the beginning of
     * the path included in exception error trace.
     */
    const ERROR_PAGE_HIDE_DRECTORY = '/var/www/mimikyu.info';

    /**
     * This constant is used to specify the document root path name.
     * The path name after this constant is concatenated with the GitHub URL.
     */
    const ERROR_PAGE_DOCUMENT_ROOT_NAME = 'mimikyu.info';

    /**
     * This constant is used to specify the GitHub URL for displaying the source code in the exception error trace.
     * The path name after the DOCUMENT_ROOT_NAME constant is concatenated with this URL.
     */
    const ERROR_PAGE_GITHUB_URL = 'https://github.com/mimimiku778/MimimalCMS-v0.1/blob/master/';
}
