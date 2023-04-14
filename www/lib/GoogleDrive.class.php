<?php
// include_once(__DIR__.'/../api/lib/config.php');
include_once(__DIR__.'/vendor/autoload.php');
// ----------------------------------------------------------------- //
// Google Drive
class GoogleDrive 
{
    private $_app_name = __APP_NAME__;
    private $_client = null;
    private $_drive_service = null;
    private $_target_folderid = __GOOGLE_DRIVE_TARGET_FOLDERID__; // set config.php
    private $_credentialsFile = __GOOGLE_DRIVE_CREDENTIALSFILE__; // set config.php

    /**
     * save file to Google Drive
     * api/v1.0/upload/index.php 에서 사용하는 메소드입니다.
     */
    public function save_file_to_google_drive($files) {
        $this->init_google_drive();
        if(! is_array($files['name'])) {
            $files['name'] = array($files['name']);
            $files['type'] = array($files['type']);
            $files['tmp_name'] = array($files['tmp_name']);
            $files['error'] = array($files['error']);
            $files['size'] = array($files['size']);
        }
        $r = array();
        for($i=0; $i<count($files['name']); $i++) {
            $file_size = $files['size'][$i];
            $file_type = $files['type'][$i];
            $file_sourcefile = $files['tmp_name'][$i];
            $file_old_name = $files['name'][$i];
            $file_name = hash('sha256', basename($file_old_name).'-'.time().mt_rand(111111,999999));
            $file_ext = end(explode('.',$file_old_name));
            $new_file = dirname($file_sourcefile).'/'.$file_name.'.'.$file_ext;
            if(!file_exists($file_sourcefile)) {var_dump($file_sourcefile); exit(' file not found. - save_file_to_google_drive');}
            rename($file_sourcefile, $new_file);

            // file check (100MB 미만 파일만 가능)
            if($file_size > 0 && $file_size < 1024*1024*100 && file_exists($new_file)) {
                
                // image orientation - new ImageManager 라이브 로드 애러 발생
                // if (strpos($file_type, 'image/')!==false && extension_loaded('imagick')) {
                //     $manager = new ImageManager(array('driver' => 'imagick'));
                //     $img = $manager->make($new_file)->orientate();
                //     $img->save($new_file);
                // }

                $fileurl = '';
                $fileid = $this->upload_google_drive($new_file);
                if($fileid) {
                    $fileurl = $this->view_url_google_drive($fileid);
                }
                $r[] = array('name'=>$file_old_name, 'url'=>$fileurl);
            } else {
                $r[] = array('name'=>$file_old_name, 'url'=>'');
            }
            unlink($new_file);
        }
        return $r;
    }

    /**
     * 구글 드라이브 준비(로그인)
     * @param String 구글드라이브계정정보가 담긴 키파일.
     */
    public function init_google_drive($credentialsFile='')
    {
        if(!$this->_client) {
            $this->_client = new Google\Client();
            if(!$this->_client) {
                throw new RuntimeException('구글 드라이브 라이브러리가 필요합니다! "composer require google/apiclient:^2.0" 명령어로 설치해주세요.');
            }
            $credentialsFile = $credentialsFile ? $credentialsFile : __DIR__.'/'.$this->_credentialsFile;
            if (!file_exists($credentialsFile)) {
                throw new RuntimeException('Service account credentials을 찾을 수 없습니다! $credentialsFile:'.$credentialsFile);
            }
            $this->_client->setAuthConfig($credentialsFile);
            $this->_client->setApplicationName($this->_app_name);
            $this->_client->setScopes(Google\Service\Drive::DRIVE);
            $this->_drive_service = new Google\Service\Drive($this->_client);
        }
    }

    /**
     * 구글 드라이브 파일 업로드
     * @param String $file 업로드할 파일(경로포함)
     * @param String $target_folderid 업로드할 구글드라이브 폴더의 아이디. 미지정시 브라우저에서 확인 불가합니다.
     * @return String 파일아이디. 오류시 빈값.
     */
    public function upload_google_drive($file, $target_folderid='')
    {
        $target_folderid = $target_folderid ? $target_folderid : $this->_target_folderid;
        $file_mimetype = mime_content_type($file);
        $file_name = basename($file);

        // 링크가 있는 모든 사람이 볼수 있는 permission 정보 설정
        $anyoneReaderPermission = new Google\Service\Drive\Permission();
        $anyoneReaderPermission->setType('anyone');
        $anyoneReaderPermission->setRole('reader');

        // 드라이브에 업로드할 파일의 정보 설정.
        $DriveFile = new Google\Service\Drive\DriveFile();
        $DriveFile->name = $file_name; //"Big File";// var_dump($DriveFile->name);
        $DriveFile->setMimeType($file_mimetype); // plain/text, image/jpeg// var_dump($DriveFile->mimeType);
        $DriveFile->setPermissions($anyoneReaderPermission); // var_dump($DriveFile->permissions);
        if($target_folderid) $DriveFile->setParents(array($target_folderid)); // 저장될 폴더 아이디 folderId // 수동으로 관리할때 필요합니다. 드라이브 url에서 확인할수 있습니다. 예: https://drive.google.com/drive/u/0/folders/1N0fNpCtBnTetQVMS8fKuZmtrIC9TvXbl

        // 파일 생성
        $chunkSizeBytes = 1 * 1024 * 1024;
        $this->_client->setDefer(true); // 미디어 업로드로 API를 호출하고 즉시 반환하지 않으므로 연기하지 않습니다.
        $request = $this->_drive_service->files->create($DriveFile);

        // 미디어 파일 업로드를 작성하여 업로드 프로세스를 나타냅니다.
        $media = new Google\Http\MediaFileUpload(
            $this->_client,
            $request,
            $file_mimetype,
            null,
            true,
            $chunkSizeBytes
        );
        $media->setFileSize(filesize($file));

        // 다양한 청크를 업로드하십시오. 프로세스가 완료 될 때까지 $status는 false입니다.
        $status = false;
        $handle = fopen($file, "rb");
        while (!$status && !feof($handle)) {
            // $file에서 $chunkSizeBytes를 얻을 때까지 읽으십시오
            // 스트림이 버퍼링 된 경우에는 스트림이 읽혀지고 일반 파일을 나타내지 않으면 프리드가 8192 바이트 이상을 반환하지 않습니다.
            // 읽기 버퍼링 된 파일의 예는 URL에서 읽을 때입니다.
            $chunk = $this->_readVideoChunk_google_drive($handle, $chunkSizeBytes);
            $status = $media->nextChunk($chunk);
        }

        // $status의 최종 값은 업로드 된 객체의 API의 데이터가됩니다.
        $fileid = '';
        if ($status != false) {
            $fileid = $status->getId();
            $this->_drive_service->permissions->create($fileid, $anyoneReaderPermission);
        }

        return $fileid;
    }

    /**
     * 파일 검색 - 파일이름 검색 결과를 받습니다.
     * @param String $name 검색어. 단어로 분리해  "and", "contains" 으로 검색합니다.
     * @return Array 드라이브파일 객체가 담겨있는 배열형식의 결과를 리턴합니다.
     * 다른 검색 예: https://developers.google.com/drive/api/v3/search-files?hl=en#query_string_examples
     * 검색 API: https://developers.google.com/drive/api/v3/reference/files/list?hl=en
     */
    public function search_google_drive($name='')
    {
        $this->init_google_drive();
        $files = array();
        $query = array("mimeType != 'application/vnd.google-apps.folder'"); // 파일만 검색
        if($name) {
            $name = explode(' ', $name);
            $name = array_map(function($v){ return "name contains '{$v}'";}, $name); // 이름이 포함된 파일 검색
            $query[] = implode(' and ', $name); // AND 검색. 즉, 단어가 모두 있어야 함.
        }
        $filter = array('q'=>implode(' and ', $query));
        $list = $this->_drive_service->files->listFiles($filter);
        return $list ? $list->getFiles() : null;
    }

    /**
     * 구글드라이브 파일 삭제
     */
    public function delete_google_drive($fileid)
    {
        $this->init_google_drive();
        try {
            $r = $this->_drive_service->files->delete($fileid);
            $r = isset($r->error) ? false : true;
        } catch (Exception $e) {
            $r = false;
        }
        return $r;
    }
    public function delete_google_drive_by_url($view_url)
    {
        $this->init_google_drive();
        $defualt_view_url = $this->view_url_google_drive();
        $fileid = str_replace($defualt_view_url, '', $view_url);
        try {
            $r = $this->_drive_service->files->delete($fileid);
            $r = isset($r->error) ? false : true;
        } catch (Exception $e) {
            $r = false;
        }
        return $r;
    }

    /**
     * 구글드라이브 전체 파일 삭제
     */
    public function delete_all_google_drive()
    {
        $this->init_google_drive();
        $files = array();
        $filter = array('q'=>"mimeType != 'application/vnd.google-apps.folder'"); // 파일만 검색
        $files = $this->_drive_service->files->listFiles($filter)->getFiles();
        foreach($files as $file) {
            try {
                $this->_drive_service->files->delete($file->getId());
            } catch (Exception $e) {}
        }
    }


    public function view_url_google_drive($fileid = '')
    {
        return 'https://drive.google.com/uc?export=view&id=' . $fileid;
    }

    private function _readVideoChunk_google_drive ($handle, $chunkSize) {
        $byteCount = 0;
        $giantChunk = "";
        while (!feof($handle)) {
            // fread will never return more than 8192 bytes if the stream is read buffered and it does not represent a plain file
            $chunk = fread($handle, 8192);
            $byteCount += strlen($chunk);
            $giantChunk .= $chunk;
            if ($byteCount >= $chunkSize)
            {
                return $giantChunk;
            }
        }
        return $giantChunk;
    }
}