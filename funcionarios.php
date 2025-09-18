<?php
require_once 'config/session.php';
require_once 'classes/Funcionario.php';

verificarLogin();

$usuario = obterUsuarioLogado();
$mensagem = '';
$erro = '';
$funcionarioEdicao = null;

// Processa ações (adicionar, editar, excluir)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    
    if ($acao === 'adicionar') {
        $nome = trim($_POST['nome'] ?? '');
        $cargo = trim($_POST['cargo'] ?? '');
        $salario = floatval($_POST['salario'] ?? 0);
        
        if (empty($nome) || empty($cargo) || $salario <= 0) {
            $erro = 'Por favor, preencha todos os campos corretamente.';
        } else {
            try {
                $funcionario = new Funcionario($nome, $cargo, $salario);
                if ($funcionario->salvar()) {
                    header("Location: funcionarios.php?sucesso=1");
                    exit;
                } else {
                    $erro = 'Erro ao adicionar funcionário.';
                }
            } catch (Exception $e) {
                $erro = 'Erro interno do sistema.';
            }
        }
    }
    
    elseif ($acao === 'editar') {
        $id = intval($_POST['id'] ?? 0);
        $nome = trim($_POST['nome'] ?? '');
        $cargo = trim($_POST['cargo'] ?? '');
        $salario = floatval($_POST['salario'] ?? 0);
        
        if ($id <= 0 || empty($nome) || empty($cargo) || $salario <= 0) {
            $erro = 'Por favor, preencha todos os campos corretamente.';
        } else {
            try {
                $funcionario = Funcionario::buscarPorId($id);
                if ($funcionario) {
                    $funcionario->setNome($nome);
                    $funcionario->setCargo($cargo);
                    $funcionario->setSalario($salario);
                    
                    if ($funcionario->atualizar()) {
                        header("Location: funcionarios.php?atualizado=1");
                        exit;
                    } else {
                        $erro = 'Erro ao atualizar funcionário.';
                    }
                } else {
                    $erro = 'Funcionário não encontrado.';
                }
            } catch (Exception $e) {
                $erro = 'Erro interno do sistema.';
            }
        }
    }
}

// Processa exclusão via GET
if (isset($_GET['excluir'])) {
    $id = intval($_GET['excluir']);
    try {
        $funcionario = Funcionario::buscarPorId($id);
        if ($funcionario && $funcionario->deletar()) {
            header("Location: funcionarios.php?excluido=1");
            exit;
        } else {
            $erro = 'Erro ao excluir funcionário.';
        }
    } catch (Exception $e) {
        $erro = 'Erro interno do sistema.';
    }
}

// Carrega funcionário para edição
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    try {
        $funcionarioEdicao = Funcionario::buscarPorId($id);
        if (!$funcionarioEdicao) {
            $erro = 'Funcionário não encontrado.';
        }
    } catch (Exception $e) {
        $erro = 'Erro interno do sistema.';
    }
}

// Mensagens de sucesso após redirecionamento
if (isset($_GET['sucesso'])) {
    $mensagem = 'Funcionário adicionado com sucesso!';
}
if (isset($_GET['atualizado'])) {
    $mensagem = 'Funcionário atualizado com sucesso!';
}
if (isset($_GET['excluido'])) {
    $mensagem = 'Funcionário excluído com sucesso!';
}

// Busca todos os funcionários
try {
    $funcionarios = Funcionario::buscarTodos();
} catch (Exception $e) {
    $erro = 'Erro ao carregar funcionários.';
    $funcionarios = [];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Funcionários - Gerenciador de Projetos</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <h1>Gerenciador de Projetos</h1>
            <nav>
                <ul class="nav-menu">
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="funcionarios.php">Funcionários</a></li>
                    <li><a href="projetos.php">Projetos</a></li>
                    <li><a href="alocacao.php">Alocações</a></li>
                </ul>
            </nav>
            <div class="user-info">
                <span>Olá, <?= htmlspecialchars($usuario['email']) ?></span>
                <a href="logout.php" class="logout-btn" 
   onclick="return confirm('Tem certeza que deseja sair?')">Sair</a>
            </div>
        </div>
    </header>

    <div class="container">
        <h2>Gerenciamento de Funcionários</h2>
        
        <?php if ($mensagem): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($mensagem) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($erro): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($erro) ?>
            </div>
        <?php endif; ?>
        
        <!-- Formulário de Cadastro/Edição -->
        <div class="card">
            <h2><?= $funcionarioEdicao ? 'Editar Funcionário' : 'Adicionar Novo Funcionário' ?></h2>
            <form method="POST">
                <input type="hidden" name="acao" value="<?= $funcionarioEdicao ? 'editar' : 'adicionar' ?>">
                <?php if ($funcionarioEdicao): ?>
                    <input type="hidden" name="id" value="<?= $funcionarioEdicao->getId() ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" class="form-control" 
                           value="<?= htmlspecialchars($funcionarioEdicao ? $funcionarioEdicao->getNome() : ($_POST['nome'] ?? '')) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="cargo">Cargo:</label>
                    <input type="text" id="cargo" name="cargo" class="form-control" 
                           value="<?= htmlspecialchars($funcionarioEdicao ? $funcionarioEdicao->getCargo() : ($_POST['cargo'] ?? '')) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="salario">Salário:</label>
                    <input type="number" id="salario" name="salario" class="form-control" 
                           step="0.01" min="0"
                           value="<?= $funcionarioEdicao ? $funcionarioEdicao->getSalario() : ($_POST['salario'] ?? '') ?>" required>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        <?= $funcionarioEdicao ? 'Atualizar' : 'Adicionar' ?>
                    </button>
                    <?php if ($funcionarioEdicao): ?>
                        <a href="funcionarios.php" class="btn btn-secondary">Cancelar</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <!-- Lista de Funcionários -->
        <div class="card">
            <h2>Lista de Funcionários</h2>
            <?php if (!empty($funcionarios)): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Cargo</th>
                            <th>Salário</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($funcionarios as $funcionario): ?>
                            <tr>
                                <td><?= $funcionario->getId() ?></td>
                                <td><?= htmlspecialchars($funcionario->getNome()) ?></td>
                                <td><?= htmlspecialchars($funcionario->getCargo()) ?></td>
                                <td>R$ <?= number_format($funcionario->getSalario(), 2, ',', '.') ?></td>
                                <td>
                                    <a href="funcionarios.php?editar=<?= $funcionario->getId() ?>" 
                                       class="btn btn-warning btn-sm">Editar</a>
                                    <a href="funcionarios.php?excluir=<?= $funcionario->getId() ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Tem certeza que deseja excluir este funcionário?')">Excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Nenhum funcionário cadastrado.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Remove parâmetros da URL após ação
        if (window.location.search.includes('sucesso') || 
            window.location.search.includes('atualizado') || 
            window.location.search.includes('excluido')) {
            setTimeout(() => {
                window.history.replaceState({}, document.title, window.location.pathname);
            }, 100);
        }
    </script>
</body>
</html>
