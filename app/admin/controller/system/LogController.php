<?php

namespace app\admin\controller\system;

use app\admin\model\SystemLog;
use app\common\controller\AdminController;
use app\common\services\annotation\ControllerAnnotation;
use app\common\services\annotation\MiddlewareAnnotation;
use app\common\services\annotation\NodeAnnotation;
use app\common\services\tool\CommonTool;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use think\facade\Db;
use support\Request;
use support\Response;

#[ControllerAnnotation(title: '操作日志管理')]
class LogController extends AdminController
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new SystemLog();
    }

    #[NodeAnnotation(title: '列表', auth: true)]
    public function index(Request $request): Response
    {
        if (!$request->isAjax()) return $this->fetch();
        [$page, $limit, $where, $excludeFields] = $this->buildTableParams(['month']);
        $month = !empty($excludeFields['month']) ? date('Ym', strtotime($excludeFields['month'])) : date('Ym');
        if (empty($month)) $month = date('Ym');
        try {
            $count = $this->model->setMonth($month)->where($where)->count();
            $list  = $this->model->setMonth($month)->where($where)->order($this->order)->with(['admin'])->limit($limit)->select()->toArray();
        }catch (\PDOException|\Exception $exception) {
            $count = 0;
            $list  = [];
        }
        $data = [
            'code'  => 0,
            'msg'   => '',
            'count' => $count,
            'data'  => $list,
        ];
        return json($data);
    }

    #[NodeAnnotation(title: '导出', auth: true)]
    public function export(Request $request): Response|bool
    {
        if (env('EASYADMIN.IS_DEMO', false)) {
            return $this->error('演示环境下不允许操作');
        }
        # 功能简单，请根据业务自行扩展
        [$page, $limit, $where, $excludeFields] = $this->buildTableParams(['month']);
        $tableName = $this->model->getTable();
        $tableName = CommonTool::humpToLine(lcfirst($tableName));
        $dbList    = Db::query("show full columns from {$tableName}");
        $header    = [];
        foreach ($dbList as $vo) {
            $comment = !empty($vo['Comment']) ? $vo['Comment'] : $vo['Field'];
            if (!in_array($vo['Field'], $this->noExportFields)) {
                $header[] = [$comment, $vo['Field']];
            }
        }
        $month = !empty($excludeFields['month']) ? date('Ym', strtotime($excludeFields['month'])) : date('Ym');
        if (empty($month)) $month = date('Ym');
        try {
            $list = $this->model->setMonth($month)->where($where)->order($this->order)->limit(100000)->select()->toArray();
        }catch (\PDOException|\Exception $exception) {
            return $this->error($exception->getMessage());
        }
        if (empty($list)) return $this->error('暂无数据');
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

    #[MiddlewareAnnotation(ignore: MiddlewareAnnotation::IGNORE_LOG)]
    #[NodeAnnotation(title: '框架日志', auth: true, ignore: NodeAnnotation::IGNORE_NODE)]
    public function record(): Response|string
    {
        return (new \Wolfcode\PhpLogviewer\webman\thinkphp\LogViewer())->fetch();
    }

    #[NodeAnnotation(title: '删除指定日志', auth: true)]
    public function deleteMonthLog(): Response|string
    {
        if (!request()->isAjax()) {
            return $this->fetch();
        }

        if ($this->isDemo) return $this->error('演示环境下不允许操作');

        $monthsAgo = (int)request()->post('month', 0);
        if ($monthsAgo < 1) return $this->error('月份错误');
        $dbPrefix   = env('DB_PREFIX');
        $dbLike     = "{$dbPrefix}system_log_";
        $tables     = Db::query("SHOW TABLES LIKE '$dbLike%'");
        $threshold  = date('Ym', strtotime("-$monthsAgo month"));
        $tableNames = [];
        try {
            foreach ($tables as $table) {
                $tableName = current($table);
                if (!preg_match("/^$dbLike\d{6}$/", $tableName)) continue;
                $datePart   = substr($tableName, -6);
                $issetTable = Db::query("SHOW TABLES LIKE '$tableName'");
                if (!$issetTable) continue;
                if ($datePart - $threshold <= 0) {
                    Db::execute("DROP TABLE `$tableName`");
                    $tableNames[] = $tableName;
                }
            }
        }catch (\Throwable) {
        }
        if (empty($tableNames)) return $this->error('没有需要删除的表');
        return $this->success('操作成功 - 共删除 ' . count($tableNames) . ' 张表<br/>' . implode('<br>', $tableNames));
    }

}
