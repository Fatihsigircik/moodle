<?php

use core\exception\moodle_exception;
defined('MOODLE_INTERNAL') || die();

/**
 * Modülü kursa ekleme fonksiyonu.
 */
function exam_add_instance($exam, $mform = null)
{
    global $DB;
    $exam->timecreated = time();
    $exam->timemodified = time();

    $exam->subcategory = optional_param('subcategory', 0, PARAM_INT);
    $exam->thirdlevelcategory = optional_param('thirdlevelcategory', 0, PARAM_INT);

    return $DB->insert_record('exam', $exam);
}

/**
 * Modülü güncelleme fonksiyonu.
 */
function exam_update_instance($exam, $mform = null)
{
    global $DB;

    $exam->timemodified = time();
    $exam->id = $exam->instance;

    $exam->subcategory = optional_param('subcategory', 0, PARAM_INT);
    $exam->thirdlevelcategory = optional_param('thirdlevelcategory', 0, PARAM_INT);

    return $DB->update_record('exam', $exam);
}

/**
 * Modülü silme fonksiyonu.
 */
function exam_delete_instance($id)
{
    global $DB;

    if (!$exam = $DB->get_record('exam', array('id' => $id))) {
        return false;
    }

    $DB->delete_records('exam', array('id' => $id));
    return true;
}

/**
 * Modülün desteklediği özellikleri tanımlar.
 */
function exam_supports($feature)
{
    switch ($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        default:
            return null;
    }
}





/**
 * Genel API isteği fonksiyonu
 * @param string $endpoint API endpoint'i
 * @return array API yanıtı
 */
function exam_make_api_request($endpoint)
{
    $apiurl = get_config('mod_exam', 'apiurl');
    $apikey = get_config('mod_exam', 'apikey');

    if (!$apiurl || !$apikey) {
        throw new moodle_exception('apiconfigerror', 'mod_exam');
    }

    $url = rtrim($apiurl, '/') . '/' . ltrim($endpoint, '/');
    //die($url);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . $apikey,
        'Content-Type: application/json'
    ));

    $response = curl_exec($curl);
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($httpcode != 200) {
        error_log('API isteği başarısız: ' . $url . ' - HTTP Kodu: ' . $httpcode);
        throw new moodle_exception('apierror', 'mod_exam', '', null, 'HTTP Kodu: ' . $httpcode);
    }

    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new moodle_exception('apiresponseerror', 'mod_exam');
    }

    return $data;
}

/**
 * Ana kategorileri çek
 * @return array Ana kategoriler
 */
function exam_get_main_categories()
{
    error_log('exam_get_main_categories fonksiyonu çağrıldı');
    try {
        $data = exam_make_api_request('api/examcategory/program-list');
        return $data ?? [];
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Alt kategorileri çek
 * @param int $main_category_id Ana kategori ID'si
 * @return array Alt kategoriler
 */
function exam_get_sub_categories($main_category_id)
{
    try {
        $data = exam_make_api_request('api/examcategory/program-module-list/' . $main_category_id);
        return $data ?? [];
    } catch (Exception $e) {
        return [];
    }
}

/**
 * 3. seviye kategorileri çek
 * @param int $sub_category_id Alt kategori ID'si
 * @return array 3. seviye kategoriler
 */
function exam_get_third_level_categories($sub_category_id)
{
    try {
        $data = exam_make_api_request('api/examcategory/program-module-section-list/' . $sub_category_id);
        return $data ?? [];
    } catch (Exception $e) {
        return [];
    }
}