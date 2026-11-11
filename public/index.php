<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../src/functions.php';

$version = '1.0';
$today = date('d.m.Y');
$employees = loadEmployees(__DIR__ . '/../data/employees.csv');
$total = count($employees);
$departments = array_values(array_unique(array_column($employees, 'department')));

$selectedDept = trim($_GET['dept'] ?? '');
$shown = filterEmployees($employees, $selectedDept);

$old = ['name' => '', 'email' => '', 'department' => '', 'experience' => ''];
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['name']       = trim($_POST['name'] ?? '');
    $old['email']      = trim($_POST['email'] ?? '');
    $old['department'] = trim($_POST['department'] ?? '');
    $old['experience'] = trim($_POST['experience'] ?? '');

    $errors = validateInput($old, $departments);
    $success = ($errors === []);
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Практическая работа 4</title>
</head>
<body>
<main>
    <h1>Сотрудники</h1>
    <p>Версия приложения: <?= e($version) ?>, дата: <?= e($today) ?></p>
    <p>Всего сотрудников: <?= $total ?></p>

    <h2>Добавить заявку сотрудника</h2>

    <?php if ($success): ?>
        <p><b>Заявка принята: <?= e($old['name']) ?></b></p>
    <?php endif; ?>

    <form method="post">
        <p>
            <label>ФИО:
                <input type="text" name="name" value="<?= e($old['name']) ?>">
            </label>
            <?php if (isset($errors['name'])): ?>
                <span style="color:red"><?= e($errors['name']) ?></span>
            <?php endif; ?>
        </p>
        <p>
            <label>Email:
                <input type="text" name="email" value="<?= e($old['email']) ?>">
            </label>
            <?php if (isset($errors['email'])): ?>
                <span style="color:red"><?= e($errors['email']) ?></span>
            <?php endif; ?>
        </p>
        <p>
            <label>Отдел:
                <select name="department">
                    <option value="">— выберите —</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= e($d) ?>" <?= $old['department'] === $d ? 'selected' : '' ?>><?= e($d) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <?php if (isset($errors['department'])): ?>
                <span style="color:red"><?= e($errors['department']) ?></span>
            <?php endif; ?>
        </p>
        <p>
            <label>Стаж (лет):
                <input type="text" name="experience" value="<?= e($old['experience']) ?>">
            </label>
            <?php if (isset($errors['experience'])): ?>
                <span style="color:red"><?= e($errors['experience']) ?></span>
            <?php endif; ?>
        </p>
        <button type="submit">Отправить</button>
    </form>

    <h2>Список сотрудников</h2>

    <form method="get">
        <select name="dept">
            <option value="">Все отделы</option>
            <?php foreach ($departments as $d): ?>
                <option value="<?= e($d) ?>" <?= $selectedDept === $d ? 'selected' : '' ?>><?= e($d) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Фильтр</button>
    </form>

    <?php if ($shown === []): ?>
        <p>Сотрудники не найдены.</p>
    <?php else: ?>
        <table border="1" cellpadding="6">
            <tr>
                <th>ID</th><th>ФИО</th><th>Отдел</th><th>Стаж</th><th>Email</th>
            </tr>
            <?php foreach ($shown as $emp): ?>
                <tr>
                    <td><?= $emp['id'] ?></td>
                    <td><?= e($emp['name']) ?></td>
                    <td><?= e($emp['department']) ?></td>
                    <td><?= $emp['experience'] ?></td>
                    <td><?= e($emp['email']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</main>
</body>
</html>