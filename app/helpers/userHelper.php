<?php

//função para validar os campos para registar um User
function validateUser($user){
    
    $errors = array();
    $table = 'users';

    if (empty($user['nome_user'])){
        array_push($errors, 'O campo nome não pode estar vazio');

    }

    if (empty($user['email_user'])){
        array_push($errors, 'O campo email não pode estar vazio');

    }

    if (empty($user['password_user'])){
        array_push($errors, 'O campo password não pode estar vazio');

    }

    if ($user['passwordConf'] !== $user['password_user']){
        array_push($errors, 'A password não é igual');

    }


    $existingUser = selectOne($table,['email_user' => $user['email_user']]);
    if ($existingUser){
        if(isset($user['update-user']) && $existingUser['id'] != $user['id']){  //No menu Edit, significa que estamos a criar um topic diferente com um titulo que já existe
            array_push($errors, 'O email já existe ');
        }

        if(isset($user['create-admin'])){ // se estamos a adicionar um novo topico , verifica se já existe algum topico existente
            array_push($errors, 'O email já existe ');
        }

    }

    return $errors;
}


function validateLogin($user){
    
    $errors = array();

    if (empty($user['email_user'])){
        array_push($errors, 'O campo email não pode estar vazio');

    }

    if (empty($user['password_user'])){
        array_push($errors, 'O campo password não pode estar vazio');

    }

    return $errors;
}