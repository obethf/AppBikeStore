<?php include("../../bd.php");
if(isset($_GET['txtID'])){
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
    $sentencia=$conexion->prepare("SELECT * FROM customers WHERE customer_id=:id");
    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();
    //Creamos la variable
    $registro=$sentencia->fetch(PDO::FETCH_LAZY);
    $first_name=$registro["first_name"];
    $last_name=$registro["last_name"];
    $imagen=$registro["imagen"];
    $phone=$registro["phone"];
    $email=$registro["email"];
    $street=$registro["street"];
    $city=$registro["city"];
    $state=$registro["state"];
}
//el siguiente codigo
if($_POST){
    //Recolectamos los datos del metodo POST
    $txtID=(isset($_POST["txtID"])?$_POST["txtID"]:"");
    $first_name=(isset($_POST["first_name"])?$_POST["first_name"]:"");
    $last_name=(isset($_POST["last_name"])?$_POST["last_name"]:"");
    //Para foto cambiamos a $_FILES y agregamos ['name']
    //$imagen=(isset($_FILES["imagen"]['name'])?$_FILES["imagen"]['name']:"");

    $phone=(isset($_POST["phone"])?$_POST["phone"]:"");
    $email=(isset($_POST["email"])?$_POST["email"]:"");
    $street=(isset($_POST["street"])?$_POST["street"]:"");
    $city=(isset($_POST["city"])?$_POST["city"]:"");
    $state=(isset($_POST["state"])?$_POST["state"]:"");
    //Preparamos la insercion de los datos
    $sentencia=$conexion->prepare("UPDATE customers SET
    first_name=:first_name,
    last_name=:last_name,
    phone=:phone,
    email=:email,
    street=:street,
    city=:city,
    state=:state WHERE customer_id=:id");
    //Asignar los valores que tienen uso de :variable
    $sentencia->bindParam(":first_name",$first_name);
    $sentencia->bindParam(":last_name",$last_name);
    $sentencia->bindParam(":phone",$phone);
    $sentencia->bindParam(":email",$email);
    $sentencia->bindParam(":street",$street);
    $sentencia->bindParam(":city",$city);
    $sentencia->bindParam(":state",$state);

    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();

    $imagen=(isset($_FILES["imagen"]['name'])?$_FILES["imagen"]['name']:"");
    //Adjuntamos la foto con un nombre distinto de archivo
    $fecha_=new DateTime();
    $nombreArchivo_foto=($imagen!='')?$fecha_->getTimestamp()."_".$_FILES["imagen"]['name']:"";
    //Creamos archivo temporal de la foto
    $tmp_foto=$_FILES["imagen"]['tmp_name'];
    if($tmp_foto!=''){
        move_uploaded_file($tmp_foto,"./".$nombreArchivo_foto);
        //buscar el archivo relacionado don el cliente
        $sentencia=$conexion->prepare("SELECT imagen FROM customers WHERE customer_id=:id");
        $sentencia->bindParam(":id", $txtID);
        $sentencia->execute();
        $registro_recuperado=$sentencia->fetch(PDO::FETCH_LAZY);
        //Buscar el archivo para borrarlo
        if(isset($registro_recuperado['imagen'])&& $registro_recuperado['imagen'!=""]){
            if(file_exists("./".$registro_recuperado['imagen'])){
                unlink("./".$registro_recuperado['imagen']);
            }
        }
        $sentencia=$conexion->prepare("UPDATE customers SET imagen=:imagen WHERE customer_id=:id");
        $sentencia->bindParam(":imagen",$nombreArchivo_foto);
        $sentencia->bindParam(":id",$txtID);
        $sentencia->execute();

    }
    
    $mensaje="Registro modificado";
    header("Location:index.php?mensaje=".$mensaje);
}

?>
<?php include("../../templates/header.php");?>
<h2>Editar cliente</h2>
<div class="card">
    <div class="card-header">Datos del cliente</div>
    <div class="card-body">
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <input type="text" value="<?php echo $txtID; ?>" class="form-control"
                    readonly name="txtID" id="txtID" aria-describedby="helpId"
                    placeholder="ID"/>
            </div>
            <div class="mb-3">
                <input type="text" value="<?php echo $first_name; ?>" class="form-control" 
                name="first_name" id="first_name" aria-describedby="helpId"
                placeholder="Nombres"/>
                <small id="helpId" class="form-text text-muted">Ingrese los nombres del cliente</small>
            </div>
            <div class="mb-3">
                <input type="text" value="<?php echo $last_name; ?>" class="form-control" 
                name="last_name" id="last_name" aria-describedby="helpId"
                placeholder="Apellidos"/>
                <small id="helpId" class="form-text text-muted">Ingrese los apellidos del cliente</small>
            </div>
            <div class="mb-3">
                <label for="imagen" class="form-label">Foto:</label>
                <br>
                <img width="100" src="<?php echo $imagen; ?>" class="img-fluid rounded" alt="Foto"/>
                <br><br>
                <input type="file" class="form-control" name="imagen" id="imagen" 
                aria-describedby="helpId" placeholder="Foto"/>
                <small id="helpId" class="form-text text-muted">Ingrese el archivo tipo imagen del cliente</small>
            </div>
            <div class="mb-3">
                <input type="text" value="<?php echo $phone; ?>" class="form-control" 
                name="phone" id="phone" aria-describedby="helpId" placeholder="Telefono"/>
                <small id="helpId" class="form-text text-muted">Ingrese el numero de tel&eacute;fono del cliente</small>
            </div>
            <div class="mb-3">
                <input type="email" value="<?php echo $email; ?>" class="form-control" 
                name="email" id="email" aria-describedby="helpId" placeholder="Correo"/>
                <small id="helpId" class="form-text text-muted">Ingrese el correo electr&oacute;nico del cliente</small>
            </div>
            <div class="mb-3">
                <input type="text" value="<?php echo $street; ?>" class="form-control" 
                name="street" id="street" aria-describedby="helpId" placeholder="Calle"/>
                <small id="helpId" class="form-text text-muted">Ingrese la calle del cliente</small>
            </div>
            <div class="mb-3">
                <input type="text" value="<?php echo $city; ?>" class="form-control" 
                name="city" id="city" aria-describedby="helpId" placeholder="Ciudad"/>
                <small id="helpId" class="form-text text-muted">Ingrese la ciudad del cliente</small>
            </div>
            <div class="mb-3">
                <input type="text" value="<?php echo $state; ?>" class="form-control" 
                name="state" id="state" aria-describedby="helpId" placeholder="Departamento"/>
                <small id="helpId" class="form-text text-muted">Ingrese el departamento del cliente</small>
            </div>
            <button type="submit" class="btn btn-outline-success">Actualizar registro</button>
            <a name="" id="" class="btn btn-outline-primary" href="index.php" role="button">
                Cancelar</a>
        </form>
    </div>
<?php include("../../templates/footer.php");?>