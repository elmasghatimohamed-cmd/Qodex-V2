<?php
$currentPage = 'dashboard';
require_once '../../config/database.php';
include '../partials/header.php';
require_once '../partials/nav_student.php';
require_once '../../classes/Security.php';

Security::requireStudent();

$studentId = $_SESSION['user_id'];
$studentName = $_SESSION['user_nom'];
?>

<div class="container mx-auto px-4 py-8 max-w-7xl">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
            Bonjour, <?= htmlspecialchars($studentName) ?>
        </h1>
        <p class="text-gray-600">Voici un aperçu de vos performances</p>
    </div>

    <div id="loader" class="text-center py-16">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        <p class="mt-4 text-gray-600">Chargement de vos statistiques...</p>
    </div>

    <div id="dashboardContent" class="hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white bg-opacity-30 rounded-lg">
                        <i class="fas fa-clipboard-list text-2xl"></i>
                    </div>
                    <span class="text-3xl font-bold" id="totalQuizzes">0</span>
                </div>
                <h3 class="text-lg font-semibold">Quiz passés</h3>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white bg-opacity-30 rounded-lg">
                        <i class="fas fa-chart-line text-2xl"></i>
                    </div>
                    <span class="text-3xl font-bold" id="averageScore">0%</span>
                </div>
                <h3 class="text-lg font-semibold">Moyenne générale</h3>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white bg-opacity-30 rounded-lg">
                        <i class="fas fa-trophy text-2xl"></i>
                    </div>
                    <span class="text-3xl font-bold" id="bestScore">0%</span>
                </div>
                <h3 class="text-lg font-semibold">Meilleur score</h3>
            </div>

            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white bg-opacity-30 rounded-lg">
                        <i class="fas fa-tasks text-2xl"></i>
                    </div>
                    <span class="text-3xl font-bold" id="availableQuizzes">0</span>
                </div>
                <h3 class="text-lg font-semibold">Quiz disponibles</h3>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-history text-blue-600 mr-2"></i>
                    Derniers résultats
                </h2>
                <a href="result.php" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                    Voir tout <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div id="recentResults" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

            </div>
        </div>
    </div>
</div>

<script>
    async function loadDashboardData() {
        const loader = document.getElementById('loader');
        const dashboardContent = document.getElementById('dashboardContent');

        try {
            const response = await fetch('../../actions/student/dashboard.php');
            const result = await response.json();

            if (!result.success) throw new Error(result.error);

            // Stats principales
            document.getElementById('totalQuizzes').textContent = result.stats.total_quiz || 0;
            document.getElementById('averageScore').textContent =
                result.stats.moyenne ? Math.round(result.stats.moyenne) + '%' : '0%';
            document.getElementById('bestScore').textContent =
                result.stats.meilleur_score ? Math.round(result.stats.meilleur_score) + '%' : '0%';
            document.getElementById('availableQuizzes').textContent = result.available_quizzes || 0;

            // Derniers résultats
            renderRecentResults(result.recent_results || []);

            loader.classList.add('hidden');
            dashboardContent.classList.remove('hidden');

        } catch (error) {
            console.error('Erreur:', error);
            loader.innerHTML = `
                <div class="text-red-600">
                    <i class="fas fa-exclamation-circle text-4xl mb-2"></i>
                    <p>Erreur lors du chargement des statistiques</p>
                </div>
            `;
        }
    }

    function renderRecentResults(results) {
        const container = document.getElementById('recentResults');

        if (results.length === 0) {
            container.innerHTML = `
                <div class="col-span-full text-center py-12 text-gray-500">
                    <i class="fas fa-inbox text-5xl mb-3"></i>
                    <p class="text-lg">Aucun résultat pour le moment</p>
                    <p class="text-sm mt-2">Commencez par passer un quiz !</p>
                    <a href="categories.php" class="inline-block mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Explorer les quiz
                    </a>
                </div>
            `;
            return;
        }

        container.innerHTML = results.map(result => {
            const percentage = (result.score / result.total_questions) * 100;
            let scoreColor, bgColor, icon;

            if (percentage >= 70) {
                scoreColor = 'text-green-600';
                bgColor = 'bg-green-50 border-green-200';
                icon = 'fa-check-circle';
            } else if (percentage >= 50) {
                scoreColor = 'text-orange-600';
                bgColor = 'bg-orange-50 border-orange-200';
                icon = 'fa-exclamation-circle';
            } else {
                scoreColor = 'text-red-600';
                bgColor = 'bg-red-50 border-red-200';
                icon = 'fa-times-circle';
            }

            return `
                <div class="border-2 ${bgColor} rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900 mb-1">${escapeHtml(result.quiz_titre)}</h4>
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-folder text-gray-400 mr-1"></i>
                                ${escapeHtml(result.categorie_nom)}
                            </p>
                        </div>
                        <i class="fas ${icon} ${scoreColor} text-2xl"></i>
                    </div>
                    <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                        <span class="text-sm text-gray-600">${result.score}/${result.total_questions} correctes</span>
                        <span class="text-xl font-bold ${scoreColor}">${Math.round(percentage)}%</span>
                    </div>
                </div>
            `;
        }).join('');
    }

    function escapeHtml(text) {
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text ? text.replace(/[&<>"']/g, m => map[m]) : '';
    }

    document.addEventListener('DOMContentLoaded', loadDashboardData);
</script>

<?php require_once '../partials/footer.php'; ?>