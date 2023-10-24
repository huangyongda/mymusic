<?php
namespace Home\Controller;

use Think\Controller;
use Think\Log;

class IndexController extends Controller
{
    public function index()
    {
//        dump(M('music')->select());
//        echo U("Index/addTask");
        $post_content = $_POST  ? var_export($_POST,true) : "";

        if (!$post_content) {
            $post_content = file_get_contents("php://input");
        }
        Log::record(var_export($post_content,true),'INFO');
        $data = [];
        if ($post_content) {
            $post_content = json_decode($post_content,true);
            Log::record("json_decode>>>",'INFO');
            Log::record(var_export($post_content,true),'INFO');
            Log::record('post_content','INFO');
            $list = $post_content['list'] ?? [];
            Log::record("list>>>",'INFO');
            Log::record(var_export($list,true),'INFO');
            M('log')->add(['post'=>"list:".var_export($list,true) ?? "","time"=>date("Y-m-d H:i:s"),"url"=>U()]);
            $quality = $post_content['quality'] ?? "";
            foreach ($list as $key => $item) {
                $map = [
                    'name' => $item['name'] ?? '' ,
                    'singer' => $item['singer'] ?? '' ,
                    'duration' => $item['interval'] ?? '' ,
                    'album' => $item['meta']['albumName'] ?? '' ,
                    'quality' => $quality ,
                    'status' => 1 ,
                ];

                $file = M('music')->where($map)->getField('file');
                // 读取文件内容
//                $file_content = file_get_contents($file);
//                $base64_content = base64_encode($file_content);
                $data[$key] = [
                    'url' => $file ? $file : "",
                    'name' => $map['name'] . " "."-"." " . $map['singer'] . ".mp3",
//                    'base64_content' => $base64_content,
                ];
            }
        }



        M('log')->add(['post'=>$post_content ?? "","time"=>date("Y-m-d H:i:s"),"url"=>U()]);
//        $this->show('HI','utf-8');
        $this->ajaxReturn($data);
    }

    public function getmp3base64()
    {
        $url = $_GET['url'];
        $post = var_export($_GET,true);
        M('log')->add(['post'=>$post ?? "","time"=>date("Y-m-d H:i:s"),"url"=>U()]);
        if (!$url) {
            $data = ['error'=>'不存在url'];
            return $this->ajaxReturn($data);
        }
        $file_content = file_get_contents($url);
        $base64_content = base64_encode($file_content);
        $data = [
            'base64_content' => $base64_content,
        ];

        return $this->ajaxReturn($data);
    }


    public function upload()
    {
        $f = var_export($_FILES,true);
        $post = var_export($_POST,true);
        M('log')->add(['post'=>($f ?? "") . ($post ?? ""),"time"=>date("Y-m-d H:i:s") ,"url"=>U()]);
        $path = 'Public/music';
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     1024 * 1024 * 20 ;// 设置附件上传大小
        $upload->exts      =     array('mp3');// 设置附件上传类型
        $upload->rootPath  =      './'.$path.'/'; // 设置附件上传根目录
        $upload->savePath  =      ''; // 设置附件上传（子）目录
        $upload->autoSub = true;
        $upload->subName = array('date','Ymd');

        $map = [
            'name' => $_POST['name'] ?? '',
            'singer' => $_POST['singer'] ?? '',
            'album' => $_POST['album'] ?? '',
            'duration' => $_POST['interval'] ?? '',
        ];
        if (M('music')->where($map)->find()) {
            echo "已存在相同资源信息";
            return;
        }
        // 上传文件
        $info   =   $upload->upload();
        if(!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        }else{// 上传成功 获取上传文件信息
            if (count($info) > 1 ) {
                echo "上传个数不能大于1";
                return;
            }
            foreach($info as $file){

                $savePath = $path."/".$file['savepath'].$file['savename'];

                $music_id = M('music')->add([
                    'file' => $savePath,
                    'name' => $_POST['name'] ?? '',
                    'singer' => $_POST['singer'] ?? '',
                    'album' => $_POST['album'] ?? '',
                    'duration' => $_POST['interval'] ?? '',
                    'source' => $_POST['source'] ?? '',
                    'hash' => $file['md5'] ?? '',
                    'size' => $file['size'] ?? '',
                    'quality' => $_POST['quality'] ?? '',
                    'content' => $_POST['downloadInfo'] ?? '',
                    'status' => 1,
                    'created_at' => date("Y-m-d H:i:s"),
                ]);
                echo "新增歌曲成功";
                return;
            }
        }
        echo "新增歌曲失败";
        return;
    }

    public function getqqqueryID($str)
    {
        $output = array(); // 用于存储输出结果

// 执行Shell脚本
        exec("curl -i '{$str}' \
  -H 'authority: c6.y.qq.com' \
  -H 'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7' \
  -H 'accept-language: zh-CN,zh;q=0.9' \
  -H 'cache-control: no-cache' \
  -H 'pragma: no-cache' \
  -H 'sec-ch-ua: \"Google Chrome\";v=\"117\", \"Not;A=Brand\";v=\"8\", \"Chromium\";v=\"117\"' \
  -H 'sec-ch-ua-mobile: ?0' \
  -H 'sec-ch-ua-platform: \"macOS\"' \
  -H 'sec-fetch-dest: document' \
  -H 'sec-fetch-mode: navigate' \
  -H 'sec-fetch-site: none' \
  -H 'sec-fetch-user: ?1' \
  -H 'upgrade-insecure-requests: 1' \
  -H 'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Safari/537.36' \
  --compressed", $output, $returnVar);

// 打印输出结果

        $idValue = 0;
        foreach ($output as $line) {


            if (stripos($line, "location:") !== false) {
                $url = str_replace('location:',"",$line);
//                echo $url . "<br>";

                // 使用 parse_url 函数获取 URL 的查询部分
                $query = parse_url($url, PHP_URL_QUERY);

                // 使用 parse_str 函数将查询部分解析为关联数组
                parse_str($query, $params);

                // 获取 "id" 参数的值
                if (isset($params['id'])) {
                    $idValue = $params['id'];
                } else {
                    E( "URL 中没有ID参数。");
                }
            }
        }
        if (!$idValue) {
            E( "qq 歌单 获取歌单的id失败。");
        }
        return $idValue;


    }

    public function addTask()
    {
        try {




            $str = "https://y.qq.com/n/ryqq/playlist/4235159627";
            //`str` varchar(255) COLLATE utf8_bin DEFAULT NULL,
            //  `status` int(11) DEFAULT '0' COMMENT '任务状态  0 未开始   1已完成  -1  失败',
            //  `content` text COLLATE utf8_bin COMMENT '详情',
            //  `finish_num` int(11) DEFAULT NULL COMMENT '已下载数',
            //  `total_num` int(11) DEFAULT NULL COMMENT '歌单歌曲总数',
            //  `tag` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '标签',
            //  `type` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '歌单类型',
            $taskId = M('task')->add([
                'created_at' => date("Y-m-d H:i:s"),
                'str' => $str,
                'status' => 0 , //任务状态  0 未开始   1已完成  -1  失败
            ]);


            $str = "https://c6.y.qq.com/base/fcgi-bin/u?__=BXXHJ0VhE7aL";

            if (stripos($str, "qq.com") !== false) {
                $list = $this->get_qq_play_list($str,$taskId);
                print_r($list);
            }

            echo "执行结束";

        } catch (\Exception $e) {
            echo $e->getMessage()."<br>";
        }

    }

    public function get_qq_play_list($str,$taskId)
    {
        $content = "";

        if (stripos($str, "https://c6.y.qq.com/base/fcgi-bin") !== false) {
            $id = $this->getqqqueryID($str);
        }
        $str = "https://c.y.qq.com/qzone/fcg-bin/fcg_ucc_getcdinfo_byids_cp.fcg?type=1&json=1&utf8=1&onlysong=0&new_format=1&disstid={$id}&loginUin=0&hostUin=0&format=json&inCharset=utf8&outCharset=utf-8&notice=0&platform=yqq.json&needNewCode=0";
        $info = $this->curlGetWebPageWithTimeout($str,5,[
            'Origin: https://y.qq.com',
            'Referer: https://y.qq.com/n/yqq/playsquare/{$id}.html',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36',
        ]);
        $list = json_decode($info,true);
        if (!$list || !isset($list['cdlist']) || count($list['cdlist']) <= 0 && isset($list['cdlist'][0]['songlist']) ) {
            $content = $info;
            M("task")->where('id',$taskId)->save([
                'content' => $info ? json_encode($info) : "",
                'status' => -1,
            ]);
            E('获取歌单失败');
        }
        M("task")->where('id',$taskId)->save([
            'content' => $info ? json_encode($info) : "",
            'total_num' => $list['cdlist'][0]['total_song_num'] ?? 0,
        ]);

        foreach ($list['cdlist'][0]['songlist'] as $key => $item ) {

            $content = $item ? json_encode($item) : "";
            $sort = $key+1;
            $name = $item['title'] ?? "";
            $singer = $item['singer'][0]['title'] ?? "" ;
            $album = $item['album']['title'] ?? "" ;
            $duration = $item['interval'] ?? 0 ;
            $music_id = 0 ;


            $has_music_id = M('music')->where([
                'name' => $name,
                'singer' => $singer,
                'album' => $album,
                'duration' => $duration,
            ])->getField('id');
            if (!$has_music_id) {
                $has_music_id = M('music')->where([
                    'name' => $name,
                    'singer' => $singer,
                    'album' => $album,
                ])->getField('id');
            }
            if ($has_music_id) {
                $music_id = $has_music_id;
            }

            //如果音乐库没有这个 则加一个任务
            if (!$music_id) {
                $music_id = M('music')->add([
                    'name' => $name,
                    'singer' => $singer,
                    'album' => $album,
                    'duration' => $duration,
                    'status' => 0,
                    'created_at' => date("Y-m-d H:i:s"),
                ]);
            }



            M('task_detail')->add([
                'music_id' => $music_id,
                'name' => $name,
                'singer' => $singer,
                'album' => $album,
                'duration' => $duration,
                'sort' => $sort,
                'content' => $content,
                'created_at' => date("Y-m-d H:i:s"),
            ]);
        }
    }


    function curlGetWebPageWithTimeout($url, $timeout = 5 ,$httpHeader = []) {
        // 初始化cURL会话
        $ch = curl_init();

        // 设置cURL选项
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        if ($httpHeader) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
        }


        // 设置超时时间
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        // 执行cURL请求并获取响应
        $response = curl_exec($ch);

        // 检查请求是否成功
        if ($response === false) {
            return 'cURL请求失败: ' . curl_error($ch);
        }

        // 关闭cURL会话
        curl_close($ch);

        return $response;
    }
}