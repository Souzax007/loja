<?php
session_start();

// Verifica se o usuário está autenticado
if (!isset($_SESSION['id_login'])) {
    // Redireciona para a página de login se não estiver autenticado
    header("Location:../protect.php");
    exit();
}

include("../conexao/conexao.php");

// Consulta SQL para obter categorias
$sql_categorias = "SELECT id_categoria, nm_categoria FROM categorias";
$result_categorias = $conn->query($sql_categorias);

// Verifique se há resultados antes de usar
$categorias = array();
if ($result_categorias->num_rows > 0) {
    while ($row_categoria = $result_categorias->fetch_assoc()) {
        $categorias[] = $row_categoria;
    }
}

// Consulta SQL para obter marcas
$sql_marcas = "SELECT id_marca, nm_marca FROM marcas";
$result_marcas = $conn->query($sql_marcas);

// Verifique se há resultados antes de usar
$marcas = array();
if ($result_marcas->num_rows > 0) {
    while ($row_marca = $result_marcas->fetch_assoc()) {
        $marcas[] = $row_marca;
    }
}

// Consulta SQL para obter tamanhos
$sql_tam = "SELECT id_tam, nm_tam FROM tamanhos";
$result_tam = $conn->query($sql_tam);

// Verifique se há resultados antes de usar
$tam = array();
if ($result_tam->num_rows > 0) {
    while ($row_tam = $result_tam->fetch_assoc()) {
        $tam[] = $row_tam;
    }
}

// Inicialização de variáveis
$id_produto = $id_categoria = $id_marca = $nm_produto = $id_tam = $nm_tam = $ds_descricao = $vl_valor = $nr_estoque = $mensagem = "";

// Verifica se 'id' está definido antes de usá-lo
$id = (isset($_GET["id"])) ? intval($_GET["id"]) : 0;

// Sanitização da ação
$acao = (isset($_POST['act'])) ? $_POST["act"] : (isset($_GET["acao"]) ? $_GET["acao"] : "");

// Processamento do formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitização e obtenção de dados do formulário
    $var_id_produto = (isset($_POST["id_produto"])) ? intval($_POST["id_produto"]) : 0;
    $var_id_categoria = (isset($_POST["id_categoria"])) ? intval($_POST["id_categoria"]) : 0;
    $var_id_marca = (isset($_POST["id_marca"])) ? intval($_POST["id_marca"]) : 0;
    $var_nm_produto = (isset($_POST["nm_produto"])) ? mysqli_real_escape_string($conn, $_POST["nm_produto"]) : "";
    $var_ds_descricao = (isset($_POST["ds_descricao"])) ? mysqli_real_escape_string($conn, $_POST["ds_descricao"]) : "";
    $var_vl_valor = (isset($_POST["vl_valor"])) ? floatval($_POST["vl_valor"]) : 0.0;
    $var_nr_estoque = (isset($_POST["nr_estoque"])) ? intval($_POST["nr_estoque"]) : 0;

    // Verificar se os campos não estão vazios antes de inserir ou atualizar
    if (!empty($var_nm_produto) && !empty($var_ds_descricao) && $var_vl_valor > 0 && $var_nr_estoque >= 0) {
        if ($acao === "insert") {

            // Verificar duplicidade de ID antes de inserir
            $check_duplicate_sql = "SELECT id_produto FROM produtos WHERE id_produto = ?";
            $check_stmt = $conn->prepare($check_duplicate_sql);
            $check_stmt->bind_param("i", $var_id_produto);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();

            if ($check_result->num_rows > 0) {
                $mensagem = "O ID inserido é inválido. Este ID já existe na tabela.";
            } else {
                // SQL preparado para inserção
                $sql_insert = "INSERT INTO produtos (id_produto, id_categoria, id_marca, nm_produto, ds_descricao, vl_valor, nr_estoque) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql_insert);
                $stmt->bind_param("iiisssi", $var_id_produto, $var_id_categoria, $var_id_marca, $var_nm_produto, $var_ds_descricao, $var_vl_valor, $var_nr_estoque);

                if ($stmt->execute()) {
                    // Obtém o ID do novo registro inserido
                    $id_produto = mysqli_insert_id($conn);
                    $mensagem = "Produto inserido com sucesso!";
                } else {
                    $mensagem = "Erro ao inserir os dados: " . $stmt->error;
                }

                //RECUPERAR O ÚLTIMO ID DO PRODUTO
                $id_produto_insert = mysqli_insert_id($conn);

                //GRAVAR OS TAMANHOS SELECIONADOS NO CHECKBOX
                $id_tam = $_POST["id_tam"];
                foreach ($id_tam as $valor_tam) {
                    $sql_insert_prod_tam = "INSERT INTO prod_tam (id_produto, id_tamanho) VALUES (?, ?)";
                    $stmt_prod_tam = $conn->prepare($sql_insert_prod_tam);
                    $stmt_prod_tam->bind_param("ii", $id_produto_insert, $valor_tam);
                    $stmt_prod_tam->execute();
                }                

            }
        } elseif ($acao === "upd") {
            if (!is_numeric($id) || $id <= 0) {
                $mensagem = "ID inválido.";
            } else {
                // SQL preparado para atualização
                $sql_upd = "UPDATE produtos SET id_produto = ?, id_categoria = ?, id_marca = ?, nm_produto = ?, ds_descricao = ?, vl_valor = ?, nr_estoque = ? WHERE id_produto = ?";
                $stmt = $conn->prepare($sql_upd);
                $stmt->bind_param("iiisssii", $var_id_produto, $var_id_categoria, $var_id_marca, $var_nm_produto, $var_ds_descricao, $var_vl_valor, $var_nr_estoque, $id);

                if ($stmt->execute()) {
                    $mensagem = "Registro atualizado com sucesso!";
                } else {
                    $mensagem = "Erro na atualização: " . $stmt->error;
                }
            }
        }
    } else {
        $mensagem = "Certifique-se de preencher os campos corretamente.";
    }
}

// Consulta SQL usando instrução preparada
if (is_numeric($id) && $id > 0) {
    $sql = "SELECT id_produto, id_categoria, id_marca, nm_produto, ds_descricao, vl_valor, nr_estoque FROM produtos WHERE id_produto = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_produto = $row["id_produto"];
        $id_categoria = $row["id_categoria"];
        $id_marca = $row["id_marca"];
        $nm_produto = $row["nm_produto"];
        $ds_descricao = $row["ds_descricao"];
        $vl_valor = $row["vl_valor"];
        $nr_estoque = $row["nr_estoque"];
    } else {
        $mensagem = "Nenhum registro encontrado com ID: $id";
    }
}

// Consulta SQL usando instrução preparada
if (is_numeric($id) && $id > 0) {
    $sql = "SELECT id_tam,nm_tam FROM tamanhos WHERE id_tam = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_tam = $row["id_tam"];
    }
}


// Fechar a conexão com o banco de dados
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="../produtos/style2.css">
    <script>

        function func_categoria(valor){
            var idTam = document.getElementById("id_tam");
            //alert(valor);
            // Verifica se o valor selecionado é igual a 93
            if (valor == 93) {
                idTam.style.display = "block"; // Mostra o campo
            } else {
                idTam.style.display = "none"; // Oculta o campo
            }            
        }
    </script>
</head>
<body onload="func_categoria(document.form_produtos.id_categoria.value);">

    <a href="../produtos/prod.php"><button class="btn">Voltar</button></a>

    <div class="add">
        <form name="form_produtos" class="for" action="prod_form.php?id=<?php echo $id;?>" method="POST">
            <h1><?php echo ($acao == "insert") ? "Inserir" : "Atualizar"; ?> produto</h1>

            <select name="id_categoria" required="required" class="lista-select" onchange="func_categoria(this.value);">
                <?php
                foreach ($categorias as $categoria) {
                    $id_categoria_option = $categoria["id_categoria"];
                    $nm_categoria_option = $categoria["nm_categoria"];
                    echo "<option value=\"$id_categoria_option\"";
                    if ($id_categoria_option == $id_categoria) {
                        echo " selected";
                    }
                    echo ">$nm_categoria_option</option>";
                }
                ?>
            </select>
            <div id="id_tam" class="checkbox" >
            <?php
            foreach ($tam as $tam_option) {
                $id_tam_option = $tam_option["id_tam"];
                $nm_tam_option = $tam_option["nm_tam"];
                

                //para deixar o campo checkbox salvo
                echo "<div style=\"display: inline-block; margin:5px;\">"; // Adiciona um div para exibir em linha
                echo "<label><input type=\"checkbox\" name=\"id_tam[]\" value=\"$id_tam_option\"";
                
                if (in_array($id_tam_option, (array)$id_tam)) {
                    echo " checked";
                }

                echo ">$nm_tam_option</label>";
                echo "</div>"; // Fecha o div
            }
            ?>

            </div>

            <select name="id_marca" required="required" class="lista-select" >
                <?php
                foreach ($marcas as $marca) {
                    $id_marca_option = $marca["id_marca"];
                    $nm_marca_option = $marca["nm_marca"];
                    echo "<option value=\"$id_marca_option\"";
                    if ($id_marca_option == $id_marca) {
                        echo " selected";
                    }
                    echo ">$nm_marca_option</option>";
                }
                ?>
            </select>

            <label for="nm_produto">Nome <span>*</span></label>
            <input type="text" name="nm_produto" placeholder="Nome do filme" value="<?php echo $nm_produto; ?>">

            <label for="ds_descricao">Descrição <span>*</span></label>
            <input type="text" name="ds_descricao" placeholder="Fale sobre o filme " value="<?php echo $ds_descricao; ?>">

            <label for="vl_valor">Valor <span>*</span></label>
            <input type="number" name="vl_valor" placeholder="Quanto irá custar" value="<?php echo $vl_valor; ?>">

            <label for="nr_estoque">Estoque <span>*</span></label>
            <input type="number" name="nr_estoque" placeholder="Quantidade em estoque" value="<?php echo $nr_estoque; ?>">

            <input type="hidden" name="id_produto" value="<?php echo $id_produto; ?>" <?php echo ($acao === "upd") ? 'readonly' : ''; ?>>
            <input type="hidden" name="act" value="<?php echo $acao; ?>">

            <button type="submit">SALVAR</button>
            <p><?php echo $mensagem; ?></p>
        </form>
    </div>
</body>
</html>
