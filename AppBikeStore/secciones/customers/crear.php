<?php include("../../bd.php");
if($_POST){
    //Recolectamos los datos del metodo POST
    $first_name=(isset($_POST["first_name"])?$_POST["first_name"]:"");
    $last_name=(isset($_POST["last_name"])?$_POST["last_name"]:"");
    //Para foto cambiamos a $_FILES y agregamos ['name']
    $imagen=(isset($_FILES["imagen"]['name'])?$_FILES["imagen"]['name']:"");
    $phone=(isset($_POST["phone"])?$_POST["phone"]:"");
    $email=(isset($_POST["email"])?$_POST["email"]:"");
    $street=(isset($_POST["street"])?$_POST["street"]:"");
    $city=(isset($_POST["city"])?$_POST["city"]:"");
    $state=(isset($_POST["state"])?$_POST["state"]:"");
    //Preparamos la insercion de los datos
    $sentencia=$conexion->prepare("INSERT INTO customers(customer_id,first_name,last_name,
    imagen,phone,email,street,city,state)
    VALUES(null,:first_name,:last_name,:imagen,:phone,:email,:street,:city,:state)");
    //Asignar los valores que tienen uso de :variable
    $sentencia->bindParam(":first_name",$first_name);
    $sentencia->bindParam(":last_name",$last_name);
    //Adjuntamos la foto con un nombre distinto de archivo
    $fecha_=new DateTime();
    $nombreArchivo_foto=($imagen!='')?$fecha_->getTimestamp()."_".$_FILES["imagen"]['name']:"";
    //Creamos archivo temporal de la foto
    $tmp_foto=$_FILES["imagen"]['tmp_name'];
    if($tmp_foto!=''){
        move_uploaded_file($tmp_foto,"./".$nombreArchivo_foto);
    }
    $sentencia->bindParam(":imagen",$nombreArchivo_foto);

    $sentencia->bindParam(":phone",$phone);
    $sentencia->bindParam(":email",$email);
    $sentencia->bindParam(":street",$street);
    $sentencia->bindParam(":city",$city);
    $sentencia->bindParam(":state",$state);
    $sentencia->execute();
    $mensaje="Registro agregado";
    //Redireccionar a index.php
    header("Location:index.php?mensaje=".$mensaje);
}
?>
<?php include("../../templates/header.php");?>
<br>
<div class="card">
    <div class="card-header">Datos del cliente</div>
    <div class="card-body">
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <input type="text" class="form-control" name="first_name"
                    id="first_name" aria-describedby="helpId"
                    placeholder="Nombres"
                />
                <small id="helpId" class="form-text text-muted">Ingrese los nombres del cliente</small>
            </div>
            <div class="mb-3">
                <input type="text" class="form-control" name="last_name"
                    id="last_name" aria-describedby="helpId"
                    placeholder="Apellidos"
                />
                <small id="helpId" class="form-text text-muted">Ingrese los apellidos del cliente</small>
            </div>
            <div class="mb-3">
                <input type="file" class="form-control" name="imagen"
                    id="imagen" aria-describedby="helpId"
                    placeholder="Foto"
                />
                <small id="helpId" class="form-text text-muted">Ingrese el archivo tipo imagen del cliente</small>
            </div>
            <div class="mb-3">
                <input type="text" class="form-control" name="phone"
                    id="phone" aria-describedby="helpId"
                    placeholder="Telefono"
                />
                <small id="helpId" class="form-text text-muted">Ingrese el numero de telefono del cliente</small>
            </div>
            <div class="mb-3">
                <input type="email" class="form-control" name="email"
                    id="email" aria-describedby="helpId"
                    placeholder="Correo"
                />
                <small id="helpId" class="form-text text-muted">Ingrese el correo electronico del cliente</small>
            </div>
            <div class="mb-3">
                <input type="text" class="form-control" name="street"
                    id="street" aria-describedby="helpId"
                    placeholder="Calle"
                />
                <small id="helpId" class="form-text text-muted">Ingrese la calle del cliente</small>
            </div>
            <div class="mb-3">
                <input type="text" class="form-control" name="city"
                    id="city" aria-describedby="helpId"
                    placeholder="Ciudad"
                />
                <small id="helpId" class="form-text text-muted">Ingrese la ciudad del cliente</small>
            </div>
            <div class="mb-3">
                <input type="text" class="form-control" name="state"
                    id="state" aria-describedby="helpId"
                    placeholder="Departamento"
                />
                <small id="helpId" class="form-text text-muted">Ingrese el departamento del cliente</small>
            </div>
            <button type="submit" class="btn btn-outline-success">Agregar registro</button>
            <a name="" id="" class="btn btn-outline-primary" href="index.php" role="button">
                Cancelar</a>
        </form>
    </div>
</div>
<?php include("../../templates/footer.php");?>