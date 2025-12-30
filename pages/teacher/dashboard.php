<?php
/**
 * Page: Tableau de bord Enseignant
 * Affiche les statistiques et les quiz récents
 */

require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/Security.php';
require_once '../../classes/Category.php';
require_once '../../classes/Quiz.php';

// Vérifier que l'utilisateur est enseignant
Security::requireTeacher();

// Variables pour la navigation
$currentPage = 'dashboard';
$pageTitle = 'Tableau de bord';

// Récupérer les données de l'utilisateur
$teacherId = $_SESSION['user_id'];
$userName = $_SESSION['user_nom'];

// Récupérer les statistiques
$categoryObj = new Category();
$quizObj = new Quiz();

$categories = $categoryObj->getAllByTeacher($teacherId);
$quizzes = $quizObj->getAllByTeacher($teacherId);

$totalQuizzes = count($quizzes);
$totalCategories = count($categories);

// Calculer le nombre total de questions
$totalQuestions = 0;
foreach ($quizzes as $quiz) {
    $totalQuestions += $quiz['questions_count'];
}

// Calculer le nombre total de participants
$totalParticipants = 0;
foreach ($quizzes as $quiz) {
    $totalParticipants += $quiz['participants_count'];
}

// Initiales pour l'avatar
$initials = strtoupper(substr($userName, 0, 1) . substr(explode(' ', $userName)[1] ?? '', 0, 1));
?>
<?php include '../partials/header.php'; ?>

<?php include '../partials/nav_teacher.php'; ?>

<!-- Main Content -->
<div class="pt-16">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h1 class="text-4xl font-bold mb-4">Tableau de bord Enseignant</h1>
            <p class="text-xl text-indigo-100 mb-6">Gérez vos quiz et suivez les performances de vos étudiants</p>
            <div class="flex gap-4">
                <a href="categories.php" class="bg-white text-indigo-600 px-6 py-3 rounded-lg font-semibold hover:bg-indigo-50 transition">
                    <i class="fas fa-folder-plus mr-2"></i>Nouvelle Catégorie
                </a>
                <a href="quiz.php" class="bg-indigo-700 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-800 transition">
                    <i class="fas fa-plus-circle mr-2"></i>Créer un Quiz
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Total Quiz -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Quiz</p>
                        <p class="text-3xl font-bold text-gray-900"><?= $totalQuizzes ?></p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <i class="fas fa-clipboard-list text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            
            <!-- Catégories -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Catégories</p>
                        <p class="text-3xl font-bold text-gray-900"><?= $totalCategories ?></p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <i class="fas fa-folder text-purple-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            
            <!-- Questions -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Questions</p>
                        <p class="text-3xl font-bold text-gray-900"><?= $totalQuestions ?></p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg">
                        <i class="fas fa-question-circle text-green-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            
            <!-- Participants -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Participants</p>
                        <p class="text-3xl font-bold text-gray-900"><?= $totalParticipants ?></p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-lg">
                        <i class="fas fa-user-graduate text-yellow-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Quizzes -->
        <div class="mt-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Quiz Récents</h2>
            
            <?php if (empty($quizzes)): ?>
                <div class="bg-white rounded-xl shadow-md p-8 text-center">
                    <i class="fas fa-clipboard-list text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Aucun quiz pour le moment</h3>
                    <p class="text-gray-600 mb-6">Créez votre premier quiz pour commencer</p>
                    <a href="quiz.php" class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">
                        <i class="fas fa-plus-circle mr-2"></i>Créer un Quiz
                    </a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach (array_slice($quizzes, 0, 6) as $quiz): ?>
                        <div class="bg-white rounded-xl shadow-md overflow-hidden">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                                        <?= htmlspecialchars($quiz['categorie_nom']) ?>
                                    </span>
                                    <?php if ($quiz['is_active']): ?>
                                        <span class="text-green-600"><i class="fas fa-circle text-xs"></i> Actif</span>
                                    <?php else: ?>
                                        <span class="text-gray-400"><i class="fas fa-circle text-xs"></i> Inactif</span>
                                    <?php endif; ?>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2"><?= htmlspecialchars($quiz['titre']) ?></h3>
                                <p class="text-gray-600 mb-4 text-sm"><?= htmlspecialchars(substr($quiz['description'], 0, 80)) ?>...</p>
                                <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                    <span><i class="fas fa-question-circle mr-1"></i><?= $quiz['questions_count'] ?> questions</span>
                                    <span><i class="fas fa-user-friends mr-1"></i><?= $quiz['participants_count'] ?> participants</span>
                                </div>
                                <a href="quiz_edit.php?id=<?= $quiz['id'] ?>" class="block w-full text-center bg-indigo-600 text-white py-2 rounded-lg font-semibold hover:bg-indigo-700 transition">
                                    <i class="fas fa-edit mr-2"></i>Modifier
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../partials/footer.php'; ?>
