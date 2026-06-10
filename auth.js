// ===== REGEX SENHA =====

const senhaRegex =
/^(?=.*[!@#$%^&*]).{6,}$/;

// ===== CADASTRO =====

const cadastroForm = document.getElementById("cadastroForm");

if (cadastroForm) {

cadastroForm.addEventListener("submit",async function(e){

e.preventDefault();

const email = document.getElementById("email").value;

const login = document.getElementById("login").value;

const senha = document.getElementById("senha").value;

if (!senhaRegex.test(senha)) {

    alert("Senha deve ter 6+ caracteres e 1 especial");

return;

}

try {

const resposta = await fetch("./petmatch-api/cadastro.php",{method: "POST",headers: {"Content-Type": "application/json"},body: JSON.stringify({email,login,senha}) } );

const data = await resposta.json();

if (!data.success){

alert(data.message);
return;

}

alert("Cadastro realizado!");

window.location.replace("./login.html");

} catch(error){

console.error(error);

alert(
"Erro ao cadastrar"
);

}

});
}

// ===== LOGIN =====

const loginForm = document.getElementById("loginForm");

if (loginForm) {

  loginForm.addEventListener("submit", async function (e) {
    e.preventDefault();

    const loginEmail = document.getElementById("loginEmail").value;
    const senha = document.getElementById("loginSenha").value;
    localStorage.clear();
    try {

      const resposta = await fetch("./petmatch-api/login.php", { method: "POST", headers: { "Content-Type": "application/json" }, credentials: "include",
        body: JSON.stringify({
          login: loginEmail.trim(),
          senha: senha.trim()
        })
      });

      const data = await resposta.json();

      console.log(data);

      if (!data.success) {
        alert(data.message);
        return;
      }

      alert("Login realizado!");
      window.location.replace("./index.html");

    } catch (error) {
      console.error(error);
      alert("Erro ao conectar ao servidor");
    }

  });

}

// ===== RECUPERAÇÃO =====

const recuperarForm = document.getElementById("recuperarForm");

if (recuperarForm) {

recuperarForm.addEventListener(
"submit",
function(e){

e.preventDefault();

const email =document.getElementById("emailRecuperar").value;

// gerar token
const token =Math.random().toString(36).substring(2);

localStorage.setItem("resetToken",token);

// simulação email
alert(`Token enviado para ${email}(Token simulado: ${token})`);

});
}

// ===== VALIDAR SESSÃO =====

async function validarSessao(){

try {

const resposta =await fetch("./petmatch-api/validar.php",{credentials: "include"});

const data =await resposta.json();

if (!data.logado){ window.location.replace("./login.html"); }

} catch(error){

window.location.replace("./login.html");

}

}

async function logout(){

console.log("logout executado");

try {

const resposta =await fetch("./petmatch-api/logout.php", { method: "POST", credentials: "include" } );

const data =await resposta.json();

console.log(data);

window.location.replace("./login.html");

} catch(error){

console.error(
"Erro no logout:",
error
);

}

}

const btnLogout = document.getElementById("btnLogout");

if (btnLogout){

btnLogout.addEventListener("click",logout);

}