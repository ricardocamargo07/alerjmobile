<?php

function array_utf8_converter($array)
{
    foreach ((array) $array as $key => $item) {
        if (gettype($item) == 'object' || gettype($item) == 'array') {
            $item = array_utf8_converter((array) $item);
        } else if (!mb_detect_encoding((string) $item, 'utf-8', true)) {
            $item = utf8_encode($item);
        }

        $array[$key] = $item;
    }

    return $array;
}
