<?php
     defined('MOODLE_INTERNAL') || die();

     // API ayarları için bir bölüm oluştur
     $settings->add(new admin_setting_heading(
         'mod_exam/apiheader',
         get_string('apiheader', 'mod_exam'),
         get_string('apiheader_desc', 'mod_exam')
     ));

     // API URL ayarı
     $settings->add(new admin_setting_configtext(
         'mod_exam/apiurl',
         get_string('apiurl', 'mod_exam'),
         get_string('apiurl_desc', 'mod_exam'),
         '',
         PARAM_URL
     ));

     // API Key ayarı
     $settings->add(new admin_setting_configpasswordunmask(
         'mod_exam/apikey',
         get_string('apikey', 'mod_exam'),
         get_string('apikey_desc', 'mod_exam'),
         ''
     ));
     