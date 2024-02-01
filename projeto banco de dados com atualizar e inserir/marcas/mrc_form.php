<?php
include("../conexao/conexao.php");

$id = (isset($_GET["id"])) ? intval($_GET["id"]) : 0;

$acao = (isset($_POST['act'])) ? $_POST["act"] : (isset($_GET["acao"]) ? $_GET["acao"] : "");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($acao === "insert") {
        $var_nm_marca = (isset($_POST["nm_marca"])) ? mysqli_real_escape_string($conn, $_POST["nm_marca"]) : "";


        if (!empty($var_nm_marca)) {
            $sql_insert = "INSERT INTO marcas (nm_marca) VALUES (?)";
            $stmt = $conn->prepare($sql_insert);
            $stmt->bind_param("s", $var_nm_marca);

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
            $var_nm_marca = (isset($_POST["nm_marca"])) ? mysqli_real_escape_string($conn, $_POST["nm_marca"]) : "";
           
            if (!empty($var_nm_marca)) {
                $sql_upd = "UPDATE marcas SET nm_marca = ? WHERE id_marca = ?";
                $stmt = $conn->prepare($sql_upd);
                $stmt->bind_param("si", $var_nm_marca, $id);

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

if (is_numeric($id) && $id > 0) {
    $sql = "SELECT id_marca, nm_marca FROM marcas WHERE id_marca = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_marca = $row["id_marca"];
        $nm_marca = $row["nm_marca"];
    } else {
        $id_marca = "";
        $nm_marca = "";
        echo "Nenhum registro encontrado com ID: $id";
    }
} else {
    $id_marca = "";
    $nm_marca = "";
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Título da Página</title>
    <link rel="stylesheet" href="../categorias/style.css">
</head>

<body>
    <div>
        <a href="../marcas/mrc.php"><button><< Voltar</button></a>

        <form action="mrc_form.php?id=<?php echo $id; ?>" method="POST">
            <h1><?php if ($acao == "insert") {
                    echo "Inserir";
                } else {
                    echo "Atualizar";
                } ?> marcas</h1>

            <label for="nm_marca">Nome:</label>
            <input type="text" name="nm_marca" value="<?php echo $nm_marca; ?>">

            <input type="hidden" name="id" value="<?php echo $id_marca; ?>">
            <input type="hidden" name="act" value="<?php echo $acao; ?>">
            <br>
            <button type="submit" name="salvar">SALVAR</button>
        </form>
    </div>
</body>

</html>

<?php $conn->close(); ?>