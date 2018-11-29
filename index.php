<?php

/**
 * Created by PhpStorm.
 * User: Mars_Chung
 * Date: 18-11-29
 * Time: 下午2:43
 */

function testAction()
{
    //任务执行开始时间 必须按此格式 2018-11-21T08:45:04.04Z
    $data['start_time'] = str_replace('+00:00', 'Z', gmdate('c'));

    $data               = [
        'channel_code'  => 'Offical Site',
        'store_code'    => 'HM-CN-Store',
        'brand_code'    => 'HM',
        'response_time' => '响应时间，时间戳格式,表示任务的执行时间[end_time - start_time]或接口请求的响应时间',
        'rst_code'      => '执行状态编码：如：ORDER_PAYED_10000（必填）',
        'rst_message'   => '执行结果描述：如：执行成功',
        'module'        => '模块名，如果不存在则标为:index',
        'controller'    => '操作器名，如果不存在则标为:index',
        'action'        => '行为名（或者说是函数名），如果不存在则标为:index',
        'order_no'      => '12345678',
        'action_type'   => 'make_order'

    ];


    //--------情况1:处理业务-------
    //处理业务流程 step 1

    //处理业务流程 step 2

    //任务执行结束时间 必须按此格式 2018-11-21T08:45:04.04Z
    $data['end_time'] = str_replace('+00:00', 'Z', gmdate('c'));

    $data['response_time'] = str_replace('+00:00', 'Z', gmdate('c'));



    //-------情况2:请求接口类-------




    $data['request'] = [
        'store_id'   => 1,
        'start_time' => time() - 3600 * 24 * 30,
        'end_time'   => time(),
        'status'     => 'Payed'
    ];

    $data['response'] = [
        'total' => 100000,
        'items' => [
            [
                'id'   => '2',
                'name' => 'apple 8'
            ],
            [
                'id'   => '3',
                'name' => 'apple 9'
            ]
        ]
    ];

    //日志打印时间
    $data['datetime'] = str_replace('+00:00', 'Z', gmdate('c'));

    $result = CommonLog::emergency($data);
}

phpinfo();
testAction();

class CommonLog
{
    const SYSTEM_CODE = 'Site';

    public static function emergency(array $data)
    {
        $data['ip']       = $_SERVER['SERVER_ADDR'];
        $data['hostname'] = gethostbyaddr($_SERVER['SERVER_ADDR']);

        $redis = new Redis();
        var_dump($redis);


//        echo '<pre/>';
//        print_r($data);
    }
}

