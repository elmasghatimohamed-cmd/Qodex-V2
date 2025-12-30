<?php
/**
 * Page: Liste des Quiz
 * Affiche tous les quiz de l'enseignant
 */

require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/Security.php';
require_once '../../classes/Quiz.php';
require_once '../../classes/Category.php';

// Vérifier que l'utilisateur est enseignant
Security::requireTeacher();

// Variables pour la navigation
$currentPage = 'quiz';
$pageTitle = 'Mes Quiz';

// Récupérer les données
$teacherId = $_SESSION['user_id'];
$userName = $_SESSION['user_nom'];

$quizObj = new Quiz();
$categoryObj = new Category();

$quizzes = $quizObj->getAllByTeacher($teacherId);
$categories = $categoryObj->getAllByTeacher($teacherId);

// Messages
$success = $_SESSION['quiz_success'] ?? '';
$error = $_SESSION['quiz_error'] ?? '';
unset($_SESSION['quiz_success'], $_SESSION['quiz_error']);
?>
<?php include '../partials/header.php'; ?>

<?php include '../partials/nav_teacher.php'; ?>

<div class="pt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Mes Quiz</h2>
                <p class="text-gray-600 mt-2">Créez et gérez vos quiz</p>
            </div>
            <a href="quiz_create.php"
                class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">
                <i class="fas fa-plus mr-2"></i>Créer un Quiz
            </a>
        </div>

        <?php include '../partials/alerts.php'; ?>

        <?php if (empty($quizzes)): ?>
            <div class="bg-white rounded-xl shadow-md p-8 text-center">
                <i class="fas fa-clipboard-list text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Aucun quiz</h3>
                <p class="text-gray-600 mb-6">Créez votre premier quiz pour commencer</p>
                <?php if (empty($categories)): ?>
                    <p class="text-orange-600 mb-4">
                        <i class="fas fa-info-circle mr-2"></i>
                        Créez d'abord une catégorie avant de créer un quiz
                    </p>
                    <a href="categories.php"
                        class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">
                        <i class="fas fa-folder-plus mr-2"></i>Créer une Catégorie
                    </a>
                <?php else: ?>
                    <a href="quiz_create.php"
                        class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">
                        <i class="fas fa-plus-circle mr-2"></i>Créer un Quiz
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($quizzes as $quiz): ?>
                    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                                    <?= htmlspecialchars($quiz['categorie_nom']) ?>
                                </span>
                                <div class="flex gap-2">
                                    <!-- Toggle Actif/Inactif -->
                                    <a href="../../actions/teacher/quiz_toggle.php?id=<?= $quiz['id'] ?>&token=<?= Security::generateCSRFToken() ?>"
                                        class="<?= $quiz['is_active'] ? 'text-green-600 hover:text-green-700' : 'text-gray-400 hover:text-gray-600' ?>"
                                        title="<?= $quiz['is_active'] ? 'Désactiver' : 'Activer' ?>">
                                        <i class="fas fa-power-off"></i>
                                    </a>
                                    <a href="quiz_edit.php?id=<?= $quiz['id'] ?>" class="text-blue-600 hover:text-blue-700">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="../../actions/teacher/quiz_delete.php?id=<?= $quiz['id'] ?>&token=<?= Security::generateCSRFToken() ?>"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce quiz et toutes ses questions ?')"
                                        class="text-red-600 hover:text-red-700">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2"><?= htmlspecialchars($quiz['titre']) ?></h3>
                            <p class="text-gray-600 mb-4 text-sm">
                                <?= htmlspecialchars(substr($quiz['description'] ?? '', 0, 100)) ?></p>
                            <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                <span><i class="fas fa-question-circle mr-1"></i><?= $quiz['questions_count'] ?>
                                    questions</span>
                                <span><i class="fas fa-user-friends mr-1"></i><?= $quiz['participants_count'] ?>
                                    participants</span>
                            </div>
                            <?php if ($quiz['is_active']): ?>
                                <span class="inline-block px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                                    <i class="fas fa-check-circle mr-1"></i>Actif
                                </span>
                            <?php else: ?>
                                <span class="inline-block px-3 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-full">
                                    <i class="fas fa-pause-circle mr-1"></i>Inactif
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../partials/footer.php'; ?>