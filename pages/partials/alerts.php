<?php
/**
 * PARTIAL: Alerts
 * Affiche les messages de succès et d'erreur
 */
?>

<?php if (!empty($success)): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        <i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>
