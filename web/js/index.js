function get(apiRoute,param,cb){
	var url = 'http://letseatapi.xtzero.me/index.php/'+apiRoute+'?unlessparam=unlessvalue';
	for(var i in param){
		url += '&'+i+'='+param[i];
	}
	var xhr = new XMLHttpRequest();
	xhr.open("get", url, true);
	xhr.onload = function(res){
		console.log(url,res);
		cb && cb(JSON.parse(res.currentTarget.response),res);
	}

	xhr.onerror = function(res){
		cb && cb(res);
	};

	xhr.send();
}

function param(par){
    //获取当前URL
    var local_url = document.location.href;
    //获取要取得的get参数位置
    var get = local_url.indexOf(par +"=");
    if(get == -1){
        return false;
    }
    //截取字符串
    var get_par = local_url.slice(par.length + get + 1);
    //判断截取后的字符串是否还有其他get参数
    var nextPar = get_par.indexOf("&");
    if(nextPar != -1){
        get_par = get_par.slice(0, nextPar);
    }
    return get_par;
}

function rand(minNum,maxNum){
    return parseInt(Math.random()*(maxNum-minNum+1)+minNum,10); 
}

function dxever_numToTime(nums,full){	
	
	if(!nums){
		return '';
	}
	
	
	var time = parseInt(nums);//转换为数字类型
	var unixTimestamp = new Date(time * 1000);
	var timeStamp = unixTimestamp.getTime();//后台传来的时间戳！important
	
	
	var start=new Date(); 
	var currTime = start.getTime();//现在的手机的时间戳！important
	
    start.setHours(0);  
    start.setMinutes(0);  
    start.setSeconds(0);  
    start.setMilliseconds(0);  
	var todayStartTime = Date.parse(start);//今天0:00的时间戳 ！important
	var todayEndTime = todayStartTime + 86400000;//今天结束时候 ！important

	//精确到分的 时间戳时间获取
	var year = unixTimestamp.getFullYear();
	var mon  = unixTimestamp.getMonth()+1;
	var day  = unixTimestamp.getDate();
	var hour = unixTimestamp.getHours();
	var mins = unixTimestamp.getMinutes();
	
	if( mins >= 0 && mins <= 9 ){
		mins = ""+"0"+mins;
	}
	if( hour >= 0 && hour <= 9 ){
		hour = ""+"0"+hour;
	}
	
	//一天是 【86400000】 毫秒
	if( full == true || currTime < timeStamp ){//显示完全模式 如果后台传来的时间戳 也是这样
		return year+'-'+mon+'-'+day+' '+hour+':'+mins;
	}
	
	if( timeStamp >= todayStartTime && timeStamp <= todayEndTime ){//今天的时间
		return '今天'+' '+hour+':'+mins;
	}else if( timeStamp >= (todayStartTime-86400000) && timeStamp <= (todayEndTime-86400000) ){//昨天的时间
		return '昨天'+' '+hour+':'+mins;
	}else{//不是今天 不是昨天
		if( start.getFullYear() == year ){//今年
			if( start.getMonth()+1 == mon ){//如果是今年的今月
				return'本月'+day+'号 '+hour+':'+mins;
			}
			return mon+'月'+day+'号 '+hour+':'+mins;
		}else if( start.getFullYear()-1 == year ){//去年
			return '去年'+' '+mon+'月 '+day+'号 '+hour+':'+mins;
		}else{//剩余的年份
			return year+'-'+mon+'-'+day+' '+hour+':'+mins;
		}
	}
}

function bin2hex(bin){
    var i=0, l=bin.length,chr,hex='';
    for (i; i < l; ++i){
        chr=bin.charCodeAt(i).toString(16);
        hex+=chr.length<2 ? '0'+chr : chr;
    }
    return hex;
}

function canvas_id(){
    var canvas=document.createElement('canvas');
    var ctx=canvas.getContext('2d');
    var txt='http://eular.github.io';
    ctx.textBaseline='top';
    ctx.font="14px 'Arial'";
    ctx.fillStyle='#0ff';
    ctx.fillRect(0,0,140,50);
    ctx.fillStyle='#00f';
    ctx.fillText(txt,2,15);
    ctx.fillStyle='rgba(102,204,0,0.7)';
    ctx.fillText(txt,4,17);

    var b64=canvas.toDataURL().replace('data:image/png;base64,','');
    var bin=atob(b64);
    var crc=bin2hex(bin.slice(-16,-12));
    return crc;
}

function setCookie(name,value){
	var Days = 30;
	var exp = new Date();
	exp.setTime(exp.getTime() + Days*24*60*60*1000);
	document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();
}

function getCookie(name){
	var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
	if(arr=document.cookie.match(reg))
	return unescape(arr[2]);
	else
	return null;
}

function getClientSign(cb){
	var getSignFromCookie = getCookie('xtLetseatSign');
	if (!getSignFromCookie) {
		var newSign = rand(11111111,99999999);
		setCookie('xtLetseatSign',newSign);
		result = newSign;
	} else {
		result = getSignFromCookie;
	}
    cb && cb(result);
}

function checkUserExist(existCb)
{
	getClientSign(function(userid){
		get('getUser', {
			'userid': userid
		}, function(res) {
			if (res.code == 200) {
				if (res.msg == '该用户不存在') {
					var name = prompt(userid+'是一个新的userid，给自己起个名字吧。', '输入一个响亮的名字，例如：yoyo姐');
					get('addUser', {
						userid: userid,
						name: name
					}, function(res2){
						if (res2.code == 200) {
							existCb && existCb(userid);
						} else {
							alert(res2.msg);
						}
					});
				} else {
					existCb && existCb(userid);
				}
			} else {
				alert(res.msg);
			}
		});
	});
}

function jumpToMain(){
	window.location.href = 'main.html';
}