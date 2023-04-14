$(function () {
    const API_URL = '//api.' + window.location.host.replace('www.', '') + '/v1.0';

    // reset
    $('[name="btn-reset"]').on('click', function () {
        // $(this).closest('form').reset();
    });
    // 삭제
    $('[name="btn-delete"]').on('click', function () {
        const $tr = $(this).closest('tr');
        const symbol = $tr.find('[name=symbol]').val()||'';
        if (!symbol) { alert('symbol 값이 필요합니다.'); return false;}
        const years = $tr.find('[name=years]').val()||'';
        if (!years) { alert('years 값이 필요합니다.'); return false;}
        const data = {
            'pg_mode': 'finance_delete',
            'symbol': symbol,
            'years': years
        }
        if (confirm(years+'년도 데이터를 삭제하시겠습니까?')) {
            $.post('?', data, function (r) {
                try {
                    r = JSON.parse(r);
                } catch (e) {
                }
                if (r && r.bool) {// 성공
                    alert('삭제했습니다.'); window.location.reload();
                } else {// 실패
                    alert('삭제하지 못했습니다.');
                }
            });   
        }
        return false;
    });


    // 데이터 저장
    $('form').on('submit', function () { 
        const form = $(this).closest('form').get(0);
        const pg_mode = form.pg_mode.value||'';
        if (!pg_mode) { alert('pg_mode 값이 필요합니다.'); form.pg_mode.focus(); return false;}
        const symbol = form.symbol.value||'';
        if (!symbol) { alert('symbol 값이 필요합니다.'); form.symbol.focus(); return false;}
        const years = form.years.value||'';
        if (!years) { alert('년도를 입력해주세요'); form.years.focus(); return false; }
        const sales = form.sales.value||'';
        if (!sales) { alert('매출을 입력해주세요'); form.sales.focus(); return false; }
        const opt_profit = form.opt_profit.value||'';
        if (!opt_profit) { alert('영업이익을 입력해주세요'); form.opt_profit.focus(); return false; }
        const net_profit = form.net_profit.value||'';
        if (!net_profit) { alert('순이익을 입력해주세요'); form.net_profit.focus(); return false; }
        const assets = form.assets.value||'';
        if (!assets) { alert('자산금액을 입력해주세요'); form.assets.focus(); return false; }
        const debt = form.debt.value||'';
        if (!debt) { alert('부채금액를 입력해주세요'); form.debt.focus(); return false; }
        const equity = form.equity.value||'';
        if (!equity) { alert('자본금액을 입력해주세요'); form.equity.focus(); return false; }
        const debt_ratio = form.debt_ratio.value||'';
        if (!debt_ratio) { alert('부채비율을 입력해주세요'); form.debt_ratio.focus(); return false; }
        const roe = form.roe.value||'';
        if (!roe) { alert('자기자본이익률(ROE)를 입력해주세요'); form.roe.focus(); return false; }
        $.post('?', {
            'pg_mode':pg_mode,
            'symbol':symbol,
            'years':years,
            'sales':sales,
            'opt_profit':opt_profit,
            'net_profit':net_profit,
            'assets':assets,
            'debt':debt,
            'equity':equity,
            'debt_ratio':debt_ratio,
            'roe':roe
        }, function (r) { 
            try {
                r = JSON.parse(r);
            } catch (e) {
                r = { 'msg': r };
            }
            if (r && r.bool) {// 성공
                alert('저장했습니다.'); window.location.reload();
            } else {// 실패
                const msg = r.msg || '';
                alert('저장하지 못했습니다. '+msg);
            }
        })
        return false;
    })



});