<?php 

include_once("./lib/lib.php");

define("DAYS", 47);
define("DOMAIN_WP", "https://spri.eus");
$current_url = explode("?", $_SERVER['REQUEST_URI']);
define("ROOT", $current_url[0]);

$intereses = array (
  "interes-ciberseguridad"=> 'Ciberseguridad',
  "interes-digitalizacion" => 'Digitalización',
  "interes-emprendimiento" =>  'Emprendimiento',
  "interes-i+d" =>  'I+D',
  "interes-innovacion" =>  'Innovación',
  "interes-internacionalizacion" =>  'Internacionalización',
  "interes-invertir-en-euskadi" =>  'Invertir en Euskadi',
  "interes-invertir-inmovilizado" =>  'Invertir en inmovilizado',
  "interes-relanzamiento-empresarial" =>  'Relanzamiento empresarial',
  "interes-sostenibilidad-medioambiental" =>  'Sostenibilidad Medioambiental',
  "interes-fondos-capital-riesgo" =>  'Fondos de Capital Riesgo'
);

if (isset($_REQUEST['lang']) && $_REQUEST['lang'] == 'eu') $lang = "eu";
else $lang = "es";

$number = 0;
$number_helps = 0;
$number_banners = 0;
