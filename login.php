<?php
require_once 'config/session.php';
require_once 'classes/Usuario.php';

$erro = '';

if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        $erro = 'Por favor, preencha todos os campos.';
    } else {
        $usuario = Usuario::buscarPorEmail($email);

        if ($usuario && $usuario->verificarSenha($senha)) {
            // salva sessão do usuário logado
            $_SESSION['usuario_id'] = $usuario->getId();
            $_SESSION['usuario_email'] = $usuario->getEmail();

            header('Location: dashboard.php');
            exit;
        } else {
            $erro = 'Email ou senha incorretos.';
        }

    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gerenciador de Projetos</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="login-container">
        <h2>Gerenciador de Projetos</h2>

        <?php if ($erro): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($erro) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" class="form-control"
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Entrar</button>
        </form>

        <div class="text-center mt-3">
            <p><strong>Usuário de teste:</strong></p>
            <p>Email: admin@empresa.com</p>
            <p>Senha: admin123</p>
        </div>
    </div>
</body>

</html>