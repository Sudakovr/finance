<?php
#components/bitrix/example/ajax.php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

class ExampleAjaxController extends \Bitrix\Main\Engine\Controller
{
    #в параметр $person будут автоматически подставлены данные из REQUEST
    public function sayByeAction($person = 'guest')
    {
        return "Goodbye {$person}";
    }

    public function listUsersAction(array $filter)
    {
        $users = [];
        //выборка пользователей по фильтру
        //наполнения массива данными для ответа
        
        return $users;
    }
}
