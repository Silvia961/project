<?php
include(ROOT_PATH . "/app/database/db.php");
include(ROOT_PATH . "/app/helpers/middlewareHelper.php");
include(ROOT_PATH . "/app/helpers/postHelper.php");


$table = 'posts';

$topics = selectAll('topics');  //Ir buscar todos os topicos para "popular" o createPost na selectionBox
//$posts = selectAll($table);
$posts = getPostsWithUser($table);

$errors = array();
$id ='';
$title ='';
$body ='';
$topic_id ='';
$published ='';

// receber os dados para popular o menu de editar
if(isset($_GET['id'])){

    $post = selectOne($table, ['id' => $_GET['id']]);
    $id = $post['id'];
    $title = $post['title'];
    $body =$post['body'];
    $topic_id =$post['topic_id'];
    $published =$post['published'];

}


// *** APAGAR UM POST ***
if(isset($_GET['delete_id'])){
    adminOnly();//OU UM AUTOR , NÃO ESTA DEFINIDO
    $count = deleteRecord($table,$_GET['delete_id']);
    $_SESSION['message'] = "Post apagado com sucesso";
    $_SESSION['type'] = "success";
    header('location:' . BASE_URL . "/admin/posts/indexPost.php");
    exit();

}


//
if(isset($_GET['published']) && isset($_GET['p_id'])){
    adminOnly(); //OU UM AUTOR , NÃO ESTA DEFINIDO

    $published = $_GET['published'];
    $p_id = $_GET['p_id']; // ID DO POST PARA ALTERAR ENTRE PUBLISHED E UNPUBLISHED

    //...update published
    $count = updateRecord($table,$p_id,['published' => $published]);
    
    $_SESSION['message'] = "O estado do post foi alterado";
    $_SESSION['type'] = "success";
    header('location:' . BASE_URL . "/admin/posts/indexPost.php");
    exit();


}


// SE O BOTÃO PARA CRIAR BUTTON, *** CRIAR UM POST  ***
if(isset($_POST['add-post'])){
    adminOnly(); //OU UM AUTOR , NÃO ESTA DEFINIDO
    //dd($_FILES['image']);                         DEBBUG HELPERS
    //dd($_FILES['image']['name']);                 DEBBUG HELPERS
    $errors = validatePost($_POST);

    if (!empty($_FILES['image']['name'])) {

        $image_name = time() . '_' . $_FILES['image']['name'];
        $destination = ROOT_PATH . "/assets/images/" . $image_name;

        $result = move_uploaded_file($_FILES['image']['tmp_name'], $destination);

        if ($result) {
           $_POST['image'] = $image_name;
        } else {
            array_push($errors, "O upload da imagem falhou");
        }

    } else {
       array_push($errors, "É preciso escolher uma imagem"); // apagar isto , para a imagem nao ser obrigatoria
    }

    if(count($errors) == 0){

        unset($_POST['add-post']);

        $_POST['user_id'] = $_SESSION['id'];  // para saber quem esta a criar o post 1 - admin | 0 - user
        $_POST['published'] = isset($_POST['published']) ? 1 : 0 ;  //verifica se a opção published foi selecionada ou não , 1 - sim | 0 - não
        $_POST['body'] = htmlentities($_POST['body']); //htmlentities serve para encriptar as tags do HMTL quando enviado para a bd , (evitar xss - cross site scripting)


        $post_id = createRecord($table, $_POST);
        $_SESSION['message'] = "Post criado com sucesso";
        $_SESSION['type'] = "success";
        header('location:' . BASE_URL . "/admin/posts/indexPost.php");

    } else {

        $title = $_POST['title'];
        $body = $_POST['body'];
        $topic_id = $_POST['topic_id'];
        $published = isset($_POST['published']) ? 1 : 0 ;

    }
    

}


//  *** EDITAR UM POST BUTTON  ***
if(isset($_POST['update-post'])){
    adminOnly(); //OU UM AUTOR , NÃO ESTA DEFINIDO
    $errors = validatePost($_POST);

    if (!empty($_FILES['image']['name'])) {

        $image_name = time() . '_' . $_FILES['image']['name'];
        $destination = ROOT_PATH . "/assets/images/" . $image_name;

        $result = move_uploaded_file($_FILES['image']['tmp_name'], $destination);

        if ($result) {
           $_POST['image'] = $image_name;
        } else {
            array_push($errors, "O upload da imagem falhou");
        }

    } else {
       array_push($errors, "É preciso escolher uma imagem"); // apagar isto , para a imagem nao ser obrigatoria
    }


    if(count($errors) == 0){

        $id = $_POST['id'];
        unset($_POST['update-post'], $_POST['id']);

        $_POST['user_id'] = $_SESSION['id'];
        $_POST['published'] = isset($_POST['published']) ? 1 : 0 ;  //verifica se a opção published foi selecionada ou não , 1 - sim | 0 - não
        $_POST['body'] = htmlentities($_POST['body']); //htmlentities serve para encriptar as tags do HMTL quando enviado para a bd , (evitar xss - cross site scripting)


        $post_id = updateRecord($table,$id,$_POST);
        $_SESSION['message'] = "Post editado com sucesso";
        $_SESSION['type'] = "success";
        header('location:' . BASE_URL . "/admin/posts/indexPost.php");
        exit();

    } else {

        $title = $_POST['title'];
        $body = $_POST['body'];
        $topic_id = $_POST['topic_id'];
        $published = isset($_POST['published']) ? 1 : 0 ;

    }




}


