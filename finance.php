<?php
//采集美股交易的数据
header("Content-type: text/html; charset=utf-8"); 
include_once("conn.php");
$id = $_GET['id'];
if(isset($id) && $id <= 369){
	echo 'http://stock.finance.sina.com.cn/usstock/api/jsonp.php/IO.XSRV2.CallbackList[\'p1tyUwCR$iBlNO8Q\']/US_CategoryService.getList?page='.$id.'&num=20&sort=chg&asc=1&market=&id='.'<br>';
    // 初始化一个 cURL 对象
    $curl = curl_init(); 
    // 设置你需要抓取的URL
    curl_setopt($curl, CURLOPT_URL, 'http://stock.finance.sina.com.cn/usstock/api/jsonp.php/IO.XSRV2.CallbackList[\'p1tyUwCR$iBlNO8Q\']/US_CategoryService.getList?page='.$id.'&num=20&sort=chg&asc=1&market=&id=');
    //curl没有超时限制
    curl_setopt($curl,CURLOPT_CONNECTTIMEOUT,0);
    // 设置header
    curl_setopt($curl, CURLOPT_HEADER, 0);
    // 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    // 运行cURL，请求网页
    $data = curl_exec($curl);
    // 关闭URL请求
    curl_close($curl);
    // 显示获得的数据
    //var_dump($data);
    $preg = "/{name:\".*\",cname:\"(.*)\",category:[\"]?(.*)[\"]?,symbol:\"(.*)\",price:\"(.*)\",diff:\".*\",chg:\"(.*)\",preclose:\"(.*)\",open:\"(.*)\",high:\"(.*)\",low:\"(.*)\",amplitude:\"(.*)\",volume:\"(.*)\",mktcap:\"(.*)\",/iUs";
    preg_match_all($preg,$data,$arr);
    //var_dump($arr);
    //exit;

    //遍历
    foreach($arr[1] as $key => $val){
		if($arr[11][$key] < 1000000){
            continue;
        }
		if($arr[4][$key] < 5){
            continue;
        }
        if($arr[5][$key] >= -0.05){
            echo "<script>location.href='http://localhost/short_stocks/finance.php?id=370'</script>";
        }
        $arr[1][$key] = iconv("GBK", "UTF-8", $arr[1][$key]);
        $arr[2][$key] = iconv("GBK", "UTF-8", $arr[2][$key]);
        $arr[2][$key] = ltrim($arr[2][$key] ,'"');
        echo "'".$arr[1][$key]."','".$arr[2][$key]."','".$arr[3][$key]."','".$arr[4][$key]."','".$arr[5][$key]."','".$arr[10][$key]."','".$arr[11][$key]."','".$arr[12][$key]."'"."<br>";
        //exit;
        //插入库中
		$sql = "INSERT into `stock` (`cname`,`category`,`symbol`,`price`,`chg`,`amplitude`,`open`,`volume`,`mktcap`) VALUES ('".$arr[1][$key]."','".$arr[2][$key]."','".$arr[3][$key]."','".$arr[4][$key]."','".$arr[5][$key]."','".$arr[10][$key]."','".$arr[7][$key]."','".$arr[11][$key]."','".$arr[12][$key]."')";
        //exit;
		echo $sql.'<br>';
        mysql_query($sql);
    }
    $_GET['id'] += 1;
    //通过网页跳转的好处是不会使php死掉
    echo "<script>location.href='http://localhost/short_stocks/finance.php?id=".$_GET['id']."'</script>";
 
}
