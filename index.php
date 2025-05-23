<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Akoko ti Esu</title>
  <link rel='icon' href='Imagens/Ferpinha.ico' type='image/png'>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: Arial, sans-serif;
    }

    body {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background-color: #000;
      color: #fff;
    }

    .container {
      text-align: center;
      background-color: #151515;
      padding: 13px 23px;
      border: 3px solid #888;
      border-radius: 10px;
      box-shadow: 4px 4px 3px #4448;
      width: 100%;
      max-width: 1100px;
    }

    h1 {
      margin-bottom: 10px;
      color: #bbb;
    }

    #relogio {
      font-size: 1.0rem;
      margin-bottom: 13px;
      color: #f22;
    }

    input, button {
      padding: 10px;
      font-size: 1rem;
      text-align: center;
      margin-bottom: 10px;
      border: 1px solid #ccc;
      border-radius: 13px;
      width: 80%;
      text-shadow: 0 0 1px #0008;
    }

    button {
      background-color: #0057ff;
      color: #fff;
      cursor: pointer;
      transition: background-color 0.3s;
      border: none;
      border-radius: 23px;
      width: 50%;
      box-shadow: 2px 2px 3px #0008;
    }

    button:hover {
      background-color: #003bb5;
    }

    button:disabled {
      background-color: #ccc;
      color: #c99;
      cursor: not-allowed;
    }

    ul {
      list-style: none;
      padding: 0;
      margin-top: 20px;
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 5px;
    }

    li {
      margin-bottom: 5px;
      background-color: #333;
      text-shadow: 2px 2px 1px #0008;
      padding: 13px;
      border-radius: 13px;
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      box-shadow: 2px 2px 3px #0008;
    }

    .remover {
      background-color: #ff2222;
      border: none;
      padding: 5px;
      border-radius: 13px;
      color: #fff;
      text-shadow: 0 3px 1px #0008;
      cursor: pointer;
      margin: auto;
      align-self: flex-end;
    }
    .remover:hover {
      background-color: #dd5555;
      border: none;
      padding: 5px;
      border-radius: 13px;
      color: #fff;
      cursor: pointer;
      margin: auto;
      align-self: flex-end;
    }

    body.alarm-triggered {
      background-color: #ff0000;
      animation: shake 0.5s infinite;
    }

    @keyframes shake {
      0% { transform: translate(1px, 1px) rotate(0deg); }
      10% { transform: translate(-1px, -2px) rotate(-1deg); }
      20% { transform: translate(-3px, 0px) rotate(1deg); }
      30% { transform: translate(3px, 2px) rotate(0deg); }
      40% { transform: translate(1px, -1px) rotate(1deg); }
      50% { transform: translate(-1px, 2px) rotate(-1deg); }
      60% { transform: translate(-3px, 1px) rotate(0deg); }
      70% { transform: translate(3px, 1px) rotate(-1deg); }
      80% { transform: translate(-1px, -1px) rotate(1deg); }
      90% { transform: translate(1px, 2px) rotate(0deg); }
      100% { transform: translate(1px, -2px) rotate(-1deg); }
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Akoko ti Esu</h1>
    <div id="relogio">00:00</div>
    <input type="time" id="horaAlarme" required />
    <br>
    <button id="configurarAlarme">Adicionar Alarme</button>
    <br>
    <button id="pararAlarme" disabled>Parar Alarme</button>
    <br>
    <ul id="listaAlarmes"></ul>
    <audio id="somAlarme" src="sons/alarme.mp3" loop></audio>
  </div>

  <script>
    const audio = document.getElementById("somAlarme");
    const btnParar = document.getElementById("pararAlarme");
    const btnConfigurar = document.getElementById("configurarAlarme");
    const inputHoraAlarme = document.getElementById("horaAlarme");
    const listaAlarmes = document.getElementById("listaAlarmes");
    const relogio = document.getElementById("relogio");

    let alarmes = JSON.parse(localStorage.getItem("alarmes")) || [];
    let alarmeTocado = false;
    let ultimaHoraTocada = null;

    function atualizarRelogio() {
      const agora = new Date();
      const horas = agora.getHours().toString().padStart(2, '0');
      const minutos = agora.getMinutes().toString().padStart(2, '0');
      const segundos = agora.getSeconds().toString().padStart(2, '0');
      relogio.textContent = `${horas}:${minutos}:${segundos}`;
      return `${horas}:${minutos}`;
    }

    function tocarAlarme() {
      if (!alarmeTocado) {
        audio.play();
        btnParar.disabled = false;
        alarmeTocado = true;
        document.body.classList.add('alarm-triggered');
      }
    }

    function pararAlarme() {
      audio.pause();
      audio.currentTime = 0;
      btnParar.disabled = true;
      alarmeTocado = false;
      document.body.classList.remove('alarm-triggered');
    }

    function renderizarAlarmes() {
      listaAlarmes.innerHTML = "";
      alarmes.forEach((hora, index) => {
        const li = document.createElement("li");
        li.textContent = hora;
        const btn = document.createElement("button");
        btn.textContent = "🗑️";
        btn.className = "remover";
        btn.onclick = () => {
          alarmes.splice(index, 1);
          salvarAlarmes();
          renderizarAlarmes();
        };
        li.appendChild(btn);
        listaAlarmes.appendChild(li);
      });
    }

    function salvarAlarmes() {
      localStorage.setItem("alarmes", JSON.stringify(alarmes));
    }

    btnConfigurar.addEventListener("click", () => {
      const hora = inputHoraAlarme.value;
      if (hora && !alarmes.includes(hora)) {
        alarmes.push(hora);
        salvarAlarmes();
        renderizarAlarmes();
        inputHoraAlarme.value = "";
        alert(`Alarme configurado para ${hora}`);
      } else {
        alert("Horário inválido ou já adicionado.");
      }
    });

    btnParar.addEventListener("click", pararAlarme);

    setInterval(() => {
      const horaAtual = atualizarRelogio();

      if (alarmes.includes(horaAtual) && horaAtual !== ultimaHoraTocada && !alarmeTocado) {
        tocarAlarme();
        ultimaHoraTocada = horaAtual;
      }

      // Permite que o alarme toque novamente no próximo dia ou minuto, caso tenha passado
      if (horaAtual !== ultimaHoraTocada && alarmeTocado) {
        alarmeTocado = false;
      }

    }, 1000);

    // Inicializa a lista de alarmes ao carregar a página
    renderizarAlarmes();
    atualizarRelogio();
  </script>
</body>
</html>
