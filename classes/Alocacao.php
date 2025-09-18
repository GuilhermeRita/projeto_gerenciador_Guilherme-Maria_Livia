<?php

require_once __DIR__ . '/../config/Conexao.php';
require_once __DIR__ . '/Funcionario.php';
require_once __DIR__ . '/Projeto.php';

class Alocacao {
    private $id;
    private $id_funcionario;
    private $id_projeto;
    
    /**
     * Construtor da classe Alocacao
     * @param int $id_funcionario
     * @param int $id_projeto
     */
    public function __construct(int $id_funcionario, int $id_projeto) {
        $this->id_funcionario = $id_funcionario;
        $this->id_projeto = $id_projeto;
    }
    
    // Getters
    public function getId() {
        return $this->id;
    }
    
    public function getIdFuncionario() {
        return $this->id_funcionario;
    }
    
    public function getIdProjeto() {
        return $this->id_projeto;
    }
    
    // Setters
    public function setId($id) {
        $this->id = $id;
    }
    
    public function setIdFuncionario(int $id_funcionario) {
        $this->id_funcionario = $id_funcionario;
    }
    
    public function setIdProjeto(int $id_projeto) {
        $this->id_projeto = $id_projeto;
    }
    
    /**
     * Salva uma nova alocação no banco de dados
     * @return bool
     */
    public function salvar(): bool {
        try {
            $pdo = Conexao::getConexao();
            
            // Verifica se a alocação já existe
            $sqlVerifica = "SELECT id FROM alocacao WHERE id_funcionario = ? AND id_projeto = ?";
            $stmtVerifica = $pdo->prepare($sqlVerifica);
            $stmtVerifica->execute([$this->id_funcionario, $this->id_projeto]);
            
            if ($stmtVerifica->fetch()) {
                return false; // Alocação já existe
            }
            
            $sql = "INSERT INTO alocacao (id_funcionario, id_projeto) VALUES (?, ?)";
            $stmt = $pdo->prepare($sql);
            
            $resultado = $stmt->execute([
                $this->id_funcionario,
                $this->id_projeto
            ]);
            
            if ($resultado) {
                $this->id = $pdo->lastInsertId();
            }
            
            return $resultado;
            
        } catch (PDOException $e) {
            error_log("Erro ao salvar alocação: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Deleta a alocação do banco de dados
     * @return bool
     */
    public function deletar(): bool {
        try {
            $pdo = Conexao::getConexao();
            $sql = "DELETE FROM alocacao WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            
            return $stmt->execute([$this->id]);
            
        } catch (PDOException $e) {
            error_log("Erro ao deletar alocação: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Busca todas as alocações com informações de funcionários e projetos
     * @return array
     */
    public static function buscarTodas(): array {
        try {
            $pdo = Conexao::getConexao();
            $sql = "SELECT a.id, a.id_funcionario, a.id_projeto, 
                           f.nome as nome_funcionario, f.cargo,
                           p.nome_projeto, p.status_projeto
                    FROM alocacao a
                    INNER JOIN funcionarios f ON a.id_funcionario = f.id
                    INNER JOIN projetos p ON a.id_projeto = p.id
                    ORDER BY f.nome, p.nome_projeto";
            $stmt = $pdo->query($sql);
            
            $alocacoes = [];
            while ($row = $stmt->fetch()) {
                $alocacao = [
                    'id' => $row['id'],
                    'id_funcionario' => $row['id_funcionario'],
                    'id_projeto' => $row['id_projeto'],
                    'nome_funcionario' => $row['nome_funcionario'],
                    'cargo' => $row['cargo'],
                    'nome_projeto' => $row['nome_projeto'],
                    'status_projeto' => $row['status_projeto']
                ];
                $alocacoes[] = $alocacao;
            }
            
            return $alocacoes;
            
        } catch (PDOException $e) {
            error_log("Erro ao buscar alocações: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Busca uma alocação específica pelo ID
     * @param int $id
     * @return Alocacao|null
     */
    public static function buscarPorId(int $id): ?Alocacao {
        try {
            $pdo = Conexao::getConexao();
            $sql = "SELECT * FROM alocacao WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            
            $row = $stmt->fetch();
            if ($row) {
                $alocacao = new Alocacao($row['id_funcionario'], $row['id_projeto']);
                $alocacao->setId($row['id']);
                return $alocacao;
            }
            
            return null;
            
        } catch (PDOException $e) {
            error_log("Erro ao buscar alocação por ID: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Busca alocações por funcionário
     * @param int $id_funcionario
     * @return array
     */
    public static function buscarPorFuncionario(int $id_funcionario): array {
        try {
            $pdo = Conexao::getConexao();
            $sql = "SELECT a.*, p.nome_projeto, p.status_projeto
                    FROM alocacao a
                    INNER JOIN projetos p ON a.id_projeto = p.id
                    WHERE a.id_funcionario = ?
                    ORDER BY p.nome_projeto";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_funcionario]);
            
            $alocacoes = [];
            while ($row = $stmt->fetch()) {
                $alocacoes[] = $row;
            }
            
            return $alocacoes;
            
        } catch (PDOException $e) {
            error_log("Erro ao buscar alocações por funcionário: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Busca alocações por projeto
     * @param int $id_projeto
     * @return array
     */
    public static function buscarPorProjeto(int $id_projeto): array {
        try {
            $pdo = Conexao::getConexao();
            $sql = "SELECT a.*, f.nome, f.cargo
                    FROM alocacao a
                    INNER JOIN funcionarios f ON a.id_funcionario = f.id
                    WHERE a.id_projeto = ?
                    ORDER BY f.nome";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_projeto]);
            
            $alocacoes = [];
            while ($row = $stmt->fetch()) {
                $alocacoes[] = $row;
            }
            
            return $alocacoes;
            
        } catch (PDOException $e) {
            error_log("Erro ao buscar alocações por projeto: " . $e->getMessage());
            return [];
        }
    }
}

?>

