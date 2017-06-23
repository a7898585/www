<?php
return array(
		'URL_ROUTER_ON'             => true,
		'URL_ROUTE_RULES'           => array(
            'login'        => 'Public/login',
            'reg'        => 'Public/reg',
            'reg_ok'        => 'Public/reg_ok',
            'login_out'        => 'Public/loginout',
            'readme'        => 'Public/readme',

            'serve'        => 'Index/dkfw',

            'about'        => 'Index/about',
            'contact'        => 'Index/contact',

            '/pro\/(\S+)$/' => 'Index/pro_info?pro=:1',
            'pro'        => 'Index/product',

            'personal'        => 'User/personal',
            'personal_information'        => 'User/personal_information',
            'personal_list'        => 'User/personal_list',
			
				
			'apply'        => '/Home/User/quickchannel_landing',	
			'checkpasswd'        => '/Home/User/checkpasswd',
				
				
            'personal_info'        => '/Home/User/personal_info',
            'personal_upinformation'        => 'User/personal_upinformation',
            'personal_uppassword'        => 'User/personal_uppassword',
            'quickchannel_landing'        => 'User/quickchannel_landing',
            'quickchannel_nolanding'        => 'User/quickchannel_nolanding',
            'credit_personal'        => 'User/credit_personal',
            'credit_company'        => 'User/credit_company',
            'credit_jiufu'        => 'User/credit_jiufu',
            'complain'        => 'User/complain',
            'summary'        => 'Index/summary',
            'uppassword'        => 'Index/uppassword',
            'info'        => 'Index/info',
            'jigou_info'        => 'Index/jigou_info',

            'Institution'        => 'Index/Institution_Manage',
            'Institution_Info'        => 'Index/Institution_Info',
            'Institution_upInfo'        => 'Index/Institution_upInfo',
            'proposer'        => 'Index/proposer',
            'proposer_details'        => 'Index/proposer_details',
            'proposer_info'        => 'Index/proposer_info',
            'credit'        => 'Index/credit',
            'credit_info'        => 'Index/credit_info',
            'credit_add'        => 'Index/credit_add',
            'credit_manager'        => 'Index/credit_manager',
            'Institution_uppassword'        => 'Index/Institution_uppassword',
		)
);