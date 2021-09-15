<?php

function generateNewsletterHtml($intereses, $posts) {
  $template['big'] = file_get_contents ("./templates/noticia_big.html");
  $template['medium'] = file_get_contents ("./templates/noticia_medium.html");
  $template['small'] = file_get_contents ("./templates/noticia_small.html");
  $template['help'] = file_get_contents ("./templates/ayuda.html");
  $template['banner'] = file_get_contents ("./templates/banner.html");

  //Noticias ------------------
  $news = '';
  foreach ($_REQUEST['data'] as $item ) {
    if($item['post_id'] >= 0 && $item['format'] != '') {
      $temp = $template[$item['format']];
      //https://help.activecampaign.com/hc/en-us/articles/220358207-How-to-use-Conditional-Content-in-emails
      if (isset($item['intereses']) && count($item['intereses']) > 0) {
        $temp = "<!-- %IF in_array('".implode($item['intereses'], "', \$SLASHED_TAGS) || in_array('")."', \$SLASHED_TAGS)% -->\n\n".$temp;
        $temp = $temp ."\n\n<!-- %/IF% -->";
      }
      $temp = str_replace("[text]", ( isset($item['texto_especial']) && $item['texto_especial'] != '' ? strip_tags($item['texto_especial']) : strip_tags($posts[$item['post_id']]->excerpt->rendered)), str_replace("[link]", $posts[$item['post_id']]->link, str_replace("[title]", $posts[$item['post_id']]->title->rendered, $temp)));
      $cat = json_decode(file_get_contents("https://spri.eus/wp-json/wp/v2/categories/". $posts[$item['post_id']]->categories[0]));
      $temp = str_replace("[category]", $cat->name, $temp);
      if($item['imagen'] != '') {
        $temp = str_replace("[image]", $item['imagen'], $temp);
      } else {
        $current_image = json_decode(file_get_contents("https://www.spri.eus/wp-json/wp/v2/media/". $posts[$item['post_id']]->featured_media));
        if($current_image->media_details->sizes->medium_large) $temp = str_replace("[image]", $current_image->media_details->sizes->medium_large->source_url, $temp);
        else $temp = str_replace("[image]", $current_image->media_details->sizes->full->source_url, $temp);
      }
    } else $temp = "";
    $news .= $temp;
  }

  //Ayudas -------------
  $helps = '';
  if(is_array($_REQUEST['help']) && count($_REQUEST['help']) > 0) {
    $max = ceil(count($_REQUEST['help'])/3) * 3;
    for($i = 1; $i <= $max; $i++) {
      $key = $i-1;
      $item = $_REQUEST['help'][$key];
      if($item['title'] != '') {
        //$helps .= str_replace("[date]", $item['date'], str_replace("[link]", $item['link'], str_replace("[title]", $item['title'], $template['help'])));
        $helps .= replaceTags ($item, $template['help']);
      } else {
        $helps .= "<td width='33%'></td>";
      }
      if (($i%3) == 0 && $i < $max) {
        $helps .= "</tr><tr>";                
      }
    }
  }

  //Banners ------------------
  $banners = '';
  if(is_array($_REQUEST['banner']) && count($_REQUEST['banner']) > 0) {
    foreach($_REQUEST['banner'] as $item) {
      //$banners .= str_replace("[text]", $item['text'], str_replace("[image]", $item['image'], str_replace("[link]", $item['link'], str_replace("[title]", $item['title'], $template['banner']))));
      $banners .= replaceTags ($item, $template['banner']); 
    }
  }

  //Genearamos la plantilla
  if($lang == 'eu') $newsletter = str_replace("[BANNERS]", $banners, str_replace("[YEAR]", $_REQUEST['ano'], str_replace("[HELPS]", $helps, str_replace("[NEWS]", $news, file_get_contents ("./templates/plantilla_eu.html")))));
  else $newsletter = str_replace("[BANNERS]", $banners, str_replace("[YEAR]", $_REQUEST['ano'], str_replace("[HELPS]", $helps, str_replace("[NEWS]", $news, file_get_contents ("./templates/plantilla.html")))));

  if(is_array($_REQUEST['help']) && count($_REQUEST['help']) > 0) $newsletter = str_replace("[END_IF_HELPS]", "", str_replace("[IF_HELPS]", "", $newsletter));
  else $newsletter = str_replace("[END_IF_HELPS]", " -->", str_replace("[IF_HELPS]", "<!-- ", $newsletter));

  file_put_contents("temp.html", $newsletter);
  return $newsletter;
}

function getPosts($lang) {
  //return json_decode(file_get_contents(DOMAIN_WP."/wp-json/wp/v2/posts?per_page=100&lang=".$lang));
  return json_decode(file_get_contents(DOMAIN_WP."/wp-json/wp/v2/posts?orderby=date&order=desc&after=".date('Y-m-d', strtotime('-'.DAYS.' days'))."T00:00:00&per_page=100&lang=".$lang."&nocache=".date("YmdHis")));
}

function getHelps($lang) {
  if (isset($lang) && $lang == 'eu') $ayudas_id = 124735;
  else $ayudas_id = 124655;
  $default_helps = array();
  $pages = json_decode(file_get_contents(DOMAIN_WP."/wp-json/wp/v2/pages?parent=".$ayudas_id."&per_page=100&lang=".$lang));
  foreach ($pages as $page) {
    $default_helps[$lang][] = array ("url" => $page->link, "title" => $page->title->rendered);
  }
  return $default_helps;
}

function generateForm($number, $posts, $number_helps, $default_helps, $number_banners, $intereses, $lang) { ?>
  <script>
    var posts = {};
    <?php foreach($posts as $key => $post) { ?>
      posts[<?php echo $key; ?>] = {
        "formato": <?php if($post->ac_newsletter_posts->formato != '') { ?>'<?php echo $post->ac_newsletter_posts->formato; ?>',<?php } else { ?>false,<?php } ?>
        "texto_especial": <?php if($post->ac_newsletter_posts->texto_especial != '') { ?>'<?php echo $post->ac_newsletter_posts->texto_especial; ?>',<?php } else { ?>false,<?php } ?>
        "imagen": <?php if($post->ac_newsletter_posts->imagen != '') { ?>'<?php echo $post->ac_newsletter_posts->imagen; ?>',<?php } else { ?>false,<?php } ?>
        "intereses": <?php if(is_array($post->ac_newsletter_posts->intereses)) { ?>['<?php echo implode("', '", $post->ac_newsletter_posts->intereses); ?>']<?php } else { ?>false<?php } ?>
      };
    <?php } ?>
  </script>
  <form action="<?php echo ROOT; ?>" method="post" class="row">
    <div class="col-3 p-3">
      <h4 style="text-align: center">Idioma<br/>
      <select name="lang" onchange="this.form.submit()">
        <option value="es"<?php echo ($lang == "es" ? " selected='selected'" : ""); ?>>ES</option>
        <option value="eu"<?php echo ($lang == "eu" ? " selected='selected'" : ""); ?>>EU</option>
      </select></h4>
    </div>
    <div class="col-3 p-3">
      <h4 style="text-align: center">Noticias<br/>
      <select name="news" onchange="this.form.submit()">
        <option value="0">0</option>
        <?php for($i = 1; $i < 100; $i++) { ?>
          <option value="<?php echo $i; ?>"<?php echo ($number == $i ? " selected='selected'" : ""); ?>><?php echo $i; ?></option>
        <?php } ?>
      </select></h4>
    </div>
    <div class="col-3 p-3">
      <h4 style="text-align: center">Ayudas<br/>
      <select name="helps" onchange="this.form.submit()">
        <option value="0">0</option>
        <?php for($i = 1; $i < 100; $i++) { ?>
          <option value="<?php echo $i; ?>"<?php echo ($number_helps == $i ? " selected='selected'" : ""); ?>><?php echo $i; ?></option>
        <?php } ?>
      </select><input type="text" name="ano" value="<?php echo (isset($_REQUEST['ano']) && $_REQUEST['ano'] != '' ? $_REQUEST['ano'] : date("Y")); ?>" placeholder="Fecha de las ayudas" style="width: 90px;"/></h4>
    </div>
    <div class="col-3 p-3">
      <h4 style="text-align: center">Banners<br/>
      <select name="banners" onchange="this.form.submit()">
        <option value="0">0</option>
        <?php for($i = 1; $i < 100; $i++) { ?>
          <option value="<?php echo $i; ?>"<?php echo ($number_banners == $i ? " selected='selected'" : ""); ?>><?php echo $i; ?></option>
        <?php } ?>
      </select></h4>
    </div>
    <?php if(isset($_REQUEST['crear']) || isset($_REQUEST['guardar']) || isset($_REQUEST['test'])) { ?>
      <div class="row p-1">
        <div class="col-md-3"><input type="email" name="test_email" style="width: 100%; padding: 5px;" value="" placeholder="Email de prueba" /></div>
        <div class="col-md-3"><input type="submit" name="test" value="Enviar prueba" class="btn btn-primary" /></div>
      </div>
    <?php } ?>
    <div class="col-12 p-1">
      <input type="submit" name="crear" value="Generar boletín" class="btn btn-primary" />
      <input type="submit" name="guardar" value="Generar boletín y guardar como plantilla" class="btn btn-secondary" />
      <a href='<?php echo ROOT; ?>?template=yes&lang=<?php echo $lang; ?>' class='btn btn-info'>Recuperar plantilla</a>
    </div>
    <div id="noticias" class="p-0 m-0">
      <?php for($i = 0; $i < $number; $i++) { ?>
        <div id="post_<?php echo $i; ?>" class="col-12 p-1 noticia_drag" order="<?php echo $i; ?>">
          <div class="p-3 rounded-3 noticia">
            <div class="row">
              <div class="col-6"><h5>Noticia <?php echo ($i + 1); ?></h5></div>
              <div class="col-6 text-end">
                
                <a href="#" class="btn btn-danger newsup" title="SUBIR">&#11014;</a>
                <a href="#" class="btn btn-danger newsdown" title="BAJAR">&#11015;</a>
              


              
                <a href="#" class="btn btn-danger newsdelete" data-delete="post_<?php echo $i; ?>" title="BORRAR">&#10006;</a>
              </div>
              <div class="col-md-7">
                <select name="data[<?php echo $i; ?>][post_id]" data-post-id="<?php echo $i; ?>" class="select-post" style="width: 100%;">
                  <option value="-1">Elegir noticia</option>
                  <?php foreach($posts as $key => $post) { ?>
                    <option value="<?php echo $key; ?>"<?php if(isset($_REQUEST['data'][$i]['post_id']) && intval($_REQUEST['data'][$i]['post_id']) == intval($key)) echo " selected='selected'"; ?>><?php echo date("Y-m-d", strtotime($post->date)); ?> - <?php echo $post->title->rendered; ?></option>
                  <?php } ?>
                </select>
                <select name="data[<?php echo $i; ?>][format]" class="formato" style="width: 100%;">>
                  <option value="">Elegir formato</option>
                  <option value="big"<?php echo ($_REQUEST['data'][$i]['format'] == 'big' ? " selected='selected'" : ""); ?>>GRANDE</option>
                  <option value="medium"<?php echo ($_REQUEST['data'][$i]['format'] == 'medium' ? " selected='selected'" : ""); ?>>MEDIANO</option>
                  <option value="small"<?php echo ($_REQUEST['data'][$i]['format'] == 'small' ? " selected='selected'" : ""); ?>>PEQUEÑO</option>
                </select>
                <textarea <?php echo ($_REQUEST['data'][$i]['format'] != 'big' ? " class='hidden'" : ""); ?> name="data[<?php echo $i; ?>][texto_especial]" placeholder="Rellenar este campo si queremos un texto especial para la noticia." style="width: 100%; height: 62px;"><?php echo $_REQUEST['data'][$i]['texto_especial']; ?></textarea>
                <input class="imagen<?php echo ($_REQUEST['data'][$i]['format'] != 'big' && $_REQUEST['data'][$i]['format'] != 'medium' ? " hidden" : ""); ?>" type="text" name="data[<?php echo $i; ?>][imagen]" value="<?php echo $_REQUEST['data'][$i]['imagen']; ?>" placeholder="Imagen especial" style="width: 100%;" />
                <div class="imagen_preview text-center"><?php if($_REQUEST['data'][$i]['imagen'] != '') { ?><img src="<?php echo $_REQUEST['data'][$i]['imagen']; ?>" /><?php } ?></div>
              </div>
              <div class="col-md-5 px-3">
                <div class="row">
                  <?php foreach($intereses as $key => $interes) { ?>
                    <div class="col-6 px-2 pt-2"><label><input name="data[<?php echo $i; ?>][intereses][]" type="checkbox" value="<?php echo $key; ?>"<?php if(is_array($_REQUEST['data'][$i]['intereses']) && in_array($key, $_REQUEST['data'][$i]['intereses'])) echo " checked='checked'"; ?>> <?php echo $interes; ?></label></div>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>
    </div>
    <div id="ayudas" class="p-0 m-0">
    <?php for($i = 0; $i < $number_helps; $i++) { ?>
      <div id="help_<?php echo $i; ?>" class="col-12 p-1 ayuda_drag" order="<?php echo $i; ?>">
	<div class="p-3 rounded-3 ayuda">
	  <div class="row pb-2">
	    <div class="col-10">
   	      <h5>Ayuda <?php echo ($i + 1); ?></h5>
	    </div>
	    <div class="col-2 text-end pb-2">
	      <a href="#" class="btn btn-danger helpsup" title="SUBIR">&#11014;</a>
	      <a href="#" class="btn btn-danger helpsdown" title="BAJAR">&#11015;</a>
	      <a href="#" class="btn btn-danger helpsdelete" data-delete="help_<?php echo $i; ?>" title="BORRAR">&#10006;</a>
	    </div>
	    <div class="col-md-2">
	      <select name="help[<?php echo $i; ?>][default]" id="select-help-<?php echo ($i + 1); ?>" class="select-help" style="width: 100%; margin-bottom: 5px;">
	        <option value="">Ayudas pregeneradas</option>
	        <?php foreach($default_helps[$lang] as $key => $default) { ?>
	          <option value="<?php echo $default['url']; ?>"<?php echo ($default['url'] == $_REQUEST['help'][$i]['default'] ? " selected='selected'" : ""); ?>><?php echo $default['title']; ?></option>
	        <?php } ?>
	      </select>
	    </div>
	    <div class="col-md-3">
	      <input id="select-help-title-<?php echo ($i + 1); ?>" name="help[<?php echo $i; ?>][title]" style="width: 100%;" value="<?php echo $_REQUEST['help'][$i]['title']; ?>" placeholder="Título" required />
	    </div>
	    <div class="col-md-4">
	      <input name="help[<?php echo $i; ?>][date]" style="width: 100%;" value="<?php echo $_REQUEST['help'][$i]['date']; ?>" placeholder="Fecha" required />
	    </div>
	    <div class="col-md-3">
	      <input id="select-help-url-<?php echo ($i + 1); ?>" name="help[<?php echo $i; ?>][link]" style="width: 100%;" value="<?php echo $_REQUEST['help'][$i]['link']; ?>" placeholder="Enlace" required />
	    </div>
	  </div>
	</div>
      </div>
    <?php } ?>
    </div>
    <?php for($i = 0; $i < $number_banners; $i++) { ?>
      <div id="banner_<?php echo $i; ?>" class="col-12 p-1">
        <div class="p-3 rounded-3 banner">
          <div class="row pb-2">
            <div class="col-11">
              <h5>Banner <?php echo ($i + 1); ?></h5>
            </div>
            <div class="col-1 text-end pb-2"><a href="#" class="btn btn-danger bannersdelete" data-delete="banner_<?php echo $i; ?>" title="BORRAR">&#10006;</a></div>
            <div class="col-md-8">
              <input name="banner[<?php echo $i; ?>][title]" style="width: 100%;" value="<?php echo $_REQUEST['banner'][$i]['title']; ?>" placeholder="Título" maxlength="40" required />
              <input name="banner[<?php echo $i; ?>][text]" style="width: 100%;" value="<?php echo $_REQUEST['banner'][$i]['text']; ?>" placeholder="Texto" maxlength="120" required />
            </div>
            <div class="col-md-4">
              <input name="banner[<?php echo $i; ?>][image]" style="width: 100%;" value="<?php echo $_REQUEST['banner'][$i]['image']; ?>" placeholder="Imagen" required />
              <input name="banner[<?php echo $i; ?>][link]" style="width: 100%;" value="<?php echo $_REQUEST['banner'][$i]['link']; ?>" placeholder="Enlace" required />
            </div>

          </div>
        </div>
      </div>
    <?php } ?>
    <div class="col-12 p-1">
      <input type="submit" name="crear" value="Generar boletín" class="btn btn-primary" />
      <input type="submit" name="guardar" value="Generar boletín y guardar como plantilla" class="btn btn-secondary" />
      <a href='<?php echo ROOT; ?>?template=yes&lang=<?php echo $lang; ?>' class='btn btn-info'>Recuperar plantilla</a>
    </div>
  </form>
<?php }

function downloadHtml() {
  // Fetch the file info.
  $filePath = './temp.html';

  if(file_exists($filePath)) {
      $fileName = basename($filePath);
      $fileSize = filesize($filePath);

      // Output headers.
      header("Cache-Control: private");
      header("Content-Type: application/stream");
      header("Content-Length: ".$fileSize);
      header("Content-Disposition: attachment; filename=NEWSLETTER SPRI ".date("Y-m-d H:i:s").".html");

      // Output file.
      readfile ($filePath);                   
      exit();
  }
  else {
      die('The provided file path is not valid.');
  }
}

function saveTemplate() {
  global $lang;
  $save = $_REQUEST;
  unset($save['data']);
  unset($save['guardar']);
  unset($save['news']);
  file_put_contents("plantilla_".$lang.".json", json_encode($save));
}

function loadTemplate() {
  global $lang;
  $_REQUEST = json_decode(file_get_contents("plantilla_".$lang.".json"), true);
}


function sendTest($email) {
  $headers = 'Content-Type: text/html; charset=UTF-8'. "\r\n" .'From: prueba@enuutisworking.com'. "\r\n" .'Reply-To: prueba@enuutisworking.com';
  if(mail($email, "PRUEBA DE NEWSLETTER SPRI ".date("Y-m-d H:i:s"), file_get_contents("temp.html"), $headers)) return true;
  else return false;
}

function replaceTags ($item, $html) {
  foreach ($item as $tag => $value) {
    $html = str_replace("[".$tag."]", $value, $html);
  }
  return $html;
}
