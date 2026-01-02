<?php
require_once '../../config/database.php';
include '../partials/header.php';
require_once '../partials/nav_student.php';
?>

<div class="container mx-auto p-8">
    <h2 class="text-2xl font-bold mb-4">Catégories Disponibles</h2>

    <?php if (isset($_SESSION['category_success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php
            echo htmlspecialchars($_SESSION['category_success']);
            unset($_SESSION['category_success']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['category_error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php
            echo htmlspecialchars($_SESSION['category_error']);
            unset($_SESSION['category_error']);
            ?>
        </div>
    <?php endif; ?>

    <div id="loader" class="text-center py-8">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
        <p class="mt-2 text-gray-600">Chargement des catégories...</p>
    </div>

    <div id="errorMessage" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <p class="font-bold">Erreur</p>
        <p id="errorText"></p>
    </div>

    <div id="noCategories" class="hidden text-center py-8">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
            </path>
        </svg>
        <h3 class="mt-2 text-lg font-medium text-gray-900">Aucune catégorie disponible</h3>
        <p class="mt-1 text-sm text-gray-500">Il n'y a pas encore de quiz actifs à passer.</p>
    </div>

    <div id="categoriesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 hidden"></div>
</div>

<script>
    async function loadCategories() {
        const loader = document.getElementById('loader');
        const noCategories = document.getElementById('noCategories');
        const categoriesGrid = document.getElementById('categoriesGrid');
        const errorMessage = document.getElementById('errorMessage');
        const errorText = document.getElementById('errorText');

        try {

            const response = await fetch('../../actions/student/show_categories.php', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            });


            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Erreur lors du chargement');
            }

            const result = await response.json();

            loader.classList.add('hidden');

            if (result.success && result.data.length > 0) {
                categoriesGrid.classList.remove('hidden');

                categoriesGrid.innerHTML = result.data.map(category => `
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden cursor-pointer transform hover:-translate-y-1"
                     onclick="viewQuizzes(${category.id})">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold text-gray-800">${escapeHtml(category.nom)}</h3>
                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                ${category.quiz_count} quiz
                            </span>
                        </div>
                        
                        ${category.description ? `
                            <p class="text-gray-600 text-sm mb-4">
                                ${escapeHtml(category.description)}
                            </p>
                        ` : ''}
                        
                        <div class="flex items-center text-blue-600 font-medium text-sm">
                            <span>Voir les quiz</span>
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            `).join('');
            } else {
                noCategories.classList.remove('hidden');
            }

        } catch (error) {
            loader.classList.add('hidden');
            errorText.textContent = `Impossible de charger les catégories. ${error.message}`;
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

    function viewQuizzes(categoryId) {
        window.location.href = `quizzes.php?category_id=${categoryId}`;
    }

    document.addEventListener('DOMContentLoaded', loadCategories);
</script>

<?php require_once '../partials/footer.php'; ?>