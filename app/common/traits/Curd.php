<?php

namespace app\common\traits;

use app\common\services\tool\CommonTool;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Respect\Validation\Validator;
use support\Db;
use support\Request;
use support\Response;
use app\common\services\annotation\ControllerAnnotation;
use app\common\services\annotation\NodeAnnotation;

/**
 * 后台CURD复用
 * Trait Curd
 * @package app\admin\traits
 */
trait Curd
{

    #[NodeAnnotation(title: '列表', auth: true)]
    public function index(Request $request): Response
    {
        if (!$request->isAjax()) return $this->fetch();
        if ($request->input('selectFields')) {
            return $this->selectList();
        }
        list($page, $limit, $where) = $this->buildTableParams();
        $count = $this->model->where($where)->count();
        $list  = $this->model->where($where)->orderByDesc($this->order)->paginate($limit)->items();
        $data  = [
            'code'  => 0,
            'msg'   => '',
            'count' => $count,
            'data'  => $list,
        ];
        return json($data);
    }

    #[NodeAnnotation(title: '添加', auth: true)]
    public function add(Request $request): Response
    {
        if ($request->isAjax()) {
            try {
                $save = insertFields($this->model);
            }catch (\Exception $e) {
                return $this->error('保存失败:' . $e->getMessage());
            }
            return $save ? $this->success('保存成功') : $this->error('保存失败');
        }
        return $this->fetch();
    }

    #[NodeAnnotation(title: '编辑', auth: true)]
    public function edit(Request $request)
    {
        $id  = (int)$request->input('id');
        $row = $this->model->find($id);
        if (empty($row)) return $this->error('数据不存在');
        if ($request->isAjax()) {
            try {
                $save = updateFields($this->model, $row);
            }catch (\PDOException|\Exception $e) {
                return $this->error('保存失败:' . $e->getMessage());
            }
            return $save ? $this->success('保存成功') : $this->error('保存失败');
        }
        $this->assign(compact('row'));
        return $this->fetch();
    }

    #[NodeAnnotation(title: '删除', auth: true)]
    public function delete(Request $request): Response
    {
        if (!$request->isAjax()) return $this->error();
        $id = $request->input('id');
        if (!is_array($id)) $id = (array)$id;
        $row = $this->model->whereIn('id', $id)->get()->toArray();
        if (empty($row)) return $this->error('数据不存在');
        try {
            $save = $this->model->whereIn('id', $id)->delete();
        }catch (\PDOException|\Exception $e) {
            return $this->error('删除失败:' . $e->getMessage());
        }
        return $save ? $this->success('删除成功') : $this->error('删除失败');
    }

    #[NodeAnnotation(title: '导出', auth: true)]
    public function export(Request $request): Response|bool
    {
        if (env('EASYADMIN.IS_DEMO', false)) {
            return $this->error('演示环境下不允许操作');
        }
        # 功能简单，请根据业务自行扩展
        list($page, $limit, $where) = $this->buildTableParams();
        $tableName = $this->model->getTable();
        $tableName = CommonTool::humpToLine(lcfirst($tableName));
        $prefix    = config('database.connections.mysql.prefix');
        $dbList    = Db::select("show full columns from {$prefix}{$tableName}");
        $header    = [];
        foreach ($dbList as $vo) {
            $comment = !empty($vo->Comment) ? $vo->Comment : $vo->Field;
            if (!in_array($vo->Field, $this->noExportFields)) {
                $header[] = [$comment, $vo->Field];
            }
        }
        $list = $this->model->where($where)->limit(100000)->orderByDesc('id')->get();
        if (empty($list)) return $this->error('暂无数据');
        $list     = $list->toArray();
        $fileName = '后台导出文件';
        try {
            $excelKeys = [];
            for ($x = 'A'; $x != 'IW'; $x++) $excelKeys[] = $x;
            $spreadsheet = new Spreadsheet();
            $sheet       = $spreadsheet->getActiveSheet();
            $countHeader = count($header);
            $headerKeys  = [];
            for ($i = 0; $i < $countHeader; $i++) {
                $sheet->setCellValue($excelKeys[$i] . '1', $header[$i][0] ?? '');
                $headerKeys [] = $header[$i][1] ?? '';
            }
            foreach ($list as $key => $value) {
                for ($j = 0; $j < $countHeader; $j++) {
                    $_val = $value[$headerKeys[$j]] ?? '';
                    $sheet->setCellValue($excelKeys[$j] . ($key + 2), $_val . "\t");
                }
            }
            $writer    = new Xlsx($spreadsheet);
            $file_path = runtime_path() . '/' . $fileName . '.xlsx';
            // 保存文件到 public 下
            $writer->save($file_path);
            // 下载文件
            return response()->download($file_path, $fileName . '.xlsx');
        }catch (\Exception|\PhpOffice\PhpSpreadsheet\Exception$e) {
            return $this->error($e->getMessage());
        }
    }

    #[NodeAnnotation(title: '属性修改', auth: true)]
    public function modify(Request $request): Response
    {
        if (!$request->isAjax()) return $this->error();
        $post = $request->post();
        Validator::input($post, [
            'id'    => Validator::notEmpty()->setName('ID'),
            'field' => Validator::notEmpty()->setName('字段'),
        ]);
        $row = $this->model->find($post['id']);
        if (empty($row)) {
            return $this->error('数据不存在');
        }
        try {
            foreach ($post as $key => $item) if ($key == 'field') $row->$item = $post['value'];
            $row->save();
        }catch (\PDOException|\Exception $e) {
            return $this->error("操作失败:" . $e->getMessage());
        }
        return $this->success('保存成功');
    }

}
