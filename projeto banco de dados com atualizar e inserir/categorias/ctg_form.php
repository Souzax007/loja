<?php

include("../conexao/conexao.php");


// Certifique-se de que o 'id' está definido antes de usá-lo
$id = (isset($_GET["id"])) ? intval($_GET["id"]) : 0;

// Sanitização da ação
$acao = (isset($_POST['act'])) ? $_POST["act"] : (isset($_GET["acao"]) ? $_GET["acao"] : "");

//Se o formulário for enviado, executar as ações
if($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($acao === "insert") {
        // Sanitização dos dados
        $var_nm_categoria = (isset($_POST["nm_categoria"])) ? mysqli_real_escape_string($conn, $_POST["nm_categoria"]) : "";

        // Verifique se o campo não está vazio antes de inserir
        if (!empty($var_nm_categoria)) {
            // SQL preparado
            $sql_insert = "INSERT INTO categorias (nm_categoria) VALUES (?)";
            $stmt = $conn->prepare($sql_insert);
            $stmt->bind_param("s", $var_nm_categoria);

            if ($stmt->execute()) {
                echo "Registro inserido com sucesso.";
            } else {
                echo "Erro ao inserir os dados: " . $stmt->error;
            }
        } else {
            echo "O campo de categoria não pode estar vazio.";
        }
    } elseif ($acao === "upd") {
        if (!is_numeric($id) || $id <= 0) {
            echo "ID inválido.";
        } else {
            // Sanitização dos dados
            $var_nm_categoria = (isset($_POST["nm_categoria"])) ? mysqli_real_escape_string($conn, $_POST["nm_categoria"]) : "";

            // Verifique se o campo não está vazio antes de atualizar
            if (!empty($var_nm_categoria)) {
                // SQL preparado
                $sql_upd = "UPDATE categorias SET nm_categoria = ? WHERE id_categoria = ?";
                $stmt = $conn->prepare($sql_upd);
                $stmt->bind_param("si", $var_nm_categoria, $id);

                if ($stmt->execute()) {
                    echo "Registro atualizado com sucesso!";
                } else {
                    echo "Erro na atualização: " . $stmt->error;
                }
            } else {
                echo "O campo de categoria não pode estar vazio.";
            }
        }
    }
}

// Consulta SQL usando instrução preparada
if(is_numeric($id) && $id > 0) {
    $sql = "SELECT id_categoria, nm_categoria FROM categorias WHERE id_categoria = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_categoria = $row["id_categoria"];
        $nm_categoria = $row["nm_categoria"];
    } else {
        $id_categoria = "";
        $nm_categoria = "";
        echo "Nenhum registro encontrado com ID: $id";
    }
}else{
    $id_categoria = "";
    $nm_categoria = "";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="../categorias/style.css">
</head>
<body>
    <div>
        <a href="../categorias/ctg.php"><button><< Voltar</button></a>

        <form action="ctg_form.php?id=<?php echo $id;?>" method="POST">
            <h1><?php if($acao=="insert") { echo "Inserir"; }else{ echo "Atualizar"; }?> categoria</h1>
            
            <label for="nm_categoria">Nome:</label>
            <input type="text" name="nm_categoria" value="<?php echo $nm_categoria;?>">
            <input type="hidden" name="id" value="<?php echo $id_categoria;?>">
            <input type="hidden" name="act" value="<?php echo $acao;?>">
            <br>
            <button name >SALVAR</button>
        </form>
    </div>
</body>
</html>
<?php $conn->close(); ?>