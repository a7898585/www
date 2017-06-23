<?php

return array(
//    'TMPL_EXCEPTION_FILE' => '404.html', // 异常页面的模板文件
//Cookie设置
    'COOKIE_EXPIRE' => 0, // Cookie有效期
    'COOKIE_DOMAIN' => 'xinwenwang.com', // Cookie有效域名
    'COOKIE_PATH' => '/', // Cookie路径
    'COOKIE_PREFIX' => 'xw_', // Cookie前缀 避免冲突
//SPHINX 搜索引擎设置
    'SPHINX_SERVER' => '127.0.0.1',
    'SPHINX_HOST' => 9312,
    
//'配置项'=>'配置值'
    'SHOW_ERROR_MSG' => true,
    'LAYOUT_ON' => false,
    'URL_MODEL' => 2,
    'TMPL_PARSE_STRING' => array('__APP__' => '/Home/',),
    'APP_SUB_DOMAIN_DEPLOY' => 1,
    'DEFAULT_HEAD' => '/Public/Home/images/default_head.jpg',
    'URL_DOMAIN' => 'http://www.xinwenwang.com',
    'URL_ROUTER_ON' => true,
    'URL_ROUTE_RULES' => array(
        '/register(|\/)$/' => 'Public/register',
        '/login(|\/)$/' => 'Public/login',
        '/loginout(|\/)$/' => 'Public/loginout',
        '/hot(|\/)$/' => 'News/hot?n=hot',
        '/shehui(|\/)$/' => 'News/lists?type_id=1802&n=shehui',
        '/yule(|\/)$/' => 'News/lists?type_id=1803&n=yule',
        '/tiyu(|\/)$/' => 'News/lists?type_id=1804&n=tiyu',
        '/qiche(|\/)$/' => 'News/lists?type_id=1808&n=qiche',
        '/caijing(|\/)$/' => 'News/lists?type_id=1805&n=caijing',
        #其他分类
        '/guoji(|\/)$/' => 'News/lists?type_id=1807&n=guoji',
        '/junshi(|\/)$/' => 'News/lists?type_id=1806&n=junshi',
        '/youxi(|\/)$/' => 'News/lists?type_id=1810&n=youxi',
        '/lvyou(|\/)$/' => 'News/lists?type_id=1811&n=lvyou',
        '/shichang(|\/)$/' => 'News/lists?type_id=1826&n=shichang',
        '/keji(|\/)$/' => 'News/lists?type_id=1809&n=keji',
        '/shuma(|\/)$/' => 'News/lists?type_id=1827&n=shuma',
        '/fangchan(|\/)$/' => 'News/lists?type_id=1815&n=fangchan',
        '/jiaju(|\/)$/' => 'News/lists?type_id=1823&n=jiaju',
        '/gaoxiao(|\/)$/' => 'News/lists?type_id=1828&n=gaoxiao',
        '/dongman(|\/)$/' => 'News/lists?type_id=1833&n=dongman',
        '/sousuo(|\/)$/' => 'News/lists?type_id=1820&n=sousuo',
        '/lishi(|\/)$/' => 'News/lists?type_id=1830&n=lishi',
        '/nvren(|\/)$/' => 'News/lists?type_id=1813&n=nvren',
        '/jiankang(|\/)$/' => 'News/lists?type_id=1814&n=jiankang',
        '/dianying(|\/)$/' => 'News/lists?type_id=1816&n=dianying',
        '/dushu(|\/)$/' => 'News/lists?type_id=1817&n=dushu',
        '/yangsheng(|\/)$/' => 'News/lists?type_id=1818&n=yangsheng',
        '/yulu(|\/)$/' => 'News/lists?type_id=1819&n=yulu',
        '/yuer(|\/)$/' => 'News/lists?type_id=1821&n=yuer',
        '/wenhua(|\/)$/' => 'News/lists?type_id=1824&n=wenhua',
        '/xingzuo(|\/)$/' => 'News/lists?type_id=1825&n=xingzuo',
        '/qinggan(|\/)$/' => 'News/lists?type_id=1829&n=qinggan',
        '/meishi(|\/)$/' => 'News/lists?type_id=1831&n=meishi',
        '/jianfei(|\/)$/' => 'News/lists?type_id=1832&n=jianfei',
        '/giftu(|\/)$/' => 'News/lists?type_id=1834&n=giftu',
        '/qutu(|\/)$/' => 'News/lists?type_id=1812&n=qutu',
        '/meitu(|\/)$/' => 'News/lists?type_id=1822&n=meitu',
        '/updates(|\/)$/' => 'Index/updates',
        '/search(|\/)$/' => 'Index/search',
        '/hot_comments(|\/)$/' => 'Index/hot_comments',
        '/app_server(|\/)$/' => 'app/index',
        '/user/pin(|\/)$/' => 'User/pin',
        '/user/subscribe(|\/)$/' => 'User/subscribe',
        '/^r(\d+)(|\/)$/' => 'News/info?id=:1',
        '/^m(\d+)(|\/)$/' => 'News/dingyue?id=:1',
        '/^show-(.*)(|\/)$/' => 'Index/index',
        '/^subject\/(\d+)(|\/)$/' => 'Subject/lists?type_id=:1',
    )
);