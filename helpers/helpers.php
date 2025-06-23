<?php

use Carbon\Carbon;

function serializeDate(?Carbon $date = null): string|null
{
    $timezone = new DateTimeZone('America/Sao_Paulo');

    return $date?->setTimezone($timezone)->format('Y-m-d H:i:s');
}
