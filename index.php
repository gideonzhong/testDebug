<?php

/**
 * Created by PhpStorm.
 * User: Mars_Chung
 * Date: 18-11-29
 * Time: 下午2:43
 */
declare(strict_types = 1);//根据项目的版本自行添加.
testAction();

echo RedisConnect::getRedis()->LPop('site:hm:errorLog');

function testAction()
{
    $data = [
        'channel_code' => 'Offical Site',
        'store_code'   => 'HM-CN-Store',
        'brand_code'   => 'HM',
        'module'       => 'Index',
        'controller'   => 'Index',
        'action'       => 'test',
        'order_no'     => '12345678',
        'start_time'   => str_replace('+00:00', 'Z', gmdate('c'))//任务执行开始时间
    ];

    //--------情况1:处理业务-------
    //处理业务流程 step 1

    //处理业务流程 step 2

    //任务执行结束时间 必须按此格式 2018-11-21T08:45:04.04Z
    $data['end_time'] = str_replace('+00:00', 'Z', gmdate('c'));

    //响应时间，时间戳格式,表示任务的执行时间[end_time - start_time]或接口请求的响应时间
    $data['response_time'] = strtotime($data['end_time']) - strtotime($data['start_time']);

    //执行状态编码：如：ORDER_PAYED_10000（必填）
    $data['rst_code'] = 'ORDER_PAYED_10000';
    //执行结果描述：如：执行成功(必填）
    $data['st_message'] = '成功－－订单支付';

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

    //日志打印时间(必填）
    $data['datetime'] = str_replace('+00:00', 'Z', gmdate('c'));
    //(必填）此字段用于运维告警时使用,跟上面的action是两个不同概念,必须全局统一，同一个action内可以有多个不相同的action_type
    $data['action_type'] = 'make_order_business_flow';

    CommonLog::emergency($data, 'site:hm:errorLog');

    //-------情况2:请求接口类-------

    //任务执行开始时间 必须按此格式 2018-11-21T08:45:04.04Z
    $data['start_time'] = str_replace('+00:00', 'Z', gmdate('c'));

    //调用第三方接口 CURL *****/api

    //任务执行结束时间 必须按此格式 2018-11-21T08:45:04.04Z
    $data['end_time'] = str_replace('+00:00', 'Z', gmdate('c'));
    //响应时间，时间戳格式,表示任务的执行时间[end_time - start_time]或接口请求的响应时间
    $data['response_time'] = strtotime($data['end_time']) - strtotime($data['start_time']);

    //执行状态编码：如：ORDER_PAYED_10000（必填）
    $data['rst_code'] = 'ORDER_SEND_10000';
    //执行结果描述：如：执行成功(必填）
    $data['st_message'] = '成功－－调用第三方接口';

    $data['request'] = [
        'store_id' => 1
    ];

    $data['response'] = [
        'success' => 100000
    ];

    //日志打印时间(必填）
    $data['datetime'] = str_replace('+00:00', 'Z', gmdate('c'));
    //(必填）此字段用于运维告警时使用,跟上面的action是两个不同概念,必须全局统一，同一个action内可以有多个不相同的action_type
    $data['action_type'] = 'make_order_call_api';

    CommonLog::emergency($data, 'site:hm:errorLog');
}

class CommonLog
{
    const SYSTEM_CODE = 'Site';//(必填）Your project ,such as OMS or AI-Designer

    /**
     * Description: Function 描述
     * User: Mars_Chung
     * Date: 18-11-30
     * Time: 下午1:37
     * Version : 1.0
     * @param array $data
     * @param string $list_key Depend on your project.The format is, platform_name:***
     */
    public static function emergency(array $data, string $list_key = 'site:hm:errorLog')
    {
        $data['ip']          = $_SERVER['SERVER_ADDR'];//(必填）
        $data['hostname']    = gethostbyaddr($_SERVER['SERVER_ADDR']);
        $data['system_code'] = self::SYSTEM_CODE;

        RedisConnect::getRedis()->LPush($list_key, json_encode($data));
    }
}

class RedisConnect
{

    private static $_RedisInstance = null;

    private function __construct()
    {
        self::$_RedisInstance = new Redis();

        $application_env = getenv('APPLICATION_ENV') ? : 'Production';
        $config          = [
            'host'         => '192.168.0.73',
            'port'         => 6379,
            'cache_time'   => 30,
            'cache_prefix' => '',
            'password'     => ''
        ];

        if ('Test' == $application_env) {
            $config = [
                'host'         => 'localhost',
                'port'         => 6379,
                'cache_time'   => 30,
                'cache_prefix' => '',
                'password'     => true
            ];
        }
        self::$_RedisInstance->connect($config['host'], $config['port']);
        if (isset($config['password'])) {
            self::$_RedisInstance->auth($config['password']);
        }
    }

    public static function getRedis()
    {
        return self::$_RedisInstance ? : new self;
    }

    public static function LPush(string $key, $value)
    {
        self::$_RedisInstance->lPush($key, $value);
    }

    public static function LPop(string $key)
    {
        self::$_RedisInstance->lPop($key);
    }

    private function __clone(){ return false; }

}



