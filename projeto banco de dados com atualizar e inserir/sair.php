<?php
//vai destroir as sessão é manda pra login
if (!isset($_SESSION)) {
    session_start();
}

session_destroy();

header("Location:login.php");

?>