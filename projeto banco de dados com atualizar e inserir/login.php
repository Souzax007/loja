<?php
include 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["email"]) && isset($_POST["senha"])) {
        $email = validarEntrada($_POST["email"]);
        $senha = validarEntrada($_POST["senha"]);

        $stmt = $mysqli->prepare("SELECT id_login, email, senha FROM login WHERE email = ? AND senha = ?");
        $stmt->bind_param("ss", $email, $senha);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $usuario = $result->fetch_assoc();

            session_start();
            $_SESSION['id_login'] = $usuario['id_login'];
            $_SESSION['email'] = $usuario['email'];

            header("Location: ./produtos/prod.php");
            exit();
        } else {
            echo "Falha ao logar! E-mail ou senha incorretos";
        }
    } else {
        echo "Campos de e-mail e senha não estão definidos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="screen">
            <div class="screen__content">
                <form class="login" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="login__field">
                        <i class="login__icon fas fa-user"></i>
                        <input type="text" name="email" class="login__input" placeholder="Email">
                    </div>
                    <div class="login__field">
                        <i class="login__icon fas fa-lock"></i>
                        <input type="password" name="senha" class="login__input" placeholder="Senha">
                    </div>
                    <input class="login__submit" type="submit" value="Enviar">
                </form>
                <div class="nome-dos-sistema">
                    <h3>LojaControl</h3>
                </div>
            </div>
            <div class="screen__background">
                <span class="screen__background__shape screen__background__shape4"></span>
                <span class="screen__background__shape screen__background__shape3"></span>
                <span class="screen__background__shape screen__background__shape2"></span>
                <span class="screen__background__shape screen__background__shape1"></span>
            </div>
        </div>
    </div>
</body>
</html>
