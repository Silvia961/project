<?php
include(ROOT_PATH . "/app/database/db.php");
include(ROOT_PATH . "/app/helpers/middlewareHelper.php");
include(ROOT_PATH . "/app/helpers/userHelper.php");

$table= 'users';

//$admin_users = selectAll($table, ['is_admin' => 1]); //procurar todos os admins

$admin_users = selectAll($table); //procurar todos os users





//variaveis para manter os campos preenchidos caso houver algum erro nos campos.
$errors = array();
$username= '';
$id='';
$is_admin = '';
$email='';
$password = '';
$passwordConf = '';


//Função para fazer o redirect para o página inicial
function loginUser($user){

    $_SESSION['id'] = $user['id'];
    $_SESSION['nome_user'] = $user['nome_user'];
    $_SESSION['is_admin'] = $user['is_admin'];
    $_SESSION['message'] = "O login foi efetuado com sucesso ";
    $_SESSION['type'] = "success";

        if($_SESSION['is_admin']) {
            header('location:' . BASE_URL . '/admin/dashboard.php');
        }else{
            header('location:' . BASE_URL . '/index.php');
        }
        
    exit(); 

}

//Lidar com formulário de registo no Register.php e com o tabela para criar admins no admin panel
if (isset($_POST['register-btn']) || isset($_POST['create-admin'])) {
    $errors = validateUser($_POST);

    if (count($errors) === 0) {
        unset($_POST['register-btn'], $_POST['passwordConf'], $_POST['create-admin']);
        $_POST['password_user'] = password_hash($_POST['password_user'], PASSWORD_DEFAULT);
        
        if (isset($_POST['is_admin'])) {
            $_POST['is_admin'] = 1;
            $user_id = createRecord($table, $_POST);
            $_SESSION['message'] = 'Administrador criado com sucesso';
            $_SESSION['type'] = 'success';
            header('location: ' . BASE_URL . '/admin/users/indexUser.php'); 
            exit();
        } else {
            $_POST['is_admin'] = 0;
            $user_id = createRecord($table, $_POST);
            $user = selectOne($table, ['id' => $user_id]);
            loginUser($user);
        }
    } else {
        $username = $_POST['nome_user'];
        $is_admin = isset($_POST['is_admin']) ? 1 : 0;
        $email = $_POST['email_user'];
        $password = $_POST['password_user'];
        $passwordConf = $_POST['passwordConf'];
    }
}


//Editar um user
if(isset($_POST['update-user'])){
    adminOnly(); 
    $errors = validateUser($_POST);

    if (count($errors) === 0) {
        $id = $_POST['id']; 
        unset($_POST['passwordConf'],$_POST['update-user'],$_POST['id']);
        $_POST['password_user'] = password_hash($_POST['password_user'], PASSWORD_DEFAULT);


        $_POST['is_admin'] = isset($_POST['is_admin']) ? 1 : 0;
        $count = updateRecord ($table, $id, $_POST);
        $_SESSION['message'] = 'Utilizador atualizado com sucesso';
        $_SESSION['type'] = 'success';
        header('location: ' . BASE_URL . '/admin/users/indexUser.php'); 
        exit();
        
    } else {
        $username = $_POST['nome_user'];
        $is_admin = isset($_POST['is_admin']) ? 1 : 0;
        $email = $_POST['email_user'];
        $password = $_POST['password_user'];
        $passwordConf = $_POST['passwordConf'];
    }
    
}

//Popular o formulário do Edit Users
if(isset($_GET['id'])){

    $user = selectOne($table, ['id' => $_GET['id']]);
    $id = $user['id'];
    $username = $user['nome_user'];
    $is_admin = $user['is_admin'] == 1 ? 1 : 0;
    $email = $user['email_user'];
    
}


//Lidar com formulário de login no Login.php
if (isset($_POST['login-btn'])){

    $errors = validateLogin($_POST);

    if(count($errors) === 0){

        unset($_POST['login-btn']);
        $user = selectOne($table,['email_user' => $_POST['email_user']]);

        if($user && password_verify($_POST['password_user'], $user['password_user'])){  //return true | false
            //true, login e redirect
            loginUser($user);
        }else{
        //false   
            array_push($errors, 'Credênciais erradas');
        }

    }

    $email = $_POST['email_user'];
    
}


//APAGAR UM USER 
if(isset($_GET['delete_id'])){
    adminOnly(); 
    $count = deleteRecord($table, $_GET['delete_id']);
    $_SESSION['message'] = 'Administrador apagado com sucesso';
    $_SESSION['type'] = 'success';
    header('location: ' . BASE_URL . '/admin/users/indexUser.php'); 
    exit();


}
