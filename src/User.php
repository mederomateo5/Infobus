<?php
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

        /*
            Valida todos los campos del registro.
            Devuelve un array de errores (vacío = todo OK).
        */
        public function validateRegister(array $data): array {
            $errors = [];

            // Nombre
            if (empty(trim($data['nombre'])))
                $errors['nombre'] = 'El nombre es obligatorio.';

            // Apellido
            if (empty(trim($data['apellido'])))
                $errors['apellido'] = 'El apellido es obligatorio.';

            // Email
            if (empty(trim($data['email']))) {
                $errors['email'] = 'El email es obligatorio.';
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'El email no tiene un formato válido.';
            } elseif ($this->emailExists($data['email'])) {
                $errors['email'] = 'Este email ya está registrado.';
            }

            // Teléfono (solo dígitos y opcionalmente + al inicio)
            if (empty(trim($data['telefono']))) {
                $errors['telefono'] = 'El teléfono es obligatorio.';
            } elseif (!preg_match('/^\+?[0-9]{7,15}$/', $data['telefono'])) {
                $errors['telefono'] = 'El teléfono debe tener entre 7 y 15 dígitos.';
            }

            // Contraseña
            $passErrors = $this->validatePassword($data['password']);
            if (!empty($passErrors))
                $errors['password'] = implode(' ', $passErrors);

            // Confirmación de contraseña
            if ($data['password'] !== $data['confirm_password'])
                $errors['confirm_password'] = 'Las contraseñas no coinciden.';

            return $errors;
        }

        /*
            Valida la fortaleza de la contraseña.
            Devuelve un array de mensajes de error.
        */
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

        /*
            Registra un nuevo usuario en la base de datos.
            Devuelve el ID del usuario registrado.
        */
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

        /*
            Intenta loguear al usuario
            Devuelve el array con los datos del usuario, o false si falla
        */
        public function login(string $email, string $password): array|false {
            $stmt = $this->pdo->prepare(
                'SELECT * FROM users WHERE email = :email LIMIT 1'
            );
            $stmt->execute(['email' => strtolower(trim($email))]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                return $user;
            }
            return false;
        }

        /* Verifica si un email ya está registrado */
        private function emailExists(string $email): bool {
            $stmt = $this->pdo->prepare(
                'SELECT COUNT(*) FROM users WHERE email = :email'
            );
            $stmt->execute(['email' => strtolower(trim($email))]);
            return (int) $stmt->fetchColumn() > 0;
        }
    }
?>