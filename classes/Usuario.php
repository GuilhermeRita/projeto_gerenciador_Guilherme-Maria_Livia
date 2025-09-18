<?php
require_once __DIR__ . '/../config/Conexao.php';

class Usuario {
    private $id;
    private $email;
    private $senha;

    public function __construct($id, $email, $senha) {
        $this->id = $id;
        $this->email = $email;
        $this->senha = $senha;
    }

    public function getId() {
        return $this->id;
    }

    public function getEmail() {
        return $this->email;
    }

    // compara senha como texto puro
    public function verificarSenha($senhaDigitada) {
        return $senhaDigitada === $this->senha;
    }

    // busca usuário pelo email
    public static function buscarPorEmail($email) {
        $pdo = Conexao::getConexao();

        $sql = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dados) {
            return new Usuario($dados['id'], $dados['email'], $dados['senha']);
        }

        return null;
    }
}
