<?php
	/*
	Clase Subir, sube archivo al servidor
	*/
	
  class Subir
  {
  	var $directory_name;
  	var $max_filesize;
  	var $error;
  	var $file_name;
  	var $full_name;
  	var $file_size;
  	var $file_type;
  	var $check_file_type;
  	var $thumb_name;
    var $tmp_name;
	var $existe;
    var $id_documento;
	var $text_aleatorio;
	var $idioma;
	
	function setIdioma($n_i)
	{
		$this->idioma = $n_i;
	}
	function setTextAleatorio($n_ta)
	{
		$this->texto_aleatorio = $n_ta;
	}
	function getTextoAleatorio()
	{
		return $this->texto_aleatorio;
	}
	
	function setExiste($exis){
		$this->existe = $exis;
	}
	function getExiste(){
		return $this->existe;
	}
   	function setDirectory($dir_name)
  	{
  	 $this->directory_name = $dir_name;
  	}

   	function setMaxSize($max_file = 3000000)
  	{
  	 $this->max_filesize = $max_file;
  	}
  	
  	function checkForDirectory()
  	{
        if (!file_exists($this->directory_name))
        {
           mkdir($this->directory_name,0777);
        }
        @chmod($this->directory_name,0777);
  	}

   	function error()
  	{
  	 return $this->error;
  	}

  	function setFileSize($file_size)
  	{
  	 $this->file_size = $file_size;
  	}

  	function setFileType($file_type)
  	{
  	 $this->file_type = $file_type;
  	}
	
  	function getFileType()
  	{
  	 return $this->file_type;
  	}

  	function setTempName($temp_name)
  	{
  	 $this->tmp_name = $temp_name;
  	}

   	function setFileName($file)
  	{
  		$this->file_name = $file;
  		$this->full_name = $this->directory_name."/".$file;
  	}
	function getFileName()
	{
		return $this->file_name;
	}
	
	function setIdDocumento($n_idd)
	{
		$this->id_documento = $n_idd;
	}
	function getIdDocumento()
	{
		return $this->id_documento;
	}
  	/*
	* @PARAMS : 
	* 	$uploaddir : Directory Name in which uploaded file is placed
	* 	$name : file input type field name
	* 	$rename : you may pass string or boolean 
	* 			 true : rename the file if it already exists and returns the renamed file name.
	* 			 String : rename the file to given string.
	* 	$replace =true : replace the file if it is already existing
	* 	$file_max_size : file size in bytes. 0 for default
	* 	$check_type : checks file type exp ."(jpg|gif|jpeg)"
	* 
	* 	Example UPLOAD::upload_file("temp","file",true,true,0,"jpg|jpeg|bmp|gif")
	* 
	* return : On success it will return file name else return (boolean)false
	*/
	
    function uploadFile($uploaddir,$name,$rename=null,$replace=false,$file_max_size=3000000,$check_type)
    {

		$this->setFileType($_FILES[$name]['type']);
        $this->setFileSize($_FILES[$name]['size']);
        $this->error=$_FILES[$name]['error'];
        $this->setTempName($_FILES[$name]['tmp_name']);
        $this->setMaxSize($file_max_size);
		$this->setDirectory($uploaddir);
        $this->checkForDirectory();

        $this->setFileName($_FILES[$name]['name']);

		if(!is_uploaded_file($this->tmp_name))
		{
			$mensaje_err = "ERROR: Archivo no fue correctamente cargado!, edite el archivo guardelo e intente nuevamente por favor";
		 	$this->error = $mensaje_err;
		 	echo "<script>alert('$mensaje_err')</script>";
		}
		if(empty($this->file_name))
		{
			$mensaje_err = "ERROR: Archivo no fue correctamente cargado!";
		 	$this->error = $mensaje_err;
		 	echo "<script>alert('$mensaje_err')</script>";
		}
		if($this->error!=""){
          return false;
		 }

		if(!empty($check_type))
        {
		   if(!eregi("\.($check_type)$",$this->file_name))
		   {
		   	
           	 $mensaje_err = "ERROR: Tipo de Archivo no válido";
		 	$this->error = $mensaje_err;
		 	echo "<script>alert('$mensaje_err')</script>";
			 return false;
			}
        }

		if(!is_bool($rename)&&!empty($rename))
		{
			if(preg_match("/\..*+$/",$this->file_name,$matches))
			   $this->set_file_name($rename.$matches[0]);
		}
		elseif($rename && file_exists($this->full_name))
		{
			if(preg_match("/\..*+$/",$this->file_name,$matches))
			   $this->setFileName(substr_replace($this->file_name,"_".rand(0, rand(0,99)),-strlen($matches[0]),0));
		}
		$this->setExiste(false);
		if(file_exists($this->full_name))
        {
			$this->setExiste(true);
          if($replace)
            @unlink($this->full_name);
          else
		  {
           	 $mensaje_err = "ERROR: El archivo ya existe!";
		 	$this->error = $mensaje_err;
		 	echo "<script>alert('$mensaje_err')</script>";
			 return false;
		  }
        }

		
        $this->startUpload($name);
        if($this->error!="")
          return false;
        else
          return $this->file_name;
    }

  	function startUpload($nombre)
  	{
  		if(!isset($this->file_name)){
  		 $mensaje_err = "ERROR: Debe definir nombre de fichero";
		 	$this->error = $mensaje_err;
		 	echo "<script>alert('$mensaje_err')</script>";
		}

      if ($this->file_size <= 0){
  		 $mensaje_err = "ERROR: En el tamaño del archivo!";
		 	$this->error = $mensaje_err;
		 	echo "<script>alert('$mensaje_err')</script>";
		}

      if ($this->file_size > $this->max_filesize && $this->max_filesize!=0){
  		 $mensaje_err = "ERROR: En el tamaño del archivo!";
		 	$this->error = $mensaje_err;
		 	echo "<script>alert('mensaje_err')</script>";
		}

       if ($this->error=="")
       {
			$destination=$this->full_name;
			
			list($nombre_archivo, $extension) = split('[.]', $this->getFileName());
			
			//echo $this->file_name." extttt ".$extension." hhhh ".$this->getTextoAleatorio();
			//$this->error = "errorrr";
			
			$nombre_archivo_subir = $this->getTextoAleatorio().".".$extension;
			
			$destination = "fotos/".$nombre_archivo_subir;
			
			//echo $destination;
			
			if (!move_uploaded_file($_FILES[$nombre]['tmp_name'], 'fotos/'.$nombre_archivo_subir)){ 
				//echo "Ocurrió algún error al subir el fichero. No pudo guardarse."; 
				$mensaje_err = "ERROR: Hubo un error al subir el fichero, el archivo no puedo guardarse!";
				$this->error = $mensaje_err;
				echo "<script>alert('mensaje_err')</script>"; 
			}
			else
			{
				//unlink ($variable_malvada);
				$imagen = $destination;
				$nombre_imagen_asociada = $nombre_archivo_subir;
				$this->redimensionar_imagen($imagen, $nombre_imagen_asociada, 800, 600);
				
				//seccion que convierte la imagen en pequeño thumb para mostrar en lista
				$imagen = $destination;
				$nombre_imagen_asociada = "thumb_".$nombre_archivo_subir;
				$this->redimensionar_imagen($imagen, $nombre_imagen_asociada, 100, 100);
			}
	/*
  			if (!@move_uploaded_file ($this->tmp_name,$destination)){
  			 $this->error = "Imposible copiar ".$this->file_name." de ".$this->tmp_name." al directorio de destino. ";
			 echo $this->error."</br>";
			 }
	*/
  		}
  	}
	//funcion que cambia de tamaño una imagen
	function redimensionar_imagen($imagen, $nombre_imagen_asociada, $nuevo_ancho, $nuevo_alto)
    {
        //indicamos el directorio donde se van a colgar las imágenes
        $directorio 	= "fotos/" ;
        //establecemos los límites de ancho y alto
       // $nuevo_ancho 	= 100;
      //  $nuevo_alto 	= 100;
        //Recojo información de la imágen
        $info_imagen 	= getimagesize($imagen);
        $alto 			= $info_imagen[1];
        $ancho 			= $info_imagen[0];
        $tipo_imagen 	= $info_imagen[2];
        //Determino las nuevas medidas en función de los límites
        if($ancho > $nuevo_ancho OR $alto > $nuevo_alto)
        {
            if(($alto - $nuevo_alto) > ($ancho - $nuevo_ancho))
            {
                $nuevo_ancho = round($ancho * $nuevo_alto / $alto,0);
            }
            else
            {
                $nuevo_alto = round($alto * $nuevo_ancho / $ancho,0);
            }
        }
        else //si la imagen es más pequeña que los límites la dejo igual.
        {
            $nuevo_alto = $alto;
            $nuevo_ancho = $ancho;
        }
        // dependiendo del tipo de imagen tengo que usar diferentes funciones
        switch ($tipo_imagen) {
            case 1: //si es gif …
                $imagen_nueva = imagecreate($nuevo_ancho, $nuevo_alto);
                $imagen_vieja = imagecreatefromgif($imagen);
                //cambio de tamaño…
                imagecopyresampled($imagen_nueva, $imagen_vieja, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho, $alto);
                if (!imagegif($imagen_nueva, $directorio . $nombre_imagen_asociada)) return false;
            break;
            case 2: //si es jpeg …
                $imagen_nueva = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);
                $imagen_vieja = imagecreatefromjpeg($imagen);
                imagecopyresampled($imagen_nueva, $imagen_vieja, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho, $alto);
                if (!imagejpeg($imagen_nueva, $directorio . $nombre_imagen_asociada)) return false;
            break;
            case 3: //si es png …
                $imagen_nueva = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);
                $imagen_vieja = imagecreatefrompng($imagen);
                //cambio de tamaño…
                imagecopyresampled($imagen_nueva, $imagen_vieja, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho, $alto);
                if (!imagepng($imagen_nueva, $directorio . $nombre_imagen_asociada)) return false;
            break;
        }
        return true; //si todo ha ido bien devuelve true
    }
	//fin funcion que cambia de tamaño una imagen

  }
?>
