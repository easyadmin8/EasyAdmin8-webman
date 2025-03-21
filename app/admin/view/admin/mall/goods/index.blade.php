@include('admin.layout.head')
<div class="layuimini-container">
    <div class="layuimini-main">
        <table id="currentTable" class="layui-table layui-hide"
               data-auth-add="{{auths('mall/goods/add')}}"
               data-auth-edit="{{auths('mall/goods/edit')}}"
               data-auth-delete="{{auths('mall/goods/delete')}}"
               data-auth-stock="{{auths('mall/goods/stock')}}"
               lay-filter="currentTable">
        </table>
    </div>
</div>
<script>
    let cate = JSON.parse('{!!json_encode($cate,JSON_UNESCAPED_UNICODE)!!}');
</script>
@include('admin.layout.foot')
