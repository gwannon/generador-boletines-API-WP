<?php 

ini_set("display_errors", 1);

include_once("./lib/config.php"); 

if(isset($_REQUEST['download'])) {
  downloadHtml();
}

?><html>
<head>
  <title>GENERADOR: Newsletter SPRI ES</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
  <?php /* <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;0,700;0,800;1,300;1,400;1,600;1,700;1,800&display=swap" rel="stylesheet"> */ ?>
  <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <link href="./css/style.css" rel="stylesheet" />
</head>
<body>
  <section>
    <div class="container">
      <div class="row">
        <div class="col-12 p-3 text-center">
          <h1>GENERADOR: Newsletter SPRI</h1>
        </div>
        <div class="col-12 p-1">
          <?php 
            $posts = getPosts($lang);
            if(isset($_REQUEST['data'])) $_REQUEST['data'] = array_values($_REQUEST['data']);
            if(isset($_REQUEST['help'])) $_REQUEST['help'] = array_values($_REQUEST['help']);
            if(isset($_REQUEST['banner'])) $_REQUEST['banner'] = array_values($_REQUEST['banner']);
            //cho "<pre>"; print_r ($_REQUEST); echo "</pre>";
            
            if(isset($_REQUEST['test']) && isset($_REQUEST['test_email'])) { 
              if(sendTest($_REQUEST['test_email'])) {
                ?><p class="text-center alert alert-success">Prueba enviada</p><?php
              } else {
                ?><p class="text-center alert alert-warning">Error al enviar la prueba. Intentalo más tarde.</p><?php
              }
            }

            if(isset($_REQUEST['template'])) { ?>
              <p class="text-center alert alert-success">Plantilla cargada</p>
              <?php loadTemplate();
            }

            if (isset($_REQUEST['news']) && $_REQUEST['news'] > 0) $number = $_REQUEST['news'];
            else $number = count($posts);

            if (isset($_REQUEST['helps']) && $_REQUEST['helps'] > 0) {
              $default_helps = getHelps($lang);
              $number_helps = $_REQUEST['helps'];
            }

            if (isset($_REQUEST['banners']) && $_REQUEST['banners'] > 0) $number_banners = $_REQUEST['banners'];

            //INICIO CREACIÓN BOLETÍN
            if(isset($_REQUEST['crear']) || isset($_REQUEST['guardar'])) {
              $newsletter = generateNewsletterHtml($intereses, $posts); ?>
              <p class="text-center alert alert-success">Newsletter generada correctamente<br/><br/><a href='#' class="pop-up-open btn btn-primary">Previsualizar</a> <a href='<?php echo ROOT; ?>?download=yes' target='_blank' class='btn btn-secondary'>Descargar</a></p>
              <div class="pop-up-bg">
                <div class="pop-up">
                  <div class="pop-up-close">✕</div>
                  <iframe style='width: 100%; height: 100%;' src="temp.html?hash=<?php echo date("YmdHis"); ?>"></iframe>
                </div>
              </div>
              <?php
            } 

            if(isset($_REQUEST['guardar'])) { ?>
              <p class="text-center alert alert-success">Plantilla guardada</p>
              <?php saveTemplate();
            }
            //FIN CREACIÓN BOLETÍN ?>
        </div>
      </div>
      <?php generateForm($number, $posts, $number_helps, $default_helps, $number_banners, $intereses, $lang); ?>
    </div>
  </section>
  <script src="./js/scripts.js"></script>
</body>
</html>