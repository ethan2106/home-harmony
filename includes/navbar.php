<?php
/**
 * @var array<int, array<string, mixed>> $profiles
 * @var string|null $date_fr
 */
?>
<!-- BARRE DE NAVIGATION SUPÉRIEURE -->
<nav class="flex flex-col md:flex-row justify-between items-center py-10 gap-8">
    <a href="<?php echo BASE_URL; ?>/" class="flex items-center gap-4 hover:opacity-80 transition-opacity">
        <div class="w-14 h-14 bg-indigo-600 rounded-3xl flex items-center justify-center text-3xl shadow-2xl shadow-indigo-200 rotate-3 transition-transform hover:rotate-0">
            ✨
        </div>
        <div>
            <h1 class="text-4xl font-extrabold text-slate-900 tracking-tighter">Harmony</h1>
            <p class="text-indigo-600 font-bold text-sm tracking-widest uppercase"><?php echo $date_fr ?? date('d/m/Y'); ?></p>
        </div>
    </a>

    <!-- Sélecteur de profil -->
    <div id="profile-selector" class="flex items-center gap-3 glass-panel p-3 rounded-4xl border border-white/50 shadow-sm">
        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest px-3">Qui travaille ?</span>
        <div class="flex gap-2">
        <?php foreach ($profiles as $profile): ?>
            <button 
                onclick="selectProfile('<?php echo $profile['nom']; ?>', '<?php echo $profile['couleur']; ?>')"
                id="btn-<?php echo $profile['nom']; ?>"
                class="profile-btn h-12 px-6 rounded-2xl text-white font-bold text-sm shadow-md transition-all active:scale-95 bg-<?php echo $profile['couleur']; ?>"
            >
                <?php echo (isset($profile['emoji']) && $profile['emoji']) ? $profile['emoji'] . ' ' : ''; ?><?php echo $profile['nom']; ?>
            </button>
        <?php endforeach; ?>
        </div>
    </div>
</nav>
