<?php
// Set up error reporting and session
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Include database connection
include("../conexao/conexao.php");

// Handle search
$search = "";
if (isset($_GET["search"])) {
    $_SESSION["search_value"] = $_GET["search"];
    $search = " WHERE nm_tam LIKE '%" . $_GET["search"] . "%' OR id_tam = " . (int) $_GET["search"];
} elseif (isset($_SESSION["search_value"])) {
    $search = " WHERE nm_tam LIKE '%" . $_SESSION["search_value"] . "%' OR id_tam = " . (int) $_SESSION["search_value"];
}

// Reset search if "TUDO" button is clicked
if (isset($_GET["show_all"])) {
    unset($_SESSION["search_value"]);
    $search = "";
}

// Pagination settings
$registros_por_pagina = 5;
$pagina_atual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
$offset = ($pagina_atual - 1) * $registros_por_pagina;

// Fetch data for the main table
$sql = "SELECT id_tam, nm_tam, ds_status FROM tamanhos $search LIMIT $registros_por_pagina OFFSET $offset";
$result = $conn->query($sql);

// Fetch total number of records for pagination
$total_registros_sql = "SELECT COUNT(id_tam) AS total FROM tamanhos $search";
$total_result = $conn->query($total_registros_sql);
$total_registros = $total_result->fetch_assoc()['total'];

// Calculate total pages for pagination
$total_paginas = ceil($total_registros / $registros_por_pagina);

// Close the connection to the database
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tamanhos</title>
    <link rel="stylesheet" href="../categorias/style.css">
    <!-- Adicione o script jQuery abaixo do seu formulário HTML -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function () {
            // Adicione um ouvinte de evento para detectar mudanças no valor do select
            $("#ds_status").change(function () {
                // Obtenha o valor selecionado
                var novoStatus = $(this).val();

                // Faça uma requisição AJAX para o script PHP que atualiza o banco de dados
                $.ajax({
                    type: "POST",
                    url: "atualizar_status.php", // Substitua pelo caminho do seu script PHP
                    data: { id_tam: <?= $id_tam; ?>, novoStatus: novoStatus }, // Envie os dados necessários
                    success: function (response) {
                        // Lide com a resposta (opcional)
                        console.log(response);
                    },
                    error: function (xhr, status, error) {
                        // Lide com erros (opcional)
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>
</head>

<body>
    <div>
        <?php include("../nav/nav.php"); ?>
    </div>

    <div class="tabela">
        <div>
            <a href="tam_form.php?id=0&acao=insert"><button type="submit">NOVO</button></a>
            <a href="tam.php?show_all=1"><button type="submit">TUDO</button></a>
            <form action="tam.php" method="GET" style="float:right;">
                <input name="search" type="text" placeholder="Pesquisar" style="width:150px"
                    value="<?= isset($_SESSION["search_value"]) ? $_SESSION["search_value"] : ''; ?>">
                <button type="submit">BUSCAR</button>
            </form>
        </div>

        <table>
            <tr>
                <th>ID</th>
                <th>Tamanhos</th>
                <th>AÇÕES</th>
                <th>STATUS</th>
            </tr>

            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $id_tam = $row["id_tam"];
                    $nm_tam = $row["nm_tam"];
                    $ds_status = $row["ds_status"];
                    ?>
                    <tr>
                        <td>
                            <?= $id_tam; ?>
                        </td>
                        <td>
                            <?= $nm_tam; ?>
                        </td>
                        <td><a href="tam_form.php?id=<?= $id_tam; ?>&acao=upd"><button name="editar">Editar</button></a></td>
                        <td><?php echo $ds_status; ?>
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
                    <?php if ($pagina_atual > 1): ?>
                        <li class="paginacao"><a class="page-link"
                                href="?pagina=<?= $pagina_atual - 1; ?>&search=<?= isset($_SESSION["search_value"]) ? $_SESSION["search_value"] : ''; ?>">Anterior</a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <li class="paginacao"><a class="page-link"
                                href="?pagina=<?= $i; ?>&search=<?= isset($_SESSION["search_value"]) ? $_SESSION["search_value"] : ''; ?>">
                                <?= $i; ?>
                            </a></li>
                    <?php endfor; ?>

                    <?php if ($pagina_atual < $total_paginas): ?>
                        <li class="paginacao"><a class="page-link"
                                href="?pagina=<?= $pagina_atual + 1; ?>&search=<?= isset($_SESSION["search_value"]) ? $_SESSION["search_value"] : ''; ?>">Próximo</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</body>

</html>
