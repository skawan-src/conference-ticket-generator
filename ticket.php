<?php
session_start();

$nome = $_SESSION["usuario_nome"];
$email = $_SESSION["usuario_email"];
$user_git = $_SESSION["usuario_git"];

$dia = $_SESSION['dia'];
$mesText = $_SESSION['mesText'];
$ano = $_SESSION['ano'];


$cidade = $_SESSION['cidade'];
$estado = $_SESSION['estado'];
$pais = $_SESSION['pais_sigla'];

?>
<!doctype html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Frontend Mentor | Conference ticket generator</title>
  <link
    rel="icon"
    type="image/png"
    sizes="32x32"
    href="/assets/images/favicon-32x32.png" />
  <link rel="stylesheet" href="assets/css/styleTicket.css" />
</head>

<body>
  <h2 class="titulo"><img src="/assets/images/logo-mark.svg" alt="" />Coding Conf</h2>
  <main>
    <header>
      <h1>
        Congrats, <?php echo "<span class='name'>" . htmlspecialchars($nome) . "!</span>"; ?>
        Your ticket is ready.
      </h1>
      <p>
        We've emailed your ticket to <br>
        <?php echo "<span class='email'>" . htmlspecialchars($email) . "</span>" ?>
        and will send updates in <br>
        the run up to the event.
      </p>
    </header>
    <!-- Form ends -->
    <!-- Generated tickets starts -->

    <!-- Congrats, -->
    <!-- Full Name! Your ticket is ready. We've emailed your ticket to -->
    <!-- Email Address -->
    <section class="ticket">
      <img src="/assets/images/pattern-ticket.svg" alt="">
      <div class="texto-sobreposto">
        <h2 class="titulo tticket"><img src="/assets/images/logo-mark.svg" alt="" />Coding Conf</h2>
        <p><?php echo "<span class='textDate'>" . htmlspecialchars($mesText) . ' ' . htmlspecialchars($dia) . ', ' . htmlspecialchars($ano) . ' ' . '/ ' . htmlspecialchars($cidade) . ', ' . htmlspecialchars($estado) . "</span>" ?></p>

        <div class="profile">
          <img id="ticket-avatar" class="img-avatar" src="" alt="">
          <p><?php echo "<span class='user-name'>" . htmlspecialchars($nome) . "</span>" . "<br>" ?>
            <span class="profile-gitHub">
              <img src="/assets/images/icon-github.svg" alt="">
              <?php
              $user_com_arroba = str_starts_with(($user_git ?? ''), '@') ? htmlspecialchars($user_git ?? '') : '@' . htmlspecialchars($user_git ?? '');
              echo "<span class='user-gitHub'>" . $user_com_arroba . "</span>"
              ?>
            </span>
          </p>
        </div>
      </div>
    </section>
    <!-- and will send updates in the run up to the event. Coding Conf Jan 31, 2025 /
          Austin, TX -->
  </main>
  <!-- Generated tickets ends -->
  <!-- <footer class="attribution">
        Challenge by
        <a href="https://www.frontendmentor.io?ref=challenge">Frontend Mentor</a>.
        Coded by <a href="#">Your Name Here</a>.
      </footer> -->
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const avatarImg = document.getElementById("ticket-avatar");

      // Busca a imagem Base64 que o seu arquivo cadastrar.php salvou
      const savedAvatar = localStorage.getItem("userAvatar");

      if (savedAvatar) {
        avatarImg.src = savedAvatar;
        avatarImg.style.display = "block"; // Mostra a imagem na tela
      } else {
        // Fallback: Caso o usuário não tenha imagem, exibe uma imagem padrão
        avatarImg.src = "/assets/images/image-avatar.png";
        avatarImg.style.display = "block";
      }
    });
  </script>
</body>

</html>