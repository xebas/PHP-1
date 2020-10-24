<?php

namespace App\Models;

use Core\Model;
use PDO;

class ProjectModel extends Model
{
    private $db;

    public function __construct()
    {
        $this->db = Model::getInstanceDB();
    }
    /*
    * ==========================================================
    *     USER & PROJECTS (ALL) DATA INFO DB
    * ==========================================================
    */
    public function dataUserProjectsDB($idUser, $email)
    {
        $sql = 'select * from users where email = :email';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        if (!$stmt->execute()) {
            return false;
        } // fallo select
        else {
            $dataUser = $stmt->fetch(PDO::FETCH_ASSOC);
            $sql = 'select * from projects where users_id = :id';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $idUser);
            if (!$stmt->execute()) {
                return false;
            } // fallo select
            else {
                $rows = $stmt->rowCount();
                if ($rows === 0) {
                    return array($dataUser, false);
                } // no projects exists
                else {
                    $projects = $stmt->fetchAll();
                    return array($dataUser, $projects);
                }
            }
        }
    }
    /*
    * ==========================================================
    *     USER PROJECT (ONE) DATA INFO DB
    * ==========================================================
    */
    public function dataUserProjectDB($idUser, $idProject)
    {
        $sql = 'select * from projects where users_id = :idUser and id = :idProject';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idUser', $idUser);
        $stmt->bindParam(':idProject', $idProject);
        if (!$stmt->execute()) {
            return false;
        } // fallo select
        else {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
    /*
    * ==========================================================
    *    CHECK IF PROJECT EXISTS DB
    * ==========================================================
    */
    public function checkProjectDB($title)
    {
        $sql = 'select * from projects where title = :title';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?? false;
    }
    /*
    * ==========================================================
    *    ADD PROJECT DB
    * ==========================================================
    */
    public function addProjectDB($title, $desc, $image, $id, $email)
    {
        $sql = 'insert into projects values (null, :title, :description, :image, default, :users_id)';
        $stmt = $this->db->prepare($sql);

        $image = 'uploads/user/' . $email . '/projects/' . $image;

        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $desc);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':users_id', $id);

        if ($stmt->execute()) {
            return 1;
        } // insert ok
        else {
            return 0;
        } // error insert
    }
    /*
    * ==========================================================
    *    EDIT PROJECT DB (NICK & PASS & IMAGE)
    * ==========================================================
    */
    public function editProjectDB($title, $desc, $image, $idUser, $idProject)
    {
        $sql = 'update projects set title = :newTitle, description = :newDesc, image = :newImage where id = :idProject and users_id = :idUser';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':newTitle', $title);
        $stmt->bindParam(':newDesc', $desc);
        $stmt->bindParam(':newImage', $image);
        $stmt->bindParam(':idProject', $idProject);
        $stmt->bindParam(':idUser', $idUser);
        if ($stmt->execute()) {
            return 1;
        } // update ok
        else {
            return 0;
        } // error update
    }
    /*
    * ==========================================================
    *     DELETE PROJECT DB
    * ==========================================================
    */
    public function delProjectDB($id, $title)
    {
        $sql = 'delete from projects where title = :title and users_id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':title', $title);

        if ($stmt->execute()) {
            return 1;
        } // delete ok
        else {
            return 0;
        } // error delete
    }
}
