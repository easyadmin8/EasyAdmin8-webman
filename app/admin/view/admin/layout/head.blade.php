<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{sysconfig('site','site_name')}}</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="icon" href="{{sysconfig('site','site_ico')}}" type="image/x-icon">
    <meta name="csrf-token" content="">
    <!--[if lt IE 9]>
    <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
    <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" href="/static/admin/css/public.css?v={{$version}}" media="all">
    <link rel="stylesheet" href="" id="layuicss-theme-dark" media="all">
    <script>
        window.CONFIG = {
            ADMIN: "{{$adminModuleName}}",
            CONTROLLER_JS_PATH: "{{$thisControllerJsPath}}",
            ACTION: "{{$thisAction}}",
            AUTOLOAD_JS: "{{$autoloadJs?1:0}}",
            IS_SUPER_ADMIN: "{{$isSuperAdmin}}",
            VERSION: "{{$version}}",
            CSRF_TOKEN: '',
            ADMIN_UPLOAD_URL: "{{$adminUploadUrl}}",
            EDITOR_TYPE: "{{$adminEditor}}",
        };
    </script>
    <script src="/static/plugs/xmSelect/xm-select.js" charset="utf-8"></script>
    <script src="/static/plugs/layui-v2.x/layui.js" charset="utf-8"></script>
    <script src="/static/plugs/require-2.3.6/require.js" charset="utf-8"></script>
    <script src="/static/config-admin.js?v={{$version}}" charset="utf-8"></script>
    <script src="/static/common/js/admin.js?v={{$version}}" charset="utf-8"></script>
    @include('admin.layout.editor')
</head>
<body>

