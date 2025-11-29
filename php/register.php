<?php
require "db_functions.php";

$error = false;
$success = false;
$name = $email = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST["name"]) && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirm_password"])) {

    $conn = connect_db();

    $name = mysqli_real_escape_string($conn,$_POST["name"]);
    $email = mysqli_real_escape_string($conn,$_POST["email"]);
    $password = mysqli_real_escape_string($conn,$_POST["password"]);
    $confirm_password = mysqli_real_escape_string($conn,$_POST["confirm_password"]);

    if ($password == $confirm_password) {
      $password = md5($password);
      
      try {
        $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";
        
        if(mysqli_query($conn, $sql)){
          $success = true;
        }
        else {
          throw new Exception(mysqli_error($conn)); // Converte erro em exceção
        }
      } 
      catch (Exception $e) {
        $error_message = $e->getMessage();
        
        // DETECTA SE É EMAIL DUPLICADO
        if (strpos($error_message, 'Duplicate entry') !== false && strpos($error_message, 'email') !== false) {
          $error_msg = "Este email já está cadastrado! Use outro email ou faça login.";
        } else {
          $error_msg = "Erro ao criar usuário: " . $error_message;
        }
        $error = true;
      }
    }
    else {
      $error_msg = "Senha não confere com a confirmação.";
      $error = true;
    }
  }
  else {
    $error_msg = "Por favor, preencha todos os dados.";
    $error = true;
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Registro</title>
  <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
  <div class="tela tela-registro">
    <h1>Registrar Novo Usuário</h1>

    <?php if ($success): ?>
      <div class="mensagem-sucesso">
        <h3>Usuário criado com sucesso!</h3>
        <p>Você já pode fazer <a href="login.php" class="botao-link">login</a></p>
      </div>
    <?php endif; ?>

    <?php if ($error): ?>
      <div class="mensagem-erro">
        <h3>Erro: <?php echo $error_msg; ?></h3>
      </div>
    <?php endif; ?>

    <form action="register.php" method="post" class="form-registro">
      <div class="form-group">
        <label for="name">Nome:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
      </div>

      <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
      </div>

      <div class="form-group">
        <label for="password">Senha:</label>
        <input type="password" name="password" required>
      </div>

      <div class="form-group">
        <label for="confirm_password">Confirmação da Senha:</label>
        <input type="password" name="confirm_password" required>
      </div>

      <div class="form-botoes">
        <input type="submit" name="submit" value="Criar Usuário" class="botao">
        <a href="../index.php" class="botao botao-voltar">Voltar</a>
      </div>
    </form>
  </div>
</body>
</html>