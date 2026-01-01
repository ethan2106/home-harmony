/**
 * Harmony Pro - Logique Frontend
 * Gère les profils, les validations et les filtres.
 */

let currentProfile = null;

/**
 * Sélectionne un profil utilisateur
 */
function selectProfile(name, color) {
    currentProfile = { name, color };
    
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
 * Valide une tâche (Mode Lux)
 */
async function toggleTaskLux(taskId, element) {
    if (!currentProfile) {
        const selector = document.getElementById('profile-selector');
        if (selector) {
            selector.classList.add('ring-4', 'ring-red-400', 'animate-shake');
            console.warn("Action bloquée : Aucun profil sélectionné.");
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
    
    setTimeout(async () => {
        try {
            const response = await fetch('update_task.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${taskId}&profil=${encodeURIComponent(currentProfile.name)}`
            });

            if (response.ok) {
                location.reload();
            }
        } catch (error) {
            console.error("Erreur lors de la mise à jour :", error);
            element.classList.remove('checked', 'bg-indigo-600');
        }
    }, 600);
}

/**
 * Annule une tâche terminée et la remet en "À faire"
 */
async function undoTask(taskId, element) {
    if (!confirm("Voulez-vous annuler cette tâche et la remettre en 'À faire' ?")) return;

    element.classList.add('scale-95', 'opacity-50');
    
    try {
        const response = await fetch('update_task.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${taskId}` // L'absence de profil déclenche le 'undo' en PHP
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
 * Filtre les tâches par catégorie (Maison, Jardin, Voiture)
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
