<?php
require "db_functions.php";
require "authenticate.php";

$error = false;
$password = $email = "";

if (!$login && $_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST["email"]) && isset($_POST["password"])) {

    $conn = connect_db();

    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);
    $password = md5($password);

    $sql = "SELECT id,username,email,user_password FROM users WHERE email = '$email';";

    $result = mysqli_query($conn, $sql);
    if ($result) {
      if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if ($user["user_password"] == $password) {
          $_SESSION["user_id"] = $user["id"];
          $_SESSION["user_name"] = $user["username"];
          $_SESSION["user_email"] = $user["email"];

          header("Location: ../index.php");
          exit();
        } else {
          $error_msg = "Senha incorreta!";
          $error = true;
        }
      } else {
        $error_msg = "Usuário não encontrado!";
        $error = true;
      }
    } else {
      $error_msg = mysqli_error($conn);
      $error = true;
    }
  } else {
    $error_msg = "Por favor, preencha todos os dados.";
    $error = true;
  }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Type the Zombies</title>
  <link rel="stylesheet" href="../styles/styles.css">
</head>

<body>
  <div class="tela tela-login">
    <h1>Login</h1>

    <?php if ($login): ?>
      <div class="mensagem-info">
        <h3>Você já está logado!</h3>
        <a href="../index.php" class="botao">Voltar ao Jogo</a>
      </div>
    <?php else: ?>

      <?php if ($error): ?>
        <div class="mensagem-erro">
          <h3><?php echo $error_msg; ?></h3>
        </div>
      <?php endif; ?>

      <form action="login.php" method="post" class="form-login">
        <div class="form-group">
          <label for="email">Email:</label>
          <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>

        <div class="form-group">
          <label for="password">Senha:</label>
          <input type="password" name="password" required>
        </div>

        <div class="form-botoes">
          <input type="submit" name="submit" value="Entrar" class="botao">
          <button type="button" onclick="window.location.href='../index.php'"
            class="botao botao-voltar">
            Voltar
          </button>
        </div>

        <div class="login-links">
          <p>Não tem conta? <a href="register.php">Cadastre-se aqui</a></p>
        </div>
      </form>

    <?php endif; ?>
  </div>
</body>

</html>