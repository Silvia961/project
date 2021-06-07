<?php

//função para validar os campos para registar um Topic
function validateTopic($topic){
    
    $errors = array();
    $table = 'topics';

    if (empty($topic['name'])){
        array_push($errors, 'O nome do topico não pode ficar vazio');

    }


    $existingTopic = selectOne($table,['name' => $topic['name']]);
    if ($existingTopic){
        if(isset($topic['update-topic']) && $existingTopic['id'] != $topic['id']){  //No menu Edit, significa que estamos a criar um topic diferente com um titulo que já existe
            array_push($errors, 'O topico já existe ');
        }

        if(isset($topic['add-topic'])){ // se estamos a adicionar um novo topico , verifica se já existe algum topico existente
            array_push($errors, 'O topico já existe ');
        }

    }
    

    return $errors;
}


