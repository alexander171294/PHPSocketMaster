<?php namespace PHPSocketMaster;

trait TimeOut
{
    private $DatTimeOut = array();
    
    private function setTimeOut($callback, $time, $repeat = false, $params = array())
    {
        $this->DatTimeOut[] = array('callback' => $callback, 'started' => microtime(), 'end' => microtime()+$time, 'loop' => $repeat, 'params' => $params, 'time' => $time);
    }
    
    public function TimeOut_refresh()
    {
        for($i = 0; $i<count($this->DatTimeOut); $i++)
        {
            $target = $this->DatTimeOut[$i];
            if($target['end'] < microtime())
            {
                if($target['loop'])
                {
                    $this->DatTimeOut[$i]['end'] = $this->DatTimeOut[$i]['end'] + $this->DatTimeOut[$i]['time'];
                } else {
                    unset($this->DatTimeOut[$i]);
                    $this->DatTimeOut = array_values($this->DatTimeOut);
                }
                call_user_func_array(array('this', $target['callback']), $target['params']);
            }
        }
    }
    
}