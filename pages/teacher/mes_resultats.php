<?php
/**
 * Page: Mes Résultats (Enseignant)
 * Affiche les résultats des quiz pour l'enseignant
 */

require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/Security.php';
require_once '../../classes/Result.php';

// Vérifier que l'utilisateur est connecté
Security::requireLogin();

// Variables pour la navigation
$currentPage = 'resultats';
$pageTitle = 'Mes Résultats';

// Récupérer les données
$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_nom'];

// Créer l'objet Result
$resultObj = new Result();

// Récupérer MES résultats uniquement
$mesResultats = $resultObj->getMyResults($userId);
$mesStats = $resultObj->getMyStats($userId);
?>
<?php include '../partials/header.php'; ?>

<?php include '../partials/nav_teacher.php'; ?>

<!-- Main Content -->
<div class="pt-16">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h1 class="text-4xl font-bold mb-4">
                <i class="fas fa-chart-bar mr-3"></i>Mes Résultats
            </h1>
            <p class="text-xl text-indigo-100">Historique de vos scores aux quiz</p>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total Quiz passés -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Quiz Passés</p>
                        <p class="text-3xl font-bold text-gray-900"><?= $mesStats['total_quiz'] ?? 0 ?></p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <i class="fas fa-clipboard-check text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            
            <!-- Moyenne -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Moyenne</p>
                        <p class="text-3xl font-bold text-gray-900">
                            <?= $mesStats['moyenne'] ? round($mesStats['moyenne'], 1) . '%' : '-' ?>
                        </p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg">
                        <i class="fas fa-chart-line text-green-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            
            <!-- Meilleur Score -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Meilleur Score</p>
                        <p class="text-3xl font-bold text-gray-900">
                            <?= $mesStats['meilleur_score'] ? round($mesStats['meilleur_score'], 1) . '%' : '-' ?>
                        </p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-lg">
                        <i class="fas fa-trophy text-yellow-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historique -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-history mr-2 text-indigo-600"></i>Historique
                </h2>
            </div>
            
            <?php if (empty($mesResultats)): ?>
                <div class="p-8 text-center">
                    <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Aucun résultat</h3>
                    <p class="text-gray-600">Vous n'avez pas encore passé de quiz.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quiz</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catégorie</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Score</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Pourcentage</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($mesResultats as $resultat): ?>
                                <?php 
                                $pourcentage = ($resultat['score'] / $resultat['total_questions']) * 100;
                                if ($pourcentage >= 80) {
                                    $colorClass = 'bg-green-100 text-green-800';
                                } elseif ($pourcentage >= 60) {
                                    $colorClass = 'bg-yellow-100 text-yellow-800';
                                } else {
                                    $colorClass = 'bg-red-100 text-red-800';
                                }
                                ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">
                                            <?= htmlspecialchars($resultat['quiz_titre']) ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 bg-indigo-100 text-indigo-700 text-xs rounded-full">
                                            <?= htmlspecialchars($resultat['categorie_nom'] ?? 'N/A') ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold">
                                        <?= $resultat['score'] ?>/<?= $resultat['total_questions'] ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-3 py-1 <?= $colorClass ?> text-sm font-semibold rounded-full">
                                            <?= round($pourcentage, 1) ?>%
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 text-sm">
                                        <?= date('d/m/Y H:i', strtotime($resultat['created_at'])) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../partials/footer.php'; ?>

