<?php
     define('AJAX_SCRIPT', true);

     require_once('../../config.php');
     require_once($CFG->dirroot . '/mod/exam/lib.php');

     require_login();
     require_sesskey();

     $action = required_param('action', PARAM_ALPHA);
     $response = array('success' => false, 'data' => array());

            //die($action);
     try {
         switch ($action) {
             case 'getsubcategories':
                
                 $main_category_id = required_param('main_category_id', PARAM_INT);
                 $sub_categories = exam_get_sub_categories($main_category_id);
                 $response['success'] = true;
                 $response['data'] = $sub_categories;
                 break;

             case 'getthirdlevelcategories':
                 $sub_category_id = required_param('sub_category_id', PARAM_INT);
                 $third_level_categories = exam_get_third_level_categories($sub_category_id);
                 $response['success'] = true;
                 $response['data'] = $third_level_categories;
                 break;

             default:
                 throw new moodle_exception('invalidaction', 'mod_exam');
         }
     } catch (Exception $e) {
         $response['error'] = $e->getMessage();
     }

     echo json_encode($response);
     die();