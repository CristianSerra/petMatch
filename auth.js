// ===== REGEX SENHA =====

const senhaRegex = /^(?=.*[!@#$%^&*]).{6,}$/;

// ===== CADASTRO =====

const cadastroForm = document.getElementById("cadastroForm");

if (cadastroForm) {

    cadastroForm.addEventListener( "submit",  async function(e){
         e.preventDefault();

            const email = document.getElementById("email").value.trim();
            const login = document.getElementById("login").value.trim();
            const senha = document.getElementById("senha").value;
            const dataNascimento =  document.getElementById("dataNascimento").value;

            const confirmarSenha =  document.getElementById("confirmarSenha").value;

            console.log("===== CAMPOS =====");
            console.log("email:", email);
            console.log("login:", login);
            console.log("senha:", senha);
            console.log("dataNascimento:", dataNascimento);

            const payload = {

                email: email,
                login: login,
                senha: senha,
                birth_date: dataNascimento

            };

           try {

    const resposta = await fetch( "./petmatch-api/cadastro.php",
        {
            method: "POST",  headers: {  "Content-Type": "application/json"  }, body: JSON.stringify(payload)
        }
    );

    const data = await resposta.json();

    console.log("===== RESPOSTA PHP =====");
    console.log(data);

    if (data.success) {

        alert(data.message); // "Usuário cadastrado com sucesso."

        // limpa formulário
        cadastroForm.reset();

        // redireciona para login
        window.location.href = "login.html";

    } else {
        alert(data.message); // erro vindo do PHP
    }

} catch (error) {
    console.error(error);
    alert("Erro ao conectar com o servidor.");
}
        }
    );
}

// ===== LOGIN =====

const loginForm = document.getElementById("loginForm");

if (loginForm) {

  loginForm.addEventListener("submit", async function (e) {
    e.preventDefault();

    const loginEmail = document.getElementById("loginEmail").value;
    const senha = document.getElementById("loginSenha").value;
    const msg = document.getElementById("msgLogin");
    localStorage.clear();
    try {
      const resposta = await fetch("./petmatch-api/login.php", {
        method: "POST", headers: { "Content-Type": "application/json" },  credentials: "include",
        body: JSON.stringify({ login: loginEmail.trim(), senha: senha.trim() })
      });

      const data = await resposta.json();

      console.log("LOGIN RESPONSE:", data);

      if (!data.success) {

        msg.textContent = data.message;
        msg.style.color = "red";

        return;
      }

      msg.textContent = "Login realizado com sucesso!";
      msg.style.color = "green";

      // 🔥 FORÇA REDIRECIONAMENTO
      setTimeout(() => {
        window.location.href = "index.html";
      }, 600);

    } catch (error) {

      console.error(error);

      msg.textContent = "Erro ao conectar com servidor";
      msg.style.color = "red";

    }

  });

}

// ===== RECUPERAÇÃO =====

const recuperarForm = document.getElementById("recuperarForm");

if (recuperarForm) { recuperarForm.addEventListener( "submit", function(e){
e.preventDefault();
const email = document.getElementById( "emailRecuperar" ).value;
// gerar token
localStorage.setItem( "resetEmail", email );
window.location.href = "nova-senha.html";

});
}

// ===== VALIDAR SESSÃO =====

async function validarSessao(){
  try {
  const resposta = await fetch( "./petmatch-api/validar.php", { credentials: "include" } );
  const data =await resposta.json();
  if (!data.logado){ window.location.replace("./login.html"); }
  } catch(error){
  window.location.replace("./login.html");
  }

}

async function logout(){
  console.log("logout executado");
  try {
    const resposta = await fetch( "./petmatch-api/logout.php", { method: "POST", credentials: "include" } );
    const data =await resposta.json();
    console.log(data);
    window.location.replace("./login.html");
    } catch(error){
      console.error( "Erro no logout:", error);
    }
}

const btnLogout = document.getElementById("btnLogout");
if (btnLogout){ btnLogout.addEventListener( "click", logout); }