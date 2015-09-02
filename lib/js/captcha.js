(function($){
	$.extend($.fn, {
		CaptchaObj : {},
		CaptchaUrl : '',
		CaptchaImg : null,
		CaptchaImgSet :{'img_x':120,'img_y':40},
		CaptchaInput : null,
		CaptchaReload : null,
		CaptchaAutoSet : null
	});
})( jQuery );
$.Captcha = function(){
	$.fn.CaptchaUrl = "<?php echo $_SESSION['SiteUrl']?>Captcha/";
	$.fn.CaptchaImg = function(Set){
		if(Set){
			Set = Set.split('x');
			Set = {'img_x':Set[0],'img_y':Set[1]};
			$.extend($.fn.CaptchaImgSet, Set);
		}
		/*
		// if(Set){$.extend($.fn.CaptchaImgSet, Set);}
		img_x
		img_y
		*/
		$.fn.CaptchaImgSet = $.fn.CaptchaUrl+"ImgPut?img_x="+$.fn.CaptchaImgSet.img_x+"&img_y="+$.fn.CaptchaImgSet.img_y;
		$(this).html("<img id = '_CaptchaImg' src = '"+$.fn.CaptchaImgSet+'&'+Math.random()+"' />");
		$.fn.CaptchaObj.Img = $(this);
	};
	$.fn.CaptchaInput = function(){
		if($.fn.CaptchaObj.Img){
			$(this).html("<input type = 'text' id = '_CaptchaInput' maxlength = '6' size = '10' placeholder = 'Capthcha' />");
			$.fn.CaptchaObj.Input = $(this);
		}
	};
	$.fn.CaptchaReload = function(Set){
		if($.fn.CaptchaObj.Input){
			var Setting = "<span class='mega-octicon octicon-sync'></span>";
			if(Set){$.extend(Setting, Set);}
			$(this).html("<span id = '_CapthchaReLoad' class = 'link'>" + Setting + "</span>");
			$('#_CapthchaReLoad').on('click',function(){
				$('#_CaptchaImg')[0].src=$.fn.CaptchaImgSet+'&'+Math.random();
				$('#_CaptchaInput').val('');
			});
			$.fn.CaptchaObj.Reload = $(this);
		}
	};
	$.fn.CaptchaAutoSet = function(Set){
		/*Set = {'Img':obj,'Input':obj,'Reload':obj};*/
		if(Set){
			$.fn.CaptchaObj = Set;
			$.fn.CaptchaObj.Img.CaptchaImg();
			$.fn.CaptchaObj.Input.CaptchaInput();
			$.fn.CaptchaObj.Reload.CaptchaReload();
		}else{
			$(this).append("<AutoSetCaptchaImg /><AutoSetCaptchaInput /><AutoSetCaptchaReload />");
			$.fn.CaptchaObj = $(this);
			$.fn.CaptchaAutoSet({'Img':$('AutoSetCaptchaImg'),'Input':$('AutoSetCaptchaInput'),'Reload':$('AutoSetCaptchaReload')});
		}
	};
	$.CaptchaCheck = function () {
		if($.fn.CaptchaObj.Input){
			var obj = $.fn.CaptchaObj.Input.find('input');
			if(obj.length > 0 && obj.val() != ''){
				var toSer = {
					'url':$.fn.CaptchaUrl+"ImgCheck",
					'data':{'captcha':obj.val()}
				};
				return ($.JQPost(toSer) == 1);
			}else{
				return false;
			}
		}else{
			return false;
		}
	};
};
$.Captcha();