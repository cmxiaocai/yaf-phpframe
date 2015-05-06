YAF扩展框架
==================

基于YAF框架扩展，新增Service层和引入单元测试。目标为可自动化测试，可扩展的基础框架。
	
## 部署

 * 安装yaf扩展
	(1) 在环境中编译yaf扩展,并在php.ini加入yaf.so
		extension = yaf.so
	(2) 在php.ini设置yaf参数
		yaf.use_namespace = 1	;开启命名空间
		yaf.lowcase_path  = 1	;目录名小写

 * 使用compose编译php包
	(1) 进入项目expand目录中(composer.json文件所在位置),执行composer.phar install编译php包

 * 配置nginx设置
	(1) 加入rewrite规则
--
	if (!-e $request_filename) {
		rewrite  ^(.*)$  /index.php?r=/$1  last;
		break;
	}
--

 * 设置环境变量
--
	fastcgi_param  ENVIRONMENT    LOC;
	#LOC表示本地环境,DEV表示内网环境,COM表示外网环境,若不设置默认为COM
--

 * [建议]域名设置
--
	server_name  www.xiaocai.loc;
	#将本地的域名后缀设置为.loc,以后需要同时在内外网进行调试时可以避免频繁的切换host
--

 * [建议]路径解析
--
	root /mnt/hgfs/www/git_xiaocai_com/webroot;
	#请将nginx访问路径解析到项目目录中的webroot文件夹,将项目代码放在webroot外层能够提高一定的安全性。
--

 * 修改config中的ini配置
 
	(1) 所有ini文件中都有common、com、dev、loc四个配置项,common表示公共的配置项,另外三个分别对应不同的配置环境，根据自己的需要修改配置项。


## 项目结构：
--
		++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		+			 +				  +			   +		   +
		+    views	 +	 controllers  +	 services  +   models  +
		+			 +				  +			   +		   +
		++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		application			#项目代码目录，用于存放项目的逻辑代码
			+config			#项目配置文件
			+controllers	#控制器
			+library		#本地类库
			+models			#模型层
			+modules		#模块
			+plugins		#插件
			+services		#服务层
			+views			#视图层
		expand				#存放yaf扩展文件
			+adapter		#数据库连接适配器
			+vendor			#composer编译包
			+init.php		#扩展引用文件
			+composer.json  #composer配置
		test				#项目的测试用例文件(测试用例文件需要与application结构保存一致)
			+controllers	#控制器测试用例
			+models			#模型测试用例
			+services		#服务测试用例
		webroot				#项目对外nginx访问目录
--

