<?php

namespace app\admin\controller\system;

use app\common\services\annotation\ControllerAnnotation;
use app\common\services\annotation\NodeAnnotation;
use app\common\controller\AdminController;
use app\common\services\curd\BuildCurd;
use app\common\services\curd\exceptions\FileException;
use support\Request;
use support\Response;
use think\db\exception\PDOException;
use think\facade\Db;
use think\helper\Str;

#[ControllerAnnotation(title: 'CURD可视化管理')]
class CurdGenerateController extends AdminController
{
    #[NodeAnnotation(title: '列表', auth: true)]
    public function index(Request $request): Response
    {
        return $this->fetch();
    }

    #[NodeAnnotation(title: '保存', auth: true)]
    public function save(Request $request): Response
    {
        if (!$request->isAjax()) return $this->error();
        $type      = $request->input('type', '');
        $tb_prefix = $request->input('tb_prefix', '');
        $tb_name   = $request->input('tb_name', '');
        if (empty($tb_name)) return $this->error('参数错误');
        switch ($type) {
            case "search":
                try {
                    $list = Db::query("SHOW FULL COLUMNS FROM {$tb_prefix}{$tb_name}");
                    $data = [];
                    foreach ($list as $value) {
                        $data[] = [
                            'name'  => $value['Field'],
                            'type'  => $value['Type'],
                            'key'   => $value['Key'],
                            'extra' => $value['Extra'],
                            'null'  => $value['Null'],
                            'desc'  => $value['Comment'],
                        ];
                    }
                    return $this->success('查询成功', compact('data', 'list'));
                }catch (PDOException $exception) {
                    return $this->error($exception->getMessage());
                }
                break;
            case "add":
                $force = $request->post('force/d', 0);
                try {
                    $build = (new BuildCurd())->setTablePrefix($tb_prefix)->setTable($tb_name);
                    $build->setForce($force); // 强制覆盖
                    $build    = $build->render();
                    $fileList = $build->getFileList();
                    if (empty($fileList)) return $this->error('这里什么都没有');
                    $result = $build->create();
                    $_file  = $result[0] ?? '';
                    $link   = '';
                    if (!empty($_file)) {
                        $_fileExp        = explode(DIRECTORY_SEPARATOR, $_file);
                        $_fileExp_last   = array_slice($_fileExp, -2);
                        $_fileExp_last_0 = $_fileExp_last[0] . '/';
                        if ($_fileExp_last[0] == 'controller') $_fileExp_last_0 = '';
                        $link = '/' . env('EASYADMIN.ADMIN', 'admin') . '/' . $_fileExp_last_0 . Str::snake(explode('Controller.php', end($_fileExp_last))[0] ?? '') . '/index';
                        var_dump($_fileExp_last_0);
                        var_dump($link);
                    }
                    return $this->success('生成成功', compact('result', 'link'));
                }catch (FileException $exception) {
                    return json(['code' => -1, 'msg' => $exception->getMessage()]);
                }
                break;
            case "delete":
                try {
                    $build    = (new BuildCurd())->setTablePrefix($tb_prefix)->setTable($tb_name);
                    $build    = $build->render();
                    $fileList = $build->getFileList();
                    if (empty($fileList)) return $this->error('这里什么都没有');
                    $result = $build->delete();
                    return $this->success('删除自动生成CURD文件成功', compact('result'));
                }catch (FileException $exception) {
                    return json(['code' => -1, 'msg' => $exception->getMessage()]);
                }
                break;
            default:
                return $this->error('参数错误');
                break;
        }
    }
}