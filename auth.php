<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        "httponly" => true,
        "secure" => !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off",
        "samesite" => "Lax",
    ]);
    session_start();
}

function csrf_token()
{
    if (empty($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
    }

    return $_SESSION["csrf_token"];
}

function csrf_is_valid()
{
    return isset($_SESSION["csrf_token"], $_POST["csrf_token"])
        && hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"]);
}

function require_staff_login()
{
    if (empty($_SESSION["staff_id"])) {
        header("Location: login.php");
        exit();
    }
}

function require_admin_login()
{
    require_staff_login();

    if (($_SESSION["staff_role"] ?? "") !== "admin") {
        http_response_code(403);
        exit("Only an administrator can add staff accounts.");
    }
}
