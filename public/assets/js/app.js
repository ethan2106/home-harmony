/**
 * Harmony Pro - Logique Frontend
 * Gère les profils (avec cookies), les validations fluides et les filtres.
 */

let currentProfile = null;

// Au chargement, on vérifie si un profil était déjà enregistré
document.addEventListener('DOMContentLoaded', () => {
    const savedProfile = getCookie('harmony_last_profile');
    if (savedProfile) {
        try {
            const profileData = JSON.parse(savedProfile);
            selectProfile(profileData.name, profileData.color, false);
        } catch (e) {
            console.error("Erreur lors du chargement du profil sauvegardé", e);
        }
    }
});

/**
 * Sélectionne un profil utilisateur et l'enregistre dans un cookie
 */
function selectProfile(name, color, saveToCookie = true) {
    currentProfile = { name, color };
    
    if (saveToCookie) {
        setCookie('harmony_last_profile', JSON.stringify(currentProfile), 30);
    }

    // Nettoyage visuel des boutons
    document.querySelectorAll('.profile-btn').forEach(btn => {
        btn.classList.remove('ring-4', 'ring-offset-2', 'ring-indigo-500', 'scale-105');
    });
    
    // Activation du bouton sélectionné
    const activeBtn = document.getElementById('btn-' + name);
    if (activeBtn) {
        activeBtn.classList.add('ring-4', 'ring-offset-2', 'ring-indigo-500', 'scale-105');
    }
}

/**
 * Valide une tâche (Mode Lux) avec mise à jour temps réel et animations
 */
async function toggleTaskLux(taskId, element) {
    if (!currentProfile) {
        const selector = document.getElementById('profile-selector');
        if (selector) {
            selector.classList.add('ring-4', 'ring-red-400', 'animate-shake');
            showFlashMessage("Sélectionne ton profil d'abord ! 🧐", "error");
            setTimeout(() => selector.classList.remove('ring-4', 'ring-red-400', 'animate-shake'), 1000);
        }
        return;
    }

    // Feedback visuel immédiat
    element.classList.add('checked', 'bg-indigo-600');
    const svg = element.querySelector('svg');
    if(svg) svg.classList.remove('hidden');
    
    const card = element.closest('.glass-card');
    if (card) card.classList.add('opacity-50', 'scale-95');
    
    // Petit délai pour laisser l'animation de la checkbox se voir
    setTimeout(async () => {
        try {
            const response = await fetch('/api/update-task', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${taskId}&profil=${encodeURIComponent(currentProfile.name)}`
            });

            const data = await response.json();
            
            if (data.success) {
                // 1. Mise à jour fluide du compteur dans la sidebar
                const statElement = document.getElementById(`stat-${currentProfile.name}`);
                if (statElement) {
                    let currentCount = parseInt(statElement.textContent);
                    statElement.textContent = currentCount + 1;
                    statElement.classList.add('scale-125', 'text-indigo-600', 'font-black');
                    setTimeout(() => statElement.classList.remove('scale-125', 'text-indigo-600'), 500);
                }

                // 2. Animation de sortie de la carte
                card.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
                card.classList.add('translate-x-full', 'opacity-0');
                
                setTimeout(() => {
                    location.reload(); // On recharge pour mettre à jour les archives et les stats
                }, 500);
            }
        } catch (error) {
            console.error("Erreur lors de la mise à jour :", error);
            element.classList.remove('checked', 'bg-indigo-600');
            if (card) card.classList.remove('opacity-50', 'scale-95');
        }
    }, 600);
}

/**
 * Annule une tâche terminée
 */
async function undoTask(taskId, element) {
    if (!confirm("Voulez-vous annuler cette tâche et la remettre en 'À faire' ?")) return;

    element.classList.add('scale-95', 'opacity-50');
    
    try {
        const response = await fetch('/api/undo-task', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${taskId}` // L'absence de profil déclenche le 'undo'
        });

        if (response.ok) {
            location.reload();
        }
    } catch (error) {
        console.error("Erreur lors de l'annulation :", error);
        element.classList.remove('scale-95', 'opacity-50');
    }
}

/**
 * Filtre les tâches par catégorie
 */
function filterTasks(category, btn) {
    document.querySelectorAll('.filter-btn').forEach(b => {
        b.classList.remove('bg-slate-900', 'text-white', 'filter-btn-active');
        b.classList.add('bg-white/50', 'text-slate-600');
    });
    btn.classList.add('bg-slate-900', 'text-white', 'filter-btn-active');

    document.querySelectorAll('[data-category]').forEach(card => {
        if (category === 'all' || card.dataset.category === category) {
            card.style.display = 'flex';
            card.classList.add('animate-fade-in');
        } else {
            card.style.display = 'none';
        }
    });
}

/**
 * Utilitaires Cookies
 */
function setCookie(name, value, days) {
    const d = new Date();
    d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
    let expires = "expires=" + d.toUTCString();
    document.cookie = name + "=" + value + ";" + expires + ";path=/";
}

function getCookie(name) {
    let nameEQ = name + "=";
    let ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

function showFlashMessage(text, type) {
    const msg = document.createElement('div');
    msg.className = `fixed top-10 left-1/2 -translate-x-1/2 px-6 py-3 rounded-full text-white font-bold shadow-2xl z-50 transition-all duration-500 transform translate-y-0 ${type === 'error' ? 'bg-red-500' : 'bg-green-500'}`;
    msg.innerText = text;
    document.body.appendChild(msg);
    setTimeout(() => {
        msg.classList.add('opacity-0', '-translate-y-4');
        setTimeout(() => msg.remove(), 500);
    }, 2500);
}
