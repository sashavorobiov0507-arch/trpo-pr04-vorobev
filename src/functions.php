<?php

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function parseEmployee(array $row): array
{
    return [
        'id'         => (int)$row[0],
        'name'       => $row[1],
        'department' => $row[2],
        'experience' => (int)$row[3],
        'email'      => $row[4],
    ];
}

function loadEmployees(string $path): array
{
    $employees = [];
    $handle = fopen($path, 'r');
    if ($handle === false) {
        return [];
    }

    fgetcsv($handle, 0, ';', '"', ''); // пропускаем строку с заголовками

    while (($row = fgetcsv($handle, 0, ';', '"', '')) !== false) {
        if (count($row) < 5) {
            continue; // пустая строка в конце файла
        }
        $employees[] = parseEmployee($row);
    }

    fclose($handle);
    return $employees;
}

function validateInput(array $input, array $departments): array
{
    $errors = [];

    $nameLen = mb_strlen($input['name']);
    if ($input['name'] === '') {
        $errors['name'] = 'Введите ФИО';
    } elseif ($nameLen < 2 || $nameLen > 100) {
        $errors['name'] = 'ФИО должно быть от 2 до 100 символов';
    }

    if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Некорректный email';
    }

    if (!in_array($input['department'], $departments, true)) {
        $errors['department'] = 'Выберите отдел из списка';
    }

    $exp = filter_var($input['experience'], FILTER_VALIDATE_INT);
    if ($input['experience'] === '') {
        $errors['experience'] = 'Укажите стаж';
    } elseif ($exp === false || $exp < 0 || $exp > 50) {
        $errors['experience'] = 'Стаж — целое число от 0 до 50';
    }

    return $errors;
}

function filterEmployees(array $employees, string $department): array
{
    if ($department === '') {
        return $employees;
    }

    $result = [];
    foreach ($employees as $emp) {
        if ($emp['department'] === $department) {
            $result[] = $emp;
        }
    }
    return $result;
}
