<?php

use function DI\value;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');
require_once($CFG->dirroot . '/mod/exam/lib.php');

class mod_exam_mod_form extends moodleform_mod
{

    public function definition()
    {
        global $CFG, $PAGE;
        $mform = $this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));
        $mform->addElement('text', 'name', get_string('name'), array('size' => '64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $this->standard_intro_elements();

        $mform->addElement('header', 'categorysection', get_string('categorysection', 'mod_exam'));

        $main_categories = exam_get_main_categories();
        $main_options = array('' => get_string('selectmaincategory', 'mod_exam'));
        foreach ($main_categories as $category) {
            $main_options[$category['id']] = $category['name'];
        }
        $mform->addElement('select', 'maincategory', get_string('maincategory', 'mod_exam'), $main_options);
        $mform->addRule('maincategory', get_string('required'), 'required', null, 'client');

        $mform->addElement('select', 'subcategory', get_string('subcategory', 'mod_exam'), array('' => get_string('selectsubcategory', 'mod_exam')));
        $mform->disabledIf('subcategory', 'maincategory', 'eq', '');

        $mform->addElement('select', 'thirdlevelcategory', get_string('thirdlevelcategory', 'mod_exam'), array('' => get_string('selectthirdlevelcategory', 'mod_exam')));
        $mform->disabledIf('thirdlevelcategory', 'subcategory', 'eq', '');

        $mform->setType('subcategory', PARAM_INT);
        $mform->setType('thirdlevelcategory', PARAM_INT);


        $this->standard_coursemodule_elements();
        $this->add_action_buttons();

        $PAGE->requires->strings_for_js(
            array('selectsubcategory', 'selectthirdlevelcategory'),
            'mod_exam'
        );

        $PAGE->requires->js_call_amd('mod_exam/category_selector', 'init');
    }
    public function validation($data, $files)
    {
        $errors = parent::validation($data, $files);

        // Ana kategori kontrolü
        if (empty($data['maincategory'])) {
            $errors['maincategory'] = get_string('required');
        }

        return $errors;
    }

    public function data_preprocessing(&$defaultvalues) {
    if (isset($defaultvalues['maincategory'])) {
        $maincategory = $defaultvalues['maincategory'];

        // Seçilen ana kategoriye göre alt kategorileri çek
        $subcategories = exam_get_sub_categories($maincategory);
        //$subcategoryoptions = ['' => get_string('selectsubcategory', 'mod_exam')];
        foreach ($subcategories as $subcategory) {
            $subcategoryoptions[$subcategory['id']] = $subcategory['name'];
        }
        $this->_form->getElement('subcategory')->load($subcategoryoptions);
    }

    if (isset($defaultvalues['subcategory'])) {
        $subcategory = $defaultvalues['subcategory'];

        // Seçilen alt kategoriye göre 3. seviye kategorileri çek
        $thirdcategories = exam_get_third_level_categories($subcategory);
        //$thirdoptions = ['' => get_string('selectthirdlevelcategory', 'mod_exam')];
        foreach ($thirdcategories as $third) {
            $thirdoptions[$third['id']] = $third['name'];
        }
        $this->_form->getElement('thirdlevelcategory')->load($thirdoptions);
    }
}



}
