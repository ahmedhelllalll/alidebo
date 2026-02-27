<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap');
        
        body { 
            font-family: 'Cairo', sans-serif; 
            margin: 0; padding: 0; 
            background-color: #f8f9fa; 
            color: #3c4043;
            padding: 40px 20px;
        }
        
        .card { 
            max-width: 600px; 
            margin: 0 auto; 
            background: #ffffff; 
            border: 1px solid #dadce0; 
            border-radius: 8px; 
            overflow: hidden;
        }
        
        .header { 
            padding: 32px 40px 20px; 
            text-align: right; 
        }
        
        .content { padding: 0 40px 40px; text-align: right; }

        h1 { font-size: 19px; font-weight: 700; color: #202124; margin-bottom: 16px; }
        p { font-size: 14px; line-height: 1.7; color: #5f6368; margin-bottom: 20px; }
        
        .btn-container { text-align: center; margin: 32px 0; }
        .btn { 
            display: inline-block; 
            padding: 12px 28px; 
            background-color: #f45018; 
            color: #ffffff !important; 
            text-decoration: none; 
            border-radius: 6px; 
            font-weight: 700; 
            font-size: 15px;
            box-shadow: 0 2px 4px rgba(244, 80, 24, 0.2);
        }
        
        .benefit-section {
            background-color: #fffaf8;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
        }

        .note-section { 
            font-size: 13px; 
            color: #70757a; 
            line-height: 1.6; 
            border-right: 3px solid #f45018; 
            padding-right: 15px; 
            margin-top: 25px;
        }

        .footer { max-width: 600px; margin: 24px auto 0; text-align: center; font-size: 11px; color: #70757a; }
        .footer a { color: #70757a; text-decoration: underline; margin: 0 8px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <table cellpadding="0" cellspacing="0" border="0" dir="rtl">
                <tr>
                    <td style="vertical-align: middle; padding-right: 12px;">
                        <span style="font-size: 24px; font-weight: 800; color: #202124; letter-spacing: -1px; line-height: 1;">alidebo</span>
                    </td>
                    <td style="vertical-align: middle;">
                        <img src="{{ asset('images/logo.png') }}" alt="alidebo" width="40" style="display: block; width: 40px; height: auto;">
                    </td>
                </tr>
            </table>
            <div style="height: 1px; background: #e8eaed; width: 100%; margin-top: 20px;"></div>
        </div>

        <div class="content">
            @yield('content')
        </div>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} alidebo LLC. | <a href="#">الدعم الفني</a> | <a href="#">الشروط والأحكام</a></p>
    </div>
</body>
</html>