<?php

function getUri()
{
	return 'http://'.urlencode($_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
}

?>