<?php

require_once __DIR__ . '/../config/Conexao.php';

class Funcionario {
    private $id;
    private $nome;
    private $cargo;
    private $salario;

    public function __construct(string $nome, string $cargo, float $salario) {
        $this->nome = $nome;
        $this->cargo = $cargo;
        $this->salario = $salario;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getNome() { return $this->nome; }
    public function getCargo() { return $this->cargo; }
    public function getSalario() { return $this->salario; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setNome(string $nome) { $this->nome = $nome; }
    public function setCargo(string $cargo) { $this->cargo = $cargo; }
    public function setSalario(float $salario) { $this->salario = $salario; }

    // CRUD
    public function salvar(): bool {
        try {
            $pdo = Conexao::getConexao();
            $sql = "INSERT INTO funcionarios (nome, cargo, salario) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $resultado = $stmt->execute([$this->nome, $this->cargo, $this->salario]);
            if ($resultado) $this->id = $pdo->lastInsertId();
            return $resultado;
        } catch (PDOException $e) {
            error_log("Erro ao salvar funcionário: " . $e->getMessage());
            return false;
        }
    }

    public function atualizar(): bool {
        try {
            $pdo = Conexao::getConexao();
            $sql = "UPDATE funcionarios SET nome = ?, cargo = ?, salario = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([$this->nome, $this->cargo, $this->salario, $this->id]);
        } catch (PDOException $e) {
            error_log("Erro ao atualizar funcionário: " . $e->getMessage());
            return false;
        }
    }

    public function deletar(): bool {
        try {
            $pdo = Conexao::getConexao();
            $sql = "DELETE FROM funcionarios WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([$this->id]);
        } catch (PDOException $e) {
            error_log("Erro ao deletar funcionário: " . $e->getMessage());
            return false;
        }
    }

    public static function buscarTodos(): array {
        try {
            $pdo = Conexao::getConexao();
            $stmt = $pdo->query("SELECT * FROM funcionarios ORDER BY nome");
            $funcionarios = [];
            while ($row = $stmt->fetch()) {
                $func = new Funcionario($row['nome'], $row['cargo'], floatval($row['salario']));
                $func->setId($row['id']);
                $funcionarios[] = $func;
            }
            return $funcionarios;
        } catch (PDOException $e) {
            error_log("Erro ao buscar funcionários: " . $e->getMessage());
            return [];
        }
    }

    public static function buscarPorId(int $id): ?Funcionario {
        try {
            $pdo = Conexao::getConexao();
            $stmt = $pdo->prepare("SELECT * FROM funcionarios WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            if ($row) {
                $func = new Funcionario($row['nome'], $row['cargo'], floatval($row['salario']));
                $func->setId($row['id']);
                return $func;
            }
            return null;
        } catch (PDOException $e) {
            error_log("Erro ao buscar funcionário por ID: " . $e->getMessage());
            return null;
        }
    }

    // ============================
    // FUNÇÕES DE PROJETO
    // ============================

    // Retorna todos os funcionários de um projeto
    public static function buscarPorProjeto(int $idProjeto): array {
        try {
            $pdo = Conexao::getConexao();
            $sql = "
                SELECT f.* 
                FROM funcionarios f
                INNER JOIN projeto_funcionario pf ON f.id = pf.funcionario_id
                WHERE pf.projeto_id = ?
                ORDER BY f.nome
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$idProjeto]);

            $funcionarios = [];
            while ($row = $stmt->fetch()) {
                $func = new Funcionario($row['nome'], $row['cargo'], floatval($row['salario']));
                $func->setId($row['id']);
                $funcionarios[] = $func;
            }
            return $funcionarios;

        } catch (PDOException $e) {
            error_log("Erro ao buscar funcionários do projeto: " . $e->getMessage());
            return [];
        }
    }

    // Vincula funcionário a um projeto
    public function vincularAoProjeto(int $idProjeto): bool {
        try {
            $pdo = Conexao::getConexao();
            $sql = "INSERT INTO projeto_funcionario (projeto_id, funcionario_id) VALUES (?, ?)";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([$idProjeto, $this->id]);
        } catch (PDOException $e) {
            error_log("Erro ao vincular funcionário ao projeto: " . $e->getMessage());
            return false;
        }
    }

    // Remove funcionário de um projeto
    public function removerDoProjeto(int $idProjeto): bool {
        try {
            $pdo = Conexao::getConexao();
            $sql = "DELETE FROM projeto_funcionario WHERE projeto_id = ? AND funcionario_id = ?";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([$idProjeto, $this->id]);
        } catch (PDOException $e) {
            error_log("Erro ao remover funcionário do projeto: " . $e->getMessage());
            return false;
        }
    }
}

?>
