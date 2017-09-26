<?php

if (!function_exists('generate_fields')) {
    /**
     * Generate fields code.
     * @param  string $model
     * @param  int|null $id
     * @return string
     */
    function generate_fields(string $model, $id = null, $except = null)
    {
        return call_user_func($model . '::generateFormFields', $id, $except);
    }
}
