(function ($) {
	// alert(1);
	Kakao.init('5146ba68bd059beb004cc12265023df8'); //발급받은 키 중 javascript키를 사용해준다. benant@paran.com kmcse
	console.log('kakao isInitialized: ', Kakao.isInitialized()); // sdk초기화여부판단
	//카카오로그인
	function kakaoLogin(callback) {
		Kakao.Auth.login({
			persistAccessToken: true,
			success: function (response) {
				Kakao.API.request({
					url: '/v2/user/me',
					success: function (response) {
						// console.log('login success response:', response) // {id:2226830532, connected_at:"2022-05-03T03:49:23Z"}
						if (callback) {
							callback(response);
						}
					},
					fail: function (error) {
						console.log('login fail error1:', error)
					},
				})
			},
			fail: function (error) {
				console.log('login fail error2:', error)
			},
		})
	}
	window.kakaoLogin = kakaoLogin;
	//카카오로그아웃  
	function kakaoLogout(callback) {
		if (Kakao.Auth.getAccessToken()) {
			Kakao.API.request({
				url: '/v1/user/unlink',
				success: function (response) {
					// console.log(response)
					if (callback) {
						callback(response);
					}
				},
				fail: function (error) {
					console.log('kakaoLogout fail error1:', error)
				},
			})
			Kakao.Auth.setAccessToken(undefined)
		}
	}
	window.kakaoLogout = kakaoLogout;
})(jQuery);