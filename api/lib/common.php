<?php
/**
 * 跨域访问开启
 */
function crossDomain(){
    header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Headers:x-requested-with,content-type');
    header("Content-type: text/html; charset=utf-8");
}

/**
 * 接口返回数据
 * @param int $code
 * @param string $msg
 * @param array $data
 */
function ajax($code = 200, $msg = '成功', $data = []){
    echo json_encode([
        'code' => $code,
        'msg' => $msg,
        'data' => $data
    ]);
    die();
}

/**
 * 调用ajax来返回一整个Service
 * @param $s
 */
function ajaxS($s) {
    ajax($s::$CODE, $s::$MSG, $s:: $DATA);
}

/**
 * 对外开放的显示异常信息
 */
function error($info){
    throw new Exception('<h1>'.$info.'</h1><br/><p>'.$_SERVER['PHP_SELF'].' <p><hr/>');
    die();
}

/**
 * 显示异常信息
 */
function displayException($e){
    //异常处理
    echo $e->getMessage();
    $trace = $e->getTrace();
    foreach($trace as $k => $v){
        echo 'In file: '.$v['file'].',line '.$v['line'].'<br/>';
        echo 'Error function: '.$v['function'].',error info: <br/>';
        if($v['args']){
            foreach($v['args'] as $kk => $vv){
                echo $kk.'.'.$vv.'<br/>';
            }
        }else{
            echo '[none]';
        }

        echo '<br/><hr/>';
    }
}

/**
 * 二维数组下某个键变成数组索引
 */
function keyToIndex($array,$keyName){
    foreach($array as $k => $v){
        $array_[$v[$keyName]] = $v;
    }

    return $array_;
}

function curl($url, $params = false, $ispost = 0)
{
    $httpInfo = array();
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'JuheData');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    if ($ispost) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_URL, $url);
    } else {
        if ($params) {
            curl_setopt($ch, CURLOPT_URL, $url.'?'.$params);
        } else {
            curl_setopt($ch, CURLOPT_URL, $url);
        }
    }
    $response = curl_exec($ch);
    if ($response === FALSE) {
        //echo "cURL Error: " . curl_error($ch);
        return false;
    }
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
    curl_close($ch);
    return $response;
}

function array_key_values($key, $arr)
{
    $res = array();
    if (!empty($arr)) {
        foreach ($arr as $item) {
            if (isset($item[$key])) {
                $res[$item[$key]] = $item;
            }
        }
    }
    return $res;
}
