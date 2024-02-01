<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        .formulario {
            margin-top: 20px;
            margin-bottom: 20px;
            text-align: center;
        }

        .formulario input {
            margin: 5px;
            padding: 10px;
        }

        .formulario input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .formulario input[type="submit"]:hover {
            background-color: #45a049;
        }

        .item-container {
            margin: 10px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-align: center;
            background-color: white;
        }

        .item-container h3 {
            margin-bottom: 10px;
        }

        .image-container img {
            max-width: 100%;
            max-height: 100%;
            border-radius: 5px;
        }

        .button-container {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        .button-container button {
            flex-grow: 1;
            margin: 5px;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .button-container button.view {
            background-color: #4CAF50;
            color: white;
        }

        .button-container button.delete {
            background-color: #ff3333;
            color: white;
        }

        .button-container button.download {
            background-color: #008CBA;
            color: white;
        }

        .button-container button:hover {
            filter: brightness(90%);
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 10px;
        }
    </style>
    <title>Links e Nomes</title>
</head>
<body>

<?php
$servername = "localhost";
$username = "root";
$password = "linuxville";
$dbname = "imagens";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_id'])) {
        // Excluir a imagem com base no ID recebido
        $delete_id = $_POST['delete_id'];
        $stmt_delete = $conn->prepare("DELETE FROM img WHERE id = ?");
        $stmt_delete->bind_param('i', $delete_id);
        $stmt_delete->execute();
        $stmt_delete->close();
    } else {
        // Adicionar uma nova imagem ao banco de dados
        $nome = $_POST["nome"];
        $link = $_POST["link"];

        if (!preg_match("~^(?:f|ht)tps?://~i", $link)) {
            $link = "http://" . $link;
        }

        $stmt = $conn->prepare("INSERT INTO img (nome, link, conteudo) VALUES (?, ?, ?)");
        $conteudoPadrao = 'Seu_Valor_Padrao';
        $stmt->bind_param('sss', $nome, $link, $conteudoPadrao);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$sql = "SELECT * FROM img";
$result = $conn->query($sql);

if ($result !== false) {
    ?>
    <div class="formulario">
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            Nome: <input type="text" name="nome" required>
            Link: <input type="text" name="link" required>
            <input type="submit" value="Enviar">
        </form>
    </div>

    <div class="grid-container">
        <?php
        while ($row = $result->fetch_assoc()) {
            echo "<div class='item-container'>";
            echo "<h3>" . $row["nome"] . "</h3>";
            echo "<div class='image-container'><a href='" . $row["link"] . "' target='_blank'><img src='" . $row["link"] . "'></a></div>";
            echo "<div class='button-container'>";
            echo "<button class='view' onclick='location.href=\"" . $row["link"] . "\"'>VER</button>";
            echo "<form method='post' action='" . $_SERVER['PHP_SELF'] . "'>";
            echo "<input type='hidden' name='delete_id' value='" . $row["id"] . "'>";
            echo "<button class='delete' type='submit'>Excluir</button>";
            echo "</form>";
            echo "<a href='" . $row["link"] . "' download><button class='download'>Download</button></a>";
            echo "</div>";
            echo "</div>";
        }
        ?>
    </div>
    <?php
} else {
    echo "Erro na consulta: " . $conn->error;
}

$conn->close();
?>
</body>
</html>
