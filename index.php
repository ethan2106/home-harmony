<?php
/**
 * Application : Home Harmony
 * Architecture : Modulaire (Partials / CSS / JS)
 */
require_once 'includes/bootstrap.php';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    
    <?php include 'includes/sidebar.php'; ?>

    <!-- COLONNE DROITE : CONTENU PRINCIPAL -->
    <div class="lg:col-span-9 space-y-10">

                
                <!-- TITRE DE SECTION -->
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 px-4">
                    <div>
                        <h2 class="text-5xl font-black text-slate-900 tracking-tight">Objectifs du jour</h2>
                        <p class="text-slate-600 font-semibold mt-2 text-lg italic">"La maison ne se range pas toute seule..." 😉</p>
                    </div>
                </div>

                <!-- GRILLE DES TÂCHES (TODO) -->
                <div id="todo-container" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php if (empty($todoTasks)): ?>
                        <div class="col-span-full glass-panel py-24 text-center rounded-[4rem] border-dashed border-3 border-indigo-200 bg-white/20">
                            <div class="text-7xl mb-8">🥂</div>
                            <h3 class="text-3xl font-black text-slate-800">C'est l'heure de l'apéro !</h3>
                            <p class="text-slate-600 font-bold text-lg mt-2">La maison brille, tout est à jour pour aujourd'hui.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($todoTasks as $task): 
                            // Catégorisation simplifiée pour le filtre JS
                            $cat = ($task['zone'] === 'jardin') ? 'jardin' : 
                                   (($task['zone'] === 'voiture') ? 'voiture' : 'maison');
                        ?>
                            <div class="glass-card p-8 rounded-[3rem] flex flex-col justify-between gap-8 group bg-white/80 hover:bg-white border border-white shadow-sm transition-all" data-category="<?php echo $cat; ?>">
                                <div class="flex justify-between items-center">
                                    <div class="flex gap-5">
                                        <div class="w-16 h-16 rounded-[1.5rem] bg-slate-50 shadow-inner flex items-center justify-center text-3xl group-hover:scale-110 transition-transform border border-slate-100">
                                            <?php echo $task['room_emoji'] ?? '📋'; ?>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-3 mb-1">
                                                <span class="px-2.5 py-1 bg-slate-900 text-[10px] font-black text-white uppercase rounded-lg tracking-widest">
                                                    <?php echo $task['frequence']; ?>
                                                </span>
                                                <span class="text-[11px] font-black text-indigo-600 uppercase tracking-widest">
                                                    <?php echo $task['room_nom'] ?? 'Général'; ?>
                                                </span>
                                            </div>
                                            <h3 class="text-xl font-extrabold text-slate-800 group-hover:text-indigo-600 transition-colors">
                                                <?php echo ucfirst($task['titre']); ?>
                                            </h3>
                                        </div>
                                    </div>
                                    
                                    <!-- Case à cocher "Lux" (Interaction JS) -->
                                    <div onclick="toggleTaskLux(<?php echo $task['id']; ?>, this)" class="lux-checkbox cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-white hidden"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between pt-4 border-t border-slate-100/50">
                                    <div class="text-xs font-black text-slate-400 flex items-center gap-2 uppercase tracking-tighter">
                                        <span class="w-2.5 h-2.5 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.5)]"></span> Disponible
                                    </div>
                                    <div class="text-[10px] font-black text-slate-300 tracking-[0.2em] uppercase">Prêt à être validé</div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- SECTION DES TÂCHES TERMINÉES (ARCHIVES) -->
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
                            <div onclick="undoTask(<?php echo $task['id']; ?>, this)" class="group relative cursor-pointer p-5 bg-white/40 border-2 border-white rounded-[2rem] flex items-center gap-5 opacity-80 hover:opacity-100 hover:border-red-200 transition-all backdrop-blur-md shadow-sm overflow-hidden">
                                <!-- Badge d'annulation au survol -->
                                <div class="absolute inset-0 bg-red-50/90 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity z-10">
                                    <div class="flex items-center gap-2 text-red-600 font-black uppercase text-xs tracking-widest">
                                        <span class="text-lg">✕</span> Annuler
                                    </div>
                                </div>

                                <div class="w-12 h-12 rounded-2xl bg-<?php echo $profile['couleur'] ?? 'slate-400'; ?> flex items-center justify-center text-white text-sm font-black shadow-lg shrink-0">
                                    <?php echo (isset($profile['emoji']) && $profile['emoji']) ? $profile['emoji'] : substr($task['fait_par'] ?? '?', 0, 1); ?>
                                </div>
                                <div class="truncate grow">
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

<?php include 'includes/footer.php'; ?>
