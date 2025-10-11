<style>
    @font-face {
        font-family: 'THSarabunNew';
        font-style: normal;
        font-weight: normal;
        src: url("{{ storage_path('fonts/THSarabunNew.ttf') }}") format('truetype');
    }

    @font-face {
        font-family: 'THSarabunNew';
        font-style: normal;
        font-weight: bold;
        src: url("{{ storage_path('fonts/THSarabunNew_Bold.ttf') }}") format('truetype');
    }

    @font-face {
        font-family: 'THSarabunNew';
        font-style: italic;
        font-weight: bold;
        src: url("{{ storage_path('fonts/THSarabunNew_Bold_Italic.ttf') }}") format('truetype');
    }

    @font-face {
        font-family: 'THSarabunNew';
        font-style: italic;
        font-weight: bold;
        src: url("{{ storage_path('fonts/THSarabunNew_Italic.ttf') }}") format('truetype');
    }

    @font-face {
        font-family: 'BeVietnamPro';
        font-style: normal;
        font-weight: normal;
        src: url("{{ storage_path('fonts/BeVietnamPro-Black.ttf') }}") format('truetype');
    }

    @font-face {
        font-family: 'BeVietnamPro';
        font-style: italic;
        font-weight: normal;
        src: url("{{ storage_path('fonts/BeVietnamPro-BlackItalic.ttf') }}") format('truetype');
    }

    @font-face {
        font-family: 'BeVietnamPro';
        font-style: italic;
        font-weight: bold;
        src: url("{{ storage_path('fonts/BeVietnamPro-bold.ttf') }}") format('truetype');
    }

    @font-face {
        font-family: 'SimHei';
        src: url('{{ asset('fonts/simhei.ttf') }}') format('truetype');
    }

    @php
        $locale = app()->getLocale();
        $font = match ($locale) {
            'ja' => 'ipag',
            'hi' => 'hindi',
            'th' => 'THSarabunNew',
            'vi' => 'BeVietnamPro',
            default => in_array($locale, ['zh', 'zh-CN', 'zh-TW']) ? 'SimHei' : 'Verdana',
        };
    @endphp

    * {
        font-family: {{$font}}, DejaVu Sans, Arial, sans-serif;
    }

    body {
        font-weight: normal !important;
        direction: {{ isRtl() ? 'rtl' : 'ltr' }};
    }
</style>
