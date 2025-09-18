<?php

require_once __DIR__ . '/../config/Conexao.php';
require_once __DIR__ . '/Funcionario.php';

class Projeto {
    private $id;
    private $nome_projeto;
    private $descricao;
    private $status_projeto;

    public function __construct(string $nome_projeto, string $descricao, string $status_projeto) {
        $this->nome_projeto = $nome_projeto;
        $this->descricao = $descricao;
        $this->status_projeto = $status_projeto;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getNomeProjeto() { return $this->nome_projeto; }
    public function getDescricao() { return $this->descricao; }
    public function getStatusProjeto() { return $this->status_projeto; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setNomeProjeto(string $nome_projeto) { $this->nome_projeto = $nome_projeto; }
    public function setDescricao(string $descricao) { $this->descricao = $descricao; }
    public function setStatusProjeto(string $status_projeto) { $this->status_projeto = $status_projeto; }

    // Salvar projeto
    public function salvar(): bool {
        try {
            $pdo = Conexao::getConexao();
            $sql = "INSERT INTO projetos (nome_projeto, descricao, status_projeto) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);

            $resultado = $stmt->execute([$this->nome_projeto, $this->descricao, $this->status_projeto]);

            if ($resultado) $this->id = $pdo->lastInsertId();

            return $resultado;
        } catch (PDOException $e) {
            error_log("Erro ao salvar projeto: " . $e->getMessage());
            return false;
        }
    }

    // Atualizar projeto
    public function atualizar(): bool {
        try {
            $pdo = Conexao::getConexao();
            $sql = "UPDATE projetos SET nome_projeto = ?, descricao = ?, status_projeto = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);

            return $stmt->execute([$this->nome_projeto, $this->descricao, $this->status_projeto, $this->id]);
        } catch (PDOException $e) {
            error_log("Erro ao atualizar projeto: " . $e->getMessage());
            return false;
        }
    }

    // Deletar projeto
    public function deletar(): bool {
        try {
            $pdo = Conexao::getConexao();
            $sql = "DELETE FROM projetos WHERE id = ?";
            $stmt = $pdo->prepare($sql);

            return $stmt->execute([$this->id]);
        } catch (PDOException $e) {
            error_log("Erro ao deletar projeto: " . $e->getMessage());
            return false;
        }
    }

    // Buscar todos os projetos
    public static function buscarTodos(): array {
        try {
            $pdo = Conexao::getConexao();
            $sql = "SELECT * FROM projetos ORDER BY nome_projeto";
            $stmt = $pdo->query($sql);

            $projetos = [];
            while ($row = $stmt->fetch()) {
                $projeto = new Projeto($row['nome_projeto'], $row['descricao'], $row['status_projeto']);
                $projeto->setId($row['id']);
                $projetos[] = $projeto;
            }

            return $projetos;
        } catch (PDOException $e) {
            error_log("Erro ao buscar projetos: " . $e->getMessage());
            return [];
        }
    }

    // Buscar projeto por ID
    public static function buscarPorId(int $id): ?Projeto {
        try {
            $pdo = Conexao::getConexao();
            $sql = "SELECT * FROM projetos WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);

            $row = $stmt->fetch();
            if ($row) {
                $projeto = new Projeto($row['nome_projeto'], $row['descricao'], $row['status_projeto']);
                $projeto->setId($row['id']);
                return $projeto;
            }

            return null;
        } catch (PDOException $e) {
            error_log("Erro ao buscar projeto por ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Busca todos os funcionários vinculados a este projeto
     * @return Funcionario[]
     */
    public function buscarFuncionarios(): array {
        try {
            $pdo = Conexao::getConexao();
            // Assumindo tabela intermediária projeto_funcionario(id_projeto, id_funcionario)
            $sql = "SELECT f.* 
                    FROM funcionarios f
                    INNER JOIN projeto_funcionario pf ON f.id = pf.id_funcionario
                    WHERE pf.id_projeto = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$this->id]);

            $funcionarios = [];
            while ($row = $stmt->fetch()) {
                $funcionario = new Funcionario($row['nome'], $row['cargo'], $row['salario']);
                $funcionario->setId($row['id']);
                $funcionarios[] = $funcionario;
            }

            return $funcionarios;
        } catch (PDOException $e) {
            error_log("Erro ao buscar funcionários do projeto: " . $e->getMessage());
            return [];
        }
    }
}

?>
