<?php
include("../conexao/conexao.php");

if (!is_numeric($_GET["id"])) {
    echo "ID inválido.";
} else {
    $id = (int)$_GET["id"];

        // Excluir a marca
        $sql_delete_marca = "DELETE FROM marcas WHERE id_marca = ?";
        $stmt_delete_marca = $conn->prepare($sql_delete_marca);
        $stmt_delete_marca->bind_param("i", $id);

        if ($stmt_delete_marca->execute()) {
            echo "Marca e produtos associados excluídos com sucesso.";
            header("Location: mrc.php");
        } else {
            echo "Erro ao excluir a marca: " . $stmt_delete_marca->error;
        }

}
?>