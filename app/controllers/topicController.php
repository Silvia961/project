<?php
include(ROOT_PATH . "/app/database/db.php");
include(ROOT_PATH . "/app/helpers/middlewareHelper.php");
include(ROOT_PATH . "/app/helpers/topicHelper.php");

$table = 'topics';

//variaveis para manter os campos preenchidos ao editar um topico.
$errors = array();
$id = '';
$name ='';
$description ='';

$topics = selectAll($table);


// SE O BOTÃO PARA CRIAR, *** CRIAR UM TOPICO  ***
if(isset($_POST['add-topic'])){
    adminOnly(); //OU UM AUTOR , NÃO ESTA DEFINIDO
    $errors = validateTopic($_POST);

    if (count($errors) === 0){

        unset($_POST['add-topic']);
        $_POST['description'] = htmlentities($_POST['description']);
        $topic_id = createRecord($table,$_POST);
        $_SESSION['message'] = 'Topico criado com sucesso';
        $_SESSION['type'] = 'success';
        header('location: ' . BASE_URL . '/admin/topics/indexTopic.php');
        exit();

    } else {
        //preservar os dados já introduzos nas caixas input caso aconteca algum erro
        $name = $_POST['name'];
        $description = $_POST['description'];

    }


}


// SE O BOTÃO DE UPDATE TOPICO FOI CARREGADO, *** EDITAR UM TOPICO ***
if(isset($_POST['update-topic'])){

    $errors = validateTopic($_POST);
    adminOnly(); //OU UM AUTOR , NÃO ESTA DEFINIDO
    if (count($errors) === 0){

        $id = $_POST['id']; //id do topico para a query
        unset($_POST['update-topic'], $_POST['id']);
        $_POST['description'] = htmlentities($_POST['description']);
        $topic_id = updateRecord($table, $id, $_POST); //$_POST = vai o id, o nome, e a descricão

        $_SESSION['message'] = 'O topico alterado com sucesso';
        $_SESSION['type'] = 'success';

        header('location: ' . BASE_URL . '/admin/topics/indexTopic.php');
        exit();

    }else{
        //preservar os dados já introduzos nas caixas input caso aconteca algum erro
        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = htmlentities($_POST['description']);


    }

    
    
 
}


//PARA PRESERVAR OS DADOS NO EDITAR, *** EDITAR UM TOPICO (HELPER) ***
if(isset($_GET['id'])){
       
    $id = $_GET['id'];
    $topic = selectOne($table, ['id' => $id]); 
    $id = $topic['id'];
    $name = $topic['name'];
    $description = $topic['description'];



}


// SE O BOTÃO PARA APAGAR *** APAGAR UM TOPICO ***
 if(isset($_GET['del_id'])){
    adminOnly(); //OU UM AUTOR , NÃO ESTA DEFINIDO
    $id = $_GET['del_id'];

    $count = deleteRecord($table,$id);

    $_SESSION['message'] = 'Topico apagado com sucesso';
    $_SESSION['type'] = 'success';

   header('location: ' . BASE_URL . '/admin/topics/indexTopic.php');
   exit();
   
}


