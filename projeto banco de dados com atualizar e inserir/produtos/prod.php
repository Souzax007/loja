<?php
session_start();

// Verifica se o usuário está autenticado
if (!isset($_SESSION['id_login'])) {
    // Redireciona para a página de login se não estiver autenticado
    header("Location:../protect.php");
    exit();
}

include("../conexao/conexao.php");

$searchValue = "";

if (isset($_GET["search"])) {
    $_SESSION['search'] = $_GET["search"];
}

$registros_por_pagina = 5;
$pagina_atual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
$offset = ($pagina_atual - 1) * $registros_por_pagina;

if (isset($_GET["show_all"])) {
    unset($_SESSION['search']);
    $search = "";
    $searchValue = "";
} else {
    $search = "";
    if (isset($_SESSION['search'])) {
        $searchValue = $_SESSION['search'];
        $escapedSearch = mysqli_real_escape_string($conn, $_SESSION['search']);
        $search = " WHERE m.nm_marca LIKE '%$escapedSearch%' OR c.nm_categoria LIKE '%$escapedSearch%' OR p.nm_produto LIKE '%$escapedSearch%' OR p.id_produto = " . (int)$_SESSION['search'];
    }
}

$sql = "SELECT p.id_produto, c.nm_categoria, m.nm_marca, pt.id_tamanho, t.nm_tam, p.nm_produto, p.ds_descricao, p.vl_valor, p.nr_estoque
        FROM produtos p
        LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
        LEFT JOIN marcas m ON p.id_marca = m.id_marca
        LEFT JOIN prod_tam pt ON p.id_produto = pt.id_produto
        LEFT JOIN tamanhos t ON pt.id_tamanho = t.id_tam
        $search
        LIMIT $offset, $registros_por_pagina";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos</title>
    <link rel="stylesheet" href="../produtos/prod_form.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            background-repeat: no-repeat;
            background-size: cover;
            font-family: Arial, sans-serif;
        }

        #loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        #loading img {
            width: 100%;
            height: 100%;
        }

        .tabela {
            display: none;
        }

        .popup {
            display: flex;
            position: fixed;
            top: 80%;
            left: 90%;
            padding: 20px;
            z-index: 1;
            width: 100px;
            height: 100px;
        }
        .nav {
            text-align: center;
            background-color: red;
            width: 100%;
            height: 50px;
        }
        /* Outros estilos aqui */
    </style>
</head>
<body>
    <div class="nav" >
        <?php include("../nav/nav.php");?>
    </div>

    <div id="loading">
        <img src="https://gizmodo.uol.com.br/wp-content/blogs.dir/8/files/2021/02/nyan-cat.gif" alt="">
    </div>

    <div class="tabela">
        <div>
            <a href="prod_form.php?id=0&acao=insert"><button type="submit">NOVO</button></a>
            <a href="prod.php?show_all=1"><button type="submit">TUDO</button></a>
            <form action="prod.php" method="GET" style="float:right;">
                <input name="search" type="text" placeholder="Pesquisar" style="width:150px;" value="<?= htmlspecialchars($searchValue); ?>">
                <button type="submit">BUSCAR</button>
            </form>
        </div>

        <table>
            <tr>
                <th>MARCAS</th>
                <th>CATEGORIA</th>
                <th>TAMANHO</th>
                <th>NOME</th>
                <th>DESCRIÇÃO</th>
                <th>VALOR</th>
                <th>ESTOQUE</th>
                <th>AÇÕES</th>
            </tr>

            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $id_produto = $row["id_produto"];
                    $nm_marca = $row["nm_marca"];
                    $nm_categoria = $row["nm_categoria"];
                    $nm_tam = $row["nm_tam"];
                    $nm_produto = $row["nm_produto"];
                    $ds_descricao = $row["ds_descricao"];
                    $vl_valor = $row["vl_valor"];
                    $nr_estoque = $row["nr_estoque"];
                    ?>
                    <tr>
                        <td><?php echo $nm_marca;?></td>
                        <td><?php echo $nm_categoria;?></td>
                        <td><?php echo $nm_tam;?></td>
                        <td><?php echo $nm_produto;?></td>
                        <td><?php echo $ds_descricao;?></td>
                        <td><?php echo $vl_valor;?></td>
                        <td><?php echo $nr_estoque;?></td>
                        <td>
                            <a href="prod_form.php?id=<?php echo $id_produto;?>&acao=upd"><button class="acao" name="editar">Editar</button></a>
                            <a href="prod_del.php?id=<?php echo $id_produto;?>"><button class="acao" name="excluir">Excluir</button></a>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                echo "<tr><td colspan='7'>Nenhum resultado encontrado na tabela 'produtos'.</td></tr>";
            }
            ?>
        </table>

        <div class='pagination'>
            <?php
            $total_registros_sql = "SELECT COUNT(*) as total FROM produtos p 
                                    LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                                    LEFT JOIN marcas m ON p.id_marca = m.id_marca
                                    $search";
            $total_registros_result = $conn->query($total_registros_sql);
            $total_registros = $total_registros_result->fetch_assoc()['total'];

            $total_paginas = ceil($total_registros / $registros_por_pagina);

            if ($pagina_atual > 1) {
                echo "<a href='prod.php?pagina=" . ($pagina_atual - 1) . "' class='pagination-btn'>Anterior</a> ";
            }

            for ($i = 1; $i <= $total_paginas; $i++) {
                echo "<a href='prod.php?pagina=$i' class='pagination-btn'>" . $i . "</a> ";
            }

            if ($pagina_atual < $total_paginas) {
                echo "<a href='prod.php?pagina=" . ($pagina_atual + 1) . "' class='pagination-btn'>Próximo</a>";
            }
            ?>
        </div>
    </div>

    <!-- Conteúdo do pop-up -->
    <div class="popup" id="popup">
        <img src="https://i.gifer.com/4Snl.gif" alt="">
    </div>

    <script>
        window.addEventListener('load', function () {
            document.getElementById('loading').style.display = 'none';
            document.querySelector('.tabela').style.display = 'block';
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>
