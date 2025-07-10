<?php include("templates/header.php"); ?>

<?php include("templates/footer.php"); ?>



<?php
session_start();
session_destroy();
header("Location: Login.php"); // Ajusta la ruta según tu estructura
exit();
?>