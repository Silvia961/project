<?php

function usersOnly($redirect = '/index.php'){

    if(empty($_SESSION['id'])){

        $_SESSION['message'] = 'Precisa primeiro de estar autenticado';
        $_SESSION['type'] = 'error';
        header('location: ' . BASE_URL . $redirect);
        exit();
    }


}


function adminOnly($redirect = '/index.php'){

    if(empty($_SESSION['id']) || empty($_SESSION['is_admin'])){

        $_SESSION['message'] = 'Não está autorizado ';
        $_SESSION['type'] = 'error';
        header('location: ' . BASE_URL . $redirect);
        exit();
    }


}


function guestsOnly($redirect = '/index.php'){

    if(isset($_SESSION['id'])){
        header('location: ' . BASE_URL . $redirect);
        exit();
    }


}