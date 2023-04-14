/* utf 한글 */
function flashObject(file_name,flashVar,width,height){
  document.write('<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="' + width + '" height="' + height + '">');
  document.write('<param name="movie" value="' + file_name + '">');
  document.write('<param name=FlashVars value="' + flashVar + '">');
  document.write('<param name="quality" value="high">');
  document.write('<param name="wmode" value="transparent">');
  document.write('<embed src="' + file_name +'" FlashVars="' + flashVar +'" width="' + width + '" height="' + height + '" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" wmode="transparent"></embed>');
  document.write('</object>');
}