<?php
include("../conexao/conexao.php");

if (!is_numeric($_GET["id"])) {
    echo "ID inválido.";
} else {
    $id = (int)$_GET["id"];
    // Executar a lógica de exclusão
    $sql_delete = "DELETE FROM categorias WHERE id_categoria = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        //echo "Registro excluído com sucesso.";
        header("Location:ctg.php");
    } else {
        echo "Erro ao excluir o registro: " . $stmt->error;
    }
}
?>