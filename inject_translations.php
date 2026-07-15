<?php

$translations = [
    'en' => [
        'admin' => [
            "'media_alt' => 'Media Alt Text (SEO)',",
            "'save_blog' => 'Save Blog',",
            "'blog_created' => 'Blog created successfully.',",
            "'blog_updated' => 'Blog updated successfully.',",
            "'blog_deleted' => 'Blog deleted successfully.',"
        ],
        'forms' => [
            "'validation_error' => 'Please check the form for errors.',",
            "'sections' => 'Sections',",
            "'profile_health' => 'Profile Health',",
            "'completeness' => 'Completeness',",
            "'biz_name_hint' => 'Enter your business name.',",
            "'biz_desc_hint' => 'Write a short description.',",
            "'cover_image' => 'Cover Image',",
            "'social_desc' => 'Social Media Links',",
            "'no_images_uploaded' => 'No images uploaded yet.',"
        ]
    ],
    'ar' => [
        'admin' => [
            "'media_alt' => 'النص البديل للوسائط (SEO)',",
            "'save_blog' => 'حفظ المقال',",
            "'blog_created' => 'تم إنشاء المقال بنجاح.',",
            "'blog_updated' => 'تم تحديث المقال بنجاح.',",
            "'blog_deleted' => 'تم حذف المقال بنجاح.',"
        ],
        'forms' => [
            "'validation_error' => 'يرجى التحقق من النموذج بحثًا عن أخطاء.',",
            "'sections' => 'الأقسام',",
            "'profile_health' => 'صحة الملف الشخصي',",
            "'completeness' => 'الاكتمال',",
            "'biz_name_hint' => 'أدخل اسم عملك.',",
            "'biz_desc_hint' => 'اكتب وصفاً قصيراً.',",
            "'cover_image' => 'صورة الغلاف',",
            "'social_desc' => 'روابط وسائل التواصل الاجتماعي',",
            "'no_images_uploaded' => 'لم يتم رفع أي صور بعد.',"
        ]
    ],
    'es' => [
        'admin' => [
            "'media_alt' => 'Texto alternativo de medios (SEO)',",
            "'save_blog' => 'Guardar Blog',",
            "'blog_created' => 'Blog creado con éxito.',",
            "'blog_updated' => 'Blog actualizado con éxito.',",
            "'blog_deleted' => 'Blog eliminado con éxito.',"
        ],
        'forms' => [
            "'validation_error' => 'Por favor, revise el formulario en busca de errores.',",
            "'sections' => 'Secciones',",
            "'profile_health' => 'Salud del perfil',",
            "'completeness' => 'Integridad',",
            "'biz_name_hint' => 'Ingrese el nombre de su negocio.',",
            "'biz_desc_hint' => 'Escriba una breve descripción.',",
            "'cover_image' => 'Imagen de portada',",
            "'social_desc' => 'Enlaces de redes sociales',",
            "'no_images_uploaded' => 'No se han subido imágenes todavía.',"
        ]
    ],
    'de' => [
        'admin' => [
            "'media_alt' => 'Medien-Alternativtext (SEO)',",
            "'save_blog' => 'Blog speichern',",
            "'blog_created' => 'Blog erfolgreich erstellt.',",
            "'blog_updated' => 'Blog erfolgreich aktualisiert.',",
            "'blog_deleted' => 'Blog erfolgreich gelöscht.',"
        ],
        'forms' => [
            "'validation_error' => 'Bitte überprüfen Sie das Formular auf Fehler.',",
            "'sections' => 'Abschnitte',",
            "'profile_health' => 'Profilgesundheit',",
            "'completeness' => 'Vollständigkeit',",
            "'biz_name_hint' => 'Geben Sie Ihren Firmennamen ein.',",
            "'biz_desc_hint' => 'Schreiben Sie eine kurze Beschreibung.',",
            "'cover_image' => 'Titelbild',",
            "'social_desc' => 'Social Media Links',",
            "'no_images_uploaded' => 'Noch keine Bilder hochgeladen.',"
        ]
    ],
    'zh' => [
        'admin' => [
            "'media_alt' => '媒体替代文本 (SEO)',",
            "'save_blog' => '保存博客',",
            "'blog_created' => '博客创建成功。',",
            "'blog_updated' => '博客更新成功。',",
            "'blog_deleted' => '博客删除成功。',"
        ],
        'forms' => [
            "'validation_error' => '请检查表单是否有错误。',",
            "'sections' => '部分',",
            "'profile_health' => '个人资料健康度',",
            "'completeness' => '完整性',",
            "'biz_name_hint' => '输入您的公司名称。',",
            "'biz_desc_hint' => '写一个简短的描述。',",
            "'cover_image' => '封面图片',",
            "'social_desc' => '社交媒体链接',",
            "'no_images_uploaded' => '尚未上传任何图片。',"
        ]
    ],
    'tr' => [
        'admin' => [
            "'media_alt' => 'Medya Alternatif Metni (SEO)',",
            "'save_blog' => 'Blogu Kaydet',",
            "'blog_created' => 'Blog başarıyla oluşturuldu.',",
            "'blog_updated' => 'Blog başarıyla güncellendi.',",
            "'blog_deleted' => 'Blog başarıyla silindi.',"
        ],
        'forms' => [
            "'validation_error' => 'Lütfen formdaki hataları kontrol edin.',",
            "'sections' => 'Bölümler',",
            "'profile_health' => 'Profil Sağlığı',",
            "'completeness' => 'Tamamlanma',",
            "'biz_name_hint' => 'İşletme adınızı girin.',",
            "'biz_desc_hint' => 'Kısa bir açıklama yazın.',",
            "'cover_image' => 'Kapak Resmi',",
            "'social_desc' => 'Sosyal Medya Bağlantıları',",
            "'no_images_uploaded' => 'Henüz resim yüklenmedi.',"
        ]
    ]
];

foreach ($translations as $loc => $files) {
    foreach ($files as $file => $lines) {
        $path = __DIR__ . "/lang/$loc/$file.php";
        if (file_exists($path)) {
            $content = file_get_contents($path);
            
            if ($file === 'admin') {
                // Just append before the last ];
                $insert = "\n    // newly added\n    " . implode("\n    ", $lines) . "\n";
                $content = preg_replace('/];\s*$/', $insert . "];\n", $content);
            } elseif ($file === 'forms') {
                // We need to inject inside 'business' => [ ... ]
                // Find 'business' => [ and append
                $insert = "\n        // newly added\n        " . implode("\n        ", $lines) . "\n";
                $content = preg_replace('/\'business\'\s*=>\s*\[/', "'business' => [" . $insert, $content);
            }
            
            file_put_contents($path, $content);
            echo "Updated $loc/$file.php\n";
        }
    }
}
