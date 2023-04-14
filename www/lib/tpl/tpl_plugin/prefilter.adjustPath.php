<?php
function adjustPath($source, $tpl, $indicator='', $type='absolute')
{

	$default_indicator = 'adjustPath & .,css,js,gif,jpg,jpeg,png,swf';
	$path_filter = array();

//
	# $document_root = $_SERVER['DOCUMENT_ROOT']; 

	if (!$indicator || $indicator==='default') $indicator=$default_indicator;
	if (!$indicator=str_replace(',', '|', preg_replace('/^,\s*|\s*,$/', '', $indicator))) return $source;

	$on_ms   =$tpl->on_ms;

	// (1) get template path info

	$tpl_path =$tpl->tpl_path;
	if ($on_ms) $tpl_path=preg_replace('@\\\\+@', '/', $tpl_path);
	
	$tpl_dirs	=explode('/', preg_replace('@/[^/]+$@', '', $tpl_path));
	$tpl_dir_cnt=count($tpl_dirs);

	// (2) get web path info
	
	if (!empty($_SERVER['SCRIPT_NAME'])) {
		// need not check friendly url
		$web_path=$_SERVER['SCRIPT_NAME'];
		if ($on_ms) $web_path=preg_replace('@\\\\+@', '/', $web_path);
		$web_dirs=explode('/', $web_path);
		array_pop($web_dirs);
	} else if (!empty($_SERVER['PHP_SELF'])) {
		$web_path=$_SERVER['PHP_SELF'];
		if ($on_ms) $web_path=preg_replace('@\\\\+@', '/', $web_path);
		$web_dirs=explode('/', $web_path);
		if (!empty($_SERVER['SCRIPT_FILENAME'])) {
			$script_filename=$_SERVER['SCRIPT_FILENAME'];
			if ($on_ms) $script_filename=preg_replace('@\\\\+@', '/', $script_filename);
			$filename = array_pop(explode('/', $script_filename));
			while ($web_dirs && array_pop($web_dirs) != $filename);
		} else {
			array_pop($web_dirs);
		}
	} else {
		$tpl->report('Error #34', 'prefilter "adjustPath" cannot find web address', true);
	}
	$web_dir_cnt=count($web_dirs);

	// (3) get OS' absolute path info of url
	
	$abs_web_path=realpath('.');
	if (empty($abs_web_path)) {
		if (!empty($_SERVER['SCRIPT_FILENAME'])) {
			$abs_web_path=$_SERVER['SCRIPT_FILENAME'];
			$real_abs_web_path=realpath($abs_web_path);	// for symbolic linked document root 
			if ($real_abs_web_path) $abs_web_path = $real_abs_web_path;
			if ($on_ms) $abs_web_path=preg_replace('@\\\\+@', '/', $abs_web_path);
			$abs_web_path=preg_replace('@/[^/]+$@', '', $abs_web_path);
		} else {
			$tpl->report('Error #33', 'prefilter "adjustPath" cannot find absolute path of <b>'.$_SERVER['PHP_SELF'].' on OS</b>', true);	
		}
	} else {
		if ($on_ms) $abs_web_path=preg_replace('@\\\\+@', '/', $abs_web_path);
	}
	$abs_web_dirs=explode('/', $abs_web_path);
	$abs_web_dir_cnt=count($abs_web_dirs);


	// (4) set pattern to identify path

	$Dot='(?<=url\()\\\\*\./(?:(?:[^)/]+/)*[^)/]+)?'.
		'|(?<=")\\\\*\./(?:(?:[^"/]+/)*[^"/]+)?'.
		"|(?<=')\\\\*\./(?:(?:[^'/]+/)*[^'/]+)?";
	$Ext= $indicator[0]==='.' ? substr($indicator,2) : $indicator;
	$Ext='(?<=url\()(?:[^"\')/]+/)*[^"\')/]+\.(?:'.$Ext.')(?=\))'.
		'|(?<=")(?:[^"/]+/)*[^"/]+\.(?:'.$Ext.')(?=")'.
		"|(?<=')(?:[^'/]+/)*[^'/]+\.(?:".$Ext.")(?=')".
		'|(?<=\\\\")(?:[^"/]+/)*[^"/]+\.(?:'.$Ext.')(?=\\\\")'.
		"|(?<=\\\\')(?:[^'/]+/)*[^'/]+\.(?:".$Ext.")(?=\\\\')";
	if ($indicator==='.') $pattern=$Dot;
	else $pattern= $indicator[0]==='.' ? $Ext.'|'.$Dot : $Ext;
	$pattern='@('.$pattern.')@ix';
	$split=preg_split($pattern, $source, -1, PREG_SPLIT_DELIM_CAPTURE);

	$m=array();

// convert to relative path :: for peculiar usage requested by some user.

	if ($type==='relative') {
		$less_cnt=$abs_web_dir_cnt<$tpl_dir_cnt ? $abs_web_dir_cnt : $tpl_dir_cnt;
		for ($i=0; $i<$less_cnt; $i++) {
			if ($abs_web_dirs[$i]!=$tpl_dirs[$i]) break;
		}
		$rel_path_pfx = $abs_web_dir_cnt>$i ? str_repeat('../',$abs_web_dir_cnt-$i) : '';
		if ($tpl_dir_cnt>$i) {
			$reducible = $tpl_dir_cnt - $i;
			$rel_path_pfx.=implode('/',array_slice($tpl_dirs, $i)).'/';
		} else {
			$reducible = 0;
		}
		for ($i=1,$s=count($split); $i<$s; $i+=2) {
			if (substr($split[$i], 0, 1)==='\\') {
				$split[$i]=substr($split[$i],1);
				continue;
			}
			$split[$i] = preg_replace('@^(\./)+@','',$split[$i]);
			if ($reducible && preg_match('@^((?:\.{2}/)+)@', $split[$i], $m)) {
				$reduce = substr_count($m[1], '../');
				if ($reduce > $reducible) $reduce = $reducible;
				$split[$i] = preg_replace('@(?:[^/]+/){'.$reduce.'}$@', '', $rel_path_pfx) . preg_replace('@^(\.{2}/){'.$reduce.'}@','',$split[$i]);
			} else {
				$split[$i] = $rel_path_pfx . $split[$i];
			}
		}
		return implode('', $split);
	}

// convert to absolute path

	$path_search =array_keys($path_filter);
	$path_replace=array_values($path_filter);
	
	if (empty($document_root)) {
		if ($web_dir_cnt===1) {
			$base_path=implode('/', $abs_web_dirs);
		} else {
			$less_cnt=($web_dir_cnt < $abs_web_dir_cnt ? $web_dir_cnt : $abs_web_dir_cnt)-1;
				
			$web_test=array_reverse($web_dirs);
			$abs_test=array_reverse($abs_web_dirs);

			if ($on_ms) {
				for ($i=0; $i<$less_cnt; $i++) {
					if (strtolower($web_test[$i])!=strtolower($abs_test[$i])) break;
				}
			} else {
				for ($i=0; $i<$less_cnt; $i++) {
					if ($web_test[$i]!=$abs_test[$i]) break;
				}
			}

			$base_path=implode('/', $i ? array_slice($abs_web_dirs, 0, -$i) : $abs_web_dirs);
			if ($i<$web_dir_cnt-1) {					
				array_unshift($path_search, '/^/');
				array_unshift($path_replace, implode('/', $i ? array_slice($web_dirs, 0, -$i) : $web_dirs));
			}
		}
		$base_length =strlen($base_path);
	} else {
		if ($on_ms) $document_root=preg_replace('@\\\\+@', '/', $document_root);
		$base_length =strlen($document_root);
		if (substr($document_root, -1)=='/') $base_length--;
	}

	$tpl_path_prefix=preg_replace('@[^/]+$@', '', $tpl_path);

	for ($i=1,$s=count($split); $i<$s; $i+=2) {
		if (substr($split[$i], 0, 1)==='\\') {
			$split[$i]=substr($split[$i],1);
			continue;
		}
		// 해당 파일의 수정날짜 구하기. 
		$_add_time = false;
		if(preg_match('/\.(css|js)$/', trim($split[$i]))) {
			$_add_time = true;
			$_filemtime = filemtime(realpath($tpl_path_prefix.$split[$i]));
		}
		if (!$src=realpath($tpl_path_prefix.$split[$i])) {
			
			// simplify path. e.g) /a/b/c/../../d/e --> /a/d/e
			
			if (preg_match('@^((?:\.{1,2}/)+)@', $split[$i], $m)) {
				$src=preg_replace('@(?:[^/]+/){'.substr_count($m[1],'../').'}$@', '', $tpl_path_prefix)
					.preg_replace('@^(\.{1,2}/)+@','',$split[$i]);
			} else {
				$src=$tpl_path_prefix . $split[$i];
			}
		}
		if ($on_ms) $src = preg_replace('@\\\\+@', '/', $src);
		$split[$i]=substr($src, $base_length);
		if ($path_search) $split[$i]=preg_replace($path_search, $path_replace, $split[$i]);
		// css, js 에 해당 파일의 수정날짜를 추가함.
		if($_add_time) {
			$split[$i] = strpos($split[$i], '?')!==false ? $split[$i].'&t='.$_filemtime : $split[$i].'?t='.$_filemtime;
		}
	}
	return implode('', $split);
}
?>