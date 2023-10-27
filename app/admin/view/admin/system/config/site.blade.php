<form id="app-form" class="layui-form layuimini-form">

    <div class="layui-form-item">
        <label class="layui-form-label">站点名称</label>
        <div class="layui-input-block">
            <input type="text" name="site_name" class="layui-input" lay-verify="required" placeholder="请输入站点名称" value="{{sysconfig('site','site_name')}}">
            <tip>填写站点名称。</tip>
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">浏览器图标</label>
        <div class="layui-input-block layuimini-upload">
            <input name="site_ico" class="layui-input layui-col-xs6" lay-verify="required" placeholder="请上传浏览器图标,ico类型" value="{{sysconfig('site','site_ico')}}">
            <div class="layuimini-upload-btn">
                <span><a class="layui-btn" data-upload="site_ico" data-upload-number="one" data-upload-exts="ico"><i class="fa fa-upload"></i> 上传</a></span>
                <span><a class="layui-btn layui-btn-normal" id="select_site_ico" data-upload-select="site_ico" data-upload-number="one"><i class="fa fa-list"></i> 选择</a></span>
            </div>
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">后台背景图</label>
        <div class="layui-input-block layuimini-upload">
            <input name="admin_background" class="layui-input layui-col-xs6" placeholder="不填默认#333333" value="{{sysconfig('site','admin_background')}}">
            <div class="layuimini-upload-btn">
                <span><a class="layui-btn" data-upload="site_ico" data-upload-number="one" data-upload-exts="png|jpg|jpeg"><i class="fa fa-upload"></i> 上传</a></span>
                <span><a class="layui-btn layui-btn-normal" id="select_admin_background" data-upload-select="admin_background" data-upload-number="one"><i class="fa fa-list"></i> 选择</a></span>
            </div>
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">版本信息</label>
        <div class="layui-input-block">
            <input type="text" name="site_version" class="layui-input" lay-verify="required" placeholder="请输入版本信息" value="{{sysconfig('site','site_version')}}">
            <tip>填写版本信息。</tip>
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">备案信息</label>
        <div class="layui-input-block">
            <input type="text" name="site_beian" class="layui-input" lay-verify="required" placeholder="请输入备案信息" value="{{sysconfig('site','site_beian')}}">
            <tip>填写备案信息。</tip>
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">版权信息</label>
        <div class="layui-input-block">
            <input type="text" name="site_copyright" class="layui-input" lay-verify="required" placeholder="请输入版权信息" value="{{sysconfig('site','site_copyright')}}">
            <tip>填写版权信息。</tip>
        </div>
    </div>

    <div class="hr-line"></div>
    <div class="layui-form-item text-center">
        <button type="submit" class="layui-btn layui-btn-normal layui-btn-sm" lay-submit="system/config/save" data-refresh="false">确认</button>
        <button type="reset" class="layui-btn layui-btn-primary layui-btn-sm">重置</button>
    </div>

</form>
