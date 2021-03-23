<?php
require_once __DIR__ . '/vendor/autoload.php';

use Authing\Mgmt\UsersManagementClient;
use Authing\Types\LoginByEmailInput;
use Authing\Auth\AuthenticationClient;
use Authing\Types\LoginByUsernameInput;


$authentication = new AuthenticationClient(function ($options) {
    $options->appId = "5f97fb40d352ecf69ffe6d98";
});

// $authentication = new AuthenticationClient('5f88506c81cd279930195660');
$user = $authentication->loginByUsername(new LoginByUsernameInput("shubuzuo", "123456"));

$userManagementClient = new UsersManagementClient($authentication);

// $userManagementClient = new UserManagementClientClient('-');
// $user = $userManagementClient->loginByUsername(new LoginByUsernameInput("-", "-"));

// $userManagementClient->setAccessToken($user->token);

// [ ] 批量获取自定义数据
// [ ] 批量设置自定义数据
// [ ] 检查用户是否存在
$_ = new stdClass;
$_->username = "-";
// $_->email = "1409458062@qq.com";
// $_->phone = 17630802710;
// TODO: 有问题
// $data = $userManagementClient->exists($_);

// [ ] 获取用户所在组织机构


// $data = $userManagementClient->listOrgs($user->id);

// [ ] 获取用户被授权的所有资源列表

$data = $userManagementClient->listAuthorizedResources('602e2ddf5fd407fe8a8e8f19', "default");


echo 'aa';
