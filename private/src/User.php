<?php
require_once __DIR__ . '/../config/config.php';

/*
    Clase User
    Maneja registro, login y validaciones de usuarios.
*/
class User {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // ----------------------------------------------------------------
    //  Validaciones
    // ----------------------------------------------------------------

    public function validateRegister(array $data): array {
        $errors = [];

        if (empty(trim($data['nombre'] ?? '')))
            $errors['nombre'] = 'El nombre es obligatorio.';

        if (empty(trim($data['apellido'] ?? '')))
            $errors['apellido'] = 'El apellido es obligatorio.';

        $email = trim($data['email'] ?? '');
        if (empty($email)) {
            $errors['email'] = 'El email es obligatorio.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'El email no tiene un formato válido.';
        } elseif (!preg_match('/@gmail\.com$/i', $email)) {
            $errors['email'] = 'Solo se permiten correos electrónicos de Gmail (@gmail.com).';
        } elseif ($this->emailExists($email)) {
            $errors['email'] = 'Este email ya está registrado.';
        }

        $telefono = trim($data['telefono'] ?? '');
        if (empty($telefono)) {
            $errors['telefono'] = 'El teléfono es obligatorio.';
        } elseif (!preg_match('/^\+?[0-9]{7,15}$/', $telefono)) {
            $errors['telefono'] = 'El teléfono debe tener entre 7 y 15 dígitos.';
        }

        $passErrors = $this->validatePassword($data['password'] ?? '');
        if (!empty($passErrors))
            $errors['password'] = implode(' ', $passErrors);

        if (($data['password'] ?? '') !== ($data['confirm_password'] ?? ''))
            $errors['confirm_password'] = 'Las contraseñas no coinciden.';

        return $errors;
    }

    public function validatePassword(string $password): array {
        $errors = [];
        if (strlen($password) < 8)
            $errors[] = 'Mínimo 8 caracteres.';
        if (!preg_match('/[A-Z]/', $password))
            $errors[] = 'Debe contener al menos una mayúscula.';
        if (!preg_match('/[!@#$%^&*(),.?":{}|<>_\-\[\]\/\\\\]/', $password))
            $errors[] = 'Debe contener al menos un símbolo especial.';
        return $errors;
    }

    // ----------------------------------------------------------------
    //  Operaciones de base de datos
    // ----------------------------------------------------------------

    public function register(array $data): int {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare(
            'INSERT INTO users (nombre, apellido, email, telefono, password, tipo)
            VALUES (:nombre, :apellido, :email, :telefono, :password, "usuario")'
        );

        $stmt->execute([
            'nombre'   => htmlspecialchars(trim($data['nombre'])),
            'apellido' => htmlspecialchars(trim($data['apellido'])),
            'email'    => strtolower(trim($data['email'])),
            'telefono' => trim($data['telefono']),
            'password' => $hashedPassword,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function login(string $email, string $password): array|false {
        $email = strtolower(trim($email));

        if (!preg_match('/@gmail\.com$/i', $email)) {
            return false;
        }

        $stmt = $this->pdo->prepare(
            'SELECT * FROM users WHERE email = :email LIMIT 1'
        );
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function emailExists(string $email): bool {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM users WHERE email = :email'
        );
        $stmt->execute(['email' => strtolower(trim($email))]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function getByEmail(string $email): array|false {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM users WHERE email = :email LIMIT 1'
        );
        $stmt->execute(['email' => strtolower(trim($email))]);
        return $stmt->fetch();
    }
}
?>