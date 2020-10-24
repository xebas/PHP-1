<?php
/*
* ==========================================================
*     PROJECTS CONTROLLER (CRUD)
* ==========================================================
*/

namespace App\Controllers;

use Core\Controller;
use App\Models\ProjectModel;
use Core\View;
use Core\Security;
use Verot\Upload;
use FilesystemIterator;

class ProjectController extends Controller
{
    /*
    * ==========================================================
    *     READ PROJECT
    * ==========================================================
    */
    public function renderProjects()
    {
        Security::checkCookie();
        Security::checkSessionExpired();

        if (!isset($_SESSION['SESSID'])) {
            header('location: login');
        } else {
            $model = new ProjectModel;
            $data = $model->dataUserProjectsDB($_SESSION['SESSID'], $_SESSION['SESSEMAIL']);
            if (!$data) {
                throw new \Exception('Error en al conexion a la base de datos', 500);
                exit;
            } else {
                if ($data[1] === false) {
                    echo View::renderTwig('Project/projects.html', array('dataUser' => $data[0]));
                } else {
                    echo View::renderTwig('Project/projects.html', array('dataUser' => $data[0], 'dataUserProjects' => $data[1]));
                }
            }
        }
    }
    /*
    * ==========================================================
    *     ADD PROJECT
    * ==========================================================
    */
    public function addProject($params)
    {
        if (!isset($params['submitAddProject'])) {
            throw new \Exception('Acceso no permitido', 403);
            exit;
        }

        usleep(500000);

        $title = Security::secure_input($params['titleProject']);
        $title = str_replace(' ', '-', $title);
        $desc = Security::secure_input($params['descProject']);

        $warningIcon = '<i class="fa fa-exclamation-circle"></i>';
        $okIcon = '<i class="fa fa-check" aria-hidden="true"></i>';
        $spinnerIcon = '<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>';

        if (empty($title)) {
            echo json_encode($warningIcon . ' Por favor, añade un título de proyecto');
        } elseif (empty($desc)) {
            echo json_encode($warningIcon . ' Por favor, añade una descripción de proyecto');
        } else {
            $model = new ProjectModel;
            $check = $model->checkProjectDB($title);
            if ($check) {
                echo json_encode($warningIcon . ' Título de proyecto ya existente');
            } else {
                $handle = new \Verot\Upload\Upload($_FILES['imageProject']);

                if ($handle->uploaded) {
                    $handle->dir_chmod = 0777;
                    $handle->file_new_name_body   = $title;
                    $handle->process('../uploads/user/' . $_SESSION['SESSEMAIL'] . '/projects');
                    if ($handle->processed) { // imagen subida a la  carpeta del servidor ok

                        $handle->clean();
                        $image = $title . '.' . $handle->file_src_name_ext;

                        $addProject = $model->addProjectDB($title, $desc, $image, $_SESSION['SESSID'], $_SESSION['SESSEMAIL'],);
                        $model->closeDB();

                        switch ($addProject) {
                            case 0:
                                echo json_encode($warningIcon . ' Error añadiendo nuevo proyecto');
                                break;
                            case 1:
                                echo json_encode(array($okIcon . ' Proyecto añadido correctamente ' . $spinnerIcon));
                                break;
                            default:
                                throw new \Exception('Error en la acción de registro', 500);
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
    }
    /*
    * ==========================================================
    *     EDIT PROJECT
    * ==========================================================
    */
    public function userEditProject($params)
    {
        if (!isset($params['submitEditProject'])) {
            throw new \Exception('Acceso no permitido', 403);
            exit;
        }

        usleep(500000);

        $title = Security::secure_input($params['etitleProject']);
        $title = str_replace(' ', '-', $title);
        $desc = Security::secure_input($params['eDescProject']);

        $warningIcon = '<i class="fa fa-exclamation-circle"></i>';
        $okIcon = '<i class="fa fa-check" aria-hidden="true"></i>';
        $spinnerIcon = '<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>';

        $model = new ProjectModel;
        // title project exists?
        $projectExists = false;
        $dataProjects = $model->dataUserProjectsDB($_SESSION['SESSID'], $_SESSION['SESSEMAIL']);
        for ($i = 0; $i < count($dataProjects[1]); $i++) {
            if ($dataProjects[1][$i]['title'] === $title) {
                $projectExists = true;
                break;
            }
        }
        if ($projectExists && !file_exists($_FILES['eImageProject']['tmp_name'])) {
            echo json_encode($warningIcon . ' Título de proyecto ya existente');
        } else {
            $dataProject = $model->dataUserProjectDB($_SESSION['SESSID'], $params['idProject']);
            if (!$dataProject) {
                throw new \Exception('Error en al conexion a la base de datos', 500);
                exit;
            } else {
                // get image from DB
                $imageDB = strrchr($dataProject['image'], '/');
                $imageDB = substr($imageDB, 1);
                $imageDB = explode('.', $imageDB);
                $titleImageDB = $imageDB[0];
                $extImageDB = $imageDB[1];
                // change $title in folder
                $path = '../uploads/user/' . $_SESSION['SESSEMAIL'] . '/projects';
                $it = new FilesystemIterator($path);

                // if exists image
                if (file_exists($_FILES['eImageProject']['tmp_name'])) {
                    // delete old image from folder
                    foreach ($it as $fileinfo) {
                        $image = explode('.', $fileinfo->getFilename());
                        if ($image[0] === $titleImageDB) {
                            unlink($path . '/' . $titleImageDB . '.' . $extImageDB);
                        }
                    }

                    $handle = new \Verot\Upload\Upload($_FILES['eImageProject']);

                    if ($handle->uploaded) {
                        $handle->dir_chmod = 0777;
                        $handle->file_new_name_body   = $title;
                        $handle->process('../uploads/user/' . $_SESSION['SESSEMAIL'] . '/projects');
                        if ($handle->processed) { // imagen subida a la  carpeta del servidor ok

                            $handle->clean();
                            $image = 'uploads/user/' . $_SESSION['SESSEMAIL'] . '/projects/' . $title . '.' . $handle->file_src_name_ext;

                            $editProject = $model->editProjectDB($title, $desc, $image, $_SESSION['SESSID'], $params['idProject']);
                            $model->closeDB();

                            switch ($editProject) {
                                case 0:
                                    echo json_encode($warningIcon . ' Error editando proyecto');
                                    break;
                                case 1:
                                    echo json_encode(array($okIcon . ' Proyecto editado correctamente ' . $spinnerIcon));
                                    break;
                                default:
                                    throw new \Exception('Error en la acción editar los datos de proyecto', 500);
                                    exit;
                                    break;
                            }
                        } else {
                            echo json_encode($warningIcon . ' error : ' . $handle->error);
                        }
                    } else {
                        echo json_encode($warningIcon . ' error : No se ha podido subir la imagen, inténtalo más tarde');
                    }
                } else { // no image
                    // get title old image and rename image to new title
                    foreach ($it as $fileinfo) {
                        $image = explode('.', $fileinfo->getFilename());
                        if ($image[0] === $titleImageDB) {
                            $oldPath = $path . '/' . $image[0] . '.' . $image[1];
                            $newPath = $path . '/' . $title . '.' . $image[1];
                            rename($oldPath, $newPath);
                        }
                    }
                    $image = substr($newPath, 3);
                    $editProject = $model->editProjectDB($title, $desc, $image, $_SESSION['SESSID'], $params['idProject']);
                    $model->closeDB();

                    switch ($editProject) {
                        case 0:
                            echo json_encode($warningIcon . ' Error editando proyecto');
                            break;
                        case 1:
                            echo json_encode(array($okIcon . ' Proyecto editado correctamente ' . $spinnerIcon));
                            break;
                        default:
                            throw new \Exception('Error en la acción editar los datos de proyecto', 500);
                            exit;
                            break;
                    }
                }
            }
        }
    }
    /*
    * ==========================================================
    *     DELETE PROJECT
    * ==========================================================
    */
    public function deleteProject($params)
    {
        if (!isset($params['title'])) {
            throw new \Exception('Acceso no permitido', 403);
            exit;
        }

        usleep(500000);

        $warningIcon = '<i class="fa fa-exclamation-circle"></i>';
        $okIcon = '<i class="fa fa-check" aria-hidden="true"></i>';
        $spinnerIcon = '<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>';

        $model = new ProjectModel;
        $delete = $model->delProjectDB($_SESSION['SESSID'], $params['title']);

        switch ($delete) {
            case 0:
                echo json_encode($warningIcon . ' Error eliminando proyecto');
                break;
            case 1:
                // borra imagen del proyecto
                $path = '../uploads/user/' . $_SESSION['SESSEMAIL'] . '/projects';
                $it = new FilesystemIterator($path);
                foreach ($it as $fileinfo) {
                    $image = explode('.', $fileinfo->getFilename());
                    if ($image[0] === $params['title']) {
                        $ext = $fileinfo->getExtension();
                    }
                }
                unlink($path . '/' . $params['title'] . '.' . $ext);
                echo json_encode(array($okIcon . ' Proyecto eliminado correctamente ' . $spinnerIcon));
                break;

            default:
                throw new \Exception('Error en la acción de borrar proyecto', 500);
                exit;
                break;
        }
    }
}
