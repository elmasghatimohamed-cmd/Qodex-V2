<?php
require_once '../../config/database.php';
include '../partials/header.php';
require_once '../partials/nav_student.php';

$categoryId = isset($_GET['category_id']) ? (int) $_GET['category_id'] : 0;

if ($categoryId <= 0) {
    $_SESSION['category_error'] = 'Catégorie invalide';
    header('Location: categories.php');
    exit();
}
?>

<div class="container mx-auto px-4 py-8 max-w-7xl">
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 flex items-center gap-2">
            <i class="fas fa-clipboard-list text-blue-600"></i>
            Quiz disponibles
        </h2>
        <a href="categories.php"
            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-medium transition-colors">
            <i class="fas fa-arrow-left"></i>
            Retour aux catégories
        </a>
    </div>

    <?php if (isset($_SESSION['quiz_success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
            <?php
            echo htmlspecialchars($_SESSION['quiz_success']);
            unset($_SESSION['quiz_success']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['quiz_error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
            <?php
            echo htmlspecialchars($_SESSION['quiz_error']);
            unset($_SESSION['quiz_error']);
            ?>
        </div>
    <?php endif; ?>

    <div id="loader" class="text-center py-16">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        <p class="mt-4 text-gray-600">Chargement des quiz...</p>
    </div>

    <div id="errorMessage" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
        <p class="font-bold">Erreur</p>
        <p id="errorText"></p>
    </div>

    <div id="noQuizzes" class="hidden text-center py-16">
        <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
            </path>
        </svg>
        <h3 class="mt-4 text-xl font-semibold text-gray-900">Aucun quiz disponible</h3>
        <p class="mt-2 text-sm text-gray-600">Il n'y a pas encore de quiz actifs dans cette catégorie.</p>
    </div>

    <div id="quizzesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 hidden"></div>
</div>

<script>
    const categoryId = <?= $categoryId ?>;

    async function loadQuizzes() {
        const loader = document.getElementById('loader');
        const noQuizzes = document.getElementById('noQuizzes');
        const quizzesGrid = document.getElementById('quizzesGrid');
        const errorMessage = document.getElementById('errorMessage');
        const errorText = document.getElementById('errorText');

        try {
            console.log('Chargement des quiz pour la catégorie:', categoryId);

            const response = await fetch(`../../actions/student/show_quizzes.php?category_id=${categoryId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            console.log('Statut de la réponse:', response.status);

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Erreur lors du chargement');
            }

            const result = await response.json();
            console.log('Résultat:', result);

            loader.classList.add('hidden');

            if (result.success && result.data.length > 0) {
                quizzesGrid.classList.remove('hidden');

                quizzesGrid.innerHTML = result.data.map(quiz => `
                    <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden cursor-pointer transform hover:-translate-y-1 border-l-4 border-blue-600"
                         onclick="startQuiz(${quiz.id})">
                        <div class="p-6">
                            <!-- Header -->
                            <div class="flex items-start justify-between mb-4">
                                <h3 class="text-lg font-bold text-gray-900 flex-1 pr-2">
                                    ${escapeHtml(quiz.titre)}
                                </h3>
                                <i class="fas fa-play-circle text-2xl text-blue-600"></i>
                            </div>
                            
                            <!-- Métadonnées -->
                            <div class="flex items-center gap-4 mb-4 text-sm text-gray-600">
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-question-circle text-blue-600"></i>
                                    ${quiz.questions_count} question${quiz.questions_count > 1 ? 's' : ''}
                                </span>
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-folder text-blue-600"></i>
                                    ${escapeHtml(quiz.categorie_nom)}
                                </span>
                            </div>
                            
                            <!-- Description -->
                            ${quiz.description ? `
                                <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                    ${escapeHtml(quiz.description)}
                                </p>
                            ` : ''}
                            
                            <!-- Footer -->
                            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                <span class="text-xs text-gray-500 flex items-center gap-1">
                                    <i class="fas fa-calendar"></i>
                                    ${formatDate(quiz.created_at)}
                                </span>
                                <button 
                                    onclick="event.stopPropagation(); startQuiz(${quiz.id})"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                                    <i class="fas fa-play"></i>
                                    Commencer
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('');
            } else {
                noQuizzes.classList.remove('hidden');
            }

        } catch (error) {
            console.error('Erreur détaillée:', error);
            loader.classList.add('hidden');
            errorText.textContent = `Impossible de charger les quiz. ${error.message}`;
            errorMessage.classList.remove('hidden');
        }
    }

    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text ? text.replace(/[&<>"']/g, m => map[m]) : '';
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('fr-FR', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    function startQuiz(quizId) {
        window.location.href = `take_quiz.php?quiz_id=${quizId}`;
    }

    document.addEventListener('DOMContentLoaded', loadQuizzes);
</script>

<?php require_once '../partials/footer.php'; ?>