<?php
require_once 'config/session.php';
require_once 'classes/Projeto.php';
require_once 'classes/Alocacao.php';

verificarLogin();

$usuario = obterUsuarioLogado();
$mensagem = '';
$erro = '';
$projetoEdicao = null;

// Processa ações (adicionar, editar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    
    if ($acao === 'adicionar') {
        $nome_projeto = trim($_POST['nome_projeto'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $status_projeto = trim($_POST['status_projeto'] ?? '');
        
        if (empty($nome_projeto) || empty($descricao) || empty($status_projeto)) {
            $erro = 'Por favor, preencha todos os campos.';
        } else {
            try {
                $projeto = new Projeto($nome_projeto, $descricao, $status_projeto);
                if ($projeto->salvar()) {
                    header("Location: projetos.php?sucesso=1");
                    exit;
                } else {
                    $erro = 'Erro ao adicionar projeto.';
                }
            } catch (Exception $e) {
                $erro = 'Erro interno do sistema.';
            }
        }
    }
    
    elseif ($acao === 'editar') {
        $id = intval($_POST['id'] ?? 0);
        $nome_projeto = trim($_POST['nome_projeto'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $status_projeto = trim($_POST['status_projeto'] ?? '');
        
        if ($id <= 0 || empty($nome_projeto) || empty($descricao) || empty($status_projeto)) {
            $erro = 'Por favor, preencha todos os campos corretamente.';
        } else {
            try {
                $projeto = Projeto::buscarPorId($id);
                if ($projeto) {
                    $projeto->setNomeProjeto($nome_projeto);
                    $projeto->setDescricao($descricao);
                    $projeto->setStatusProjeto($status_projeto);
                    
                    if ($projeto->atualizar()) {
                        header("Location: projetos.php?editado=1");
                        exit;
                    } else {
                        $erro = 'Erro ao atualizar projeto.';
                    }
                } else {
                    $erro = 'Projeto não encontrado.';
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
        $projeto = Projeto::buscarPorId($id);
        if ($projeto && $projeto->deletar()) {
            header("Location: projetos.php?excluido=1");
            exit;
        } else {
            $erro = 'Erro ao excluir projeto.';
        }
    } catch (Exception $e) {
        $erro = 'Erro interno do sistema.';
    }
}

// Carrega projeto para edição
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    try {
        $projetoEdicao = Projeto::buscarPorId($id);
        if (!$projetoEdicao) {
            $erro = 'Projeto não encontrado.';
        }
    } catch (Exception $e) {
        $erro = 'Erro interno do sistema.';
    }
}

// Busca todos os projetos
try {
    $projetos = Projeto::buscarTodos();
} catch (Exception $e) {
    $erro = 'Erro ao carregar projetos.';
    $projetos = [];
}

// Opções de status
$statusOptions = ['Planejamento', 'Em Andamento', 'Concluído', 'Cancelado'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projetos - Gerenciador de Projetos</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Modal básico */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            padding-top: 100px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: #fff;
            margin: auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 500px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover { color: black; }
    </style>
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
    <h2>Gerenciamento de Projetos</h2>

    <!-- Mensagens -->
    <?php if ($mensagem): ?>
        <div class="alert alert-success"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>
    <?php if ($erro): ?>
        <div class="alert alert-error"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['sucesso'])): ?>
        <div class="alert alert-success">Projeto adicionado com sucesso!</div>
    <?php endif; ?>
    <?php if (isset($_GET['editado'])): ?>
        <div class="alert alert-success">Projeto atualizado com sucesso!</div>
    <?php endif; ?>
    <?php if (isset($_GET['excluido'])): ?>
        <div class="alert alert-success">Projeto excluído com sucesso!</div>
    <?php endif; ?>

    <!-- Formulário de Cadastro/Edição -->
    <div class="card">
        <h2><?= $projetoEdicao ? 'Editar Projeto' : 'Adicionar Novo Projeto' ?></h2>
        <form method="POST">
            <input type="hidden" name="acao" value="<?= $projetoEdicao ? 'editar' : 'adicionar' ?>">
            <?php if ($projetoEdicao): ?>
                <input type="hidden" name="id" value="<?= $projetoEdicao->getId() ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="nome_projeto">Nome do Projeto:</label>
                <input type="text" id="nome_projeto" name="nome_projeto" class="form-control"
                       value="<?= htmlspecialchars($projetoEdicao ? $projetoEdicao->getNomeProjeto() : ($_POST['nome_projeto'] ?? '')) ?>" required>
            </div>

            <div class="form-group">
                <label for="descricao">Descrição:</label>
                <textarea id="descricao" name="descricao" class="form-control" rows="4" required><?= htmlspecialchars($projetoEdicao ? $projetoEdicao->getDescricao() : ($_POST['descricao'] ?? '')) ?></textarea>
            </div>

            <div class="form-group">
                <label for="status_projeto">Status:</label>
                <select id="status_projeto" name="status_projeto" class="form-control" required>
                    <option value="">Selecione um status</option>
                    <?php foreach ($statusOptions as $status): ?>
                        <option value="<?= htmlspecialchars($status) ?>"
                                <?= ($projetoEdicao && $projetoEdicao->getStatusProjeto() === $status) ||
                                   (!$projetoEdicao && ($_POST['status_projeto'] ?? '') === $status) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($status) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success"><?= $projetoEdicao ? 'Atualizar' : 'Adicionar' ?></button>
                <?php if ($projetoEdicao): ?>
                    <a href="projetos.php" class="btn btn-secondary">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Lista de Projetos -->
    <div class="card">
        <h2>Lista de Projetos</h2>
        <?php if (!empty($projetos)): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome do Projeto</th>
                        <th>Descrição</th>
                        <th>Status</th>
                        <th>Ações</th>
                        <th>Funcionários</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projetos as $projeto): ?>
                        <tr>
                            <td><?= $projeto->getId() ?></td>
                            <td><?= htmlspecialchars($projeto->getNomeProjeto()) ?></td>
                            <td><?= htmlspecialchars(substr($projeto->getDescricao(), 0, 100)) ?><?= strlen($projeto->getDescricao()) > 100 ? '...' : '' ?></td>
                            <td>
                                <?php
                                $badgeClass = 'badge-info';
                                if ($projeto->getStatusProjeto() === 'Em Andamento') $badgeClass = 'badge-warning';
                                if ($projeto->getStatusProjeto() === 'Concluído') $badgeClass = 'badge-success';
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($projeto->getStatusProjeto()) ?></span>
                            </td>
                            <td>
                                <a href="projetos.php?editar=<?= $projeto->getId() ?>" class="btn btn-warning btn-sm">Editar</a>
                                <a href="projetos.php?excluir=<?= $projeto->getId() ?>" class="btn btn-danger btn-sm"
                                   onclick="return confirm('Tem certeza que deseja excluir este projeto?')">Excluir</a>
                            </td>
                            <td>
                                <?php
                                $alocados = Alocacao::buscarPorProjeto($projeto->getId());
                                ?>
                                <button class="btn btn-info btn-sm" onclick="abrirModal(<?= $projeto->getId() ?>)">Visualizar</button>

                                <!-- Modal -->
                                <div id="modal-<?= $projeto->getId() ?>" class="modal">
                                    <div class="modal-content">
                                        <span class="close" onclick="fecharModal(<?= $projeto->getId() ?>)">&times;</span>
                                        <h3>Funcionários do Projeto: <?= htmlspecialchars($projeto->getNomeProjeto()) ?></h3>
                                        <?php if (!empty($alocados)): ?>
                                            <ul>
                                                <?php foreach ($alocados as $alocacao): ?>
                                                    <li><?= htmlspecialchars($alocacao['nome']) ?> - <?= htmlspecialchars($alocacao['cargo']) ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            <p>Nenhum funcionário alocado.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nenhum projeto cadastrado.</p>
        <?php endif; ?>
    </div>
</div>

<script>
function abrirModal(id) {
    document.getElementById('modal-' + id).style.display = 'block';
}
function fecharModal(id) {
    document.getElementById('modal-' + id).style.display = 'none';
}
// Fecha o modal clicando fora da área
window.onclick = function(event) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    });
}
// Remove parâmetros da URL após exibir mensagens
if (window.location.search.includes('sucesso') || 
    window.location.search.includes('editado') || 
    window.location.search.includes('excluido') || 
    window.location.search.includes('editar')) {
    setTimeout(() => {
        window.history.replaceState({}, document.title, window.location.pathname);
    }, 100);
}
</script>
</body>
</html>
