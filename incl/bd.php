<?php
class BD
{
	private $conexion=null; /*Objeto conector mysqli*/
	private $stmt=null; /*preparación de la consulta SQL*/
	/*Constructor de la clase*/
	public function __construct($valor=False) {
		if($valor==1){
            Utilidades::log("info", "conexión BD escritura");
			$this->conectar();
		}
		else{
            Utilidades::log("info", "conexión BD sólo lectura");
			$this->conectarsololectura();
		}
	}

	/*Destructor de la clase*/
	function __destruct() {
        Utilidades::log("info", "Cerrando conexión con BD");
		$this->finalizar();
	}

   
	/*conecta con la base de datos para operaciones de escritura*/
	protected function conectar() {        
        try
        {
            $this->conexion = new mysqli('localhost','usuadmnot','nczCXh3gu2mU','bd_infestatal');

            if ($this->conexion->connect_errno) {
                Utilidades::log("error", "Error con la conexion en la base de datos".this->conexion->connect_errno);
                exit();
            }
        } catch (Exception $e) {
            Utilidades::log("error", $e->getMessage()."\n".var_dump($e));
        }
	}

	/*conecta con la base de datos para operaciones de lectura (SELECT)*/
	protected function conectarsololectura() {
        try
        {
            $this->conexion = new mysqli('localhost','ususel','20nczCXh3gu2mU23','bd_infestatal');
            if ($this->conexion->connect_errno) {
                Utilidades::log("error", "Error con la conexion en la base de datos".this->conexion->connect_errno);
                exit();
            }
        } catch (Exception $e) {
            Utilidades::log("error", $e->getMessage()."\n".var_dump($e));
        }
	}
	
	/*Cierra las conexiones*/
	protected function finalizar() {
		$this->conexion->close();
	}

    public function insertarNoticias($objeto, $tipo)
    {
        $this->insertarNoticiasRSS($objeto, $tipo);
    }
    
    private function insertarNoticiasRSS($objeto, $tipo)
    {
        $ncontador=0;
        if(isset($objeto->channel->item)){
            echo("acceso normal");
            foreach($objeto->channel->item as $elemento) {
                $titulo=substr(html_entity_decode(strip_tags($elemento->title), ENT_QUOTES, 'UTF-8'),0, 200);
                $texto=html_entity_decode(strip_tags($elemento->description), ENT_QUOTES, 'UTF-8');
                echo("$titulo\n$enlace\n$fecha\n$texto\n--------------------");

                if($this->CompruebaNoticia($elemento->title,$elemento->link)==0){
                    $ncontador+=$this->insertaNoticia($titulo, $elemento->link, $elemento->pubDate, $texto, $tipo);
                }
                            }
            Utilidades::log("info", "insertadas $ncontador noticias de un total de: ".count($objeto->channel->item));
        }
        elseif(isset($objeto)){
            echo("acceso entry");
            foreach($objeto as $elemento) {
                $titulo=html_entity_decode(strip_tags($elemento->title), ENT_QUOTES, 'UTF-8');
                if(isset($elemento->description)){
                    $texto=html_entity_decode(strip_tags($elemento->description), ENT_QUOTES, 'UTF-8');
                }
                else{
                    $texto=html_entity_decode(strip_tags($elemento->content), ENT_QUOTES, 'UTF-8');
                }
                if(isset($elemento->pubDate)){
                    $fecha=$elemento->pubDate;
                }
                else{
                    $fecha=$elemento->updated;
                }
                if(strlen($elemento->link)>0){
                    $enlace=$elemento->link;
                }
                elseif(isset($elemento->link->href)){
                    $enlace=$elemento->link->href;
                }
                echo("$titulo\n$enlace\n$fecha\n$texto\n".strlen($elemento->link)."--------------------");
             
                if($this->CompruebaNoticia($elemento->title,$enlace)==0){
                    $ncontador+=$this->insertaNoticia($titulo, $enlace, $fecha, $texto, $tipo);
                }

            }
            Utilidades::log("info", "Insertadas $ncontador noticias de un total de: ".count($objeto->entry));
        }
        elseif(isset($objeto->entry)){
            echo("acceso entry");
            foreach($objeto->entry as $elemento) {
                $titulo=html_entity_decode(strip_tags($elemento->title), ENT_QUOTES, 'UTF-8');
                
                if(isset($elemento->description)){
                    $texto=html_entity_decode(strip_tags($elemento->description), ENT_QUOTES, 'UTF-8');
                }
                else{
                    $texto=html_entity_decode(strip_tags($elemento->content), ENT_QUOTES, 'UTF-8');
                }
                if(isset($elemento->pubDate)){
                    $fecha=$elemento->pubDate;
                }
                else{
                    $fecha=$elemento->updated;
                }
                if(strlen($elemento->link)>0){
                    $enlace=$elemento->link;
                }
               
                echo("$titulo\n$enlace\n$fecha\n$texto\n--------------------");
                
                if($this->CompruebaNoticia($elemento->title,$enlace)==0){
                      $ncontador+=$this->insertaNoticia($titulo, $enlace, $fecha, $texto, $tipo);
                }
                
            }
            Utilidades::log("info", "Insertadas $ncontador noticias de un total de: ".count($objeto->entry));
        }
    }

    private function insertarNoticia($titulo, $enlace, $fecha, $texto, $tipo)
    {
        Utilidades::log("info", "Insertando la noticia");

        if($this->CompruebaNoticia($titulo,$enlace)==0){
            $ncontador+=$this->insertaNoticia($titulo, $enlace, $fecha, $texto, $tipo);
        }
    }

    private function CompruebaNoticia($titulo, $url)
    {
        $dev=0;
        $sql=sprintf("SELECT codigo FROM noticia WHERE enlace='%s';",
            $this->conexion->real_escape_string($url));

        $resultado=$this->conexion->query($sql);

		if($resultado){
			if($resultado->num_rows>0){$dev=1;}
			$resultado->close();
        }
        else{
            Utilidades::log("info", "Noticia duplicada\t$titulo\t$url");
        }

        return $dev;
    }

    private function insertaNoticia($titular, $enlace, $fecha, $descripcion, $tipo)
    {
        $dev=0;

        $sql=sprintf("INSERT INTO noticia(titulo,enlace,fecha,texto,tipo) VALUES('%s','%s','%s','%s',%d);",
            $this->conexion->real_escape_string($titular), 
			$this->conexion->real_escape_string($enlace), 
			$this->conexion->real_escape_string(date("Y-m-d H:i:s",strtotime($fecha))),
            $this->conexion->real_escape_string($descripcion),
            intval($tipo));

        try
        {
            $resultado=$this->conexion->query($sql);

            if($resultado){
                $dev=1;
            }

        } catch (Exception $e) {
            Utilidades::log("error", var_dump($$e->getMessage()));
        }

        return $dev;
    }
}
?>