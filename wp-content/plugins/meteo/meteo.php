<?php

/**
 * @package Meteo
 * @version 1.0.0
 */
/*
Plugin Name: Meteo
Plugin URI: http://localhost/meteo/
Description: Just a tiny widget.
Author: Webdevoo
Version: 1.0.0
Author URI: https://webdevoo.com
*/

/**
 * defined('ABSPATH') bloque la visibilité du script php si quelqu'un va sur l'url
 */
defined('ABSPATH') or die('Vous n\'êtes pas autorisé à accéder à ce fichier !');

// Créer une méthode d'activation
// register_activation_hook(__FILE__, function () {
//    touch(__DIR__ . '/test');
// });

// Créer une méthode de désactivation
// register_deactivation_hook(__FILE__, function () {
//    unlink(__DIR__ . '/test');
// });

// Activer si besoin d'une fonction de désinstallation
// register_uninstall_hook(__FILE__, function(){
//    unlink(__DIR__);
// });

add_action('admin_menu', 'meteo_menu');
function meteo_menu()
{
   //add_menu_page($page_title,$menu_title,$capability, $menu_slug, $function, $icon_url, $position)
   add_menu_page(
      'meteo',
      'meteo',
      'manage_options',
      'meteo',
      'show_meteo',
      '../wp-content/themes/oceanwp-child/assets/img/parapluie.png',
      79
   );
}

function show_meteo()
{
   ;?>
   <h1>Bonjour,</h1>
   <form id="meteo-form" method="POST" action="options.php">
      <?php
      settings_fields('meteo_option_group');
      do_settings_sections('meteo');
      submit_button('Valider');
      ?>
   </form>
<?php
}

function api_key_callback()
{
   printf('<input type="text" name="meteo_option_name[api_key]" id="api_key" placeholder="%s"/>', 'api_key');
}
function city_code_callback()
{
   printf('<input type="text" name="meteo_option_name[city_id]" id="city_id" placeholder="%s"/>', '42');
}
function widget_type_callback()
{
   printf('<input type="text" name="meteo_option_name[widget_type]" id="widget_type" placeholder="%s"/>', 'meteo');
}

function void_callback(){

}

function meteo_fields()
{
   /**register_setting( 
    * string $option_group, 
    * string $option_name, 
    * array $args = array() 
    * ) 
    * */
   register_setting(
      'meteo_option_group',
      'meteo_option_name',
      'sanitizer'
   );

   add_settings_section(
      'meteo_setting_section',
      'Bienvenue sur votre plugin de météo',
      'void_callback',
      'meteo'
   );

   /**
    * add_settings_field(
    * string $id, 
    * string $title, 
    * callable $callback, 
    * string $page, 
    * string $section = 'default', 
    * array $args = array() 
    * )
    */
   add_settings_field(
      'api_key',
      'Entre votre clé API',
      'api_key_callback',
      'meteo',
      'meteo_setting_section'
   );

   add_settings_field(
      'city_code_id',
      'Entrez l\'ID de la ville',
      'city_code_callback',
      'meteo',
      'meteo_setting_section'
   );

   add_settings_field(
      'widget_type',
      'Choisissez votre widget',
      'widget_type_callback',
      'meteo',
      'meteo_setting_section'
   );
}
add_action('admin_init', 'meteo_fields');

function sanitizer($value){
   $values = array();
   if(isset($value['api_key']) && !empty($value['api_key'])){
      $values['api_key'] = sanitize_text_field($value['api_key']);
   }
   if(isset($value['city_id']) && !empty($value['city_id'])){
      $values['city_id'] = sanitize_text_field($value['city_id']);
   }
   if(isset($value['widget_type']) && !empty($value['widget_type'])){
      $values['widget_type'] = sanitize_text_field($value['widget_type']);
   }
   return $values;
}



function meteo_css()
{
   echo '
   <style>
   #meteo-form{
      width:420px;
      display:grid;
      grid-template-columns:minmax(150px,1fr);
      grid-auto-rows:minmax(20px, auto);
      grid-auto-flow:row dense;
      gap:0.25em;
   }
   #meteo-form input[type="submit"]{
      max-width:250px;
      margin-left:auto;
      grid-column:1;
      grid-row:4;
   }
   </style>
   ';
}

add_action('admin_head', 'meteo_css');;
