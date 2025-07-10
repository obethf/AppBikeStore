<?php
session_start();
include("templates/header.php");
?>
<br>
<div class="p-5 mb-4 bg-light rounded-3">
    <div class="container-fluid py-5">
        <h1 class="display-5 fw-bold">Bienvenido</h1>
        <p class="col-md-8 fs-4">
            Usuario: <?php echo isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Invitado'; ?>
        </p>
        <button class="btn btn-primary btn-lg" type="button">
            Aceptar
        </button>
    </div>
</div>
<br>
<?php include("templates/footer.php"); ?>