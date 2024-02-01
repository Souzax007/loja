<?php
include("../conexao/conexao.php");
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .success-message {
            color: green;
        }

        .error-message {
            color: red;
        }

        .warning-message {
            color: black;
        }
    </style>
</head>
<body>

<?php
if (!is_numeric($_GET["id"])) {
    echo '<p class="error-message">ID inválido.</p>';
} else {

    $id = (int)$_GET["id"];
    //SQL p/ retorno do nome da marca
    $sql_nome_categoria = "SELECT nm_categoria FROM categorias WHERE id_categoria = ".$id."";
    $result_nome_categoria = $conn->query($sql_nome_categoria);
    $row_nm_categoria = $result_nome_categoria->fetch_assoc();
    $nm_categoria = $row_nm_categoria["nm_categoria"];

    // Verifica se o botão mestre foi pressionado
    $isMasterDelete = isset($_GET["master"]) && $_GET["master"] === "true";

    // Verificar se existem produtos associados à marca
    if (!$isMasterDelete) {
        $sql_count_produtos = "SELECT COUNT(*) AS total_produtos FROM produtos WHERE id_categoria = ?";
        $stmt_count = $conn->prepare($sql_count_produtos);
        $stmt_count->bind_param("i", $id);
        $stmt_count->execute();
        $result_count = $stmt_count->get_result();
        $row_count = $result_count->fetch_assoc();
        $total_produtos = $row_count['total_produtos'];

        if ($total_produtos > 0) {
            echo '<p class="warning-message">Não é possível excluir a marca. Existem produtos associados a ela.</p>';
            // Adicione aqui o código para exibir o botão mestre
            echo '<button onclick="confirmMasterDelete()">Visializar</button>';
        }
    }

    if ($isMasterDelete || $total_produtos == 0) {
        // Não há produtos associados ou o botão mestre foi pressionado, então podemos excluir a marca
        $sql_delete = "DELETE FROM categorias WHERE id_categoria = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("i", $id);

        if ($stmt_delete->execute()) {
            echo '<p class="success-message">Marca excluída com sucesso.</p>';
            header("Location:ctg.php");
        } else {
            echo '<p class="error-message">Erro ao excluir a marca: ' . $stmt_delete->error . '</p>';
        }
    }
}

?>

<script>
    function confirmMasterDelete() {
        //if (confirm("Tem certeza de que deseja excluir esta marca, incluindo todos os produtos associados?")) {
            window.location.href = "../produtos/prod.php?search=<?php echo $nm_categoria; ?>&master=true";
        //}
    }
</script>

</body>
</html>
