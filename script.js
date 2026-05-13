function normalizeText(text) {
    return text.toUpperCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .replace(/[^A-Z]/g, "");
}

function calculateNumerology(name) {
    const normalized = normalizeText(name);
    let sum = 0;
    
    // Calculer la somme des valeurs des lettres (A=1, B=2, ..., Z=26)
    for (let char of normalized) {
        sum += char.charCodeAt(0) - 64;
    }
    
    // Réduire à un seul chiffre
    while (sum > 9) {
        sum = sum.toString().split('').reduce((a, b) => parseInt(a) + parseInt(b), 0);
    }
    
    return sum;
}

function calculateCompatibility() {
    const boyName = document.getElementById('boyName').value.trim();
    const girlName = document.getElementById('girlName').value.trim();
    const resultDiv = document.getElementById('result');

    // Vérifier que les deux champs sont remplis
    if (!boyName || !girlName) {
        alert('⚠️ Veuillez remplir les deux prénoms !');
        return;
    }

    // Calculer les nombres numérologiques
    const boyNumber = calculateNumerology(boyName);
    const girlNumber = calculateNumerology(girlName);

    // Déterminer la parité
    const boyParity = boyNumber % 2 === 0 ? 'pair' : 'impair';
    const girlParity = girlNumber % 2 === 0 ? 'pair' : 'impair';

    // Vérifier la compatibilité
    const compatible = boyParity === girlParity;

    // Afficher le résultat
    resultDiv.style.display = 'block';
    resultDiv.className = 'result ' + (compatible ? 'compatible' : 'incompatible');

    if (compatible) {
        resultDiv.innerHTML = `
            <div class="result-icon heart">❤️</div>
            <h2>Vous êtes compatibles !</h2>
            <p style="font-size: 18px; margin: 10px 0;">
                ${boyName} et ${girlName} forment un couple harmonieux ! ✨
            </p>
            <div class="details">
                <div class="calculation">👨 ${boyName} : ${boyNumber} (${boyParity})</div>
                <div class="calculation">👩 ${girlName} : ${girlNumber} (${girlParity})</div>
                <div style="margin-top: 12px; font-weight: 600;">
                    ${boyParity.toUpperCase()} + ${girlParity.toUpperCase()} = COMPATIBLE 💕
                </div>
            </div>
        `;
    } else {
        resultDiv.innerHTML = `
            <div class="result-icon">💔</div>
            <h2>Vous êtes incompatibles</h2>
            <p style="font-size: 18px; margin: 10px 0;">
                ${boyName} et ${girlName} ont des énergies différentes
            </p>
            <div class="details">
                <div class="calculation">👨 ${boyName} : ${boyNumber} (${boyParity})</div>
                <div class="calculation">👩 ${girlName} : ${girlNumber} (${girlParity})</div>
                <div style="margin-top: 12px; font-weight: 600;">
                    ${boyParity.toUpperCase()} + ${girlParity.toUpperCase()} = INCOMPATIBLE ❌
                </div>
            </div>
        `;
    }

    resultDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

// Permettre de valider avec la touche Entrée
document.getElementById('boyName').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') calculateCompatibility();
});

document.getElementById('girlName').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') calculateCompatibility();
});