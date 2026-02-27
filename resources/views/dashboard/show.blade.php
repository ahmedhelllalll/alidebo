<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $business->name }} | AliDebo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; scroll-behavior: smooth; }
        .section-fade { animation: fadeIn 0.8s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-white text-slate-900">

    @foreach($business->sections as $section)
        <div class="section-fade">
            @includeIf("templates.{$section->section_type}.{$section->template_key}", [
                'content' => $section->content,
                'media' => $business->media,
                'business' => $business
            ])
        </div>
    @endforeach

    <footer class="py-8 text-center bg-slate-50 border-t border-slate-100">
        <p class="text-sm font-bold text-slate-400">Powered by <span class="text-primary font-black italic">AliDebo</span></p>
    </footer>

</body>
</html>