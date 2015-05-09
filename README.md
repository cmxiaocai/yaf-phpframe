YAF扩展框架
==================

基于YAF框架扩展，新增Service层和引入单元测试。目标为可自动化测试，可扩展的基础框架。

http://xiaocai.loc:8001/demo/index/
	
--
if (!-e $request_filename) {
    rewrite  ^(.*)$  /index.php?uri=/$1  last;
    break;
}
--

