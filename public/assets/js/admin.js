function confirmReset() {
    if (confirm("Êtes-vous sûr de vouloir tout remettre à zéro ? Cette action est irréversible (tâches, pièces, profils et historique seront supprimés).")) {
        window.location.href = '/admin/reset-app';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const emojiPicker = document.getElementById('emoji-picker');
    const colorPicker = document.getElementById('color-picker');
    const selectedEmojiInput = document.getElementById('selected-emoji');
    const selectedColorInput = document.getElementById('selected-color');
    const form = document.getElementById('add-room-form');

    const profileEmojiPicker = document.getElementById('profile-emoji-picker');
    const selectedProfileEmojiInput = document.getElementById('selected-profile-emoji');
    const profileColorPicker = document.getElementById('profile-color-picker');
    const selectedProfileColorInput = document.getElementById('selected-profile-color');

    const emojis = ['🛋️', '🛏️', '🍽️', '🍳', '🛁', '🚽', '🖥️', '📚', '👕', '📦', '🌿', '🚗', '🛠️', '🧹', '🧺', '🧼', '🚿', '🚪', '🪟', '🪴', '🪵', '🔥', '🚲', '🛵', '🛒', '🧸', '🎨', '🎹', '🎸'];
    const profileEmojis = ['👨', '👩', '👦', '👧', '👶', '👴', '👵', '🧔', '👱‍♀️', '👻', '👽', '🤖', '👾', '🐱', '🐶', '🦊', '🦁', '🐯', '🐸', '🐼', '🐨', '🐻', '🐹', '🐰', '🦄', '🐲', '🦖', '🐢', '🐙', '🐝', '🦋'];
    const colors = [
        'slate-500', 'red-500', 'orange-500', 'amber-500', 'yellow-500', 
        'lime-500', 'green-500', 'emerald-500', 'teal-500', 'cyan-500', 
        'sky-500', 'blue-500', 'indigo-500', 'violet-500', 'purple-500', 
        'fuchsia-500', 'pink-500', 'rose-500'
    ];

    // Populate Emojis for Rooms
    if (emojiPicker) {
        emojis.forEach(emoji => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = emoji;
            btn.className = 'p-2 rounded-lg text-2xl hover:bg-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500';
            btn.addEventListener('click', () => {
                selectedEmojiInput.value = emoji;
                document.querySelectorAll('#emoji-picker button').forEach(b => b.classList.remove('bg-indigo-200'));
                btn.classList.add('bg-indigo-200');
            });
            emojiPicker.appendChild(btn);
        });
    }

    // Populate Emojis for Profiles
    if (profileEmojiPicker) {
        profileEmojis.forEach(emoji => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = emoji;
            btn.className = 'p-2 rounded-lg text-2xl hover:bg-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500';
            btn.addEventListener('click', () => {
                selectedProfileEmojiInput.value = emoji;
                document.querySelectorAll('#profile-emoji-picker button').forEach(b => b.classList.remove('bg-indigo-200'));
                btn.classList.add('bg-indigo-200');
            });
            profileEmojiPicker.appendChild(btn);
        });
    }

    // Populate Colors for Rooms
    if (colorPicker) {
        colors.forEach(color => {
            const colorDiv = document.createElement('div');
            colorDiv.className = `w-10 h-10 rounded-lg cursor-pointer bg-${color} border-2 border-transparent hover:border-indigo-500`;
            colorDiv.dataset.color = color;
            colorDiv.addEventListener('click', () => {
                selectedColorInput.value = color;
                document.querySelectorAll('#color-picker div').forEach(d => d.classList.remove('ring-4', 'ring-offset-2', 'ring-indigo-500'));
                colorDiv.classList.add('ring-4', 'ring-offset-2', 'ring-indigo-500');
            });
            colorPicker.appendChild(colorDiv);
        });
    }

    // Populate Colors for Profiles
    if (profileColorPicker) {
        colors.forEach(color => {
            const colorDiv = document.createElement('div');
            colorDiv.className = `w-10 h-10 rounded-lg cursor-pointer bg-${color} border-2 border-transparent hover:border-indigo-500`;
            colorDiv.dataset.color = color;
            colorDiv.addEventListener('click', () => {
                selectedProfileColorInput.value = color;
                document.querySelectorAll('#profile-color-picker div').forEach(d => d.classList.remove('ring-4', 'ring-offset-2', 'ring-indigo-500'));
                colorDiv.classList.add('ring-4', 'ring-offset-2', 'ring-indigo-500');
            });
            profileColorPicker.appendChild(colorDiv);
        });
    }

    // Set default values
    if (emojiPicker && emojiPicker.children.length > 0) emojiPicker.children[0].click();
    if (profileEmojiPicker && profileEmojiPicker.children.length > 0) profileEmojiPicker.children[0].click();
    if (colorPicker && colorPicker.children.length > 0) colorPicker.children[0].click();
    if (profileColorPicker && profileColorPicker.children.length > 0) profileColorPicker.children[0].click();

    // Form validation
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!selectedEmojiInput.value || !selectedColorInput.value) {
                e.preventDefault();
                alert('Veuillez sélectionner un emoji et une couleur.');
            }
        });
    }
});

