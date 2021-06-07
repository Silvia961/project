<?php

//função para validar os campos de um post
function validatePost($post){
    
    $errors = array();
    $table = 'posts';

    if (empty($post['title'])){
        array_push($errors, 'O titulo não pode estar vazio');

    }

    if (empty($post['body'])){
        array_push($errors, 'O body não pode estar vazio');

    }

    if (empty($post['topic_id'])){
        array_push($errors, 'Escolha um topico');

    }


    $existingPost = selectOne($table,['title' => $post['title']]);
    if ($existingPost){
        if(isset($post['update-post']) && $existingPost['id'] != $post['id']){  //aqui significa que estamos a criar um post diferente com um titulo que já existe
            array_push($errors, 'O post com este titulo já existe ');
        }

        if(isset($post['add-post'])){ // se estamos a criar
            array_push($errors, 'O post com este titulo já existe ');
        }

    }

    return $errors;
}