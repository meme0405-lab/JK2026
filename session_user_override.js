// Arquivo temporário para injetar usuário mock durante testes
window.usuarioAtual = window.usuarioAtual || {};
window.usuarioAtual.id = 9999;
window.usuarioAtual.nome = window.usuarioAtual.nome || 'Teste Usuário';
window.usuarioAtual.nickname = window.usuarioAtual.nickname || 'testeuser';
window.usuarioAtual.foto = 'images/avatar_drive_1.jpg';
window.usuarioAtual.rank = window.usuarioAtual.rank || 'Gold';
window.usuarioAtual.plataforma = window.usuarioAtual.plataforma || 'PC';
window.usuarioAtual.nivel = window.usuarioAtual.nivel || 5;

console.info('session_user_override: usuário mock injetado');

// Tenta aplicar imediatamente no DOM caso a página já esteja pronta
function _applyMockToDOM() {
	try {
		const name = window.usuarioAtual.nome || window.usuarioAtual.nickname || 'Usuário';
		const userNameEl = document.getElementById('userName');
		const userNameMini = document.getElementById('userNameMini');
		const userNicknameMini = document.getElementById('userNicknameMini');
		const avatarMini = document.getElementById('userAvatarMini');

		if (userNameEl) userNameEl.textContent = name;
		if (userNameMini) userNameMini.textContent = name;
		if (userNicknameMini) userNicknameMini.textContent = window.usuarioAtual.nickname ? `@${window.usuarioAtual.nickname}` : '';
		if (avatarMini) {
			if (avatarMini.tagName === 'IMG') {
				avatarMini.src = window.usuarioAtual.foto || 'images/avatar-default.svg';
				avatarMini.alt = name;
				avatarMini.onerror = function() { this.onerror=null; this.src='images/avatar-default.svg'; };
			} else {
				avatarMini.style.backgroundImage = `url('${window.usuarioAtual.foto || 'images/avatar-default.svg'}')`;
				avatarMini.style.backgroundSize = 'cover';
			}
		}
	} catch (e) {
		console.warn('session_user_override: erro ao aplicar no DOM', e);
	}
}

if (document.readyState === 'complete' || document.readyState === 'interactive') {
	_applyMockToDOM();
} else {
	document.addEventListener('DOMContentLoaded', _applyMockToDOM);
}

// Também tenta chamar carregarDashboard() se disponível
setTimeout(() => {
	if (typeof carregarDashboard === 'function') {
		try { carregarDashboard(); } catch(e) { console.warn('Erro ao chamar carregarDashboard():', e); }
	}
}, 150);
