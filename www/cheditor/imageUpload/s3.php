<?php
include_once __DIR__.'/../../lib/vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Intervention\Image\ImageManager;

class S3 {

    var $S3_KEY = 'AKIA42T7UG7KZBISQB7Z';
    var $S3_SECRET_KEY = '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';
    var $S3_REGION = 'ap-northeast-2';
    var $S3_BUCKET = 'teaplat';

    function delete_file_to_s3($objecturl) {

        // 업로드된 파일 S3에 저장하기.
        $bucket = $this->S3_BUCKET;

        // 사용하는 저장소의 이미지만 처리 합니다. kmcse.s3.ap-northeast-2.amazonaws.com
        if(strpos($objecturl, 's3.')===false || strpos($objecturl, $bucket)===false) {
            return true;
        }

        $s3 = new S3Client([
            'version' => 'latest',
            'region'  => $this->S3_REGION, // https://docs.aws.amazon.com/ko_kr/general/latest/gr/rande.html
            'credentials' => array( // 이렇게 해야 합니다. 아니면 아래 putObject 부분에서 curl 애러가 발생하며 접속도 못합니다.
                'key' => $this->S3_KEY,
                'secret' => $this->S3_SECRET_KEY
            )
        ]);

        // https://kmcse.s3.ap-northeast-2.amazonaws.com/tmp/201807/5ca75e71eba894e05f72e8f7918e41c48b1d29d4b55c2b6d7ac026e633f8432e.png
        $keyname = substr($objecturl, strpos($objecturl, '.amazonaws.com/')+strlen('.amazonaws.com/'));
        // var_dump($keyname); exit;
        try {
            $result = $s3->deleteObject(array(
                'Bucket' => $bucket,
                'Key' => $keyname,
            ));
        } catch (S3Exception $e) {
            // echo $e->getMessage() . PHP_EOL;
            return false;
        }

        return $result['DeleteMarker'];

    }

    function copy_tmpfile_to_s3($objecturl) {

        // 업로드된 파일 S3에 저장하기.
        $bucket = $this->S3_BUCKET;

        // 사용하는 저장소의 이미지만 처리 합니다.
        if(strpos($objecturl, 's3.')===false || strpos($objecturl, $bucket)===false) {
            return true;
        }

        $s3 = new S3Client([
            'version' => 'latest',
            'region'  => $this->S3_REGION, // https://docs.aws.amazon.com/ko_kr/general/latest/gr/rande.html
            'credentials' => array( // 이렇게 해야 합니다. 아니면 아래 putObject 부분에서 curl 애러가 발생하며 접속도 못합니다.
                'key' => $this->S3_KEY,
                'secret' => $this->S3_SECRET_KEY
            )
        ]);

        $oldkeyname = substr($objecturl, strpos($objecturl, 'tmp/'));
        $keyname = str_replace('tmp/', '', $oldkeyname);

        try {
            $result = $s3->copyObject(array(
                'Bucket' => $bucket,
                'Key' => $keyname,
                'CopySource'=> "{$bucket}/{$oldkeyname}",
                'ACL'    => 'public-read' // file permission
            ));
        } catch (S3Exception $e) {
            echo $e->getMessage() . PHP_EOL; exit;
            return false;
        }

        return $result['ObjectURL'];

    }

    function move_tmpfile_to_s3($objecturl) {

        // 업로드된 파일 S3에 저장하기.
        $bucket = $this->S3_BUCKET;

        $s3 = new S3Client([
            'version' => 'latest',
            'region'  => $this->S3_REGION, // https://docs.aws.amazon.com/ko_kr/general/latest/gr/rande.html
            'credentials' => array( // 이렇게 해야 합니다. 아니면 아래 putObject 부분에서 curl 애러가 발생하며 접속도 못합니다.
                'key' => $this->S3_KEY,
                'secret' => $this->S3_SECRET_KEY
            )
        ]);

        $oldkeyname = substr($objecturl, strpos($objecturl, 'tmp/'));
        $keyname = str_replace('tmp/', '', $oldkeyname);

        try {
            $result = $s3->copyObject(array(
                'Bucket' => $bucket,
                'Key' => $keyname,
                'CopySource'=> "{$bucket}/{$oldkeyname}",
                'ACL'    => 'public-read' // file permission
            ));
        } catch (S3Exception $e) {
            // echo $e->getMessage() . PHP_EOL;
            return false;
        }

        $this->delete_file_to_s3($objecturl);

        return $result['ObjectURL'];

    }

    /**
     * save file to S3
     * @param Array $files 서버로 전송된 PHP 업로드 파일 객체($_FILES 변수 구조 그대로 사용)
     */
    function save_file_to_s3($files) {

        // 업로드 파일 배열로 정리.
        // $_FILES 변수에 정의되는 형식으로 전달 받게 됩니다.
        // file_data[] 이름으로 보낼때 $files 구조는 다음과 같습니다.
        //     array(5) {
        //       ["name"]=>array(1) {[0]=>string(9) "kenny.png"}
        //       ["type"]=>array(1) {[0]=>string(9) "image/png"}
        //       ["tmp_name"]=>array(1) {[0]=>string(24) "C:\xampp\tmp\php7CAF.tmp"}
        //       ["error"]=>array(1) {[0]=>int(0)}
        //       ["size"]=>array(1) {[0]=>int(132929)}
        //     }
        // file_data 이름으로 보낼때 $files 구조는 다음과 같습니다.
        //     array(5) {
        //       ["name"]=>string(9) "kenny.png"
        //       ["type"]=>string(9) "image/png"
        //       ["tmp_name"]=>string(23) "C:\xampp\tmp\phpD7C.tmp"
        //       ["error"]=>int(0)
        //       ["size"]=>int(132929)
        //     }

        if(! is_array($files['name'])) {
            $files['name'] = array($files['name']);
            $files['type'] = array($files['type']);
            $files['tmp_name'] = array($files['tmp_name']);
            $files['error'] = array($files['error']);
            $files['size'] = array($files['size']);
        }

        // 업로드된 파일 S3에 저장하기.
        $bucket = $this->S3_BUCKET;
        $s3 = new S3Client([
            'version' => 'latest',
            'region'  => $this->S3_REGION, // https://docs.aws.amazon.com/ko_kr/general/latest/gr/rande.html
            'credentials' => array( // 이렇게 해야 합니다. 아니면 아래 putObject 부분에서 curl 애러가 발생하며 접속도 못합니다.
                'key' => $this->S3_KEY,
                'secret' => $this->S3_SECRET_KEY
            )
        ]);

        $r = array();
        try {
            for($i=0; $i<count($files['name']); $i++) {

                $file_type = $files['type'][$i];
                $file_size = $files['size'][$i];
                $file_sourcefile = $files['tmp_name'][$i];
                $file_old_name = $files['name'][$i];
                $file_name = hash('sha256', basename($file_old_name).'-'.time().mt_rand(111111,999999));
                $file_ext = end(explode('.',$file_old_name));
                $keyname = 'tmp/'.date('Ym').'/'.$file_name.'.'.$file_ext;

                // file check
                if($file_size > 0 && file_exists($file_sourcefile)) {

                    // image orientation
                    if (strpos($file_type, 'image/')!==false && extension_loaded('imagick')) {
                        $manager = new ImageManager(array('driver' => 'imagick'));
                        $img = $manager->make($file_sourcefile)->orientate();
                        $img->save($file_sourcefile);
                    }

                    // Upload data.
                    $result = $s3->putObject([
                        'Bucket' => $bucket,    // file storeage
                        'Key'    => $keyname,   // file name
                        'SourceFile' => $file_sourcefile, // file contents
                        'ACL'    => 'public-read' // file permission
                    ]);
                    // Print the URL to the object.
                    // echo $result['ObjectURL'] . PHP_EOL;
                    // $r[] = array('file'=>$file, 'ur'=>$result['ObjectURL']);
                    $r[] = array('name'=>$file_old_name, 'url'=>$result['ObjectURL']);
                } else {
                    $r[] = array('name'=>$file_old_name, 'url'=>'');
                }

            }
            // var_dump($r); exit;
            // array(1) { [0]=> array(2) { ["name"]=> string(27) "20190708110311_pgtzckwp.png" ["url"]=> string(133) "https://kmcse.s3.ap-northeast-2.amazonaws.com/tmp/201907/dc3b01dbd3dc23c67c8d7780082be7377ac1518d0ab46d60b18678b31279dfcd.png" } }
            // if(count($r)==1) {
            //     $r = $r[0];
            // };
        } catch (S3Exception $e) {
            // echo $e->getMessage() . PHP_EOL;
            return false;
        }

        return $r;

    }

}