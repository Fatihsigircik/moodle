<?php

use core\output\html_writer;
require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

$id = required_param('id', PARAM_INT); // Course module ID
$PAGE->set_url('/mod/exam/view.php', ['id' => $id]);
$cm = get_coursemodule_from_id('exam', $id, 0, false, MUST_EXIST);
$context = context_module::instance($cm->id);
require_login($cm->course, true, $cm);
require_capability('mod/exam:view', $context);

$exam = $DB->get_record('exam', ['id' => $cm->instance], '*', MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);

$apiurl = get_config('mod_exam', 'apiurl');
$apikey = get_config('mod_exam', 'apikey');

// API'ye gönderilecek veri

if (optional_param('exam_submit', null, PARAM_RAW)) {
    $code = optional_param('question_code', null, PARAM_RAW);
    $userId = $USER->id;
    $postdata = [
        'code' => $code,
        'userId' => $userId
    ];
    $url = rtrim($apiurl, '/') . '/' . ltrim('api/examcategory/submit-answer', '/');
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $apikey,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
    $response = curl_exec($ch);
    curl_close($ch);
    // Gelen cevabı işle
    $data = json_decode($response, true);
    //başarılı ise veri tabanında başarılı olarak kaydet


    echo $OUTPUT->header();
    echo $OUTPUT->heading("Cevap Gönderildi");
    if ($data['success']) {

         $message = html_writer::div(
            $OUTPUT->pix_icon('i/valid', '') . ' <strong>Doğru cevap!</strong> Tebrikler, bu bölümü başarıyla tamamladınız.',
            'alert alert-success'
        );

        $completion = new completion_info($course);
        $completion->update_state($cm, COMPLETION_COMPLETE, $USER->id);
    } else {
        $message = html_writer::div(
            $OUTPUT->pix_icon('i/invalid', '') . ' <strong>Yanlış cevap.</strong> Lütfen tekrar deneyin.',
            'alert alert-danger'
        );

    }
    echo $message;
    echo html_writer::empty_tag('br');
    echo html_writer::link(new moodle_url('/course/view.php', ['id' => $cm->course]), 'Kurs sayfasına dön');

    echo $OUTPUT->footer();
} else {

    $postdata = [
        'ProgramId' => $exam->maincategory,
        'ModuleId' => $exam->subcategory,
        'SectionId' => $exam->thirdlevelcategory,
        'userId' => $USER->id
    ];

    $url = rtrim($apiurl, '/') . '/' . ltrim('api/examcategory/get-question', '/');

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $apikey,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
    $response = curl_exec($ch);
    curl_close($ch);

    // Gelen cevabı işle
    $data = json_decode($response, true);
    $questionText = $data['question'] ?? 'Soru alınamadı.';
    $questionCode = $data['code'] ?? 'Soru kodu alınamadı.';
    echo $OUTPUT->header();
    echo $OUTPUT->heading("Soru");

    echo html_writer::start_tag('div', ['class' => 'question-box']);
    echo html_writer::tag('p', format_text($questionText, FORMAT_PLAIN));
    echo html_writer::end_tag('div');

    // Bir buton gösterelim
    echo html_writer::start_tag('form', ['method' => 'post']);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'value' => $questionCode, 'name' => 'question_code']);
    echo html_writer::empty_tag('input', ['type' => 'submit', 'value' => 'Cevapla', 'class' => 'btn btn-primary', 'name' => 'exam_submit']);
    echo html_writer::end_tag('form');

    echo $OUTPUT->footer();
    $completion = new completion_info($course);
    $completion->set_module_viewed($cm);
}