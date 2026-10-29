<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class DosFactores {
    const VIGENCIA     = 300;  // 5 minutos
    const MAX_INTENTOS = 5;

    /* Iniciar 2FA para usuario existente (Login) */
    public static function iniciar(array $user): bool {
        $codigo = random_int(100000, 999999);

        $_SESSION['2fa'] = [
            'es_registro' => false,
            'user_id'     => $user['id'],
            'nombre'      => $user['nombre'],
            'tipo'        => $user['tipo'],
            'hash'        => password_hash((string) $codigo, PASSWORD_DEFAULT),
            'expira'      => time() + self::VIGENCIA,
            'intentos'    => 0,
        ];

        return self::enviar($user['email'], $codigo);
    }

    /* Iniciar 2FA para usuario NUEVO (Registro pendiente de guardar en BD) */
    public static function iniciarRegistro(array $datosRegistro): bool {
        $codigo = random_int(100000, 999999);

        $_SESSION['2fa'] = [
            'es_registro'    => true,
            'datos_registro' => $datosRegistro,
            'hash'           => password_hash((string) $codigo, PASSWORD_DEFAULT),
            'expira'         => time() + self::VIGENCIA,
            'intentos'       => 0,
        ];

        return self::enviar($datosRegistro['email'], $codigo);
    }

    /* Devuelve: ok | incorrecto | vencido | bloqueado | sin_pendiente */
    public static function verificar(string $ingresado, ?PDO $pdo = null): string {
        $p = $_SESSION['2fa'] ?? null;
        if (!$p) return 'sin_pendiente';

        if (time() > $p['expira']) {
            unset($_SESSION['2fa']);
            return 'vencido';
        }
        if ($p['intentos'] >= self::MAX_INTENTOS) {
            unset($_SESSION['2fa']);
            return 'bloqueado';
        }
        if (!password_verify($ingresado, $p['hash'])) {
            $_SESSION['2fa']['intentos']++;
            return 'incorrecto';
        }

        // Código correcto:
        session_regenerate_id(true);

        if (!empty($p['es_registro']) && $p['es_registro'] === true) {
            // Es un registro nuevo: ahora sí lo guardamos en la base de datos
            if ($pdo) {
                $userObj = new User($pdo);
                // Validar por seguridad una vez más que no se haya registrado en paralelo
                if ($userObj->emailExists($p['datos_registro']['email'])) {
                    unset($_SESSION['2fa']);
                    return 'email_existente';
                }
                $nuevoId = $userObj->register($p['datos_registro']);
                
                $_SESSION['user_id']   = $nuevoId;
                $_SESSION['user_name'] = $p['datos_registro']['nombre'];
                $_SESSION['user_tipo'] = 'usuario';
            } else {
                unset($_SESSION['2fa']);
                return 'error_bd';
            }
        } else {
            // Inicio de sesión normal
            $_SESSION['user_id']   = $p['user_id'];
            $_SESSION['user_name'] = $p['nombre'];
            $_SESSION['user_tipo'] = $p['tipo'];
        }

        unset($_SESSION['2fa']);
        return 'ok';
    }

    private static function enviar(string $destino, int $codigo): bool {
        $cfg  = require __DIR__ . '/../config/mail.php';
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $cfg['usuario'];
            $mail->Password   = $cfg['clave'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom($cfg['usuario'], $cfg['nombre']);
            $mail->addAddress($destino);
            $mail->Subject = 'Tu código de acceso a Infobus';
            $mail->Body    = "Tu código es: $codigo\n\nVence en 5 minutos. "
                           . "Si no fuiste vos, ignorá este mensaje.";
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('2FA correo: ' . $mail->ErrorInfo);
            return false;
        }
    }
}
?>