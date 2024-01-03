<?php
class BD
{
	private $conexion=null; /*Objeto conector mysqli*/
	private $stmt=null; /*preparación de la consulta SQL*/
	/*Constructor de la clase*/
	public function __construct($valor=False) {
		if($valor==1){
			$this->conectar();
		}
		else{
			$this->conectarsololectura();
		}
	}

	/*Destructor de la clase*/
	function __destruct() {
		$this->finalizar();
	}

	/*conecta con la base de datos para operaciones de escritura*/
	protected function conectar() {
		$this->conexion = new mysqli('lourdesexhadm.mysql.db','lourdesexhadm','nczCXh3gu2mU','lourdesexhadm');
		if ($this->conexion->connect_errno) {header("/e500"); exit();}
	}

	/*conecta con la base de datos para operaciones de lectura (SELECT)*/
	protected function conectarsololectura() {
		$this->conexion = new mysqli('lourdesexhadm.mysql.db','lourdesexhadm','nczCXh3gu2mU','lourdesexhadm');
		if ($this->conexion->connect_errno) {header("/e500"); exit();}/*if ($this->conexion->connect_errno == 1040 OR $this->conexion->connect_errno == 1203) ¿demasiadas conexiones?*/
	}
	
	/*Cierra las conexiones*/
	protected function finalizar() {
		$this->conexion->close();
	}

    public function insertarNoticia($titulo, $enlace, $fecha, $texto, $tipo)
    {
        if($this->CompruebaNoticia($titulo,$enlace)==0){
            $ncontador+=$this->insertaNoticia($titulo, $enlace, $fecha, $texto, $tipo);
        }
    }

    public function insertarNoticias($objeto, $tipo)
    {
        $this->insertarNoticiasRSS($objeto, $tipo);
    }

    private function insertarNoticiasRSS($objeto, $tipo)
    {
        $ncontador=0;
        if(isset($objeto->channel->item)){
            foreach($objeto->channel->item as $elemento) {
                $titulo=html_entity_decode(strip_tags($elemento->title), ENT_QUOTES, 'UTF-8');
                $texto=html_entity_decode(strip_tags($elemento->description), ENT_QUOTES, 'UTF-8');
                echo("$titulo<br>".$elemento->link."<br>".$elemento->pubDate."<br>".$texto."<br>");
                if(strpos(mb_strtolower($titulo, 'UTF-8'),"báñez")===false && strpos(mb_strtolower($titulo, 'UTF-8'),"Montoro")===false 
                && strpos(mb_strtolower($texto, 'UTF-8'),"báñez")===false && strpos(mb_strtolower($texto, 'UTF-8'),"Montoro")===false){
                    if($this->CompruebaNoticia($elemento->title,$elemento->link)==0){
                       $ncontador+=$this->insertaNoticia($titulo, $elemento->link, $elemento->pubDate, $texto, $tipo);
                    }
                }
            }
            echo("<br>insertadas $ncontador noticias de un total de: ".count($objeto->channel->item));
        }
        elseif(isset($objeto->entry)){
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
                else{
                    $enlace=$elemento->link{'href'};                    
                }
               if(strpos($enlace,"http://")===false){
                   $enlace="http://www.juntadeandalucia.es".$enlace;
               }
                echo("$titulo<br>".$enlace."<br>".$fecha."<br>".$texto."<br><hr>");
                if(strpos(mb_strtolower($titulo, 'UTF-8'),"báñez")===false && strpos(mb_strtolower($titulo, 'UTF-8'),"Montoro")===false 
                && strpos(mb_strtolower($texto, 'UTF-8'),"báñez")===false && strpos(mb_strtolower($texto, 'UTF-8'),"Montoro")===false){
                    if($this->CompruebaNoticia($elemento->title,$enlace)==0){
                       $ncontador+=$this->insertaNoticia($titulo, $enlace, $fecha, $texto, $tipo);
                    }
                }
            }
            echo("<br>insertadas $ncontador noticias de un total de: ".count($objeto->entry));
        }
    }

    private function CompruebaNoticia($titulo, $url)
    {
        $dev=0;
       /*$this->stmt=$this->conexion->stmt_init();
        $sql="SELECT codigo FROM noticia WHERE titulo=? AND enlace=?;";
		if($this->stmt=$this->conexion->prepare($sql)) {
			$this->stmt->bind_param("ss",
                $this->conexion->real_escape_string($titulo),
                $this->conexion->real_escape_string($url));
			$this->stmt->execute();
			$resultado=$this->stmt->get_result();
            if($resultado->num_rows>0){$dev=1;}
			$resultado->close();			
		}
        $this->stmt->close();*/
       /* $sql=sprintf("SELECT codigo FROM noticia WHERE titulo='%s' AND enlace='%s';",
            $this->conexion->real_escape_string($titulo),
            $this->conexion->real_escape_string($url));*/
        $sql=sprintf("SELECT codigo FROM noticia WHERE enlace='%s';",
            $this->conexion->real_escape_string($url));
        $resultado=$this->conexion->query($sql);
		if($resultado){
			if($resultado->num_rows>0){$dev=1;}
			$resultado->close();
        }
        return $dev;
    }

    private function insertaNoticia($titular, $enlace, $fecha, $descripcion, $tipo)
    {
        $dev=0;
        /*$this->stmt=$this->conexion->stmt_init();
        $sql="INSERT INTO noticia(titulo,enlace,fecha,texto,tipo) VALUES(?,?,?,?,?);";
        if($this->stmt=$this->conexion->prepare($sql))
		{
			$this->stmt->bind_param("ssssi",
				$this->conexion->real_escape_string($titular), 
				$this->conexion->real_escape_string($enlace), 
				$this->conexion->real_escape_string(date("Y-m-d H:i:s",strtotime($fecha))),
                $this->conexion->real_escape_string($descripcion),
                intval($tipo));
			if($this->stmt->execute())
			{
				$codigo=$this->stmt->insert_id;
			}
			$this->stmt->close();
		}*/
        $sql=sprintf("INSERT INTO noticia(titulo,enlace,fecha,texto,tipo) VALUES('%s','%s','%s','%s',%d);",
            $this->conexion->real_escape_string($titular), 
			$this->conexion->real_escape_string($enlace), 
			$this->conexion->real_escape_string(date("Y-m-d H:i:s",strtotime($fecha))),
            $this->conexion->real_escape_string($descripcion),
            intval($tipo));
        $resultado=$this->conexion->query($sql);

		if($resultado){
            $dev=1;
        }
        return $dev;
    }

    public function ContarNoticias()
    {
        return $this->CuentaNoticias();
    }

    private function CuentaNoticias()
    {
        $ncuenta=0;
        $sql=sprintf("SELECT COUNT(*) FROM noticia WHERE YEAR(fecha)=%d;",
            utilidades::devano());
         $resultado=$this->conexion->query($sql);
         if($resultado){
            $rs=$resultado->fetch_array(MYSQLI_NUM);
			$ncuenta=$rs[0];
			$resultado->close();
         }
         return $ncuenta;
    }

    public function muestraNoticias($limite)
    {
        return $this->Noticias($limite);
    }

    private function Noticias($limite)
    {
        $sql=sprintf("SELECT titulo,enlace,fecha,texto,tipo FROM noticia WHERE YEAR(fecha)=%d ORDER BY fecha DESC LIMIT %d,3;",
            utilidades::devano(),
            $limite);
        $resultado=$this->conexion->query($sql);
        return $resultado;
    }
}
?>