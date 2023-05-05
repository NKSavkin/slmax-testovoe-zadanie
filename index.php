<?php
require_once 'Person.php';
require_once 'PeopleList.php';

// Создание нового объекта класса и сохранение его в БД
$person = new Person([
    'name' => 'Иван',
    'surname' => 'Иванов',
    'birthdate' => '1990-01-01',
    'gender' => 1,
    'birthplace' => 'Москва'
]);

// Получение объекта класса из БД по id
$person = new Person(['id' => 1]);

// Форматирование объекта класса с преобразованием возраста и пола
$formatted_person = $person->format(true, true);

// Удаление объекта класса из БД
$person->delete();

?>