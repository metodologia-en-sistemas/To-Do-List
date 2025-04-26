<?php 
require_once './controllers/UserController.php';


$controller = new UserController();

if ($_POST['action'] === 'register') {
    $response = $controller->register($_POST);
    echo json_encode($response);
}
if ($_POST['action'] === 'login') {
    $response = $controller->login($_POST['email'], $_POST['password']);
    echo json_encode($response);
}
if ($_POST['action'] === 'login') {
    $response = $controller->login($_POST['email'], $_POST['password']);
    echo json_encode($response);
}
if ($_POST['action'] === 'logout') {
    $response = $controller->logout();
    echo json_encode($response);
}
?>