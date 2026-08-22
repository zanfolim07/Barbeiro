<?php

function postTexto($campo)
{
    return trim((string) ($_POST[$campo] ?? ''));
}

function postEmail($campo)
{
    $email = filter_var(postTexto($campo), FILTER_VALIDATE_EMAIL);
    return $email ?: null;
}

function mascararTelefone($telefone)
{
    $digitos = preg_replace('/\D/', '', (string) $telefone);

    if (strlen($digitos) <= 4) {
        return $digitos ?: '-';
    }

    return str_repeat('*', strlen($digitos) - 4) . substr($digitos, -4);
}

function redirecionarComStatus($destino, $mensagem)
{
    $query = http_build_query([
        'status' => 'erro',
        'msg' => $mensagem
    ]);

    header("Location: {$destino}?{$query}");
    exit;
}

function tokenCsrf()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrfValido($token)
{
    return is_string($token)
        && !empty($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}