<?php
$hash = '$2y$10$QGs6HaUMc23LzrDXBn4C5efpz94rEVKhHN3BfJCrxA8rA9vLLf8nm';
$result = password_verify('picpic123', $hash);
echo $result ? 'VALID' : 'INVALID';
?>
