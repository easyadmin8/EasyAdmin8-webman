@switch($adminEditor)
    @case('ckeditor')

        <script src="/static/plugs/ckeditor4/ckeditor.js?v={{$version}}" charset="utf-8"></script>

        @break

    @case('wangEditor')

        <link rel="stylesheet" href="/static/plugs/wangEditor/dist/style.css?v={{$version}}">
        <script src="/static/plugs/wangEditor/dist/index.js?v={{$version}}"></script>

        @break
    @default

        <script src="/static/plugs/ueditor/ueditor.config.js?v={{$version}}" charset="utf-8"></script>
        <script src="/static/plugs/ueditor/ueditor.all.js?v={{$version}}" charset="utf-8"></script>
        <script src="/static/plugs/ueditor/lang/zh-cn/zh-cn.js?v={{$version}}" charset="utf-8"></script>
        <script src="/static/plugs/ueditor/third-party/codemirror/codemirror.js?v={{$version}}" charset="utf-8"></script>
        <script src="/static/plugs/ueditor/third-party/zeroclipboard/zeroclipboard.js?v={{$version}}" charset="utf-8"></script>

@endswitch

