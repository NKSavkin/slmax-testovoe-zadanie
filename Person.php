<?php

class Person
{
    private $id;
    private $name;
    private $surname;
    private $birthdate;
    private $gender;
    private $birthplace;

    public function __construct($data)
    {
        if (isset($data['id'])) {
            $this->id = $data['id'];
            $this->name = $data['name'];
            $this->surname = $data['surname'];
            $this->birthdate = $data['birthdate'];
            $this->gender = $data['gender'];
            $this->birthplace = $data['birthplace'];
        } else {
            $this->name = $data['name'];
            $this->surname = $data['surname'];
            $this->birthdate = $data['birthdate'];
            $this->gender = $data['gender'];
            $this->birthplace = $data['birthplace'];
            $this->save();
        }
    }

    public function save()
    {
        $db = new mysqli('localhost', 'username', 'password', 'database_name');

        // Проверяем, есть ли у объекта id
        if (!isset($this->id)) {
            // Если id нет, значит, объект нужно создать в БД
            $query = "INSERT INTO people (name, surname, birthdate, gender, birthplace) VALUES (?, ?, ?, ?, ?)";

            // Создаем prepared statement и привязываем параметры
            $stmt = $db->prepare($query);
            $stmt->bind_param('sssis', $this->name, $this->surname, $this->birthdate, $this->gender, $this->birthplace);

            // Выполняем запрос
            $stmt->execute();

            // Получаем id созданной записи
            $this->id = $db->insert_id;
        } else {
            // Если id есть, значит, объект нужно обновить в БД
            $query = "UPDATE people SET name=?, surname=?, birthdate=?, gender=?, birthplace=? WHERE id=?";

            // Создаем prepared statement и привязываем параметры
            $stmt = $db->prepare($query);
            $stmt->bind_param('sssisi', $this->name, $this->surname, $this->birthdate, $this->gender, $this->birthplace, $this->id);

            // Выполняем запрос
            $stmt->execute();
        }

        // Закрываем соединение с БД и освобождаем ресурсы
        $stmt->close();
        $db->close();
    }

    public function delete()
    {
        $db = new mysqli('localhost', 'username', 'password', 'database_name');

        // Проверяем, есть ли у объекта id
        if (isset($this->id)) {
            // Если id есть, удаляем запись из БД
            $query = "DELETE FROM people WHERE id=?";

            // Создаем prepared statement и привязываем параметры
            $stmt = $db->prepare($query);
            $stmt->bind_param('i', $this->id);

            // Выполняем запрос
            $stmt->execute();

            // Обнуляем id объекта
            $this->id = null;
        }

        // Закрываем соединение с БД и освобождаем ресурсы
        $stmt->close();
        $db->close();
    }

    public static function ageFromBirthdate($birthdate)
    {
        // Реализация преобразования даты рождения в возраст (полных лет)
        $birthdate_timestamp = strtotime($birthdate);
        $age = date('Y') - date('Y', $birthdate_timestamp);
        if (date('md') < date('md', $birthdate_timestamp)) {
            $age--;
        }
        return $age;
    }

    public static function genderFromBinary($binary)
    {
        // Реализация преобразования пола из двоичной системы в текстовую (муж, жен)
        return $binary ? 'муж' : 'жен';
    }

    public function format($include_age = false, $include_gender = false)
    {
        // Форматирование человека с преобразованием возраста и (или) пола (п.3 и п.4) в зависимости от параметров
        $formatted = new stdClass();
        $formatted->id = $this->id;
        $formatted->name = $this->name;
        $formatted->surname = $this->surname;
        $formatted->birthdate = $this->birthdate;
        $formatted->gender = $this->gender;
        $formatted->birthplace = $this->birthplace;
        if ($include_age) {
            $formatted->age = self::ageFromBirthdate($this->birthdate);
        }
        if ($include_gender) {
            $formatted->gender = self::genderFromBinary($this->gender);
        }
        return $formatted;
    }
}
