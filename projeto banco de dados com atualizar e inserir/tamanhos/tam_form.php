<?php
include("../conexao/conexao.php");

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

$acao = isset($_POST['act']) ? $_POST["act"] : (isset($_GET["acao"]) ? $_GET["acao"] : "");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($acao === "insert") {
        handleInsert();
    } elseif ($acao === "upd") {
        handleUpdate($id);
    }
}

if (is_numeric($id) && $id > 0) {
    $tamData = getTamData($conn, $id);

    if ($tamData) {
        extract($tamData);
    } else {
        $id_tam = "";
        $nm_tam = "";
        $ds_status = "";
        echo "Nenhum registro encontrado com ID: $id";
    }
} else {
    $id_tam = "";
    $nm_tam = "";
    $ds_status = "";
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
        <a href="../tamanhos/tam.php"><button>Voltar</button></a>

        <form action="tam_form.php?id=<?php echo $id; ?>" method="POST">
            <h1><?php echo ($acao == "insert") ? "Inserir" : "Atualizar"; ?> tamanhos</h1>

            <label for="nm_tam">Nome:</label>
            <input type="text" name="nm_tam" value="<?php echo $nm_tam; ?>">
            <input type="hidden" name="id" value="<?php echo $id_tam; ?>">
            <input type="hidden" name="act" value="<?php echo $acao; ?>">

            <?php if ($acao === "upd"): ?>
                <label for="ds_status">Status:</label>
                <select name="ds_status" id="ds_status">
                    <option value="Ativo" <?php echo ($ds_status === "Ativo") ? "selected" : ""; ?>>Ativo</option>
                    <option value="Inativo" <?php echo ($ds_status === "Inativo") ? "selected" : ""; ?>>Inativo</option>
                </select>
            <?php endif; ?>

            <br>
            <button type="submit" name="salvar">SALVAR</button>
        </form>
    </div>
</body>

</html>

<?php
$conn->close();

function handleInsert() {
    global $conn;

    $var_nm_tam = isset($_POST["nm_tam"]) ? mysqli_real_escape_string($conn, $_POST["nm_tam"]) : "";

    if (!empty($var_nm_tam)) {
        $sql_insert = "INSERT INTO tamanhos (nm_tam) VALUES (?)";
        $stmt = $conn->prepare($sql_insert);
        $stmt->bind_param("s", $var_nm_tam);

        if ($stmt->execute()) {
            echo "Registro inserido com sucesso.";
        } else {
            echo "Erro ao inserir os dados: " . $stmt->error;
        }
    } else {
        echo "O campo de categoria não pode estar vazio.";
    }
}

function handleUpdate($id) {
    global $conn;

    if (!is_numeric($id) || $id <= 0) {
        echo "ID inválido.";
        return;
    }

    $var_nm_tam = isset($_POST["nm_tam"]) ? mysqli_real_escape_string($conn, $_POST["nm_tam"]) : "";
    $var_ds_status = isset($_POST["ds_status"]) ? mysqli_real_escape_string($conn, $_POST["ds_status"]) : "";

    if (!empty($var_nm_tam)) {
        $sql_upd = "UPDATE tamanhos SET nm_tam = ?, ds_status = ? WHERE id_tam = ?";
        $stmt = $conn->prepare($sql_upd);
        $stmt->bind_param("ssi", $var_nm_tam, $var_ds_status, $id);

        if ($stmt->execute()) {
            echo "Registro atualizado com sucesso!";
        } else {
            echo "Erro na atualização: " . $stmt->error;
        }
    } else {
        echo "O campo de categoria não pode estar vazio.";
    }
}

function getTamData($conn, $id) {
    $sql = "SELECT id_tam, nm_tam, ds_status FROM tamanhos WHERE id_tam = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    return ($result->num_rows > 0) ? $result->fetch_assoc() : null;
}
?>
