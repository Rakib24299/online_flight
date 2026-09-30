<?php
/**
 * Global Helper Functions
 */

function layout($file) {
    $path = __DIR__ . '/../layouts/' . $file;
    if (file_exists($path)) {
        include $path;
    } else {
        include __DIR__ . '/../sub-views/' . $file;
    }
}

function subview($file) {
    layout($file);
}