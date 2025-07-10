<?php include("../../bd.php");
//Envio de parametros en la URL o en el metodo GET
if(isset($_GET['txtID'])){
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
    //Buscar el archivo relacionado con el cliente
    $sentencia=$conexion->prepare("SELECT imagen FROM customers 
        WHERE customer_id=:id");
    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();
    $registro_recuperado=$sentencia->fetch(PDO::FETCH_LAZY);

    //Buscar el archivo imagen y eliminar
    if(isset($registro_recuperado["imagen"]) && $registro_recuperado["imagen"]!=""){
        if(file_exists("./".$registro_recuperado["imagen"])){
            unlink("./".$registro_recuperado["imagen"]);
        }
    }
    //Borra los datos del cliente
    $sentencia=$conexion->prepare("DELETE FROM customers WHERE customer_id=:id");
    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();
    $mensaje="Registro eliminado";
}
//Consulta para clientes para mostrar como unico registro
$sentencia=$conexion->prepare("SELECT * FROM customers");
$sentencia->execute();
$lista_clientes=$sentencia->fetchAll(PDO::FETCH_ASSOC);
//print_r($lista_clientes);
?>
<?php include("../../templates/header.php");?>
<h2>Lista de Clientes</h2>
<div class="card">
    <div class="card-header">
        <a name="" id="" class="btn btn-outline-primary" href="crear.php" role="button">
            Nuevo</a></div>
    <div class="card-body">
        <div class="table-responsive-sm">
            <table class="table table-primary" id="tabla_id">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nombres y Apellidos</th>
                        <th scope="col">Foto</th>
                        <th scope="col">Telefono</th>
                        <th scope="col">Correo</th>
                        <th scope="col">Calle</th>
                        <th scope="col">Ciudad</th>
                        <th scope="col">Depa</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($lista_clientes as $registro) { ?>
                    <tr class="">
                        <td scope="row"><?php echo $registro['customer_id']; ?></td>
                        <td><?php echo $registro['first_name']; ?>
                            <?php echo $registro['last_name']; ?>
                        </td>
                        <td><img width="50"
                            src="<?php echo $registro['imagen']; ?>"
                            class="img-fluid rounded" alt="Foto del cliente"/>
                        </td>
                        <td><?php echo $registro['phone']; ?></td>
                        <td><?php echo $registro['email']; ?></td>
                        <td><?php echo $registro['street']; ?></td>
                        <td><?php echo $registro['city']; ?></td>
                        <td><?php echo $registro['state']; ?></td>
                        <td><a class="btn btn-outline-primary" href="editar.php?txtID=<?php echo $registro['customer_id']; ?>" role="button">Editar</a>
                            <a class="btn btn-outline-danger" href="index.php?txtID=<?php echo $registro['customer_id']; ?>" role="button">Eliminar</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>      
    </div>
</div>
<?php include("../../templates/footer.php");?>