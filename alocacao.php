<?php
require_once 'config/session.php';
require_once 'classes/Funcionario.php';
require_once 'classes/Projeto.php';
require_once 'classes/Alocacao.php';

verificarLogin();

$usuario = obterUsuarioLogado();
$mensagem = '';
$erro = '';

// Processa ações (adicionar, excluir)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    
    if ($acao === 'adicionar') {
        $id_funcionario = intval($_POST['id_funcionario'] ?? 0);
        $id_projeto = intval($_POST['id_projeto'] ?? 0);
        
        if ($id_funcionario <= 0 || $id_projeto <= 0) {
            $erro = 'Por favor, selecione um funcionário e um projeto.';
        } else {
            try {
                $alocacao = new Alocacao($id_funcionario, $id_projeto);
                if ($alocacao->salvar()) {
                    // Redireciona para a mesma página evitando duplicação
                    header("Location: alocacao.php?sucesso=1");
                    exit;
                } else {
                    $erro = 'Erro ao criar alocação. Verifique se esta alocação já existe.';
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
        $alocacao = Alocacao::buscarPorId($id);
        if ($alocacao && $alocacao->deletar()) {
            header("Location: alocacao.php?excluido=1");
            exit;
        } else {
            $erro = 'Erro ao excluir alocação.';
        }
    } catch (Exception $e) {
        $erro = 'Erro interno do sistema.';
    }
}

// Mensagens de sucesso após redirecionamento
if (isset($_GET['sucesso'])) {
    $mensagem = 'Alocação criada com sucesso!';
}
if (isset($_GET['excluido'])) {
    $mensagem = 'Alocação excluída com sucesso!';
}

// Busca dados para os formulários e listagem
try {
    $funcionarios = Funcionario::buscarTodos();
    $projetos = Projeto::buscarTodos();
    $alocacoes = Alocacao::buscarTodas();
} catch (Exception $e) {
    $erro = 'Erro ao carregar dados.';
    $funcionarios = [];
    $projetos = [];
    $alocacoes = [];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alocações - Gerenciador de Projetos</title>
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
        <h2>Gerenciamento de Alocações</h2>
        
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
        
        <!-- Formulário de Nova Alocação -->
        <div class="card">
            <h2>Nova Alocação</h2>
            <form method="POST">
                <input type="hidden" name="acao" value="adicionar">
                
                <div class="form-group">
                    <label for="id_funcionario">Funcionário:</label>
                    <select id="id_funcionario" name="id_funcionario" class="form-control" required>
                        <option value="">Selecione um funcionário</option>
                        <?php foreach ($funcionarios as $funcionario): ?>
                            <option value="<?= $funcionario->getId() ?>" 
                                    <?= (($_POST['id_funcionario'] ?? '') == $funcionario->getId()) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($funcionario->getNome()) ?> - <?= htmlspecialchars($funcionario->getCargo()) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="id_projeto">Projeto:</label>
                    <select id="id_projeto" name="id_projeto" class="form-control" required>
                        <option value="">Selecione um projeto</option>
                        <?php foreach ($projetos as $projeto): ?>
                            <option value="<?= $projeto->getId() ?>" 
                                    <?= (($_POST['id_projeto'] ?? '') == $projeto->getId()) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($projeto->getNomeProjeto()) ?> - <?= htmlspecialchars($projeto->getStatusProjeto()) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-success">Criar Alocação</button>
            </form>
        </div>
        
        <!-- Lista de Alocações -->
        <div class="card">
            <h2>Alocações Existentes</h2>
            <?php if (!empty($alocacoes)): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Funcionário</th>
                            <th>Cargo</th>
                            <th>Projeto</th>
                            <th>Status do Projeto</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alocacoes as $alocacao): ?>
                            <tr>
                                <td><?= $alocacao['id'] ?></td>
                                <td><?= htmlspecialchars($alocacao['nome_funcionario']) ?></td>
                                <td><?= htmlspecialchars($alocacao['cargo']) ?></td>
                                <td><?= htmlspecialchars($alocacao['nome_projeto']) ?></td>
                                <td>
                                    <?php
                                    $badgeClass = 'badge-info';
                                    if ($alocacao['status_projeto'] === 'Em Andamento') $badgeClass = 'badge-warning';
                                    if ($alocacao['status_projeto'] === 'Concluído') $badgeClass = 'badge-success';
                                    ?>
                                    <span class="badge <?= $badgeClass ?>">
                                        <?= htmlspecialchars($alocacao['status_projeto']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="alocacao.php?excluir=<?= $alocacao['id'] ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Tem certeza que deseja excluir esta alocação?')">Excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Nenhuma alocação encontrada.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Remove parâmetros da URL após ação
        if (window.location.search.includes('sucesso') || window.location.search.includes('excluido')) {
            setTimeout(() => {
                window.history.replaceState({}, document.title, window.location.pathname);
            }, 100);
        }
    </script>
</body>
</html>
