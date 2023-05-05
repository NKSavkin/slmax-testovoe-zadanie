<?php

if (!class_exists('Person')) {
    throw new Exception('Ошибка: класс Person не объявлен.');
}
class PeopleList {
    private $ids = array(); // Массив с id людей

    public function __construct($params = array()) {
        $db = new mysqli('localhost', 'username', 'password', 'database_name');

        $query = "SELECT id FROM people WHERE 1=1";

        // Добавляем условия для поиска
        if (isset($params['name'])) {
            $query .= " AND name LIKE '%{$params['name']}%'";
        }

        if (isset($params['surname'])) {
            $query .= " AND surname LIKE '%{$params['surname']}%'";
        }

        if (isset($params['birthdate'])) {
            $birthdate = $db->real_escape_string($params['birthdate']);
            $query .= " AND birthdate='{$birthdate}'";
        }

        if (isset($params['gender'])) {
            $gender = $params['gender'] == 1 ? 'Male' : 'Female';
            $query .= " AND gender='{$gender}'";
        }

        if (isset($params['city'])) {
            $city = $db->real_escape_string($params['city']);
            $query .= " AND city='{$city}'";
        }

        // Выполняем запрос и записываем id людей в массив
        $result = $db->query($query);
        while ($row = $result->fetch_assoc()) {
            $this->ids[] = $row['id'];
        }

        // Закрываем соединение с БД и освобождаем ресурсы
        $result->close();
        $db->close();
    }

    public function getPeople() {
        $people = array();

        // Получаем массив экземпляров класса People по массиву id
        foreach ($this->ids as $id) {
            $people[] = new People($id);
        }

        return $people;
    }

    public function deletePeople() {
        $db = new mysqli('localhost', 'username', 'password', 'database_name');

        // Удаляем записи из БД в соответствии с массивом id
        $query = "DELETE FROM people WHERE id IN (" . implode(',', $this->ids) . ")";
        $db->query($query);

        // Закрываем соединение с БД и освобождаем ресурсы
        $db->close();
    }
}
