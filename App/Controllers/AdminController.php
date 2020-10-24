<?php
/*
* ==========================================================
*     ADMIN CONTROLLER
* ==========================================================
*/

namespace App\Controllers;

use Core\Controller;
use App\Models\AdminModel;
use Core\View;
use Core\Security;

class AdminController extends Controller
{
    /*
    * ==========================================================
    *     RENDERS VIEWS
    * ==========================================================
    */
    public function renderAccount()
    {
        Security::checkCookie();
        Security::checkSessionExpired();

        if (!isset($_SESSION['SESSID'])) {
            header('location: login');
        } else {
            $model = new AdminModel;
            $dataUser = $model->dataUserDB($_SESSION['SESSEMAIL']);
            if (!$dataUser) {
                throw new \Exception('Error en al conexion a la base de datos', 500);
                exit;
            } else {
                $pass = Security::en_de_cryptIt($dataUser['pass'], 'de');
                echo View::renderTwig('Admin/account.html', array('dataUser' => $dataUser, 'pass' => $pass));
            }
        }
    }
    /*
    * ==========================================================
    *     CLOSE SESSION
    * ==========================================================
    */
    public function closeSession()
    {
        Security::closeSession();
    }

    /*
    * ==========================================================
    *     EDIT ACCOUNT (NICK & PASS) & UPLOAD IMAGE
    * ==========================================================
    */
    public function userEditAccount($params)
    {
        if (!isset($params['submitEditNick']) && !isset($params['submitEditPass']) && !isset($params['submitImageAcc'])) {
            throw new \Exception('Acceso no permitido', 403);
            exit;
        }

        usleep(500000);

        $warningIcon = '<i class="fa fa-exclamation-circle"></i>';
        $okIcon = '<i class="fa fa-check" aria-hidden="true"></i>';
        $spinnerIcon = '<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>';
        // formulario de editar Nick
        if (isset($params['submitEditNick'])) {
            $nick = Security::secure_input($params['editNick']);
            // validaciones
            if (empty($nick)) {
                echo json_encode($warningIcon . ' Por favor, modifica tu nick');
            } else {
                $model = new AdminModel;
                $edit = $model->editAccDB($nick, $_SESSION['SESSEMAIL'], 'nick');
                $model->closeDB();

                switch ($edit) {
                    case 0:
                        echo json_encode(array($okIcon . ' Nick modificado correctamente ' . $spinnerIcon, $nick));
                        break;
                    case 1:
                        throw new \Exception($warningIcon . ' Error en la conexión a la base de datos', 500);
                        exit;
                        break;
                    case 2:
                        throw new \Exception($warningIcon . ' Error en la acción editar los datos de usuario', 500);
                        exit;
                        break;
                    case 3:
                        echo json_encode($warningIcon . ' Añade un nick diferente al ya existente');
                        break;
                    default:
                        throw new \Exception($warningIcon . ' Error en la acción editar los datos de usuario', 500);
                        exit;
                        break;
                }
            }
        }
        // formulario de editar Pass
        if (isset($params['submitEditPass'])) {
            $pass = Security::secure_input($params['editPass']);
            // validaciones
            if (empty($pass)) {
                echo json_encode($warningIcon . ' Por favor, modifica tu contraseña');
            } elseif (strlen($pass) < 6) {
                echo json_encode($warningIcon . ' Por favor, introduce tu contraseña de 6 caracteres mínimo');
            } else {
                $hashedPsw = Security::en_de_cryptIt($pass, 'en');

                $model = new AdminModel;
                $edit = $model->editAccDB($hashedPsw, $_SESSION['SESSEMAIL'], 'pass');
                $model->closeDB();

                switch ($edit) {
                    case 0:
                        ob_start();
                        View::renderTwig('Email/password.html', array('pass' => $pass));
                        $body = ob_get_contents();
                        ob_end_clean();
                        $subject = 'Modificación de contraseña en MVC-Proyectos';

                        if (!Security::email($_SESSION['SESSEMAIL'], $subject, $body)) {
                            echo json_encode($warningIcon . ' No se ha podido enviar el email de confirmación de registro. Inténtalo más tarde');
                        } else {
                            echo json_encode(array($okIcon . ' Contraseña modificada correctamente. Te hemos enviado un email con la nueva contraseña ' . $spinnerIcon));
                        }
                        break;

                    case 1:
                        throw new \Exception($warningIcon . ' Error en la conexión a la base de datos', 500);
                        exit;
                        break;
                    case 2:
                        throw new \Exception($warningIcon . ' Error en la acción editar los datos de usuario', 500);
                        exit;
                        break;
                    case 3:
                        echo json_encode($warningIcon . ' Añade una contraseña diferente a la ya existente');
                        break;
                    default:
                        throw new \Exception($warningIcon . ' Error en la acción editar los datos de usuario', 500);
                        exit;
                        break;
                }
            }
        }
        // si existe imagen de perfil
        if (isset($_FILES['imageAcc'])) {
            $handle = new \Verot\Upload\Upload($_FILES['imageAcc']);

            if ($handle->uploaded) {
                $handle->dir_chmod = 0777;
                $handle->file_overwrite = true;
                $handle->file_new_name_body = 'user-image';
                $handle->process('../uploads/user/' . $_SESSION['SESSEMAIL']);
                if ($handle->processed) { // imagen subida a la  carpeta del servidor ok

                    $handle->clean();
                    $image = 'user-image.' . $handle->file_src_name_ext;

                    $model = new AdminModel;
                    $edit = $model->editAccDB($image, $_SESSION['SESSEMAIL'], 'image');
                    $model->closeDB();

                    $imageUrl = 'uploads/user/' . $_SESSION['SESSEMAIL'] . '/' . $image;

                    switch ($edit) {
                        case 0:
                            echo json_encode(array($okIcon . ' La imagen se ha actualizado correctamente ' . $spinnerIcon, $imageUrl));
                            break;
                        case 1:
                            throw new \Exception($warningIcon . ' Error en la conexión a la base de datos', 500);
                            exit;
                            break;
                        case 2:
                            throw new \Exception($warningIcon . ' Error en la acción editar los datos de usuario', 500);
                            exit;
                            break;
                        default:
                            throw new \Exception($warningIcon . ' Error en la acción editar los datos de usuario', 500);
                            exit;
                            break;
                    }
                } else {
                    echo json_encode($warningIcon . ' error : ' . $handle->error);
                }
            } else {
                echo json_encode($warningIcon . ' error : No se ha podido subir la imagen, inténtalo más tarde');
            }
        }
    }
    /*
    * ==========================================================
    *     DELETE ACCOUNT
    * ==========================================================
    */
    public function userDeleteAccount()
    {
        // detect ajax request
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            usleep(500000);

            $warningIcon = '<i class="fa fa-exclamation-circle"></i>';
            $okIcon = '<i class="fa fa-check" aria-hidden="true"></i>';
            $spinnerIcon = '<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>';

            $model = new AdminModel;
            $delete = $model->userDeleteAccountDB($_SESSION['SESSID'], $_SESSION['SESSEMAIL']);
            $model->closeDB();

            switch ($delete) {

                case 0:
                    throw new \Exception($warningIcon . ' Error en la conexión a la base de datos', 500);
                    exit;
                    break;
                case 1:
                    // borra carpeta e imagenes de los proyectos, si existe la carpeta
                    if (file_exists('../uploads/user/' . $_SESSION['SESSEMAIL'] . '/projects')) {
                        $files = glob('../uploads/user/' . $_SESSION['SESSEMAIL'] . '/projects/*');
                        foreach ($files as $file) {
                            if (is_file($file)) {
                                unlink($file);
                            }
                        }
                        rmdir('../uploads/user/' . $_SESSION['SESSEMAIL'] . '/projects');
                    }
                    if (file_exists('../uploads/user/' . $_SESSION['SESSEMAIL'])) {
                        $files = glob('../uploads/user/' . $_SESSION['SESSEMAIL'] . '/*');
                        foreach ($files as $file) {
                            if (is_file($file)) {
                                unlink($file);
                            }
                        }
                        rmdir('../uploads/user/' . $_SESSION['SESSEMAIL']);
                    }
                    echo json_encode(array($okIcon . ' Tus datos de tu cuenta se han eliminado correctamente ' . $spinnerIcon));
                    break;

                case 2:
                    echo json_encode(array($okIcon . ' Tus datos de tu cuenta se han eliminado correctamente ' . $spinnerIcon));
                    break;
                default:
                    throw new \Exception($warningIcon . ' Error en la acción eliminar los datos de usuario', 500);
                    exit;
                    break;
            }
        } else {
            throw new \Exception('Acceso no permitido', 403);
            exit;
        }
    }
}
