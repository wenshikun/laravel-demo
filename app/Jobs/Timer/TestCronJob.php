<?php   
namespace App\Jobs\Timer;
use App\Tasks\TestTask;
use Swoole\Coroutine;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use Hhxsv5\LaravelS\Swoole\Timer\CronJob;
use Swoole\WebSocket\Server;
use Swoole\WebSocket\Frame;
use App\Models\RedEnvelopes;
class TestCronJob extends CronJob
{
    protected $i = 0;
    // !!! 定时任务的`interval`和`isImmediate`有两种配置方式（二选一）：一是重载对应的方法，二是注册定时任务时传入参数。
    // --- 重载对应的方法来返回配置：开始
    public function interval()
    {
        return 1;// 每1秒运行一次
    }
    public function isImmediate()
    {
        return false;// 是否立即执行第一次，false则等待间隔时间后执行第一次
    }
    // --- 重载对应的方法来返回配置：结束
    public function run()
    {
        // \Log::info(__METHOD__, ['start', $this->i, microtime(true)]);
        $bonus_total=rand(10,100);
        $bonus_count=rand(3,7);
        $bonus_max=$bonus_total/$bonus_count+rand(1,7);
        $bonus_min=0.01;
        $returnArr = RedEnvelopes::getBonus($bonus_total, $bonus_count, $bonus_max, $bonus_min);
        app('log')->info(RedEnvelopes::createOnlyId());
        // do something
        // sleep(1); // Swoole < 2.1
        // Coroutine::sleep(1); // Swoole>=2.1 run()方法已自动创建了协程。
        $this->i++;
        // \Log::info(__METHOD__, ['end', $this->i, microtime(true)]);
        // if ($this->i >= 10) { // 运行10次后不再执行
        //     \Log::info(__METHOD__, ['stop', $this->i, microtime(true)]);
        //     $this->stop(); // 终止此任务
        //     // CronJob中也可以投递Task，但不支持Task的finish()回调。
        //     // 注意：
        //     // 1.参数2需传true
        //     // 2.config/laravels.php中修改配置task_ipc_mode为1或2，参考 https://wiki.swoole.com/wiki/page/296.html
        //     $ret = Task::deliver(new TestTask('task data'), true);
        //     var_dump($ret);
        // }
        // throw new \Exception('an exception');// 此时抛出的异常上层会忽略，并记录到Swoole日志，需要开发者try/catch捕获处理
    }
}