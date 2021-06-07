<?php

session_start();  //Muitas páginas usam esta, por isso tem sentido manter a session aqui
require('connect.php');




//exemplo array com todas as condicões para o SELECT
/*
$conditions = [
    'nome_user'=>'Pedro',
    'is_prof' => 0
];

//exemplo array para inserir um registo
$inserir = [
    'is_prof'=> 1,
    'nome_user'=>'PedroProf',
    'email_user'=>'ped@prof.com',
    'password_user'=>'12345'
];

//exemplo array para modificar um registo
$modificar = [
    'is_prof'=> 1,
    'nome_user'=>'Joao',
    'email_user'=>'ped@prof.com',
    'password_user'=>'12345'
];

//exemplo array para modificar um registo
$remover = [
    'id'=> 3
];
  
//exemplo para inserir um registo
$id = createRecord('users',$inserir);
dd($id);

//exemplo encontrar todos os registos, sem parametros
$users = selectAll('users');

//exemplo para encontrar todos os registos com parametros
$users = selectAll('users',$conditions);

//exemplo para encontrar um registo, não faz muito sentido receber os paremtros
$users = selectOne('users',$conditions);

//exemplo para modificar um registo
$modify = updateRecord('users', 3, $modificar);
dd($modify);


//exemplo para remover um registo
$remove = deleteRecord('users', 3);
dd($remove);

*/




//Função com select para ir buscar todos os resultados à bd, $table = a tabela que querermos, $conditions = as condições do select | **PODE SER USADA SEM O PARAMETRO $conditions**
function selectAll($table, $conditions =[]){

    global $conn;

    // vai buscar todos os users da tabela "users"
    $sql = "SELECT * FROM $table";

    if(empty($conditions)){
        //vai preparar a query sem parametros
        $stmt = $conn->prepare($sql);    
        $stmt->execute();
        $records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return $records;

        //verificar se existe registos 
       /* if ($records > 0){
            
        }else{
            echo "Não existem registos na tabela : ". $table;
        }*/

    }else{
        //return registos com condição
        $i = 0;
        foreach($conditions as $key => $value){ //foreach para gerar o select "completo" com todas as condições que forem definidas no array $conditions
            
            if($i === 0){
                $sql = $sql . " WHERE $key=?";
            }else{
                $sql = $sql . " AND $key=?";
            }

            $i++;
            
        }//foreach
    

        $stmt = executeQuery($sql,$conditions); //função para executar o SELECT, com os parametros

        $records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        //verificar se existe registos 
        if ($records > 0){
            return $records;
        }else{
            echo "Não existem registos na tabela : ". $table;
        }

    }

    
}

//Função com select que DEVOLVE UM RECORD. $table = a tabela que querermos, $conditions = as condições do select
function selectOne($table, $conditions){

    global $conn;

    // vai buscar todos os users da tabela "users"
    $sql = "SELECT * FROM $table";

    //return registos com condição
    $i = 0;
    foreach($conditions as $key => $value){ //foreach para gerar o select "completo" com todas as condições que forem definidas no array $conditions
        
        if($i === 0){
            $sql = $sql . " WHERE $key=?";
        }else{
            $sql = $sql . " AND $key=?";
        }

        $i++;
        
    }//foreach

    $sql = $sql . " LIMIT 1";

    $stmt = executeQuery($sql,$conditions);

    $records = $stmt->get_result()->fetch_assoc();

    return $records;
    
}

//Função para executar o SELECT com os parametros
function executeQuery($sql, $data){

    global $conn;

    $stmt = $conn->prepare($sql);
    $values = array_values($data);
    $types = str_repeat('s',count($values));
    $stmt->bind_param($types, ...$values);
    $stmt->execute();
    return $stmt;

}

//Função para criar um registo, devolve o ID do registo efetuado
function createRecord($table,$data){

   
    global $conn;
    //sql = INSERT INTO users SET nome_user=?, is_prof=?, email_user=?, password=?"
    $sql="INSERT INTO $table SET ";

    $i = 0;
    foreach($data as $key => $value){ //foreach para gerar o select "completo" com todas as condições que forem definidas no array $conditions
        if($i === 0){
            $sql = $sql . " $key=?";
        }else{
            $sql = $sql . ", $key=?";
        }
        $i++;
    }//foreach

   // dd($sql);
    dd($data);
    
    $stmt = executeQuery($sql, $data);
    $id = $stmt->insert_id;
    return $id;


}

//Função para atualizar um registo *** o "$id" é o id do registo
function updateRecord($table, $id, $data){

    global $conn;
    //sql = UPDATE users SET nome_user=?, is_prof=?, email_user=?, password=? WHERE id=?"
    $sql="UPDATE $table SET ";

    $i = 0;
    foreach($data as $key => $value){ //foreach para gerar o select "completo" com todas as condições que forem definidas no array $conditions
        if($i === 0){
            $sql = $sql . " $key=?";
        }else{
            $sql = $sql . ", $key=?";
        }
        $i++;
    }//foreach

    $sql = $sql . " WHERE id=?";
    $data['id'] = $id;    //$data[id] = vai adicionar uma linha ao array com os dados para alterar
    $stmt = executeQuery($sql, $data);
    return $stmt->affected_rows;   //boolean se 1 = Sucess | -1 = Failed

}

//Função para apagar um registo *** o "$id" é o id do registo
function deleteRecord($table, $id){

    global $conn;
    //sql = DELETE FROM users WHERE id=?"
    $sql="DELETE FROM $table WHERE id=?";
    $data['id'] = $id;    //$data[id] = vai adicionar uma linha ao array com os dados para alterar
    $stmt = executeQuery($sql, ['id' => $id]);   //o executeQuery() como segundo parametro, só aceita arrays associativos, por isso criamos um array so com $id
    return $stmt->affected_rows;   //boolean se 1 = Sucess | -1 = Failed

}


//Função para ir buscar todos os posts de uma pessoa (é o que esta aplicar no index, todos os posts que aparecem são do admin)
function getPublishedPosts(){

    global $conn;
    // SELECT * FROM posts WHERE published=1
    $sql = "SELECT p.*,u.nome_user FROM posts AS p JOIN users AS u ON p.user_id=u.id WHERE p.published=? ORDER BY p.created_at DESC";

    $stmt = executeQuery($sql,['published' => 1]); //função para executar o SELECT, com os parametros

    $records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    return $records;

}


//Função para procurar posts atráves de um search (pagina inicial => search )
function searchPosts($term){

    global $conn;
    $match = '%' . $term . '%';    
    // SELECT * FROM posts WHERE published=1
    $sql = "SELECT 
            p.*,u.nome_user 
            FROM posts AS p 
            JOIN users AS u 
            ON p.user_id=u.id 
            WHERE p.published=?
            AND p.title LIKE ? OR p.body LIKE ? ORDER BY created_at DESC";

    $stmt = executeQuery($sql,['published' => 1, 'title' => $match, 'body' => $match]); //função para executar o SELECT, com os parametros

    $records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    return $records;

}


//Função para ir buscar todos os posts atráves de um topico
function getPostsByTopicId($topic_id){

    global $conn;
    // SELECT * FROM posts WHERE published=1
    $sql = "SELECT p.*,u.nome_user FROM posts AS p JOIN users AS u ON p.user_id=u.id WHERE p.published=? AND topic_id=? ORDER BY p.created_at DESC";

    $stmt = executeQuery($sql,['published' => 1, 'topic_id' => $topic_id]); //função para executar o SELECT, com os parametros

    $records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    return $records;

}


//Função para ir buscar os posts com o id do autor
function getPostsWithUser(){

    global $conn;
    // SELECT * FROM posts WHERE published=1
    $sql = "SELECT p.*,u.nome_user FROM posts AS p JOIN users AS u ON p.user_id=u.id  ORDER BY p.created_at DESC";
    $stmt = $conn->prepare($sql);    
    $stmt->execute();
    $records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    return $records;
    
}




//Funcão para imprimir valores util para debbug  vai ser apagada mais tarde
function dd($value){  
 
    echo"<pre>", print_r($value, true),"</pre>";  //o TRUE é para fazer desaparecer o numero que parece no fim do array
    
    
}








