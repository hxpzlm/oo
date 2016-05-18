<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/12
 * Time: 14:44
 */
namespace frontend\extend\PHPExcel;
$path = dirname(__FILE__);
require $path.".php";
class Excel extends \PHPExcel{

    public static $instance;

    public static function getInstance()
    {
        if (empty(self::$instance)) self::$instance = new self();
        return self::$instance;
    }

    /**
     * 将数据保存至Excel表格
     * 说明：只是一个子表时请自行处理，道理类似
     * @param string $outFile 要保存的文件路径
     * @param array $data 需要保存的数据 二维数组
     * @return bool
     */
    public function saveSheet($outFile, array $data)
    {
        $path = explode('/',$outFile);
        unset($path[count($path)-1]);
        // DIRECTORY_SEPARATOR 常量是框架定义的目录分隔符，（服务器环境自知，可以不用这么麻烦）
        $path = implode('/',$path) . DIRECTORY_SEPARATOR;
        //目录不存在 则创建目录 需要父目录有写权限才可以创建子目录，
        // Linux基础现在几乎都多多少少会了，不再是什么高深的知识
        if (!file_exists($path)) {
            @mkdir($path, 0777, TRUE);
            @chmod($path, 0777);
        }

        // 实例化一个PHPExcel对象
        $newExcel = new \PHPExcel();
        // 得到一个默认的激活表格，预备写入数据
        $newSheet = $newExcel->getActiveSheet();
        $newSheet->fromArray($data);
        // 格式按自己需要，源码文件样例中有写，（下面这个其实是excel2003的标准）
        $objWriter = \PHPExcel_IOFactory::createWriter($newExcel, 'Excel5');
        // 保存数据到表格中
        $objWriter->save($outFile);
        unset($objWriter,$newSheet,$newExcel);
        return true;
    }

    /**
     * @param array $data 需要过滤处理的数据 二维数组
     * @param int $cols  取N列
     * @param int $offset  排除 N 行，比如读取一个表格数据时，标题这一行可能是不希望读出来的，
     *                     毕竟这部分和存入数据库中没什么关系，就排除这一行
     * @param bool|int $must 某列不可为空  0 - index
     * @return array
     */
    public function handleSheetArray(array $data, $cols = 10, $offset = 1, $must = false)
    {
        $final = [];
        if ($must && $must >= $cols) {
            $must = false;
        }

        foreach($data as $key => $row) {
            if ($key < $offset) {
                continue;
            }
            $t = [];
            for ($i = 0; $i < $cols; $i++) {
                if (isset($row[$i])) {
                    $t[$i] = trim(strval($row[$i]));
                } else {
                    $t[$i] = '';
                }
            }
            if (is_array($row) && implode('', $t) && ($must===false || $t[$must])) {
                $final[] = $t;
                continue;
            }
        }

        return $final;
    }

}