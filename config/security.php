<?php

return [
    'admin_ips' => array_filter(explode(',', (string) env('ADMIN_IP_ALLOWLIST'))),
];
