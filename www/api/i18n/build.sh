# www/api/lib 폴더와 www/api/v1.0 폴더에서만 번역 대상 문구 추출합니다.
# lib 폴더 - 하위폴더 제외합니다.
ls ../lib/*.php > lib_file_list.txt
xgettext --from-code=UTF-8 --default-domain=API --output-dir=. --output=API.pot --join-existing -f lib_file_list.txt
xgettext --from-code=UTF-8 --default-domain=API --output-dir=. --output=API.pot --join-existing -f lib_file_list.txt --keyword=__ --keyword=_e
rm lib_file_list.txt
# v1.0 폴더
find ../v1.0/ -iname "*.php" > v10_file_list.txt
xgettext --from-code=UTF-8 --default-domain=API --output-dir=. --output=API.pot --join-existing -f v10_file_list.txt
xgettext --from-code=UTF-8 --default-domain=API --output-dir=. --output=API.pot --join-existing -f v10_file_list.txt --keyword=__ --keyword=_e
rm v10_file_list.txt
