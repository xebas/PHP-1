<?php
/*
* ==========================================================
*     USER CONTROLLER
* ==========================================================
*/

namespace App\Controllers;

use Core\Controller;
use App\Models\AuthModel;
use Core\View;
use Core\Security;
use Verot\Upload;

class AuthController extends Controller
{
    /*
    * ==========================================================
    *     RENDERS VIEWS
    * ==========================================================
    */
    // login
    public function renderLogin()
    {
        Security::checkSession();
        echo View::renderTwig('Auth/login.html');
    }
    // registro
    public function renderRegister()
    {
        Security::checkSession();
        echo View::renderTwig('Auth/register.html');
    }
    // contraseña olvidada
    public function renderLostPass()
    {
        Security::checkSession();
        echo View::renderTwig('Auth/lostPass.html');
    }
    /*
    * ==========================================================
    *     REGISTER
    * ==========================================================
    */
    public function userRegister($params)
    {
        if (!isset($params['submitReg'])) {
            throw new \Exception('Acceso no permitido', 403);
            exit;
        }

        usleep(500000);

        $nick = Security::secure_input($params['registerNick']);
        $email = Security::secure_input($params['registerEmail']);
        $pass = Security::secure_input($params['registerPassword']);
        $pass2 = Security::secure_input($params['registerPassword2']);

        $warningIcon = '<i class="fa fa-exclamation-circle"></i>';
        $okIcon = '<i class="fa fa-check" aria-hidden="true"></i>';
        // validaciones
        if (empty($nick)) {
            echo json_encode($warningIcon . ' Por favor, introduce tu nick');
        } elseif (empty($email)) {
            echo json_encode($warningIcon . ' Por favor, introduce tu email');
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode($warningIcon . ' Por favor, introduce un email correcto');
        } elseif (empty($pass)) {
            echo json_encode($warningIcon . ' Por favor, introduce tu contraseña');
        } elseif (strlen($pass) < 6) {
            echo json_encode($warningIcon . ' Por favor, introduce tu contraseña de 6 caracteres mínimo');
        } elseif (empty($pass2)) {
            echo json_encode($warningIcon . ' Por favor, repite tu contraseña');
        } elseif (($pass != $pass2)) {
            echo json_encode($warningIcon . ' Las contraseñas no coinciden');
        } else {
            $hashedPsw = Security::en_de_cryptIt($pass, 'en');
            $emailToken = Security::tokenGen(20);
            $id = Security::tokenGen(4);

            $model = new AuthModel;
            $register = $model->registerDB($id, $nick, $email, $hashedPsw, $emailToken);
            $model->closeDB();

            switch ($register) {

                case 0:
                    echo json_encode($warningIcon . ' El email introducido ya está registrado');
                    break;
                case 1:
                    echo json_encode($warningIcon . ' Error en la conexión a la base de datos');
                    break;
                case 2:

                    $href = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
                    $href = substr($href, 0, -13) . '/registerConfirm';

                    ob_start();
                    View::renderTwig('Email/register.html', array('href' => $href, 'token' => $emailToken, 'nick' => $nick, 'email' => $email));
                    $body = ob_get_contents();
                    ob_end_clean();
                    $subject = 'Por favor, confirma el registro';

                    if (!Security::email($email, $subject, $body)) {
                        echo json_encode($warningIcon . ' No se ha podido enviar el email de confirmación de registro. Inténtalo más tarde');
                    } else {
                        echo json_encode($okIcon . ' Usuario registrado con éxito, verifica tu email para confirmar el registro');
                    }
                    break;

                default:
                    throw new \Exception('Error en la acción de registro', 500);
                    exit;
                    break;
            }
        }
    }
    /*
    * ==========================================================
    *     EMAIL CONFIRM REGISTER
    * ==========================================================
    */
    public function registerConfirm($params)
    {
        if (!isset($params['action'])) {
            throw new \Exception('Acceso no permitido', 403);
            exit;
        }

        $model = new AuthModel;
        $check = $model->checkToken($params['email'], $params['token']);
        $model->closeDB();

        $warningIcon = '<i class="fa fa-exclamation-circle"></i>';
        $okIcon = '<i class="fa fa-check" aria-hidden="true"></i>';

        switch ($check) {
            case 0:
                View::renderTwig('Auth/login.html', array('regConfirmMsg' => $warningIcon . ' El registro se ha confirmado anteriormente'));
                break;
            case 1:
                View::renderTwig('Auth/login.html', array('regConfirmMsg' => $warningIcon . ' No se ha podido confirmar el registro'));
                break;
            case 2:
                View::renderTwig('Auth/login.html', array('regConfirmMsg' => $okIcon . ' Registro confirmado correctamente'));
                break;
            case 3:
                throw new \Exception($warningIcon . ' Error en la conexión a la base de datos', 500);
                exit;
                break;
            default:
                throw new \Exception($warningIcon . ' Error en la acción confirmar el registro', 500);
                exit;
                break;
        }
    }
    /*
    * ==========================================================
    *     LOGIN
    * ==========================================================
    */
    public function userLogin($params)
    {
        if (!isset($params['submitLog'])) {
            throw new \Exception('Acceso no permitido', 403);
            exit;
        }

        usleep(500000);

        $email = Security::secure_input($params['loginUsername']);
        $pass = Security::secure_input($params['loginPassword']);

        $warningIcon = '<i class="fa fa-exclamation-circle"></i>';
        // validaciones
        if (empty($email)) {
            echo json_encode($warningIcon . ' Por favor, introduce tu email');
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode($warningIcon . ' Por favor, introduce un email correcto');
        } elseif (empty($pass)) {
            echo json_encode($warningIcon . ' Por favor, introduce tu contraseña');
        } else {
            $hashedPsw = Security::en_de_cryptIt($pass, 'en');
            $model = new AuthModel;
            $login = $model->loginDB($email, $hashedPsw);
            $model->closeDB();

            switch (true) {
                case $login === 0:
                    echo json_encode($warningIcon . ' Email no registrado en la app');
                    break;
                case $login === 1:
                    echo json_encode($warningIcon . ' No se ha confirmado el registro, por favor, revisa tu email');
                    break;
                case $login === 2:
                    echo json_encode($warningIcon . ' La contraseña no es correcta');
                    break;

                case is_array($login) && $login[0] === 3: // login ok

                    if (isset($params['rememberMe'])) {
                        Security::setCookie($login[1]['id'], $login[1]['email']);
                    }
                    Security::setSessions($login[1]['id'], $login[1]['email']);
                    echo json_encode('auth');
                    break;

                default:
                    throw new \Exception('Error en la acción de login', 500);
                    exit;
                    break;
            }
        }
    }
    /*
    * ==========================================================
    *     LOST PASSWORD
    * ==========================================================
    */
    public function lostPassEmail($params)
    {
        if (!isset($params['submitLostPass'])) {
            throw new \Exception('Acceso no permitido', 403);
            exit;
        }

        usleep(500000);

        $email = Security::secure_input($params['emailLostPass']);

        $warningIcon = '<i class="fa fa-exclamation-circle"></i>';
        $okIcon = '<i class="fa fa-check" aria-hidden="true"></i>';
        // validaciones
        if (empty($email)) {
            echo json_encode($warningIcon . ' Por favor, introduce tu email');
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode($warningIcon . ' Por favor, introduce un email correcto');
        } else {
            $model = new AuthModel;
            $recPass = $model->passRecovery($email);
            $model->closeDB();

            switch (true) {
                case $recPass === 0:
                    echo json_encode($warningIcon . ' Email no registrado en la app');
                    break;
                case is_array($recPass) && $recPass[0] === 1: // login ok

                    $pass = Security::en_de_cryptIt($recPass[1], 'de');
                    ob_start();
                    View::renderTwig('Email/password.html', array('pass' => $pass));
                    $body = ob_get_contents();
                    ob_end_clean();
                    $subject = 'Tu contraseña MVC-Proyectos';

                    if (!Security::email($email, $subject, $body)) {
                        echo json_encode($warningIcon . ' No se ha podido enviar el email de confirmación de registro. Inténtalo más tarde');
                    } else {
                        echo json_encode($okIcon . ' Te hemos enviado un email con tu contraseña');
                    }
                    break;

                default:
                    throw new \Exception('Error en la acción confirmar el registro', 500);
                    exit;
                    break;
            }
        }
    }
}
