Dijiste:
<?php
session_start();//manejo de sesiones
require_once '../config/database.php';
require_once '../models/User.php';
//Decodificacion de Json recibido
$data = json_decode(file_get_contents("php://input"), true);

//Revisamos si vienen los campos necesarios
if (!isset($data['email']) || !isset($data['password'])) {
    echo json_encode([
        "success" => false,
        "message" => "Datos incompletos."
    ]);
    exit;
}

// Extraemos los datos del request
$email = $data['email'];
$password = $data['password'];
$nombre = isset($data['nombre']) ? $data['nombre']: null; //El nombre podria no venir del login

// Creamos un objeto de usuario
$user = new User($nombre, $email, $password);

//Es un registro o un inicio de sesion?
if ($nombre !== null) {
    //Registro

    
// Verificamos si el email ya está registrado
    if ($user->findByEmail($email)) {
    echo json_encode([
        "success" => false,
        "message" => "El correo electrónico ya está registrado"
    ]);
    exit;
}

    // 👤 IMAGEN DE PERFIL
    $uploadDir = '../uploads/';
    $defaultImage = $uploadDir . 'default.png';
    $profileImagePath = $defaultImage;

    // Si se está usando formulario con enctype multipart/form-data, puedes usar $_FILES
    if (!empty($_FILES['imagen_perfil']) && $_FILES['imagen_perfil']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['imagen_perfil']['tmp_name'];
        $imageName = basename($_FILES['imagen_perfil']['name']);
        $ext = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($ext, $allowed)) {
            $newName = uniqid('img_') . '.' . $ext;
            $dest = $uploadDir . $newName;

            if (move_uploaded_file($tmpName, $dest)) {
                $profileImagePath = $dest;
            }
        }
    }


// Si no está registrado, intentamos crear el usuario
if ($user->create($data)) {
    echo json_encode([
        "success" => true,
        "message" => "Registro exitoso.",
        "redirect" => true,
        "url" => "../frontend/login.html"  // redirigir al login
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Error al registrar usuario. Intente nuevamente."
    ]);
}
}
else {
    //Login
    $usuarioEncontrado = $user->findByEmail($email);

    if ($usuarioEncontrado && password_verify($password, $usuarioEncontrado['password'])){
        //Password correcto, iniciar sesion.
        $_SESSION['usuario_email'] = $email;

        echo json_encode([
            "success" => true,
            "message" => "Inicio de sesión exitoso.",
            "redirect" => true,
            "url" => "../../frontend/index.html" //Redirige al dashboard o home
        ]);
    }
    else {
        echo json_encode([
            "success" => false,
            "message" => "Email o contraseña incorrectos."
        ]);
    }
}
?>