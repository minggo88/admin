<?php
// ---------------------------------------------------------
 $app_name = "phpJobScheduler";
 $phpJobScheduler_version = "3.7";
// ---------------------------------------------------------
  define('TIME_WINDOW', 0);//denomination is in seconds, so 3600 = 60 minute time frame window

  define('ERROR_LOG', TRUE);// prints successful runs and errors to log table

  define('LOCATION', dirname(__FILE__) ."/");// used to open local files

  define('PJS_TABLE','phpjobscheduler');// pjs table name
  define('LOGS_TABLE','phpjobscheduler_logs');// logs table name

  define('LOG_CUTTING_POSITION','end');// 로그를 자르는 위치. begin, middel, end. 기본값: begin
  define('MAX_ERROR_LOG_LENGTH',1200);// maximum string length of output to record in error log table

  define('DATE_FORMAT', '%Y-%m-%d'); // 날짜 형식
  define('TIME_FORMAT', '%H:%M:%S'); // 시간 형식
  
  
