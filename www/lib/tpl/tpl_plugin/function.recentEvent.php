<?php
function recentEvent(&$tpl,&$dbcon,$limit=5)
{
	$query = array();
	$query['table_name'] = 'js_event';
	$query['tool'] = 'select';
	$query['fields'] = 'event_code,
		subject,
		start_date,
		end_date,
		contents,
		event_img_a,
		event_img_b,
		bool_event,
		(select count(*) from js_event_goods where event_code=js_event.event_code) AS cnt_event_goods,
		regdate';
	$query['where'] = 'where bool_event=1 order by start_date desc limit '.$limit;
	$result = $dbcon->query($query,__FILE__,__LINE__);
	$loop = array();
	while ($row = mysqli_fetch_assoc($result)) {
		$loop[] = $row;
	}
	$tpl->define('contents','shop/event_main.html');
	$tpl->assign('loop_event_recent',$loop);
	$tpl->print_('contents');
}
?>