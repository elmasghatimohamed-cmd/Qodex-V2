<?php
require_once '../../classes/Security.php';

$userName = $userName ?? $_SESSION['user_nom'] ?? 'User';
$initials = strtoupper(substr($userName, 0, 1) . substr(explode(' ', $userName)[1] ?? '', 0, 1));

?>
<nav class="bg-white shadow-lg fixed w-full z-50 top-0">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <div class="flex-shrink-0 flex items-center">
                    <i class="fas fa-graduation-cap text-3xl text-blue-600"></i>
                    <span class="ml-2 text-2xl font-bold text-gray-900">Qodex</span>
                    <span
                        class="ml-3 px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">Étudiant</span>
                </div>

                <div class="hidden md:ml-10 md:flex md:space-x-8">
                    <a href="../student/dashboard.php"
                        class="<?= ($currentPage ?? '') === 'dashboard' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors">
                        <i class="fas fa-home mr-2"></i>Tableau de bord
                    </a>

                    <a href="../student/categories.php"
                        class="<?= ($currentPage ?? '') === 'categories' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors">
                        <i class="fas fa-folder-open mr-2"></i>Catégories
                    </a>

                    <a href="../student/quizzes.php"
                        class="<?= ($currentPage ?? '') === 'quizzes' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors">
                        <i class="fas fa-play-circle mr-2"></i>Passer un Quiz
                    </a>

                    <a href="../student/result.php"
                        class="<?= ($currentPage ?? '') === 'results' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors">
                        <i class="fas fa-chart-line mr-2"></i>Mes Résultats
                    </a>

                    <a href="../student/history.php"
                        class="<?= ($currentPage ?? '') === 'history' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors">
                        <i class="fas fa-history mr-2"></i>Historique
                    </a>
                </div>
            </div>

            <div class="flex items-center">
                <div class="flex items-center space-x-4">

                    <div
                        class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold">
                        <?= $initials ?>
                    </div>

                    <div class="hidden md:block">
                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($userName) ?></div>
                        <div class="text-xs text-gray-500">Étudiant</div>
                    </div>

                    <a href="../auth/logout.php?token=<?= Security::generateCSRFToken() ?>"
                        class="text-red-600 hover:text-red-700 transition-colors" title="Déconnexion">
                        <i class="fas fa-sign-out-alt text-xl"></i>
                    </a>
                </div>
            </div>

            <button type="button"
                class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 transition-colors"
                onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>
    </div>

    <!-- Menu Mobile -->
    <div class="md:hidden hidden bg-white border-t border-gray-200" id="mobile-menu">
        <div class="pt-2 pb-3 space-y-1">
            <a href="../student/dashboard.php"
                class="<?= ($currentPage ?? '') === 'dashboard' ? 'bg-blue-50 border-blue-500 text-blue-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors">
                <i class="fas fa-home mr-2"></i>Tableau de bord
            </a>
            <a href="../student/categories.php"
                class="<?= ($currentPage ?? '') === 'categories' ? 'bg-blue-50 border-blue-500 text-blue-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors">
                <i class="fas fa-folder-open mr-2"></i>Catégories
            </a>
            <a href="../student/quizzes.php"
                class="<?= ($currentPage ?? '') === 'quizzes' ? 'bg-blue-50 border-blue-500 text-blue-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors">
                <i class="fas fa-play-circle mr-2"></i>Passer un Quiz
            </a>
            <a href="../student/result.php"
                class="<?= ($currentPage ?? '') === 'results' ? 'bg-blue-50 border-blue-500 text-blue-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors">
                <i class="fas fa-chart-line mr-2"></i>Mes Résultats
            </a>
            <a href="../student/history.php"
                class="<?= ($currentPage ?? '') === 'history' ? 'bg-blue-50 border-blue-500 text-blue-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors">
                <i class="fas fa-history mr-2"></i>Historique
            </a>
        </div>
    </div>
</nav>

<style>
    body {
        padding-top: 64px;
    }

    nav {
        background-color: #ffffff;
        backdrop-filter: blur(10px);
    }

    #mobile-menu {
        background-color: #ffffff;
    }
</style>