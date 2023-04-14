#!/bin/bash
# 프로세스 확인 스크립트
# 대몬으로 작동해야 하는 스크립트가 잘동중인지 확인해서 미작동시 재시작 시키는 스크립트입니다.
SCRIPT_PATH=$(dirname $(realpath $0))
#SCRIPT_PATH="/home/ubuntu/www/www.kmcse.com/www/api/cronjob"
declare -a cmds
# dev 용
# cmds=("genChart.php JIN USD"  "genChart.php BTC USD" "genChart.php ETH USD" "genChart.php LTC USD" "genChart.php BCH USD" "genChart.php ETC USD" "genChart.php QTUM USD" "check_deposit.php JIN" "check_deposit.php BTC" "check_deposit.php ETH" "check_deposit.php LTC" "check_deposit.php BCH" "check_deposit.php ETC" "check_deposit.php QTUM")
# stage 용
cmds=("genChart.php JIN USD"  "genChart.php BTC USD" "genChart.php ETH USD" "genChart.php LTC USD" "genChart.php BCH USD" "genChart.php ETC USD" "genChart.php QTUM USD" "check_deposit.php JIN" "check_deposit.php BTC" "check_deposit.php LTC" "check_deposit.php BCH" "check_deposit.php ETC" "check_deposit.php QTUM" "bt-player-live.php JIN USD" "bt-player-live.php BTC USD" "bt-player-live.php ETH USD" "bt-player-live.php LTC USD" "bt-player-live.php ETC USD" "bt-player-live.php QTUM USD") #  "check_deposit.php ETH" "bt-player-live.php BCH USD"
cnt=${#cmds[@]}
# echo ${PWD}
while true; do
    i=0
    while [ ${i} -lt ${cnt} ]; do
        cmd=${cmds[${i}]}
        # echo "${i} , ${cmd} , ps -ef | grep \"${SCRIPT_PATH}/${cmd}\" | grep -v grep | wc -l "
        # continue
        pscnt=$(ps -ef | grep "${SCRIPT_PATH}/${cmd}" | grep -v grep | wc -l )
        # echo "${pscnt} "
        if [ ${pscnt} -lt 1 ]; then
            #echo "nohup /usr/bin/php ${SCRIPT_PATH}/${cmd} & > ${SCRIPT_PATH}/nohup.out 2>&1"
            ( nohup /usr/bin/php ${SCRIPT_PATH}/${cmd} & > ${SCRIPT_PATH}/nohup.out )
            #echo "${cmd} start"
        fi
        ((i+=1))
    done
    sleep 5
done
