#!/usr/bin/php
<?php
include("./incl/funciones.php");
include("./incl/bd.php");
set_error_handler(array('Utilidades','log')); 

Utilidades::log("info", "inicio aplicación");

if($argc > 1)
{
    $objetoBD = new BD(1);
    $tiponoticia = intval($argv[1]);

    switch($tiponoticia)
    {
        case 1:
            Utilidades::log("info", "Agencia Tributaria noticias");
            $url="https://sede.agenciatributaria.gob.es/Sede/todas-noticias.xml";
            break;

        case 2:
            Utilidades::log("info", "Agencia Tributaria notas de prensa");
            $url="https://sede.agenciatributaria.gob.es/Sede/sala-prensa/notas-prensa.xml";
            break;

        case 3:
            Utilidades::log("info", "Agencia Tributaria más");
            $url="https://sede.agenciatributaria.gob.es/Sede/informacion-institucional/sobre-agencia-tributaria.xml";
            break;

        case 4:
            Utilidades::log("info", "Secretaria de Estado de Función Pública");
            $url="https://funcionpublica.hacienda.gob.es/rss/?feedPath=/rssaggregatorFP&generatorName=rssInternal&channel=channel-noticias&lang=es";
            break;

        case 5:
            Utilidades::log("info", "Sanidad");
            $url="https://www.sanidad.gob.es/gabinete/notap_rss.do";
            break;

        case 6:
            Utilidades::log("info", "MINISTERIO DE HACIENDA - Empleo");
            $url="https://www.hacienda.gob.es/_layouts/15/RssEmpleo.aspx?hiloId=4";
            break;


        case 7:
            Utilidades::log("info", "MINISTERIO DE HACIENDA - Notas de prensa");
            $url="https://www.hacienda.gob.es/_layouts/15/RssNotasPrensa.aspx?hiloId=1";
            break; 

        case 8:
            Utilidades::log("info", "MINISTERIO DE HACIENDA - Novedades");
            $url="https://www.hacienda.gob.es/_layouts/15/RssNovedades.aspx?hiloId=9";
            break;

        case 9:
            Utilidades::log("info", "Boletin Oficial del Estado");
            $url="https://www.boe.es/rss/boe.php";
            break;

        case 10:
            Utilidades::log("info", "Boletin Oficial del Registro Mercantil");
            $url="https://www.boe.es/rss/borme.php";
            break;         

        case 11:
            Utilidades::log("info", "Seguridad Social");
            $url="https://www.seg-social.es/wps/wcm/connect/wss/poin_contenidos/internet/1139/?srv=cmpnt&source=library&cmpntid=601fa53b-f1d2-4180-a5e7-fe0b130e0296";
            break;     

        case 12:
            Utilidades::log("info", "Ministerio de Trabajo y Seguridad Social");
            $url="http://prensa.mites.gob.es/WebPrensa/rss";
            break;     
    }

    try
    {
        $objetoRSS=simplexml_load_file($url);
        try
        {
            if(isset($objetoRSS->channel->item))
            {
                if(count($objetoRSS->channel->item)>0){
                    $objetoBD->insertarNoticias($objetoRSS, $tiponoticia);
                }
                else
                {
                    Utilidades::log("warning", "no se encuentran elementos en el objeto RSS");
                }
            }
            elseif(isset($objetoRSS->entry)){
                if(count($objetoRSS->entry)>0){
                    $objetoBD->insertarNoticias($objetoRSS, $tiponoticia);
                }
                else
                {
                    Utilidades::log("warning", "no se encuentran elementos en el objeto RSS");
                }
            }
            elseif(isset($objetoRSS)){
                if(count($objetoRSS)>0){
                    $objetoBD->insertarNoticias($objetoRSS, $tiponoticia);
                }
                else
                {
                    Utilidades::log("warning", "no se encuentran elementos en el objeto RSS");
                }
            }
            else{
                throw new Exception("No se encuentran elementos en el objeto ".count($objetoRSS));
                Utilidades::log("error", "no se encuentran elementos en el objeto RSS");
            }
        }catch (Exception $e) {
            Utilidades::log("error", var_dump($e->getMessage()));
        }

    } catch (Exception $e) {
        Utilidades::log("error", $e->getMessage());
    }

    $objetoBD=null;
}
else
{
    Utilidades::log("warning", "no hay parámetros de inicio");
}
restore_error_handler();?>