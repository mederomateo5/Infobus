<?php
    class User {
        // Conexión a la base de datos
        private $pdo;
        // Constructor
        public function __construct($pdo) {
            $this->pdo = $pdo;
        }

        // =========================
        // REGISTRO DE USUARIO
        // =========================
        public function register($username, $password, $email) {
            // Verificar si el email ya existe
            $checkEmail = $this->pdo->prepare(
                "SELECT id FROM users WHERE email = :email"
            );
            $checkEmail->execute([
                'email' => $email
            ]);
            // Si ya existe, detener registro
            if ($checkEmail->fetch()) {
                die("Ese email ya está registrado.");
            }
            // Encriptar contraseña
            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );
            // Insertar usuario
            $stmt = $this->pdo->prepare(
                "INSERT INTO users (username, password, email)
                VALUES (:username, :password, :email)"
            );
            // Ejecutar consulta
            $stmt->execute([
                'username' => $username,
                'password' => $hashedPassword,
                'email' => $email
            ]);
        }

        // =========================
        // LOGIN
        // =========================
        public function login($username, $password, $email) {
            // Buscar usuario
            $stmt = $this->pdo->prepare(
                "SELECT * FROM users
                WHERE username = :username
                AND email = :email"
            );
            // Ejecutar consulta
            $stmt->execute([
                'username' => $username,
                'email' => $email
            ]);
            // Obtener usuario
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            // Verificar contraseña
            if ($user && password_verify($password, $user['password'])) {
                return $user;
            }
            // Si falla
            return false;
        }
    }
?>