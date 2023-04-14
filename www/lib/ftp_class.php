<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/

class FTP
{
	var $ftp_server; 
	var $ftp_user; 
	var $ftp_pass; 
	var $ftp_conn;
	var $ftp_path;

	function FTP($ftp_server,$ftp_user,$ftp_pass,$ftp_path,$limit_size=1)
	{
		$this->ftp_conn = ftp_connect($ftp_server);
		if($this->ftp_conn) {
			if (!@ftp_login($this->ftp_conn, $ftp_user, $ftp_pass)) { errMsg(Lang::main_ftp1); }
		}
		$this->limit_size = $limit_size;
	}

	function upload(&$file)
	{
		if(substr($file['type'],0,6) == 'images') { if(!uploadLimit($ext,'img')) { errMsg(Lang::main_ftp2); }}
		else {
			if(!uploadLimit($ext,'file')) { errMsg(Lang::main_ftp2); }
		}
		$ext = getExtension($file['name']);
		$file_name = randCode().'.'.$ext;
		$file_name_db = $file_name.'||'.$file['name'];
		if(@ftp_put($this->ftp_conn,$this->path.'/'.$file_name, $file['tmp_name'], FTP_BINARY)) {
			if(@ftp_size($this->ftp_path.'/'.$file_name) > $this->limit_size*1024) {
				if(@ftp_delete($this->ftp_conn,$this->ftp_path.'/'.$file_name)) { errMsg(str_replace(Lang::main_ftp3, '{size}', $this->size)); }
			}
		}
		else { errMsg(Lang::main_ftp4); }
		return $file_name_db;
	}

	function ftpChmod($target,$val)
	{
		if (!@ftp_site($this->ftp_conn,'CHMOD '.$val.' '.$target)) { errMsg(Lang::main_ftp5); }
	}

	function ftpMkdir($dir)
	{
		if(!@ftp_mkdir($this->ftp_conn,$dir)) { errMsg(Lang::main_ftp6); }
	}

	function ftpRmdir($dir)
	{
		$arr_files = @ftp_nlist($this->ftp_conn, $dir); 
		if (is_array($arr_files)) {
			for ($i=0;$i<sizeof($arr_files);$i++) {
				$st_file = $arr_files[$i]; 
				if (@ftp_size($this->ftp_conn, $st_file) == -1) { ftpRmdir($this->ftp_conn, $st_file); }
				else { @ftp_delete($this->ftp_conn, $st_file); }
			}
		}
		if(!@ftp_rmdir($this->ftp_conn, $dir)) { errMsg(Lang::main_ftp7); }
	}

	function ftpClose()
	{
		if (function_exists("ftp_close")) {
			ftp_close($this->ftp_conn);
			if(!$this->ftp_conn) { errMsg(Lang::main_ftp8); }
		}
		else { if(!@ftp_exec($this->ftp_conn,"QUIT")) { errMsg(Lang::main_ftp8); }	}
	}

	function dataMake($dir)
	{
		$this->ftpMkdir($dir);
		$this->ftpChmod($dir,'0707');
		$this->ftpClose();
		return 1;
	}
}

?>