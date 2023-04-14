jQuery(function ($) {
    // ajax cache off
    /*$.ajaxSetup({ cache: false });

    // 상품 받아오기
    var _products = {}, _wallet = {},
        _displayWallet = function () { // 지갑 화면에 출력.
            var _html = [];
            for (symbol in _products) {
                var coin = _products[symbol], _tpl = '', balance = 0, address = '<a type="button" value="create new address" name="createNewAddress" symbol="' + symbol + '">create new address</a>', check_txn = '<a type="button" value="check transaction" name="checkTxn" symbol="' + symbol + '">check transaction</a>';
                if (typeof _wallet[symbol] != typeof undefined) {
                    balance = _wallet[symbol].confirmed ? _wallet[symbol].confirmed : 0;
                    address = _products[symbol].creatable=='Y' ? (_wallet[symbol].address ? _wallet[symbol].address : '<a type="button" name="createNewAddress" symbol="' + symbol + '">create new address</a>') : '<a type="button" value="create new address" name="insertAddress" symbol="' + symbol + '">Bank account registration</a>';
                }

                _html.push('<li>' + coin.symbol.toUpperCase() + ', ' + balance + ', ' + address + ', ' + check_txn + '</li>');
            }
            $('#my_wallet').empty().html(_html.join(''));
        }
        ;
    $.getJSON('/api/v1.0/getCurrency', function (r) {
        if (r && r.success) {
            for (i in r.payload) {
                var coin = r.payload[i];
                _products[coin.symbol.toLowerCase()] = coin;
            }
        }
        // 지갑 잔액 가져오기
        $.getJSON('/api/v1.0/getBalance?symbol=all', function (r) {
            if (r && r.success) {
                for (i in r.payload) {
                    var _w = r.payload[i];
                    if (typeof _w != typeof undefined) {
                        _wallet[_w.symbol.toLowerCase()] = _w;
                    }
                }
            }
            _displayWallet();
        });
        // console.log(_products);

        $('#my_wallet').on('click', '[name=createNewAddress]', function (i) {
            var address = '', symbol = $(this).attr('symbol');
            $.getJSON('/api/v1.0/createNewAddress?symbol=' + symbol, function (r) {
            // $.getJSON('/api/v1.0/getBalance?symbol=' + symbol, function (r) {
                if (r && r.success) {
                    _wallet[symbol] = {
                        'address' : r.payload.address,
                        'confirmed' : 0
                    };
                    _displayWallet();
                } else {
                    if(r.error) {
                        if(r.error.code=='001') { // 로그아웃됨.
                            if(confirm('Would you like to login again?')) {
                                // alertGo('','/login/'.base64_encode($_SERVER['REQUEST_URI']));;
                                window.location.replace("/login/"+btoa(unescape(encodeURIComponent(window.location.href))));
                            }
                        } else {
                            alert(r.error.message);
                        }
                    }
                }
            });
        });
    });*/

});