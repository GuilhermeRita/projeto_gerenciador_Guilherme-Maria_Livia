<?php

class Conexao {
    // Configurações do banco
    private const DB_HOST = "localhost";   // ou 127.0.0.1
    private const DB_NAME = "empresa_tech";
    private const DB_USER = "root";        // ajuste se tiver outro usuário
    private const DB_PASS = "";            // ajuste se tiver senha no XAMPP
    
    private static $conexao = null;
    
    /**
     * Método estático para obter a conexão com o banco de dados
     * @return PDO|null
     */
    public static function getConexao() {
        if (self::$conexao === null) {
            try {
                $dsn = "mysql:host=" . self::DB_HOST . ";dbname=" . self::DB_NAME . ";charset=utf8";
                
                self::$conexao = new PDO($dsn, self::DB_USER, self::DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]);
                
            } catch (PDOException $e) {
                error_log("Erro de conexão com o banco de dados: " . $e->getMessage());
                throw new Exception("Erro ao conectar com o banco de dados");
            }
        }
        
        return self::$conexao;
    }
    
    /**
     * Método para fechar a conexão
     */
    public static function fecharConexao() {
        self::$conexao = null;
    }
}

?>
