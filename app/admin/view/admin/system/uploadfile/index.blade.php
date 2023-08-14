@include('admin.layout.head')
<div class="layuimini-container">
    <div class="layuimini-main">
        <table id="currentTable" class="layui-table layui-hide"
               data-auth-add="{:auth('system/uploadfile/add')}"
               data-auth-edit="{:auth('system/uploadfile/edit')}"
               data-auth-delete="{:auth('system/uploadfile/delete')}"
               lay-filter="currentTable">
        </table>
    </div>
</div>
@include('admin.layout.foot')
