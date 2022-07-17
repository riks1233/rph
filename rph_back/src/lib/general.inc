<?php

function request_param($param_name, $is_mandatory = true)
{
    if (isset($_REQUEST[$param_name])) {
        return $_REQUEST[$param_name];
    }

    if ($is_mandatory) {
        respond_error("Bad or missing request parameter [$param_name]");
    }

    return null;
}

function custom_sanitize(&$value) {
    if (is_string($value)) {
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

function sanitize_array(array &$arr) {
    array_walk_recursive($arr, 'custom_sanitize');
}

function respond_success(array $data = [])
{
    respond(1, '', $data);
}

function respond_error(string $error_msg, array $data = [])
{
    respond(0, $error_msg, $data);
}

function respond(int $success, string $error_msg, array $data)
{
    sanitize_array($data);
    echo json_encode([
        'success' => $success,
        'error_msg' => $error_msg,
        'data' => $data,
    ]);
    exit;
}

function should_be_post_request()
{
    if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        return;
    }

    respond_error('Bad request method');
}

?>
