let currentProfile = null;

function selectProfile(name, color) {
    currentProfile = { name, color };
    // Nettoyage des styles de sélection
    document.querySelectorAll('.profile-btn').forEach(btn => {
        btn.classList.remove('ring-4', 'ring-offset-2', 'ring-indigo-500', 'scale-105');
    });
    // Activation visuelle
    const activeBtn = document.getElementById('btn-' + name);
    if (activeBtn) {
        activeBtn.classList.add('ring-4', 'ring-offset-2', 'ring-indigo-500', 'scale-105');
    }
}

async function toggleTaskLux(taskId, element) {
    if (!currentProfile) {
        // Animation d'alerte sur le sélecteur de profil
        const selector = document.getElementById('profile-selector');
        if (selector) {
            selector.classList.add('ring-4', 'ring-red-400', 'animate-shake');
            console.warn("Action bloquée : Aucun profil sélectionné.");
            setTimeout(() => selector.classList.remove('ring-4', 'ring-red-400', 'animate-shake'), 1000);
        }
        return;
    }



    // Animation visuelle de validation
    element.classList.add('checked', 'bg-indigo-600');
    const svg = element.querySelector('svg');
    if(svg) svg.classList.remove('hidden');
    
    const card = element.closest('.glass-card');
    if (card) card.classList.add('opacity-50', 'scale-95');
    
    // On attend la fin de l'animation CSS avant de recharger
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
            element.classList.remove('checked', 'bg-indigo-600'); // Reset en cas d'erreur
        }
    }, 600);
}

async function undoTask(taskId, element) {
    if (!confirm("Voulez-vous annuler cette tâche et la remettre en 'À faire' ?")) return;

    element.classList.add('scale-95', 'opacity-50');
    
    try {
        const response = await fetch('update_task.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${taskId}` // Pas besoin de profil pour annuler
        });

        if (response.ok) {
            location.reload();
        }
    } catch (error) {
        console.error("Erreur lors de l'annulation :", error);
        element.classList.remove('scale-95', 'opacity-50');
    }
}

function filterTasks(category, btn) {

    // Mise à jour visuelle des boutons de filtre
    document.querySelectorAll('.filter-btn').forEach(b => {
        b.classList.remove('bg-slate-900', 'text-white', 'filter-btn-active');
        b.classList.add('bg-white/50', 'text-slate-600');
    });
    btn.classList.add('bg-slate-900', 'text-white', 'filter-btn-active');

    // Filtrage réel des cartes
    document.querySelectorAll('[data-category]').forEach(card => {
        if (category === 'all' || card.dataset.category === category) {
            card.style.display = 'flex';
            card.classList.add('animate-fade-in'); // Petite animation si définie en CSS
        } else {
            card.style.display = 'none';
        }
    });
}
