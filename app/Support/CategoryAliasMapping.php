<?php

namespace App\Support;

class CategoryAliasMapping
{
    /**
     * Map of normalized search terms to target [new_category_id, level, needs_review].
     */
    public static array $aliases = [
        // Google Maps place types & production pending categories
        'mattress store' => [507, 2, false],
        'mattress shop' => [507, 2, false],
        'home goods store' => [501, 2, false],
        'carpet store' => [506, 2, false],
        'carpet and flooring shop' => [506, 2, false],
        'curtain store' => [506, 2, false],
        'kitchen supply store' => [505, 2, false],
        'homewares shop' => [505, 2, false], // Approved for 505 (In prod: Cairo Capital Kitchens Factory ID 545)
        'home accessories' => [508, 2, false],
        'office supply store' => [502, 2, false],
        'department store' => [501, 2, false],
        'exhibition and trade center' => [501, 2, false],
        'shopping & retail' => [501, 2, false],
        'shopping mall' => [507, 2, false], // In prod data, the entry was Yanssen/Englander mattress store
        'business center' => [306, 2, false],
        'lumber store' => [105, 2, false],
        'woodworking supply store' => [105, 2, false],
        'woodworking supply shop' => [1708, 2, false],
        'manufacturer' => [17, 1, true],
        'design engineer' => [403, 2, false],
        'interior_designer' => [411, 2, false],
        'interior designer' => [411, 2, false],
        'handicraft' => [508, 2, false],
        'saw mill' => [105, 2, false],
        'small manual crushing plant near burhama area.' => [1, 1, true], // Haji Hassan Group -> L1 1 + needs_review
        'haji hassan group' => [1, 1, true], // Haji Hassan Group -> L1 1 + needs_review
        'لراحتك' => [21, 1, true], // Company لراحتك -> L1 21 + needs_review
        'liaoning borui machinery co., ltd' => [1707, 2, false], // ID 25 approved for 1707
        'rd automation' => [1602, 2, false], // ID 176 approved for 1602
        'مصنع القاهرة كابيتل للمطابخ و الاثاث' => [505, 2, false], // ID 545 approved for 505

        // Industrial machinery main & sub category aliases (Final_2)
        'الآلات والمعدات الصناعية' => [17, 1, false],
        'آلات ومعدات صناعية' => [17, 1, false],
        'industrial machinery & equipment' => [17, 1, false],
        'industrial machinery' => [17, 1, false],
        'التكييف والتبريد والمصاعد' => [1709, 2, false],
        'قطع غيار وتصنيع وتشغيل الماكينات' => [1710, 2, false],
        'مضخات وضواغط' => [1711, 2, false],
        'معدات الأغذية والمطاعم' => [1712, 2, false],
        'معدات البناء والرافعات والتأجير' => [1713, 2, false],
        'معدات الزراعة والطاقة والمعدات المتخصصة' => [1714, 2, false],
        'معدات صناعية عامة' => [1715, 2, false],

        // Production legacy active categories
        'furniture' => [5, 1, true],
        'الأثاث' => [5, 1, true],
        'كهرباء وإضاءة' => [6, 1, true],
        'lighting & electrical' => [6, 1, true],
        'مواد بناء وسيراميك' => [1, 1, true],
        'building materials and ceramics' => [1, 1, true],
        'المواد البناء و السراميك' => [1, 1, true],
        'construction' => [4, 1, true],
        'مقاولات وبناء' => [4, 1, true],
        'interior design' => [4, 1, true],
        'ديكور وتشطيبات' => [4, 1, true],
        'electronics' => [7, 1, true],
        'الإلكترونيات' => [7, 1, true],
        'technology & software' => [16, 1, true],
        'تكنولوجيا وبرمجيات' => [16, 1, true],
        'restaurants & cafes' => [8, 1, true],
        'مطاعم وكافيهات' => [8, 1, true],
        'real estate' => [3, 1, true],
        'عقارات وأراضي' => [3, 1, true],
        'medical services' => [10, 1, true],
        'خدمات طبية ومستشفيات' => [10, 1, true],
        'cars & showrooms' => [2, 1, true],
        'سيارات ومعارض' => [2, 1, true],
        'beauty & personal care' => [11, 1, true],
        'تجميل وعناية شخصية' => [11, 1, true],
        'fashion & clothing' => [19, 1, true],
        'أزياء وملابس' => [19, 1, true],
        'tourism & hotels' => [13, 1, true],
        'سياحة وفنادق' => [13, 1, true],
        'shipping & transport' => [22, 1, true],
        'نقل وشحن' => [22, 1, true],
        'sports & gym' => [20, 1, true],
        'رياضة وجيم' => [20, 1, true],
        'furnishings' => [5, 1, true],
        'المفروشات' => [5, 1, true],
        'home appliances' => [7, 1, true],
        'الكهرومنزلية' => [7, 1, true],
        'vehicles' => [2, 1, true],
        'السيارات والمركبات' => [2, 1, true],
        'services' => [14, 1, true],
        'الخدمات' => [14, 1, true],
        'food companies' => [9, 1, true],
        'شركات الأغذية' => [9, 1, true],
        'accessories' => [1905, 2, false],
        'إكسسوارات' => [1905, 2, false],
        'precious metals' => [1905, 2, false],
        'معادن ثمينة' => [1905, 2, false],
        'asa' => [null, 0, true], // flagged for manual review, never created

        // Batch Category Audit: Industrial & Manufacturing (L1 ID 17)
        'صمامات وأنابيب صناعية' => [1716, 2, false],
        'صمامات الانابيب الصناعية' => [1716, 2, false],
        'صمامات وأنابيب' => [1716, 2, false],
        'السيور الناقلة ومعدات المناولة' => [1717, 2, false],
        'السيور الناقلة' => [1717, 2, false],
        'المعدات والآلات الصناعية' => [17, 1, false],
        'معدات وآلات صناعية' => [17, 1, false],
        'ماكينات ومعدات صناعية' => [17, 1, false],
        'الماكينات والمعدات الصناعية' => [17, 1, false],
        'مراوح وشفاطات صناعية' => [1732, 2, false],
        'مراوح وشفاطات' => [1732, 2, false],
        'معدات التهوية والتكييف الصناعي' => [1709, 2, false],
        'معدات الحريق والإطفاء' => [1731, 2, false],
        'معدات الحرايق والاطفاء' => [1731, 2, false],
        'معدات الحريق والإطفاء والسلامة' => [1731, 2, false],
        'معدات المحاجر والكسارات' => [1730, 2, false],
        'محاجر' => [1730, 2, false],
        'معدات محاجر وكسارات' => [1730, 2, false],
        'صيانة وتشغيل معدات المحاجر' => [1730, 2, false],
        'تعدين وخامات' => [1730, 2, false],
        'معدات ثقيلة وصيانة' => [1713, 2, false],
        'معدات ثقيلة وبناء' => [1713, 2, false],
        'ماكينات النجارة ومعدات الأخشاب' => [1722, 2, false],
        'ماكينات النجارة' => [1722, 2, false],
        'ماكينات النجار والاخشاب' => [1722, 2, false],
        'ماكينات نشر الأخشاب' => [1722, 2, false],
        'تجارة الأخشاب' => [1722, 2, false],
        'مستلزمات النجارة والأخشاب' => [1722, 2, false],
        'الغلايات والمراجل البخارية' => [1719, 2, false],
        'الغلايات البخارية' => [1719, 2, false],
        'معدات اللحام والقطع' => [1720, 2, false],
        'ماكينات الليزر والسي إن سي' => [1721, 2, false],
        'ماكينات الليزر' => [1721, 2, false],
        'ورش صيانة ماكينات الليزر والسي إن سي' => [1721, 2, false],
        'قطع غيار وصيانة ماكينات الليزر' => [1721, 2, false],
        'ماكينات ومواد التعبئة والتغليف' => [1726, 2, false],
        'ماكينات التعبئة والتغليف' => [1726, 2, false],
        'التعبئة والتغليف' => [1726, 2, false],
        'مستلزمات ومواد التعبئة والتغليف' => [1726, 2, false],
        'الموازين والمعايرة الصناعية' => [1727, 2, false],
        'موردو ومصنعو الموازين' => [1727, 2, false],
        'المعايرة وأدوات القياس' => [1727, 2, false],
        'صيانة وإصلاح الموازين' => [1727, 2, false],
        'إعادة التدوير وإدارة المخلفات' => [1728, 2, false],
        'معدات تدوير المخلفات' => [1728, 2, false],
        'إعادة تدوير المخلفات الإلكترونية' => [1728, 2, false],
        'إعادة تدوير المخلفات الصلبة' => [1728, 2, false],
        'إعادة تدوير الأخشاب وتصنيع الوقود الحيوي' => [1728, 2, false],
        'إعادة تدوير المخلفات الزراعية وتصنيع الألياف' => [1728, 2, false],
        'محطات ومعالجة المياه' => [1729, 2, false],
        'محطات معالجة المياه' => [1729, 2, false],
        'محطات_معالجة_المياة' => [1729, 2, false],
        'ماكينات وقوالب حقن البلاستيك' => [1723, 2, false],
        'مصانع حقن البلاستيك' => [1723, 2, false],
        'قوالب واسطمبات حقن البلاستيك' => [1723, 2, false],
        'ماكينات حقن البلاستيك' => [1723, 2, false],
        'حقن البلاستيك' => [1723, 2, false],
        'ماكينات النسيج والملابس الجاهزة' => [1724, 2, false],
        'ماكينات النسيج والملابس' => [1724, 2, false],
        'ماكينات ومعدات الطباعة' => [1725, 2, false],
        'ماكينات الطباعة' => [1725, 2, false],
        'ماكينات التصوير والطابعات' => [1725, 2, false],
        'طباعة الورق' => [1725, 2, false],
        'معدات الهيدروليك والنيوماتيك' => [1718, 2, false],
        'معدات الهيدروليك' => [1718, 2, false],
        'صيانة وإصلاح المعدات الهيدروليكية' => [1718, 2, false],
        'تصنيع المعدات الهيدروليكية' => [1718, 2, false],
        'مضخات ومحركات هيدروليكية' => [1718, 2, false],
        'خراطيم ووصلات هيدروليك' => [1718, 2, false],

        // Batch Category Audit: Appliances & Electronics (L1 ID 7)
        'الأجهزة الإلكترونية' => [7, 1, false],
        'أجهزة إلكترونية عامة' => [711, 2, false],
        'أجهزة كهربائية' => [7, 1, false],
        'طابعات وأجهزة مكتبية' => [709, 2, false],
        'أجهزة صوتيات وسماعات' => [710, 2, false],
        'شاشات وتلفزيونات' => [703, 2, false],
        'كاميرات مراقبة وأنظمة أمنية' => [707, 2, false],

        // Batch Category Audit: Agriculture & Livestock (L1 ID 18)
        'الزراعة والمعدات الزراعية' => [18, 1, false],
        'معدات وآلات زراعية' => [1804, 2, false],
        'أنظمة الري والرش' => [1804, 2, false],
        'أدوات وعدد زراعية' => [1804, 2, false],
        'جرارات وقطع غيار زراعية' => [1804, 2, false],
        'صوب زراعية وشبكات تظليل' => [1807, 2, false],
        'حبوب وغلال وصوامع' => [1808, 2, false],

        // Batch Category Audit: Cars & Vehicles (L1 ID 2)
        'معدات ورش وجراجات السيارات' => [211, 2, false],
        'معدات ورشة والجراج' => [211, 2, false],
        'معدات ورش وجراجات' => [211, 2, false],

        // Batch Category Audit: Food & Agro Export (L1 ID 9)
        'الفواكه' => [908, 2, false],
        'فواكه' => [908, 2, false],
        'تصدير الفواكه الطازجة' => [908, 2, false],
        'تصدير الفواكه الطازجه' => [908, 2, false],
        'تصدير فواكه طازجة' => [908, 2, false],
        'تصدير الفواكه والخضروات' => [908, 2, false],
        'تصدير الفواكه' => [908, 2, false],
        'fresh fruits export' => [908, 2, false],
        'fresh fruit export' => [908, 2, false],
        'fresh fruits & vegetables export' => [908, 2, false],
        'محطات تعبئة وتجهيز الفواكه' => [909, 2, false],
        'محطات تعبئة وتجهيز الخضار والفاكهة' => [909, 2, false],
        'محطات تعبئة وتجهيز الحاصلات الزراعية' => [909, 2, false],
        'محطات فرز وتعبئة الحاصلات الزراعية' => [909, 2, false],
        'محطات فرز وتعبئة الفاكهة' => [909, 2, false],
        'محطات تعبئة الفواكه' => [909, 2, false],
        'agricultural packing & sorting stations' => [909, 2, false],
        'fruit packing stations' => [909, 2, false],
        'تبريد وتخزين الفواكه' => [910, 2, false],
        'تبريد وتخزين المواد الغذائية' => [910, 2, false],
        'تخزين وتبريد الفواكه' => [910, 2, false],
        'ثلاجات حفظ الفاكهة' => [910, 2, false],
        'food cold storage & logistics' => [910, 2, false],
        'food cold storage' => [910, 2, false],
        'تصدير الحبوب والأرز والسكر' => [911, 2, false],
        'تصدير الحبوب والارز والسكر' => [911, 2, false],
        'تصدير الحبوب والأرز' => [911, 2, false],
        'تصدير الحبوب والارز' => [911, 2, false],
        'تصدير الأرز والحبوب' => [911, 2, false],
        'تصدير الارز والحبوب' => [911, 2, false],
        'تصدير السكر والحبوب' => [911, 2, false],
        'grains, rice & sugar export' => [911, 2, false],
        'تصدير الزيوت والتوابل والأعشاب' => [912, 2, false],
        'تصدير الزيوت والتوابل والاعشاب' => [912, 2, false],
        'تصدير التوابل والأعشاب' => [912, 2, false],
        'تصدير التوابل والاعشاب' => [912, 2, false],
        'تصدير الزيوت النباتية' => [912, 2, false],
        'تصدير الأعشاب الطبية' => [912, 2, false],
        'تصدير الاعشاب الطبية' => [912, 2, false],
        'oils, spices & medicinal herbs export' => [912, 2, false],
        'oils, spices & herbs export' => [912, 2, false],
        'تصدير العسل والتمور والمكسرات' => [913, 2, false],
        'تصدير التمور والعسل والمكسرات' => [913, 2, false],
        'تصدير التمور والعسل' => [913, 2, false],
        'تصدير التمور المصرية' => [913, 2, false],
        'تصدير عسل النحل' => [913, 2, false],
        'honey, dates & nuts export' => [913, 2, false],
        'تصدير المعلبات والمخللات والمجمدات' => [914, 2, false],
        'تصدير المعلبات والمخللات والمجمدات الغذائية' => [914, 2, false],
        'تصدير المخللات والمعلبات' => [914, 2, false],
        'تصدير الخضار والفاكهة المجمدة' => [914, 2, false],
        'تصدير الزيتون والمخللات' => [914, 2, false],
        'مخللات ومعلبات ومجمدات' => [914, 2, false],
        'canned, pickled & frozen foods export' => [914, 2, false],
        'canned & frozen foods export' => [914, 2, false],
        'تصدير اللحوم والدواجن والأسماك' => [915, 2, false],
        'تصدير اللحوم والدواجن والاسماك' => [915, 2, false],
        'تصدير اللحوم والدواجن' => [915, 2, false],
        'تصدير الأسماك والمأكولات البحرية' => [915, 2, false],
        'تصدير الاسماك والماكولات البحرية' => [915, 2, false],
        'meat, poultry & fish export' => [915, 2, false],
        'meat, poultry & seafood export' => [915, 2, false],
        'تصدير الألبان والأجبان' => [916, 2, false],
        'تصدير الالبان والاجبان' => [916, 2, false],
        'تصدير الألبان والأجبان ومنتجات الحليب' => [916, 2, false],
        'تصدير منتجات الألبان' => [916, 2, false],
        'تصدير منتجات الالبان' => [916, 2, false],
        'dairy & cheese products export' => [916, 2, false],
        'dairy & cheese export' => [916, 2, false],
        'تصدير المكرونة والدقيق والمخبوزات والحلويات' => [917, 2, false],
        'تصدير المكرونة والدقيق والحلويات' => [917, 2, false],
        'تصدير المكرونة والمخبوزات' => [917, 2, false],
        'تصدير الدقيق والمخبوزات' => [917, 2, false],
        'تصدير البسكويت والحلويات' => [917, 2, false],
        'pasta, flour, bakery & confectionery export' => [917, 2, false],
        'pasta, bakery & confectionery export' => [917, 2, false],
        'تصدير العصائر والمشروبات والشاي والقهوة' => [918, 2, false],
        'تصدير العصائر والمشروبات' => [918, 2, false],
        'تصدير مركزات العصائر' => [918, 2, false],
        'تصدير الشاي والقهوة' => [918, 2, false],
        'juices, beverages, tea & coffee export' => [918, 2, false],
        'juices & beverages export' => [918, 2, false],
        'تصدير مواد غذائية متنوعة' => [919, 2, false],
        'تصدير المواد الغذائية المتنوعة' => [919, 2, false],
        'تصدير المواد الغذائية' => [919, 2, false],
        'تصدير مواد غذائية' => [919, 2, false],
        'استيراد وتصدير مواد غذائية' => [919, 2, false],
        'شركات تصدير مواد غذائية' => [919, 2, false],
        'food export' => [919, 2, false],
        'food exports' => [919, 2, false],
        'general foodstuff export & import' => [919, 2, false],
        'general food export' => [919, 2, false],

        // Batch Category Audit: Fashion & Apparel (L1 ID 19)
        'الأزياء والملابس' => [19, 1, false],
        'الملابس والأزياء' => [19, 1, false],
        'الملابس والأزياء النسائية' => [1902, 2, false],
        'الملابس والازياء النسائية' => [1902, 2, false],
        'ملابس نسائية' => [1902, 2, false],
        'متاجر ملابس رجالي' => [1901, 2, false],
        'محلات ملابس رجالي' => [1901, 2, false],
        'مصانع قمصان رجالي' => [1901, 2, false],
        'ملابس منزلية رجالي' => [1901, 2, false],
        'men\'s clothing stores' => [1901, 2, false],
        'mens clothing stores' => [1901, 2, false],
        'بدل رجالي جاهزة' => [1909, 2, false],
        'بدل رجالي' => [1909, 2, false],
        'مصانع بدل رجالي' => [1909, 2, false],
        'بدل ورسميات رجالي' => [1909, 2, false],
        'بدل ورسميات رجالي ومصانع تفصيل' => [1909, 2, false],
        'men\'s suits & formalwear' => [1909, 2, false],
        'mens suits & formalwear' => [1909, 2, false],
        'mens suits' => [1909, 2, false],
        'جينز رجالي' => [1910, 2, false],
        'جينز وبناطيل نسائية' => [1910, 2, false],
        'جينز وبناطيل' => [1910, 2, false],
        'جينز وملابس كاجوال' => [1910, 2, false],
        'جينز وملابس كاجوال وبناطيل' => [1910, 2, false],
        'ملابس رجالي كاجوال جملة' => [1910, 2, false],
        'ملابس كاجوال رجالي' => [1910, 2, false],
        'jeans, denim & casual trousers' => [1910, 2, false],
        'jeans & denim' => [1910, 2, false],
        'denim & jeans' => [1910, 2, false],
        'ملابس رجالي مقاسات كبيرة' => [1911, 2, false],
        'ملابس مقاسات كبيرة' => [1911, 2, false],
        'ملابس مقاسات خاصة' => [1911, 2, false],
        'ملابس مقاسات خاصة وكبيرة' => [1911, 2, false],
        'plus size & big fashion' => [1911, 2, false],
        'plus size clothing' => [1911, 2, false],
        'عبايات وإسدالات' => [1912, 2, false],
        'عبايات واسدالات' => [1912, 2, false],
        'عبايات وإسدالات وملابس محجبات' => [1912, 2, false],
        'عبايات واسدالات وملابس محجبات' => [1912, 2, false],
        'ملابس محجبات وأزياء إسلامية' => [1912, 2, false],
        'ملابس محجبات وازياء اسلامية' => [1912, 2, false],
        'ملابس محجبات' => [1912, 2, false],
        'أزياء إسلامية' => [1912, 2, false],
        'ازياء اسلامية' => [1912, 2, false],
        'abayas, isdals & islamic modest wear' => [1912, 2, false],
        'abayas & isdals' => [1912, 2, false],
        'modest wear' => [1912, 2, false],
        'حجاب وإيشاربات' => [1913, 2, false],
        'حجاب وايشاربات' => [1913, 2, false],
        'حجاب وإيشاربات وطرح وإكسسوارات الحجاب' => [1913, 2, false],
        'طرح وإيشاربات' => [1913, 2, false],
        'طرح وايشاربات' => [1913, 2, false],
        'إيشاربات وطرح' => [1913, 2, false],
        'مكملات الحجاب' => [1913, 2, false],
        'hijabs, scarves & headwear' => [1913, 2, false],
        'hijabs & scarves' => [1913, 2, false],
        'ملابس داخلية ولانجري' => [1914, 2, false],
        'ملابس داخليه ولانجري' => [1914, 2, false],
        'لانجري وملابس داخلية' => [1914, 2, false],
        'لانجيري' => [1914, 2, false],
        'ملابس داخلية' => [1914, 2, false],
        'lingerie & underwear' => [1914, 2, false],
        'lingerie' => [1914, 2, false],
        'ملابس بيتي ونوم' => [1915, 2, false],
        'ملابس بيتي وبيجامات ونوم' => [1915, 2, false],
        'بيجامات وملابس بيتي' => [1915, 2, false],
        'ملابس نوم وبيجامات' => [1915, 2, false],
        'homewear, pajamas & sleepwear' => [1915, 2, false],
        'homewear & sleepwear' => [1915, 2, false],
        'pajamas & sleepwear' => [1915, 2, false],
        'فساتين زفاف وسهرة' => [1916, 2, false],
        'فساتين زفاف وسهرة وسواريه' => [1916, 2, false],
        'فساتين سواريه وسهرة' => [1916, 2, false],
        'فساتين زفاف' => [1916, 2, false],
        'فساتين سهرة' => [1916, 2, false],
        'bridal & evening dresses' => [1916, 2, false],
        'evening dresses' => [1916, 2, false],
        'wedding dresses' => [1916, 2, false],
        'متاجر ملابس نسائية' => [1902, 2, false],
        'محلات ملابس نسائية' => [1902, 2, false],
        'مصانع ملابس نسائية' => [1902, 2, false],
        'ملابس نسائية بالجملة' => [1902, 2, false],
        'تجارة ملابس حريمي بالجملة' => [1902, 2, false],
        'women\'s clothing stores' => [1902, 2, false],
        'womens clothing stores' => [1902, 2, false],
        'women\'s clothing factories' => [1902, 2, false],
        'women\'s clothing wholesale' => [1902, 2, false],
        'ملابس رياضية نسائية' => [1907, 2, false],
        'ملابس رياضية حريمي' => [1907, 2, false],
        'women\'s sportswear' => [1907, 2, false],
        'womens sportswear' => [1907, 2, false],
        // Final_35 Sheet Category Additions & Normalizations
        // Main categories fallback
        'الصناعة والتصنيع' => [17, 1, false],
        'صناعة وتصنيع' => [17, 1, false],
        'الرياضة والترفيه' => [20, 1, false],
        'رياضة وترفيه' => [20, 1, false],

        // ID 212: تمويل وتقسيط السيارات
        'تمويل وتقسيط السيارات' => [212, 2, false],
        'تمويل وتقسيط سيارات' => [212, 2, false],
        'تقسيط سيارات' => [212, 2, false],
        'تمويل سيارات' => [212, 2, false],
        'تقسيط السيارات' => [212, 2, false],
        'تمويل السيارات' => [212, 2, false],
        'شركات تقسيط سيارات' => [212, 2, false],
        'شركات تمويل السيارات' => [212, 2, false],
        'قروض سيارات' => [212, 2, false],
        'car financing & installments' => [212, 2, false],
        'auto financing & installments' => [212, 2, false],
        'car financing' => [212, 2, false],
        'auto financing' => [212, 2, false],
        'car installments' => [212, 2, false],
        'auto loans' => [212, 2, false],

        // ID 213: دراجات هوائية
        'دراجات هوائية' => [213, 2, false],
        'دراجات هوائيه' => [213, 2, false],
        'دراجات هوائية ومعدات ركوب' => [213, 2, false],
        'محلات دراجات هوائية' => [213, 2, false],
        'محلات دراجات هوائيه' => [213, 2, false],
        'محلات عجل' => [213, 2, false],
        'تجارة دراجات هوائية' => [213, 2, false],
        'دراجات هوائية وعجل' => [213, 2, false],
        'عجل هوائي' => [213, 2, false],
        'bicycles & cycling gear' => [213, 2, false],
        'bicycles' => [213, 2, false],
        'bicycle stores' => [213, 2, false],
        'cycling gear' => [213, 2, false],
        'bicycles and cycling' => [213, 2, false],
        'bikes' => [213, 2, false],

        // ID 214: فحص وتقييم السيارات
        'فحص وتقييم السيارات' => [214, 2, false],
        'فحص وتقييم سيارات' => [214, 2, false],
        'فحص سيارات' => [214, 2, false],
        'تقييم سيارات' => [214, 2, false],
        'مراكز فحص السيارات' => [214, 2, false],
        'مراكز فحص سيارات' => [214, 2, false],
        'كشف كمبيوتر سيارات' => [214, 2, false],
        'كشف سيارات قبل الشراء' => [214, 2, false],
        'فحص السيارات المستعملة' => [214, 2, false],
        'تقييم وتسعير السيارات' => [214, 2, false],
        'car inspection & valuation' => [214, 2, false],
        'car inspection' => [214, 2, false],
        'vehicle inspection' => [214, 2, false],
        'car valuation' => [214, 2, false],
        'pre-purchase car inspection' => [214, 2, false],
        'auto inspection & valuation' => [214, 2, false],

        // ID 215: معدات خدمة السيارات
        'معدات خدمة السيارات' => [215, 2, false],
        'معدات خدمة سيارات' => [215, 2, false],
        'تجهيز محطات خدمة السيارات' => [215, 2, false],
        'معدات مراكز خدمة السيارات' => [215, 2, false],
        'معدات محطات البنزين والخدمة' => [215, 2, false],
        'خدمة سيارات ومعدات' => [215, 2, false],
        'automotive service equipment' => [215, 2, false],
        'auto service equipment' => [215, 2, false],
        'car service equipment' => [215, 2, false],
        'vehicle service equipment' => [215, 2, false],

        // ID 1733: مصانع الألعاب المائية والأكوا بارك
        'مصانع الألعاب المائية والأكوا بارك' => [1733, 2, false],
        'مصانع الالعاب المائية والاكوا بارك' => [1733, 2, false],
        'ألعاب مائية وأكوا بارك' => [1733, 2, false],
        'العاب مائية واكوا بارك' => [1733, 2, false],
        'ألعاب مائية واكوا بارك' => [1733, 2, false],
        'مصانع ألعاب مائية' => [1733, 2, false],
        'مصانع العاب مائية' => [1733, 2, false],
        'مصانع أكوا بارك' => [1733, 2, false],
        'مصانع اكوا بارك' => [1733, 2, false],
        'تجهيز أكوا بارك وألعاب مائية' => [1733, 2, false],
        'زحاليق وألعاب مائية' => [1733, 2, false],
        'water park & aqua park equipment manufacturers' => [1733, 2, false],
        'water park & aqua park factories' => [1733, 2, false],
        'aqua park equipment' => [1733, 2, false],
        'water park manufacturers' => [1733, 2, false],
        'water slides manufacturers' => [1733, 2, false],

        // ID 1734: مصانع البطاريات الصناعية
        'مصانع البطاريات الصناعية' => [1734, 2, false],
        'مصانع البطاريات الصناعيه' => [1734, 2, false],
        'بطاريات صناعية' => [1734, 2, false],
        'بطاريات صناعيه' => [1734, 2, false],
        'مصانع بطاريات صناعية' => [1734, 2, false],
        'مصانع بطاريات الطاقة الشمسية' => [1734, 2, false],
        'بطاريات رافعات وبطاريات صناعية' => [1734, 2, false],
        'بطاريات ups وتخزين طاقة' => [1734, 2, false],
        'industrial battery manufacturers' => [1734, 2, false],
        'industrial battery factories' => [1734, 2, false],
        'industrial batteries' => [1734, 2, false],
        'traction battery manufacturers' => [1734, 2, false],
        'solar storage batteries' => [1734, 2, false],

        // ID 1917: إكسسوارات نسائية
        'إكسسوارات نسائية' => [1917, 2, false],
        'إكسسوارات نسائيه' => [1917, 2, false],
        'اكسسوارات نسائية' => [1917, 2, false],
        'اكسسوارات نسائيه' => [1917, 2, false],
        'إكسسوارات حريمي' => [1917, 2, false],
        'اكسسوارات حريمي' => [1917, 2, false],
        'متاجر إكسسوارات نسائية' => [1917, 2, false],
        'محلات إكسسوارات حريمي' => [1917, 2, false],
        'محلات اكسسوارات حريمي' => [1917, 2, false],
        'إكسسوارات فاشون نسائية' => [1917, 2, false],
        'مجوهرات بديلة وإكسسوارات نسائية' => [1917, 2, false],
        'women\'s fashion accessories' => [1917, 2, false],
        'womens fashion accessories' => [1917, 2, false],
        'women accessories' => [1917, 2, false],
        'womens accessories' => [1917, 2, false],
        'ladies accessories' => [1917, 2, false],

        // ID 1918: ملابس متنوعة
        'ملابس متنوعة' => [1918, 2, false],
        'ملابس متنوعه' => [1918, 2, false],
        'محلات ملابس متنوعة' => [1918, 2, false],
        'متاجر ملابس متنوعة' => [1918, 2, false],
        'أزياء وملابس متنوعة' => [1918, 2, false],
        'تجارة ملابس متنوعة' => [1918, 2, false],
        'ملابس جاهزة متنوعة' => [1918, 2, false],
        'ملابس عامة ومتنوعة' => [1918, 2, false],
        'general & miscellaneous apparel' => [1918, 2, false],
        'general apparel' => [1918, 2, false],
        'miscellaneous apparel' => [1918, 2, false],
        'mixed apparel' => [1918, 2, false],
        'general clothing' => [1918, 2, false],
        'mixed clothing' => [1918, 2, false],

        // ID 2008: أدوات الصيد
        'أدوات الصيد' => [2008, 2, false],
        'ادوات الصيد' => [2008, 2, false],
        'أدوات صيد' => [2008, 2, false],
        'ادوات صيد' => [2008, 2, false],
        'مستلزمات صيد' => [2008, 2, false],
        'مستلزمات صيد الأسماك' => [2008, 2, false],
        'مستلزمات صيد الاسماك' => [2008, 2, false],
        'أدوات صيد الأسماك' => [2008, 2, false],
        'ادوات صيد الاسماك' => [2008, 2, false],
        'محلات أدوات الصيد' => [2008, 2, false],
        'محلات ادوات الصيد' => [2008, 2, false],
        'معدات الصيد والسنارات' => [2008, 2, false],
        'صيد أسماك ومعدات' => [2008, 2, false],
        'fishing & hunting tackle' => [2008, 2, false],
        'fishing tackle & equipment' => [2008, 2, false],
        'fishing tackle' => [2008, 2, false],
        'fishing equipment' => [2008, 2, false],
        'hunting & fishing gear' => [2008, 2, false],
        'fishing gear' => [2008, 2, false],
    ];

    /**
     * Reverse map from New L1 Category ID to legacy categories.id for dual-write.
     */
    public static array $newToLegacyL1 = [
        1 => 67, // Building Materials -> مواد بناء وسيراميك
        2 => 18, // Cars & Vehicles -> Cars & Showrooms
        3 => 14, // Real Estate -> Real Estate
        4 => 15, // Contracting -> Construction
        5 => 26, // Furniture -> Furniture
        6 => 66, // Lighting -> كهرباء وإضاءة
        7 => 28, // Appliances & Electronics -> Home appliances
        8 => 13, // Restaurants & Cafes -> Restaurants & Cafes
        9 => 33, // Food -> Food Companies
        10 => 17, // Health & Medical -> Medical Services
        11 => 20, // Beauty -> Beauty & Personal Care
        13 => 22, // Tourism -> Tourism & Hotels
        14 => 31, // Business Services -> Services
        16 => 19, // Technology -> Technology & Software
        17 => 46, // Industry & Manufacturing -> Manufacturer
        18 => 33, // Agriculture & Livestock -> Food Companies
        19 => 21, // Fashion -> Fashion & Clothing
        20 => 24, // Sports -> Sports & Gym
        22 => 23, // Shipping -> Shipping & Transport
    ];

    /**
     * Resolve a raw category string to [new_category_id, level, needs_review].
     */
    public static function resolve(?string $rawName): ?array
    {
        if (empty($rawName)) {
            return null;
        }

        $key = mb_strtolower(trim($rawName), 'UTF-8');

        if (isset(self::$aliases[$key])) {
            $mapped = self::$aliases[$key];
            if ($mapped[0] === null) {
                return null;
            }

            return [
                'new_category_id' => $mapped[0],
                'level' => $mapped[1],
                'category_needs_review' => $mapped[2],
            ];
        }

        return null;
    }

    /**
     * For dual-write: get legacy category_id given a new_category_id.
     */
    public static function getLegacyCategoryId(?int $newCategoryId): ?int
    {
        if (! $newCategoryId) {
            return null;
        }

        // If it's L2, get the L1 parent ID (e.g. 507 -> 5, 105 -> 1)
        $l1Id = $newCategoryId > 100 ? (int) floor($newCategoryId / 100) : $newCategoryId;

        return self::$newToLegacyL1[$l1Id] ?? null;
    }
}
