<?php

class Login
{
   
//objeto var La conexión de la base de datos

    private $db_connection = null;
    
   // Colección de mensajes de error
    public $errors = array();
    
     // Matriz Colección de mensajes de éxito
    public $messages = array();

     //la función "__construct ()" se inicia automáticamente cada vez que se crea un objeto de esta clase,
    public function __construct()
    {
        // crear / leer sesión
        session_start();

        // verifique las posibles acciones de inicio de sesión:
        // si el usuario intentó cerrar sesión (sucede cuando el usuario hace clic en el botón de cerrar sesión)
        if (isset($_GET["logout"])) {
            $this->doLogout();
        }
        // iniciar sesión a través de datos de publicación (si el usuario acaba de enviar un formulario de inicio de sesión)
        elseif (isset($_POST["login"])) {
            $this->dologinWithPostData();
        }
    }

    private function dologinWithPostData()
    {
        // check login form contents
        if (empty($_POST['user_name'])) {
            $this->errors[] = "Campo usuario Vacio.";
        } elseif (empty($_POST['user_password'])) {
            $this->errors[] = "Campo password Vacio.";
        } elseif (!empty($_POST['user_name']) && !empty($_POST['user_password'])) {

            // crear una conexión de base de datos, usando las constantes de config / db.php (que cargamos en index.php)
            $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

            // cambie a utf8 y verifíca
            if (!$this->db_connection->set_charset("utf8")) {
                $this->errors[] = $this->db_connection->error;
            }

            // si no hay errores de conexión
            if (!$this->db_connection->connect_errno) {

                
                $user_name = $this->db_connection->real_escape_string($_POST['user_name']);

                // consulta de la base de datos.
                $sql = "SELECT user_id, user_name, firstname, user_email, user_password_hash
                        FROM users
                        WHERE user_name = '" . $user_name . "' OR user_email = '" . $user_name . "';";
                $result_of_login_check = $this->db_connection->query($sql);

                // si usuario existe
                if ($result_of_login_check->num_rows == 1) {

                    // obtener fila de resultados
                    $result_row = $result_of_login_check->fetch_object();

                    // verificar si el passwd se ajusta al hash.
                    if (password_verify($_POST['user_password'], $result_row->user_password_hash)) {

                        // Escribe los datos del usuario en ;a sesion de php en elservidor.
                        $_SESSION['user_id'] = $result_row->user_id;
			$_SESSION['firstname'] = $result_row->firstname;
			$_SESSION['user_name'] = $result_row->user_name;
                        $_SESSION['user_email'] = $result_row->user_email;
                        $_SESSION['user_login_status'] = 1;

                    } else {
                        $this->errors[] = "Usuario y/o contraseña no coinciden.";
                    }
                } else {
                    $this->errors[] = "Usuario y/o contraseña no coinciden.";
                }
            } else {
                $this->errors[] = "Problema de conexión de base de datos.";
            }
        }
    }

    public function doLogout()
    {
        // elimina y destruye la sesion del usuario.
        $_SESSION = array();
        session_destroy();
        $this->messages[] = "Ha cerrado sesion.";

    }

    /**
     * devuelve el estado actual de sesion del usuario.
     */
    public function isUserLoggedIn()
    {
        if (isset($_SESSION['user_login_status']) AND $_SESSION['user_login_status'] == 1) {
            return true;
        }
        return false;
    }
}
