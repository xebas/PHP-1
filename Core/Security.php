<?php
/*
* ==========================================================
*     SECURITY FUNCTIONS
* ==========================================================
*/

namespace Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class Security
{
    /*
    * ==========================================================
    *     FILTER INPUTS
    * ==========================================================
    */
    protected function secure_input($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    /*
    * ==========================================================
    *     ENCRYPT & DECRYPT
    * ==========================================================
    */
    protected function en_de_cryptIt($string, $action)
    {
        $secret_key = '32452c24d2e5242394f51L24eEr210';
        $secret_iv = 'A)2C!u427z^';

        $output = false;
        $encrypt_method = 'AES-256-CBC';
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        if ($action == 'en') {
            $output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
        } elseif ($action == 'de') {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }

        return $output;
    }
    /*
    * ==========================================================
    *     TOKEN GENERATOR
    * ==========================================================
    */
    protected function tokenGen($length)
    {
        $code = 'qwertzuiopasdfghjklyxcvbnmQWERTZUIOPASDFGHJKLYXCVBNM0123456789';
        $str_shuffle = str_shuffle($code);
        $token = substr($str_shuffle, 0, $length);
        return $token;
    }
    /*
    * ==========================================================
    *     SESSIONS & COOKIES
    * ==========================================================
    */
    protected function setSessions($id, $email)
    {
        $_SESSION['userAgent'] = $_SERVER['HTTP_USER_AGENT']; // tipo navegador
        $_SESSION['LastActivity'] = $_SERVER['REQUEST_TIME']; // fecha Unix de inicio de la petición
        $_SESSION['SESSID'] = $id; // id user
        $_SESSION['SESSEMAIL'] = $email; // email user
    }

    protected function checkSession()
    {
        if (isset($_SESSION['SESSID']) || isset($_SESSION['SESSEMAIL'])) {
            header('location: projects');
        }
    }

    protected function checkSessionExpired()
    {
        // session expired in 30 min of inactivity
        if (isset($_SESSION['LastActivity'])) {
            if (time() - $_SESSION['LastActivity'] > 60 * 30) {
                // last request was more than 30 minutes ago
                $this->closeSession();
            } elseif (time() - $_SESSION['LastActivity'] > 60) {
                $_SESSION['LastActivity'] = time(); // update last activity time stamp
            }
        }
    }

    protected function setCookie($id, $email)
    {
        $time = time() + 3600 * 4;
        $userHashId = $this->en_de_cryptIt($id, 'en');
        $userHashEmail = $this->en_de_cryptIt($email, 'en');
        $dataCookie = array($userHashId, $userHashEmail);
        setcookie('USER', serialize($dataCookie), $time, '/');
    }

    protected function checkCookie()
    {
        if (!isset($_SESSION['SESSID']) && isset($_COOKIE['USER'])) {
            $dataCookie = unserialize($_COOKIE['USER']);
            $id = $this->en_de_cryptIt($dataCookie[0], 'de');
            $email = $this->en_de_cryptIt($dataCookie[1], 'de');
            $_SESSION['SESSID'] = $id;
            $_SESSION['SESSEMAIL'] = $email;
        }
    }

    protected function closeSession()
    {
        session_unset();
        session_destroy();
        session_regenerate_id(true);
        $time = time() - 3600 * 4;
        setcookie('USER', '', $time, '/');
        header('location: login');
    }
    /*
    * ==========================================================
    *     EMAILS
    * ==========================================================
    */
    protected function email($email, $subject, $body)
    {
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->IsHTML(true);
        $mail->SMTPDebug = 0;
        $mail->CharSet = 'UTF-8';
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 465;
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPAuth = true;
        $mail->Username = 'xxxxxxx';
        $mail->Password = 'xxxxxxx';
        $mail->setFrom('xxxxxxx', 'MVC-PHP-MySQL');
        $mail->addAddress($email);
        $mail->Subject = $subject;
        $mail->MsgHTML($body);

        if ($mail->send()) {
            return true;
        } else {
            return false;
        }
    }
}
