<?php
     defined('MOODLE_INTERNAL') || die();

     function xmldb_exam_upgrade($oldversion) {
         global $DB;
         $dbman = $DB->get_manager();

         if ($oldversion < 2025052807) {
             // Yeni introformat alanını ekle.
             $table = new xmldb_table('exam');
             $field = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '1', 'intro');

             if (!$dbman->field_exists($table, $field)) {
                 $dbman->add_field($table, $field);
             }

             // Versiyonu güncelle.
             upgrade_mod_savepoint(true, 2025052807, 'exam');
         }

         if ($oldversion < 2025052808) {
             // Kategori alanlarını ekle
             $table = new xmldb_table('exam');

             $field1 = new xmldb_field('maincategory', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'introformat');
             if (!$dbman->field_exists($table, $field1)) {
                 $dbman->add_field($table, $field1);
             }

             $field2 = new xmldb_field('subcategory', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'maincategory');
             if (!$dbman->field_exists($table, $field2)) {
                 $dbman->add_field($table, $field2);
             }

             $field3 = new xmldb_field('thirdlevelcategory', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'subcategory');
             if (!$dbman->field_exists($table, $field3)) {
                 $dbman->add_field($table, $field3);
             }

             upgrade_mod_savepoint(true, 2025052808, 'exam');
         }

         return true;
     }
     