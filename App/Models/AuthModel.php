<?php
/*
* ==========================================================
*     USER MODEL
* ==========================================================
*/

namespace App\Models;

use Core\Model;
use PDO;

class AuthModel extends Model
{
    private $db;

    public function __construct()
    {
        $this->db = Model::getInstanceDB();
    }
    /*
    * ==========================================================
    *     USER INFO DB
    * ==========================================================
    */
    public function dataUserDB($email)
    {
        $sql = 'select * from users where email = :email';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        if ($stmt->execute()) {
            return $stmt->fetch(PDO::FETCH_ASSOC) ?? false;
        } else {
            return false;
        }
    }
    /*
    * ==========================================================
    *     REGISTER DB
    * ==========================================================
    */
    public function registerDB($id, $nick, $email, $pass, $emailToken)
    {
        $dataUser = $this->dataUserDB($email);

        if (!$dataUser) {
            $sql = 'insert into users values (:id, :nick, :email, :pass, default, :emailToken, default, default)';
            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':nick', $nick);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':pass', $pass);
            $stmt->bindParam(':emailToken', $emailToken);

            if ($stmt->execute()) {
                return 2;
            } // registro ok
            else {
                return 1;
            } // error registro
        } else {
            return 0;
        } // email registrado
    }
    /*
    * ==========================================================
    *     EMAIL CONFIRM REGISTER DB
    * ==========================================================
    */
    public function checkToken($email, $token)
    {
        $dataUser = $this->dataUserDB($email);

        if ($dataUser['isEmailConfirmed'] === '1') {
            return 0;
        } // email ya confirmado
        elseif ($dataUser['emailToken'] === $token) { // tokens iguales

            $sql = 'update users set isEmailConfirmed = 1 where email = :email';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':email', $email);
            if ($stmt->execute()) {
                return 2;
            } // update ok
            else {
                return 3;
            } // error update
        } else {
            return 1;
        } // no coinciden tokens
    }
    /*
    * ==========================================================
    *     LOGIN DB
    * ==========================================================
    */
    public function loginDB($email, $pass)
    {
        $dataUser = $this->dataUserDB($email);

        if (!$dataUser) {
            return 0;
        } // email no registrado
        else {
            if ($dataUser['email'] === $email) {
                if ($dataUser['isEmailConfirmed'] !== '1') {
                    return 1;
                }  // email no confirmado
                elseif ($dataUser['pass'] !== $pass) {
                    return 2;
                } // pass incorrecto
                else {
                    return array(3, $dataUser);
                } // login ok
            } else {
                return 0;
            } // email no registrado
        }
    }
    /*
    * ==========================================================
    *    LOST PASSWORD DB
    * ==========================================================
    */
    public function passRecovery($email)
    {
        $dataUser = $this->dataUserDB($email);

        if (!$dataUser) {
            return 0;
        } // email no registrado
        else {
            return array(1, $dataUser['pass']);
        }
    }
}
