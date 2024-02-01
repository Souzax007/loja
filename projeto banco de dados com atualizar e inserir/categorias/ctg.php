<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

include("../conexao/conexao.php");

$search = "";

if (isset($_GET["search"])) {
    $_SESSION["search_value"] = $_GET["search"];
    $search = " WHERE nm_categoria LIKE '%" . $_GET["search"] . "%' OR id_categoria = " . (int)$_GET["search"] . "";
} else {
    if (isset($_SESSION["search_value"])) {
        $search = " WHERE nm_categoria LIKE '%" . $_SESSION["search_value"] . "%' OR id_categoria = " . (int)$_SESSION["search_value"] . "";
    }
}

if (isset($_GET["show_all"])) {
    // If "TUDO" button is clicked, reset the search
    unset($_SESSION["search_value"]);
    $search = "";
}

$registros_por_pagina = 5;
$pagina_atual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
$offset = ($pagina_atual - 1) * $registros_por_pagina;

$sql = "SELECT id_categoria, nm_categoria FROM categorias $search LIMIT $registros_por_pagina OFFSET $offset";
$result = $conn->query($sql);

$total_registros_sql = "SELECT COUNT(id_categoria) AS total FROM categorias $search";
$total_result = $conn->query($total_registros_sql);
$total_registros = $total_result->fetch_assoc()['total'];

$total_paginas = ceil($total_registros / $registros_por_pagina);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorias</title>
    <link rel="stylesheet" href="../categorias/style.css">
</head>
<body>
    <div>
        <?php include("../nav/nav.php"); ?>
    </div>

    <div class="tabela">
        <div>
            <a href="ctg_form.php?id=0&acao=insert"><button type="submit">NOVO</button></a>
            <a href="ctg.php?show_all=1"><button type="submit">TUDO</button></a>
            <form action="ctg.php" method="GET" style="float:right;">
                <input name="search" type="text" placeholder="Pesquisar" style="width:150px" value="<?php echo isset($_SESSION["search_value"]) ? $_SESSION["search_value"] : ''; ?>">
                <button type="submit">BUSCAR</button>
            </form>
        </div>

        <table>
            <tr>
                <th>ID</th>
                <th>CATEGORIAS</th>
                <th>AÇÕES</th>
            </tr>

            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $id_categoria = $row["id_categoria"];
                    $nm_categoria = $row["nm_categoria"];
                    ?>
                    <tr>
                        <td><?php echo $id_categoria; ?></td>
                        <td><?php echo $nm_categoria; ?></td>
                        <td>
                            <a href="ctg_form.php?id=<?php echo $id_categoria; ?>&acao=upd"><button name="editar">Editar</button></a>
                            <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $id_categoria; ?>)"><button name="excluir">Excluir</button></a>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                echo "<tr><td colspan='3'>Nenhum resultado encontrado na tabela 'categorias'.</td></tr>";
            }
            ?>
        </table>

        <div class="pag">
            <nav aria-label="Navegação de página exemplo">
                <ul class="btn-paginacao-horizontal">
                    <?php if ($pagina_atual > 1) : ?>
                        <li class="paginacao"><a class="page-link" href="?pagina=<?php echo $pagina_atual - 1; ?>&search=<?php echo isset($_SESSION["search_value"]) ? $_SESSION["search_value"] : ''; ?>">Anterior</a></li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_paginas; $i++) : ?>
                        <li class="paginacao"><a class="page-link" href="?pagina=<?php echo $i; ?>&search=<?php echo isset($_SESSION["search_value"]) ? $_SESSION["search_value"] : ''; ?>"><?php echo $i; ?></a></li>
                    <?php endfor; ?>

                    <?php if ($pagina_atual < $total_paginas) : ?>
                        <li class="paginacao"><a class="page-link" href="?pagina=<?php echo $pagina_atual + 1; ?>&search=<?php echo isset($_SESSION["search_value"]) ? $_SESSION["search_value"] : ''; ?>">Próximo</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            var confirmDelete = confirm("Tem certeza que deseja excluir este registro?");
            
            if (confirmDelete) {
                // Se o usuário confirmar, redirecione para a página de exclusão
                window.location.href = "ctg_del.php?id=" + id;
            } else {
                // Se o usuário cancelar, não faça nada
            }
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
