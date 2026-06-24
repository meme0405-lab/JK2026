# 🔗 API de Chat - Integração Backend

## 📋 Visão Geral

Este documento descreve como integrar o frontend de chat com um backend PHP/MySQL para sincronização completa de mensagens em tempo real.

---

## 📊 Estrutura de Dados

### Tabela: `mensagens`

```sql
CREATE TABLE mensagens (
    id INT PRIMARY KEY AUTO_INCREMENT,
    match_id INT NOT NULL,
    remetente_id INT NOT NULL,
    tipo ENUM('texto', 'audio', 'video_call') NOT NULL DEFAULT 'texto',
    conteudo LONGTEXT NOT NULL,
    duracao_segundos INT DEFAULT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    lido BOOLEAN DEFAULT FALSE,
    lido_em TIMESTAMP NULL,
    
    FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
    FOREIGN KEY (remetente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_match (match_id),
    INDEX idx_criado (criado_em)
);
```

### Tabela: `chamadas_video`

```sql
CREATE TABLE chamadas_video (
    id INT PRIMARY KEY AUTO_INCREMENT,
    match_id INT NOT NULL,
    iniciador_id INT NOT NULL,
    duracao_segundos INT DEFAULT 0,
    iniciada_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    finalizada_em TIMESTAMP NULL,
    status ENUM('ativa', 'encerrada', 'recusada') DEFAULT 'ativa',
    
    FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
    FOREIGN KEY (iniciador_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_match (match_id)
);
```

---

## 🔌 Endpoints da API

### 1. Enviar Mensagem de Texto

**POST** `/api/mensagens/enviar`

**Request:**
```json
{
  "match_id": 1,
  "tipo": "texto",
  "conteudo": "Olá, tudo bem?"
}
```

**Response (200 OK):**
```json
{
  "sucesso": true,
  "mensagem": "Mensagem enviada com sucesso",
  "dados": {
    "id": 123,
    "match_id": 1,
    "remetente_id": 1,
    "tipo": "texto",
    "conteudo": "Olá, tudo bem?",
    "criado_em": "2024-01-20T15:30:00Z",
    "lido": false
  }
}
```

**Errors:**
```json
{
  "sucesso": false,
  "erro": "Match não encontrado"
}
```

---

### 2. Enviar Áudio

**POST** `/api/mensagens/enviar-audio`

**Request (multipart/form-data):**
```
match_id: 1
audio: [arquivo WAV/MP3]
duracao_segundos: 15
```

**Response (200 OK):**
```json
{
  "sucesso": true,
  "mensagem": "Áudio enviado com sucesso",
  "dados": {
    "id": 124,
    "match_id": 1,
    "remetente_id": 1,
    "tipo": "audio",
    "conteudo": "/uploads/audio/msg_124.webm",
    "duracao_segundos": 15,
    "criado_em": "2024-01-20T15:30:15Z"
  }
}
```

---

### 3. Buscar Histórico de Mensagens

**GET** `/api/mensagens/historico/MATCH_ID`

**Query Parameters:**
```
?pagina=1
?limite=50
?desde=2024-01-20T00:00:00Z
```

**Response (200 OK):**
```json
{
  "sucesso": true,
  "dados": {
    "total": 150,
    "pagina": 1,
    "mensagens": [
      {
        "id": 1,
        "match_id": 1,
        "remetente_id": 1,
        "remetente_nome": "João",
        "tipo": "texto",
        "conteudo": "Olá!",
        "criado_em": "2024-01-20T15:20:00Z",
        "lido": true
      },
      {
        "id": 2,
        "match_id": 1,
        "remetente_id": 2,
        "remetente_nome": "Maria",
        "tipo": "texto",
        "conteudo": "Oi! Como vai?",
        "criado_em": "2024-01-20T15:21:00Z",
        "lido": true
      }
    ]
  }
}
```

---

### 4. Marcar Mensagens como Lidas

**PUT** `/api/mensagens/marcar-lidas`

**Request:**
```json
{
  "match_id": 1,
  "mensagem_ids": [1, 2, 3, 4, 5]
}
```

**Response (200 OK):**
```json
{
  "sucesso": true,
  "mensagem": "Mensagens marcadas como lidas"
}
```

---

### 5. Iniciar Chamada de Vídeo

**POST** `/api/chamadas/iniciar`

**Request:**
```json
{
  "match_id": 1,
  "offer_sdp": "v=0\r\no=- ..."
}
```

**Response (200 OK):**
```json
{
  "sucesso": true,
  "dados": {
    "chamada_id": 42,
    "match_id": 1,
    "iniciador_id": 1,
    "iniciada_em": "2024-01-20T15:30:00Z",
    "status": "ativa"
  }
}
```

---

### 6. Responder Chamada de Vídeo

**POST** `/api/chamadas/responder`

**Request:**
```json
{
  "chamada_id": 42,
  "answer_sdp": "v=0\r\no=- ..."
}
```

**Response (200 OK):**
```json
{
  "sucesso": true,
  "mensagem": "Chamada conectada com sucesso"
}
```

---

### 7. Encerrar Chamada

**POST** `/api/chamadas/encerrar`

**Request:**
```json
{
  "chamada_id": 42,
  "duracao_segundos": 180
}
```

**Response (200 OK):**
```json
{
  "sucesso": true,
  "mensagem": "Chamada encerrada",
  "dados": {
    "chamada_id": 42,
    "status": "encerrada",
    "duracao_segundos": 180
  }
}
```

---

## 🔄 Integração com WebSocket (Real-time)

### Configurar Socket.IO

**Instalação:**
```bash
npm install socket.io socket.io-client
```

**Servidor (Node.js com Express):**
```javascript
const io = require('socket.io')(server);

io.on('connection', (socket) => {
    console.log('Cliente conectado:', socket.id);

    // Juntar sala do match
    socket.on('join-match', (matchId) => {
        socket.join(`match_${matchId}`);
    });

    // Receber mensagem
    socket.on('send-message', (data) => {
        io.to(`match_${data.matchId}`).emit('new-message', {
            id: Date.now(),
            ...data,
            criado_em: new Date()
        });
    });

    // Iniciar chamada
    socket.on('start-call', (data) => {
        io.to(`match_${data.matchId}`).emit('incoming-call', {
            chamada_id: data.chamada_id,
            iniciador_id: data.iniciador_id,
            offer_sdp: data.offer_sdp
        });
    });

    // ICE Candidate
    socket.on('ice-candidate', (data) => {
        io.to(`match_${data.matchId}`).emit('ice-candidate', data);
    });
});
```

**Cliente (JavaScript):**
```javascript
const socket = io('http://seu-servidor.com');

// Conectar ao match
socket.emit('join-match', matchId);

// Ouvir novas mensagens
socket.on('new-message', (mensagem) => {
    exibirMensagem(mensagem);
});

// Enviar mensagem pelo socket
socket.emit('send-message', {
    matchId: 1,
    remetente_id: usuarioAtual.id,
    tipo: 'texto',
    conteudo: 'Olá!'
});
```

---

## 🔐 Autenticação e Permissões

### Headers Necessários

```javascript
fetch('/api/mensagens/enviar', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + sessionStorage.getItem('token'),
        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        match_id: 1,
        tipo: 'texto',
        conteudo: 'Mensagem'
    })
});
```

### Validações no Backend

```php
<?php

function verificarPermissaoChat($usuarioId, $matchId) {
    $conn = obterConexao();
    
    // Verificar se o usuário está envolvido no match
    $query = "SELECT * FROM matches 
              WHERE id = ? 
              AND (usuario1_id = ? OR usuario2_id = ?)
              AND status = 'aceito'";
    
    $stmt = $conn->prepare($query);
    $stmt->execute([$matchId, $usuarioId, $usuarioId]);
    
    return $stmt->rowCount() > 0;
}

// Usar na função de enviar mensagem
if (!verificarPermissaoChat($usuarioId, $_POST['match_id'])) {
    responder_json('erro', 'Acesso negado', []);
    exit;
}

?>
```

---

## 📱 Implementação Frontend

### Modificar chat.js para usar API

```javascript
// Função atualizada para enviar com backend
async function enviarMensagem() {
    const input = document.getElementById('messageInput');
    const texto = input.value.trim();

    if (!texto) return;

    try {
        // Enviar para backend
        const response = await fetch('/api/mensagens/enviar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + obterToken()
            },
            body: JSON.stringify({
                match_id: matchId,
                tipo: 'texto',
                conteudo: texto
            })
        });

        const resultado = await response.json();

        if (resultado.sucesso) {
            exibirMensagem(resultado.dados);
            input.value = '';
            input.focus();
            salvarHistorico();
        } else {
            mostrarMensagem('❌ Erro: ' + resultado.erro, 'error');
        }
    } catch (erro) {
        console.error('Erro ao enviar:', erro);
        // Fallback para localStorage se offline
        enviarMensagemLocal(texto);
    }
}

// Carregar histórico do backend
async function carregarHistoricoMensagens() {
    try {
        const response = await fetch(`/api/mensagens/historico/${matchId}?limite=50`, {
            headers: {
                'Authorization': 'Bearer ' + obterToken()
            }
        });

        const resultado = await response.json();

        if (resultado.sucesso) {
            mensagens = resultado.dados.mensagens;
            mensagens.forEach(msg => exibirMensagem(msg));
        }
    } catch (erro) {
        console.error('Erro ao carregar histórico:', erro);
        // Fallback para localStorage
        carregarHistoricoLocal();
    }
}
```

---

## 🚀 Deploy em Produção

### Requisitos

- Node.js 14+
- SSL/HTTPS
- Certificado válido
- STUN/TURN servers públicos

### STUN/TURN Gratuitos

```javascript
const rtcConfig = {
    iceServers: [
        // Servidores públicos gratuitos
        { urls: ['stun:stun.l.google.com:19302'] },
        { urls: ['stun:stun1.l.google.com:19302'] },
        { urls: ['stun:stun2.l.google.com:19302'] },
        { urls: ['stun:stun.stunprotocol.org:3478'] }
    ]
};
```

### TURN Pago (Recomendado)

```javascript
const rtcConfig = {
    iceServers: [
        {
            urls: ['turn:seu-servidor-turn.com:3478'],
            username: 'seu-usuario',
            credential: 'sua-senha'
        }
    ]
};
```

---

## 📊 Otimizações

### Compressão de Áudio

```javascript
// Converter para formato otimizado
async function comprimirAudio(audioBlob) {
    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
    const arrayBuffer = await audioBlob.arrayBuffer();
    const audioBuffer = await audioContext.decodeAudioData(arrayBuffer);
    
    // Converter para Opus (compressão melhor)
    // Usar biblioteca: libopus.js
    return compressedBlob;
}
```

### Paginação

```javascript
// Carregar histórico em chunks
async function carregarMaisAntigos() {
    const pagina = Math.floor(mensagens.length / 50) + 1;
    const response = await fetch(
        `/api/mensagens/historico/${matchId}?pagina=${pagina}&limite=50`
    );
    // ...
}
```

### Sincronização Offline

```javascript
// Fila de mensagens offline
let filaOffline = [];

async function enviarMensagemComFallback(texto) {
    try {
        // Tentar enviar online
        await enviarMensagemOnline(texto);
    } catch {
        // Se offline, adicionar à fila
        filaOffline.push({
            texto,
            timestamp: Date.now()
        });
        
        // Exibir localmente
        exibirMensagemLocal(texto);
        
        // Sincronizar quando voltar online
        window.addEventListener('online', sincronizarFila);
    }
}

async function sincronizarFila() {
    for (let msg of filaOffline) {
        await enviarMensagemOnline(msg.texto);
    }
    filaOffline = [];
}
```

---

## ✅ Checklist de Implementação

- [ ] Criar tabelas no banco
- [ ] Implementar endpoints da API
- [ ] Adicionar autenticação
- [ ] Integrar Socket.IO
- [ ] Testes de stress (1000+ conexões)
- [ ] Implementar rate limiting
- [ ] Adicionar logs
- [ ] Backup automático
- [ ] Monitoramento em produção
- [ ] Documentação de deployment

---

## 📞 Suporte Técnico

Para questões de integração:

1. Verifique os logs: `logs/chat.log`
2. Teste endpoints com Postman
3. Verifique console do navegador (F12)
4. Ative debug mode: `APP_DEBUG=true`

---

**Última atualização: Janeiro 2024**
