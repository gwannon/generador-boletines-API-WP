<?php
/**
 * Plugin Name: ActiveCampaignNewsletterComposer
 * Plugin URI:  https://www.gwannon.com/
 * Description: Campos extras para noticias para el generador de boletines
 * Version:     2.0
 * Author:      Gwannon
 * Author URI:  https://www.gwannon.com/
 * License:     GNU General Public License v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ac_newsletter_composer
 *
 * PHP 7.3
 * WordPress 5.5.3
 */

//ini_set("display_errors", 1);

//CAMPOS personalizados ---------------------------
// ------------------------------------------------
function get_ac_newsletter_posts_custom_fields () {

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

	$fields = array(
		'formato' => array ('titulo' => "Formato", 'tipo' => 'select', "valores" => array("" => "Elegir formato", "big" =>  'Grande', "small" => 'Pequeño')),
		'texto_especial' => array ('titulo' => "Texto alternativo", 'tipo' => 'text'),
		'intereses' => array ('titulo' => "Intereses", 'tipo' => 'checkbox', "valores" => $intereses),
    'imagen' => array ('titulo' => "Imagen alternativa", 'tipo' => 'image'),
		/*'desplegable' => array ('titulo' => "Select", 'tipo' => 'select', "valores" => array("en_proceso" =>  'En proceso', "terminado" => 'Terminado')),
    'checkboxes' => array ('titulo' => "Select", 'tipo' => 'checkbox', "valores" => array("en_proceso" =>  'En proceso', "terminado" => 'Terminado')),
    'texto' => array ('titulo' => "Texto", 'tipo' => 'text'),
    'fecha' => array ('titulo' => "Fecha", 'tipo' => 'date'),
		'textarea' => array ('titulo' => "Textarea", 'tipo' => 'textarea'),
		'imagen' => array ('titulo' => "Imagen", 'tipo' => 'image'),
		'enlace' => array ('titulo' => "Enlace", 'tipo' => 'link'),
		'oculto => array ('titulo' => "Dato oculto", 'tipo' => 'hidden'), */
	);
	return $fields;
}

function ac_newsletter_posts_add_custom_fields() {
    add_meta_box(
        'box_ac_newsletter_posts', // $id
        __('Campos especiales del post para generar newsletter', 'ac_newsletter_composer'), // $title 
        'ac_newsletter_posts_show_custom_fields', // $callback
        'post', // $page
        'normal', // $context
        'high'); // $priority
}
add_action('add_meta_boxes', 'ac_newsletter_posts_add_custom_fields');

function ac_newsletter_posts_show_custom_fields() { //Show box
	global $post; 
	$fields = get_ac_newsletter_posts_custom_fields (); ?>
		<div>
      <?php foreach ($fields as $field => $datos) { ?>
        <div style="width: calc(50% - 10px); float: left; padding: 5px;">
          <p><b><?php echo $datos['titulo']; ?></b></p>
          <?php if($datos['tipo'] == 'text' || $datos['tipo'] == 'link') { ?>
            <input  type="text" class="_ac_newsletter_post_<?php echo $field; ?>" id="_ac_newsletter_post_<?php echo $field; ?>" style="width: 100%;" name="_ac_newsletter_post_<?php echo $field; ?>" value="<?php echo get_post_meta( $post->ID, '_ac_newsletter_post_'.$field, true ); ?>" />
          <?php } else if($datos['tipo'] == 'date') { ?>
            <input type="date" class="_ac_newsletter_post_<?php echo $field; ?>" id="_ac_newsletter_post_<?php echo $field; ?>" style="width: 50%;" name="_ac_newsletter_post_<?php echo $field; ?>" value="<?php echo get_post_meta( $post->ID, '_ac_newsletter_post_'.$field, true ); ?>" />
          <?php } else if($datos['tipo'] == 'hidden') { ?>
            <input disabled="disabled" type="text" class="_ac_newsletter_post_<?php echo $field; ?>" id="_ac_newsletter_post_<?php echo $field; ?>" style="width: 50%;" name="_ac_newsletter_post_<?php echo $field; ?>" value="<?php echo get_post_meta( $post->ID, '_ac_newsletter_post_'.$field, true ); ?>" />
          <?php } else if($datos['tipo'] == 'image') { ?>
            <input type="text" class="_ac_newsletter_post_<?php echo $field; ?>" id="input_ac_newsletter_post_<?php echo $field; ?>" style="width: 80%;" name="_ac_newsletter_post_<?php echo $field; ?>" value='<?php echo get_post_meta( $post->ID, '_ac_newsletter_post_'.$field, true ); ?>' />
            <a href="#" id="button_ac_newsletter_post_<?php echo $field; ?>" class="button insert-media add_media" data-editor="input_ac_newsletter_post_<?php echo $field; ?>" title="<?php _e("Añadir fichero", 'ac_newsletter_composer'); ?>"><span class="wp-media-buttons-icon"></span> <?php _e("Añadir fichero", 'ac_newsletter_composer'); ?></a>
            <script>
              jQuery(document).ready(function () {			
                jQuery("#input_ac_newsletter_post_<?php echo $field; ?>").change(function() {
                  a_imgurlar = jQuery(this).val().match(/<a href=\"([^\"]+)\"/);
                  img_imgurlar = jQuery(this).val().match(/<img[^>]+src=\"([^\"]+)\"/);
                  if(img_imgurlar !==null ) jQuery(this).val(img_imgurlar[1]);
                  else  jQuery(this).val(a_imgurlar[1]);
                });
              });
            </script>
            <?php if(get_post_meta( $post->ID, '_ac_newsletter_post_'.$field, true ) != '') { ?><img src="<?php echo get_post_meta( $post->ID, '_ac_newsletter_post_'.$field, true ); ?>" alt=""  style="max-width: 150px; width: 100%;"/><?php } ?>
          <?php } else if($datos['tipo'] == 'text') { ?>
            <input  type="text" class="_ac_newsletter_post_<?php echo $field; ?>" id="_ac_newsletter_post_<?php echo $field; ?>" style="width: 100%;" name="_ac_newsletter_post_<?php echo $field; ?>" value="<?php echo get_post_meta( $post->ID, '_ac_newsletter_post_'.$field, true ); ?>" />
          <?php } else if($datos['tipo'] == 'textarea') { ?>
            <?php $settings = array( 'media_buttons' => true, 'quicktags' => true, 'textarea_rows' => 5 ); ?>
            <?php wp_editor( get_post_meta( $post->ID, '_ac_newsletter_post_'.$field, true ), '_ac_newsletter_post_'.$field, $settings ); ?>
          <?php } else if ($datos['tipo'] == 'select') { ?>
                    <select name="_ac_newsletter_post_<?php echo $field; ?>">
                <?php foreach($datos['valores'] as $key => $value) { ?>
                  <option value="<?php echo $key; ?>"<?php if ($key == get_post_meta( $post->ID, '_ac_newsletter_post_'.$field, true )) echo " selected='selected'"; ?>><?php echo $value; ?></option>
              <?php } ?>	
            </select>
          <?php }  else if ($datos['tipo'] == 'checkbox') { ?>
                <?php $results = get_post_meta( $post->ID, '_ac_newsletter_post_'.$field, true ); ?>
                <?php foreach($datos['valores'] as $key => $value) { ?>
                  <input type="checkbox" class="_ac_newsletter_post_<?php echo $field; ?>" id="_ac_newsletter_post_<?php echo $field; ?>" name="_ac_newsletter_post_<?php echo $field; ?>[]" value="<?php echo $key; ?>" <?php if(is_array($results) && in_array($key, $results)) { echo "checked='checked'"; } ?> /> <?php echo $value; ?><br/>
              <?php } ?>	
          <?php }  ?>
        </div>
    <?php } ?>
    <div style="clear: both;"></div>
	</div><?php
}

function ac_newsletter_posts_save_custom_fields( $post_id ) { //Save changes
	global $wpdb;
	$fields = get_ac_newsletter_posts_custom_fields();
	foreach ($fields as $field => $datos) {
		$label = '_ac_newsletter_post_'.$field;
		if (isset($_POST[$label])) update_post_meta( $post_id, $label, $_POST[$label]);
		else if (!isset($_POST[$label]) && $datos['tipo'] == 'checkbox') delete_post_meta( $post_id, $label);
	}
}
add_action('save_post', 'ac_newsletter_posts_save_custom_fields' );


//REST API ---------------------------
// ------------------------------------------------
function ac_newsletter_posts_custom_fields_rest_api() {
  register_rest_field(
    'post', 
    'ac_newsletter_posts',
    array(
      'get_callback'    => 'ac_newsletter_posts_get_custom_fields',
      'update_callback' => null,
      'schema'          => null,
    )
  );
}
add_action( 'rest_api_init', 'ac_newsletter_posts_custom_fields_rest_api');

function ac_newsletter_posts_get_custom_fields( $object, $field_name, $request ) {
  $fields = get_ac_newsletter_posts_custom_fields ();
  $custom_fields = array();
  foreach ($fields as $field => $datos) {
		$label = '_ac_newsletter_post_'.$field;
    $custom_fields[$field] = get_post_meta(get_the_ID(), $label, true);
	}
  return $custom_fields;
}
