# Corrections pour les erreurs JS dans /reports/create et Summernote

## Problèmes identifiés

1. **Uncaught ReferenceError: $ is not defined** - jQuery n'était pas chargé correctement
2. **Detected multiple instances of Alpine running** - Alpine était chargé plusieurs fois
3. **Alpine Expression Error: Unexpected identifier 'hévéa'** - Problème d'encodage avec les accents dans les expressions Alpine
4. **Summernote ne s'affichait pas** - Initialisation peu robuste

## Solutions apportées

### 1. Correction du chemin jQuery (app.blade.php)
**Fichier** : `resources/views/layouts/app.blade.php`
- **Avant** : `<script src="/jQuery/jquery.min.js"></script>` (fichier introuvable)
- **Après** : `<script src="/jQuery/jquery-3.4.1.min.js"></script>` (fichier existant)

### 2. Suppression du double chargement de jQuery
**Fichier** : `resources/views/technitian/reports/create.blade.php`
- Supprimé la ligne dupliquée : `<script src="/jQuery/jquery.min.js"></script>`
- jQuery est déjà chargé dans le layout principal

### 3. Optimisation du chargement d'Alpine.js

**Fichier** : `resources/views/layouts/app.blade.php`
- Supprimé le chargement manuel d'Alpine.js (Livewire le charge automatiquement)
- Déplacé collapse.js après `@livewireScripts` pour s'assurer qu'Alpine est chargé avant

**Fichier** : `resources/views/layouts/auth.blade.php`
- Gardé le chargement manuel d'Alpine.js pour les pages sans Livewire (login, etc.)

### 4. Initialisation robuste de Summernote

**Fichiers** : 
- `resources/views/admin/reports/create.blade.php`
- `resources/views/technitian/reports/create.blade.php`

**Améliorations** :
- Création d'une fonction `initSummernote()` réutilisable
- Vérification que jQuery et Summernote sont chargés avant l'initialisation
- Écoute de plusieurs événements : DOMContentLoaded, livewire:init
- Timeout de secours (500ms) pour les cas limites
- Vérification que Livewire est défini avant d'utiliser `Livewire.find()`
- Gestion sécurisée du contenu initial

### 5. Gestion des accents dans Alpine (déjà correct)
**Fichier** : `resources/views/livewire/report-form.blade.php`
- Les expressions Alpine utilisent `@js()` pour échapper les chaînes PHP (lignes 56, 99, 143)
- Cela résout le problème avec les caractères spéciaux comme 'hévéa'

## Structure finale

### Chargement des scripts dans app.blade.php
```html
<script src="/jQuery/jquery-3.4.1.min.js"></script>
@livewireStyles
...
@livewireScripts
<script src="/alpine/collapse.js" defer></script>
@stack('scripts')
```

### Chargement des scripts dans auth.blade.php
```html
<script src="/alpine/alpine.js" defer></script>
@stack('styles')
```

### Initialisation de Summernote
```javascript
function initSummernote() {
    var notesElement = document.getElementById('notes');
    if (notesElement && typeof $ !== 'undefined' && $.fn.summernote) {
        // Initialisation avec vérifications
    }
}

// Appel via plusieurs événements
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSummernote);
} else {
    initSummernote();
}

if (typeof Livewire !== 'undefined') {
    document.addEventListener('livewire:init', initSummernote);
}

setTimeout(initSummernote, 500);
```

## Fichiers modifiés

1. `resources/views/layouts/app.blade.php` - Correction jQuery, optimisation Alpine
2. `resources/views/layouts/auth.blade.php` - Conservation Alpine pour les pages sans Livewire
3. `resources/views/admin/reports/create.blade.php` - Initialisation Summernote robuste
4. `resources/views/technitian/reports/create.blade.php` - Initialisation Summernote robuste, suppression double jQuery

## Vérifications à faire

Après déploiement, vérifier dans la console du navigateur :
- [ ] Pas d'erreur `$ is not defined`
- [ ] Pas d'erreur `Detected multiple instances of Alpine running`
- [ ] Pas d'erreur Alpine avec les accents
- [ ] Summernote s'affiche correctement sur le champ Notes
- [ ] Le contenu de Notes est bien sauvegardé
