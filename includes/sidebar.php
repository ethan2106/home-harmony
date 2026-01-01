<!-- COLONNE GAUCHE : STATS & CATÉGORIES -->
<div class="lg:col-span-3 space-y-8">
    <!-- LEADERBOARD (Tableau d'Honneur) -->
    <div class="glass-panel p-6 rounded-[2.5rem] border border-white shadow-sm bg-white/40">
        <h3 class="text-xs font-black text-slate-500 uppercase tracking-[0.2em] mb-6">Tableau d'Honneur</h3>
        <div class="space-y-4">
            <?php foreach ($profiles as $profile): ?>
                <div class="flex items-center justify-between p-3 rounded-2xl hover:bg-white/80 transition-colors border border-transparent hover:border-slate-200">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-<?php echo $profile['couleur']; ?> flex items-center justify-center text-sm text-white font-bold shadow-lg">
                            <?php echo (isset($profile['emoji']) && $profile['emoji']) ? $profile['emoji'] : substr($profile['nom'], 0, 1); ?>
                        </div>
                        <span class="font-bold text-slate-700"><?php echo $profile['nom']; ?></span>
                    </div>
                    <div class="flex flex-col items-end">
                        <span id="stat-<?php echo $profile['nom']; ?>" class="text-xl font-black text-slate-900"><?php echo $stats[$profile['nom']] ?? 0; ?></span>
                        <span class="text-[8px] font-bold text-slate-400 uppercase tracking-tighter">tâches</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- MENU DES ZONES -->
    <div class="glass-panel p-6 rounded-[2.5rem] border border-white shadow-sm bg-white/40">
        <h3 class="text-xs font-black text-slate-500 uppercase tracking-[0.2em] mb-6">Zones</h3>
        <div class="grid grid-cols-1 gap-3">
            <button onclick="filterTasks('all', this)" class="filter-btn filter-btn-active group flex items-center justify-between p-4 rounded-2xl bg-white/50 text-slate-600 transition-all border border-slate-200">
                <span class="font-bold">🌍 Toutes zones</span>
                <span class="bg-white/40 px-2 py-0.5 rounded-lg text-[10px] font-black border border-white/50">ALL</span>
            </button>
            <button onclick="filterTasks('maison', this)" class="filter-btn group flex items-center justify-between p-4 rounded-2xl bg-white/50 text-slate-600 hover:bg-white hover:border-indigo-200 transition-all border border-slate-200">
                <span class="font-bold">🏠 Maison</span>
            </button>
            <button onclick="filterTasks('jardin', this)" class="filter-btn group flex items-center justify-between p-4 rounded-2xl bg-white/50 text-slate-600 hover:bg-white hover:border-indigo-200 transition-all border border-slate-200">
                <span class="font-bold">🌳 Jardin</span>
            </button>
            <button onclick="filterTasks('voiture', this)" class="filter-btn group flex items-center justify-between p-4 rounded-2xl bg-white/50 text-slate-600 hover:bg-white hover:border-indigo-200 transition-all border border-slate-200">
                <span class="font-bold">🚗 Voiture</span>
            </button>
        </div>
    </div>

    <a href="admin.php" class="flex items-center justify-center p-6 rounded-[2.5rem] bg-indigo-50 text-indigo-700 font-black text-xs uppercase tracking-[0.2em] hover:bg-indigo-600 hover:text-white transition-all border-2 border-indigo-100/50 shadow-sm">
        Paramètres ⚙️
    </a>
</div>
