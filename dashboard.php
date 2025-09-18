<?php
require_once 'config/session.php';
require_once 'classes/Funcionario.php';
require_once 'classes/Projeto.php';
require_once 'classes/Alocacao.php';

verificarLogin();

$usuario = obterUsuarioLogado();

// Busca estatísticas para o dashboard
try {
    $funcionarios = Funcionario::buscarTodos();
    $projetos = Projeto::buscarTodos();
    $alocacoes = Alocacao::buscarTodas();
    
    $totalFuncionarios = count($funcionarios);
    $totalProjetos = count($projetos);
    $totalAlocacoes = count($alocacoes);
    
    // Conta projetos por status
    $projetosEmAndamento = 0;
    $projetosConcluidos = 0;
    $projetosPlanejamento = 0;
    
    foreach ($projetos as $projeto) {
        switch ($projeto->getStatusProjeto()) {
            case 'Em Andamento':
                $projetosEmAndamento++;
                break;
            case 'Concluído':
                $projetosConcluidos++;
                break;
            case 'Planejamento':
                $projetosPlanejamento++;
                break;
        }
    }
    
} catch (Exception $e) {
    $erro = 'Erro ao carregar dados do dashboard.';
}


?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gerenciador de Projetos</title>
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
        <h2>Dashboard</h2>
        
        <?php if (isset($erro)): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($erro) ?>
            </div>
        <?php endif; ?>
        
        <div class="dashboard-stats">
            <div class="stat-card">
                <h3><?= $totalFuncionarios ?></h3>
                <p>Funcionários Cadastrados</p>
            </div>
            
            <div class="stat-card">
                <h3><?= $totalProjetos ?></h3>
                <p>Projetos Totais</p>
            </div>
            
            <div class="stat-card">
                <h3><?= $totalAlocacoes ?></h3>
                <p>Alocações Ativas</p>
            </div>
            
            <div class="stat-card">
                <h3><?= $projetosEmAndamento ?></h3>
                <p>Projetos em Andamento</p>
            </div>
        </div>
        
        <div class="card">
            <h2>Resumo dos Projetos por Status</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Quantidade</th>
                        <th>Porcentagem</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="badge badge-warning">Em Andamento</span></td>
                        <td><?= $projetosEmAndamento ?></td>
                        <td><?= $totalProjetos > 0 ? round(($projetosEmAndamento / $totalProjetos) * 100, 1) : 0 ?>%</td>
                    </tr>
                    <tr>
                        <td><span class="badge badge-success">Concluído</span></td>
                        <td><?= $projetosConcluidos ?></td>
                        <td><?= $totalProjetos > 0 ? round(($projetosConcluidos / $totalProjetos) * 100, 1) : 0 ?>%</td>
                    </tr>
                    <tr>
                        <td><span class="badge badge-info">Planejamento</span></td>
                        <td><?= $projetosPlanejamento ?></td>
                        <td><?= $totalProjetos > 0 ? round(($projetosPlanejamento / $totalProjetos) * 100, 1) : 0 ?>%</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="card">
            <h2>Últimas Alocações</h2>
            <?php if (!empty($alocacoes)): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Funcionário</th>
                            <th>Cargo</th>
                            <th>Projeto</th>
                            <th>Status do Projeto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($alocacoes, -5) as $alocacao): ?>
                            <tr>
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
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Nenhuma alocação encontrada.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

