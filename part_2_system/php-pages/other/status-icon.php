<?php
function getStatusIconClass($status) {
    $icon_classes = [
        "ongoing" => "bx-refresh",
        "complete" => "bx-check-circle",
        "paused" => "bx-pause-circle",
        "overdue" => "bx-alarm-exclamation",
        "not started" => "bx-dots-horizontal-rounded"
    ];
    $status = strtolower($status);

    return $icon_classes[$status];
}
