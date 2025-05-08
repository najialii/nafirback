<?php

use Illuminate\Support\Facades\Log;

function clog($data, $label = null)
{
    $output = $label ? "$label: " . print_r($data, true) : print_r($data, true);
    Log::info($output);
}
