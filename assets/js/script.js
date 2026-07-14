function myScope() {
  // =========================================================================
  // 1. SELEÇÃO DE ELEMENTOS (CENTRALIZADA)
  // [MELHORIA]: Todos os elementos são buscados APENAS UMA VEZ aqui no topo.
  // Evita redundância e buscas repetidas no DOM a cada clique ou submit.
  // =========================================================================
  const dropZone = document.querySelector(".upload-avatar");
  const fileInput = document.querySelector("#fileInput");
  const previewAround = document.querySelector(".around-img");
  const imgUpload = document.querySelector("#img-upload");

  const form = document.querySelector("form");
  const nameInput = document.querySelector("#nameId");
  const githubInput = document.querySelector("#githubId");
  const emailInput = document.querySelector("#emailId");

  const infoUpload = document.querySelector(".info-upload p");
  const sendsP = document.querySelector(".send p");
  const btnImg = document.querySelectorAll(".btnImg");
  const btnRemoveImg = document.querySelector(".btnRemoveImg");
  const btnChangeImg = document.querySelector(".btnChangeImg");
  const msgErrorName = document.querySelector(".msg-infoName");
  const msgErrorEmail = document.querySelector(".msg-infoEmail");
  const msgErrorGitHub = document.querySelector(".msg-infoGitHub");

  // =========================================================================
  // 2. CONFIGURAÇÕES E CONSTANTES
  // [CLEAN CODE]: Dados estáticos e textos de erro isolados da lógica de controle.
  // Mudar textos ou caminhos de imagens agora é feito em um só lugar.
  // =========================================================================

  const CONFIG = {
    maxSizeInBytes: 500 * 1024, // 500 KB
    allowedTypes: ["image/jpeg", "image/png"],
    assets: {
      imgStandard:
        '<img class="info-img" src="assets/images/simbolo-de-informacao.png" alt="" />',
      imgError:
        '<img class="info-img" src="assets/images/simbolo-de-informacao-error.png" alt="" />',
    },
    errors: {
      name: "Please enter a name.",
      email: "Please enter a valid email address.",
      github: "Please enter your user name.",
      avatar: "Upload your photo (JPG or PNG, max size: 500KB).",
    },
  };

  // =========================================================================
  // 3. ESTADO DA APLICAÇÃO
  // =========================================================================
  let currentImageBase64 = "";

  // Inicialização obrigatória
  clearAndResetAll();

  // =========================================================================
  // 4. LISTENERS DE EVENTOS (FIXOS E ÚNICOS)
  // =========================================================================

  btnChangeImg.addEventListener("click", (e) => {
    e.stopPropagation();
    fileInput.click();
  });

  btnRemoveImg.addEventListener("click", (e) => {
    e.stopPropagation();
    resetPreview();
  });

  // --- Upload / Drag and Drop ---
  dropZone.addEventListener("click", () => {
    if (currentImageBase64) return;

    fileInput.click();
    fileInput.addEventListener("input", (e) => {
      if (!(e.target.value === "")) {
        sendsP.style.display = "none";
        for (let valor of btnImg) {
          valor.style.display = "inline";
        }

        dropZone.style.cursor = "auto";
      }
    });
  });

  dropZone.addEventListener("dragover", (e) => {
    if (currentImageBase64) return;

    dropZone.classList.add("drag-over");
  });

  ["dragleave", "dragend"].forEach((type) => {
    dropZone.addEventListener(type, () => {
      dropZone.classList.remove("drag-over");
    });
  });

  dropZone.addEventListener("drop", (e) => {
    dropZone.classList.remove("drag-over");
    resetAvatarErrorVisuals();

    sendsP.style.display = "none";
    for (let valor of btnImg) {
      valor.style.display = "inline";
    }

    dropZone.style.cursor = "auto";
    dropZone.addEventListener("click", (e) => e.preventDefault());

    if (e.dataTransfer.files.length) {
      handleFile(e.dataTransfer.files[0]);
    }
  });

  fileInput.addEventListener("change", () => {
    if (fileInput.files.length) {
      handleFile(fileInput.files[0]);
      resetAvatarErrorVisuals();
    }
  });

  // --- Validação em Tempo Real (Inputs) ---
  // [CORREÇÃO DE ERRO CRÍTICO]: Movidos para o escopo principal.
  // No código anterior, estes listeners eram criados DE DENTRO do evento de 'submit'.
  // Isso gerava um "Vazamento de Memória" (Memory Leak), acumulando múltiplos listeners
  // idênticos nos inputs a cada tentativa frustrada de envio do usuário.
  nameInput.addEventListener("input", () =>
    validateField(nameInput, msgErrorName, CONFIG.errors.name),
  );
  emailInput.addEventListener("input", () =>
    validateField(emailInput, msgErrorEmail, CONFIG.errors.email),
  );
  githubInput.addEventListener("input", () =>
    validateField(githubInput, msgErrorGitHub, CONFIG.errors.github),
  );

  // --- Envio do Formulário ---
  form.addEventListener("submit", (e) => {
    // [MELHORIA]: Removido o contador manual genérico "vazio++ / vazio -= 1",
    // que era propenso a bugs de contagem negativa. Agora usamos validação booleana estrita.
    const isFormValid = runAllValidations();

    // Se o formulário não for válido (Dados vázios)
    if (!isFormValid) {
      e.preventDefault();
      // form.reset();
      // resetPreview();
      return;
    }

    const userProfile = {
      imagem: currentImageBase64,
    };

    localStorage.setItem("userProfile", JSON.stringify(userProfile));

    console.log("Dados enviados e salvos no Storage com sucesso!");

  });

  // =========================================================================
  // 5. FUNÇÕES DE LÓGICA E MANIPULAÇÃO (HANDLERS)
  // =========================================================================

  function handleFile(file) {
    if (!CONFIG.allowedTypes.includes(file.type)) {
      alert("Apenas imagens JPG ou PNG são permitidas.");
      resetPreview();
      return;
    }

    if (file.size > CONFIG.maxSizeInBytes) {
      alert("O arquivo é muito grande! O tamanho máximo é 500KB.");
      resetPreview();
      return;
    }

    const reader = new FileReader();
    reader.onload = (e) => {
      currentImageBase64 = e.target.result;

      // Salva apenas a imagem
      localStorage.setItem("userAvatar", currentImageBase64);

      displayPreview(currentImageBase64);
    };
    reader.readAsDataURL(file);
  }

  function displayPreview(url) {
    if (imgUpload) imgUpload.style.opacity = "0";

    previewAround.style.backgroundImage = `url(${url})`;
    previewAround.style.backgroundSize = "cover";
    previewAround.style.backgroundPosition = "center";
    previewAround.style.border = "1px solid white";
  }

  function resetPreview() {
    // [CORREÇÃO DE ERRO]: Removido o uso incorreto do "this.imgUpload".
    // Em funções normais do JS que não são construtoras, o "this" pode perder o escopo (ficar undefined).
    if (imgUpload) imgUpload.style.opacity = "1";

    previewAround.style.backgroundImage = "none";
    previewAround.style.border = "1px solid rgba(255, 255, 255, 0.116)";
    currentImageBase64 = "";

    localStorage.removeItem("userAvatar");

    sendsP.style.display = "block";
    for (let valor of btnImg) {
      valor.style.display = "none";
    }

    dropZone.style.cursor = "pointer";
  }

  // =========================================================================
  // 6. SESSÃO DE VALIDAÇÕES (SUBSTITUTA DA ANTIGA 'MsgError')
  // [MELHORIA]: A antiga função construtora MsgError violava o princípio SRP
  // (Responsabilidade Única). Esta nova abordagem separa a lógica de validação por funções.
  // =========================================================================

  // Executa todas as validações no momento do submit e retorna se o form está válido
  function runAllValidations() {
    // [DICA DE CLEAN CODE]: Em um ambiente real, substitua as edições de .style.border
    // injetadas pelo JS por manipulação de classes CSS (ex: input.classList.add("error")).
    let isValid = true;

    // Validação do Avatar
    if (!currentImageBase64) {
      infoUpload.innerHTML = `${CONFIG.assets.imgError} ${CONFIG.errors.avatar}`;
      infoUpload.style.color = "hsl(7, 86%, 67%)";
      isValid = false;
    }

    // Validação dos Campos de Texto
    if (!validateField(nameInput, msgErrorName, CONFIG.errors.name))
      isValid = false;
    if (!validateField(emailInput, msgErrorEmail, CONFIG.errors.email))
      isValid = false;
    if (!validateField(githubInput, msgErrorGitHub, CONFIG.errors.github))
      isValid = false;

    return isValid;
  }

  // Valida um campo de texto individual (reutilizável tanto no submit quanto no evento input)
  function validateField(inputElement, errorContainer, errorMessage) {
    if (inputElement.value.trim().length > 0) {
      errorContainer.innerHTML = "";
      inputElement.style.border = "";
      return true;
    } else {
      errorContainer.innerHTML = `${CONFIG.assets.imgError} ${errorMessage}`;
      inputElement.style.border = "1px solid hsl(7, 86%, 67%)";
      return false;
    }
  }

  // Reseta visualmente a seção do avatar para o estado padrão sem erros
  function resetAvatarErrorVisuals() {
    infoUpload.innerHTML = `${CONFIG.assets.imgStandard} ${CONFIG.errors.avatar}`;
    infoUpload.style.color = "white";
    previewAround.style.border = "1px solid rgba(255, 255, 255, 0.116)";
  }

  function clearAndResetAll() {
    localStorage.removeItem("userProfile");
    form.reset();
    resetPreview();
  }
}

myScope();
