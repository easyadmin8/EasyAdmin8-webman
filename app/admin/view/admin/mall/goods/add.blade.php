@include('admin.layout.head')
<div class="layuimini-container">
    <form id="app-form" class="layui-form layuimini-form">
        <div class="layui-form-item">
            <label class="layui-form-label">商品分类</label>
            <div class="layui-input-block">
                <select name="cate_id" lay-verify="required" data-select="{{__url('mall/cate/index')}}" data-fields="id,title">
                </select>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">商品标题</label>
            <div class="layui-input-block">
                <input type="text" name="title" class="layui-input" lay-verify="required" placeholder="请输入商品标题" value="">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label required">商品LOGO</label>
            <div class="layui-input-block layuimini-upload">
                <input name="logo" class="layui-input layui-col-xs6" lay-verify="required" placeholder="请上传分类图片" value="">
                <div class="layuimini-upload-btn">
                    <span><a class="layui-btn" data-upload="logo" data-upload-number="one" data-upload-exts="png|jpg|ico|jpeg" data-upload-icon="image"><i class="fa fa-upload"></i> 上传</a></span>
                    <span><a class="layui-btn layui-btn-normal" id="select_logo" data-upload-select="logo" data-upload-number="one" data-upload-mimetype="image/*"><i class="fa fa-list"></i> 选择</a></span>
                </div>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label required">商品图片</label>
            <div class="layui-input-block layuimini-upload">
                <input name="images" class="layui-input layui-col-xs6" lay-verify="required" placeholder="请上传商品图片" value="">
                <div class="layuimini-upload-btn">
                    <span><a class="layui-btn" data-upload="images" data-upload-number="more" data-upload-exts="png|jpg|ico|jpeg" data-upload-icon="image"><i class="fa fa-upload"></i> 上传</a></span>
                    <span><a class="layui-btn layui-btn-normal" id="select_images" data-upload-select="images" data-upload-number="more" data-upload-mimetype="image/*"><i class="fa fa-list"></i> 选择</a></span>
                </div>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">市场价格</label>
            <div class="layui-input-block">
                <input type="text" name="market_price" class="layui-input" lay-verify="required" placeholder="请输入市场价格" value="0">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">折扣价格</label>
            <div class="layui-input-block">
                <input type="text" name="discount_price" class="layui-input" lay-verify="required" placeholder="请输入折扣价格" value="0">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">虚拟销量</label>
            <div class="layui-input-block">
                <input type="text" name="virtual_sales" class="layui-input" lay-verify="required" placeholder="请输入虚拟销量" value="0">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">商品描述</label>
            <div class="layui-input-block">
                <textarea name="describe" rows="20" class="layui-textarea editor" placeholder="请输入商品描述"></textarea>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">分类排序</label>
            <div class="layui-input-block">
                <input type="number" name="sort" class="layui-input" placeholder="请输入分类排序" value="0">
            </div>
        </div>

        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">备注信息</label>
            <div class="layui-input-block">
                <textarea name="remark" class="layui-textarea" placeholder="请输入备注信息"></textarea>
            </div>
        </div>

        <div class="hr-line"></div>
        <div class="layui-form-item text-center">
            <button type="submit" class="layui-btn layui-btn-normal layui-btn-sm" lay-submit>确认</button>
            <button type="reset" class="layui-btn layui-btn-primary layui-btn-sm">重置</button>
        </div>

    </form>
</div>
@include('admin.layout.foot')
