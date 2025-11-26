<?php

declare(strict_types=1);

namespace FredikaPutra\AsyncLogger\Enums;

enum OperationType: string
{
    case CREATE = 'create';
    case READ = 'read';
    case UPDATE = 'update';
    case DELETE = 'delete';
}