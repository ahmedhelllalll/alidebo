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

    <footer class="py-12 text-center bg-slate-50 border-t border-slate-100">
        <div class="max-w-xs mx-auto opacity-50 hover:opacity-100 transition-opacity">
            <p class="text-xs font-black uppercase tracking-widest text-slate-400 mb-2">Developed By</p>
            <span class="text-xl font-black italic tracking-tighter text-slate-900">Ali<span class="text-primary">Debo</span></span>
        </div>
    </footer>

</body>
</html>