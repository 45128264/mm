<?php
//Restful风格API接口开发springMVC篇 Restful风格的API是一种软件架构风格，设计风格而不是标准，只是提供了一组设计原则和约束条件。它主要用于客户端和服务器交互类的软件。基于这个风格设计的软件可以更简洁，更有层次，更易于实现缓存等机制。
//在Restful风格中，用户请求的url使用同一个url而用请求方式：get，post，delete，put...等方式对请求的处理方法进行区分，这样可以在前后台分离式的开发中使得前端开发人员不会对请求的资源地址产生混淆和大量的检查方法名的麻烦，形成一个统一的接口。
//在Restful风格中，现有规定如下：
//GET（SELECT）：从服务器查询，可以在服务器通过请求的参数区分查询的方式。 POST（CREATE）：在服务器新建一个资源，调用insert操作。 PUT（UPDATE）：在服务器更新资源，调用update操作。 PATCH（UPDATE）：在服务器更新资源（客户端提供改变的属性）

//GET：读取（Read）
//POST：新建（Create）
//PUT：更新（Update）
//PATCH：更新（Update），通常是部分更新
//DELETE：删除（Delete）

use Qyk\Mm\Route\RouterRegister;

RouterRegister::group(['prefix' => 'users', 'suffix' => '.html'], function () {
    //未登陆
    RouterRegister::group(['middleware' => 'unLogon'], function () {
        RouterRegister::post('login', 'UsersController@login');
        RouterRegister::post('register', 'UsersController@register');
    });
    //已登陆
    RouterRegister::group(['middleware' => ['logined', 'checkRolePermission'], 'after' => ['logVisitedHistory']], function () {
        //        RouterRegister::get('logout', 'UsersController@logout');
        //        RouterRegister::get('{user_id}/card', 'UsersController@cardList');
        RouterRegister::any('{pageSize}/{user_id}/{card_id}/card', 'UsersController@cardDetail')->setDefaultVal(['pageSize' => '1'])->response('json');
        RouterRegister::get('session/save', 'UsersController@sessionSave')->response('json');
        RouterRegister::get('session/show', 'UsersController@sessionShow')->response('json');

        RouterRegister::get('curl/get', 'UsersController@curlGetClient')->response('json');
        RouterRegister::get('curl/service', 'UsersController@curlGetServer')->response('json');
        RouterRegister::get('captcha/index', 'UsersController@captchaIndex')->response('none');
        RouterRegister::get('captcha/verify/{code}', 'UsersController@captchaVerrify')->response('none');
        RouterRegister::get('excel/exp', 'UsersController@excelExp')->response('none');
        //        RouterRegister::post('card', 'UsersController@addCard');
        //        RouterRegister::put('card', 'UsersController@editCard');
        //        RouterRegister::patch('avatar', 'UsersController@avatar');
    });

});

//消息队列
RouterRegister::group(['prefix' => 'mq', 'response' => 'json'], function () {
    RouterRegister::get('sender', 'RabbitMqController@sender');
    RouterRegister::get('receiver', 'RabbitMqController@receiver');
});

//rpc
RouterRegister::group(['prefix' => 'thrift', 'response' => 'json'], function () {
    RouterRegister::get('hello', 'ThriftController@index');
});