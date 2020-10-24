<?php
/*
* ==========================================================
*     ADMIN MODEL
* ==========================================================
*/

namespace App\Models;

use Core\Model;
use PDO;

class AdminModel extends Model
{
    private $db;

    public function __construct()
    {
        $this->db = Model::getInstanceDB();
    }
    /*
    * ==========================================================
    *     USER DATA INFO DB
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
    *     USER PROJECTS EXISTS INFO DB
    * ==========================================================
    */
    public function projectsExistsDB($idUser)
    {
        $sql = 'select * from projects where users_id = :idUser';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idUser', $idUser);
        if ($stmt->execute()) {  // ok select
            $rows = $stmt->rowCount();
            if ($rows > 0) {
                return true;
            } // projects exists
            else {
                return false;
            } // projects NO exists
        } else {
            return 0;
        } // error select
    }
    /*
    * ==========================================================
    *    EDIT ACCOUNT DB (NICK & PASS & IMAGE)
    * ==========================================================
    */
    public function editAccDB($data, $email, $field)
    {
        $dataUser = $this->dataUserDB($email);

        switch ($field) {
            case 'nick':

                if ($dataUser['nick'] === $data) {
                    return 3;
                } else {
                    $sql = 'update users set nick = :newNick where email = :email';
                }
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':newNick', $data);
                $stmt->bindParam(':email', $email);
                if ($stmt->execute()) {
                    return 0;
                } // update ok
                else {
                    return 1;
                } // error update
                break;

            case 'pass':

                if ($dataUser['pass'] === $data) {
                    return 3;
                } else {
                    $sql = 'update users set pass = :newPass where email = :email';
                }
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':newPass', $data);
                $stmt->bindParam(':email', $email);
                if ($stmt->execute()) {
                    return 0;
                } // update ok
                else {
                    return 1;
                } // error update
                break;

            case 'image':

                $image = 'uploads/user/' . $email . '/' . $data;

                $sql = 'update users set image = :newImage where email = :email';
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':newImage', $image);
                $stmt->bindParam(':email', $email);
                if ($stmt->execute()) {
                    return 0;
                } // update ok
                else {
                    return 1;
                } // error update
                break;

            default:
                return 2; // error interno
                break;
        }
    }
    /*
    * ==========================================================
    *    DELETE ACCOUNT DB
    * ==========================================================
    */
    public function userDeleteAccountDB($idUser, $email)
    {
        $projects = $this->projectsExistsDB($idUser);

        if ($projects) {
            $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, 1);
            $sql = 'delete from projects where users_id = :idUser;
                    delete from users where email = :email';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':idUser', $idUser);
            $stmt->bindParam(':email', $email);

            if ($stmt->execute()) {
                return 1;
            } // update ok
            else {
                return 0;
            } // error update
        } else {
            $sql = 'delete from users where email = :email';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':email', $email);

            if ($stmt->execute()) {
                return 2;
            } // update ok
            else {
                return 0;
            } // error update
        }
    }
}
