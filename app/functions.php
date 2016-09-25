<?php

/**
 * @param mixed $value
 *
 * @return mixed
 */
function prepareForView($value)
{
    return json_decode(json_encode($value), true);
}
