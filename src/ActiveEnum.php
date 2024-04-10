<?php

namespace Blrf\Bookstore;

/**
 * Example Enum assigned to Publisher as publisher_active field.
 */
enum ActiveEnum: string
{
    case YES = 'Y';
    case NO = 'N';
}
