<?php
session_start();

function verificarLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: login.php');
        exit;
    }
}

function obterUsuarioLogado() {
    if (isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_email'])) {
        return [
            'id' => $_SESSION['usuario_id'],
            'email' => $_SESSION['usuario_email']
        ];
    }
    return null;
}
