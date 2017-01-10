<?php
function register($user_name, $user_id, $account_id, $service) {
    try {
        $pdo = new PDO('mysql:host='.mysql['host'].';dbname='.mysql['db'].';charset=utf8', mysql['id'], mysql['password'], [PDO::ATTR_PERSISTENT => true]);

        $stmt = $pdo->prepare("INSERT IGNORE INTO accounts (user_name, user_id, account_id, $service) VALUES (:user_name, :user_id, :account_id, :service)");
        $stmt->bindValue(':user_name', $user_name);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->bindValue(':account_id', hash('sha256', $user_id.$account_id, false));
        $stmt->bindValue(':service', $account_id);
        $stmt->execute();

        header('Location: ../timeline.php');
        exit;
    } catch (PDOException $e) {
        exit($e->getCode());
    }
}
