define(['jquery', 'core/ajax'], function($) {
         return {
             init: function() {
                 // Ana kategori değiştiğinde
                 $('#id_maincategory').on('change', function() {
                     var mainCategoryId = $(this).val();
                     var subCategorySelect = $('#id_subcategory');
                     var thirdLevelSelect = $('#id_thirdlevelcategory');

                     // Alt kategori ve 3. seviye kategorileri sıfırla
                     subCategorySelect.empty().append(
                         '<option value="">' +
                         M.util.get_string('selectsubcategory', 'mod_exam') +
                         '</option>'
                     );
                     thirdLevelSelect.empty().append(
                         '<option value="">' +
                         M.util.get_string('selectthirdlevelcategory', 'mod_exam') +
                         '</option>'
                     );

                     if (mainCategoryId) {
                         // Alt kategorileri çek
                         $.ajax({
                             url: M.cfg.wwwroot + '/mod/exam/ajax.php',
                             method: 'POST',
                             data: {
                                 action: 'get_sub_categories',
                                 main_category_id: mainCategoryId,
                                 sesskey: M.cfg.sesskey // Moodle oturum anahtarı
                             },
                             dataType: 'json',
                             success: function(response) {
                                 if (response.success) {
                                     $.each(response.data, function(index, category) {
                                         subCategorySelect.append(
                                             '<option value="' + category.id + '">' +
                                             category.name +
                                             '</option>'
                                         );
                                     });
                                 }
                             },
                             error: function() {
                                 // Hata ayıklama için Moodle loglarını kullanabilirsiniz
                             }
                         });
                     }
                 });

                 // Alt kategori değiştiğinde
                 $('#id_subcategory').on('change', function() {
                     var subCategoryId = $(this).val();
                     var thirdLevelSelect = $('#id_thirdlevelcategory');

                     // 3. seviye kategorileri sıfırla
                     thirdLevelSelect.empty().append(
                         '<option value="">' +
                         M.util.get_string('selectthirdlevelcategory', 'mod_exam') +
                         '</option>'
                     );

                     if (subCategoryId) {
                         // 3. seviye kategorileri çek
                         $.ajax({
                             url: M.cfg.wwwroot + '/mod/exam/ajax.php',
                             method: 'POST',
                             data: {
                                 action: 'get_third_level_categories',
                                 sub_category_id: subCategoryId,
                                 sesskey: M.cfg.sesskey
                             },
                             dataType: 'json',
                             success: function(response) {
                                 if (response.success) {
                                     $.each(response.data, function(index, category) {
                                         thirdLevelSelect.append(
                                             '<option value="' + category.id + '">' +
                                             category.name +
                                             '</option>'
                                         );
                                     });
                                 }
                             },
                             error: function() {
                                 // Hata ayıklama için Moodle loglarını kullanabilirsiniz
                             }
                         });
                     }
                 });
             }
         };
     });