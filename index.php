<?php
/**
 * Application de Gestion Ménagère - Design "Ultra Modern Glass"
 * Projet : Entretien Maison & Jardin
 * Date : 01 Janvier 2026
 */

// --- 1. LOGIQUE DE RESET JOURNALIER (LAZY CRON) ---
$lastResetFile = 'last_reset.txt';
$today = date('Y-m-d');
$lastReset = file_exists($lastResetFile) ? file_get_contents($lastResetFile) : '';

if ($lastReset !== $today) {
    $tasksData = file_exists('tasks.json') ? json_decode(file_get_contents('tasks.json'), true) : [];
    if (is_array($tasksData)) {
        foreach ($tasksData as &$task) {
            if (isset($task['date_prevue'])) { $task['date_prevue'] = null; }
        }
        file_put_contents('tasks.json', json_encode($tasksData, JSON_PRETTY_PRINT));
    }
    file_put_contents($lastResetFile, $today);
}

// --- 2. CHARGEMENT DES DONNÉES ---
$profiles = json_decode(file_get_contents("profiles.json"), true) ?? [];
$tasks = json_decode(file_get_contents("tasks.json"), true) ?? [];
$rooms = json_decode(file_get_contents("rooms.json"), true) ?? [];

// --- 3. LOGIQUE MÉTIER ---
function isTaskDue($task) {
    if (empty($task['dernier_fait'])) return true;
    $lastDone = new DateTime($task['dernier_fait']);
    $now = new DateTime(date('Y-m-d'));
    $diff = $now->diff($lastDone)->days;
    switch ($task['frequence']) {
        case 'Quotidien': return $diff >= 1;
        case 'Hebdomadaire': return $diff >= 7;
        case 'Mensuel': return $diff >= 30;
        case 'Saisonnier': return $diff >= 90;
        default: return true;
    }
}

// --- 4. STATISTIQUES ---
$history = file_exists('history.json') ? json_decode(file_get_contents('history.json'), true) : [];
$stats = [];
$currentMonth = date('Y-m');
foreach ($profiles as $p) { $stats[$p['nom']] = 0; }
if (is_array($history)) {
    foreach ($history as $entry) {
        if (isset($entry['date']) && strpos($entry['date'], $currentMonth) === 0) {
            if (isset($stats[$entry['profil']])) { $stats[$entry['profil']]++; }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Harmony - Premium Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --glass-bg: rgba(255, 255, 255, 0.75);
            --glass-border: rgba(203, 213, 225, 0.6); /* Slate 300 plus visible */
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(-45deg, #f1f5f9, #cbd5e1, #f8fafc, #94a3b8);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            min-height: 100vh;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .glass-panel {
            background: var(--glass-bg);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid var(--glass-border);
            box-shadow: 0 8px 32px 0 rgba(15, 23, 42, 0.08);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.6);
            border: 1.5px solid var(--glass-border); /* Bordure renforcée */
            transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        }

        .glass-card:hover {
            background: rgba(255, 255, 255, 0.9);
            transform: translateY(-4px);
            border-color: rgba(99, 102, 241, 0.4);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08);
        }

        .profile-btn {
            position: relative;
            overflow: hidden;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .profile-active {
            transform: scale(1.05);
            z-index: 10;
            box-shadow: 0 0 0 4px white, 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        /* Checkbox Custom Lux - Visibilité Améliorée */
        .lux-checkbox {
            width: 44px; /* Plus grande */
            height: 44px;
            border-radius: 14px;
            background: #f8fafc; /* Fond clair solide pour contraste */
            border: 2px solid #cbd5e1; /* Bordure visible */
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .lux-checkbox:hover {
            border-color: #6366f1;
            background: white;
            transform: scale(1.1);
        }

        .lux-checkbox.checked {
            background: #6366f1;
            border-color: #4f46e5;
            transform: rotate(10deg) scale(1.1);
            box-shadow: 0 8px 15px rgba(99, 102, 241, 0.3);
        }

        .lux-checkbox svg {
            display: none;
            width: 24px;
            height: 24px;
            color: white;
            stroke-width: 4;
        }

        .lux-checkbox.checked svg {
            display: block;
        }

        .filter-btn-active {
            background: #0f172a !important;
            color: white !important;
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.2);
        }

        .no-scrollbar::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="pb-16 px-4 md:px-8">

    <div class="max-w-7xl mx-auto">
        
        <!-- TOP NAVIGATION BAR -->
        <nav class="flex flex-col md:flex-row justify-between items-center py-10 gap-8">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-indigo-600 rounded-3xl flex items-center justify-center text-3xl shadow-2xl shadow-indigo-200 rotate-3">
                    ✨
                </div>
                <div>
                    <h1 class="text-4xl font-extrabold text-slate-900 tracking-tighter">Harmony</h1>
                    <p class="text-indigo-600 font-bold text-sm tracking-widest uppercase"><?php echo date("l d F"); ?></p>
                </div>
            </div>

            <div id="profile-selector" class="flex items-center gap-3 glass-panel p-3 rounded-[2rem]">
                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest px-3">Qui travaille ?</span>
                <div class="flex gap-2">
                <?php foreach ($profiles as $profile): ?>
                    <button 
                        onclick="selectProfile('<?php echo $profile['nom']; ?>', '<?php echo $profile['couleur']; ?>')"
                        id="btn-<?php echo $profile['nom']; ?>"
                        class="profile-btn h-12 px-6 rounded-2xl text-white font-bold text-sm shadow-md transition-all bg-<?php echo $profile['couleur']; ?>"
                    >
                        <?php echo $profile['nom']; ?>
                    </button>
                <?php endforeach; ?>
                </div>
            </div>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- LEFT COLUMN: STATS & CATEGORIES -->
            <div class="lg:col-span-3 space-y-8">
                <!-- LEADERBOARD -->
                <div class="glass-panel p-6 rounded-[2.5rem]">
                    <h3 class="text-xs font-black text-slate-500 uppercase tracking-[0.2em] mb-6">Tableau d'Honneur</h3>
                    <div class="space-y-4">
                        <?php foreach ($profiles as $profile): ?>
                            <div class="flex items-center justify-between p-3 rounded-2xl hover:bg-white/60 transition-colors border border-transparent hover:border-slate-200">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-<?php echo $profile['couleur']; ?> flex items-center justify-center text-sm text-white font-bold shadow-lg">
                                        <?php echo substr($profile['nom'], 0, 1); ?>
                                    </div>
                                    <span class="font-bold text-slate-700"><?php echo $profile['nom']; ?></span>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="text-xl font-black text-slate-900"><?php echo $stats[$profile['nom']] ?? 0; ?></span>
                                    <span class="text-[8px] font-bold text-slate-400 uppercase tracking-tighter">tâches</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- MENU -->
                <div class="glass-panel p-6 rounded-[2.5rem]">
                    <h3 class="text-xs font-black text-slate-500 uppercase tracking-[0.2em] mb-6">Zones</h3>
                    <div class="grid grid-cols-1 gap-3">
                        <button onclick="filterTasks('all', this)" class="filter-btn filter-btn-active group flex items-center justify-between p-4 rounded-2xl bg-white/50 text-slate-600 transition-all border border-slate-200">
                            <span class="font-bold">🌍 Toutes zones</span>
                            <span class="bg-white/40 px-2 py-0.5 rounded-lg text-[10px] font-black border border-white/50">ALL</span>
                        </button>
                        <button onclick="filterTasks('maison', this)" class="filter-btn group flex items-center justify-between p-4 rounded-2xl bg-white/50 text-slate-600 hover:bg-white transition-all border border-slate-200">
                            <span class="font-bold">🏠 Maison</span>
                        </button>
                        <button onclick="filterTasks('jardin', this)" class="filter-btn group flex items-center justify-between p-4 rounded-2xl bg-white/50 text-slate-600 hover:bg-white transition-all border border-slate-200">
                            <span class="font-bold">🌳 Jardin</span>
                        </button>
                        <button onclick="filterTasks('voiture', this)" class="filter-btn group flex items-center justify-between p-4 rounded-2xl bg-white/50 text-slate-600 hover:bg-white transition-all border border-slate-200">
                            <span class="font-bold">🚗 Voiture</span>
                        </button>
                    </div>
                </div>

                <a href="admin.php" class="flex items-center justify-center p-6 rounded-[2.5rem] bg-indigo-50 text-indigo-700 font-black text-xs uppercase tracking-[0.2em] hover:bg-indigo-600 hover:text-white transition-all border-2 border-indigo-100/50">
                    Paramètres ⚙️
                </a>
            </div>

            <!-- RIGHT COLUMN: MAIN CONTENT -->
            <div class="lg:col-span-9 space-y-10">
                
                <!-- HEADER SEPARATEUR -->
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 px-4">
                    <div>
                        <h2 class="text-5xl font-black text-slate-900 tracking-tight">Objectifs du jour</h2>
                        <p class="text-slate-600 font-semibold mt-2 text-lg">Il reste encore du travail 🤣</p>
                    </div>
                </div>

                <!-- TODO GRID -->
                <div id="todo-container" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php 
                    $todoTasks = array_filter($tasks, function($t) use ($today) {
                        return ($t['dernier_fait'] ?? '') !== $today && isTaskDue($t);
                    });

                    if (empty($todoTasks)): ?>
                        <div class="col-span-full glass-panel py-24 text-center rounded-[4rem] border-dashed border-3 border-indigo-200">
                            <div class="text-7xl mb-8">🥂</div>
                            <h3 class="text-3xl font-black text-slate-800">C'est l'heure de l'apéro !</h3>
                            <p class="text-slate-600 font-bold text-lg">La maison brille, tout est à jour.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($todoTasks as $task): 
                            $room = array_values(array_filter($rooms, fn($r) => $r['id'] == ($task['room_id'] ?? 0)))[0] ?? null;
                            $cat = ($task['room_id'] === 'jardin') ? 'jardin' : (($task['room_id'] === 'voiture') ? 'voiture' : 'maison');
                        ?>
                            <div class="glass-card p-8 rounded-[3rem] flex flex-col justify-between gap-8 group" data-category="<?php echo $cat; ?>">
                                <div class="flex justify-between items-center">
                                    <div class="flex gap-5">
                                        <div class="w-16 h-16 rounded-[1.5rem] bg-white shadow-xl flex items-center justify-center text-3xl group-hover:scale-110 transition-transform border border-slate-100">
                                            <?php echo $room['emoji'] ?? '📋'; ?>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-3 mb-1">
                                                <span class="px-2.5 py-1 bg-slate-900 text-[10px] font-black text-white uppercase rounded-lg tracking-widest">
                                                    <?php echo $task['frequence']; ?>
                                                </span>
                                                <span class="text-[11px] font-black text-indigo-600 uppercase tracking-widest">
                                                    <?php echo $room['nom'] ?? 'Général'; ?>
                                                </span>
                                            </div>
                                            <h3 class="text-xl font-extrabold text-slate-800 group-hover:text-indigo-600 transition-colors">
                                                <?php echo $task['titre']; ?>
                                            </h3>
                                        </div>
                                    </div>
                                    
                                    <!-- Case à cocher très visible -->
                                    <div onclick="toggleTaskLux(<?php echo $task['id']; ?>, this)" class="lux-checkbox">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between pt-4 border-t border-slate-100/50">
                                    <div class="text-xs font-black text-slate-400 flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.5)]"></span> DISPONIBLE
                                    </div>
                                    <div class="text-[10px] font-black text-slate-300 tracking-[0.2em] uppercase">Ready to done</div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- COMPLETED SECTION -->
                <?php $doneTasks = array_filter($tasks, function($t) use ($today) { return ($t['dernier_fait'] ?? '') === $today; }); ?>
                <?php if (!empty($doneTasks)): ?>
                <section class="mt-24">
                    <div class="flex items-center gap-6 mb-10">
                        <span class="h-0.5 bg-slate-300/30 grow rounded-full"></span>
                        <h2 class="text-sm font-black text-slate-500 uppercase tracking-[0.5em] whitespace-nowrap">Archives du succès</h2>
                        <span class="h-0.5 bg-slate-300/30 grow rounded-full"></span>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($doneTasks as $task): 
                            $profile = array_values(array_filter($profiles, fn($p) => $p['nom'] === $task['fait_par']))[0] ?? null;
                        ?>
                            <div class="p-5 bg-white/60 border-2 border-white rounded-[2rem] flex items-center gap-5 opacity-90 backdrop-blur-md shadow-sm">
                                <div class="w-12 h-12 rounded-2xl bg-<?php echo $profile['couleur'] ?? 'slate-400'; ?> flex items-center justify-center text-white text-sm font-black shadow-lg shrink-0">
                                    <?php echo substr($task['fait_par'], 0, 1); ?>
                                </div>
                                <div class="truncate">
                                    <h4 class="text-base font-bold text-slate-400 line-through truncate"><?php echo $task['titre']; ?></h4>
                                    <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest">Gagné par <?php echo $task['fait_par']; ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        let currentProfile = null;

        function selectProfile(name, color) {
            currentProfile = { name, color };
            document.querySelectorAll('.profile-btn').forEach(btn => {
                btn.classList.remove('profile-active');
            });
            const activeBtn = document.getElementById('btn-' + name);
            activeBtn.classList.add('profile-active');
        }

        async function toggleTaskLux(taskId, element) {
            if (!currentProfile) {
                // Shake effect on profile selector
                const selector = document.getElementById('profile-selector');
                selector.classList.add('ring-4', 'ring-red-400');
                setTimeout(() => selector.classList.remove('ring-4', 'ring-red-400'), 1000);
                
                selector.animate([
                    { transform: 'translateX(0)' },
                    { transform: 'translateX(-10px)' },
                    { transform: 'translateX(10px)' },
                    { transform: 'translateX(0)' }
                ], { duration: 250, iterations: 3 });
                return;
            }

            // Visual toggle
            element.classList.add('checked');
            
            // Wait for animation
            setTimeout(async () => {
                const response = await fetch('update_task.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${taskId}&profil=${encodeURIComponent(currentProfile.name)}`
                });

                if (response.ok) {
                    location.reload();
                }
            }, 500);
        }

        function filterTasks(category, btn) {
            // Update UI buttons
            document.querySelectorAll('.filter-btn').forEach(b => {
                b.classList.remove('filter-btn-active');
            });
            btn.classList.add('filter-btn-active');

            // Actual filtering
            document.querySelectorAll('[data-category]').forEach(card => {
                card.style.display = (category === 'all' || card.dataset.category === category) ? 'flex' : 'none';
            });
        }
    </script>
</body>
</html>